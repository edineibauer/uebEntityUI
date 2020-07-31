<?php

$entity = strip_tags(trim(filter_input(INPUT_POST, 'entity', FILTER_DEFAULT)));

$entity = explode('\\', $entity);
$entity = str_replace('.json', '', $entity[count($entity) - 1]);

if (file_exists(PATH_HOME . "entity/cache/{$entity}.json"))
    unlink(PATH_HOME . "entity/cache/{$entity}.json");

if (file_exists(PATH_HOME . "entity/cache/info/{$entity}.json"))
    unlink(PATH_HOME . "entity/cache/info/{$entity}.json");

$sql = new \Conn\SqlCommand();
$sql->exeCommand("DROP TABLE IF EXISTS " . PRE . $entity);