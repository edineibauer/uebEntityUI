<?php
$data['data'] = [];
foreach (\Helpers\Helper::listFolder("entity/cache") as $json) {
    if($json !== "info" && preg_match('/\.json$/i', $json)) {
        $name = str_replace('.json', '', $json);
        $dados = \Entity\Metadados::getDicionario($name, null, true);
        if($dados && count($dados) > 0) {
            $e = 1;
            foreach ($dados as $i => $dado) {
                if(empty($dado['indice'])) {
                    $dados[$i]['indice'] = $e;
                    $e++;
                }
            }

            $data['data'][$name] = $dados;
        }
    }
}