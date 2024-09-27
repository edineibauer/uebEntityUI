<?php

use Config\Config;

DEV || die;

$entity = trim(strip_tags(filter_input(INPUT_POST, 'name', FILTER_DEFAULT)));

$sql = new \Conn\SqlCommand();
$del = new \Conn\Delete();
$read = new \Conn\Read();

$dic = new \Entity\Dicionario($entity);

//Remove dados extendidos multiplos e tablas de relação multiplas
if (!empty($dic->getAssociationMult())) {
    foreach ($dic->getAssociationMult() as $item)
        $sql->exeCommand("DROP TABLE {$entity}_{$item->getColumn()}");
}

//Remove dados extendidos simples
if (!empty($dic->getExtends())) {
    foreach ($dic->getExtends() as $extend) {
        $read->exeRead($entity);
        if ($read->getResult()) {
            foreach ($read->getResult() as $ddd) {
                if (!empty($ddd[$extend->getColumn()]))
                    $del->exeDelete($extend->getRelation(), "WHERE id = :id", "id={$ddd[$extend->getColumn()]}");
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
                    $sql->exeCommand("DROP TABLE {$fEntity}_{$c['column']}");

                    if (($key = array_search($i, (is_array($infoCC[$c['format']]) && !empty($infoCC[$c['format']]) ? $infoCC[$c['format']] : []))) !== false)
                        unset($infoCC[$c['format']][$key]);

                } elseif(in_array($c['format'], ['extend', 'list', 'selecao', 'checkbox_rel', 'selecaoUnique'])) {

                    //DROP FK AND INDEX
                    $constraint = substr("c_{$fEntity}_". substr($c['column'], 0, 5) . "_" . substr($c['relation'], 0, 5), 0, 64);
                    $sql->exeCommand("ALTER TABLE " . $fEntity . " DROP FOREIGN KEY {$constraint}, DROP INDEX fk_" . $c['column']);

                    //DROP UNIQUE INDEX
                    $sql->exeCommand("SHOW KEYS FROM " . $fEntity . " WHERE KEY_NAME ='unique_{$i}'");
                    if ($sql->getRowCount() > 0)
                        $sql->exeCommand("ALTER TABLE " . $fEntity . " DROP INDEX unique_" . $i);

                    //DROP COLUMN
                    $sql->exeCommand("ALTER TABLE " . $fEntity . " DROP COLUMN " . $c['column']);

                    if (($key = array_search($i, (is_array($infoCC[$c['format']]) && !empty($infoCC[$c['format']]) ? $infoCC[$c['format']] : []))) !== false)
                        unset($infoCC[$c['format']][$key]);

                    //remove column readable
                    $key = array_search($c['column'], $infoCC['columns_readable']);
                    if ($key !== false)
                        unset($infoCC['columns_readable'][$key]);

                    //remove informação de relação
                    $key = array_search($i, $infoCC['relation']);
                    if ($key !== false)
                        unset($infoCC['relation'][$key]);

                    $idr = 0;
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

if (DEV && file_exists(PATH_HOME . "public/entity" . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR . $entity . ".json"))
    unlink(PATH_HOME . "public/entity" . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR . $entity . ".json");

if (DEV && file_exists(PATH_HOME . "public/entity" . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR . "info" . DIRECTORY_SEPARATOR . $entity . ".json"))
    unlink(PATH_HOME . "public/entity" . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR . "info" . DIRECTORY_SEPARATOR . $entity . ".json");

$sql->exeCommand("DROP TABLE " . $entity);

/**
 * Remove permissões para a entidade excluída
 */
$p = json_decode(file_get_contents(PATH_HOME . "_config/permissoes.json"), !0);
if(isset($p[$entity]))
    unset($p[$entity]);

foreach ($p as $user => $entidades) {
    if(isset($p[$user][$entity]))
        unset($p[$user][$entity]);
}
Config::createFile(PATH_HOME . "_config/permissoes.json", json_encode($p));

/**
 * Remove entidade do general
 */
$p = json_decode(file_get_contents(PATH_HOME . "entity/general/general_info.json"), !0);
if(isset($p[$entity]))
    unset($p[$entity]);

foreach ($p as $entidade => $dados) {
    if(isset($dados['belongsTo'][$entity]))
        unset($dados['belongsTo'][$entity]);
}
Config::createFile(PATH_HOME . "entity/general/general_info.json", json_encode($p));

$data['data'] = true;