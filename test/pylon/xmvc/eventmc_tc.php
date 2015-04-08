<?php
require_once("simpletest/unit_tester.php");
require_once("simpletest/reporter.php");
class TestStoreSpace implements EMStore
{
    private $data=null;
    public function __construct()
    {
        $this->data=array();
    }
    public function get($name)
    {
        return $this->data[$name];
    }
    public function save($name,$val)
    {
        $this->data[$name]=$val;
    }
}

class TestRequest2 extends PropertyObj implements IRequest
{
    
    public function __construct($vars)
    {
        $this->getVars = $vars;
    }
    public function getVars()
    {
        return  $this->getVars;
    }
}


class BaseAction2
{
    public $eventMC =null;
    public $varsLife =null;
    public function __construct()
    {
        $storeSpace =  new TestStoreSpace();
        $this->eventMC = new EventMachine2($storeSpace);
        $this->varsLife= new LifeScope($storeSpace);
    }

    public function execute($request,$spaceName)
    {
        $this->varsLife->swapAutoStoreSpace($spaceName);
        $this->eventMC->dispach($spaceName ,$this->getEventDef(),$this->varsLife,$this,$request,new XProperty());

        
    }
}


class TestAction2 extends BaseAction2
{
    public $callFun="";
    public $vars=null;
    public function __construct()
    {
        parent::__construct();
        $this->varsLife->signGlobalVars('x');
        $this->varsLife->signSelfDefVars('test','b,c');
        $this->varsLife->signNoKeepVars('z');
    }

    static public function getEventDef()
    {
        $eventDef = EventsDefine::make("submit,modify","do");
        $eventDef->setDefaultEvent("Get");
        return  $eventDef;
    }

    public function doget($vars)
    {
        echo "doget\n";
        $this->callFun="doget";
        $this->vars=$vars;
        
    }

    public function dosubmit($vars)
    {

        echo "dosubmit\n";
        $this->callFun="dosubmit";
        $this->vars=$vars;
    }

    public function domodify($vars)
    {

        echo "domodify\n";
        $this->callFun="domodify";
        $this->vars=$vars;
    }
}

class EventMachineTC2  extends UnitTestCase
{
    public function testHowtoUse()
    {
        try{
        //doGet
        $request = new TestRequest2(array('a'=>'1','b'=>'2','z'=>'9'));
        $action = new TestAction2();
        $action->execute($request,"www.baidu.com");
        $this->assertEqual($action->callFun,"doget");
        $this->assertEqual($action->vars->a,'1');
        $this->assertEqual($action->vars->b,'2');
        $this->assertEqual($action->vars->z,'9');
//        $this->assertEqual($action->vars->x,null);
        //doSubmit
        $request = new TestRequest2(array('b'=>'2','x'=>10,'a'=>'3','d'=>'4','submit'=>'submit'));
        $action->execute($request,"www.baidu.com");
        $this->assertEqual($action->callFun,"dosubmit");
        $this->assertEqual($action->vars->a,'3');
        $this->assertEqual($action->vars->b,'2');
        $this->assertEqual($action->vars->x,10);
        $this->assertFalse(isset($action->vars->z));

        //doSubmit for image  
        $request = new TestRequest2(array('b'=>'2','x'=>10,'a'=>'3','d'=>'4','submit_x'=>'10'));
        $action->execute($request,"www.baidu.com");
        $this->assertEqual($action->callFun,"dosubmit");
        $this->assertEqual($action->vars->a,'3');
        $this->assertEqual($action->vars->b,'2');
        $this->assertEqual($action->vars->x,10);
        $this->assertFalse(isset($action->vars->z));
        $request=null;
        //doModify
        $request = new TestRequest2(array('modify'=>'modify'));
        $action->execute($request,"www.baidu.com");
        $this->assertEqual($action->callFun,"domodify");
        $this->assertEqual($action->vars->a,'3');
        $this->assertEqual($action->vars->b,'2');
        $this->assertEqual($action->vars->x,10);
        $this->assertFalse(isset($action->vars->z));

        //doGet
        $request = new TestRequest2(array('d'=>1,'z'=>'8'));
        $action->execute($request,"www.baidu.com");
        $this->assertEqual($action->callFun,"doget");
        $this->assertEqual($action->vars->d,1);
        $this->assertEqual($action->vars->x,10);
        $this->assertEqual($action->vars->a,'3');
        $this->assertEqual($action->vars->z,'8');

        //doGet
        $request = new TestRequest2(array('d'=>1));
        $action->execute($request,"www.baidu.com/gogog");
        $this->assertEqual($action->callFun,"doget");
        $this->assertFalse(isset($action->vars->b));
        $this->assertEqual($action->vars->d,1);
        $this->assertEqual($action->vars->x,10);
        $this->assertFalse(isset($action->vars->a));
        }
        catch(Exception $e)
        {
            echo  $e->getMessage();
            echo $e->getTraceAsString();
        }
    }
}
?>
