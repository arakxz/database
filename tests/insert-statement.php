<?php

include_once __DIR__ . '/connection.php';

# $mysql->table('table');
# $r = $mysql->columns(['column1' => 'value1', 'column2' => 'value2', 'column3' => 'value3',])->insert();
# $r = $mysql->table('table')->column('column1', 'value1')->column('column2', 'value2')->insert();

class InsertCase extends PHPUnit_Framework_TestCase
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

    public function testInsert()
    {

        $this->connection->table('referencia_empresa');
        $r = (bool) $this->connection
                         ->columns([
                             'hrut' => uniqid('test-'),
                             'id_empresa' => '1',
                             'created_at' => date('Y-m-d H:i:s'),
                             'updated_at' => date('Y-m-d H:i:s'),
                         ])
                         ->insert();

        $this->assertTrue($r);

        $r = (bool) $this->connection->table('referencia_empresa')
                                     ->column('hrut', uniqid('test-'))
                                     ->column('id_empresa', '2')
                                     ->column('created_at', date('Y-m-d H:i:s'))
                                     ->column('updated_at', date('Y-m-d H:i:s'))->insert();

        $this->assertTrue($r);

    }

    public function testExecute()
    {
        $r = (bool) $this->connection->execute(
            'insert into ? (hrut, id_empresa, created_at, updated_at) values (?, ?, ?, ?)', [
                'referencia_empresa', uniqid('test-'), '3', date('Y-m-d H:i:s'), date('Y-m-d H:i:s')
            ]
        );

        $this->assertFalse($r);
    }

}
