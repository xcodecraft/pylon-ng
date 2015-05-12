<?php
// require_once("/home/q/php/gsdk_base/sdk_base.php");
/**
 * user profile test case : not config REST_RULE
 *
 * @author yangwm
 */
class user_profile_Test extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $user           = $_SERVER['USER'] ;
        $logger = XLogKit::logger('xresttest');
        //TODO : 需要自动调用 127.0.0.1 的服务  by zuowenjian
        // $this->curl     = new GHttpCall("$user.demo.pylon360.com", $logger, null, 8360);
        $this->curl   = XHttpCall::localIns("$user.demo.pylon360.com",80);
    }

    public function testGet()
    {
        $x = $this->curl->get("/user/profile/get?uid=5555");
        $x->rest_decode();
        $this->assertEquals(0, $x->errno);
        $this->assertEquals("user_profile get user: 5555",$x->data);
    }
    public function testGets()
    {
        $x = $this->curl->get("/user/profile/gets?uids=5555,6666,7777");
        $x->rest_decode();
        $this->assertEquals(0, $x->errno);
        $this->assertEquals("user_profile gets users: 5555,6666,7777",$x->data);
    }
    public function testSaveWithPostParams()
    {
        $postParams = array("uid" => "5555", "name" => "yangwm", "type" => "g");
        $x = $this->curl->post("/user/profile/save", $postParams);
        $x->rest_decode();
        $this->assertEquals(0, $x->errno);
        $this->assertEquals("user_profile save user: 5555, yangwm, g",$x->data);
    }
    public function testSaveWithGetParams()
    {
        $x = $this->curl->post("/user/profile/save?uid=5555&name=yangwm&type=g",array());
        $x->rest_decode();
        $this->assertEquals(0, $x->errno);
        $this->assertEquals("user_profile save user: 5555, yangwm, g",$x->data);
    }
}

/**
 * game_test2 test case : config REST_RULE
 *
 */
class game_test2_Test extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $user = $_SERVER['USER'] ;
        $this->curl   = XHttpCall::localIns("$user.demo.pylon360.com");
    }

    public function testSuc()
    {
        $x  = $this->curl->get("/gexample/test2/sxd/score?uid=5555");
        $x->rest_decode();
        $this->assertEquals(0,$x->errno);
        echo $x->data;
        $this->assertEquals("sxd 1000, uid 5555",$x->data);

        $x  = $this->curl->post("/gexample/test2/sxd/start",array() );
        $x->rest_decode();
        $this->assertEquals(0,$x->errno);
        $this->assertEquals("sxd OK",$x->data);

    }
}

