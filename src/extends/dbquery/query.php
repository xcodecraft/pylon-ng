<?php
namespace Pylon\db;

class Cond
{
    const SQL_IN_EXP      = 'in';
    const SQL_NOTIN_EXP   = 'notin';
    const SQL_LIKE_EXP    = 'like';
    const SQL_NOTLIKE_EXP = 'notlike';
    const SQL_EQ_EXP      = 'eq';

    public static $inExpArr   = array(self::SQL_IN_EXP, self::SQL_NOTIN_EXP);
    public static $likeExpArr = array(self::SQL_LIKE_EXP, self::SQL_NOTLIKE_EXP);
    public static $mysqlFun   = array('count', 'sum', 'min', 'max', 'avg');

    public static $_comparison
        = array(
            self::SQL_EQ_EXP      => '=',
            'neq'                 => '!=',
            'gt'                  => '>',
            'egt'                 => '>=',
            'lt'                  => '<',
            'elt'                 => '<=',
            self::SQL_NOTLIKE_EXP => 'NOT LIKE',
            self::SQL_LIKE_EXP    => 'LIKE',
            self::SQL_IN_EXP      => 'IN',
            self::SQL_NOTIN_EXP   => 'NOT IN',
        );

    public static $_valComparison
        = array(
            'gt&lt'   => '/^\(([^,]){1,},([^,]){1,}\)$/',//开区间  in为字段，(1,2) [2016-01-02,2017-09-08]
            'gt&elt'  => '/^\(([^,]){1,},([^,]){1,}\]$/',//半开半闭
            'egt&lt'  => '/^\[([^,]){1,},([^,]){1,}\)$/',//半闭半开
            'egt&elt' => '/^\[([^,]){1,},([^,]){1,}\]$/',//闭区间
            'in'      => '/^\{(\S{1,})(\S{1,},)?\}$/', //in
            'notin'   => '/^!\{(\S{1,})(\S{1,},)?\}$/', //notin
            'egt'     => '/^>=/',
            'elt'     => '/^<=/',
            'neq'     => '/^!=/',
            'gt'      => '/^>/',
            'lt'      => '/^</',
        );
}

class dbquery
{
    private $_db     = null;
    private $_values = array();
    private $_fields = array();

    public function __construct()
    {
        if (is_null($this->_db)) {
            $this->_db = \XQuery::sql();
        }
    }

    public function __set($k, $v)
    {
        if (strpos($k, '_') !== false) {
            $field = substr($k, 0, strrpos($k, '_'));
            $exp   = substr($k, strrpos($k, '_') + 1);
            if (isset(Cond::$_comparison[$exp])) {
                $this->setQuery($field, $v, $exp);
            } else {
                $this->$k = $v;
            }
        } else {
            $this->$k = $v;
        }
    }

    public function setCond($field, array $cond)
    {
        $exp = $cond[0];
        $v   = $cond[1];

        return $this->setQuery($field, $v, $exp);
    }

    protected function setQuery($k, $v, $exp = Cond::SQL_EQ_EXP)
    {
        if ($exp != Cond::SQL_EQ_EXP) {
            unset($this->_fields[$k][Cond::SQL_EQ_EXP]);
            unset($this->_values[$k][Cond::SQL_EQ_EXP]);
        }
        if (in_array($exp, Cond::$inExpArr)) {
            $val                     = Cond::$_comparison[$exp];
            $vArr                    = explode(',', $v);
            $this->_values[$k][$exp] = implode(',', $vArr);
            foreach ($vArr as &$tempV) {
                $tempV = '?';
            }
            $this->_fields[$k][$exp] = $val . ' ( ' . implode(',', $vArr) . ' ) ';
        } elseif (in_array($exp, Cond::$likeExpArr)) {
            $val                     = Cond::$_comparison[$exp] . ' ? ';
            $this->_fields[$k][$exp] = $val;
            $this->_values[$k][$exp] = "%" . $v . "%";
        } else {
            $val                     = Cond::$_comparison[$exp] . ' ? ';
            $this->_fields[$k][$exp] = $val;
            $this->_values[$k][$exp] = $v;
        }

        return $this;
    }

