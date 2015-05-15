<?php

/**\addtogroup Ent
 * @{
 */

function sqlprocutls_ins_not_dqlobj($item)
{
    return ! $item  instanceof DQLObj ;
}
class SqlProcUtls
{
    static public function bindCond($key,$val)
    {
        //处理非单值的情况
        if ($val  instanceof DQLObj)
        {
            return  '(' . $val->tosql($key) . ')';
        }
        return  " $key = ? " ;
    }
    static public function filterCondVal($arr)
    {
        $v= array_filter($arr,sqlprocutls_ins_not_dqlobj);
        return $v;
    }
    static public function  bindUpdate($key,$val)
    {
        return  " $key = ? ";
    }
}

/**
 * @brief  产生一个DQLObj
 *
 * @param $express
 *
 * @return
 */
function QL($express,$symbol='?')
{
    return new DQLObj($express,$symbol);
}
/**
 * @brief
 */
class DQLObj
{
    public $express;
    public $symbol;
    public function __construct($express,$symbol='?')
    {
        $this->express = $express ;
        $this->symbol  = $symbol;
    }
    public function tosql($symbolVal)
    {
        return  str_replace($this->symbol, $symbolVal, $this->express) ;
    }

}


/**
 * @brief  Query对象的基类
 */
class Query
{
    public $exer=null;
    public function __construct($exer,$name=null)
    {
        $this->exer=$exer;
        $this->name=$name;
    }
    /**
     * @brief 通过Cmd获得数据
     *
     * @param $cmd
     * @param $valsArr
     * @param
     *
     * @return
     */
    public function getByCmd($cmd,$valsArr=array())
    {
        return $this->exer->query($cmd,$valsArr);
    }
    public function getRegName()
    {
        if($this->name == null)
            return get_class($this);
        else
            return $this->name;
    }
    public function getExecuter()
    {
        return $this->exer;
    }

    static public function toEntity($cls,$data)
    {
        DBC::unImplement('not implement toEntity function!');
    }

    public function getByProp($prop,$view,$viewCond='',$columns="*",$addiWhereCmd ="", $order=null)
    {
        DBC::requireNotNull($prop);
        $statement = new SQLSelectStatement($view,$viewCond);
        $statement->columns($columns);
        $valsArr =  array();
        $propWhere = self::prop2cmd($prop,$valsArr);
        $statement->where($propWhere.$addiWhereCmd);
        $statement->multiOrderBy($order);
        return $this->getByCmd($statement->generateSql(),$valsArr);
    }
    public function listByCmd($cmd,$valsArr=array())
    {
        $rows = $this->exer->querys($cmd,$valsArr);
        return $rows;

    }

    public function getCount($prop,$hashKey=null)
    {
        DBC::requireNotNull($prop);
        $statement = new SQLSelectStatement($this->getStoreTable($hashKey));
        $valsArr=array();
        if(!$prop->isEmpty())
        {

            $condsArr = $prop->getPropArray();
            $valsArr  = SqlProcUtls::filterCondVal(array_values($prop->getPropArray()));
            $where    = JoinUtls::jassoArrayEx(' and ',$condsArr,array('SqlProcUtls','bindCond'));
            $statement->where($where);
        }
        return $this->getCountImpl($statement,$valsArr);
    }

    private function getCountImpl($statement,$valsArr=array())
    {
        $statement->columns('count(1) as cnt');
        $row = $this->_exer->query($statement->generateSql(),$valsArr);
        return $row['cnt'];
    }

    public function listByCmdPage($cmd,$page,$valsArr=array())
    {
        if($page !=null)
        {
            if(!$page->isInit) $page->initTotalRows($this->countOfCmd($cmd,$valsArr));
            $cmd = $cmd . $page->toLimitStr();
        }
        $rows = $this->exer->querys($cmd,$valsArr);
        return $rows;

    }

    private function countOfCmd($cmd,$valsArr=array())
    {
        if(stristr($cmd,"group") )
        {
            //是group by ,先把选择项变成1 为 “select 1 from ….”;
            //然后在外边包一层 “select count(1) as cnt from (….)  temptablenames”;
            //            DBC::unImplement(" not support   create count sql from  have 'group' sql");
            $ms = date("sihdmy");
            $cntcmd = preg_replace("/(select .+)(from .+)/i","select 1 \$2",$cmd);
            $cntcmd += "select count(1) as cnt from ({$cntcmd}) test_{$ms}";
        }
        else
        {
            $cntcmd = preg_replace("/(select .+)(from .+)/i","select count(1) as cnt \$2",$cmd);
        }
        $row = $this->exer->query($cntcmd,$valsArr);
        return $row['cnt'];
    }
    private function getObjsCountImpl($statement,$valsArr=array())
    {
        $statement->columns('count(1) as cnt');
        $row = $this->exer->query($statement->generateSql(),$valsArr);
        return $row['cnt'];
    }
    static public function prop2cmd($prop,&$valsArr)
    {
        if($prop !=null && (!$prop->isEmpty()))
        {
            $condsArr     = $prop->getPropArray();
            $valsArr      = SqlProcUtls::filterCondVal(array_values($condsArr));
            $placeholders = array_fill(0,count($condsArr),'?');
            $propCmd= JoinUtls::jassoArrayEx(' and ',$condsArr,array('SqlProcUtls','bindCond'));
            return $propCmd;
        }
        return "";
    }
    public function listByProp($view,$viewCond,$columns,$prop=null,$page=null,$orderkey=null,$ordertype='DESC',$addiWhereCmd="")
    {
        $order = null;
        if($orderkey != null)
        {
            $order = array($orderkey => $ordertype);
        }
        $rows = $this->listByPropExt($view, $viewCond, $columns, $prop, $page, $order, $addiWhereCmd);
        return $rows;
    }
    public function listByPropExt($view,$viewCond,$columns,$prop=null,$page=null,$order=null,$addiWhereCmd="")
    {
        $statement = new SQLSelectStatement($view,$viewCond);
        $valsArr = array();
        $propWhere="";
        if($prop !=null && (!$prop->isEmpty()))
        {
            $propWhere = self::prop2cmd($prop,$valsArr);
        }
        $statement->where($propWhere.$addiWhereCmd);
        if($page !=null)
        {
            if(!$page->isInit)
                $page->initTotalRows($this->getObjsCountImpl($statement,$valsArr));
            $begin=0;
            $count=0;
            $page->getRowRange($begin,$count);
            $statement->limit($begin,$count);
        }
        $statement->multiOrderBy($order);
        $statement->columns($columns);
        $rows=$this->listByCmd($statement->generateSql(),$valsArr);
        return $rows;
    }
}

/**
 * @brief
 */
class SimpleQueryFactory
{
    private $execr = null;
    private $isSelfDefFun=null;
    public function __construct($execr)
    {
        $this->execr = $execr;
    }
    public function create($name)
    {
        $cls = XPylon::className($name);
        if($cls)
        {
            return new $cls($this->execr);
        }
        return new Query($this->execr,$name);
    }

    static public function funIns($executer,$isSelfDefFun=null)
    {
        $facotry= new SimpleQueryFactory($executer,array("ComboLoader","classExists"));
        return array($facotry,"create");
    }
}

/**
 *  @}
 */
