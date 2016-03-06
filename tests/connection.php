<?php

include_once dirname(__DIR__) . '/source/Collection.php';
include_once dirname(__DIR__) . '/source/Query/Builder.php';
include_once dirname(__DIR__) . '/source/Query/Grammars/Grammar.php';
include_once dirname(__DIR__) . '/source/Connectors/MySQLConnector.php';
include_once dirname(__DIR__) . '/source/Exception/BuilderException.php';
include_once dirname(__DIR__) . '/source/Query/Grammars/MySQLGrammar.php';

use \Arakxz\Database\Connectors\MySQLConnector as MySQL;

class Connection
{

    private $connection;

    public function __construct()
    {
        try {

            $this->connection = new MySQL('database', 'username', 'password');

        } catch (Exception $error) { die($error->getMessage()); }
    }

    public function getConnection()
    {
        return $this->connection;
    }

}
