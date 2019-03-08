<?php

namespace EntityUi;

use Helpers\Helper;
use Entity\Metadados;
use Entity\Dicionario;

class SaveEntity
{
    private $entity;
    private $id;

    /**
     * SaveEntity constructor.
     * Nome da entidade, dicionário de dados e identificador atual

     * @param string|null $entity
     * @param string|null $icon
     * @param int $user
     * @param int|null $autor
     * @param null $data
     * @param int|null $id
     */
    public function __construct(string $entity = null, string $icon = null, int $user, int $autor = null, $data = null, int $id = null)
    {
        if ($entity) {
            $this->entity = $entity;
            if ($id)
                $this->id = $id;

            if ($data)
                $this->start($data, $icon, $autor, $user);
        }
    }

    public function importMetadados(string $entity)
    {
        $this->entity = $entity;
        $data = json_decode(file_get_contents(PATH_HOME . "entity/cache/{$this->entity}.json"), true);
        $this->id = 1;
        foreach ($data as $i => $datum) {
            if ($i > $this->id)
                $this->id = (int)$i;
        }
        $this->id++;
        $tmp['info'] = $this->generateInfo($data);
        $this->createEntityJson($tmp['info'], "info");

        new EntityCreateEntityDatabase($this->entity, $tmp);
    }

    /**
     * @param null $metadados
     * @param string|null $icon
     * @param int|null $autor
     * @param int $user
     */
    private function start($metadados = null, string $icon = null, int $autor = null, int $user)
    {
        try {
            $data['dicionario'] = Metadados::getDicionario($this->entity);

            if ($data['dicionario']) {
                $data['info'] = Metadados::getInfo($this->entity);
            } else {
                Helper::createFolderIfNoExist(PATH_HOME . "entity");
                Helper::createFolderIfNoExist(PATH_HOME . "entity/cache");
                Helper::createFolderIfNoExist(PATH_HOME . "entity/cache/info");
            }

            $metadados["0"] = $this->generatePrimary();
            $this->createEntityJson($metadados);
            $this->createEntityJson($this->generateInfo($metadados, $icon, $autor, $user), "info");

            new EntityCreateEntityDatabase($this->entity, $data);

        } catch (\Exception $e) {
            echo $e->getMessage() . " #linha {$e->getLine()}";
            die;
        }
    }

    /**
     * @param array $data
     * @param mixed $dir
     */
    private function createEntityJson(array $data, $dir = null)
    {
        $fp = fopen(PATH_HOME . "entity/cache/" . ($dir ? $dir . "/" : "") . $this->entity . ".json", "w");
        fwrite($fp, json_encode($data));
        fclose($fp);
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

    /**
     * @param array $metadados
     * @param string|null $icon
     * @param int|null $autor
     * @param int $user
     * @return array
     */
    private function generateInfo(array $metadados, string $icon = null, int $autor = null, int $user): array
    {
        $data = [
            "icon" => $icon, "autor" => $autor, "user" => $user,
            "required" => null, "unique" => null, "update" => null,
            "identifier" => $this->id, "title" => null, "link" => null, "status" => null, "date" => null, "datetime" => null, "valor" => null, "email" => null, "password" => null, "tel" => null, "cpf" => null, "cnpj" => null, "cep" => null, "time" => null, "week" => null, "month" => null, "year" => null,
            "extend" => null, "extend_add" => null, "extend_mult" => null, "list" => null, "list_mult" => null, "folder" => null, "extend_folder" => null
        ];

        foreach ($metadados as $i => $dados) {
            if($dados['unique'] === "true" || $dados['unique'] === true || $dados['unique'] == 1)
                $data['unique'][] = $i;

            if (in_array($dados['key'], ["extend", "extend_add", "extend_mult", "list", "list_mult", "folder", "extend_folder"]))
                $data[$dados['key']][] = $i;

            if (in_array($dados['format'], ["title", "link", "status", "date", "datetime", "valor", "email", "password", "tel", "cpf", "cnpj", "cep", "time", "week", "month", "year"]))
                $data[$dados['format']] = $i;

            if ($dados['default'] === false || $dados['default'] === "false")
                $data['required'][] = $i;

            if ($dados['update'] === "true" || $dados['update'] === true || $dados['update'] == 1)
                $data["update"][] = $i;
        }

        $this->createGeneral($data);

        return $data;
    }

    /**
     * @param array $metadados
     */
    private function createGeneral(array $metadados)
    {
        $general = [];
        if (file_exists(PATH_HOME . "entity/general/general_info.json"))
            $general = json_decode(file_get_contents(PATH_HOME . "entity/general/general_info.json"), true);

        if (!empty($metadados['owner'])) {
            foreach ($metadados['owner'] as $owner) {
                $add = true;
                if(!empty($general[$owner["entity"]]['owner'])) {
                    foreach ($general[$owner["entity"]]['owner'] as $ow) {
                        if ($ow[0] === $this->entity)
                            $add = false;
                    }
                }
                if ($add)
                    $general[$owner["entity"]]['owner'][] = [$this->entity, $owner["column"], $owner["userColumn"]];
            }
        }
        if (!empty($metadados['ownerPublisher'])) {
            foreach ($metadados['ownerPublisher'] as $owner) {
                $add = true;
                if(!empty($general[$owner["entity"]]['ownerPublisher'])) {
                    foreach ($general[$owner["entity"]]['ownerPublisher'] as $ow) {
                        if ($ow[0] === $this->entity)
                            $add = false;
                    }
                }
                if ($add)
                    $general[$owner["entity"]]['ownerPublisher'][] = [$this->entity, $owner["column"], $owner["userColumn"]];
            }
        }


        /* Remove all belongsTo */
        foreach (Helper::listFolder(PATH_HOME . "entity/cache") as $item) {
            if(preg_match('/\.json$/i', $item))
                $general[str_replace(".json", "", $item)]['belongsTo'] = [];
        }

        /* Add belongsTo */
        foreach (Helper::listFolder(PATH_HOME . "entity/cache") as $item) {
            if(preg_match('/\.json$/i', $item)){
                $itemEntity = str_replace(".json", "", $item);
                foreach (json_decode(file_get_contents(PATH_HOME . "entity/cache/{$item}"), true) as $meta) {
                    if(!empty($meta['relation'])){
                        $dd = new Dicionario($itemEntity);
                        $columnRelevant = "";
                        if($relevant = $dd->getRelevant())
                            $columnRelevant = $relevant->getColumn();

                        $general[$meta['relation']]['belongsTo'][] = [
                            $itemEntity => [
                                "column" => $meta['column'],
                                "key" => $meta['key'],
                                "relevant" => $columnRelevant,
                                "grid_class_relational" => !empty($meta['datagrid']['grid_class_relational']) ? $meta['datagrid']['grid_class_relational'] : null,
                                "grid_style_relational" => !empty($meta['datagrid']['grid_style_relational']) ? $meta['datagrid']['grid_style_relational'] : null,
                                "grid_template_relational" => !empty($meta['datagrid']['grid_template_relational']) ? $meta['datagrid']['grid_template_relational'] : null,
                                "datagrid" => !empty($meta['datagrid']['grid_relevant_relational']) ? $meta['datagrid']['grid_relevant_relational'] : null
                            ]
                        ];
                    }
                }
            }
        }

        /* Create General */

        Helper::createFolderIfNoExist(PATH_HOME . "entity/general");
        $fp = fopen(PATH_HOME . "entity/general/general_info.json", "w");
        fwrite($fp, json_encode($general));
        fclose($fp);
    }
}