<?php
class RestDemoTest extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $user = $_SERVER['USER'] ;
        $this->curl   = XHttpCall::localIns("$user.demo.pylon360.com",80);
    }

    public function testSuc()
    {
        $a = XScopeLogTag::create("sdk_test",__FUNCTION__) ;
        $x  = $this->curl->get("/mygame/1234");
        $x->rest_decode();
        $this->assertEquals(0,$x->errno);
        $this->assertEquals("hellow world user: 1234",$x->data);
    }
    public function testIntercepterThrowException()
    {
        $x  = $this->curl->post("/mygame/exception",array());
        $x->rest_decode();
        $this->assertEquals(1100,$x->errno);
    }
    public function testIntercepterNoException()
    {
        $x  = $this->curl->get("/mygame/exception");
        $x->rest_decode();
        $this->assertEquals(0,$x->errno);
    }


    public function testFail()
    {
        $x  = $this->curl->get("/game/abc");
        $x->rest_decode();
        $this->assertEquals(500,$x->status_code);
    }
    public function testTearDownFail()
    {
        $x  = $this->curl->post("/game/abc",array());
        $x->rest_decode();
        $this->assertEquals(500,$x->status_code);
    }
    public function testPost()
    {

        $x  = $this->curl->get("/allgame");
        $x->rest_decode();
        $this->assertEquals(101,$x->errno);
        $this->assertEquals(500,$x->status_code);
    }
    public function testPostFail()
    {

        $x  = $this->curl->post("/mygame/123",array());
        $x->rest_decode();
        $this->assertEquals(1001,$x->errno);
        $this->assertEquals(404,$x->status_code);
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
