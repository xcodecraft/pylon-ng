<?php
namespace pylon\impl ;
use XProperty ;
use XBox ;
use XDBC ;
use XEntity ;
use XSetting ;
use XDBCException ;
class StdMapping implements IMappingStg
{
    static private $ins=null;
    static public function ins()
    {
        if(static::$ins == null)
        {
            static::$ins = new StdMapping();
        }
        return static::$ins;
    }
    public function convertDTO($vars)
    {
        $subdtos = array();
        $dtovars = array();
        foreach($vars as $key=>$val)
        {
            if( is_object($val))
            {
                if( $val instanceof  NullEntity)
                {
                    $dtovars[$key."__".strtolower($val->getClass())]=  null;
                }
                elseif( $val instanceof  XEntity)
                {
                    $dtovars[$key."__".strtolower(get_class($val))]= $val->id();
                }
                elseif( $val instanceof  XID)
                {
                    $subdtos[] = XProperty::fromArray($val->getPropArray());
                }
                elseif ( $val instanceof LDProxy)
                {
                    $dtovars[$key."__".strtolower(get_class($val->getObj()))]= $val->id();
                }
                else
                {
                    $cls = get_class($varl) ;
                    XDBC::unExpect("unsupport $cls obj to DTO") ;
                }
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
            if( strpos($col,'__') !== false)
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
                        $obj  =new LDProxy(array(EntityUtls,"loadObjByID"),$prop);
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
        return  XProperty::fromArray($array);
    }
}
