<?php

/** @defgroup assembly 装配器
 *
 *  使用者关注
 */
/** @defgroup aop AOP框架
 *
 *  使用者关注
 */
/** @defgroup  rest REST框架
 *
 *  使用者关注
 */


/** @defgroup entity 实体框架
 *
 *   ORM 框架
 */

/** @defgroup utls 工具
 *
 *  框架使用时，有用的工具类
 */

/** @defgroup extends 扩展
 *
 *  扩展框架的能力
 */

/** @defgroup exception 异常体系
 *
 *  使用者关注
 */

class XSetting
{

    const LOG_DEBUG_LEVEL    = 0 ;
    const LOG_INFO_LEVEL     = 1 ;
    const LOG_WARN_LEVEL     = 2 ;
    const LOG_ERROR_LEVEL    = 3 ;

    const LOG_DEBUG_MODE     = 'DEBUG';
    /**
     * @brief 线上运行
     */
    const LOG_ONLINE_MODE    = 'ONLINE' ;
    /**
     * @brief  快速模式,提高性能
     */
    const LOG_FAST_MODE      = 'FAST' ;
    /**
     * @brief 性能测试模式
     */
    const LOG_BENCHMARK_MODE = 'BENCHMARK'  ;

    static public  $logMode      = "DEBUG";
    static public  $logAll       = True ;
    static public  $logTag       = "" ;
    static public  $runPath      = "" ;
    static public  $assembly     = "" ;
    static public  $prjName      = "" ;
    static public  $bootstrap    = "bootstrap.php" ;
    static public  $logger       = null ;

    static public  $entLazyload  = true ;


    static public  $respClass   = 'XRestResp' ;
    static public  $respInsFun  = null ;

    static public  $extendData  = array() ;


    /**
     * @brief  设置日志对象的输出级别
     *
     * @param $name
     * @param $level
     *
     * @return
     */
    public static function logLevel($name,$level)
    {

        log_kit::level($name,$level);
    }

    /**
     * @brief  设置日志对象的tag
     *
     * @param $name
     * @param $tag
     *
     * @code
     * XSetting::logTag("_speed","game_sxd");
     * @endcode
     * @return
     */
    public static function logTag($name,$tag)
    {
        log_kit::tag($name,$tag);
    }
    public static function ensureEnv($key)
    {
        if(isset($_SERVER[$key]) && !empty($_SERVER[$key]) )
        {
            return  $_SERVER[$key] ;

        }
        throw new LogicException("不能获得环境变量 $key,或为空!");

    }
    //TODO:
    static public function extend($key,$val)
    {
        self::$extendData[$key] = $val ;
    }
    static public function value($key)
    {
        return  self::$extendData[$key]  ;
    }



}
interface XIlogger
{
    function debug($msg,$event = null ) ;
    function info($msg,$event = null ) ;
    function warn($msg,$event = null ) ;
    function error($msg,$event = null ) ;

}

class XNullLogger implements XIlogger 
{
    public function debug($msg,$event = null )
    {
    }

    public function info($msg,$event = null )
    {

    }

    public function warn($msg,$event = null )
    {

    }

    public function error($msg,$event = null )
    {
    }
}


class XLogger  implements XIlogger 
{

    public function __construct($name) 
    {
        $this->log = new Logger($name) ;
        $this->externLog =  is_null(XSetting::$logger) ?  new XNullLogger() : XSetting::$logger ;
        
    }
    public function debug($msg,$event = null )
    {
        $this->log->debug($msg,$event) ;
        $this->externLog->debug($msg,$event) ;

    }

    public function info($msg,$event = null )
    {
        $this->log->info($msg,$event) ;
        $this->externLog->info($msg,$event) ;

    }

    public function warn($msg,$event = null )
    {
        $this->log->warn($msg,$event) ;
        $this->externLog->warn($msg,$event) ;

    }

