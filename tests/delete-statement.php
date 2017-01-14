<?php

include_once __DIR__ . '/connection.php';

# $r = $mysql->table('table')->where('column1', 'between', [value1, value2])->delete();
# $r = $mysql->table('table')->where('column1', '=', value1)->delete();

class DeleteCase extends PHPUnit_Framework_TestCase
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

    public function testDelete()
    {
        $first = Connection::Instance()
                        ->table('usuario')
                        ->limit(2)
                        ->select()
                        ->first();

        $r = Connection::Instance()
                        ->table('usuario')
                        ->where('id', '=', $first['id'])
                        ->delete();

        $this->assertTrue($r);
    }

    public function testExecute()
    {
        $first = Connection::Instance()
                        ->table('usuario')
                        ->limit(2)
                        ->select()
                        ->first();

        $r = (bool) Connection::Instance()->execute(
            'delete from usuario where id=?', [$first['id']]
        );

        $this->assertTrue($r);
    }

}
