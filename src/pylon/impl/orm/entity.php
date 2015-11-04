<?php
namespace Pylon ;
use \XProperty as XProperty ;
use \XBox      as XBox ;

/**\addtogroup Ent
 * @{
 */
/**
* @brief 自动更新接口
 */
interface XIAutoUpdate
{
    public function index();
    public function buildSummery();
}

interface XDao
{
    public function getByID($id);
    public function update($obj);
    public function add($obj);
    public function del($obj);
    public function row2obj($cls,$row);
    public function obj2row($obj);
}

/**
 * @brief  实体ID
 */
class  XID extends XProperty
{
    public function __construct($id,$ver,$cTime,$uTime)
    {
        $this->id         = $id;
        $this->ver        = $ver;
        $this->createTime = $cTime;
        $this->updateTime = $uTime;
    }
    /**
     * @brief 根据$idname产生的 ID 创建.
     *
     * @param $idname
     *
     * @return
     */
    static public function create($idname='other')
    {
        $t = date("Y-m-d H:i:s",time());
        $id= EntityUtls::createPureID($idname);
        return new XID($id,1,$t,$t);
    }
    public static function load(&$array)
    {
        $id = new XID(intval($array['id']),intval($array['ver']),$array['createtime'],$array['updatetime']);
        unset($array['id']);
        unset($array['ver']);
        unset($array['createtime']);
        unset($array['updatetime']);
        return $id;
    }
    public function upgrade()
    {
        $this->updateTime = date("Y-m-d H:i:s",time());
        $this->ver += 1;
    }
}
/**
 * @brief  实体基类，自己定义的实体必须继承它
 */
class XEntityBase extends XProperty implements XIAutoUpdate
{
    const LAZY_LOADER  = 1 ;
    const IMMED_LOADER = 2 ;
    public function __construct($xid,$prop=null)
    {
        parent::__construct();
        $this->xid=$xid;
        if($prop != null)
            $this->merge($prop);
        DBC::requireNotNull($this->xid,"entity id is null" );
    }

    /**
     * @brief  内部实现需求
     *
     * @param $mappingStg
     *
     * @return
     */
    public function getDTO($mappingStg)
    {
        $vars = $this->getPropArray();
        return  $mappingStg->convertDTO($vars);
    }
    public function getRelationSets()
    {
        return array();
    }
    public function buildSummery()
    {
        $sets    = $this->getRelationSets();
        $key     = serialize($this->getDTO(StdMapping::ins()));
        $setskey = "";
        foreach($sets as $set)
        {
            $setskey  .= $set->haveChange();
        }
        return md5($key.$setskey);
    }
    static public function unitWork($unitwork=null)
    {
        $unitwork = XBox::get("unitwork");
        if( $unitwork === null )
            throw new XDBCException("没有调用 XAppSession::begin()");
        return $unitwork ;
    }
    public function __wakeup()
    {
        self::unitWork()->regLoad($this);
    }
    static public function loadEntity($cls,$array,$mappingStg,$clsmap=array())
    {
        $xid    = XID::load($array);
        $prop   = $mappingStg->buildEntityProp($array);
        $entity = new $cls($xid,$prop);
        $obj    = self::unitWork()->regLoad($entity);
        return $obj;
    }
    static public function loadEntity2($cls,$array,$oprop,$mappingStg,$clsName=array())
    {
        $xid    = XID::load($array);
        $prop   = $mappingStg->buildEntityProp($array);
        $prop->merge($oprop);
        $entity = new $cls($xid,$prop);
        $obj    = self::unitWork()->regLoad($entity);
        return $obj;
    }
    public function signAdd()
    {
        self::unitWork()->regAdd($this);
    }
    public function signLoad()
    {
        self::unitWork()->regLoad($this);
    }
    public function del()
    {
        self::unitWork()->regDel($this);
    }

    public function index()
    {
        return get_class($this).'_'.$this->id();
    }
}



abstract class Relation extends XProperty   implements XIAutoUpdate
{

