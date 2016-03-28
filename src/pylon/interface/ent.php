<?php
/**
 * @brief  实体父类，实体需要继承
 *
 *  class Aplle extends XEntity
 */
class XEntity extends XEntityBase
{
    public function upgrade()
    {
        $this->xid->upgrade();
    }

    /**
     * @brief  获得对象id
     *
     * @return
     */
    public function id()
    {
        return $this->xid->id;
    }

    /**
     * @brief 获得实体的版本号
     *
     * @return
     */
    public function ver()
    {
        return $this->xid->ver;
    }

    /**
     * @brief  hash store need,override  this fun in subclass;
     *
     * @return  string key; default is null;
     */
    public function hashStoreKey()
    {
        return null;
    }

    /**
     * @brief
     *
     * @return
     */
    public function createTime()
    {
        return $this->xid->createTime;
    }
    /**
     * @brief
     *
     * @return
     */
    public function updateTime()
    {
        return $this->xid->updateTime;
    }


    /**
     * @brief  通过此方法，将实体注册
     *
     * @param $entity
     *
     * @return
     */
    static public function regist($entity)
    {
        DBC::requireNotNull($entity);
        $obj = static::unitWork()->regAdd($entity);
        return $obj;
    }

    static public function createIns($cls)
    {
        $obj = new $cls(XID::create(strtolower($cls))) ;
        static::regist($obj) ;
        return $obj;
    }
}

/**
 * @brief
 */
class XQueryObj
{
    const OP_LIST   = 1;
    const OP_GET    = 2;
    const OP_ADD    = 3;
    const OP_DEL    = 4;
    const OP_UPDATE = 5;
    private $loadStg=null;
    static public function ins()
    {
        static $inst=null;
        if($inst == null)
        {
            $inst = new  XQueryObj() ;
        }
        return $inst;
    }
    public function setLoadStg($stg)
    {
        $this->loadStg = $stg;
    }
    private function getDao($cls)
    {
        $dao = DaoFinderUtls::find($cls);
        if($this->loadStg != null)
        {
            $dao->updateLoadStg($this->loadStg);
        }
        return $dao;
    }
    private function listCall($op,$cls,$name,$paramNames,$params)
    {
        $extraParams=null;
        $prop=DynCallParser::buildCondProp($paramNames,$params,$extraParams);
        if(count($extraParams) == 0 )
        {
                return  $this->getDao($cls)->listByProp($prop);
        }
        else
        {
            $page      = isset($extraParams[0])? $extraParams[0] : null;
            $orderkey  = isset($extraParams[1])? $extraParams[1] : null;
            $ordertype = isset($extraParams[2])? $extraParams[2] : 'DESC';
            return  $this->getDao($cls)->listByProp($prop,$page,$orderkey,$ordertype);
        }
    }

    public function listByProp($cls,$prop,$page=null,$orderkey=null,$ordertype='DESC')
    {
        return  $this->getDao($cls)->listByProp($prop,$page,$orderkey,$ordertype);
    }
    public function cntByProp($cls,$prop)
    {
        return  $this->getDao($cls)->cntByProp($prop);
    }

    public function listByArr($cls,$Arr,$page=null,$orderkey=null,$ordertype='DESC')
    {
        $prop = XProperty::fromArray($arr);
        return  $this->getDao($cls)->listByProp($prop,$page,$orderkey,$ordertype);
    }
    public function getByProp($cls,$prop)
    {
        return  $this->getDao($cls)->getByProp($prop);
    }


    private function getCall($op,$cls,$name,$paramNames,$params)
    {
        $extraParams=null;
        $prop=DynCallParser::buildCondProp($paramNames,$params,$extraParams);
        return  $this->getDao($cls)->getByProp($prop);
    }

    private function cntCall($op,$cls,$name,$paramNames,$params)
    {
        $extraParams=null;
        $prop=DynCallParser::buildCondProp($paramNames,$params,$extraParams);
        return  $this->getDao($cls)->getCount($prop);
    }
    private function delCall($op,$cls,$name,$paramNames,$params)
    {

        $extraParams=null;
        $prop=DynCallParser::buildCondProp($paramNames,$params,$extraParams);
        return  $this->getDao($cls)->delByProp($prop);
    }
    public function callImpl($op,$cls,$name,$paramNames,$params)
    {

        $dc = DiagnoseContext::create("XQueryObj::callImpl");
        $dc->log("call name : $name ");
        $ret = null;
        switch($op)
        {
        case 'list':
            $ret = $this->listCall($op,$cls,$name,$paramNames,$params);
            break;
        case 'get':
            $ret = $this->getCall($op,$cls,$name,$paramNames,$params);
            break;
        case 'cnt':
            $ret = $this->cntCall($op,$cls,$name,$paramNames,$params);
        case 'del':
            $ret = $this->delCall($op,$cls,$name,$paramNames,$params);
            break;
        default:
            DBC::unExpect($op,"unsupport op type");
        }
        $dc->notkeep();
        return $ret ;
    }

