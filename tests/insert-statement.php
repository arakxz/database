<?php

include_once __DIR__ . '/connection.php';


class InsertCase extends PHPUnit_Framework_TestCase
{

    /**
     * @before
     */
    public function testConnection()
    {
        switch (DATABASE_CONNECTION) {

            case Connection::DATABASE_MYSQL:
                $this->assertInstanceOf(
                    'Arakxz\Database\Connectors\MySQLConnector', Connection::Instance()
                );
                break;

            case Connection::DATABASE_POSTGRESQL:
                $this->assertInstanceOf(
                    'Arakxz\Database\Connectors\PostgreSQLConnector', Connection::Instance()
                );
                break;

        }
    }

    public function testInsert()
    {

        $r = Connection::Instance()
                        ->table('usuario')
                        ->columns([
                            'id' => rand(1, 10000000),
                            'edad' => rand(1, 90),
                            'nombre' => uniqid('test-'),
                            'apellido' => uniqid('test-'),
                        ])
                        ->insert();

        $this->assertTrue($r);

        $r = Connection::Instance()
                        ->table('usuario')
                        ->column('id', rand(1, 10000000))
                        ->column('edad', rand(1, 90))
                        ->column('nombre', uniqid('test-'))
                        ->column('apellido', uniqid('test-'))
                        ->insert();

        $this->assertTrue($r);

    }

    public function testExecute()
    {
        $r = Connection::Instance()->execute(
            'insert into usuario (id, edad, nombre, apellido) values (?, ?, ?, ?)', [
                rand(1, 10000000),
                rand(1, 90),
                uniqid('test-'),
                uniqid('test-')
            ]
        );

        $this->assertTrue($r);
    }

}
