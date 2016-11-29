<?php

namespace pylon\impl ;
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

interface IMappingStg
{
    public function convertDTO($vars);
    public function buildEntityProp(&$array);
}

interface XUnitWork
{
    public function commit();
    public function regLoad($obj);
    public function regAdd($obj);
    public function regDel($obj);
}
