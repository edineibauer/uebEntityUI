<?php

use Config\Config;

/**
 * @param string $name
 * @param int $i
 * @return string
 */
function nextName(string $name, int $i) {
    $nameTeste = $name . ($i === 0 ? "" : "_" . $i);
    if (file_exists(PATH_HOME . "entity/cache/{$nameTeste}.json"))
        return nextName($name, $i+1);

    return $nameTeste;
}

$data['data'] = 0;

if (0 < $_FILES['arquivo']['error']) {
    $data = ['response' => 2, 'error' => 'Error: ' . $_FILES['arquivo']['error'] . '<br>', 'data' => ''];
} else {
    $file = $_FILES['arquivo']['name'];
    $name = trim(str_replace(['(', ')', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0'], '', pathinfo($file)['filename']));

    if ("json" === pathinfo($file)['extension']) {

        $tipoUser = 0;
        $tipoAutor = null;
        $tipoIcon = "";

        /**
         * Verifica se a entidade já existe, se sim, então considera uma cópia
         */
        if(file_exists(PATH_HOME . 'entity/cache/' . $name . ".json")) {
            $metadadosInfo = \Entity\Metadados::getInfo($name);
            $tipoUser = (int) $metadadosInfo['user'];
            $tipoAutor = $metadadosInfo['autor'];
            $tipoIcon = $metadadosInfo['icon'] ?? "";
            $name = nextName($name, 1);
        }

        /**
         * Salva JSON da entidade importada
         */
        move_uploaded_file( $_FILES['arquivo']['tmp_name'], PATH_HOME . 'entity/cache/' . $name . ".json");

        /**
         * Importa entidade para o Sistema (banco)
         */
        $entity = new \EntityUi\SaveEntity();
        $entity->importMetadados($name);

        /**
         * dê permissão de acesso ao menu para o ADM
         */
        $p = json_decode(file_get_contents(PATH_HOME . "_config/permissoes.json"), !0);
        $p['admin'][$name]['menu'] = "true";
        Config::writeFile(PATH_HOME . "_config/permissoes.json", json_encode($p));

        $data['data'] = true;
    } else {
        $data = ['response' => 2, 'error' => 'Error: arquivo não é um JSON', 'data' => ''];
    }
}