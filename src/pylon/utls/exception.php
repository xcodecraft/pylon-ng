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

    static public function logException($plog,$e)
    {

        $level = "error" ;
        $code  = -1 ;
        if ( is_subclass_of($e, XRuntimeException ) )
        {
            $code = $e->status_code ;
            switch($e->status_code)
            {
            case 404:
            case 403:
            case 401:
            case 400:
                $level = "warning" ;
                break;
            default:
                break;
            }
        }

        if($level == "error"  )
        {
            $plog->error(get_class($e) ."($code) : " .$e->getMessage());
            $plog->error(XExceptionUtls::simple_trace($e));
        }
        if($level == "warning" )
        {
            $plog->warn(get_class($e) ."($code) : " .$e->getMessage());
            $plog->warn(XExceptionUtls::simple_trace($e));
        }
    }
}
