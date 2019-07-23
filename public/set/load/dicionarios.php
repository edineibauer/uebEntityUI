<?php
$data['data'] = [];
foreach (\Helpers\Helper::listFolder("entity/cache") as $json) {
    if($json !== "info" && !in_array($json, ["api_chave.json", "config.json", "dashboard_note.json", "dashboard_push.json", "email_envio.json", "login_attempt.json", "usuarios.json"]) && preg_match('/\.json$/i', $json)) {
        $name = str_replace('.json', '', $json);
        $dados = \Entity\Metadados::getDicionario($name, null, true);
        if($dados && count($dados) > 0) {
            $e = 1;
            foreach ($dados as $i => $dado) {
                if($dado['column'] === "autorpub" || $dado['column'] === "ownerpub") {
                    unset($dados[$i]);
                } elseif(empty($dado['indice'])) {
                    $dados[$i]['indice'] = $e;
                    $e++;
                }
            }

            $data['data'][$name] = $dados;
        }
    }
}