    public function error($msg,$event = null )
    {
        $this->log->error($msg,$event) ;
        $this->externLog->error($msg,$event) ;
    }

}
/**
 * @ingroup utls
 * @brief  日志配置
 */
class XLogKit
{

    public static function event($evt)
    {
        log_kit::event($evt);
    }
    /**
     * @brief  获得日志对象
     *
     * @param $name
     *
     * @return  $logger;
     */
    public static function logger($name)
    {
        // if (! self::$is_setting)
        return new XLogger($name);
    }
}


class PylonModule
{
    static $modleFiles=array();
}

function pylon_load_cls_index()
{

    static $index_load = false ;
    if ($index_load ) return ;
    $lib_root  = dirname(__FILE__);
    pylon_dict_data("$lib_root/class_index/_autoload_clspath.idx","PYLON2_CLASS:",$lib_root);
    pylon_dict_data("$lib_root/class_index/_autoload_clsname.idx","","");

    $runpath = XSetting::$runPath ;
    array_push(PylonModule::$modleFiles,"$runpath/_autoload_clspath.idx");
    pylon_dict_data("$runpath/autoload/_autoload_clspath.idx","CLASS:","");
    pylon_dict_data("$runpath/autoload/_autoload_clsname.idx","","");

    $index_load = true ;
}
/**
 * \public
 * @brief  定义的autoload 函数
 *
 * @param $classname
 *
 * @return
 */
function pylonlib__autoload($classname)
{
    $key       = "PYLON2_CLASS:".$classname ;
    $glogger   = new logger("_pylon");
    $path      = pylon_dict_find($key);
    if($path  != NULL)
    {
        $glogger->debug("cls : $classname , file: $path");
        include_once("$path");
        return ;
    }
}

function appsys__autoload($classname)
{

    $key        =  "CLASS:".$classname ;
    $glogger    =  new logger("_pylon");
    $path       =  pylon_dict_find($key);
    if($path !=NULL)
    {
        $glogger->debug("cls : $classname , file: $path");
        include_once("$path");
        return ;
    }
}

function pylon__unload($classname)
{
    $glogger  = new logger("_pylon");
    $glogger->error("cls : $classname , file: $path");
    $msg      = pylon_dict_prompt($classname);
    $info     = "";
    $info    .= "******* AUTOLOAD ERROR *********<br>\n";
    $info    .= "Class not found : '$classname' <br>\n";
    $info    .= $msg ;
    $info    .= "DATAFILE:<br>\n" . implode(PylonModule::$modleFiles,",<br>\n");
    $glogger->error("log load class $classname define faiure!!\n, $info");
}




spl_autoload_register(pylon_load_cls_index);

//注册 PYLON框架自已的autoload方法
spl_autoload_register(pylonlib__autoload);

//注册应用系统的的autoload方法
spl_autoload_register(appsys__autoload);

//注册没有找的处理函数
spl_autoload_register(pylon__unload);


/**
 * @ingroup interface
 * @brief Pylon框架入口
 */
class XPylon
{
    static private function load()
    {

        if (empty(XSetting::$runPath) )
        {
            throw new LogicException('没有设定 XSetting::$runPath') ;
        }
        require XSetting::$bootstrap ;
    }
    /**
     * @brief 获得websvc实例
     *
     * @return  PylonMvcSvr 实例
     */
    static public function websvc()
    {
        return new PylonMvcSvc(self::$runpath);
    }
    /**
     * @brief 启动rest 服务
     * @return
     */
    static public function serving()
    {

        ob_start();
        self::useEnv();
        $data_file = XSetting::$runPath . "/router/_router.idx" ;
        XBox::regist(XBox::ROUTER,new FastRouter($data_file),__METHOD__);
        XRouter::serving();
        ob_end_flush();
    }
    /**
     * @brief 后台服务
     * @return
     */
    static public function useEnv()
    {
        self::logConf();
        self::load();
    }

