<?php

namespace EntityUi;

use Conn\Delete;
use Conn\Read;
use Conn\SqlCommand;
use Entity\Metadados;

class EntityUpdateEntityDatabase extends EntityDatabase
{
    private $entity;
    private $old;
    private $new;

    /**
     * EntityUpdateEntityDatabase constructor.
     * @param string $entity
     * @param array $dicionarioOld
     * @param array $infoOld
     */
    public function __construct(string $entity, array $dicionarioOld, array $infoOld)
    {
        parent::__construct($entity);
        $this->setEntity($entity);
        $info = Metadados::getInfo($entity);
        $this->old = $dicionarioOld;
        $this->new = Metadados::getDicionario($entity);

        $this->adicionaCamposUsuario($info, $infoOld);
        $this->start();
    }

    /**
     *
     *  Adiciona Campos de Usuário, Autor e Multi-tenancy
     * @param array $info
     * @param array $infoOld
     */
    private function adicionaCamposUsuario(array $info, array $infoOld)
    {
        if (!empty($infoOld['user']) && $infoOld['user'] === 1)
            $this->old["999997"] = Metadados::generateUser();

        $publisher = json_decode(file_get_contents(PATH_HOME . VENDOR . "entity-ui/public/input_type/publisher.json"), !0)['publisher'];
        $owner = json_decode(file_get_contents(PATH_HOME . VENDOR . "entity-ui/public/input_type/owner.json"), !0)['owner'];

        if (!empty($infoOld['autor'])) {
            if ($infoOld['autor'] === 1) {
                $this->old["999998"] = array_replace_recursive($publisher, ["indice" => 999998, "default" => ""]);
            } elseif ($infoOld['autor'] === 2) {
                $this->old["999999"] = array_replace_recursive($owner, ["indice" => 999999, "default" => ""]);
            }
        }

        if (!empty($info['user']) && $info['user'] === 1)
            $this->new["999997"] = Metadados::generateUser();

        if (!empty($info['autor'])) {
            if ($info['autor'] === 1) {
                $this->new["999998"] = array_replace_recursive($publisher, ["indice" => 999998, "default" => ""]);
            } elseif ($info['autor'] === 2) {
                $this->new["999999"] = array_replace_recursive($owner, ["indice" => 999999, "default" => ""]);
            }
        }
    }

    /**
     * @param string $entity
     */
    public function setEntity(string $entity)
    {
        $this->entity = $entity;
    }

    public function start()
    {
        $this->checkChanges();
        $this->removeColumnsFromEntity();
        $this->addColumnsToEntity();

        /**
         * Devido ao uso de tabelas mult-tenancy, registros duplicados são válidos de usuário para usuário,
         * porém não para o mesmo usuário, ou seja, deve ser controlado pela aplicação e não pelo banco
         */
//        $this->createKeys();
    }

    private function checkChanges()
    {
        $changes = $this->getChanges();

        if ($changes) {
            $sql = new SqlCommand();

            foreach ($changes as $id => $dados) {
                if ($dados['group'] === "list")
                    $sql->exeCommand("RENAME TABLE `" . PRE . $this->entity . "_" . substr($dados['column'], 0, 5) . "` TO `" . PRE . $this->entity . "_" . substr($this->new[$id]['column'], 0, 5) . "`");
                else
                    $sql->exeCommand("ALTER TABLE " . PRE . $this->entity . " CHANGE {$dados['column']} " . parent::prepareSqlColumn($this->new[$id]));
            }
        }
    }

    private function getChanges()
    {
        $data = null;
        foreach ($this->old as $i => $d) {
            if (isset($this->new[$i])) {
                if ($d['column'] !== $this->new[$i]['column'] || $d['default'] !== $this->new[$i]['default'] || $d['size'] !== $this->new[$i]['size'])
                    $data[$i] = $d;

                /*
                 * Comenta Unique para não criar mais, devido a interferência em Multi-tenancy
                 *
                 *  if ($d['column'] !== $this->new[$i]['column'] || $d['unique'] !== $this->new[$i]['unique'] || $d['default'] !== $this->new[$i]['default'] || $d['size'] !== $this->new[$i]['size'])
                     $data[$i] = $d;*/
            }
        }

        return $data;
    }

