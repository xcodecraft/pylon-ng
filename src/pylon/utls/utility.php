<?php

/** 
 * @brief 自动记录处理时间
 * @code
 * $autoSpeed = new XLogSpeed("myaction");
 * ....
 * $autoSpeed = null;
 * @endcode
 */
class XLogSpeed
{
    /** 
     * @brief 
     * 
     * @param $action  行为
     * 
     * @return 
     */
    public function __construct($action="")
    {
        $this->action = $action ;
        $this->stime=microtime(true);
    }
    public function __destruct()
    {

        $etime      = microtime(true);
        $usetime    = sprintf("%.3f", $etime-$this->stime);
        $speedLog = new logger("_speed");
        $speedLog->info($this->action . " usetime: $usetime(s)");
    }
}

/**\addtogroup utls
 * @{
 */
function assign($array, $index, $default = '')
{
    return isset($array[$index]) ? $array[$index] : $default;
}
/** 
 * @brief 在作用域内，自动执行结束操作，即使作用域内有异常抛出。
 */
class ScopeAction
{
    private $_endFun;
    /** 
     * @brief 
     * 
     * @param $begFun  开始函数
     * @param $endFun  结束函数
     * 
     * @return 
     */
    public function __construct($begFun,$endFun)
    {   
        $this->_endFun = $endFun;
        call_user_func($begFun); 
    }
    public function __destruct()
    {   
        call_user_func($this->_endFun);
    }
}
/** 
 * @brief 同 ScopeAction 不过放置的是代码，而不是函数
 */
class ScopeExeCode
{

    private $_endCode;
    public function __construct($begCode,$endCode)
    {
        $this->_endCode= $endCode;
        eval($begCode);
    }   
    public function __destruct()
    {
        eval($this->_endCode);
    }   
}


/** 
 *  @}
 */
?>