    public function getWhere()
    {
        krsort($this->_fields);
        krsort($this->_values);
        $values = array();
        $where  = '';
        foreach ($this->_fields as $key => $field) {
            $keyTmp   = strpos($key, '.') !== 0 ? ' ' . $key . ' ' : ' `' . $key . '`';
            $tmpWhere = ! empty($where) ? ' and ( ' : ' ( ';
            $i        = 0;
            foreach ($field as $exp => $v) {
                $tmpWhere .= ($i > 0 ? ' and ' : '') . $keyTmp . $v;
                $i++;
                if (in_array($exp, Cond::$inExpArr)) {
                    $tmpvArr = explode(',', $this->_values[$key][$exp]);
                    foreach ($tmpvArr as $tmpv) {
                        $values[] = $tmpv;
                    }
                } else {
                    $values[] = $this->_values[$key][$exp];
                }
            }
            $tmpWhere .= ' ) ';
            $where .= $tmpWhere;
        }

        return array(
            empty($where) ? '' : ' where ' . $where,
            $values,
        );
    }

    protected function sqlKeyFilter(&$kArray)
    {
        foreach ($kArray as $k => $v) {
            $k = strtolower($k);
            if ($k == 'limit') {
                $this->setLimit($kArray['limit']);
                unset($kArray['limit']);
            }
            if ($k == 'order') {
                $this->setOrder($kArray['order']);
                unset($kArray['order']);
            }
            if ($k == "group") {
                $this->setGroup($kArray['group']);
                unset($kArray['group']);
            }
        }
    }

    public static function arrIns($clsQuery, $clsDTO, $filterEmpty = true)
    {
        $objArr = get_object_vars($clsQuery);
        $cls    = new dbquery();
        $cls->sqlKeyFilter($objArr);//过滤系统字段 如limit,order
        $dtoArr = get_class_vars($clsDTO);
        foreach ($objArr as $k => $v) {
            if (($v === '' || is_null($v)) && $filterEmpty) {
                unset($cls->$k);
            } else {
                $cls->$k = $v ? $v : '';
                if (array_key_exists($k, $dtoArr) && ! is_array($v)) {
                    $data = $cls->setQueryFromVal($k, $v);
                }
            }
        }

        return $cls;
    }

    private function setQueryFromVal($k, $v)
    {
        $isFind = false;
        foreach (Cond::$_valComparison as $xexp => $reg) {
            if (preg_match($reg, $v) === 1) {
                if ( ! isset(Cond::$_comparison[$xexp])) {
                    $exp1  = substr($xexp, 0, strrpos($xexp, '&'));
                    $exp2  = substr($xexp, strrpos($xexp, '&') + 1);
                    $vstr  = substr($v, 1, strlen($v) - 2);
                    $vstr1 = substr($vstr, 0, strrpos($vstr, ','));
                    $vstr2 = substr($vstr, strrpos($vstr, ',') + 1);
                    $this->setQuery($k, $vstr1, $exp1);
                    $this->setQuery($k, $vstr2, $exp2);
                } else {
                    if (in_array($xexp, Cond::$inExpArr)) {
                        if ($xexp == Cond::SQL_IN_EXP) {
                            $v = substr($v, 1, strlen($v) - 2);
                        } else {
                            $v = substr($v, 2, strlen($v) - 3);
                        }
                    } else {
                        $v = str_replace(Cond::$_comparison[$xexp], '', $v);
                    }
                    $this->setQuery($k, $v, $xexp);
                }
                $isFind = true;
                break;
            }
        }
        if ( ! $isFind) {
            $this->setQuery($k, $v);
        }

        return false;
    }

