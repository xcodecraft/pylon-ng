<?php
/**\addtogroup Autoload
 * @{
 */

/** 
 * \public
 * @brief  定义的autoload 函数 
 * 
 * @param $classname 
 * 
 * @return 
 */
const C_PYLON_DICT_COUNT="pylon_dict_count" ;
function pylon_autoload($classname)
{

    if (function_exists(C_PYLON_DICT_COUNT) && pylon_dict_count() > 0)
    {
        fast_class_load($classname);
    }
    else
    {
        throw new LogicException("unfound pylon extension !");
    }
}

spl_autoload_register('pylon_autoload');


class PylonModule
{
    static $modleFiles=array();
}


/** 
 * @brief  Pylon 的控制器
 */
class PylonGod
{
    const OFF=0;
    const ON=1;

    /** 
     * @brief  没有找到类
     */
    const NO_CLASS = 1 ;
    /** 
     * @brief  是否抛出NoClass异常
     * @code
     * PylonGod::$throwNoClass=PylonGod::OFF;
     * @endcode
     */
    public static $throwNoClass=1; 

    /** 
     * @brief  关闭异常
     * 
     * @param $point 
     * 
     * @return 
     */
    public static function disableException($point)
    {
        if($point== self::NO_CLASS)
            self::$throwNoClass = self::OFF ;
    }

}
function fast_class_load($classname)
{
    $key  =  "CLASS:".$classname ;
    $glogger = new logger("_pylon");
    $path =  pylon_dict_find($key);
    if($path !=NULL)
    {
        $glogger->debug("cls : $classname , file: $path");
        include_once("$path");
        return ;
    }
    else
    {
        $glogger->error("cls : $classname , file: $path");
        if (!PylonGod::$throwNoClass) return ;
        $msg = pylon_dict_prompt($classname);
        $options = $msg;
        $info="";
        $info .= "******* AUTOLOAD ERROR *********<br>\n";
        $info .= "Class not found : '$classname' <br>\n";
        $info .= "PROMPT:  ' $options'<br>\n";
        $info .= "DATAFILE:<br>\n" . implode(PylonModule::$modleFiles,",<br>\n");
        throw new LogicException("load class $classname define faiure!!\n, $info");
    }

}


/** 
 * \public
 * @brief 可查代码中是否存此类，忽略大小写
 * 
 * @param $clsname 
 * 
 * @return  true|false
 */
function class_have_exists($clsname)
{
    if (function_exists(C_PYLON_DICT_COUNT) && pylon_dict_count() > 0)
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
function get_class_name($clsname)
{

    $cls=  "cls_" . strtolower($clsname);
    if (function_exists(C_PYLON_DICT_COUNT) && pylon_dict_count() > 0)
    {
        $clsname= pylon_dict_find($cls) ;
        return $clsname;
    }
    else
    {
        throw new LogicException("not support no pylon extendtion");
    }
}
/** 
 * \public
 * @brief 加载模块(autoload 需要的索引文件) 
 * 
 * @param $root     类索引根路径
 * @param $relapath 当索引文件所在路径与类文件根路径不一致时使用。
 * 
 * @return 
 */
function pylon_using_module($root,$relapath="")
{
    array_push(PylonModule::$modleFiles,"$root/$relapath/_autoload_clspath.idx");
    pylon_dict_data("$root/$relapath/_autoload_clspath.idx","CLASS:",$root);
    pylon_dict_data("$root/$relapath/_autoload_clsname.idx","","");
}

/** 
 *  @}
 */

