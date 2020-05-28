<?php

use Config\Config;

$entity = trim(strip_tags(filter_input(INPUT_POST, 'name', FILTER_DEFAULT)));

$sql = new \Conn\SqlCommand();
$del = new \Conn\Delete();
$read = new \Conn\Read();

$dic = new \Entity\Dicionario($entity);

//Remove dados extendidos multiplos e tablas de relação multiplas
if (!empty($dic->getAssociationMult())) {
    foreach ($dic->getAssociationMult() as $item)
        $sql->exeCommand("DROP TABLE " . PRE . "{$entity}_{$item->getColumn()}");
}

//Remove dados extendidos simples
if (!empty($dic->getExtends())) {
    foreach ($dic->getExtends() as $extend) {
        $read->exeRead($entity);
        if ($read->getResult()) {
            foreach ($read->getResult() as $ddd) {
                if (!empty($ddd[$extend->getColumn()]))
                    $del->exeDelete(PRE . $extend->getRelation(), "WHERE id = :id", "id={$ddd[$extend->getColumn()]}");
            }
        }
    }
}

//change name entity in others relations AND DELETE TABLES AND COLUMNS RELATIONS
foreach (\Helpers\Helper::listFolder(PATH_HOME . "entity/cache") as $f) {
    if ($f !== "info" && preg_match('/\.json$/i', $f)) {
        $infoCC = json_decode(file_get_contents(PATH_HOME . "entity/cache/info/{$f}"), true);
        $cc = json_decode(file_get_contents(PATH_HOME . "entity/cache/{$f}"), true);
        $fEntity = str_replace(".json", "", $f);
        foreach ($cc as $i => $c) {
            if ($c['relation'] === $entity) {

                if (in_array($c['format'], ['list_mult', 'selecao_mult', 'checkbox_mult'])) {
                    //DROP RELATION TABLE
                    $sql->exeCommand("DROP TABLE " . PRE . "{$fEntity}_{$c['column']}");

                    if (($key = array_search($i, (is_array($infoCC[$c['format']]) && !empty($infoCC[$c['format']]) ? $infoCC[$c['format']] : []))) !== false)
                        unset($infoCC[$c['format']][$key]);

                } elseif(in_array($c['format'], ['extend', 'list', 'selecao', 'checkbox_rel', 'selecaoUnique'])) {

                    //DROP FK AND INDEX
                    $constraint = substr("c_{$fEntity}_". substr($c['column'], 0, 5) . "_" . substr($c['relation'], 0, 5), 0, 64);
                    $sql->exeCommand("ALTER TABLE " . PRE . $fEntity . " DROP FOREIGN KEY {$constraint}, DROP INDEX fk_" . $c['column']);

                    //DROP UNIQUE INDEX
                    $sql->exeCommand("SHOW KEYS FROM " . PRE . $fEntity . " WHERE KEY_NAME ='unique_{$i}'");
                    if ($sql->getRowCount() > 0)
                        $sql->exeCommand("ALTER TABLE " . PRE . $fEntity . " DROP INDEX unique_" . $i);

                    //DROP COLUMN
                    $sql->exeCommand("ALTER TABLE " . PRE . $fEntity . " DROP COLUMN " . $c['column']);

                    if (($key = array_search($i, (is_array($infoCC[$c['format']]) && !empty($infoCC[$c['format']]) ? $infoCC[$c['format']] : []))) !== false)
                        unset($infoCC[$c['format']][$key]);
                }

                //Remove from entity file
                unset($cc[$i]);
            }
        }
        $file = fopen(PATH_HOME . "entity/cache/{$f}", "w");
        fwrite($file, json_encode($cc));
        fclose($file);

        $file = fopen(PATH_HOME . "entity/cache/info/{$f}", "w");
        fwrite($file, json_encode($infoCC));
        fclose($file);
    }
}

if (file_exists(PATH_HOME . "entity" . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR . $entity . ".json"))
    unlink(PATH_HOME . "entity" . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR . $entity . ".json");

if (file_exists(PATH_HOME . "entity" . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR . "info" . DIRECTORY_SEPARATOR . $entity . ".json"))
    unlink(PATH_HOME . "entity" . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR . "info" . DIRECTORY_SEPARATOR . $entity . ".json");

$sql->exeCommand("DROP TABLE " . PRE . $entity);

/**
 * Informa ao sistema que houve atualização
 */
Config::updateSite();

$data['data'] = true;