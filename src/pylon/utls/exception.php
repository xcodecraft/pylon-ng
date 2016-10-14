<?php
/**
 * @brief 异常工具
 */
class XExceptionUtls
{
    /**
     * @brief  simple_trace
     *
     * @param $e
     *
     * @return
     */
    static public function simple_trace($e)
    {
        $bt = $e->getTrace();
        $info = "";
        foreach($bt as $i)
        {
            $file = basename($i['file']);
            $line = $i['line'];
            $fun  = $i['function'];
            $info = $info . "$file($line):$fun(); ";
        }
        return $info ;
    }
    static public function html_out($e)
    {
        $errorMsg = $e->getMessage();
        $bt       = $e->getTraceAsString();
        str_replace("#","<br>#",$bt);
        echo $errorMsg . "<br>" ;
        echo $bt;
    }
}
