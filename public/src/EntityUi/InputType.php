<?php

namespace EntityUi;

class InputType
{
    public static function getInputTypes()
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
}