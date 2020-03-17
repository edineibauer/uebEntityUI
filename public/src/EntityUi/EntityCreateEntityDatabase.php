<?php

namespace EntityUi;

use Entity\Metadados;

class EntityCreateEntityDatabase extends EntityDatabase
{
    /**
     * EntityCreateEntityDatabase constructor.
     * @param string $entity
     * @param array|null $dicionarioOld
     * @param array|null $infoOld
     */
    public function __construct(string $entity, array $dicionarioOld = null, array $infoOld = null)
    {
        parent::__construct($entity);

        $sql = new \Conn\SqlCommand();
        $sql->exeCommand("SELECT 1 FROM " . PRE . "{$entity} LIMIT 1");
        
        if (!$sql->getErro() && !empty($dicionarioOld) && !empty($infoOld))
            new EntityUpdateEntityDatabase($entity, $dicionarioOld, $infoOld);
        elseif ($sql->getErro())
            $this->createTableFromEntityJson($entity);
    }

    /**
     * @param string $entity
     * @param array $data
     */
    private function createTableFromEntityJson(string $entity)
    {
        $metadados = $this->adicionaCamposUsuario($entity);

        if(!empty($metadados)) {
            $string = "CREATE TABLE IF NOT EXISTS `" . PRE . $entity . "` (`id` INT(11) NOT NULL";
            foreach ($metadados as $dados)
                $string .= ", " . parent::prepareSqlColumn($dados);

            $string .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8";

            parent::exeSql($string);

            $this->createKeys($entity, $metadados);
        }
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
        $metadados = Metadados::getDicionario($entity) ?? [];

        if(!empty($info['user']) && $info['user'] === 1)
            $metadados["999997"] = Metadados::generateUser();

        if(!empty($info['autor'])) {
            if($info['autor'] === 1) {
                $inputType = json_decode(file_get_contents(PATH_HOME . VENDOR . "entity-ui/public/entity/input_type.json"), true);
                $metadados["999998"] = array_replace_recursive($inputType['default'], $inputType['publisher'], ["indice" => 999998, "default" => ""]);
            } elseif($info['autor'] === 2) {
                $inputType = json_decode(file_get_contents(PATH_HOME . VENDOR . "entity-ui/public/entity/input_type.json"), true);
                $metadados["999999"] = array_replace_recursive($inputType['default'], $inputType['owner'], ["indice" => 999999, "default" => ""]);
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

            /*
             * Comenta Unique para não criar mais, devido a interferência em Multi-tenancy
             *
             * if ($dados['unique'])
                parent::exeSql("ALTER TABLE `" . PRE . $entity . "` ADD UNIQUE KEY `unique_{$i}` (`{$dados['column']}`)");
            */

            if (in_array($dados['key'], ["title", "link", "status", "email", "cpf", "cnpj", "telefone", "cep"]))
                parent::exeSql("ALTER TABLE `" . PRE . $entity . "` ADD KEY `index_{$i}` (`{$dados['column']}`)");


            if ($dados['key'] === "relation") {
                if ($dados['type'] === "int")
                    parent::createIndexFk($entity, $dados['column'], $dados['relation']);

                //este criaria uma tabela intermediária com o id de relacionamento entre as duas tabelas
//                else
//                    parent::createRelationalTable($dados);

//            } elseif ($dados['key'] === "publisher") {
//                parent::createIndexFk($entity, $dados['column'], "usuarios", "", "publisher");
            }
        }
    }
}
