<?php

include '../source/Collection.php';
include '../source/Query/Builder.php';
include '../source/Query/Grammars/Grammar.php';
include '../source/Connectors/MySQLConnector.php';
include '../source/Exception/BuilderException.php';
include '../source/Query/Grammars/MySQLGrammar.php';

use \Arakxz\Database\Connectors\MySQLConnector as MySQL;

class Connection
{

    private $connection;

    public function __construct()
    {
        try {

            $this->connection = new MySQL('blinking', 'root', 'ddossow123');

        } catch (Exception $error) { die($error->getMessage()); }
    }

    public function getConnection()
    {
        return $this->connection;
    }

}
