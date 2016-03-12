<?php

include_once __DIR__ . '/connection.php';

# $r = $mysql->table('table')->where('column1', '=', value1)->select();
# $r = $mysql->table('table')->where('column1', '=', value1)->select(['column1']);
# $r = $mysql->table('table')->order('column1', 'desc')->limit(2)->select(['column1 as column1-alias']);
# $r = $mysql->execute('select * from table');
# $r = $mysql->execute('select ? from table', ['column1']);
# $r = $mysql->execute('select ? as column1-alias from table', ['column1']);

class SelectCase extends PHPUnit_Framework_TestCase
{

    private $connection;

    public function setUp()
    {
        $connection = new Connection();

        $this->connection = $connection->getConnection();
    }

    /**
     * @after
     */
    public function testConnection()
    {
        $this->assertInstanceOf('Arakxz\Database\Connectors\MySQLConnector', $this->connection);
    }

    public function testCollection()
    {
        $r = $this->connection->table('empresa')->select();

        $this->assertTrue($r instanceof \Arakxz\Database\Collection);
    }

    public function testLimit()
    {
        $r = $this->connection->table('empresa')->limit(2)->select();

        $this->assertTrue($r->length() === 2);
    }

    public function testWhere()
    {
        $r = $this->connection->table('empresa')->where('id', '=', 1)->select();

        $first = $r->first();

        # or
        # $this->connection->table('empresa')->where('id', '=', 1)->select()->first();

        $this->assertTrue($first['id'] == 1);

        $r = $this->connection->table('empresa')->where('id', 'in', [1, 2, 3])->select();

        $this->assertFalse($r->isEmpty());
    }

    public function testAlias()
    {
        $r = $this->connection->table('empresa')->limit(1)->select(['id as identificador']);

        $first = $r->first();

        $this->assertTrue(isset($first['identificador']));
    }

    public function testExecute()
    {
        $r = $this->connection->execute('select ? as identificador from empresa', ['id']);

        $this->assertFalse($r->isEmpty());
    }

    /**
     * @expectedException     Arakxz\Database\Exception\BuilderException
     * @expectedExceptionCode 0
     */
    public function testExceptionHasErrorCode0()
    {
        $this->connection->select();
    }

}
