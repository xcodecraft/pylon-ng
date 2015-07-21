<?php
class RouterStub  implements XIRouter
{
    public function _find($uri)
    {
        return '{ "rule" : "/mygoods/$uid" , "cls" : "mygoods",  "uri" : { "uid" : "1234" } } '  ;
    }
}
class mygoods  extends XSimpleService implements XService //@REST_RULE: /mygoods/$uid/
{
    //    const REST_RULE='/mygoods/$uid/';
    public function _get($xcontext,$request,$response)
    {

        $response->success("pylon is great");
        return $result ;
    }
}



class MyIntcpt extends XInterceptor
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

class AfterErr extends XInterceptor
{

    public function _before($xcontext,$request,$response)
    {
    }
    public function _after($xcontext,$request,$response)
    {
        throw new XBizException("_after Interceptor exception");
    }
	public function _exception($e,$xcontext,$request,$response)
    {

        assert(get_class($e) == XBizException) ;
        $response->error($e->getMessage(),1101) ;
    }
}



class RestTest extends PHPUnit_Framework_TestCase
{
    public function http_get($url)
    {
        $_SERVER['REQUEST_METHOD'] =  'GET' ;
        $_SERVER['REQUEST_URI'] = '/mygoods/1001';
    }

    public function testNormal()
    {
        $autoLog            = new XScopeLogEvent("e1");
        XLogKit::logger("logtest")->debug("debug","r");
        $url                = "/mygoods/1001?begin=1&limit=10";
        self::http_get($url);
        // XSetting::$assembly = "APIAssemply1" ;
        XAop::append_by_match_uri("/mygoods/.*", new MyIntcpt());
        //XBox::replace 可以重复注册
        XBox::replace(XBox::ROUTER,new RouterStub(),__METHOD__);

        $result             = XRouter::serving(false);
        $this->assertEquals($result->getData(), "pylon is great");
        $this->assertEquals(MyIntcpt::$beforeCall,1);
        $this->assertEquals(MyIntcpt::$afterCall,1);

    }
    public function testError()
    {
        $autoLog = new XScopeLogEvent("e2");
        XLogKit::logger("logtest")->debug("debug","r");
        $url = "/mygoods/1001?begin=1&limit=10";
        self::http_get($url);

        XAop::append_by_match_uri("/mygoods/.*", new AfterErr());
        //XBox::replace 可以重复注册
        XBox::replace(XBox::ROUTER,new RouterStub(),__METHOD__);
        $result             = XRouter::serving(false);
        $this->assertEquals($result->status_code, 510);
        // $this->assertEquals($result->errno, 1101);

    }
}
