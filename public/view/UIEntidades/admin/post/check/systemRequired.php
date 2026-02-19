<?php

DEV || die;

$entityName = trim(strip_tags(filter_input(INPUT_POST, 'entity', FILTER_DEFAULT)));

if (empty($entityName)) {
    $data['data'] = ['valid' => false, 'message' => 'Entidade não informada'];
    return;
}

$sql = new \Conn\SqlCommand();

// Check if table exists
$sql->exeCommand("SHOW TABLES LIKE '{$entityName}'");
if (empty($sql->getResult())) {
    $data['data'] = ['valid' => true, 'count' => 0];
    return;
}

// Check if system_id column exists
$sql->exeCommand("SHOW COLUMNS FROM `{$entityName}` LIKE 'system_id'");
if (empty($sql->getResult())) {
    $data['data'] = ['valid' => true, 'count' => 0];
    return;
}

// Count records without valid system_id
$sql->exeCommand("SELECT COUNT(*) as cnt FROM `{$entityName}` WHERE system_id IS NULL OR system_id = 0 OR system_id = ''");
$result = $sql->getResult();
$count = $result ? (int)$result[0]['cnt'] : 0;

if ($count > 0) {
    $data['data'] = [
        'valid' => false,
        'count' => $count,
        'message' => "{$count} registro(s) sem sistema definido. Defina o sistema em todos os registros antes de tornar obrigatório."
    ];
} else {
    $data['data'] = ['valid' => true, 'count' => 0];
}
