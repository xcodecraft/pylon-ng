<?php

/**
 * @ingroup utls
 * @{
 */

/**
 * @brief   对逻辑进行验证，当为flase时抛出 XDBCException
 *
 */
class XDBC
{
    /**
     * @brief 失败抛出EXCEPTION
     */
    const DO_EXCEPTION  =1;
    /**
     * @brief
     */
    const DO_ABORT      =2 ;
    const DO_WARN       =3 ;
    const DO_NO         =4 ;

    /**
     * @brief 失败的行为
     */
    public static  $failAction = XDBC::DO_EXCEPTION;

    static private function objMsg($obj,$msg)
    {
        if(is_null($obj) ) 
        {
            $obj = "null";
        }
        return  "object $msg: [$obj]";
    }
    static private function dofailAction($msg)
    {
        switch(XDBC::$failAction)
        {
        case  XDBC::DO_EXCEPTION :
            throw new XDBCException($msg);
            break;
        case XDBC::DO_ABORT:
            echo $msg;
            exit ;
        case XDBC::DO_WARN:
            echo "$msg\n";
            break;
        default:
            exit;
        }
    }


    /**
     * @brief
     *
     * @param $first
     * @param $second
     * @param $msg
     *
     * @return  $first
     */
    static public function requireEquals($first,$second,$msg= "first value != second value")
    {
        if($first != $second)
        {
            XDBC::dofailAction($msg);
        }
        return $first;
    }
    /**
     * @brief
     *
     * @param $first
     * @param $parentClass
     *
     * @return $first
     */
    static public function requireIsA($first,$parentClass,$msg="value is not subclass of ")
    {
        if(!is_a($first,$parentClass))
        {
            XDBC::dofailAction($msg);
        }
        return $first;
    }
    static public function requireNotEquals($first,$second,$msg="first value == second value")
    {
        if($first == $second )
        {
            XDBC::dofailAction($msg);
        }
        return $first;
    }
    /**
     * @brief  不期望执行到此
     *
     * @param $obj
     * @param $msg
     *
     * @return
     */
    static  public function unExpect($obj,$msg= "unexcept!")
    {
        XDBC::dofailAction("$obj $msg");
    }
    /**
     * @brief 未实现
     *
     * @param $funName
     *
     * @return void
     */
    static public function unImplement($msg="have not implment")
    {
        static::dofailAction($msg);
    }
    static public function requireNull($obj,$msg="value is not null")
    {
        if(is_null($obj)) 
        {
            return $obj;
        }
        XDBC::dofailAction($msg);

    }
    /**
     * @brief
     *
     * @param $obj
     * @param $msg
     *
     * @return $obj
     */
    static public function requireNotNull($obj,$msg="value is  null ")
    {
        if(!is_null($obj)) 
        {
            return $obj;
        }
        XDBC::dofailAction($msg);
    }


    /**
     * @brief
     *
     * @param $obj
     * @param $msg
     *
     * @return $obj
     */
    static public function requireObj($obj,$msg="value is not object")
    {
        if(is_object($obj)) 
        {
            return $obj;
        }
        XDBC::dofailAction($msg);
    }
    /**
     * @brief
     *
     * @param $obj
     * @param $msg
     *
     * @return $obj
     */
    static public function requireTrue($obj,$msg="require true ,but is false")
    {
        if($obj) 
        {
            return $obj;
        }
        XDBC::dofailAction($msg);
    }
    static public function requireArray($arr,$msg="value is not  Array")
    {
        if(is_array($arr)) return $arr;
        XDBC::dofailAction($msg);
    }


}

/**
 *  @}
 */
