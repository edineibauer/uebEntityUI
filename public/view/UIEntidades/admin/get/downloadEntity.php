<?php

if (!empty($variaveis[0])) {
    $entity = $variaveis[0];
    if (file_exists(PATH_HOME . "entity/cache/{$entity}.json") && file_exists(PATH_HOME . "entity/cache/info/{$entity}.json"))
        $data['data'] = json_encode(["cache" => json_decode(file_get_contents(PATH_HOME . "entity/cache/{$entity}.json"), !0), "info" => json_decode(file_get_contents(PATH_HOME . "entity/cache/info/{$entity}.json"), !0)]);
}