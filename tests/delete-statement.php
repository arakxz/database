<?php

include 'connection.php';

try {

    $r = $mysql->table('empresas')->where('ncd', 'between', [5, 6])->delete();

    $r = $mysql->table('empresas')->where('id', '=', 8)->delete();

} catch (Exception $error) { die($error->getMessage()); }