    public function __construct($prop=null)
    {
        parent::__construct();
        if($prop != null)
            $this->merge($prop);
    }
    public function id()
    {
        return $this->id;
    }
    public function getDTO($mappingStg)
    {
        $vars = $this->getPropArray();
        return  $mappingStg->convertDTO($vars);
    }
    public function getRelationSets()
    {
        return array();
    }
    public function buildSummery()
    {
        return md5(serialize($this->getDTO(StdMapping::ins())));
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
    static public function  loadRelation($cls,$array,$mappingStg)
    {
        $prop=$mappingStg->buildEntityProp($array);
        return new $cls($prop);
    }
}


class ObjUpdater
{
    protected $items            = array();
    protected $additems         = array();
    protected $delitems         = array();
    protected $loaditems        = array();
    protected $loaditemSummerys = array();
    const OBJ_LOAD = 1;
    const OBJ_ADD  = 2;
    const OBJ_DEL  = 3;
    protected function __construct(&$items,$objType)
    {
        $array =null;
        if(ObjUpdater::OBJ_LOAD===$objType)
        {
            $array = & $this->loaditems;
        }
        elseif(ObjUpdater::OBJ_ADD===$objType)
        {
            $array= &$this->addItems;
        }
        elseif(ObjUpdater::OBJ_DEL===$objType)
        {
            $array= &$this->delitems;
        }
        else
        {
            DBC::unExpect($objType,"ObjUpdater not support this type ");
        }
        foreach($items as $item)
        {
            $array[$item->index()] = $item;
            $this->items[$item->index()]=$item;
            if(ObjUpdater::OBJ_LOAD===$objType)
                $this->loaditemSummerys[$item->index()]=$item->buildSummery();
        }

    }
    public function cleanItem(&$items)
    {
        $cnt = count($items);
        for($i=0 ; $i<$cnt; $i++)
            $items[$i] = null;
    }
    public function __destruct()
    {
        $this->clean();

    }
    public function haveChange()
    {
        if(count($this->additems)>0)  return true;
        if(count($this->delitems)>0)  return true;
        foreach($this->loaditems as $key=>$item)
        {
            if($item->buildSummery() != $this->loaditemSummerys[$key])
                return true;
        }
        return false;
    }
    protected function commitUpdate($addfun,$delfun,$updatefun)
    {
        foreach($this->additems as $item)
        {
            call_user_func($addfun,$item);
        }
        foreach($this->delitems as $item)
        {
            call_user_func($delfun,$item);
        }
        foreach($this->loaditems as $key=>$item)
        {
            if($item->buildSummery() != $this->loaditemSummerys[$key])
            {
                call_user_func($updatefun,$item);
            }
        }
    }
    public function regLoad($obj)
    {
        DBC::requireNotNull($obj);
        //消除副本的影响！
        if(isset($this->items[$obj->index()]))
        {
            //反回第一个对象。
            return  $this->items[$obj->index()];
        }
        $this->items[$obj->index()]=$obj;
        $this->loaditems[$obj->index()]=$obj;

        $this->loaditemSummerys[$obj->index()]=$obj->buildSummery();
        return $obj;
    }
    public function regAdd($obj)
    {

        DBC::requireNotNull($obj);
        //消除副本的影响！
        if(isset($this->items[$obj->index()]))
        {
            DBC::requireNotNull($this->items[$obj->index()]);
            //反回第一个对象。
            return  $this->items[$obj->index()];
        }
        $this->additems[$obj->index()]=$obj;
        $this->items[$obj->index()]=$obj;
        return $obj;
    }
    public function regDel($obj)
    {
        $index = $obj->index();

        if(isset($this->additems[$index]))
        {
            unset($this->additems[$index]);
        }
        else
        {
            $this->delitems[$index]=$obj;
            unset($this->loaditems[$index]);
        }
        unset($this->items[$index]);
    }
    public function clean()
    {

        $this->cleanItem($this->items);
        $this->items= array();
        $this->cleanItem($this->additems);
        $this->additems= array();
        $this->cleanItem($this->delitems);
        $this->delitems= array();
        $this->cleanItem($this->loaditems);
        $this->loaditems= array();
        $this->cleanItem($this->loaditemSummerys);
        $this->loaditemSummerys=array();

    }
    public function get($index)
    {
        DBC::requireTrue(isset($this->items[$index]),"not found index[$index] obj!");
        return $this->items[$index];
    }
    public function getByObj($indexObj)
    {
        return $this->get($indexObj->index());
    }
    public function &items()
    {
        return $this->items;
    }
    public function  objcomp($a,$b)
    {
        if($a==$b) return 0;
        return $a->id() > $b->id() ? 1 :-1;
    }
    public function equal($other)
    {
        DBC::requireNotNull($other);
        $cur=$this->items();
        $oth=$other->items();
        $diff = array_udiff_assoc($cur,$oth,array($this,'objcomp'));
        return count($diff)==0;
    }
    public function regAll2Del()
    {
        foreach($this->items as $i)
        {
            $this->regDel($i);
        }
    }

}

/**
 * @brief  实体映射策略
 */
interface IMappingStg
{
    public function convertDTO($vars);
    public function buildEntityProp(&$array,$argsmap=array());
}
class SimpleMapping implements IMappingStg
{
    static private $ins=null;
    static public function ins()
    {
        if(self::$ins == null)
            self::$ins = new SimpleMapping();
        return self::$ins;
    }
    public function convertDTO($vars)
    {
        DBC::requireNotNull($vars,'$vars');
        $subdtos = array();
        $dtovars = array();
        foreach($vars as $key=>$val)
        {
            if(is_object($val) && $val instanceof  NullEntity)
            {
                $dtovars[$key."__id"]= null;
            }
            elseif(is_object($val) && $val instanceof  XEntity)
            {
                $dtovars[$key."__id"]= $val->id();
            }
            else if(is_object($val) && $val instanceof  XID)
            {
                $subdtos[] = XProperty::fromArray($val->getPropArray());
            }
            else if (is_object($val) && $val instanceof LDProxy)
            {
                $dtovars[$key."__id"]= $val->id();
            }
            else if (is_object($val) && $val instanceof ObjectSet)
            {
            }
            else {
                $dtovars[$key]=$val;
            }
        }
        $maindto = XProperty::fromArray($dtovars);
        foreach($subdtos as $sdto)
        {
            $maindto->merge($sdto) ;
        }
        return $maindto;
    }