    /**
     * Remove colunas que existiam
     */
    private function removeColumnsFromEntity()
    {
        $del = $this->getDeletes();

        if ($del) {
            foreach ($del as $id => $meta) {
                $this->dropKeysFromColumnRemoved($id, $meta);

                $sql = new SqlCommand();
                $sql->exeCommand("ALTER TABLE " . PRE . $this->entity . " DROP COLUMN " . $meta['column']);
            }
        }
    }

    private function getDeletes()
    {
        $data = null;
        foreach ($this->old as $i => $d) {
            if (!isset($this->new[$i]))
                $data[$i] = $d;
        }

        return $data;
    }

    private function dropKeysFromColumnRemoved($id, $dados)
    {
        $sql = new SqlCommand();

        //deleta dados da tabela relacional
        if ($dados['key'] === "relation") {

            if ($dados['type'] === "int") {
                $constraint = substr("c_{$this->entity}_" . substr($dados['column'], 0, 5) . "_" . substr($dados['relation'], 0, 5), 0, 64);
                $sql->exeCommand("ALTER TABLE " . PRE . $this->entity . " DROP FOREIGN KEY {$constraint}, DROP INDEX fk_" . $dados['column']);

            } elseif ($dados['group'] === "list") {
                $sql->exeCommand("DROP TABLE " . PRE . $this->entity . "_" . substr($dados['column'], 0, 5));
            }
        }

        if ($id < 999900) {

            //INDEX
            $sql->exeCommand("SHOW KEYS FROM " . PRE . $this->entity . " WHERE KEY_NAME ='index_{$id}'");
            if ($sql->getRowCount() > 0)
                $sql->exeCommand("ALTER TABLE " . PRE . $this->entity . " DROP INDEX index_" . $id);

            //UNIQUE - Comenta Unique para não criar mais, devido a interferência em Multi-tenancy
            /*$sql->exeCommand("SHOW KEYS FROM " . PRE . $this->entity . " WHERE KEY_NAME ='unique_{$id}'");
            if ($sql->getRowCount() > 0)
                $sql->exeCommand("ALTER TABLE " . PRE . $this->entity . " DROP INDEX unique_" . $id);*/
        }
    }

    private function addColumnsToEntity()
    {
        $add = $this->getAdds();

        if ($add) {
            $sql = new SqlCommand();
            foreach ($add as $id => $dados) {

                if ($dados['key'] !== "information")
                    $sql->exeCommand("ALTER TABLE " . PRE . $this->entity . " ADD " . parent::prepareSqlColumn($dados));

                if (in_array($dados['key'], ["title", "link", "status", "email", "cpf", "cnpj", "telefone", "cep"]))
                    parent::exeSql("ALTER TABLE `" . PRE . $this->entity . "` ADD KEY `index_{$id}` (`{$dados['column']}`)");

                if ($dados['key'] === "relation") {

                    if ($dados['group'] === "list")
                        parent::createRelationalTable($dados);
                    elseif ($dados['type'] === "int")
                        parent::createIndexFk($this->entity, $dados['column'], $dados['relation']);

                } elseif ($dados['key'] === "publisher") {
                    parent::createIndexFk($this->entity, $dados['column'], "usuarios", "", "publisher");
                }
            }
        }
    }

    private function getAdds()
    {
        $data = null;
        $i = 10000;
        foreach ($this->new as $e => $dic) {
            if (!isset($this->old[$e]))
                $data[$e] = $dic;
        }

        return $data;
    }
/*
    private function createKeys()
    {
        $sql = new SqlCommand();
        foreach ($this->new as $i => $dados) {

            $sql->exeCommand("SHOW KEYS FROM " . PRE . $this->entity . " WHERE KEY_NAME = 'unique_{$i}'");
            if ($sql->getRowCount() > 0 && !$dados['unique'])
                $sql->exeCommand("ALTER TABLE " . PRE . $this->entity . " DROP INDEX unique_" . $i);
            else if ($sql->getRowCount() === 0 && $dados['unique'])
                $sql->exeCommand("ALTER TABLE `" . PRE . $this->entity . "` ADD UNIQUE KEY `unique_{$i}` (`{$dados['column']}`)");
        }
    }*/
}
