<?php

namespace EntityUi;

use Entity\Metadados;
use Helpers\Helper;
use Config\Config;

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

    private  function createEntityFromJson(string $lib, string $entity) {

        if (file_exists(PATH_HOME . VENDOR . $lib . "/public/entity/cache/{$entity}.json")) {

            copy(PATH_HOME . VENDOR . "{$lib}/public/entity/cache/{$entity}.json", PATH_HOME . "entity/cache/{$entity}.json");

            /* INFO */
            if (file_exists(PATH_HOME . VENDOR . "{$lib}/public/entity/cache/info/{$entity}.json")) {

                //copia info
                if (file_exists(PATH_HOME . "entity/cache/info/{$entity}.json"))
                    unlink(PATH_HOME . "entity/cache/info/{$entity}.json");

                copy(PATH_HOME . VENDOR . "{$lib}/public/entity/cache/info/{$entity}.json", PATH_HOME . "entity/cache/info/{$entity}.json");

            } elseif (!file_exists(PATH_HOME . "entity/cache/info/{$entity}.json")) {

                //cria info
                $data = Config::createInfoFromMetadados(Metadados::getDicionario(PATH_HOME . VENDOR . "{$lib}/public/entity/cache/{$entity}.json"));
                $fp = fopen(PATH_HOME . "entity/cache/info/" . $entity . ".json", "w");
                fwrite($fp, json_encode($data));
                fclose($fp);
            }

            new EntityCreateEntityDatabase($entity);
        }
    }

    /**
     * Vare dicionário de metadados atrás de relações para
     * verificar se essas entidades relacionais existem no
     * sistema, caso não, adiciona elas
     * @param array $metadados
     */
    private function createRelationalEntitys(array $metadados) {
        foreach ($metadados as $metadado) {
            if($metadado['key'] === "relation" && $metadado['type'] === "int") {
                $entity = $metadado['relation'];

                if (!file_exists(PATH_HOME . "entity/cache/{$entity}.json")) {
                    foreach (Helper::listFolder(PATH_HOME . VENDOR) as $lib) {
                        if (file_exists(PATH_HOME . VENDOR . "{$lib}/public/entity/cache/{$entity}.json")) {
                            $this->createEntityFromJson($lib, $entity);
                            break;
                        }
                    }
                }

            }
        }
    }

    /**
     * @param string $entity
     * @param array $data
     */
    private function createTableFromEntityJson(string $entity)
    {
        list($metadados, $info) = $this->adicionaCamposUsuario($entity);

        if(!empty($metadados)) {

            //Verifica se as entidades relacionais existem, se não, cria elas antes
            $this->createRelationalEntitys($metadados);

            $string = "CREATE TABLE IF NOT EXISTS `" . PRE . $entity . "` (`id` INT(11) NOT NULL, `system_id` INT(11) DEFAULT NULL";
            foreach ($metadados as $dados)
                $string .= ", " . parent::prepareSqlColumn($dados);
            $string .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8";

            //executa comando para criar a tabela
            parent::exeSql($string);

            //cria as chaves de relacionamento
            $this->createKeys($entity, $metadados, $info);
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
                $publisher = json_decode(file_get_contents(PATH_HOME . VENDOR . "entity-ui/public/input_type/publisher.json"), !0)['publisher'];
                $metadados["999998"] = array_replace_recursive($publisher, ["indice" => 999998, "default" => ""]);
            } elseif($info['autor'] === 2) {
                $owner = json_decode(file_get_contents(PATH_HOME . VENDOR . "entity-ui/public/input_type/owner.json"), !0)['owner'];
                $metadados["999999"] = array_replace_recursive($owner, ["indice" => 999999, "default" => ""]);
            }
        }

        return [$metadados, $info];
    }

    /**
     * @param string $entity
     * @param array $metadados
     * @param array $info
     */
    private function createKeys(string $entity, array $metadados, array $info)
    {
        parent::exeSql("ALTER TABLE `" . PRE . $entity . "` ADD PRIMARY KEY (`id`), MODIFY `id` int(11) NOT NULL AUTO_INCREMENT");

        if(!empty($info['system']))
            parent::createIndexFk($entity, 'system_id', $info['system']);

        foreach ($metadados as $i => $dados) {

            if (in_array($dados['key'], ["title", "link", "status", "email", "cpf", "cnpj", "telefone", "cep"]))
                parent::exeSql("ALTER TABLE `" . PRE . $entity . "` ADD KEY `index_{$i}` (`{$dados['column']}`)");

            if ($dados['key'] === "relation") {

                if ($dados['group'] === "list")
                    parent::createRelationalTable($dados);
                elseif ($dados['type'] === "int")
                    parent::createIndexFk($entity, $dados['column'], $dados['relation']);

            } elseif ($dados['key'] === "publisher") {
                parent::createIndexFk($entity, $dados['column'], "usuarios", "", "publisher");
            }
        }
    }
}