    /**
     * @brief  动态函数
     *
     * @code
     * XQueryObj::ins()->get_Author_by_id($author->id());
     * XQueryObj::ins()->list_Author();
     * XQueryObj::ins()->cnt_Author();
     * @endcode
     */
    public function __call($name,$params)
    {
        extract( DynCallParser::condObjParse($name));
        return $this->callImpl($op,$cls,$name,$condnames,$params);
    }
}

/**
 * @ingroup entity
 * @brief  直接查询器;
 */
class XQueryArr
{
    static public function ins()
    {
        static $inst=null;
        if($inst == null)
            $inst = new XQueryArr();
        return $inst;
    }
    /**
     * @brief   动态函数
     * @code
     * XQueryArr::ins()->get_Author_by_id($author->id());
     * XQueryArr::ins()->list_Author();
     * XQueryArr::ins()->cnt_Author();
     * XQuery::obj()->get_Author_by_id($author->id())
     * XQuery::arr()->get_Author_by_id($author->id())
     * @endcode
     *
     */
    public function __call($name,$params)
    {
        extract( DynCallParser::condObjParse($name));
        return $this->callImpl($op,strtolower($cls),$name,$condnames,$params);
    }

    public function callImpl($op,$table,$name,$paramNames,$params)
    {
        $dc = DiagnoseContext::create("XQueryArr::callImpl");
        $dc->log("call name : $name ");
        $ret = null;
        switch($op)
        {
        case 'list':
            $ret = $this->listCall($table,$paramNames,$params);
            break;
        case 'get':
            $ret = $this->getCall($table,$paramNames,$params);
            break;
        default:
            DBC::unExpect($op,"$op unsupport! ");
        }
        $dc->notkeep();
        return $ret ;
    }

    private function getStyle($params)
    {
        $style = ApiStyle::RUBY;
        if(isset($params[0]) && is_array($params[0])) {
            $style = ApiStyle::MONGO;
        }
        return $style;
    }

    private function listCall($table,$paramNames,$params)
    {
        //        $table = DaoFinder::find($table)->getStoreTable();
        $style = $this->getStyle($params);
        if($style == ApiStyle::MONGO) {
            $where = isset($params[0])? $params[0] : null;
            $order = isset($params[1])? $params[1] : null;
            $page  = isset($params[2])? $params[2] : null;
            $prop = XProperty::fromArray($where);
        } else {
            $prop = DynCallParser::buildCondProp($paramNames,$params,$extraParams);
            $page      = isset($extraParams[0])? $extraParams[0] : null;
            $ordertype = isset($extraParams[2])? $extraParams[2] : 'DESC';
            if(isset($extraParams[1])) {
                $orderkey  = $extraParams[1];
                $order = array($orderkey => $ordertype);
            }
        }
        return DaoFinderUtls::query("{$table}Query")->listByPropExt($table,null,"*",$prop,$page,$order);
    }

    public function listByProp($cls,$prop,$page=null,$orderkey=null,$ordertype='DESC')
    {
        return DaoFinderUtls::query("{$cls}Query")->listByPropExt($cls,null,"*",$prop,$page,$order);
    }
    public function cntByProp($cls,$prop)
    {
        return DaoFinderUtls::query("{$cls}Query")->cntByProp($cls,null,$prop);
    }
    public function listByArr($cls,$arr,$page=null,$orderkey=null,$ordertype='DESC')
    {
        $prop = XProperty::fromArray($arr) ;
        return DaoFinderUtls::query("{$cls}Query")->listByPropExt($cls,null,"*",$prop,$page,$order);
    }
    private function getCall($table,$paramNames,$params)
    {
        $style = $this->getStyle($params);
        if($style == ApiStyle::RUBY) {
            $prop = DynCallParser::buildCondProp($paramNames,$params,$extraParams);
        } else {
            $where = isset($params[0])? $params[0] : null;
            $order = isset($params[1])? $params[1] : null;
            $prop = FilterProp::create($where);
        }
        return  DaoFinderUtls::query("{$table}Query")->getByProp($prop,$table,"","*","",$order);
    }

    public static function __callStatic($name,$params)
    {
        $ins = static::ins();
        return call_user_func_array(array($ins,$name), $params);
    }
}

/**
 * @brief
 * XQuery::obj()->get_Author_by_id($author->id()) ;
 * XQuery::arr()->get_Author_by_id($author->id()) ;
 */
class XQuery
{
    /**
     * @brief
     * XQuery::obj()->get_Author_by_id($author->id()) ;
     *
     * @return 实体对象
     */
    static public function obj()
    {
        return  XQueryObj::ins();
    }
    /**
     * @brief
     *
     * @return
     */
    static public function arr()
    {
        return  XQueryArr::ins() ;
    }

    // 添加 sql() 函数
    static public function sql()
    {
        return  XBox::get(XBOx::SQLE);
    }

}
/**
 * @ingroup entity
 * @brief  直接数据写入
 */
class XWriter
{

    static public function ins()
    {
        static $inst=null;
        if($inst == null)
        {
            $inst = new XWriter();
        }
        return $inst;
    }
    private function getStyle($params)
    {
        $style = ApiStyle::RUBY;
        if(isset($params[0]) && is_array($params[0])) {
            $style = ApiStyle::MONGO;
        }
        return $style;
    }

