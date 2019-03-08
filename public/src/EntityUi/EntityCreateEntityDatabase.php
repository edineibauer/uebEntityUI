<?php

namespace EntityUi;

use Entity\Metadados;

class EntityCreateEntityDatabase extends EntityDatabase
{
    /**
     * EntityCreateEntityDatabase constructor.
     * @param string $entity
     * @param array $dicionarioOld
     * @param array $infoOld
     */
    public function __construct(string $entity, array $dicionarioOld, array $infoOld)
    {
        parent::__construct($entity);

        $sql = new \Conn\SqlCommand();
        $sql->exeCommand("SELECT 1 FROM " . PRE . "{$entity} LIMIT 1");
        
        if (!$sql->getErro() && !empty($dicionarioOld))
            new EntityUpdateEntityDatabase($entity, $dicionarioOld, $infoOld);
        elseif ($sql->getErro())
            $this->createTableFromEntityJson($entity);
    }

    private function generateUser()
    {
        $types = json_decode(file_get_contents(PATH_HOME . VENDOR . "entity-ui/public/entity/input_type.json"), !0);
        $mode = array_merge_recursive($types["default"], $types['list']);
        $mode['nome'] = "Usuário Acesso Vínculo";
        $mode['column'] = "usuarios_id";
        $mode['form'] = "false";
        $mode['datagrid'] = "false";
        $mode['default'] = "false";
        $mode['unique'] = "false";
        $mode['update'] = "false";
        $mode['size'] = "";
        $mode['minimo'] = "";
        $mode['relation'] = "usuarios";
        $mode['indice'] = "999998";

        return $mode;
    }

    /**
     * @param string $entity
     * @param array $data
     */
    private function createTableFromEntityJson(string $entity)
    {
        $metadados = $this->adicionaCamposUsuario($entity);

        $string = "CREATE TABLE IF NOT EXISTS `" . PRE . $entity . "` (`id` INT(11) NOT NULL";
        foreach ($metadados as $dados)
            $string .= ", " . parent::prepareSqlColumn($dados);

        $string .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8";

        parent::exeSql($string);

        $this->createKeys($entity, $metadados);
    }

    /**
     *
     *  Adiciona Campos de Usuário, Autor e Multi-tenancy
     * @param array $info
     * @param array $infoOld
     * @return array
     */
    private function adicionaCamposUsuario(string $entity): array
    {
        $info = Metadados::getInfo($entity);
        $metadados = Metadados::getDicionario($entity);

        if($info['user'] === 1)
            $metadados["999997"] = $this->generateUser();

        if(!empty($info['autor'])) {
            if($info['autor'] === 1) {
                $inputType = json_decode(file_get_contents(PATH_HOME . VENDOR . "entity-ui/public/entity/input_type.json"), true);
                $metadados["999998"] = array_replace_recursive($inputType['default'], $inputType['publisher'], ["indice" => 999998, "default" => $_SESSION['userlogin']['id']]);
            } elseif($info['autor'] === 2) {
                $inputType = json_decode(file_get_contents(PATH_HOME . VENDOR . "entity-ui/public/entity/input_type.json"), true);
                $metadados["999999"] = array_replace_recursive($inputType['default'], $inputType['owner'], ["indice" => 999999, "default" => $_SESSION['userlogin']['id']]);
            }
        }

        return $metadados;
    }

    /**
     * @param string $entity
     * @param array $metadados
     */
    private function createKeys(string $entity, array $metadados)
    {
        parent::exeSql("ALTER TABLE `" . PRE . $entity . "` ADD PRIMARY KEY (`id`), MODIFY `id` int(11) NOT NULL AUTO_INCREMENT");

        foreach ($metadados as $i => $dados) {
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

    private function generatePrimary()
    {
        return [
            "format" => "none",
            "type" => "int",
            "nome" => "id",
            "column" => "id",
            "size" => "",
            "key" => "identifier",
            "unique" => "true",
            "default" => "false",
            "update" => "false",
            "relation" => "",
            "minimo" => "",
            "allow" => [
                "regex" => "",
                "options" => "",
                "validate" => ""
            ],
            "form" => [
                "input" => "hidden",
                "cols" => "12",
                "colm" => "",
                "coll" => "",
                "class" => "",
                "style" => ""
            ],
            "select" => [],
            "filter" => []
        ];
    }
}
