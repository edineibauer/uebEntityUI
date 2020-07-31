<?php

use Config\Config;

/**
 * Get name from the entity and if allready exist,
 * get a unique name, try 4 times to get a unique name
 * @param string $name
 * @param int $i
 * @return string
 */
function uniqueEntityName(string $name, int $i) {
    $nameTeste = $name . ($i === 0 ? "" : "_" . $i);
    if (file_exists(PATH_HOME . "entity/cache/{$nameTeste}.json"))
        return uniqueEntityName($name, $i+1);

    return $nameTeste;
}

$data['data'] = 0;

if (0 < $_FILES['arquivo']['error']) {
    $data = ['response' => 2, 'error' => 'Error: ' . $_FILES['arquivo']['error'] . '<br>', 'data' => ''];
} else {
    $file = $_FILES['arquivo']['name'];

    if ("json" === pathinfo($file)['extension']) {
        $content = json_decode(file_get_contents($_FILES["arquivo"]["tmp_name"]), !0);
        if(!empty($content) && !empty($content['entity']) && !empty($content['cache']) && !empty($content['info'])) {

            $metadados = $content['cache'];
            $info = $content['info'];
            $entity = uniqueEntityName($content['entity'], 0);

            /**
             * Save the entity imported
             */
            new \EntityUi\SaveEntity($entity, $info['system'] ?? "", $info['icon'] ?? "", !empty($info['user']), !empty($info['autor']), $metadados, $info['identifier'] ?? 100);

            /**
             * Give permission to Admin to see this entity on panel menu
             */
            $p = json_decode(file_get_contents(PATH_HOME . "_config/permissoes.json"), !0);
            $p['admin'][$entity]['menu'] = "true";
            Config::writeFile(PATH_HOME . "_config/permissoes.json", json_encode($p));

            $data['data'] = true;

        } else {
            $data['error'] = "formato do arquivo inválido";
        }

    } else {
        $data = ['response' => 2, 'error' => 'Error: arquivo não é um JSON', 'data' => ''];
    }
}