    protected function delCall($name,$params)
    {
        extract(DynCallParser::condObjParse($name));
        $style = $this->getStyle($params);
        if($style == ApiStyle::RUBY) {
            $prop = DynCallParser::buildCondProp($condnames,$params,$extraParams);
        } else {
            $prop = FilterProp::create($params[0]);
        }
        return DaoFinderUtls::find($cls)->delByProp($prop);
    }
    protected function updateCall($name,$params)
    {
        extract(DynCallParser::condUpdateObjParse($name));
        $style = $this->getStyle($params);
        if($style == ApiStyle::RUBY) {
            extract(DynCallParser::buildUpdateArray($updatenames,$condnames,$params));
        } else {
            $updateArray = $params[0];
            $condArray   = is_array($params[1]) ? $params[1] : array();
        }
        return DaoFinderUtls::find($cls)->updateByArray($updateArray,$condArray,null);
    }
    public function __call($name,$params)
    {
        $ret = null ;
        switch(substr($name,0,3))
        {
        case 'del':
            $ret = $this->delCall($name,$params);
            break;
        case 'upd':
            $ret = $this->updateCall($name,$params);
            break;
        default:
            DBC::unExpect($op,"unsupport op type");
        }
        return $ret ;

    }
    public static function __callStatic($name,$params)
    {
        $ins = static::ins();
        return call_user_func_array(array($ins,$name), $params);
    }
}

/**
 * @ingroup entity
 * @brief  Dao 注册工具
 */
class XEntEnv
{

    /**
     * @brief  在一般情况下，不需要编写Dao和Query的特别实现,可由Facotry来产生
     *
     * @param $daoFactory
     * @param $queryFactory
     *
     * @return
     */
    static public function registerFactory($daoFactory,$queryFactory)
    {
        DaoFinderUtls::registerFactory($daoFactory,$queryFactory);
    }


    // static public function register($dao)
    // {
    //     DaoFinderUtls::register($dao);
    // }
    static public function registerDao($dao)
    {
        DaoFinderUtls::register($dao);
    }
    static public function registerDaos()
    {
        $daos = func_get_args();
        foreach($daos as $dao)
        {
            DaoFinderUtls::register($dao);
        }
    }

    static public function registerQuerys()
    {
        $querys = func_get_args();
        foreach($querys as $query)
        {
            DaoFinderUtls::registerQuery($query);
        }
    }
    static public function registerQuery($query)
    {
        DaoFinderUtls::registerQuery($query);
    }

    static public function clean()
    {

        DaoFinderUtls::clean();
    }

    /**
     * @brief  simple setup for signle mysql ;
     *
     * @param $sql_exec  数据库执行器 ;
     * @param $idgenter  ID生成器 ;
     *
     * @return
     */
    static public function simpleSetup($sql_exec = null,$idgenter=null)
    {
        if ($sql_exec !== null)
        {
            XBox::regist(XBox::SQLE,$sql_exec,__METHOD__);
            if(empty($idgenter))
            {
                XBox::regist(XBox::IDG, new MySqlIDGenerator($sql_exec),__METHOD__);
            }
            else
            {
                XBox::regist(XBox::IDG, $idgenter,__METHOD__);
            }
        }

        $executer = XBox::must_get(XBox::SQLE);
        static::registerFactory( SimpleDaoFactory::funIns($executer), SimpleQueryFactory::funIns($executer));
    }
    /**
        * @brief  配置Dao
        *
        * @param $cls  类名
        * @param $table  表名
        * @param $mapping  映射方式: simple,std
        * @param $hashfun  分表方式
        *
        * @return
     */
    static public function configDao($cls,$table,$mapping="simple",$hashfun=null)
    {
        $executer = XBox::get(SQLE,"/$cls");
        $map_ins  = SimpleMapping::ins();
        if ($mapping === "std")
        {
            $map_ins =  StdMapping::ins();
        }
        $dao   = new DaoImp($cls,$executer,$table,$map_ins,$hashfun);
        static::registerDao($dao);
    }

    static public function query($clsName)
    {
        return DaoFinderUtls::query($clsName);
    }
    static public function dao($cls)
    {
        return DaoFinderUtls::find($cls);
    }


}








/**
 * @ingroup entity
 * @brief  应用Session
 */
class XAppSession
{
    private     $isCancle=true;
    protected   $unitWork=null;
    protected   $useDaos=array();
    static public function begin($uwork=null)
    {
        $where_begin = XBox::regist_where(1);
        if($uwork === null)
        {
            return  new XAppSession(new UnitWorkImpl(),$where_begin);
        }
        return new XAppSession($uwork,$where_begin);
    }

    protected function __construct($unitWork,$where_begin)
    {
        $this->unitWork = $unitWork;

        XBox::regist("unitwork",$unitWork,$where_begin );
    }

    public function __destruct()
    {
        if($this->isCancle )
        {
            XBox::clean("unitwork");
            $this->unitWork = null;
        }
    }
    public function commit()
    {
        $this->unitWork->commit();

        $this->isCancle=true;
    }
}

