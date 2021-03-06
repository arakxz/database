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
     * Indicates if the query returns distinct results.
     *
     * @var bool
     */
    public $distinct = false;

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

    /**
	 * Set the table which the query is targeting.
	 *
     * @param  string $table
     *
     * @return void
     */
    public function table($table)
    {
        $this->table = $table;
    }

    /**
	 * Set the "limit" value of the query.
	 *
     * @param  integer $limit
     *
     * @return void
     */
    public function limit($limit)
    {
        $this->limit = $limit;
    }

    /**
	 * Add a basic where clause to the query.
	 *
     * @throws \Arakxz\Database\Exception\BuilderException
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
	 * Add an "order by" clause to the query.
	 *
     * @param  string $column
     * @param  string $sorted
     *
     * @return void
     */
    public function order($column, $sorted)
    {
        $this->orders[] = array($column, strtoupper($sorted));
    }

    /**
     * @param  string $name  [description]
     * @param  mixed  $value [description]
     *
     * @return void
     */
    public function column($name, $value)
    {
        $this->columns[$name] = $value;
    }

    /**
     * @param  array $columns
     *
     * @return void
     */
    public function columns(array $columns)
    {
        $this->columns = $columns;
    }

    /**
     * @return array
     */
    public function bindings()
    {
        return $this->grammar->bindings();
    }

    /**
	 * Force the query to only return distinct results.
	 *
     * @return void
     */
    public function distinct()
    {
        $this->distinct = true;
    }

    /**
	 * Select statement.
	 *
	 * @throws \Arakxz\Database\Exception\BuilderException
	 *
	 * @return string
	 */
    public function select()
    {
        if (!is_string($this->table)) {
            throw new BuilderException('Table is not defined!');
        }

        return $this->grammar->select(
            $this->limit, $this->table, $this->orders, $this->wheres, $this->columns, $this->distinct
        );
    }

    /**
	 * Insert statement.
	 *
	 * @throws \Arakxz\Database\Exception\BuilderException
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
	 * Update statement.
	 *
	 * @throws \Arakxz\Database\Exception\BuilderException
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
     * Delete statement.
     *
     * @return string
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
        $this->distinct = false;

        $this->columns = array();
        $this->wheres = array();
        $this->orders = array();
        $this->grammar->grammarFlush();
    }

}
