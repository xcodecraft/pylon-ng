<?php
use pylon\impl\XRouter ;
use pylon\impl\PylonModule ;
use pylon\driver\FastRouter ;

require_once("impl/autoload/class_loads.php") ;

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
    static public  $version      = "v1" ;
    static public  $assembly     = "" ;
    static public  $prjName      = "" ;
    static public  $bootstrap    = "bootstrap.php" ;
    static public  $logCls       = null ;

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
    static public function extend($key,$val)
    {
        static::$extendData[$key] = $val ;
    }
    static public function value($key)
    {
        return  static::$extendData[$key]  ;
    }
    static public function setupModel()
    {
        $sql = XDriver::sqlex();
        XEntEnv::simpleSetup($sql) ;
    }

    static public function setupModel2($sqlex,$idg)
    {
        XEntEnv::simpleSetup($sqlex,$idg);

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
        $logCls    = XSetting::$logCls ;
        $this->externLog =  is_null($logCls) ?  new XNullLogger() : new $logCls($name);

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

    static  $loggers = array() ;
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
        $logger = null ;
        if( isset( static::$loggers[$name]))
        {
            $logger = static::$loggers[$name] ;
        }
        else
        {
            $logger =  new XLogger($name);
            static::$loggers[$name] = $logger ;
        }
        return $logger ;
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
    public static function logConf()
    {
        switch(XSetting::$logMode)
        {
        case XSetting::LOG_ONLINE_MODE :
            static::log4online();
            break;
        case XSetting::LOG_DEBUG_MODE :
            static::log4debug();
            break;
        case XSetting::LOG_BENCHMARK_MODE:
            static::log4benchmark();
            break;
        case XSetting::LOG_FAST_MODE:
            static::log4fast();
            break;
        default:
            static::log4online();
        }
    }
}



function pylon_load_cls_index()
{
    $lib_root  = dirname(dirname(__FILE__));
    PylonModule::pylon_load_cls_index($lib_root,XSetting::$version) ;
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
    PylonModule::autoload($classname) ;
}


function pylon__unload($classname)
{
    PylonModule::unload($classname) ;
}



//spl_autoload_register(pylon_load_cls_index);

//注册 PYLON框架自已的autoload方法
spl_autoload_register(pylonlib__autoload);

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
        pylon_load_cls_index();
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
        return new PylonMvcSvc(static::$runpath);
    }
    /**
     * @brief 启动rest 服务
     * @return
     */
    static public function serving($httpStatus=true)
    {
        if(!is_bool($httpStatus))
        {
            $httpStatus = true ;
        }

        ob_start();
        static::useEnv();
        $data_file = XSetting::$runPath . "/router/_router.idx" ;
        XBox::replace(XBox::ROUTER,new FastRouter($data_file),__METHOD__);
        XRouter::serving($httpStatus);
        ob_end_flush();
    }
    /**
     * @brief 后台服务
     * @return
     */
    static public function useEnv()
    {
        XLogKit::logConf();
        static::load();
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
            return  pylon_dict_find($cls) ;
        }
        else
        {
            throw new LogicException("not support no pylon extendtion");
        }
    }
}


