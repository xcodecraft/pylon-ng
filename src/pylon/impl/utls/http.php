<?php
class PYL_HttpHeader
{
    static function header_info($status_code)
    {
        static $head_dict = null;
        if($head_dict == null)
        {
            $head_dict = array();
            $head_dict[200] =  "OK" ;
            $head_dict[201] =  "Created" ;
            $head_dict[404] =  "Not Found" ;
            $head_dict[401] =  "Unauthorized" ;
            $head_dict[500] =  "Internal Server Error" ;
            $head_dict[501] =  "Not Implementd" ;
            // $head_dict[601] =  "DBC Error" ;
            // $head_dict[611] =  "Biz Error" ;

        }
        if(! isset($head_dict[$status_code]))
            DBC::unImplement("not define head_code : $status_code");
        return $head_dict[$status_code];
    }
    static function  out_header($status_code)
    {
        $info = self::header_info($status_code);
        header("HTTP/1.1 $status_code $info");
    }
}

