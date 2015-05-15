<?php
class RestDemoTest extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $user       = $_SERVER['USER'] ;
        $conf       = XHttpConf::localSvc("$user.demo.pylon360.com",80);
        $this->curl = new XHttpCaller($conf);
    }

    public function testSuc()
    {
        $a    = XScopeLogTag::create("sdk_test",__FUNCTION__) ;
        $x    = $this->curl->get("/mygame/1234");
        $data = RestResult::ok($x) ;
        $this->assertTrue($data != null);
        $this->assertEquals("hellow world user: 1234",$data);
    }
    public function testIntercepterThrowException()
    {
        $x  = $this->curl->post("/mygame/exception",array());
        $this->assertEquals(500,$x->status_code);
        $err = RestResult::fail($x) ;
        $this->assertEquals(1100,$err['errno']);
    }
    public function testIntercepterNoException()
    {
        $x    = $this->curl->get("/mygame/exception");
        $data = RestResult::ok($x) ;
        $this->assertTrue($data != null);
    }


    public function testFail()
    {
        $x  = $this->curl->get("/game/abc");
        $this->assertEquals(500,$x->status_code);
    }
    public function testTearDownFail()
    {
        $x  = $this->curl->post("/game/abc",array());
        $this->assertEquals(500,$x->status_code);
    }
    public function testPost()
    {
        $x    = $this->curl->get("/allgame");
        $this->assertEquals(500,$x->status_code);
        $data = json_decode($x->body(),true) ;
        $this->assertEquals(101,$data['errno']);
    }
    public function testPostFail()
    {

        $x  = $this->curl->post("/mygame/123",array());
        $this->assertEquals(404,$x->status_code);

        $err = RestResult::fail($x) ;
        $this->assertEquals(1001,$err['errno']);
    }
}


// class ActionDemoTest extends PHPUnit_Framework_TestCase
// {
//     public function setup()
//     {
//         $user = $_SERVER['USER'] ;
//         $this->curl   = XHttpCall::localIns("$user.action.pylon.1360.com");
//     }
//
//     public function testSuc()
//     {
//         $x      = $this->curl->get("/login.html");
//         $this->assertEquals($x->status_code , 200);
//         $x      = $this->curl->get("/login_nofound.html");
//         $this->assertEquals($x->status_code , 404);
//     }
//
//     public function testFail()
//     {
//         $x      = $this->curl->get("/loginerr.html");
//         $this->assertEquals($x->status_code , 500);
//     }
// }
//
