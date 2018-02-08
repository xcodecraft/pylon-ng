<?php
//@REST_RULE: /web/demo
class DeomSvc extends XSimpleService implements XService   
{
    public function _get($xcontext,$request,$response)
    {
        $xcontext->x = "google" ;
        $prjRoot = XSetting::ensureEnv("PRJ_ROOT") ;
        $response->tpl($xcontext,  $prjRoot . "/src/sys_web/tpls/demo.html");
    }
}

