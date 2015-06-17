<?php
class DemoREST extends XSimpleService implements XService //@REST_RULE: /demo/$uid
{
    public function _post($xcontext,$request,$response)
    {
        XLogKit::logger("rest")->debug(__FUNCTION__,"his");
        XLogKit::logger("rest")->debug(__FUNCTION__);
        $response->error("post error",XErrCode::SYS_UNKNOW,404);
    }

    public function _get($xcontext,$request,$response)
    {
        $response->success("hellow world user: " . $request->uid );
    }
}

class DemoRuleREST extends XRuleService implements XService //@REST_RULE: /demo/rule/$method
{
    public function help($xcontext,$request,$response)
    {
        XLogKit::logger("rest")->debug(__FUNCTION__,"his");
        $response->error("post error",XErrCode::SYS_UNKNOW,404);
    }

    public function lists($xcontext,$request,$response)
    {
        $response->success("hellow world user: " . $request->uid );
    }
}
