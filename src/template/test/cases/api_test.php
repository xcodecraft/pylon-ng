<?php

class ApiTest  extends PHPUnit_Framework_TestCase
{

    public function testDemo()
    {
        $baseDomain = XEnv::get("BASE_DOMAIN");
        $conf       = XHttpConf::localSvc("api.$baseDomain",80);
        $this->curl = new XHttpCaller($conf);
        $resp = $this->curl->get("/demo/1");
        $data = XRestResult::ok($resp) ;
        $this->assertTrue($data != null);
        $this->assertEquals("hellow world user: 1",$data);

    }

}
