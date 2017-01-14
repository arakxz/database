<?php

include_once __DIR__ . '/connection.php';

# $mysql->table('table');
# $r = $mysql->column('column1', 'value1')->where('column1', 'between', [value1, value2])->update();
# $mysql->table('table');
# $r = $mysql->columns(['column1' => 'value1', 'column2' => 'value2', 'column3' => 'value3',])->where('column1', '=', value1)->update();

class UpdateCase extends PHPUnit_Framework_TestCase
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

    public function testUpdate()
    {

        $first = Connection::Instance()
                        ->table('usuario')
                        ->limit(1)
                        ->select()
                        ->first();

        $n = uniqid('test-');

        $r = Connection::Instance()
                        ->table('usuario')
                        ->columns([
                            'edad' => rand(1, 90),
                            'nombre' => $n,
                        ])
                        ->where('id', '=', $first['id'])
                        ->update();

        $this->assertTrue($r);

        $first = Connection::Instance()
                        ->table('usuario')
                        ->where('id', '=', $first['id'])
                        ->select()
                        ->first();

        $this->assertTrue($first['nombre'] == $n);

        $a = uniqid('test-');

        $r = Connection::Instance()
                        ->table('usuario')
                        ->column('apellido', $a)
                        ->where('id', '=', $first['id'])
                        ->update();

        $this->assertTrue($r);

        $first = Connection::Instance()
                        ->table('usuario')
                        ->where('id', '=', $first['id'])
                        ->select()
                        ->first();

        $this->assertTrue($first['apellido'] == $a);

    }

    public function testExecute()
    {
        $first = Connection::Instance()
                        ->table('usuario')
                        ->limit(1)
                        ->select()
                        ->first();

        $r = Connection::Instance()->execute(
            'update usuario set nombre=? where id=?',
            [uniqid('test-'), $first['id']]
        );

        $this->assertTrue($r);
    }

}
