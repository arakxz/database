<?php

include 'connection.php';

try {

    $r = $mysql->table('empresas')->where('id', '=', 2)->select();

    $r = $mysql->table('empresas')->where('id', '=', 2)->select(['ID']);

    $r = $mysql->table('empresas')->order('id', 'desc')->limit(2)->select(['id as identificador']);

    $r = $mysql->execute('select * from empresas');

    $r = $mysql->execute('select ? from empresas', ['id']);

    $r = $mysql->execute('select ? as identificador from empresas', ['id']);

} catch (Exception $error) { die($error->getMessage()); }
