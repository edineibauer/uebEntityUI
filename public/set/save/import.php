<?php

use Config\Config;

$data['data'] = 0;

if (0 < $_FILES['arquivo']['error']) {
    $data = ['response' => 2, 'error' => 'Error: ' . $_FILES['arquivo']['error'] . '<br>', 'data' => ''];
} else {
    $file = $_FILES['arquivo']['name'];
    $name = pathinfo($file)['filename'];
    $extensao = pathinfo($file)['extension'];
    if ("json" === $extensao) {
        if (file_exists(PATH_HOME . "entity/cache/{$name}.json")) {
            $name .= '-' . strtotime('now');
            $file = $name . ".json";
        }

        move_uploaded_file($_FILES['arquivo']['tmp_name'], PATH_HOME . 'entity/cache/' . $file);

        $entity = new \EntityUi\SaveEntity($name, null, 0);
        $entity->importMetadados($name);


        /**
         * Se for uma nova entidade, dê permissão de menu ao ADM
         */
        $p = json_decode(file_get_contents(PATH_HOME . "_config/permissoes.json"), !0);
        $p['admin'][$name]['menu'] = "true";
        Config::writeFile(PATH_HOME . "_config/permissoes.json", json_encode($p));

        /**
         * Informa ao sistema que houve atualização
         */
        Config::updateSite();

        $data['data'] = true;
    }
}