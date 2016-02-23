<?php

include 'connection.php';

try {

    $mysql->table('empresas');

    $r = $mysql->columns(['nid' => '112266', 'ncd' => '6', 'name' => 'example',])->insert();

    $r = $mysql->table('empresas')
               ->column('nid', '112277')
               ->column('name', 'example')->insert();

} catch (Exception $error) { die($error->getMessage()); }
