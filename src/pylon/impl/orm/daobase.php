<?php
namespace pylon\impl ;
use XDBC ;
use XBox ;
use XEntity ;
use XPylon ;
// use pylon\impl\SqlProcUtls ;
/**\addtogroup Ent
 * @{
 */


/**
* @brief  具体的Dao基类.
 */

class XDaoImplUtls
{

    static public function whereByProp($statement,$prop)
    {
        $where     = JoinUtls::jassoArrayEx(' and ',$prop->getPropArray(),array('pylon\impl\SqlProcUtls','bindCond'));
        $statement->where($where);
    }

    static public function assembleWhere(SQLSelectStatement $statement ,$prop) 
    {
        $valsArr = array();
        if($prop !=null && (!$prop->isEmpty()))
        {
            $condsArr = $prop->getPropArray();
            $valsArr  = SqlProcUtls::filterCondVal(array_values($condsArr));
            XDaoImplUtls::whereByProp($statement,$prop) ;
        }
        return $valsArr ;
        
    }
    static public function genKeyVal($keys,$pairs)
    {
        $arr = array();
        foreach($keys as $key)
        {
            $arr[$key]=$pairs[$key];
        }
        return $arr;
    }
}
class DaoBase
{
    public $_executer   = null;
    public $tableFinder = null;
    public $cls         = null;
    public $hasKey      = null;
    public $loadStg     = null;
    public function __construct($cls,$executer=NULL,$tableFinder=NULL)
    {
        XDBC::requireNotNull($tableFinder,'$tableFinder');
        $this->_executer   = $executer ? $executer : XBox::must_get('SQLExecuter');
        $this->tableFinder = $tableFinder;
        $this->cls         = $cls;
        $this->hashKey     = null;
    }
    public function getExecuter()
    {
        return $this->_executer;
    }
    /**
     * @brief 用于分表存贮
     *
     * @param $key
     *
     * @return
     */
    public function setHashStoreKey($key)
    {
        $this->hashKey=$key;
    }
    public function getStoreTable($hashKey=null)
    {
        $key   = !is_null($hashKey)  ? $hashKey : $this->hashKey;
        return  call_user_func($this->tableFinder,$key);
    }
    public function add($obj)
    {
        $pairs        = $this->obj2row($obj);
        $this->addImpl($pairs,$obj->hashStoreKey());
        $relationSets = $obj->getRelationSets();
        $this->commitRelationSet($relationSets);
    }
    public function update($obj,$keys=array('id'))
    {
        $pairs = $this->obj2row($obj);
        foreach($keys as $k)
        {
            $condArr[$k]= $pairs[$k];
        }
        $this->updateByArray($pairs,$condArr,$obj->hashStoreKey());
        $relationSets = $obj->getRelationSets();
        $this->commitRelationSet($relationSets);
    }

    public function del($obj,$keys=array('id'))
    {
        $pairs     = $this->obj2row($obj);
        $hashKey   = $obj->hashStoreKey();
        $statement = new SQLDelStatement($this->getStoreTable($hashKey));
        $statement->where(JoinUtls::jassoArray(' and ','=',XDaoImplUtls::genKeyVal($keys,$pairs)));
        return $this->_executer->exeNoQuery($statement->generateSql());
    }
    public function delByProp($prop,$hashKey=null)
    {
        $statement = new SQLDelStatement($this->getStoreTable($hashKey));
        XDaoImplUtls::whereByProp($statement,$prop) ;

        $valsArr   = SqlProcUtls::filterCondVal(array_values($prop->getPropArray()));
        return $this->_executer->exeNoQuery($statement->generateSql(),$valsArr);
    }
    public function delByID($id,$hashKey=null)
    {
        $statement = new SQLDelStatement($this->getStoreTable($hashKey));
        $statement->where(" id = $id");
        return $this->_executer->exeNoQuery($statement->generateSql());
    }

    public function getByID($id,$hashKey=null)
    {
        return $this->getByProp(CondProp::make('id',$id),$hashKey);

    }

    public function getByProp($prop,$hashKey=null)
    {
        $statement = new SQLSelectStatement($this->getStoreTable($hashKey));
        $valsArr=array();
        if(!$prop->isEmpty())
        {
            $valsArr  = SqlProcUtls::filterCondVal(array_values($prop->getPropArray()));
            XDaoImplUtls::whereByProp($statement,$prop) ;
        }
        return $this->getByCmd($statement->generateSql(),$valsArr);
    }


