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
        if ( $e instanceof  XRuntimeException  )
        {
            $level = "error" ;
            switch($e->status_code)
            {
            case 404:
            case 403:
            case 401:
            case 400:
                return ;
            default:
                break;
            }
            $client->getIdent($client->captureException($e,array("level" => $level)));
            return ;
        }
        $client->getIdent($client->captureException($e));
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
    }

    public function error($msg,$event = null )
    {
        static::getClient()->captureMessage($msg,array(),array("level" => 'error'));
    }

}
