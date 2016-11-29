<?php
namespace pylon\impl ;
use XDBC ;
use XProperty ;

class ObjectSet  extends ObjUpdater
{
    protected $clsName;
    static public function load($clsName,$items)
    {
        XDBC::requireTrue(is_string($clsName));
        XDBC::requireTrue(is_array($items));
        $obj =new ObjectSet($items,ObjUpdater::OBJ_LOAD);
        $obj->clsName=$clsName;
        return $obj;
    }

    static public function createByBiz($clsName)
    {
        XDBC::requireTrue(is_string($clsName));
        $item=array();
        $obj=new ObjectSet($item,ObjUpdater::OBJ_ADD);
        $obj->clsName=$clsName;
        return $obj;
    }
    public function getClsName()
    {
        return $this->clsName;
    }
    public function saveDateSet($dao)
    {
        $this->commitUpdate(array($dao,'add'),array($dao,'del'),array($dao,'update'));
    }
}

abstract class Relation extends XProperty   implements XIAutoUpdate
{

    public function __construct($prop=null)
    {
        parent::__construct();
        if($prop != null)
        {
            $this->merge($prop);
        }
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