    public function buildEntityProp(&$array,$argsmap=array())
    {
        foreach ( $array as $col=>$val)
        {
            if(isset($argsmap[$col]))
            {
                $propName         = $argsmap[$col];
                $array[$propName] = $array[$col];
                unset($array[$col]);

            }
            elseif( strpos($col,'__id') != false)
            {
                $prop = XProperty::fromArray();
                $key= str_replace('__id','',$col);
                if(isset($array[$col]) && $array[$col]!=null)
                {
                    $prop->id= $array[$col];
                    //
                    
                    $prop->cls=XEntEnv::fullClassName($key) ;
                    if(XSetting::$entLazyload)
                    {
                        $obj  =new LDProxy(array("EntityUtls","loadObjByID"),$prop);
                    }
                    else
                    {
                        $obj   = EntityUtls::loadObjByID($prop);
                    }
                    $array[$key]  = $obj;
                    unset($array[$col]);
                }
                else
                {
                    $array[$key]= new NullEntity($key);
                }
            }

        }
        $prop = XProperty::fromArray($array);
        return $prop;
    }
}
class StdMapping implements IMappingStg
{
    static private $ins=null;
    static public function ins()
    {
        if(self::$ins == null)
            self::$ins = new StdMapping();
        return self::$ins;
    }
    public function convertDTO($vars)
    {
        $subdtos = array();
        $dtovars = array();
        foreach($vars as $key=>$val)
        {
            if(is_object($val) && $val instanceof  NullEntity)
            {
                $cls = XEntEnv::shortClassName($val->getClass()) ;
                $dtovars[$key."__".strtolower($cls)]=  null;
            }
            elseif(is_object($val) && $val instanceof  XEntity)
            {
                $cls = XEntEnv::shortClassName(get_class($val)) ;
                $dtovars[$key."__".strtolower($cls)]= $val->id();
            }
            elseif(is_object($val) && $val instanceof  XID)
            {
                $subdtos[] = XProperty::fromArray($val->getPropArray());
            }
            elseif (is_object($val) && $val instanceof LDProxy)
            {
                $cls = XEntEnv::shortClassName(get_class($val->getObj())) ;
                $dtovars[$key."__".strtolower($cls)]= $val->id();
            }
            elseif (is_object($val) && $val instanceof ObjectSet)
            {
            }
            else {
                $dtovars[$key]=$val;
            }
        }
        $maindto = XProperty::fromArray($dtovars);

        foreach($subdtos as $sdto)
        {
            $maindto->merge($sdto) ;
        }
        return $maindto;
    }
    public function buildEntityProp(&$array,$argsmap=array())
    {
        foreach ( $array as $col=>$val)
        {
            if(isset($argsmap[$col]))
            {
                $propName= $argsmap[$col];
                $array[$propName]=$array[$col];
                unset($array[$col]);

            }
            elseif( strpos($col,'__') != false)
            {
                $prop = XProperty::fromArray();
                list($key,$cls) = explode('__', $col);
                if(isset($array[$col]) && $array[$col]!=null)
                {
                    $prop->id= $array[$col];
                    $prop->cls=$cls;

                    $ctrl = PylonCtrl::objLazyLoad();
                    if($ctrl->_need($key,null))
                    {
                        $obj  =new LDProxy(array("EntityUtls","loadObjByID"),$prop);
                    }
                    else
                    {
                        $obj   = EntityUtls::loadObjByID($prop);
                    }
                    $array[$key]=$obj;
                    unset($array[$col]);
                }
            }
            else
            {
                $array[$key]=new NullEntity($key);
            }

        }
        $prop = XProperty::fromArray($array);
        return $prop;
    }
}
class EntityUtls
{
    static public function loadObjByID($prop)
    {
        return DaoFinderUtls::find($prop->cls)->getByID($prop->id);
    }

