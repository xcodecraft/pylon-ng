<?php
class StubIntercepter
{
}
class AopTest extends PHPUnit_Framework_TestCase
{
    public function testA()
    {

        $rset            = new XAopRuleSet();
        $iobj            = new StubIntercepter();
        $rset->append_by_match_uri_method("a.html",'get',$iobj);
        $request         = new XProperty() ;
        $request->uri    = "a.html";
        $request->method = "get";

        $itarget = new  XIntercepterTarget($request);
        $this->assertEquals($itarget->get("uri"),"a.html");
        $this->assertEquals($itarget->get("method"),"get");
        $iobj2= $rset->using($itarget);
        $this->assertTrue(!empty($iobj2));

    }
}
