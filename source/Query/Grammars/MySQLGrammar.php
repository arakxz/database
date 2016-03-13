<?php
namespace Arakxz\Database\Query\Grammars;

use \Arakxz\Database\Query;

class MySQLGrammar implements Grammar
{

    /**
     * The current query value bindings.
     *
     * @var array
     */
    private $bindings = array();

    /**
     * All of the available clause operators.
     *
     * @var array
     */
    private $operators = array(
        '=', '<', '<>', '>', '>=', '<=',
        'LIKE', 'NOT LIKE', 'BETWEEN', 'IN',
    );

    private $placeholder = '?';

    public function __construct()
    {
        # construct
    }

    public function __destruct()
    {
        # destruct
    }

    /**
     * @return array
     */
    public function bindings()
    {
        return $this->bindings;
    }

    /**
     * @param  string $operator
     *
     * @return boolean
     */
    public function hasOperator($operator)
    {
        return in_array($operator, $this->operators);
    }

    /**
     * @return void
     */
    public function grammarFlush()
    {
        $this->bindings = array();
    }

    /**
     * @param  integer $limit
     * @param  string  $table
     * @param  array   $orders
     * @param  array   $wheres
     * @param  array   $columns
     * @param  boolean $distinct
     *
     * @return string
     */
    public function select($limit, $table, array $orders, array $wheres, array $columns, $distinct)
    {

        # alias pattern
        $pattern = '/(?P<l>\w+)\s+as\s+(?P<r>\w+)/i';

        $statement[] = $distinct ? 'SELECT DISTINCT' : 'SELECT';
        $statement[] = $this->placeholder;
        $statement[] = 'FROM';
        $statement[] = $this->placeholder;

        $replace[] = empty($columns)
            ? '*'
            : '`' . implode('`, `', array_map(

                function ($column) use ($pattern)
                {
                    return ((bool) preg_match($pattern, $column, $aux)) ? $aux['l'] . '` AS `' . $aux['r'] : $column;
                }
                , $columns

            )) . '`';

        $replace[] = '`' . $table . '`';

        # add a basic where clause to the query.
        if (!empty($wheres)) {

            $statement[] = 'WHERE';
            $statement[] = $this->placeholder;

            $replace[] = $this->compileWheres($wheres);

        }

        if (!empty($orders)) {

            $statement[] = 'ORDER BY';
            $statement[] = $this->placeholder;

            $replace[] = $this->compileOrders($orders);

        }

        if (is_numeric($limit)) {

            $statement[] = 'LIMIT';
            $statement[] = $this->placeholder;

            $replace[] = $limit;

        }

        return $this->prepare(implode("\x20", $statement), $replace);

    }

    /**
     * @param  string  $table
     * @param  array   $columns
     *
     * @return string
     */
    public function insert($table, array $columns)
    {

        $statement[] = 'INSERT INTO';
        $statement[] = $this->placeholder;
        $statement[] = $this->placeholder;
        $statement[] = 'VALUES';
        $statement[] = $this->placeholder;

        $replace[] = '`' . $table . '`';

        $c = [];
        $v = [];

        foreach ($columns as $column => $value) {

            $c[] = $column;
            $v[] = ':c' . $column;

            $this->bindings['c' . $column] = $value;

        }

        $replace[] = '(`' . implode('`, `', $c) . '`)';
        $replace[] = '('  . implode(', '  , $v) .  ')';

        return $this->prepare(implode("\x20", $statement), $replace);

    }

    /**
     * @param  integer $limit
     * @param  string  $table
     * @param  array   $wheres
     * @param  array   $columns
     *
     * @return string
     */
    public function update($limit, $table, array $wheres, array $columns)
    {

        $statement[] = 'UPDATE';
        $statement[] = $this->placeholder;
        $statement[] = 'SET';
        $statement[] = $this->placeholder;

        $c = [];

        foreach ($columns as $column => $value)
        {
            $c[] = "`$column`=:v$column"; $this->bindings['v' . $column] = $value;
        }

        $replace[] = '`' . $table . '`';
        $replace[] = implode(', ', $c);

        # add a basic where clause to the query.
        if (!empty($wheres)) {

            $statement[] = 'WHERE';
            $statement[] = $this->placeholder;

            $replace[] = $this->compileWheres($wheres);

        }

        if (is_numeric($limit)) {

            $statement[] = 'LIMIT';
            $statement[] = $this->placeholder;

            $replace[] = $limit;

        }

        return $this->prepare(implode("\x20", $statement), $replace);

    }

    /**
     * @param integer $limit
     * @param string  $table
     * @param array   $wheres
     * @param array   $columns
     *
     * @return string
     */
    public function delete($limit, $table, array $wheres, array $columns)
    {
        $statement[] = 'DELETE FROM';
        $statement[] = $this->placeholder;

        $replace[] = '`' . $table . '`';

        # add a basic where clause to the query.
        if (!empty($wheres)) {

            $statement[] = 'WHERE';
            $statement[] = $this->placeholder;

            $replace[] = $this->compileWheres($wheres);

        }

        if (is_numeric($limit)) {

            $statement[] = 'LIMIT';
            $statement[] = $this->placeholder;

            $replace[] = $limit;

        }

        return $this->prepare(implode("\x20", $statement), $replace);

    }

    /**
     * Compile the "order by" portions of the query.
     *
     * @param  string  $orders
     *
     * @return string
     */
    private function compileOrders(array $orders)
    {
        $o = [];

        foreach ($orders as $p)
        {
            list($column, $sorted) = $p; $o[] = "`$column` $sorted";
        }

        return implode(', ', $o);
    }

    /**
    * Compile the "where" portions of the query.
    *
    * @param  array $wheres
    *
    * @return string
    */
    private function compileWheres(array $wheres)
    {
        $w = [];

        foreach ($wheres as $p) {

            list($column, $operator, $value) = $p;

            switch ($operator) {

                # The IN operator allows you to specify multiple values in a
                # WHERE clause.
                #
                # http://www.w3schools.com/sql/sql_in.asp
                case 'IN':

                    $c = [];
                    foreach ($value as $k => $v)
                    {
                        $c[] = ':win' . $k;

                        $this->bindings['win' . $k] = $v;
                    }

                    $w[] = "`$column` $operator (" . implode(', ', $c) . ")";
                    break;

                # The BETWEEN operator is used to select values within a range.
                #
                # http://www.w3schools.com/sql/sql_between.asp
                case 'BETWEEN':

                    $w[] = "`$column` $operator :wl$column AND :wr$column";

                    $this->bindings['wl' . $column] = $value[0];
                    $this->bindings['wr' . $column] = $value[1];
                    break;

                # The WHERE clause is used to extract only those records that fulfill
                # a specified criterion.
                #
                # http://www.w3schools.com/sql/sql_where.asp
                default:

                    $w[] = "`$column` $operator :w$column";

                    $this->bindings['w' . $column] = $value;
                    break;

            }

        }

        return implode(' AND ', $w);
    }

    /**
     * @param  string $query
     * @param  array  $params
     *
     * @return string
     */
    private function prepare($query, $params)
    {
        $l = substr_count($query, $this->placeholder);

        for ($i = 0; $i < $l; $i++) {
            $query = substr_replace($query, $params[$i], strpos($query, $this->placeholder), 1);
        }

        return $query;
    }

}
