<?php

include_once __DIR__ . '/connection.php';

# $r = $mysql->table('table')->where('column1', 'between', [value1, value2])->delete();
# $r = $mysql->table('table')->where('column1', '=', value1)->delete();

class DeleteCase extends PHPUnit_Framework_TestCase
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

    public function testDelete()
    {
        $r = $this->connection->table('referencia_empresa')->where('id', '=', 2)->delete();

        $this->assertTrue($r);
    }

    public function testExecute()
    {
        $r = (bool) $this->connection->execute(
            'delete from referencia_empresa where `id`=?', [3]
        );

        $this->assertTrue($r);
    }

}
