<?php
namespace Arakxz\Database\Connectors;

use \PDO;
use \PDOException;
use \Arakxz\Database\Query;
use \Arakxz\Database\Collection;

final class MySQLConnector extends PDO
{

    private $dsn = 'mysql:host=%s;port=%s;dbname=%s';
    private $builder;

    public function __construct($database, $user, $password, $host = 'localhost', $port = 3306)
    {
        try {

            # The Data Source Name, or DSN, contains the information required to
            # connect to the database.
            $dsn = sprintf($this->dsn, $host, $port, $database);

            parent::__construct($dsn, $user, $password);

            $this->builder = new Query\Builder(new Query\Grammars\MySQLGrammar());

        } catch (PDOException $exception) {
            # error code...
            throw new \Exception($exception->getMessage());
        }
    }

    public function __destruct()
    {
        # destruct
    }

    /**
     * @param  string $table
     *
     * @return $this
     */
    public function table($table)
    {
        $this->builder->table($table);

        return $this;
    }

    /**
     * @param  integer $limit
     *
     * @return $this
     */
    public function limit($limit)
    {
        $this->builder->limit($limit);

        return $this;
    }

    /**
     * @param  string $column
     * @param  string $operator
     * @param  mixed  $value
     *
     * @return $this
     */
    public function where($column, $operator, $value)
    {
        $this->builder->where($column, $operator, $value);

        return $this;
    }

    /**
     * @param  string $column
     * @param  string $sorted
     *
     * @return $this
     */
    public function order($column, $sorted = 'ASC')
    {
        $this->builder->order($column, $sorted);

        return $this;
    }

    /**
     * @param  string $name
     * @param  mixed  $value
     *
     * @return $this
     */
    public function column($name, $value)
    {
        $this->builder->column($name, $value);

        return $this;
    }

    /**
     * @param  array $columns
     *
     * @return $this
     */
    public function columns(array $columns)
    {
        $this->builder->columns($columns);

        return $this;
    }

    public function distinct()
    {
        $this->builder->distinct();

        return $this;
    }

    /**
     * @param  array $columns
     *
     * @return \Arakxz\Database\Collection
     */
    public function select(array $columns = array())
    {

        if (!empty($columns)) { $this->columns($columns); }

        $select = $this->prepare($this->builder->select());
        $collection = new Collection();

        foreach ($this->builder->bindings() as $key => $value) {
            $select->bindValue(':' . $key, $value, $this->bindValue($value));
        }

        $this->builder->builderFlush();

        if (!$select->execute()) {

            $e = $select->errorInfo();
            throw new \Exception($e[2], $e[1]);

        }

        while ($row = $select->fetch(\PDO::FETCH_ASSOC)) {
            $collection->push($row);
        }

        return $collection;

    }

    /**
     * @return integer
     */
    public function insert()
    {
        $insert = $this->prepare($this->builder->insert());

        foreach ($this->builder->bindings() as $key => $value) {
            $insert->bindValue(':' . $key, $value, $this->bindValue($value));
        }

        $this->builder->builderFlush();

        return !$insert->execute() ? 0 : $this->lastInsertId();
    }

    /**
     * @return integer
     */
    public function update()
    {
        $update = $this->prepare($this->builder->update());

        foreach ($this->builder->bindings() as $key => $value) {
            $update->bindValue(':' . $key, $value, $this->bindValue($value));
        }

        $this->builder->builderFlush();

        return $update->execute();
    }

    /**
     * @return integer
     */
    public function delete()
    {
        $delete = $this->prepare($this->builder->delete());

        foreach ($this->builder->bindings() as $key => $value) {
            $delete->bindValue(':' . $key, $value, $this->bindValue($value));
        }

        $this->builder->builderFlush();

        return $delete->execute();
    }

    /**
     * @throws \Exception
     *
     * @param  string $query
     * @param  array  $params
     *
     * @return mixed
     */
    public function execute($query, array $params = array())
    {

        $statement = $this->prepare($query);

        for ($i = 0; $i < count($params); $i++) {
            $statement->bindValue(($i + 1), $params[$i], $this->bindValue($params[$i]));
        }

        list ($command) = explode("\x20", trim($query), 2);

        switch (strtolower($command)) {

            case 'update':
            case 'delete':
                return $statement->execute();

            case 'insert':
                return !$statement->execute() ? 0 : $this->lastInsertId();

            case 'select':

                $r = $statement->execute();
                $collection = new Collection();

                while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
                    $collection->push($row);
                }

                return $collection;

            default:
                throw new \Exception('command not available.');

        }

    }

    /**
     * @param  string $value
     *
     * @return integer
     */
    protected function bindValue($value)
    {
        # boolean
        if (is_bool($value)) {
            return self::PARAM_BOOL;
        }
        # null
        if (is_null($value)) {
            return self::PARAM_NULL;
        }
        # string
        if (is_string($value)) {
            return self::PARAM_STR;
        }
        # integer
        if (is_integer($value)) {
            return self::PARAM_INT;
        }
    }

}
