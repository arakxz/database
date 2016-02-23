<?php

include '../source/Collection.php';
include '../source/Query/Builder.php';
include '../source/Query/Grammars/Grammar.php';
include '../source/Connectors/MySQLConnector.php';
include '../source/Exception/BuilderException.php';
include '../source/Query/Grammars/MySQLGrammar.php';

use Arakxz\Database\Connectors\MySQLConnector as MySQL;

try {

    $mysql = new MySQL('database', 'username', 'password');

} catch (Exception $error) { die($error->getMessage()); }
