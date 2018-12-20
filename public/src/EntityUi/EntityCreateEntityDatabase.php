<?php

namespace EntityUi;

use Entity\Metadados;

class EntityCreateEntityDatabase extends EntityDatabase
{
    /**
     * EntityCreateEntityDatabase constructor.
     * @param string $entity
     * @param array $dados
     * @param int|null $autor
     */
    public function __construct(string $entity, array $dados, int $autor = null)
    {
        parent::__construct($entity);

        $info = $dados['info'] ?? [];
        unset($dados['info']);

        if ($data = Metadados::getDicionario($entity)) {

            //remove Strings from metadados para não salvar no banco
            foreach ($data as $i => $datum) {
                if ($datum['key'] === 'information')
                    unset($data[$i]);
            }

            //remove Strings from metadados para não salvar no banco
            foreach ($dados as $i => $dadosm) {
                if (!empty($dadosm['key']) && $dadosm['key'] === 'information')
                    unset($dados[$i]);
            }

            //verifica se tem autor ou owner
            $sql = new \Conn\SqlCommand();
            $base = json_decode(file_get_contents(PATH_HOME . VENDOR . "entity-ui/public/entity/input_type.json"), true);

            //remove owner and autor if necessary
            if (!empty($info)) {
                if ($info['autor'] === 1) {
                    $dados['dicionario'][9998] = array_merge($base['default'], $base['publisher']);
                } elseif ($info['autor'] === 2) {
                    $dados['dicionario'][9997] = array_merge($base['default'], $base['owner']);
                }
            }

            //add owner and autor if necessary
            if ($autor === 1) {
                $data[9998] = array_merge($base['default'], $base['publisher']);
            } elseif ($autor === 2) {
                $data[9997] = array_merge($base['default'], $base['owner']);
            }

            $sql = new \Conn\SqlCommand();
            $sql->exeCommand("SELECT 1 FROM " . PRE . "{$entity} LIMIT 1");
            if (!$sql->getErro() && !empty($dados['dicionario']))
                new EntityUpdateEntityDatabase($entity, $data, $dados['dicionario']);
            elseif ($sql->getErro())
                $this->createTableFromEntityJson($entity, $data);
        }
    }

    /**
     * @param string $entity
     * @param array $data
     */
    private function createTableFromEntityJson(string $entity, array $data)
    {
        $data = $this->checkCreateMultSelectField($data);
        $this->prepareCommandToCreateTable($entity, $data);
        $this->createKeys($entity, $data);
    }

    /**
     * @param array $dicionario
     * @return array
     */
    private function checkCreateMultSelectField(array $dicionario): array
    {
        foreach ($dicionario as $dic) {
            if (in_array($dic['key'], ["list_mult", "extend_mult", "selecao_mult", "list", "extend_add", "extend", "selecao", "checkbox_rel", "checkbox_mult"]) && !empty($dic['select'])) {
                $relDic = Metadados::getDicionario($dic['relation']);
                foreach ($dic['select'] as $select) {
                    foreach ($relDic as $item) {
                        if ($item['column'] === $select) {
                            $ret = parent::getSelecaoUnique($dic, $select);
                            $dicionario[$ret[0]] = $ret[1];
                        }
                    }
                }
            }
        }

        return $dicionario;
    }

    /**
     * @param string $entity
     * @param array $data
     */
    private function prepareCommandToCreateTable(string $entity, array $data)
    {
        $string = "CREATE TABLE IF NOT EXISTS `" . PRE . $entity . "` (`id` INT(11) NOT NULL";
        foreach ($data as $dados) {
            if (!in_array($dados['key'], ["list_mult", "extend_mult", "selecao_mult", "checkbox_mult"])) {
                $string .= ", " . parent::prepareSqlColumn($dados);
            }
        }

        $string .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8";

        parent::exeSql($string);
    }

    private function createKeys(string $entity, array $data)
    {
        parent::exeSql("ALTER TABLE `" . PRE . $entity . "` ADD PRIMARY KEY (`id`), MODIFY `id` int(11) NOT NULL AUTO_INCREMENT");

        foreach ($data as $i => $dados) {
            if ($dados['unique'])
                parent::exeSql("ALTER TABLE `" . PRE . $entity . "` ADD UNIQUE KEY `unique_{$i}` (`{$dados['column']}`)");

            if (in_array($dados['key'], ["title", "link", "status", "email", "cpf", "cnpj", "telefone", "cep"]))
                parent::exeSql("ALTER TABLE `" . PRE . $entity . "` ADD KEY `index_{$i}` (`{$dados['column']}`)");

            if (in_array($dados['key'], array("extend", "extend_mult", "extend_add", "list", "list_mult", "selecao", "checkbox_rel", "selecao_mult", "checkbox_mult", "selecaoUnique"))) {
                if (in_array($dados['key'], ["extend", "extend_add", "list", "selecao", "checkbox_rel", "selecaoUnique"]))
                    parent::createIndexFk($entity, $dados['column'], $dados['relation'], "", $dados['key']);
                else
                    parent::createRelationalTable($dados);
            } elseif ($dados['key'] === "publisher") {
                parent::createIndexFk($entity, $dados['column'], "usuarios", "", "publisher");
            }
        }
    }
}
