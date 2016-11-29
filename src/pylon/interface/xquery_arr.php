<?php
use pylon\impl\DaoFinderUtls ;
use pylon\impl\SimpleDaoFactory ;
use pylon\impl\SimpleQueryFactory ;
use pylon\impl\SimpleMapping ;
use pylon\impl\StdMapping ;
use pylon\impl\DaoImp ;
use pylon\impl\XID ;
use pylon\impl\DQLObj ;
use pylon\impl\UnitWorkImpl ;
use pylon\impl\DynCallParser ;
use pylon\impl\DiagnoseContext ;
use pylon\impl\ApiStyle ;
use pylon\impl\FilterProp ;

class XQueryArr
{
    static public function ins()
    {
        static $inst=null;
        if($inst == null)
        {
            $inst = new XQueryArr();
        }
        return $inst;
    }
    /**
     * @brief   动态函数
     * @code
     * XQueryArr::ins()->get_Author_by_id($author->id());
     * XQueryArr::ins()->list_Author();
     * XQueryArr::ins()->cnt_Author();
     * XQuery::obj()->get_Author_by_id($author->id())
     * XQuery::arr()->get_Author_by_id($author->id())
     * @endcode
     *
     */
    public function __call($name,$params)
    {
        extract( DynCallParser::condObjParse($name));
        return $this->callImpl($op,strtolower($cls),$name,$condnames,$params);
    }

    public function callImpl($op,$table,$name,$paramNames,$params)
    {
        $dc = DiagnoseContext::create("XQueryArr::callImpl");
        $dc->log("call name : $name ");
        $ret = null;
        switch($op)
        {
        case 'list':
            $ret = $this->listCall($table,$paramNames,$params);
            break;
        case 'get':
            $ret = $this->getCall($table,$paramNames,$params);
            break;
        default:
            DBC::unExpect($op,"$op unsupport! ");
        }
        $dc->notkeep();
        return $ret ;
    }

    private function getStyle($params)
    {
        $style = ApiStyle::RUBY;
        if(isset($params[0]) && is_array($params[0])) {
            $style = ApiStyle::MONGO;
        }
        return $style;
    }

    private function listCall($table,$paramNames,$params)
    {
        $style = $this->getStyle($params);
        if($style == ApiStyle::MONGO) {
            $where = isset($params[0])? $params[0] : null;
            $order = isset($params[1])? $params[1] : null;
            $page  = isset($params[2])? $params[2] : null;
            $prop = XProperty::fromArray($where);
        } else {
            $prop = DynCallParser::buildCondProp($paramNames,$params,$extraParams);
            $page      = isset($extraParams[0])? $extraParams[0] : null;
            $ordertype = isset($extraParams[2])? $extraParams[2] : 'DESC';
            if(isset($extraParams[1])) {
                $orderkey  = $extraParams[1];
                $order = array($orderkey => $ordertype);
            }
        }
        return DaoFinderUtls::query("{$table}Query")->listByPropExt($table,null,"*",$prop,$page,$order);
    }

    public function listByProp($cls,$prop,$page=null,$orderkey=null,$ordertype='DESC')
    {
        return DaoFinderUtls::query("{$cls}Query")->listByPropExt($cls,null,"*",$prop,$page,$order);
    }
    public function cntByProp($cls,$prop)
    {
        return DaoFinderUtls::query("{$cls}Query")->cntByProp($cls,null,$prop);
    }
    public function listByArr($cls,$arr,$page=null,$orderkey=null,$ordertype='DESC')
    {
        $prop = XProperty::fromArray($arr) ;
        return DaoFinderUtls::query("{$cls}Query")->listByPropExt($cls,null,"*",$prop,$page,$order);
    }
    private function getCall($table,$paramNames,$params)
    {
        $style = $this->getStyle($params);
        if($style == ApiStyle::RUBY) {
            $prop = DynCallParser::buildCondProp($paramNames,$params,$extraParams);
        } else {
            $where = isset($params[0])? $params[0] : null;
            $order = isset($params[1])? $params[1] : null;
            $prop = FilterProp::create($where);
        }
        return  DaoFinderUtls::query("{$table}Query")->getByProp($prop,$table,"","*","",$order);
    }

    public static function __callStatic($name,$params)
    {
        $ins = static::ins();
        return call_user_func_array(array($ins,$name), $params);
    }
}
