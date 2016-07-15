<?php

use pylon\driver\XFastSQLExecutor ;
class XDriver
{

    static public function  shortSQLEX()
    {
         $host     = XEnv::get("DB_HOST") ;
         $name     = XEnv::get("DB_NAME") ;
         $user     = XEnv::get("DB_USER") ;
         $pwd      = XEnv::get("DB_PWD" ) ;
         return  new pylon\driver\XLZLExecutor($host,$user,$pwd,$name, 
             XFastSQLExecutor::SHORT_CONN,'utf8','pylon\driver\XFastSQLExecutor');

    }

    static public function  longSQLEX()
    {
         $host     = XEnv::get("DB_HOST") ;
         $name     = XEnv::get("DB_NAME") ;
         $user     = XEnv::get("DB_USER") ;
         $pwd      = XEnv::get("DB_PWD" ) ;
         return  new pylon\driver\XLZLExecutor($host,$user,$pwd,$name, 
             XFastSQLExecutor::LONG_CONN,'utf8','pylon\driver\XFastSQLExecutor');

    }

    static public function  shortSQLEX2($host,$name,$user,$pwd)
    {
        return  new pylon\driver\XLZLExecutor($host,$user,$pwd,$name, 
            XFastSQLExecutor::SHORT_CONN,'utf8','pylon\driver\XFastSQLExecutor');

    }
    static public function  sqlIDG() 
    {
        
    }
}
