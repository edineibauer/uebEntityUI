<?php
ob_start();

if (0 < $_FILES['file']['error']) {
    echo 'Error: ' . $_FILES['file']['error'] . '<br>';
} else {
    $file = $_FILES['file']['name'];
    $a = explode(".", $file);
    $name = $a[0];
    $extensao = $a[1];

    if (!file_exists(PATH_HOME . "entity/cache/{$name}.json")) {
        move_uploaded_file($_FILES['file']['tmp_name'], PATH_HOME . 'entity/cache/' . $file);

        if ("json" === $extensao) {
            $entity = new \EntityUi\SaveEntity();
            $entity->importMetadados($name);
        } elseif ("sql" === $extensao) {

            unlink(PATH_HOME . "entity/cache/{$file}");
        }
    } else {
        echo "existe";
    }
}

$data['data'] = ob_get_contents();
ob_end_clean();