    public function getByCmd($cmd,$argvals=array())
    {
        $row = $this->_executer->query($cmd,$argvals);
        if($row ) 
        {
            return $this->convertObj($row);
        }
        return null;
    }

    public function rowByCmd($cmd,$argvals=array())
    {
        $row = $this->_executer->query($cmd,$argvals);
        if($row ) 
        {
            return $row;
        }
        return null;
    }
    public function rowsByCmd($cmd,$argvals=array())
    {
        return  $this->_executer->querys($cmd,$argvals);
    }


    public function listByCmd($cmd,$argvals=array())
    {
        $rows = $this->_executer->querys($cmd,$argvals);

        $objs = array();
        if(is_array($rows))
        {
            foreach($rows as $row)
            {
                $obj =$this->convertObj($row);
                array_push($objs, $obj);
            }
        }
        return $objs;
    }
    public function cntByProp($prop,$hashKey =null)
    {
        $statement = new SQLSelectStatement($this->getStoreTable($hashKey));
        $valsArr = XDaoImplUtls::assembleWhere($statement,$prop) ;
        return $this->statementCount($statement,$valsArr);

    }

    public function getCount($prop,$hashKey=null)
    {
        return  $this->cntByProp($prop,$hashKey);
    }
    public function listByProp($prop=null,$page=null,$orderkey=null,$ordertype='DESC',$hashKey=null)
    {
        $statement = new SQLSelectStatement($this->getStoreTable($hashKey));
        $valsArr = XDaoImplUtls::assembleWhere($statement,$prop) ;
        if($page !=null)
        {
            if(!$page->isInit)
            {
                $page->initTotalRows($this->statementCount($statement,$valsArr));
            }
            $begin=0;
            $count=0;
            $page->getRowRange($begin,$count);
            $statement->limit($begin,$count);
        }
        if($orderkey !=null)
        {
            $statement->orderBy($orderkey,$ordertype);
        }
        $statement->columns('*');
        return $this->listByCmd($statement->generateSql(),$valsArr);
    }

    public function listByPropLimit($prop,$begin,$count,$order=null,$hashKey=null)
    {
        $statement = new SQLSelectStatement($this->getStoreTable($hashKey));
        $valsArr = XDaoImplUtls::assembleWhere($statement,$prop) ;
        $statement->limit($begin,$count);
        $statement->multiOrderBy($order);
        $statement->columns('*');
        return $this->listByCmd($statement->generateSql(),$valsArr);
    }

    static function cls_is_a($parentcls,$cls)
    {
        $pcls = get_parent_class($cls);
        if(empty($pcls)) 
        {
            return false;
        }
        if($pcls  == $parentcls) 
        {
            return true;
        }
        return static::cls_is_a($parentcls,$pcls);
    }

   //  impl function

    protected function addImpl($pairs,$hashKey)
    {
        $statement = new SQLInsertStatment($this->getStoreTable($hashKey));
        $statement->columnArray(array_keys($pairs));

        $pairsCnt= count($pairs);
        XDBC::requireTrue($pairsCnt > 0, "count of pairs is $pairsCnt,it mush > 0 " );
        $placeholders = array_fill(0,$pairsCnt,'?');
        $statement->dataArray($placeholders);
        return $this->_executer->exeNoQuery($statement->generateSql(),array_values($pairs));
    }
    protected function convertObj($row)
    {
        return  $this->row2obj($this->cls,$row);
    }
    protected function updateImpl($keys, $pairs,$hashKey)
    {
        $pairsCnt= count($pairs);
        XDBC::requireTrue($pairsCnt > 0, "count of pairs is $pairsCnt,it mush > 0 " );
        $placeholders = array_fill(0,$pairsCnt,'?');
        $bindParms    = array_combine(array_keys($pairs),$placeholders);
        $statement    = new SQLUpdateStatment($this->getStoreTable($hashKey));


        $statement->updateColumns(JoinUtls::jassoArray(',','=',$bindParms));
        $statement->where(JoinUtls::jassoArray(' and ','=',XDaoImplUtls::genKeyVal($keys,$pairs)));
        return $this->_executer->exeNoQuery($statement->generateSql(),array_values($pairs));
    }

