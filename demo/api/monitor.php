<? php
//@REST_RULE: /monitor 
class monitor extends XSimpleService implements XService 
{
    public function _head($xcontext, $request, $response)
    {
        //do some check ...
        $response->success("");
    }


    public function _get($xcontext, $request, $response)
    {
        //do some check ...
        $response->success(["status" => "OK"]);
    }
}
