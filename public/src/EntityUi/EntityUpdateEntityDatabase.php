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
     * @param array $novos
     * @param array $dados
     */
    public function __construct(string $entity, array $novos, array $dados)
    {
        parent::__construct($entity);
        $this->setEntity($entity);
        $this->old = $dados;
        $this->new = $novos;
        $this->start();
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
            foreach ($del as $id => $dic) {
                $this->dropKeysFromColumnRemoved($id, $dic);

                $sql = new SqlCommand();
                $sql->exeCommand("ALTER TABLE " . PRE . $this->entity . " DROP COLUMN " . $dic['column']);
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

        if (in_array($dados['format'], ["list", "extend", "extend_add", "selecao", "checkbox_rel", "selecaoUnique", "publisher", "owner"]))
            $sql->exeCommand("ALTER TABLE " . PRE . $this->entity . " DROP FOREIGN KEY {$constraint}, DROP INDEX fk_" . $dados['column']);

        //deleta dados armazenados da extensão
        if (in_array($dados['format'], ['extend_add', 'extend'])) {
            $read->exeRead($this->entity);
            if ($read->getResult()) {
                foreach ($read->getResult() as $ent) {
                    if (!empty($ent[$dados['column']]))
                        $delete->exeDelete($dados['relation'], "WHERE id = :id", "id={$ent[$dados['column']]}");
                }
            }

        } elseif ($dados['format'] === 'extend_mult') {
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

        if ($dados['format'] === "list_mult" || $dados['format'] === "extend_mult" || $dados['format'] === "selecao_mult" || $dados['format'] === "checkbox_mult") {

            //deleta dados da tabela relacional
            $sql->exeCommand("DROP TABLE " . PRE . $this->entity . "_" . $dados['column']);

        }

        if ($id < 10000) {

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
                if ($dados['key'] === "relation") {
                    if ($dados['group'] === "many") {
//                        parent::createRelationalTable($dados);

                    } else {
                        $sql->exeCommand("ALTER TABLE " . PRE . $this->entity . " ADD " . parent::prepareSqlColumn($dados));

                        if ($dados['format'] === "list")
                            parent::createIndexFk($this->entity, $dados['column'], $dados['relation'], "", $dados['key']);
                    }

                } elseif ($dados['key'] === "publisher") {
                    $sql->exeCommand("ALTER TABLE " . PRE . $this->entity . " ADD " . parent::prepareSqlColumn($dados));
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
