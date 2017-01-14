<?php

include_once __DIR__ . '/connection.php';

# $r = $mysql->table('table')->where('column1', '=', value1)->select();
# $r = $mysql->table('table')->where('column1', '=', value1)->select(['column1']);
# $r = $mysql->table('table')->order('column1', 'desc')->limit(2)->select(['column1 as column1-alias']);
# $r = $mysql->execute('select * from table');

class SelectCase extends PHPUnit_Framework_TestCase
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

    public function testCollection()
    {
        $r = Connection::Instance()
                    ->table('usuario')
                    ->select();

        $this->assertTrue($r instanceof \Arakxz\Database\Collection);
    }

    public function testLimit()
    {
        $r = Connection::Instance()
                    ->table('usuario')
                    ->limit(2)
                    ->select();

        $this->assertTrue($r->length() === 2);
    }

    public function testWhere()
    {
        $all = Connection::Instance()
                    ->table('usuario')
                    ->select();

        $f = $all->first();

        $r = Connection::Instance()
                    ->table('usuario')
                    ->where('id', '=', $f['id'])->select();

        $first = $r->first();

        $this->assertTrue($first['id'] == $f['id']);

        $in = [];
        for ($i = 0; $i < $all->length(); $i++) {
            if ($i < 2) {
                $in[] = $all->item($i)['id'];
            } else { break; }
        }

        $r = Connection::Instance()
                    ->table('usuario')
                    ->where('id', 'in', $in)->select();

        $this->assertFalse($r->isEmpty());
    }

    public function testAlias()
    {
        $r = Connection::Instance()
                    ->table('usuario')
                    ->limit(1)
                    ->select(['id as identificador']);

        $first = $r->first();

        $this->assertTrue(isset($first['identificador']));
    }

    public function testExecute()
    {
        $r = Connection::Instance()
                    ->execute('select id as identificador from usuario');

        $this->assertFalse($r->isEmpty());
    }

    /**
     * @expectedException     Arakxz\Database\Exception\BuilderException
     * @expectedExceptionCode 0
     */
    public function testExceptionHasErrorCode0()
    {
        Connection::Instance()->select();
    }

}
