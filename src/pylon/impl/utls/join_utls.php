<?php

namespace pylon\impl ;

/**\addtogroup utls
 * @{
 */

/**
* @brief Join工具类
 */
class JoinUtls
{
    static public function  j2str()
    {
        $array = func_get_args();
        return implode("", array_filter($array, function($v){ return $v !== null; })) ;
    }

    /**
        * @brief
        *
        * @param $glue
        * @param $array
        *
        * @return
     */
    static public function  jarray($glue,$array)
    {
        return implode($glue, array_filter($array, function($v){ return $v !== null; })) ;
    }
    static public function jarrayEx($glue,$array,$fun)
    {
        $array = array_filter($array, function($v){ return $v !== null; }) ;
        $fun   = function (&$item,$key)use($fun) {
            $item = call_user_func($fun,$item) ;
        } ;
        array_walk($array, $fun);
        return implode($glue,$array);
    }

    static public function j2csv($arr)
    {
        return implode(",", $arr);
    }

    static public function jwithEgis($glue,$egis,$arr)
    {
        $egisArray= array();
        foreach($arr as $item)
        {
            if(!is_null($item))
                $egisArray[]=$egis.$item.$egis;
        }
        return implode($glue, $egisArray);
    }
    static public function jassoArray($glue,$tag,$array)
    {
        $procArr= array();
        foreach($array as $key => $val)
        {
            if(!is_null($val))
            {
                $procArr[] =  "$key$tag$val";
            }
        }
        return implode($glue,$procArr);
    }
    static public function jassoArrayEx($glue,$array,$fun)
    {
        $procArr= array();
        foreach($array as $key => $val)
        {
            $ret = call_user_func($fun,$key,$val);
            if($ret)
                $procArr[] = $ret;
        }
        return implode($glue,$procArr);
    }

    static public function jsortArray($glue,$sort,$array)
    {
        $str = "";
        foreach($sort as $item)
        {
            if(!is_null($item))
                $str .= "$glue".$array[$item];
        }
        return substr($str,strlen($glue));
    }
    static public function joinPath()
    {
        $args  = func_get_args();
        $path = self::jarray('/',$args);
        return str_replace('//','/',$path);
    }
}

/**
 *  @}
 */
