<?php

use pylon\impl\DaoFinderUtls ;
use pylon\impl\DynCallParser ;
use pylon\impl\ApiStyle ;
use pylon\impl\FilterProp ;
/**
 * @ingroup entity
 * @brief  直接数据写入
 */
class XWriter
{

    static public function ins()
    {
        static $inst=null;
        if($inst == null)
        {
            $inst = new XWriter();
        }
        return $inst;
    }
    private function getStyle($params)
    {
        $style = ApiStyle::RUBY;
        if(isset($params[0]) && is_array($params[0])) {
            $style = ApiStyle::MONGO;
        }
        return $style;
    }

    protected function delCall($name,$params)
    {
        extract(DynCallParser::condObjParse($name));
        $style = $this->getStyle($params);
        if($style == ApiStyle::RUBY) {
            $prop = DynCallParser::buildCondProp($condnames,$params,$extraParams);
        } else {
            $prop = FilterProp::create($params[0]);
        }
        return DaoFinderUtls::find($cls)->delByProp($prop);
    }
    protected function updateCall($name,$params)
    {
        extract(DynCallParser::condUpdateObjParse($name));
        $style = $this->getStyle($params);
        if($style == ApiStyle::RUBY) {
            extract(DynCallParser::buildUpdateArray($updatenames,$condnames,$params));
        } else {
            $updateArray = $params[0];
            $condArray   = is_array($params[1]) ? $params[1] : array();
        }
        return DaoFinderUtls::find($cls)->updateByArray($updateArray,$condArray,null);
    }
    public function __call($name,$params)
    {
        $ret = null ;
        switch(substr($name,0,3))
        {
        case 'del':
            $ret = $this->delCall($name,$params);
            break;
        case 'upd':
            $ret = $this->updateCall($name,$params);
            break;
        default:
            DBC::unExpect($op,"unsupport op type");
        }
        return $ret ;

    }
    public static function __callStatic($name,$params)
    {
        $ins = static::ins();
        return call_user_func_array(array($ins,$name), $params);
    }
}

