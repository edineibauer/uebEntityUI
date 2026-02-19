<?php

DEV || die;

$data['data'] = [];
$sql = new \Conn\SqlCommand();

foreach (\Helpers\Helper::listFolder(PATH_HOME . "entity/cache/info") as $json) {
    if (!preg_match('/\.json$/i', $json)) continue;
    $name = str_replace('.json', '', $json);
    $entityInfo = \Entity\Metadados::getInfo($name);

    // Template (tipo 4) - sem tabela
    if (!empty($entityInfo) && isset($entityInfo['user']) && $entityInfo['user'] === 4) {
        $data['data'][$name] = ['count' => null, 'lastDate' => null];
        continue;
    }

    // Verificar se tabela existe
    $sql->exeCommand("SHOW TABLES LIKE '{$name}'");
    if (!$sql->getResult()) {
        $data['data'][$name] = ['count' => 0, 'lastDate' => null];
        continue;
    }

    // COUNT + MAX(id)
    $sql->exeCommand("SELECT COUNT(*) as cnt, MAX(id) as max_id FROM `{$name}`");
    $r = $sql->getResult();
    $count = $r ? (int)$r[0]['cnt'] : 0;
    $maxId = $r ? $r[0]['max_id'] : null;

    // Buscar data do ultimo registro
    $lastDate = null;
    if ($maxId && !empty($entityInfo['date'])) {
        $dic = \Entity\Metadados::getDicionario($name);
        $idx = $entityInfo['date'];
        if (!empty($dic[$idx]['column'])) {
            $col = $dic[$idx]['column'];
            $sql->exeCommand("SELECT `{$col}` as dt FROM `{$name}` WHERE id = {$maxId}");
            $dr = $sql->getResult();
            if ($dr && !empty($dr[0]['dt'])) $lastDate = $dr[0]['dt'];
        }
    }

    $data['data'][$name] = ['count' => $count, 'lastDate' => $lastDate];
}
