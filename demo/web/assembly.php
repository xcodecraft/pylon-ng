<?php

class HtmlIntc extends XInterceptor
{
    public function _before($xcontext,$request,$response)
    {
        $response->setRoot("/home/zuowenjian/devspace/pylon/demo/web") ;
    }
    public function _after($xcontext,$request,$response)
    {
    }
	public function _exception($e,$xcontext,$request,$response)
    {
        $response->tpl($xcontext,"error.html") ;
    }
}
class WebAssembly implements XAssembly
{
    public function setup()
    {
        XAop::append_by_match_uri("/html/.*"                 , new HtmlIntc());
    }
}

