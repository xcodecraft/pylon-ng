<?php
namespace pylon\impl ;
use XProperty ;
use XBox ;
use XDBC ;
use XEntity ;
use XSetting ;
use XDBCException ;

/**\addtogroup Ent
 * @{
 */
/**
* @brief 自动更新接口
 */

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
        {
            $this->merge($prop);
        }
        XDBC::requireNotNull($this->xid,"entity id is null" );
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
        {
            throw new XDBCException("没有调用 XAppSession::begin()");
        }
        return $unitwork ;
    }
    public function __wakeup()
    {
        static::unitWork()->regLoad($this);
    }
    static public function loadEntity($cls,$array,$mappingStg,$clsmap=array())
    {
        $xid    = XID::load($array);
        $prop   = $mappingStg->buildEntityProp($array);
        $entity = new $cls($xid,$prop);
        return  static::unitWork()->regLoad($entity);
    }
    static public function loadEntity2($cls,$array,$oprop,$mappingStg,$clsName=array())
    {
        $xid    = XID::load($array);
        $prop   = $mappingStg->buildEntityProp($array);
        $prop->merge($oprop);
        $entity = new $cls($xid,$prop);
        return  static::unitWork()->regLoad($entity);
    }
    public function signAdd()
    {
        static::unitWork()->regAdd($this);
    }
    public function signLoad()
    {
        static::unitWork()->regLoad($this);
    }
    public function del()
    {
        static::unitWork()->regDel($this);
    }

    public function index()
    {
        return get_class($this).'_'.$this->id();
    }
}





/**
 * @brief  实体映射策略
 */
class MappingUtls
{
    static public function assembleDTO($dtovars,$subdtos)
    {
        $maindto = XProperty::fromArray($dtovars);
        foreach($subdtos as $sdto)
        {
            $maindto->merge($sdto) ;
        }
        return $maindto;
    }
}
class SimpleMapping implements IMappingStg
{
    static private $ins=null;
    static public function ins()
    {
        if(static::$ins == null)
        {
            static::$ins = new SimpleMapping();
        }
        return static::$ins;
    }
    public function convertDTO($vars)
    {
        XDBC::requireNotNull($vars,'$vars');
        $subdtos = array();
        $dtovars = array();
        foreach($vars as $key=>$val)
        {
            if(is_object($val) && $val instanceof  NullEntity)
            {
                $dtovars[$key."__id"]= null;
            }
            elseif(is_object($val) && ( $val instanceof  XEntity  || $val instanceof LDProxy))
            {
                $dtovars[$key."__id"]= $val->id();
            }
            else if(is_object($val) && $val instanceof  XID)
            {
                $subdtos[] = XProperty::fromArray($val->getPropArray());
            }
            else if (is_object($val) && $val instanceof ObjectSet)
            {
                throw new XDBCException("not support ObjectSet");
            }
            else {
                $dtovars[$key]=$val;
            }
        }
        return MappingUtls::assembleDTO($dtovars,$subdtos) ;
    }

    public function buildEntityProp(&$array)
    {
        foreach ( $array as $col=>$val)
        {
            if( strpos($col,'__id') != false)
            {
                $prop = XProperty::fromArray();
                $key= str_replace('__id','',$col);
                if(isset($array[$col]) && $array[$col]!=null)
                {
                    $prop->id= $array[$col];
                    $prop->cls=$key;
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
        return  XProperty::fromArray($array);
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
        $constructFun  = $reflectionObj->getConstructor();
        $args          = $constructFun->getParameters();
        $constrctArgs  = array();
        foreach ( $args as $arg)
        {
            $key=strtolower($arg->getName());
            $col=$key;
            if(isset($argsmap[$key]))
            {
                $col=$argsmap[$key];
            }
            if(isset($array[$col]))
            {
                $constrctArgs[$key]=$array[$col] ;
            }
            elseif( isset($array[$col."__id"] ))
            {
                $prop = XProperty::fromArray();

                $prop->id  = $array[$col."__id"];
                $prop->cls = $clsmap[$col];
                $obj = new LDProxy(array(EntityUtls,"loadObjByID"),$prop);
                if($loadstg == XEntity::LAZY_LOADER)
                {
                    $constrctArgs[$key]=$obj;
                }
                else
                {
                    $constrctArgs[$key]=$obj->getObj();
                }
            }
            else
            {
                $msg = Prompt::recommend($key,array_keys($array));
                $msg = JoinUtls::jarray(',',$msg);
                XDBC::unExpect("$key not unexpect!  col is $col,<br>\n key mabey is [ $msg ] <br>\n");
            }
        }
        return  $reflectionObj->newInstanceArgs($constrctArgs) ;
    }

    static public function assembly($unitwork)
    {
        XDBC::requireNotNull($unitwork);
        XEntity::unitWork($unitwork);
    }

    static public function createPureID($idname='other')
    {
        $idSvc = XBox::must_get('IDGenterService');
        return  $idSvc->createID($idname);
    }
}




/**
 *  @}
 */
