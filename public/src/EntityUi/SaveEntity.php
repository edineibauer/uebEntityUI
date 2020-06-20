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
     * @param string|null $system
     * @param string|null $icon
     * @param int $user
     * @param int|null $autor
     * @param null $data
     * @param int|null $id
     */
    public function __construct(string $entity = "", string $system = "", string $icon = "", int $user = 0, int $autor = null, $data = null, int $id = null)
    {
        if ($entity) {
            $this->entity = $entity;
            if ($id)
                $this->id = $id;

            if ($data)
                $this->start($system, $user, $data, $icon, $autor);
        }
    }

    public function importMetadados(string $entity)
    {
        $this->entity = $entity;
        $metadados = json_decode(file_get_contents(PATH_HOME . "entity/cache/{$this->entity}.json"), true);
        $this->id = 1;
        foreach ($metadados as $i => $datum) {
            if ($i > $this->id)
                $this->id = (int)$i;
        }
        $this->id++;
        $this->createEntityJson($this->generateInfo("", $metadados), "info");

        new EntityCreateEntityDatabase($this->entity);
    }

    /**
     * @param string $system
     * @param int $user
     * @param null $metadados
     * @param string|null $icon
     * @param int|null $autor
     */
    private function start(string $system, int $user, $metadados = null, string $icon = "", int $autor = null)
    {
        try {

            //exclui todos os filedsCustom da entidade
            Helper::recurseDelete(PATH_HOME . "_cdn/fieldsCustom/" . $this->entity);

            //obtém dicionario atual (old)
            $infoOld = Metadados::getInfo($this->entity);
            if (!$metadadosOld = Metadados::getDicionario($this->entity)) {
                $metadadosOld = [];
                $infoOld = [];
                Helper::createFolderIfNoExist(PATH_HOME . "entity");
                Helper::createFolderIfNoExist(PATH_HOME . "entity/cache");
                Helper::createFolderIfNoExist(PATH_HOME . "entity/cache/info");
            }

            //criar novo dicionario
            $this->createEntityJson($metadados);
            $this->createEntityJson($this->generateInfo($system, $metadados, $icon, $autor, $user), "info");

            //criar/atualizar banco
            new EntityCreateEntityDatabase($this->entity, $metadadosOld, $infoOld);

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

    /**
     * @param string $system
     * @param array $metadados
     * @param string $icon
     * @param int|null $autor
     * @param int $user
     * @return array
     */
    private function generateInfo(string $system, array $metadados, string $icon = "", int $autor = null, int $user = 0): array
    {
        $data = [
            "icon" => $icon, "autor" => $autor, "user" => $user, "system" => $system, "setor" => "", "columns_readable" => ["id", "system_id"],
            "required" => null, "unique" => null, "update" => null,
            "identifier" => $this->id, "title" => null, "link" => null, "status" => null, "date" => null, "datetime" => null, "valor" => null, "email" => null, "password" => null, "tel" => null, "cpf" => null, "cnpj" => null, "cep" => null, "time" => null, "week" => null, "month" => null, "year" => null,
            "publisher" => "", "owner" => null, "ownerPublisher" => null, "extend" => null, "extend_mult" => null, "list" => null, "list_mult" => null, "folder" => null, "extend_folder" => null
        ];

        foreach ($metadados as $i => $dados) {
            if(is_array($dados)) {
                if (!empty($dados['unique']) && ($dados['unique'] === "true" || $dados['unique'] === true || $dados['unique'] == 1))
                    $data['unique'][] = $i;

                if (!empty($dados['key']) && $dados['key'] === "relation")
                    $data[$dados['key']][] = $i;

                if (!empty($dados['format']) && !empty($dados['key']) && $dados['format'] !== "password" && $dados['key'] !== "information")
                    $data['columns_readable'][] = $dados['column'];

                if (!empty($dados['format']) && in_array($dados['format'], ["title", "link", "status", "date", "datetime", "valor", "email", "password", "tel", "cpf", "cnpj", "cep", "time", "week", "month", "year"]))
                    $data[$dados['format']] = $i;

                if (!empty($dados['key']) && $dados['key'] === "publisher")
                    $data["publisher"] = $i;

                if (!empty($dados['format']) && $dados['format'] === "setor")
                    $data["setor"] = $dados['column'];

                if (!empty($dados['default']) && ($dados['default'] === false || $dados['default'] === "false"))
                    $data['required'][] = $i;

                if (!empty($dados['update']) && ($dados['update'] === "true" || $dados['update'] === true || $dados['update'] == 1))
                    $data["update"][] = $i;

                if (!empty($dados['relation']) && !empty($dados['format']) && $dados['relation'] === "usuarios" && $dados['format'] === "extend")
                    $data = $this->checkOwnerList($data, $metadados, $dados['column']);
            }
        }

        try {
            $this->createGeneral($data);
        } catch (\Exception $e) {

        }

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

    /**
     * @param array $data
     * @param array $metadados
     * @param string $column
     * @return array
     */
    private function checkOwnerList(array $data, array $metadados, string $column)
    {
        foreach ($metadados as $i => $metadado) {
            if ($metadado['relation'] !== "usuarios") {
                if (in_array($metadado['format'], ["extend"])) {
                    $data['owner'][] = ["entity" => $metadado['relation'], "column" => $metadado['column'], "userColumn" => $column];
                } elseif (in_array($metadado['format'], ["list", "list_mult"])) {
                    $data['ownerPublisher'][] = ["entity" => $metadado['relation'], "column" => $metadado['column'], "userColumn" => $column];
                }
            }
        }

        return $data;
    }
}