    private static function logConf()
    {
        switch(XSetting::$logMode)
        {
        case XSetting::LOG_ONLINE_MODE :
            self::log4online();
            break;
        case XSetting::LOG_DEBUG_MODE :
            self::log4debug();
            break;
        case XSetting::LOG_BENCHMARK_MODE:
            self::log4benchmark();
            break;
        case XSetting::LOG_FAST_MODE:
            self::log4fast();
            break;
        default:
            self::log4online();
        }
    }
    private static function log4online()
    {
        log_kit::init(XSetting::$prjName,XSetting::$logTag,XSetting::LOG_INFO_LEVEL);
        log_kit::level("_pylon" , XSetting::LOG_WARN_LEVEL);
        log_kit::level("_res"   , XSetting::LOG_INFO_LEVEL);
        log_kit::level("_sql"   , XSetting::LOG_INFO_LEVEL);
        log_kit::level("_speed" , XSetting::LOG_INFO_LEVEL);
        log_kit::level("_rest"  , XSetting::LOG_INFO_LEVEL);
        XLogKit::logger("_pylon")->info("Log Mode: log4online");
    }
    private static function log4fast($prj,$tag="")
    {
        log_kit::init(XSetting::$prjName,XSetting::$logTag,XSetting::LOG_WARN_LEVEL);
        log_kit::level("_pylon" , XSetting::LOG_WARN_LEVEL);
        log_kit::level("_res"   , XSetting::LOG_ERROR_LEVEL);
        log_kit::level("_sql"   , XSetting::LOG_ERROR_LEVEL);
        log_kit::level("_speed" , XSetting::LOG_INFO_LEVEL);
        log_kit::level("_rest"  , XSetting::LOG_ERROR_LEVEL);
    }
    private static function log4debug()
    {
        log_kit::init(XSetting::$prjName,XSetting::$logTag,XSetting::LOG_DEBUG_LEVEL);
        log_kit::level("_pylon" , XSetting::LOG_INFO_LEVEL);
        log_kit::level("_res"   , XSetting::LOG_INFO_LEVEL);
        log_kit::level("_sql"   , XSetting::LOG_INFO_LEVEL);
        log_kit::level("_speed" , XSetting::LOG_INFO_LEVEL);
        log_kit::level("_rest"  , XSetting::LOG_INFO_LEVEL);
        XLogKit::logger("_pylon")->info("Log Mode: log4debug");
    }

    private static function log4benchmark()
    {
        log_kit::init(XSetting::$prjName,XSetting::$logTag,XSetting::LOG_ERROR_LEVEL);
        log_kit::level("_pylon" , XSetting::LOG_ERROR_LEVEL);
        log_kit::level("_res"   , XSetting::LOG_ERROR_LEVEL);
        log_kit::level("_sql"   , XSetting::LOG_ERROR_LEVEL);
        log_kit::level("_speed" , XSetting::LOG_ERROR_LEVEL);
        log_kit::level("_rest"  , XSetting::LOG_ERROR_LEVEL);
    }

    /**
     * \public
     * @brief 可查代码中是否存此类，忽略大小写
     *
     * @param $clsname
     *
     * @return  true|false
     */
    static public function haveClass($clsname)
    {
        if (function_exists("pylon_dict_count") && pylon_dict_count() > 0)
        {
            return pylon_dict_has("CLASS:$clsname") ;
        }
        else
        {
            throw new LogicException("not support no pylon extendtion");
        }
    }

    /**
     * @brief 获得区分大小写的 ClassName
     *
     * @param $clsname
     *
     * @return
     */
    static public function className($clsname)
    {

        $cls=  "cls_" . strtolower($clsname);
        if (function_exists("pylon_dict_count") && pylon_dict_count() > 0)
        {
            $clsname= pylon_dict_find($cls) ;
            return $clsname;
        }
        else
        {
            throw new LogicException("not support no pylon extendtion");
        }
    }
}


