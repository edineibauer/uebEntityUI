<?php

use Config\Config;

$name = trim(strip_tags(filter_input(INPUT_POST, 'name', FILTER_DEFAULT)));
$icon = trim(strip_tags(filter_input(INPUT_POST, 'icon', FILTER_DEFAULT)));
$autor = trim(strip_tags(filter_input(INPUT_POST, 'autor', FILTER_VALIDATE_BOOLEAN)));
$owner = trim(strip_tags(filter_input(INPUT_POST, 'owner', FILTER_VALIDATE_BOOLEAN)));
$system = trim(strip_tags(filter_input(INPUT_POST, 'system', FILTER_DEFAULT)));
$user = trim(strip_tags(filter_input(INPUT_POST, 'user', FILTER_VALIDATE_INT)));
$newName = str_replace("-", "_", \Helpers\Check::name(trim(strip_tags(filter_input(INPUT_POST, 'newName', FILTER_DEFAULT)))));
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$dados = filter_input(INPUT_POST, 'dados', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$save = new \EntityUi\SaveEntity($name, $system, $icon, (!empty($user) && is_numeric($user) ? $user : 0), ($autor ? 1 : ($owner ? 2 : null)), $dados, $id);

if($name !== $newName) {
    $sql = new \Conn\SqlCommand();

    //Table Rename
    $sql->exeCommand("RENAME TABLE  `" . PRE . "{$name}` TO  `" . PRE . "{$newName}`");

    //Entity Rename
    rename(PATH_HOME . "entity/cache/{$name}.json",PATH_HOME . "entity/cache/{$newName}.json");
    rename(PATH_HOME . "entity/cache/info/{$name}.json",PATH_HOME . "entity/cache/info/{$newName}.json");

    //Table Rename name in Relation
    $dic = new \Entity\Dicionario($newName);
    foreach ($dic->getAssociationMult() as $item)
        $sql->exeCommand("RENAME TABLE  `" . PRE . "{$name}_{$item->getColumn()}` TO  `" . PRE . "{$newName}_{$item->getColumn()}`");

    //Entity change name in others relations
    foreach (\Helpers\Helper::listFolder(PATH_HOME . "entity/cache") as $f) {
        if($f !== "info" && preg_match('/\.json$/i', $f)) {
            $fEntity = str_replace('.json', '', $f);
            $cc = json_decode(file_get_contents(PATH_HOME . "entity/cache/{$f}"), true);
            foreach ($cc as $i => $c) {
                if($c['relation'] === $name) {

                    //Muda associação da entidade pela nova nesta entidade
                    $cc[$i]['relation'] = $newName;

                    //atualiza o nome da coluna na tabela de relação para apontar para o novo nome de entidade
                    $sql->exeCommand("ALTER TABLE  " . PRE . "{$fEntity}_{$c['column']} CHANGE {$name}_id {$newName}_id int(11)");

                }
            }
            $file = fopen(PATH_HOME . "entity/cache/{$f}", "w");
            fwrite($file, json_encode($cc));
            fclose($file);
        }
    }
}


/**
 * Se for uma nova entidade, dê permissão de menu ao ADM
 */
$p = json_decode(file_get_contents(PATH_HOME . "_config/permissoes.json"), !0);
$p['admin'][$newName]['menu'] = "true";
Config::writeFile(PATH_HOME . "_config/permissoes.json", json_encode($p));

$data['data'] = true;