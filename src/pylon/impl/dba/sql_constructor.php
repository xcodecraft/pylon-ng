<?php

/**\addtogroup DBA
 * @{
 */
/** 
 * @brief 
 * @example sql_construct_test.php
 */
abstract class SQLStatement
{
    protected $_tableName;
//    public $_tableName;

    function SQLStatement($tableName )
    {
        assert($tableName != "");
        $this->_tableName = $tableName;
    }
    function getTableName()
    {
        return $this->_tableName;
    }
    abstract public function generateSql();
}

/** 
 * @brief 
 */
class SQLInsertStatment extends  SQLStatement 
{
    var $_datas;
    var $_columns;
    public function __construct($tableName)
    {
        parent::__construct($tableName); 
    }
    public function datas($datas)
    {
        $this->_datas = $datas;
    }
    public function dataArray($datas)
    {
        $this->_datas = implode(',',$datas);
    }
    public function columnArray($columns)
    {
        $this->_columns = JoinUtls::j2csv($columns);
    }
    public function columns($columns)
    {
        $this->_columns = $columns;
    }
    function generateSql()
    {
        return sprintf("insert %s (%s) values(%s);",$this->_tableName,$this->_columns,$this->_datas);
    }
}
/** 
 * @brief 
 */
class SQLUpdateStatment extends SQLStatement
    {
        var $_condition;
        var $_updateColumns;
        public function __construct($tableName)
        {
            parent::__construct($tableName); 
        }
        public function updateColumns($str)
        {
            $this->_updateColumns =$str;
        }

        public function where($condition)
        {
            $this->_whereCond= $condition;
            return $this;
        }
        public function generateSql()
        {

            $where="";
            if ($this->_whereCond !=null)
            {
                $where = $this->_whereCond;
            }
            $whereSql = ($where == "") ? "" : " where ( ".$where." )";
            return sprintf("update %s set %s %s;",$this->_tableName,$this->_updateColumns,$whereSql);
        }

    }

/** 
 * @brief 
 */
class SQLSelectStatement extends SQLStatement
{
    var $_columns=" * ";
    var $_joinTables=array();
    var $_groupBy=null;
    var $_orderBy=null;
    var $_whereCond=null;
    var $_viewCond=null;
    var $_view =null;
    var $_pageCond = "";
    var $_limit = "";

    function SQLSelectStatement($view , $viewCond=null)
    {
        $this->_view = $view;
        $this->_viewCond = $viewCond;
    }
    function columns($columns)
    {
        $this->_columns = $columns;
    }
    function leftJoin($tableName,$condition)
    {
        array_push($this->_joinTables,"left join $tableName on $condition");
    }
    function innerJoin($tableName,$condition)
    {
        array_push($this->_joinTables,"inner join $tableName on $condition");
    }
    function joinPage($dataPage)
    {
        $fromRow =0 ;                                                                                                                  
        $pageSize =0;                                                                                                                  
        $dataPage->getRowRange($fromRow,$pageSize);                                                                                       
        $this->_pageCond = " limit $fromRow,$pageSize ";
    }
    /** 
     * @brief 
     * 
     * @return sql command string 
     */
    public function generateSql()
    {
        $sql = "select ".$this->_columns." from ".$this->_view;
        foreach($this->_joinTables as $table )
        {
            $sql .= $table ;
        }
        $cond  = array();
        if(! empty($this->_whereCond)) array_push($cond,$this->_whereCond);
        if(! empty($this->_viewCond))  array_push($cond,$this->_viewCond);
        $where = JoinUtls::jarray(" and ",$cond);

        $whereSql = ($where == "") ? "" : " where ( ".$where." )";

        $sql .= $whereSql;
        $s =  JoinUtls::j2str($this->_groupBy,$this->_orderBy,$this->_limit);
        return $sql.$s.$this->_pageCond.";";
    }
    function generateCountSql()
    {
        $sql = "select count(1) as rowcount from ".$this->_view;
        foreach($this->_joinTables as $table )
        {
            $sql .= $table ;
        }
        $where = JoinUtls::jarray(" and ",array($this->_whereCond,$this->_viewCond));

        $whereSql = ($where == "") ? "" : " where ( ".$where." )";

        $sql .= $whereSql;
        $s =  JoinUtls::j2str($this->_groupBy,$this->_orderBy);
        return $sql.$s.";";
    }
    /** 
     * @brief ½ÓÈëWhereÌõ¼þ
     * 
     * @param $condition  ±ØÐëÎª×Ö·û´®ÀàÐÍ
     * 
     * @return  void 
     */
    function where($condition)
    {
        DBC::requireTrue(is_string($condition),'is_string($condition)');
        $this->_whereCond= $condition;
        return $this;
    }
    /** 
     * @brief 
     * 
     * @param $groupBy  ±ØÐëÎª×Ö·û´®
     * 
     * @return void 
     */
    function groupBy($groupBy)
    {
        $this->_groupBy = " group by ".$groupBy;

    }
    /** 
     * @brief 
     * 
     * @param $orderBy  ±ØÐëÎª×Ö·û´®
     * @param $orderType 
     * 
     * @return void 
     */
    function orderBy($orderBy,$orderType='DESC')
    {
        if(!is_null($orderBy))
            $this->_orderBy = " order by `$orderBy` $orderType ";
    }
    /**
        * @brief
        *
        * @param $orderByArr array($col1=>"ASC",$col2=>"DESC")
        *
        * @return
     */
    function multiOrderBy($orderByArr)
    {
        if(!is_array($orderByArr)) return;
        $orderBy = "";
        $idx = 0;
        foreach($orderByArr as $col => $type)
        {
            if($idx === 0)
            {
                $orderBy .= " order by ";
            }
            $orderBy .= "`$col` $type ";
            if($idx < count($orderByArr) - 1)
            {
                $orderBy .= ",";
            }
            $idx++;
        }
        $this->_orderBy = $orderBy;
    }
    function limit($from,$num)
    {
        $from = intval($from);
        $num = intval($num);
        $this->_limit = " limit $from,$num";
    }
}

/** 
 * @brief 
 *
 */
class SQLDelStatement extends SQLStatement
{
    var $_condition;
    public function __construct($tableName)
    {
        parent::__construct($tableName); 
    }

    public function where($condition)
    {
        $this->_whereCond= $condition;
        return $this;
    }
    public function generateSql()
    {

        $where="";
        if ($this->_whereCond !=null)
        {
            $where = $this->_whereCond;
        }
        $whereSql = ($where == "") ? "" : "where (".$where." )";
        return sprintf("delete from %s %s;",$this->_tableName,$whereSql);
    }

}


/** 
 *  @}
 */
?>
