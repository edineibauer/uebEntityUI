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
        $this->createKeys();
    }

    private function checkChanges()
    {
        $changes = $this->getChanges();

        if ($changes) {
            $sql = new SqlCommand();

            foreach ($changes as $id => $dados) {
                // Se for uma tabela relacional tipo list
                if (!empty($dados['group']) && $dados['group'] === "list") {
                    $oldTableName = $this->entity . "_" . substr($dados['column'], 0, 5);
                    $newTableName = $this->entity . "_" . substr($this->new[$id]['column'], 0, 5);

                    if (parent::tableExists($oldTableName) && $oldTableName !== $newTableName) {
                        $sql->exeCommand("RENAME TABLE `{$oldTableName}` TO `{$newTableName}`");
                    }
                } else {
                    // Verifica se a coluna mudou de nome
                    $columnNameChanged = $dados['column'] !== $this->new[$id]['column'];

                    if ($columnNameChanged) {
                        // Se mudou o nome da coluna, precisa remover foreign keys antes
                        $foreignKeys = parent::getColumnForeignKeys($this->entity, $dados['column']);
                        $fkData = []; // Armazena dados das FKs para recriar depois

                        foreach ($foreignKeys as $fk) {
                            // Busca informações completas da FK antes de remover
                            $sql->exeCommand("SELECT REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME, DELETE_RULE FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND CONSTRAINT_NAME = '{$fk['CONSTRAINT_NAME']}' AND TABLE_NAME = '{$this->entity}'");
                            $fkInfo = $sql->getResult();
                            if ($fkInfo) {
                                $fkData[] = [
                                    'constraint' => $fk['CONSTRAINT_NAME'],
                                    'referenced_table' => $fkInfo[0]['REFERENCED_TABLE_NAME'],
                                    'referenced_column' => $fkInfo[0]['REFERENCED_COLUMN_NAME'],
                                    'delete_rule' => $fkInfo[0]['DELETE_RULE']
                                ];
                            }
                            parent::dropForeignKey($this->entity, $fk['CONSTRAINT_NAME']);
                        }

                        // Remove índice fk se existir
                        parent::dropIndex($this->entity, "fk_" . $dados['column']);
                    }

                    // Executa a alteração da coluna
                    $sql->exeCommand("ALTER TABLE " . $this->entity . " CHANGE `{$dados['column']}` " . parent::prepareSqlColumn($this->new[$id], 1));

                    // Recria as foreign keys com o novo nome da coluna se necessário
                    if ($columnNameChanged && isset($fkData)) {
                        foreach ($fkData as $fkInfo) {
                            $cascade = $fkInfo['delete_rule'] === 'CASCADE';
                            parent::createIndexFk($this->entity, $this->new[$id]['column'], $fkInfo['referenced_table'], $this->new[$id]['column'], $cascade);
                        }
                    }
                }

                /**
                 * change general_info column name
                 */
                if(file_exists(PATH_HOME . "entity/general/general_info.json")) {
                    $oldName = $dados['column'];
                    $newName = $this->new[$id]['column'];

                    if ($oldName !== $newName) {
                        $general = json_decode(file_get_contents(PATH_HOME . "entity/general/general_info.json"), !0);

                        foreach ($general as $entity => $gen) {
                            if (!empty($gen['belongsTo'])) {
                                foreach ($gen['belongsTo'] as $i => $gene) {
                                    foreach ($gene as $key => $value) {
                                        if(isset($value['column']) && $value['column'] === $oldName)
                                            $general[$entity]['belongsTo'][$i][$key]['column'] = $newName;
                                    }
                                }
                            }
                        }

                        $f = fopen(PATH_HOME . "entity/general/general_info.json", "w");
                        fwrite($f, json_encode($general));
                        fclose($f);
                    }
                }
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
                $sql->exeCommand("ALTER TABLE " . $this->entity . " DROP COLUMN " . $meta['column']);
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

        // 1. Remove todas as foreign keys que a coluna possui
        $foreignKeys = parent::getColumnForeignKeys($this->entity, $dados['column']);
        foreach ($foreignKeys as $fk) {
            parent::dropForeignKey($this->entity, $fk['CONSTRAINT_NAME']);
        }

        // 2. Verifica se outras tabelas referenciam esta coluna (muito raro, mas possível)
        $referencingKeys = parent::getReferencingForeignKeys($this->entity, $dados['column']);
        foreach ($referencingKeys as $refKey) {
            // Remove a foreign key da tabela que referencia
            parent::dropForeignKey($refKey['TABLE_NAME'], $refKey['CONSTRAINT_NAME']);
        }

        // 3. Remove tabela relacional se for uma relação tipo list
        if ($dados['key'] === "relation") {
            if ($dados['type'] === "int") {
                // Remove índice fk específico se existir
                parent::dropIndex($this->entity, "fk_" . $dados['column']);

            } elseif ($dados['group'] === "list") {
                $relationalTable = $this->entity . "_" . substr($dados['column'], 0, 5);
                if (parent::tableExists($relationalTable)) {
                    $sql->exeCommand("DROP TABLE `{$relationalTable}`");
                }
            }
        }

        // 4. Remove índices padrão da coluna (index_{$id}, unique_{$id})
        if ($id < 999900) {
            parent::dropIndex($this->entity, "index_" . $id);
            parent::dropIndex($this->entity, "unique_" . $id);
        }

        // 5. Remove todos os outros índices que possam estar associados à coluna
        $sql->exeCommand("SHOW KEYS FROM `{$this->entity}` WHERE Column_name = '{$dados['column']}'");
        $indexes = $sql->getResult();
        if ($indexes) {
            foreach ($indexes as $index) {
                // Não tenta remover a PRIMARY KEY
                if ($index['Key_name'] !== 'PRIMARY') {
                    parent::dropIndex($this->entity, $index['Key_name']);
                }
            }
        }
    }

    private function addColumnsToEntity()
    {
        $add = $this->getAdds();

        if ($add) {
            $sql = new SqlCommand();
            foreach ($add as $id => $dados) {

                // Adiciona a coluna apenas se não for campo informativo e se a coluna não existir
                if ($dados['key'] !== "information" && !parent::columnExists($this->entity, $dados['column'])) {
                    $sql->exeCommand("ALTER TABLE " . $this->entity . " ADD " . parent::prepareSqlColumn($dados, 1));

                    // Verifica se houve erro ao adicionar
                    if ($sql->getErro()) {
                        continue; // Pula para próxima coluna se houve erro
                    }
                }

                // Adiciona índice para campos relevantes
                if (in_array($dados['key'], ["title", "link", "status", "email", "cpf", "cnpj", "telefone", "cep"]) || in_array($dados['format'], ["select", "boolean", "radio"])) {
                    $sql->exeCommand("SHOW KEYS FROM " . $this->entity . " WHERE KEY_NAME ='index_{$id}'");
                    if ($sql->getRowCount() === 0) {
                        parent::exeSql("ALTER TABLE `" . $this->entity . "` ADD KEY `index_{$id}` (`{$dados['column']}`)", false);
                    }
                }

                // Cria relações
                if ($dados['key'] === "relation") {
                    if ($dados['group'] === "list") {
                        // Verifica se a tabela relacional já existe antes de criar
                        $relationalTable = $this->entity . "_" . substr($dados['column'], 0, 5);
                        if (!parent::tableExists($relationalTable)) {
                            parent::createRelationalTable($dados);
                        }
                    } elseif ($dados['type'] === "int") {
                        parent::createIndexFk($this->entity, $dados['column'], $dados['relation']);
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
        $sql = new SqlCommand();
        foreach ($this->new as $e => $dic) {
            if (!isset($this->old[$e]))
                $data[$e] = $dic;

            /**
             * Verifica se essa coluna existe no banco de dados, se não existir, cria a coluna
             */
            $sql->exeCommand("SHOW COLUMNS FROM `" . $this->entity . "` LIKE '" . $dic['column'] . "'");
            if(!$sql->getResult())
                $data[$e] = $dic;
        }

        return $data;
    }

    private function createKeys()
    {
        $sql = new SqlCommand();
        foreach ($this->new as $i => $dados) {
            $sql->exeCommand("SHOW KEYS FROM " . $this->entity . " WHERE KEY_NAME = 'unique_{$i}'");
            if ($sql->getRowCount() > 0)
                $sql->exeCommand("ALTER TABLE " . $this->entity . " DROP INDEX unique_" . $i);

            if ($dados['unique'])
                $sql->exeCommand("ALTER TABLE `" . $this->entity . "` ADD UNIQUE KEY `unique_{$i}` (`{$dados['column']}`, `system_id`)");
        }
    }
}