    public function __call($method, $arg)
    {
        if (strpos($method, 'set') === 0) {
            $k        = strtolower('_' . str_replace('set', '', $method));
            $arg[0]   = rawurldecode($arg[0]);
            $this->$k = $arg[0];

            return $this;
        } else if (strpos($method, 'get') === 0) {
            $k = strtolower('_' . str_replace('get', '', $method));

            return $this->$k;
        } else if (in_array(strtolower($method), Cond::$mysqlFun, true)) {
            // 统计查询的实现
            $field = isset($arg[0]) ? $arg[0] : '*';
            return $this->setField(strtoupper($method) . '(' . $field . ') AS ayb_' . $method)->fetchOne();
        }
    }

    public function getFields()
    {
        return isset($this->_field) ? $this->_field : '*';
    }

    public function getLimit()
    {
        $start = 0;
        $limit = 20;
        if (isset($this->_limit) && ! empty($this->_limit) && preg_match('/^\d{1,},\d{1,}$/', $this->_limit) === 1) {
            $limit = $this->_limit;
            $start = substr($limit, 0, strrpos($limit, ','));
            $limit = substr($limit, strrpos($limit, ',') + 1);
        }

        return array($start, $limit);
    }

    public function getOrder()
    {
        if (property_exists($this, '_order') && ! empty($this->_order)) {
            return ' order by ' . $this->_order;
        } else {
            return '';
        }
    }

    public function getGroup()
    {
        if (property_exists($this, '_group') && ! empty($this->_group)) {
            return ' group by ' . $this->_group;
        } else {
            return '';
        }
    }

    private function prepareQuery($sql = '', $where = array())
    {
        $values = array();
        if (empty($sql) && empty($where)) {
            list($where, $values) = $this->getWhere();
            $table    = $this->getTable();
            $fields   = $this->getFields();
            $order    = $this->getOrder();
            $group    = $this->getGroup();
            $sqlQuery = "select " . $fields . " from " . $table . $where . $group . $order;
        } elseif ( ! empty($sql) && empty($where)) {
            $sqlQuery = $sql;
            if (strpos(strtolower($sqlQuery), ' where ') === false) {
                list($where, $values) = $this->getWhere();
                $order    = $this->getOrder();
                $group    = $this->getGroup();
                $sqlQuery = $sqlQuery . $where . $group . $order;
            }
        } elseif (empty($sql) && ! empty($where)) {
            $this->where($where);
            list($where, $values) = $this->getWhere();
            $table    = $this->getTable();
            $fields   = $this->getFields();
            $order    = $this->getOrder();
            $group    = $this->getGroup();
            $sqlQuery = "select " . $fields . " from " . $table . $where . $group . $order;
        } else {
            $this->where($where);
            list($where, $values) = $this->getWhere();
            $order    = $this->getOrder();
            $group    = $this->getGroup();
            $sqlQuery = $sql . $where . $group . $order;
        }

        return array($sqlQuery, $values);
    }

    public function where(array $where)
    {
        $this->sqlKeyFilter($where);
        foreach ($where as $k => $v) {
            $this->setQueryFromVal($k, $v);
        }

        return $this;
    }

    public function table($table)
    {
        $this->setTable(strtolower($table));

        return $this;
    }

    public function order($orderStr)
    {
        $this->setOrder($orderStr);

        return $this;
    }

    public function group($groupStr)
    {
        $this->setGroup($groupStr);

        return $this;
    }

    public function limit($limitStr)
    {
        $this->setLimit($limitStr);

        return $this;
    }

    //获取分页查询结果
    private function fetchPage($sql, $pageObj, $values = array())
    {
        $countSql = preg_replace("/^select /", "select SQL_CALC_FOUND_ROWS ", strtolower(trim($sql)));
        $countSql = $countSql . ' limit 1';
        $this->_db->query($countSql, $values);
        $countRet = $this->_db->query('SELECT FOUND_ROWS() as count');
        if ( ! $pageObj->isInit) {
            $pageObj->initTotalRows($countRet['count']);
        }
        if ($countRet['count']) {
            $sql   = $sql . $pageObj->toLimitStr();
            $datas = $this->_db->querys($sql, $values);
        } else {
            $datas = array();
        }
        $pageObj->setData($datas);

        return $pageObj->attr;
    }


