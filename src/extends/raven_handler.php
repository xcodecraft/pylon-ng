<?php

class RavenSetting
{
    static public $server = "" ;
    static public function setup($addr)
    {
        static::$server   = $addr ;
        XSetting::$logCls = RavenLogger ;
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
    public function __construct($name)
    {}

    public function debug($msg,$event = null )
    {
    }

    public function info($msg,$event = null )
    {

    }
    private static function getClient()
    {
        static $ins = null ;
        if($ins === null)
        {
            $ins = new Raven_Client(RavenSetting::$server);
        }
        return $ins ;
    }

    public function warn($msg,$event = null )
    {
        static::getClient()->captureMessage($msg,array(),array("level" => 'warning'));
    }

    public function error($msg,$event = null )
    {
        static::getClient()->captureMessage($msg,array(),array("level" => 'error'));
    }

}
