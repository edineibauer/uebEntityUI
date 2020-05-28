<?php

$inputs = \Config\Config::getRoutesTo("input_type");
$data['data'] = [];
$default = json_decode(file_get_contents(PATH_HOME . VENDOR . "entity-ui/public/input_type/default.json"), !0)['default'];

foreach ($inputs as $input) {
    if(file_exists($input)) {
        foreach (\Helpers\Helper::listFolder($input) as $item) {
            if(preg_match("/\.json$/i", $item) && $item !== "default.json"){
                $inputType = json_decode(file_get_contents($input . "/" . $item), !0);
                $data['data'] = \Helpers\Helper::arrayMerge($data['data'], $inputType);
            }
        }
    }
}

/**
 * Set Default values on types
 */
foreach ($data['data'] as $i => $datum)
    $data['data'][$i] = \Helpers\Helper::arrayMerge($default, $datum);