    static public function loadRelation($cls,$array,$loadstg,$clsmap=array(),$argsmap=array())
    {

        $reflectionObj = new ReflectionClass($cls);
        $constructFun = $reflectionObj->getConstructor();
        $args = $constructFun->getParameters();
        $constrctArgs=array();
        foreach ( $args as $arg)
        {
            $key=strtolower($arg->getName());
            $col=$key;
            if(isset($argsmap[$key]))
                $col=$argsmap[$key];
            if(isset($array[$col]))
            {
                $constrctArgs[$key]=$array[$col] ;
            }
            elseif( isset($array[$col."__id"] ))
            {
                $prop = XProperty::fromArray();

                $prop->id  = $array[$col."__id"];
                $prop->cls = $clsmap[$col];
                $obj = new LDProxy(array("EntityUtls","loadObjByID"),$prop);
                if($loadstg == XEntity::LAZY_LOADER)
                    $constrctArgs[$key]=$obj;
                else
                    $constrctArgs[$key]=$obj->getObj();
            }
            else
            {
                $msg = Prompt::recommend($key,array_keys($array));
                $msg = JoinUtls::jarray(',',$msg);
                DBC::unExpect("$key not unexpect!  col is $col,<br>\n key mabey is [ $msg ] <br>\n");
            }
        }
        $obj= $reflectionObj->newInstanceArgs($constrctArgs);
        return $obj;
    }

    static public function assembly($unitwork)
    {
        DBC::requireNotNull($unitwork);
        XEntity::unitWork($unitwork);
    }

    static public function createPureID($idname='other')
    {
        $idSvc = XBox::must_get('IDGenterService');
        $id= $idSvc->createID($idname);
        return $id;
    }
}


class DaoFinderUtls
{
    const factory='##factory';

    private static $binder=null;

    static public function regBinder($binder)
    {
        self::$binder = $binder;
    }
    static public function clearBinder()
    {
        self::regBinder(null);
    }

    static protected function findByCls($clsName)
    {
        return self::get_impl(XBox::DAO,$clsName);
    }

    static public function getExecuterList()
    {
        return array_values(XBox::space_objs(XBox::SQLE));
    }

    static public function registerFactory($daoFactory,$queryFactory)
    {
        XBox::regist(self::factory,$daoFactory, __METHOD__,"/".XBox::DAO);
        XBox::regist(self::factory,$queryFactory, __METHOD__,"/".XBox::QUERY);
    }

    static public function get_impl($key,$cls)
    {
        $cls  =  strtolower($cls);
        $obj  = XBox::get($key,"/$cls");
        if($obj !== null) return $obj ;

        $factory = XBox::get(self::factory,"/$key");
        if($factory !== null)
        {
            $obj = call_user_func($factory,$cls);
            self::regist_impl($key,$cls,$obj);
            return $obj;
        }


        $names = Prompt::recommend($cls,array_keys(XBox::space_keys($key)));
        $str   = JoinUtls::jarray(',',$names);
        DBC::unExpect("$cls $key unfoud","maybe data env not init!");
    }

    static public function query($clsName)
    {
        $query = self::get_impl(XBox::QUERY,$clsName);
        if(self::$binder!= null)
        {
            return self::$binder->proxy($clsName,$query);
        }
        return $query;
    }

    static public function find_($obj)
    {
        $dao=null;
        if(is_object($obj))
            $obj = get_class($obj);
        $dao = self::findByCls($obj);

        return $dao;
    }

    static public function find($obj)
    {
        $dao = self::find_($obj);
        if(self::$binder!= null)
        {
            return self::$binder->proxy(
                is_string($obj)? $obj: get_class($obj),
                $dao);
        }
        return $dao;
    }


    static private function registerExer($cls ,$exec)
    {
        $root_exec = XBox::get(XBox::SQLE);
        if ( $exec !== $root_exec)
            XBox::regist(XBox::SQLE,$exec,__METHOD__ . ":$cls");
    }
    static public function register($dao)
    {

        $clsName = strtolower($dao->cls);
        self::regist_impl(XBox::DAO,$clsName,$dao);
    }

    static public function regist_impl($key,$cls,$obj)
    {
        self::registerExer($cls,$obj->getExecuter());
        XBox::regist($key,$obj,__METHOD__,"/$cls");
    }
    static public function registerQuery($query)
    {
        $clsName = strtolower($query->getRegName());
        self::regist_impl(XBox::QUERY,$clsName,$query);
    }

    static public function registerAll($dao,$query)
    {
        if($dao !=null)
            self::register($dao);
        if($query !=null)
            self::registerQuery($query);
    }
    static public function clean()
    {
        XBox::clean(XBox::DAO);
        XBox::clean(XBox::QUERY);
        XBox::clean(self::factory);
    }

}


/**
 *  @}
 */
