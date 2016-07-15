<?php

namespace pylon\impl ;
use logger ;
use XDBC ;

/**
 * @brief 拦截对象;
 * 可获得对象值如下:
 * name :  action 的名称;
 * action:  同上
 * uri  :
 * method : get,post,delete,put
 */
class XIntercepterTarget
{
    public function __construct($request)
    {
        $this->request = $request ;
    }
    public function get($key)
    {
        switch($key)
        {
        case 'name' :
            return strtolower($this->request->get('_action_name'));
        case 'uri' :
            if (empty($_SERVER['REQUEST_URI']) && $this->request->have($key))
            {
                return $this->request->get($key) ;
            }
            return  strtolower($_SERVER['REQUEST_URI']);
        case 'method' :
            if (empty($_SERVER['REQUEST_METHOD']) && $this->request->have($key))
            {
                return $this->request->get($key) ;
            }
            return  strtolower($_SERVER['REQUEST_METHOD']) ;
        }
        return null;
    }
}
/**\addtogroup xmvc
 * @{
 */

class AutoScopeIntc
{
    private $xcontext=null;
    private $intcpt  =null;
    public function __construct($intcpt,$xcontext,$request,$response)
    {
        $this->intcpt   = $intcpt;
        $this->xcontext = $xcontext;
        $this->request  = $request;
        $this->response = $response ;
        $this->intcpt->_before($this->xcontext,$this->request,$this->response);
    }
    public function __destruct()
    {
        $this->intcpt->_after($this->xcontext,$this->request,$this->response);
    }
    public function _exception($e,$xcontext,$request,$response)
    {
        $this->intcpt->_exception($e,$xcontext,$request,$response);
    }

}


class XAopRule
{
    public $pos;
    public $regex;
    public $args;
    public $isMatch;
    private $logger;
    public function __construct($pos,$isMatch,$regex,$args)
    {
        $this->pos      = $pos;
        $this->regex    = $regex;
        $this->args     = $args;
        $this->isMatch  = $isMatch;
        $this->logger   =  new logger("_pylon");
    }
    public function using($itarget)
    {
        XDBC::requireNotNull($itarget,'$itarget');
        $matchAll  = false ;

        $posIdx    =  0 ;
        foreach( $this->pos as $pos)
        {
            $matchOnce  = false;
            $matchAll   = false ;
            $data       = $itarget->get($pos);
            $regex      = "" ;
            if( $pos !=null  )
            {
                $regex     =       str_replace('/','\/',$this->regex[$posIdx]);
                if( $this->isMatch  &&    preg_match('/'.$regex . '/' ,$data ) )   
                {
                    $matchOnce = true ;
                }
                if( !$this->isMatch &&   !preg_match('/'. $regex . '/' ,$data ) ) 
                {
                    $matchOnce = true ;
                }
            }
            $this->logger->debug ( "match aop rule [  pos: $pos ,  data  : $data , regex : $regex , match : $matchOnce ]" );
            if ($matchOnce === false )
            {
                break;
            }
            $matchAll  = true ;
            $posIdx ++ ;

        }
        if($matchAll)
        {
            return $this->args ;
        }
        else
        {
            $this->logger->debug("XAopRule 没有匹配的数据 : " .  implode(",", $this->pos));
            return null;
        }
    }
    public function using_all()
    {
        return $this->args;
    }
}


class XAopRuleSet
{
    public $set=array();
    /**
     * @brief  append/replace  一个拦截器
     *
     * @param $name
     * @param $params
     *
     * @return
     */
    public function __call($name,$params)
    {
        if(!preg_match('/(\S+)_(by)_([a-zA-Z0-9]+)_(\S+)/',$name ,$matchs))
        {
            XDBC::unExpect("unknow $name ,eg:  append_by_name_match('.*',xxxx)");
        }
        list($all,$op,$by,$rule, $pos )=$matchs;

        $match      = null;
        if($rule === "match")
        {
            $match = true;
        }
        else if ($rule === "dismatch")
        {
            $match = false;
        }
        else
        {
            XDBC::unExpect("unknow $match, only support  match , dismatch");
        }

        $mutiPos    = explode("_",$pos);
        $posCnt     = count($mutiPos);
        if ((count($params ) )  != $posCnt +1  )
        {
            XDBC::unExpect("[$name]  not match  params");
        }
        $mutiVal    = array();
        for($i =0 ; $i < $posCnt ; $i++)
        {
            $mutiVal[] = $params[$i];
        }
        $procObj    = $params[$posCnt] ;
        if($op === "append")
        {
            $this->set[] = new XAopRule($mutiPos,$match,$mutiVal,$procObj) ;
        }
        else if ($op === "replace")
        {
            $this->set = array();
            $this->set[] = new XAopRule($mutiPos,$match,$mutiVal,$procObj) ;
        }
        else
        {
            XDBC::unExpect("unknow $op, only support  append, replace ");
        }
    }

    public function replace($args)
    {
        $this->set = array();
        $this->set[] = new XAopRule(null,null,null,$args) ;
    }
    public function using($itarget)
    {
        $its =array();
        foreach( $this->set as $r)
        {
            $obj = $r->using($itarget);
            if($obj  != null)
            {
                $its[] = $obj;
            }
        }
        return $its;
    }
    public function using_all()
    {
        $its =array();
        foreach( $this->set as $r)
        {
            $obj = $r->using_all();
            if($obj  != null)
            {
                $its[] = $obj;
            }
        }
        return $its;
    }
}
