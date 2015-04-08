<?php
class Htmlsvc extends XSimpleService implements XService   //@REST_RULE: /web/html/$sub
{

    public function _get($xcontext,$request,$response)
    {
        $xcontext->x = "google" ;
        $response->tpl($xcontext, $request->sub . ".html");
    }

}

class HtmlSvc2 extends XRuleService  implements XService  //@REST_RULE: /web/call/$method
{

    public function info($xcontext,$request,$response)
    {
        phpinfo();
    }

    public function go($xcontext,$request,$response)
    {
        // $response->go("http://www.haoso.com");
        $response->location("http://www.haoso.com");
        // $response->location($url)
    }
}
