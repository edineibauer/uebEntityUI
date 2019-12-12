<?php

use Config\Config;

$data['data'] = 0;

if (0 < $_FILES['arquivo']['error']) {
    $data = ['response' => 2, 'error' => 'Error: ' . $_FILES['arquivo']['error'] . '<br>', 'data' => ''];
} else {
    $file = $_FILES['arquivo']['name'];
    $name = pathinfo($file)['filename'];
    $tipoUser = 0;
    $tipoAutor = null;
    $tipoIcon = "";

    if ("json" === pathinfo($file)['extension']) {

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

        $metadados = json_decode(file_get_contents(PATH_HOME . 'entity/cache/' . $file), !0);
        $nameOld = $name;

        $name = nextName($name, 0);
        $file = $name . ".json";

        if($name !== $nameOld) {
            $metadadosInfo = \Entity\Metadados::getInfo($nameOld);
            $tipoUser = (int) $metadadosInfo['user'];
            $tipoAutor = $metadadosInfo['autor'];
            $tipoIcon = $metadadosInfo['icon'] ?? "";
        }

        $entity = new \EntityUi\SaveEntity($name, $tipoIcon, $tipoUser, $tipoAutor, $metadados);

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