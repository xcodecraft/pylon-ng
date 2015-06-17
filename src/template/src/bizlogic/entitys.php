<?php

class Hero extends XEntity
{
    static public function bizCreate($name)
    {
        $obj = XEntity::createIns(__class__) ;
        $obj->name = $name ;
        return $obj;
    }
}
