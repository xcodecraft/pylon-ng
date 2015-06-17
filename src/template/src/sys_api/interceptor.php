<?php

 class AutoCommit  extends XInterceptor
 {
     static $aps=null;
     private $needCommit=true;
     public function _before($xcontext,$request,$response)
     {
         self::$aps = XAppSession::begin();
         XLogKit::logger("main")->info("app session begin");
     }
     static public function commitAndBegin()
     {
         self::$aps->commit();
         self::$aps = null;
         self::$aps = XAppSession::begin();
     }
     public function cancleCommit()
     {
         $this->needCommit = false;
     }
     public function _after($xcontext,$request,$response)
     {
         if($this->needCommit)
         {
             XLogKit::logger("main")->info("app session commit");
             self::$aps->commit();
         }
         self::$aps=null;
         $xcontext->_autocommit=null;
     }
 }

class  AccessAllow  extends XInterceptor
{

    public function _after($xcontext,$request,$response)
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
    }
}

 class HelpInterceptor  extends XInterceptor
 {

    static private function  implPass($xcontext)
    {
        if ( $xcontext->example == 1 ||  $xcontext->help == 1 ) return true ;
        return  false ;
    }
    public function _before($xcontext,$request,$response)
    {
        ApiDesc::$fileRoot        = PathUtls::relativeSys('example/');
        RequestExtract::$fileRoot = PathUtls::relativeSys('example/');
        $xcontext->example        = Args::get($request,'_ex') ;
        $xcontext->help           = Args::get($request,'help') ;
        if (self::implPass($xcontext))
            $xcontext->__service_run = false ;
    }
    public function _after($xcontext,$request,$response)
    {
        if (!self::implPass($xcontext)) return ;
        $uri      = explode('?',$request->uri)[0] ;
        $uri      = str_replace("/v1",'',$uri);
        $respFile = "$uri/resp.json" ;
        $requFile = "$uri/requ.json" ;

        if ($xcontext->example)
        {
            $resp       = RequestExtract::file($respFile) ;
            $response->success($resp->data);

        }
        if($xcontext->help)
        {
            $xcontext->api = new ApiDesc();
            $method        = $request->method ;
            $methodMeta    = new ReflectionMethod($xcontext->__service_cls ,$method) ;
            $doc           = $methodMeta->getDocComment();
            // $xcontext->api->desc($uri,"保洁任务列表") ;
            $xcontext->api->doc($doc) ;
            $xcontext->api->request("get",$uri,$requFile) ;
            $xcontext->api->response(200,$respFile) ;
            $callback = $request->callback ;
            $response->jsonp($callback) ;
            $response->success($xcontext->api->toArr());

        }
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
