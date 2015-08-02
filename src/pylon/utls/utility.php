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
 * @brief  在Scope内执行代码
 */
class XScopeCode
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
