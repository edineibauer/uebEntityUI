<?php

DEV || die;

$entityName = trim(strip_tags(filter_input(INPUT_POST, 'entity', FILTER_DEFAULT)));
$newType = (int)filter_input(INPUT_POST, 'newType', FILTER_VALIDATE_INT);
$newSystem = trim(strip_tags(filter_input(INPUT_POST, 'newSystem', FILTER_DEFAULT)));

if (empty($entityName)) {
    $data['response'] = 2;
    $data['error'] = "Entidade nao informada";
    return;
}

$entityInfo = \Entity\Metadados::getInfo($entityName);
if (empty($entityInfo)) {
    $data['response'] = 2;
    $data['error'] = "Entidade nao encontrada";
    return;
}

$oldType = isset($entityInfo['user']) ? (int)$entityInfo['user'] : 0;
$sql = new \Conn\SqlCommand();

// Mudanca de tipo
if ($oldType !== $newType) {

    // Qualquer -> Template (4): verificar se tabela tem dados
    if ($newType === 4 && $oldType !== 4) {
        $sql->exeCommand("SHOW TABLES LIKE '{$entityName}'");
        if ($sql->getResult()) {
            $sql->exeCommand("SELECT COUNT(*) as cnt FROM `{$entityName}`");
            $r = $sql->getResult();
            if ($r && (int)$r[0]['cnt'] > 0) {
                $data['response'] = 2;
                $data['error'] = "A entidade possui " . $r[0]['cnt'] . " registro(s). Remova os dados antes de converter para Template.";
                return;
            }
            // Tabela vazia, pode dropar
            $sql->exeCommand("DROP TABLE IF EXISTS `{$entityName}`");
            $sql->exeCommand("DROP TABLE IF EXISTS `wcache_{$entityName}`");
        }
    }

    // Template (4) -> Qualquer: criar tabela
    if ($oldType === 4 && $newType !== 4) {
        new \EntityUi\EntityCreateEntityDatabase($entityName);
    }

    $entityInfo['user'] = $newType;
}

// Mudanca de sistema
if ($newSystem !== null) {
    $entityInfo['system'] = $newSystem;
}

// Gravar info atualizado
$fp = fopen(PATH_HOME . "entity/cache/info/{$entityName}.json", "w");
fwrite($fp, json_encode($entityInfo));
fclose($fp);

$data['data'] = ['success' => true];