    public function updateByArray($updateArr,$condArr,$hashKey)
    {

        $statement  = new SQLUpdateStatment($this->getStoreTable($hashKey));
        $updateSql  = JoinUtls::jassoArrayEx(',',$updateArr,array('pylon\impl\SqlProcUtls','bindUpdate'));
        $statement->updateColumns($updateSql);
        $statement->where(JoinUtls::jassoArrayEx(' and ',$condArr,array('pylon\impl\SqlProcUtls','bindCond')));
        $condvalArr = SqlProcUtls::filterCondVal(array_values($condArr));
        $bindArr    = array_merge(array_values($updateArr), $condvalArr);
        $sql        = $statement->generateSql();
        return $this->_executer->exeNoQuery($sql,$bindArr);
    }
    public function updateByProp($updateProp,$condProp,$hashKey)
    {
        return $this->updateByArray($updateProp->getPropArray(),$condProp->getPropArray(),$hashKey);
    }
    private function statementCount($statement,$valsArr=array())
    {
        $statement->columns('count(1) as cnt');
        $row = $this->_executer->query($statement->generateSql(),$valsArr);
        return $row['cnt'];
    }
    protected function commitRelationSet($relationSets)
    {
        foreach($relationSets as $set)
        {
            $itemDao =  DaoFinderUtls::find($set->getClsName());
            $set->saveDateSet($itemDao);
        }
    }

}

class DaoImp extends DaoBase implements XDao
{

    static $allLoadStg  = null;
    private $mappingStg = null;
    public function singleTableStore($key)
    {
        return $this->view;
    }
    public function __construct($cls,$execer,$view,$mappingStg,$hashStoreFun=null)
    {
        XDBC::requireTrue(!empty($cls),'$cls is empty! ');
        XDBC::requireNotNull($mappingStg);
        $this->view=$view;
        $this->mappingStg=$mappingStg;
        if($hashStoreFun == null)
        {
            parent::__construct($cls,$execer,array($this,'singleTableStore'));
        }
        else
        {
            parent::__construct($cls,$execer,$hashStoreFun);
        }
    }


    public static function simpleDao($cls,$execer)
    {
        return new DaoImp($cls,$execer,strtolower($cls),SimpleMapping::ins(),null);
    }

    public static function simpleTableDao($cls,$table,$execer)
    {
        return new DaoImp($cls,$execer,$table,SimpleMapping::ins(),null);
    }

    public static function mutiTableDao($cls,$execer,$hashStoreFun)
    {
        return new DaoImp($cls,$execer,null,SimpleMapping::ins(),$hashStoreFun);
    }
    public function obj2row($obj)
    {

        XDBC::requireNotNull($obj);
        return $obj->getDTO($this->mappingStg)->getPropArray();
    }
    public function row2obj($cls,$row)
    {

        if(DaoBase::cls_is_a('XEntity',$cls))
        {

            if(method_exists($cls,'load'))
            {
                $obj = call_user_func(array($cls,'load'),$row,$this->mappingStg);
            }
            else
            {
                $obj = XEntity::loadEntity($cls,$row,$this->mappingStg);
            }
        }
        else if(DaoBase::cls_is_a('Relation',$cls))
        {
            if(method_exists($cls,'load'))
            {
                $obj = call_user_func(array($cls,'load'),$row,$this->mappingStg);
            }
            else
            {
                $obj = Relation::loadRelation($cls,$row,$this->mappingStg);
            }
        }
        else
        {
            XDBC::unExpect("$cls load error");
        }
        return $obj;
    }
}

class SimpleDaoFactory
{
    private $execr = null;
    public function __construct($execr)
    {
        $this->execr = $execr;
    }
    public function create($cls)
    {
        $ncls    = $this->getClassName($cls);
        $selfCls = "{$ncls}DaoImpl";
        if(XPylon::haveClass($selfCls))
        {
            return new $selfCls($ncls,$this->execr);
        }
        return DaoImp::simpleDao($ncls,$this->execr);
    }
    public function getClassName($cls)
    {
        if(XPylon::haveClass($cls))
        {
            return $cls;
        }
        $ncls =   XPylon::className($cls);
        if(!empty($ncls))
        {
            return $ncls;
        }
        XDBC::requireNotNull($ncls,"not found the $cls class!");
        return $ncls;
    }
    static public function funIns($executer)
    {
        $facotry= new SimpleDaoFactory($executer);
        return array($facotry,"create");
    }
}
/**
 *  @}
 */
