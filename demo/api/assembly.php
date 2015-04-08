<?php
class SucIntcpt extends XInterceptor
{
    public function _before($xcontext,$request,$response)
    {
    }
    public function _after($xcontext,$request,$response)
    {
    }
	public function _exception($e,$xcontext,$request,$response)
    {

    }
}

class ErrIntcpt extends XInterceptor
{
    public function _before($xcontext,$request,$response)
    {
        throw new XBizException("Interceptor exception");
    }
    public function _after($xcontext,$request,$response)
    {
    }
	public function _exception($e,$xcontext,$request,$response)
    {
        assert(get_class($e) == XBizException) ;

    }
}

class AfterErrItc extends XInterceptor
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
    }
}



class ApiAssembly implements XAssembly
{
    public function setup()
    {
        XAop::append_by_match_uri_method("/mygame/.*"        , "get", new SucIntcpt());
        XAop::append_by_match_uri_method("/mygame/exception" , "post", new ErrIntcpt());
        XAop::append_by_match_uri("/game/.*"                 , new ErrIntcpt());
        XAop::append_by_match_uri_method("/error/after/.*"        , "get", new AfterErrItc());
    }
}

