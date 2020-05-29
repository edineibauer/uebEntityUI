<?php

namespace EntityUi;

class InputType
{
    /**
     * Obtém os tipos de metadados de inputs
     * @return array
     */
    public static function getInputTypes(): array
    {
        $inputs = \Config\Config::getRoutesTo("input_type");
        $default = json_decode(file_get_contents(PATH_HOME . VENDOR . "entity-ui/public/input_type/default.json"), !0)['default'];
        $result = [];

        foreach ($inputs as $input) {
            if(file_exists($input)) {
                foreach (\Helpers\Helper::listFolder($input) as $item) {
                    if(preg_match("/\.json$/i", $item) && $item !== "default.json"){
                        $inputType = json_decode(file_get_contents($input . "/" . $item), !0);
                        $result = \Helpers\Helper::arrayMerge($result, $inputType);
                    }
                }
            }
        }

        /**
         * Set Default values on types
         */
        foreach ($result as $i => $datum)
            $result[$i] = \Helpers\Helper::arrayMerge($default, $datum);

        return $result;
    }

    /**
     * Obtém metadados padrão para qualquer input
     * @return array
     */
    public static function getInputDefault(): array
    {
        return json_decode(file_get_contents(PATH_HOME . VENDOR . "entity-ui/public/input_type/default.json"), !0)['default'];
    }

    /**
     * Obtém lista dos tipos de input mais relevantes em ordem
     * @return string[]
     */
    public static function getInputRelevant(): array
    {
        return [
            "title",
            "source",
            "email",
            "cep",
            "tel",
            "cpf",
            "cnpj",
            "valor",
            "card_number",
            "percent",
            "text",
            "number",
            "select",
            "radio",
            "checkbox",
            "folder",
            "extend_folder",
            "list",
            "list_mult",
            "textarea",
            "url",
            "ie",
            "rg",
            "time",
            "week",
            "month",
            "year",
            "date",
            "datetime",
            "none"
        ];
    }
}