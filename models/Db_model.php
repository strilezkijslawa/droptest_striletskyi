<?php
/**
 * Work with database
 */

class Db_model
{
    /**
     * @var pdo object
     */
    public $db;

    /**
     * @var array db config
     */
    private $_config = [];

    private $_request = '';

    private $_select = '*';

    private $_left_join = [];

    private $_right_join = [];

    private $_join = [];

    private $_where = [];

    private $_where_or = [];

    private $_group_by = [];

    private $_limit = [];

    private $_order_by;

    private $_result;

    /**
     * Db_model constructor.
     */
    public function __construct()
    {
        $config = require APP_PATH . 'config/db.php';
        $this->setConfig( $config );

        $this->init();
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * @param array $config
     */
    public function setConfig($config)
    {
        $this->_config = $config;
    }

    /**
     * @return string
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * @return string
     */
    public function getSelect()
    {
        return $this->_select;
    }

    /**
     * @param string $select
     */
    public function setSelect($select)
    {
        $this->_select = $select;
    }

    /**
     * @return array
     */
    public function getLeftJoin()
    {
        return $this->_left_join;
    }

    /**
     * @param string $left_join, string $on
     */
    public function setLeftJoin($left_join, $on)
    {
        $this->_left_join = [$left_join, $on];
    }

    /**
     * @return array
     */
    public function getRightJoin()
    {
        return $this->_right_join;
    }

    /**
     * @param string $right_join, string $on
     */
    public function setRightJoin($right_join, $on)
    {
        $this->_right_join = [$right_join, $on];
    }

    /**
     * @return array
     */
    public function getJoin()
    {
        return $this->_join;
    }

    /**
     * @param string $join, string $on
     */
    public function setJoin($join, $on)
    {
        $this->_join = [$join, $on];
    }

    /**
     * @return array
     */
    public function getWhere()
    {
        return $this->_where;
    }

    /**
     * @param array $where
     */
    public function setWhere($where)
    {
        !is_array($where) && $where = [$where];
        $this->_where = $where;
    }

    /**
     * @return array
     */
    public function getWhereOr()
    {
        return $this->_where_or;
    }

    /**
     * @param array $where_or
     */
    public function setWhereOr($where_or)
    {
        !is_array($where_or) && $where_or = [$where_or];
        $this->_where_or = $where_or;
    }

    /**
     * @return mixed
     */
    public function getLimit()
    {
        return $this->_limit;
    }

    /**
     * @param mixed $limit
     */
    public function setLimit($limit, $offset = 0)
    {
        $this->_limit = [$limit, $offset];
    }

    /**
     * @return array
     */
    public function getGroupBy()
    {
        return $this->_group_by;
    }

    /**
     * @param array $group_by
     */
    public function setGroupBy($group_by)
    {
        !is_array($group_by) && $group_by = [$group_by];
        $this->_group_by = $group_by;
    }

    /**
     * @return mixed
     */
    public function getOrderBy()
    {
        return $this->_order_by;
    }

    /**
     * @param mixed $order_by
     */
    public function setOrderBy($order_by, $order = 'DESC')
    {
        $this->_order_by = [$order_by, $order];
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->_result;
    }

    /**
     * @param mixed $result
     */
    public function setResult($result)
    {
        $this->_result = $result;
    }

    /**
     * Create pdo
     */
    public function init()
    {
        try {
            $config = $this->getConfig();
            $this->db = new PDO('mysql:host=' . $config['host'] . ';dbname=' . $config['dbname'] . ';charset=' . $config['charset'], $config['user'], $config['password']);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch ( PDOException $exception ) {
            echo "Connection failed: " . $exception->getMessage();
        }
    }

    /**
     * Get data from db table
     * @param $table_name
     * @param array $where
     * @param array $params
     */
    public function get( $table_name, $where = [] )
    {
        if ( $this->db ) {
            $request = $this->_preGet($table_name, $where);
            $this->setResult( $this->db->query($request) );

            return $this;
        }

        return null;
    }

    /**
     * Fetch data as object
     * @return mixed
     */
    public function asObject()
    {
        $result = $this->getResult()->fetchAll(PDO::FETCH_OBJ);
        $this->_clearResult();

        return $result;
    }

    /**
     * Fetch data as array
     * @return mixed
     */
    public function asArray()
    {
        $result = $this->getResult()->fetchAll(PDO::FETCH_ASSOC);
        $this->_clearResult();

        return $result;
    }

    /**
     * Fetch data as object
     * @return mixed
     */
    public function rowAsObject()
    {
        $result = $this->getResult()->fetch(PDO::FETCH_OBJ);
        $this->_clearResult();

        return $result;
    }

    /**
     * Fetch data as array
     * @return mixed
     */
    public function rowAsArray()
    {
        $result = $this->getResult()->fetch(PDO::FETCH_ASSOC);
        $this->_clearResult();

        return $result;
    }

    /**
     * Before get data from table
     * @param $table_name
     * @param array $where
     * @return string
     */
    private function _preGet( $table_name, $where = [] )
    {
        $this->_request = 'SELECT ' . $this->getSelect() . ' FROM ' . $table_name;
        $this->_getJoinString();
        $this->_getWhereString( $where );
        $this->_getGroupByString();
        $this->_getOrderByString();
        $this->_getLimitString();

        return $this->getRequest();
    }

    /**
     * Parse where arrays as string and add to query
     * @param array $where
     */
    private function _getWhereString( $where = [] )
    {
        $where_arr = $this->getWhere();
        if (!empty($where)) {
            foreach (array_keys($where) as $key) {
                $where_arr[$key] = $where[$key];
            }
            unset($where);
        }
        if ( !empty($where_arr) ) {

            $where_str = ' WHERE';
            $i = 1;
            $n = count($where_arr);
            foreach ($where_arr as $key => $item) {
                $where_str .= " $key = $item";
                if ($i < $n) {
                    $where_str .= ' AND';
                }
                $i++;
            }

            $this->_request .= $where_str;
        }

        $where_arr = $this->getWhereOr();
        if ( !empty($where_arr) ) {
            $where_str = strpos($this->_request, 'WHERE') === false ? ' WHERE' : '';
            $i = 1;
            $n = count($where_arr);
            $where_str .= ' (';
            foreach ($where_arr as $key => $item) {
                $where_str .= " $key = $item";
                if ($i < $n) {
                    $where_str .= ' OR';
                }
                $i++;
            }
            $where_str .= ' )';

            $this->_request .= $where_str;
        }
    }

    /**
     * Parse limit array as string and add to query
     */
    private function _getLimitString()
    {
        $where_arr = $this->getLimit();
        if ( !empty($where_arr) ) {
            $where_str = ' LIMIT ' . $where_arr[0] . ' OFFSET ' . $where_arr[1];
            $this->_request .= $where_str;
        }
    }

    /**
     * Parse join arrays as string and add to query
     */
    private function _getJoinString()
    {
        $join_arr = $this->getJoin();
        if ( !empty($join_arr) ) {
            $join_str = " INNER JOIN $join_arr[0] ON $join_arr[1]";
            $this->_request .= $join_str;
        }

        $join_arr = $this->getLeftJoin();
        if ( !empty($join_arr) ) {
            $join_str = " LEFT JOIN $join_arr[0] ON $join_arr[1]";
            $this->_request .= $join_str;
        }

        $join_arr = $this->getRightJoin();
        if ( !empty($join_arr) ) {
            $join_str = " RIGHT JOIN $join_arr[0] ON $join_arr[1]";
            $this->_request .= $join_str;
        }
    }

    /**
     * Parse groupby array as string and add to query
     */
    private function _getGroupByString()
    {
        $where_arr = $this->getGroupBy();
        if ( !empty($where_arr) ) {
            $where_str = ' GROUP BY';
            foreach ($where_arr as $item) {
                $where_str .= " $item";
            }
            $this->_request .= $where_str;
        }
    }

    /**
     * Parse orderby array as string and add to query
     */
    private function _getOrderByString()
    {
        $where_arr = $this->getOrderBy();
        if ( !empty($where_arr) ) {
            $where_str = ' ORDER BY ' . $where_arr[0] . ' ' . $where_arr[1];
            $this->_request .= $where_str;
        }
    }

    private function _clearResult()
    {
        $this->_request = '';
        $this->setResult( null );
    }
}