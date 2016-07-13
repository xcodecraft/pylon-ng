<?php
/**
 * user profile test case : not config REST_RULE
 *
 * @author yangwm
 */
class user_profile_Test extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $user       = $_SERVER['USER'] ;
        // $conf       = XHttpConf::localSvc("$user.demo.pylon360.com",80, "xdemo_test");
        // $this->curl = new XHttpCaller($conf);
        $this->curl = Caller::ins();
    }

    public function testGet()
    {
        $x = $this->curl->get("/user/profile/get?uid=5555&abc=1111");
        $data = XRestResult::ok($x) ;
        $this->assertTrue($data != null);
        $this->assertEquals("user_profile get user: 5555",$data);
    }
    public function testGets()
    {
        $x = $this->curl->get("/user/profile/gets?uids=5555,6666,7777");
        $data = XRestResult::ok($x) ;
        $this->assertTrue($data != null);
        $this->assertEquals("user_profile gets users: 5555,6666,7777",$data);
    }
    public function testSaveWithPostParams()
    {
        $postParams = array("uid" => "5555", "name" => "yangwm", "type" => "g");
        $x = $this->curl->post("/user/profile/save", $postParams);
        $data = XRestResult::ok($x) ;
        $this->assertTrue($data != null);
        $this->assertEquals("user_profile save user: 5555, yangwm, g",$data);
    }
    public function testSaveWithGetParams()
    {
        $x = $this->curl->post("/user/profile/save?uid=5555&name=yangwm&type=g",array());
        $data = XRestResult::ok($x) ;
        $this->assertTrue($data != null);
        $this->assertEquals("user_profile save user: 5555, yangwm, g",$data);
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
        $user       = $_SERVER['USER'] ;
        // $conf       = XHttpConf::localSvc("$user.demo.pylon360.com",80);
        // $this->curl = new XHttpCaller($conf);
        $this->curl = Caller::ins();
    }

    public function testSuc()
    {
        $x  = $this->curl->get("/gexample/test2/sxd/score?uid=5555");
        $data = XRestResult::ok($x) ;
        $this->assertTrue($data != null);
        $this->assertEquals("sxd 1000, uid 5555",$data);

        $x  = $this->curl->post("/gexample/test2/sxd/start",array() );
        $data = XRestResult::ok($x) ;
        $this->assertTrue($data != null);
        $this->assertEquals("sxd OK",$data);

    }
}

