<?php
$path = file_exists(PATH_HOME . "entity/input_type.json") ? PATH_HOME . "entity/input_type.json" : PATH_HOME . VENDOR . "entity-form/public/entity/input_type.json";
$data['data'] = json_decode(file_get_contents($path), true);
