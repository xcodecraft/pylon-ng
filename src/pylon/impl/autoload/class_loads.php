<?php
namespace pylon\impl ;

use XSetting ;
use XLogKit ;
use XDBCException ;
use pylon_dict_data ;
use pylon_dict_find ;
class PylonModule
{
    static function pylon_load_cls_index($lib_root,$ver)
    {
        static $index_load = false ;
        if ($index_load )
        {
            return ;
        }
        pylon_dict_data("$lib_root/cls_idx/$ver/_autoload_clspath.idx","CLASS:",$lib_root);
        pylon_dict_data("$lib_root/cls_idx/$ver/_autoload_clsname.idx","","");
        XLogKit::logger("_pylon")->info("$lib_root/cls_idx/v1/_autoload_clspath.idx") ;

        $runpath = XSetting::$runPath ;
        pylon_dict_data("$runpath/autoload/_autoload_clspath.idx","CLASS:","");
        pylon_dict_data("$runpath/autoload/_autoload_clsname.idx","","");
        XLogKit::logger("_pylon")->info("$runpath/autoload/_autoload_clspath.idx") ;

        $index_load = true ;

    }

    static function autoload($classname)
    {

        $key       = "CLASS:".$classname ;
        $path      = pylon_dict_find($key);
        if($path  != NULL)
        {
            $glogger   = XLogKit::logger("_pylon");
            $glogger->debug("cls : $classname , file: $path");
            include_once("$path");
            return ;
        }
    }

    static function unload($classname)
    {
        $glogger = XLogKit::logger("_pylon");
        $calls = debug_backtrace() ;
        foreach($calls as $c )
        {
            if ($c['function'] == 'class_exists')
            {
                return ;
            }

        }
        $glogger->error("load class $classname define faiure!");
        throw new XDBCException( "cant's find cls $classname") ;
    }

}
