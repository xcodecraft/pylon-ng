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

