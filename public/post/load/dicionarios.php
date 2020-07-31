<?php
$data['data'] = [];

$listNot = [
    "api_chave.json",
    "coordenadas.json",
    "enviar_mensagem.json",
    "notifications.json",
    "notifications_report.json",
    "push_notifications.json",
    "email_envio.json",
    "login_attempt.json",
    "usuarios_token.json",
    "relatorios.json",
    "relatorios_card.json",
    "relatorios_regras.json",
    "relatorios_filtro.json",
    "relatorios_grupos.json",
    "cep.json"
];

foreach (\Helpers\Helper::listFolder("entity/cache") as $json) {
    if ($json !== "info" && !in_array($json, $listNot) && preg_match('/\.json$/i', $json)) {
        $name = str_replace('.json', '', $json);
        $dados = \Entity\Metadados::getDicionario($name, null, true);
        if ($dados && count($dados) > 0) {
            $e = 1;
            foreach ($dados as $i => $dado) {
                if ($dado['column'] === "autorpub" || $dado['column'] === "ownerpub") {
                    unset($dados[$i]);
                } elseif (empty($dado['indice'])) {
                    $dados[$i]['indice'] = $e;
                    $e++;
                }
            }

            $data['data'][$name] = $dados;
        }
    }
}