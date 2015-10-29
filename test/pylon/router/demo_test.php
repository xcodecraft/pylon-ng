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
        $resp = $this->curl->get("/mygame/1234");
        $data = XRestResult::ok($resp) ;
        $this->assertTrue($data != null);
        $this->assertEquals("hellow world user: 1234",$data);
    }
    public function testIntercepterThrowException()
    {
        $resp  = $this->curl->post("/mygame/exception",array());
        $this->assertTrue(XRestResult::matchFail($resp,510,510)) ;
    }
    public function testIntercepterNoException()
    {
        $resp = $this->curl->get("/mygame/exception");
        $data = XRestResult::ok($resp) ;
        $this->assertTrue($data != null);
    }


    public function testFail()
    {
        $resp  = $this->curl->get("/game/abc");
        $this->assertTrue(XRestResult::matchFail($resp,510)) ;
    }
    public function testTearDownFail()
    {
        $resp  = $this->curl->post("/game/abc",array());
        $this->assertTrue(XRestResult::matchFail($resp,510)) ;
    }
    public function testPost()
    {
        $resp    = $this->curl->get("/allgame");
        $this->assertTrue(XRestResult::matchFail($resp,510,101)) ;
    }
    public function testPostFail()
    {

        $resp  = $this->curl->post("/mygame/123",array());
        $this->assertTrue(XRestResult::matchFail($resp,404,2)) ;
    }
    public function testUnAuth()
    {
        $resp  = $this->curl->get("/err/unauth",array());
        $this->assertTrue(XRestResult::matchFail($resp,401)) ;
    }
}
