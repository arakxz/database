<?php

include_once __DIR__ . '/connection.php';

# $mysql->table('table');
# $r = $mysql->column('column1', 'value1')->where('column1', 'between', [value1, value2])->update();
# $mysql->table('table');
# $r = $mysql->columns(['column1' => 'value1', 'column2' => 'value2', 'column3' => 'value3',])->where('column1', '=', value1)->update();

class UpdateCase extends PHPUnit_Framework_TestCase
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

    public function testUpdate()
    {

        $r = (bool) $this->connection
                         ->table('referencia_empresa')
                         ->columns([
                             'hrut' => uniqid('test-'),
                             'updated_at' => date('Y-m-d H:i:s'),
                         ])
                         ->where('id', '=', 1)->update();

        $this->assertTrue($r);

        $r = (bool) $this->connection
                         ->table('referencia_empresa')
                         ->column('hrut', uniqid('test-'))
                         ->where('id', '=', 2)->update();

        $this->assertTrue($r);

    }

    public function testExecute()
    {
        $r = (bool) $this->connection->execute(
            'update referencia_empresa set hrut=? where `id`=?', [
                '556677889900', 3
            ]
        );

        $this->assertTrue($r);
    }

}
