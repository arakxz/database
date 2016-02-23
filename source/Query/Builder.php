<?php
namespace Arakxz\Database\Query;

use \Arakxz\Database\Query\Grammars\Grammar;
use \Arakxz\Database\Exception\BuilderException;

class Builder
{

    /**
     * The table which the query is targeting.
     *
     * @var string
     */
    private $table = null;

    /**
     * The maximum number of records to return.
     *
     * @var int
     */
    private $limit = null;

    /**
     * The orderings for the query.
     *
     * @var array
     */
    private $orders = array();

    /**
     * The where constraints for the query.
     *
     * @var array
     */
    private $wheres = array();

    /**
     * The columns that should be returned.
     *
     * @var array
     */
    private $columns = array();

    /**
     * The database query grammar instance.
     *
     * @var \Arakxz\Database\Query\Grammars\Grammar
     */
    private $grammar;

    /**
     * Create a new query builder instance.
     *
     * @param  \Arakxz\Database\Query\Grammars\Grammar $grammar
     *
     * @return void
     */
    public function __construct(Grammar $grammar)
    {
        $this->grammar = $grammar;
    }

    public function __destruct()
    {
        #destruct
    }

    public function table($table)
    {
        $this->table = $table;
    }

    public function limit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * @throws BuilderException
     *
     * @param  string $column
     * @param  string $operator
     * @param  string $value
     *
     * @return void
     */
    public function where($column, $operator, $value)
    {
        $operator = strtoupper($operator);

        if (!$this->grammar->hasOperator($operator)) {
            throw new BuilderException('Error in operators');
        }

        $this->wheres[] = array($column, $operator, $value,);
    }

    /**
     * @param  string $column
     * @param  string $sorted
     *
     * @return void
     */
    public function order($column, $sorted)
    {
        $this->orders[] = array($column, strtoupper($sorted));
    }

    public function column($name, $value)
    {
        $this->columns[$name] = $value;
    }

    public function columns($columns)
    {
        $this->columns = $columns;
    }

    public function bindings()
    {
        return $this->grammar->bindings();
    }

    /**
	 * Select a record in the database.
	 *
	 * @throws BuilderException
	 *
	 * @return string
	 */
    public function select()
    {
        if (!is_string($this->table)) {
            throw new BuilderException('Table is not defined!');
        }

        return $this->grammar->select(
            $this->limit, $this->table, $this->orders, $this->wheres, $this->columns
        );
    }

    /**
	 * Insert a record in the database.
	 *
	 * @throws BuilderException
	 *
	 * @return string
	 */
    public function insert()
    {
        if (empty($this->columns)) {
            throw new BuilderException('empty columns');
        }

        return $this->grammar->insert($this->table, $this->columns);
    }

    /**
	 * Update a record in the database.
	 *
	 * @throws BuilderException
	 *
	 * @return string
	 */
    public function update()
    {
        if (empty($this->columns)) {
            throw new BuilderException('empty columns');
        }

        return $this->grammar->update(
            $this->limit, $this->table, $this->wheres, $this->columns
        );
    }

    /**
     * Delete a record from the database.
     *
     * @return mixed
     */
    public function delete()
    {
        return $this->grammar->delete(
            $this->limit, $this->table, $this->wheres, $this->columns
        );
    }

    public function builderFlush()
    {
        $this->table = null;
        $this->limit = null;

        $this->columns = array();
        $this->wheres = array();
        $this->orders = array();
        $this->grammar->grammarFlush();
    }

}
