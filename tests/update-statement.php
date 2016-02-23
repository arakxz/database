<?php

include 'connection.php';

try {

    $mysql->table('empresas');

    $r = $mysql->column('name', 'auxiliar')->where('ncd', 'between', [5, 6])->update();

    $mysql->table('empresas');

    $r = $mysql->columns(['nid' => '1', 'ncd' => '1', 'name' => 'uno',])->where('id', '=', 1)->update();

} catch (Exception $error) { die($error->getMessage()); }
