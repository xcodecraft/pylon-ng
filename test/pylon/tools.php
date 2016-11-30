<?php
class Caller
{
    static public function ins()
    {
        $domain = $_SERVER["DOMAIN"];
        $port   = $_SERVER["PORT"];
        $conf   = XHttpConf::localSvc($domain,$port);
        $curl   = new XHttpCaller($conf);
        //$curl   = new XHttpSimulator();
        return $curl ;
    }
}
