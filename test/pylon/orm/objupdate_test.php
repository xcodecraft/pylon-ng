<?php

use pylon\impl\ObjUpdater ;
class ObjUpdateTest extends ObjUpdater
{
    public $commitObjs=array();
    public function __construct()
    {
        $array=array();
        parent::__construct($array,ObjUpdater::OBJ_LOAD);
    }
    public function addObj($obj)
    {
        $obj->op="ADD";
        $this->commitObjs[]=$obj;
    }
    public function delObj($obj)
    {
        $obj->op="DEL";
        $this->commitObjs[]=$obj;
    }
    public function updateObj($obj)
    {
        $obj->op="UPDATE";
        $this->commitObjs[]=$obj;
    }
    public function commit()
    {
        $this->commitUpdate(array($this,'addObj'), array($this,'delObj'),array($this,'updateObj'));
        $this->clean();
    }
}
class A extends XProperty
{
    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        return $this->id;
    }
    public function buildSummery()
    {
        return md5(serialize($this));
    }
}

class ObjUpdateTC extends PHPUnit_Framework_TestCase
{
    public function testObjUpdate()
    {
        $ctrl = new ObjUpdateTest();
        $a= new A;
        $a->id=3;
        $b= new A;
        $b->id=1;

        $c= new A;
        $c->id=5;
        $ctrl->regAdd($a);
        $ctrl->regAdd($b);
        $ctrl->regAdd($c);
        $ctrl->commit();
        $this->assertEquals($ctrl->commitObjs[0], $a);
        $this->assertEquals($ctrl->commitObjs[1], $b);
        $this->assertEquals($ctrl->commitObjs[2], $c);

        $ctrl->commitObjs=array();

        $ctrl->regLoad($a);
        $ctrl->regLoad($b);
        $ctrl->regLoad($c);
        $ctrl->commit();
        $this->assertEquals(0,count($ctrl->commitObjs));
        $ctrl->commitObjs=array();


        $ctrl->regDel($a);
        $ctrl->regDel($b);
        $ctrl->regDel($c);
        $ctrl->commit();
        $this->assertEquals($ctrl->commitObjs[0], $a);
        $this->assertEquals($ctrl->commitObjs[1], $b);
        $this->assertEquals($ctrl->commitObjs[2], $c);
        $ctrl->commitObjs=array();


        $ctrl->regLoad($a);
        $ctrl->regLoad($b);
        $ctrl->regAdd($c);
        $ctrl->regDel($b);
        $a->x="x";
        $ctrl->commit();
        $this->assertEquals($ctrl->commitObjs[0], $c);
        $this->assertEquals($ctrl->commitObjs[1], $b);
        $this->assertEquals($ctrl->commitObjs[2], $a);


    }

}
?>
