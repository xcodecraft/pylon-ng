<?php
namespace pylon\impl ;
use XDBC ;

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
