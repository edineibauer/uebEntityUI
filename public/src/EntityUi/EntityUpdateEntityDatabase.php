<?php

namespace EntityUi;

use Conn\Delete;
use Conn\Read;
use Conn\SqlCommand;
use Entity\Metadados;
use Helpers\Helper;

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
        if($infoOld['user'] === 1)
            $this->old["999997"] = $this->generateUser();

        if(!empty($infoOld['autor'])) {
            if($infoOld['autor'] === 1) {
                $inputType = json_decode(file_get_contents(PATH_HOME . VENDOR . "entity-ui/public/entity/input_type.json"), true);
                $this->old["999998"] = array_replace_recursive($inputType['default'], $inputType['publisher'], ["indice" => 999998, "default" => $_SESSION['userlogin']['id']]);
            } elseif($infoOld['autor'] === 2) {
                $inputType = json_decode(file_get_contents(PATH_HOME . VENDOR . "entity-ui/public/entity/input_type.json"), true);
                $this->old["999999"] = array_replace_recursive($inputType['default'], $inputType['owner'], ["indice" => 999999, "default" => $_SESSION['userlogin']['id']]);
            }
        }

        if($info['user'] === 1)
            $this->new["999997"] = $this->generateUser();

        if(!empty($info['autor'])) {
            if($info['autor'] === 1) {
                $inputType = json_decode(file_get_contents(PATH_HOME . VENDOR . "entity-ui/public/entity/input_type.json"), true);
                $this->new["999998"] = array_replace_recursive($inputType['default'], $inputType['publisher'], ["indice" => 999998, "default" => $_SESSION['userlogin']['id']]);
            } elseif($info['autor'] === 2) {
                $inputType = json_decode(file_get_contents(PATH_HOME . VENDOR . "entity-ui/public/entity/input_type.json"), true);
                $this->new["999999"] = array_replace_recursive($inputType['default'], $inputType['owner'], ["indice" => 999999, "default" => $_SESSION['userlogin']['id']]);
            }
        }
    }

    private function generateUser()
    {
        $types = json_decode(file_get_contents(PATH_HOME . VENDOR . "entity-ui/public/entity/input_type.json"), !0);
        $mode = Helper::arrayMerge($types["default"], $types['list']);
        $mode['nome'] = "Usuário Acesso Vínculo";
        $mode['column'] = "usuarios_id";
        $mode['form'] = "false";
        $mode['datagrid'] = "false";
        $mode['default'] = "";
        $mode['unique'] = "false";
        $mode['update'] = "false";
        $mode['size'] = "";
        $mode['minimo'] = "";
        $mode['relation'] = "usuarios";
        $mode['indice'] = "999997";

        return $mode;
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
        $this->createKeys();
    }

    private function checkChanges()
    {
        $changes = $this->getChanges();

        if ($changes) {
            $sql = new SqlCommand();

            foreach ($changes as $id => $dados) {
                if (in_array($dados['format'], ["extend_mult", "list_mult", "selecao_mult", "checkbox_mult"]))
                    $sql->exeCommand("RENAME TABLE `" . PRE . $this->entity . "_{$dados['column']}` TO `" . PRE . $this->entity . "_" . $this->new[$id]['column'] . "`");
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
                if ($d['column'] !== $this->new[$i]['column'] || $d['unique'] !== $this->new[$i]['unique'] || $d['default'] !== $this->new[$i]['default'] || $d['size'] !== $this->new[$i]['size'])
                    $data[$i] = $d;
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

            if (!empty($d['select']) && (empty($this->new[$i]['select']) || $d['select'] !== $this->new[$i]['select'])) {
                foreach ($d['select'] as $e => $oldSelect) {
                    if (empty($this->new[$i]['select']) || !in_array($oldSelect, $this->new[$i]['select']))
                        $data[10001 + $e] = parent::getSelecaoUnique($d, $oldSelect)[1];
                }
            }

        }

        return $data;
    }

    private function dropKeysFromColumnRemoved($id, $dados)
    {
        $read = new Read();
        $delete = new Delete();
        $sql = new SqlCommand();

        $constraint = substr("c_{$this->entity}_{$dados['column']}_{$dados['relation']}", 0, 64);

        if (in_array($dados['format'], ["list", "publisher", "owner"]))
            $sql->exeCommand("ALTER TABLE " . PRE . $this->entity . " DROP FOREIGN KEY {$constraint}, DROP INDEX fk_" . $dados['column']);

        //deleta dados armazenados da extensão
        /*if ($dados['format'] === 'extend_mult') {
            $read->exeRead($this->entity);
            if ($read->getResult()) {
                foreach ($read->getResult() as $ent) {
                    $read->exeRead($this->entity . "_" . $dados['column'], "WHERE {$this->entity}_id = :ii", "ii={$ent['id']}");
                    if ($read->getResult()) {
                        foreach ($read->getResult() as $item)
                            $delete->exeDelete($dados['relation'], "WHERE id = :id", "id={$item[$dados['relation'] . "_id"]}");
                    }
                }
            }
        }

        //deleta dados da tabela relacional
        if ($dados['key'] === "relation" && $dados['group'] === "many")
            $sql->exeCommand("DROP TABLE " . PRE . $this->entity . "_" . $dados['column']);
        */

        if ($id < 999900) {

            //INDEX
            $sql->exeCommand("SHOW KEYS FROM " . PRE . $this->entity . " WHERE KEY_NAME ='index_{$id}'");
            if ($sql->getRowCount() > 0)
                $sql->exeCommand("ALTER TABLE " . PRE . $this->entity . " DROP INDEX index_" . $id);

            //UNIQUE
            $sql->exeCommand("SHOW KEYS FROM " . PRE . $this->entity . " WHERE KEY_NAME ='unique_{$id}'");
            if ($sql->getRowCount() > 0)
                $sql->exeCommand("ALTER TABLE " . PRE . $this->entity . " DROP INDEX unique_" . $id);
        }
    }

    private function addColumnsToEntity()
    {
        $add = $this->getAdds();

        if ($add) {
            $sql = new SqlCommand();
            foreach ($add as $id => $dados) {

                if($dados['group'] !== "many" || $dados['key'] !== "relation")
                    $sql->exeCommand("ALTER TABLE " . PRE . $this->entity . " ADD " . parent::prepareSqlColumn($dados));

                if ($dados['key'] === "relation") {
                    if ($dados['group'] === "many") {
//                        parent::createRelationalTable($dados);

                    } else {
                        if ($dados['format'] === "list")
                            parent::createIndexFk($this->entity, $dados['column'], $dados['relation'], "", $dados['key']);
                    }

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


            if (!empty($dic['select']) && (empty($this->old[$e]['select']) || $dic['select'] !== $this->old[$e]['select'])) {
                foreach ($dic['select'] as $newSelect) {
                    if (empty($this->old[$e]['select']) || !in_array($newSelect, $this->old[$e]['select'])) {
                        $data[$i] = parent::getSelecaoUnique($dic, $newSelect)[1];
                        $i++;
                    }
                }
            }
        }

        return $data;
    }

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
    }
}
