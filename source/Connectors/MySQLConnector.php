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
	 * Set the table which the query is targeting.
	 *
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
	 * Set the "limit" value of the query.
	 *
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
	 * Add a basic where clause to the query.
	 *
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
	 * Add an "order by" clause to the query.
	 *
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

    /**
	 * Force the query to only return distinct results.
	 *
	 * @return $this
	 */
    public function distinct()
    {
        $this->builder->distinct();

        return $this;
    }

    /**
	 * Select a record in the database.
	 *
	 * @throws \Exception
	 *
     * @param  array $columns
     *
     * @return \Arakxz\Database\Collection
     */
    public function select(array $columns = array())
    {

        $select = $this->prepare(
            empty($columns)
                ? $this->builder->select()
                : $this->columns($columns)->builder->select()
        );

        foreach ($this->builder->bindings() as $key => $value) {
            $select->bindValue(':' . $key, $value, $this->bindValue($value));
        }

        $this->builder->builderFlush();

        if (!$select->execute()) {
            $ei = $select->errorInfo(); throw new \Exception($ei[2], $ei[1]);
        }

        $collection = new Collection();

        while ($row = $select->fetch(\PDO::FETCH_ASSOC)) {
            $collection->push($row);
        }

        return $collection;

    }

    /**
	 * Insert a record in the database.
	 *
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
	 * Update a record in the database.
	 *
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
     * Delete a record from the database.
     *
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
                throw new \Exception("Command \"$command\" not available.");

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
