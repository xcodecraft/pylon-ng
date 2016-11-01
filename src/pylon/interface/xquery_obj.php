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

class XQueryObj
{
    const OP_LIST   = 1;
    const OP_GET    = 2;
    const OP_ADD    = 3;
    const OP_DEL    = 4;
    const OP_UPDATE = 5;
    private $loadStg=null;
    static public function ins()
    {
        static $inst=null;
        if($inst == null)
        {
            $inst = new  XQueryObj() ;
        }
        return $inst;
    }
    public function setLoadStg($stg)
    {
        $this->loadStg = $stg;
    }
    private function getDao($cls)
    {
        $dao = DaoFinderUtls::find($cls);
        if($this->loadStg != null)
        {
            $dao->updateLoadStg($this->loadStg);
        }
        return $dao;
    }
    private function listCall($cls,$name,$paramNames,$params)
    {
        $extraParams=null;
        $prop=DynCallParser::buildCondProp($paramNames,$params,$extraParams);
        if(count($extraParams) == 0 )
        {
                return  $this->getDao($cls)->listByProp($prop);
        }
        else
        {
            $page      = isset($extraParams[0])? $extraParams[0] : null;
            $orderkey  = isset($extraParams[1])? $extraParams[1] : null;
            $ordertype = isset($extraParams[2])? $extraParams[2] : 'DESC';
            return  $this->getDao($cls)->listByProp($prop,$page,$orderkey,$ordertype);
        }
    }

    public function listByProp($cls,$prop,$page=null,$orderkey=null,$ordertype='DESC')
    {
        return  $this->getDao($cls)->listByProp($prop,$page,$orderkey,$ordertype);
    }
    public function cntByProp($cls,$prop)
    {
        return  $this->getDao($cls)->cntByProp($prop);
    }

    public function listByArr($cls,$Arr,$page=null,$orderkey=null,$ordertype='DESC')
    {
        $prop = XProperty::fromArray($arr);
        return  $this->getDao($cls)->listByProp($prop,$page,$orderkey,$ordertype);
    }
    public function getByProp($cls,$prop)
    {
        return  $this->getDao($cls)->getByProp($prop);
    }


    private function getCall($cls,$name,$paramNames,$params)
    {
        $extraParams=null;
        $prop=DynCallParser::buildCondProp($paramNames,$params,$extraParams);
        return  $this->getDao($cls)->getByProp($prop);
    }

    private function cntCall($cls,$name,$paramNames,$params)
    {
        $extraParams=null;
        $prop=DynCallParser::buildCondProp($paramNames,$params,$extraParams);
        return  $this->getDao($cls)->getCount($prop);
    }
    private function delCall($cls,$name,$paramNames,$params)
    {
        $extraParams=null;
        $prop=DynCallParser::buildCondProp($paramNames,$params,$extraParams);
        return  $this->getDao($cls)->delByProp($prop);
    }
    public function callImpl($op,$cls,$name,$paramNames,$params)
    {

        $dc = DiagnoseContext::create("XQueryObj::callImpl");
        $dc->log("call name : $name ");
        $ret = null;
        switch($op)
        {
        case 'list':
            $ret = $this->listCall($cls,$name,$paramNames,$params);
            break;
        case 'get':
            $ret = $this->getCall($cls,$name,$paramNames,$params);
            break;
        case 'cnt':
            $ret = $this->cntCall($cls,$name,$paramNames,$params);
        case 'del':
            $ret = $this->delCall($cls,$name,$paramNames,$params);
            break;
        default:
            DBC::unExpect($op,"unsupport op type");
        }
        $dc->notkeep();
        return $ret ;
    }

    /**
     * @brief  动态函数
     *
     * @code
     * XQueryObj::ins()->get_Author_by_id($author->id());
     * XQueryObj::ins()->list_Author();
     * XQueryObj::ins()->cnt_Author();
     * @endcode
     */
    public function __call($name,$params)
    {
        extract( DynCallParser::condObjParse($name));
        return $this->callImpl($op,$cls,$name,$condnames,$params);
    }
}
