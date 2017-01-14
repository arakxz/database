<?php

include_once dirname(__DIR__) . '/source/Collection.php';
include_once dirname(__DIR__) . '/source/Query/Builder.php';
include_once dirname(__DIR__) . '/source/Query/Grammars/Grammar.php';
include_once dirname(__DIR__) . '/source/Connectors/MySQLConnector.php';
include_once dirname(__DIR__) . '/source/Connectors/PostgreSQLConnector.php';
include_once dirname(__DIR__) . '/source/Exception/BuilderException.php';
include_once dirname(__DIR__) . '/source/Query/Grammars/MySQLGrammar.php';
include_once dirname(__DIR__) . '/source/Query/Grammars/PostgreSQLGrammar.php';

use \Arakxz\Database\Connectors\MySQLConnector as MySQL;
use \Arakxz\Database\Connectors\PostgreSQLConnector as PostgreSQL;

class Connection
{

    const DATABASE_MYSQL = 'MYSQL';
    const DATABASE_POSTGRESQL = 'POSTGRESQL';

    private static $instance = null;

    public static function Instance()
    {
        if (is_null(self::$instance)) {
            self::bootstrap();
        }

        return self::$instance;
    }

    public static function up()
    {
        self::Instance()->prepare("CREATE TABLE IF NOT EXISTS usuario(
          id        INT PRIMARY KEY NOT NULL,
          edad      INT             NOT NULL,
          nombre    TEXT            NOT NULL,
          apellido  TEXT            NOT NULL,
          insertado TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP
        )")->execute();

        register_shutdown_function('Connection::down');
    }

    public static function down()
    {
        self::Instance()->prepare("DROP TABLE usuario")->execute();
    }

    private static function bootstrap()
    {
        try {

            switch (DATABASE_CONNECTION) {

                case self::DATABASE_MYSQL:
                    self::$instance = new MySQL(
                        $_ENV['DATABASE_NAME'], $_ENV['DATABASE_USERNAME'],
                                                $_ENV['DATABASE_PASSWORD']
                    );
                    break;

                case self::DATABASE_POSTGRESQL:
                    self::$instance = new PostgreSQL(
                        $_ENV['DATABASE_NAME'], $_ENV['DATABASE_USERNAME'],
                                                $_ENV['DATABASE_PASSWORD']
                    );
                    break;

                default:
                    throw new Exception("Error Processing Request", 1);
                    break;

            }

            self::up();

        } catch (Exception $error) { die($error->getMessage()); }
    }

}
