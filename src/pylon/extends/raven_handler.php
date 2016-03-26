<?php

class RavenSetting
{
    static public $server = "" ;
    static public function setup($addr)
    {
        static::$server   = $addr ;
        XSetting::$logger = new RavenLogger() ;
        XAop::append_by_match_uri(".*" , new RavenErrorHandler());
    }
}

class RavenErrorHandler extends XInterceptor
{


    public function _exception($e,$xcontext,$request,$response)
    {
        $client = new Raven_Client(RavenSetting::$server);
        $client->getIdent($client->captureException($e,$xcontext->toArr()));
    }
}
class RavenLogger implements XIlogger 
{

    public function debug($msg,$event = null )
    {
    }

    public function info($msg,$event = null )
    {

    }

    public function warn($msg,$event = null )
    {
        $client   = new Raven_Client(RavenSetting::$server);
        $event_id = $client->getIdent($client->captureMessage($msg));
    }

    public function error($msg,$event = null )
    {
        $client   = new Raven_Client(RavenSetting::$server);
        $event_id = $client->getIdent($client->captureMessage($msg));
    }

}
