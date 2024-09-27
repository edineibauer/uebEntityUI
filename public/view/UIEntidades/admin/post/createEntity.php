<?php

DEV || die;

$entity = filter_input(INPUT_POST, 'entity', FILTER_DEFAULT);
$edit = filter_input(INPUT_POST, 'edit', FILTER_DEFAULT);

$updateEntity = new \EntityUi\SaveEntityForm($entity, $edit);
$updateEntity->setMod($_POST['mod'] ?? null);
$updateEntity->setDel($_POST['del'] ?? null);
$updateEntity->setAdd($_POST['add'] ?? null);
$updateEntity->setData($_POST['dados']);