<?php
$data['data'] = [];
foreach (\Helpers\Helper::listFolder("entity/cache/info") as $json) {
    if(preg_match('/\.json$/i', $json)) {
        $name = str_replace('.json', '', $json);
        $dados = \Entity\Metadados::getInfo($name);
        if($dados && count($dados) > 0)
            $data['data'][$name] = $dados;
    }
}