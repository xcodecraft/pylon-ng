<?php

namespace pylon\impl ;
use XProperty ;
use XBox ;
use XDBC ;
use XEntity ;
use XSetting ;
use XDBCException ;

class ObjUpdater
{
    protected $items            = array();
    protected $additems         = array();
    protected $delitems         = array();
    protected $loaditems        = array();
    protected $loaditemSummerys = array();
    const OBJ_LOAD=1;
    const OBJ_ADD=2;
    const OBJ_DEL=3;
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
            XDBC::unExpect($objType,"ObjUpdater not support this type ");
        }
        foreach($items as $item)
        {
            $array[$item->index()] = $item;
            $this->items[$item->index()]=$item;
            if(ObjUpdater::OBJ_LOAD===$objType)
            {
                $this->loaditemSummerys[$item->index()]=$item->buildSummery();
            }
        }

    }
    public function cleanItem(&$items)
    {
        $cnt = count($items);
        for($i=0 ; $i<$cnt; $i++)
        {
            $items[$i] = null;
        }
    }
    public function __destruct()
    {
        $this->clean();

    }
    public function haveChange()
    {
        if(count($this->additems)>0  ||  count($this->delitems)>0)
        {
            return true;
        }
        foreach($this->loaditems as $key=>$item)
        {
            if($item->buildSummery() != $this->loaditemSummerys[$key])
            {
                return true;
            }
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
        XDBC::requireNotNull($obj);
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

        XDBC::requireNotNull($obj);
        //消除副本的影响！
        if(isset($this->items[$obj->index()]))
        {
            XDBC::requireNotNull($this->items[$obj->index()]);
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
        XDBC::requireTrue(isset($this->items[$index]),"not found index[$index] obj!");
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
        if($a==$b)
        {
            return 0;
        }
        return $a->id() > $b->id() ? 1 :-1;
    }
    public function equal($other)
    {
        XDBC::requireNotNull($other);
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

