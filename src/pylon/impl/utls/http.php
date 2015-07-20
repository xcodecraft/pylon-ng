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
            $head_dict[400] =  "Bad Request" ;
            $head_dict[401] =  "Unauthorized" ;
            $head_dict[403] =  "Forbidden" ;
            $head_dict[404] =  "Not Found" ;
            $head_dict[405] =  "Mehod Not Allowed" ;
            $head_dict[406] =  "Not Acceptable" ;
            $head_dict[407] =  "Proxy Authentication Required" ;
            $head_dict[408] =  "Request Timeout" ;
            $head_dict[409] =  "Conflict" ;
            $head_dict[410] =  "Gone" ;
            $head_dict[411] =  "Length Required" ;
            $head_dict[412] =  "Precondition Failed" ;
            $head_dict[500] =  "Internal Server Error" ;
            $head_dict[501] =  "Not Implementd" ;
            $head_dict[502] =  "Bad Gateway" ;
            $head_dict[503] =  "Service Unavailable" ;
            $head_dict[504] =  "Gateway Timeout" ;
            $head_dict[505] =  "HTTP Version Not Supported" ;
            $head_dict[506] =  "Variant Also Negotitates" ;
            $head_dict[510] =  "Not Extended" ;

        }
        if(! isset($head_dict[$status_code]))
            return  "pylon undefine" ;
        return $head_dict[$status_code];
    }
    static function  out_header($status_code)
    {
        $info = self::header_info($status_code);
        header("HTTP/1.1 $status_code $info");
    }
}

