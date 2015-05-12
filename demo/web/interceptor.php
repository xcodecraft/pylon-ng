<?php

class HtmlIntc extends XInterceptor
{
    public function _before($xcontext,$request,$response)
    {
        $root= $_SERVER['PRJ_ROOT'] ;
        $response->setRoot("$root/demo/web") ;
    }
    public function _after($xcontext,$request,$response)
    {
    }
	public function _exception($e,$xcontext,$request,$response)
    {
        $response->tpl($xcontext,"error.html") ;
    }
}
