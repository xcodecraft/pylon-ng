<?php
class XTest2Action
{
    public function do_xtest()
    {
    }
    public function do_xtest1()
    {
    }
}
class TestIntcpt extends XInterceptor
{
    static public $beforeCall=0;
    static public $afterCall=0;
    static public function init()
    {
        self::$beforeCall = 0;
        self::$afterCall  = 0;
    }
    public function _before($xcontext,$request,$response)
    {
        self::$beforeCall ++;
    }
    public function _after($xcontext,$request,$response)
    {
        self::$afterCall ++;
    }
    public function _exception($e,$xcontext,$request,$response)
    {}
}

class TestIntcpt2 extends XInterceptor
{
    static public $beforeCall=0;
    static public $afterCall=0;
    static public function init()
    {
        self::$beforeCall = 0;
        self::$afterCall  = 0;
    }
    public function _before($xcontext,$request,$response)
    {
        throw new Exception("test exception");
        self::$beforeCall ++;
    }
    public function _after($xcontext,$request,$response)
    {
        self::$afterCall ++;
    }
    public function _exception($e,$xcontext,$request,$response)
    {}
}

//class XmvcTest extends UnitTestCase
//{
//    public $controller =null;
//    public function setup()
//    {
//        TestIntcpt::init();
//        TestIntcpt2::init();
//        $loader = XLoader::load('XMVC_TEST1','confMaps1','pylon/test/xmvc/acts_conf.php');
//        $this->controller = new XController($loader);
//    }
//    public function testXmvc()
//    {
//        $get =array();
//        $get['do']='xtest';
//        $request=  new XPeropty($get);
//        $this->controller->processEx($request,"do","xtest",null);
//        $this->assertEqual(TestIntcpt::$beforeCall,2);
//        $this->assertEqual(TestIntcpt::$afterCall,2);

//    }

//    public function testXmvc1()
//    {
//        $get =array();
//        $get['do']='xtest1';
//        $request=  new XPeropty($get);
//        $this->controller->processEx($request,"do","xtest",null);

//        $this->assertEqual(TestIntcpt::$beforeCall,1);
//        $this->assertEqual(TestIntcpt2::$beforeCall,0);
//    }

//}
?>