    /**
     * 实例化dbquery
     *
     * @param string $table
     *
     * @return dbquery
     */
    public static function Q($table = '')
    {
        $query = new dbquery();
        if (empty($table)) {
            return $query;
        } else {
            return $query->table($table);
        }
    }

    /**
     * 设置数据库的链接
     *
     * @param $dbObj
     *
     * @return $this
     */
    public function db($dbObj)
    {
        $this->_db = $dbObj;

        return $this;
    }

    /**
     * 直接执行sql语句 例如update delete
     *
     * @param $sql
     *
     * @return mixed
     */
    public function execute($sql)
    {
        return $this->_db->execute($sql);
    }

    /**
     * @param        $table
     * @param string $fields
     *
     * @return mixed 分页查询
     */
    public function queryLimit($table, $fields = '*')
    {
        return $this->table($table)->fetchAll();
    }

    /**
     * @param string $sql
     * @param array  $where
     *
     * @return mixed 获取所有查询结果、支持分页
     */
    public function fetchAll($sql = '', $where = array())
    {
        list($sqlQuery, $values) = $this->prepareQuery($sql, $where);
        if (isset($this->_limit)) {
            list($start, $limit) = $this->getLimit();
            $pageObj = DataPage::limit($start, $limit);

            return $this->fetchPage($sqlQuery, $pageObj, $values);
        } else {
            return $this->_db->querys($sqlQuery, $values);
        }
    }

    /**
     * 获取一行结果
     *
     * @param string $sql
     * @param array  $where
     *
     * @return array()
     */
    public function fetchRow($sql = '', $where = array())
    {
        list($sqlQuery, $values) = $this->prepareQuery($sql, $where);
        if (strpos(strtolower($sqlQuery), ' limit ') === false) {
            $sqlQuery .= ' limit 1';
        }

        return $this->_db->query($sqlQuery, $values);
    }

    public function fetchOne($sql = '', $where = array())
    {
        list($sqlQuery, $values) = $this->prepareQuery($sql, $where);
        if (strpos(strtolower($sqlQuery), ' limit ') === false) {
            $sqlQuery .= ' limit 1';
        }
        $data = $this->_db->query($sqlQuery, $values);

        return $data ? reset($data) : false;
    }

    public function fetchPairs($sql = '', $where = array())
    {
        $datas    = $this->fetchAll($sql, $where);
        $newDatas = array();
        if (isset($datas['datas'])) {
            $datas = $datas['datas'];
        }
        foreach ($datas as $v) {
            if (count($v) < 3) {
                $v1            = array_shift($v);
                $newDatas[$v1] = array_shift($v);
            } else {
                $v1            = array_shift($v);
                $newDatas[$v1] = $v;
            }
        }

        return $newDatas;
    }


}

/*
 * 提供分页器
 */

class DataPage
{
    public static function limit($start, $limit)
    {
        return new DataLimit($start, $limit);
    }
}

/**
 * @ingroup utls
 * @brief   数据页，根据 limit start,limit分页
 */
class DataLimit extends \XProperty
{
    private $maxLimit = 100000; //最大调取条数

    private $default = 20;

    private $limit = 0;

    private $start = 0;

    private $isInit = false;

    public function __construct($start, $limit)
    {
        if ( ! $start) {
            $start = 0;
        }
        if ( ! $limit) {
            $limit = $this->default;
        }
        $this->limit     = $limit > $this->maxLimit ? $this->maxLimit : $limit;
        $this->start     = $start;
        $this->totalRows = 0;
        $this->datas     = array();
        $this->isInit    = false;
    }

    public function initTotalRows($totalRows)
    {
        $this->totalRows = $totalRows;
        $this->isInit    = true;
        $this->start     = $this->start > $totalRows ? $totalRows : $this->start;
        $this->limit     = $this->limit > $totalRows ? $totalRows : $this->limit;
        $this->limit     = $this->limit > $this->maxLimit ? $this->maxLimit : $this->limit;
    }

    public function toLimitStr()
    {
        $begin = $this->start;
        $count = $this->limit;

        return " limit $begin, $count ";
    }

    public function setData($datas)
    {
        $this->datas = $datas;
    }
}

