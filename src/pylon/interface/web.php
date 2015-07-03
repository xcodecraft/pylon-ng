<?php
abstract class XInterceptor
{
    public function _before($xcontext,$request,$response) {}
    public function _after($xcontext,$request,$response) {}
	public function _exception($e,$xcontext,$request,$response) {}
}



interface XService
{

    public function _before($xcontext,$request,$response);
    public function _after($xcontext,$request,$response);
    public function _get($xcontext,$request,$response);
    public function _post($xcontext,$request,$response);
    public function _put($xcontext,$request,$response);
    public function _delete($xcontext,$request,$response);
    public function _options($xcontext,$request,$response);
}

interface XResponse
{
    public function send($logger,$set_header=true) ;
    public function error($errmsg,$errno = XErrCode::BIZ_UNKNOW,$status_code = 500);
    public function exception($ex);
}

