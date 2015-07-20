<?php

/**
 * @ingroup exception
 * @brief  错误码
 */
class XErrCode
{
    const SUCCESS      = 0 ;
    // const NOT_FOUND    = 404 ;
    const UNDEFINE     = 1 ;
    const UNKNOW       = 2 ;

    const CONF_ERROR   = 2001 ;
    const BUG          = 2002 ;
    // const SYS_UNKNOW   = 1001 ;
    // const LOGIC_BUG    = 1002 ;
    // const CONF_BUG    = 1003 ;
    // const DB_ERR       = 1003 ;
    // const BIZ_UNKNOW   = 1100 ;

    static public function bizcode($code)
    {
        return $code + self::BIZ_UNKNOW ;
    }
}

/**
 * @ingroup exception
 * @brief  Pylon 的运行时异常基类
 */
class XRuntimeException extends RuntimeException
{
    public function __construct($status_code,$msg,$subcode)
    {
        $this->status_code = $status_code;
        $this->sub_code    = $subcode ;
        $code              = "$status_code:$subcode" ;
        parent::__construct($msg,$code);
    }
}

/**
    * @brief 用户输入异常
 */
class XUserInputException extends RuntimeException
{
    public function __construct($message,$subcode=XErrCode::UNDEFINE)
    {
        parent::__construct(400,$message,$subcode );
    }
}

/**
 * @ingroup exception
 * @brief   404 没有找到
 */
class XNotFound extends XRuntimeException
{
    public function __construct($msg="",$code=XErrCode::UNDEFINE)
    {
        parent::__construct(404,$msg,$code);
    }
    /**
     * @brief  ACTION
     */
    const ACTION=1;
}

/**
 * @ingroup  exception
 * @brief 没有权限 401
 */
class XUnAuthorized extends XRuntimeException
{
    public function __construct($msg="",$subcode=XErrCode::UNDEFINE)
    {
        parent::__construct(401,$msg,$subcode);
    }
}

class XForbidden extends XRuntimeException
{
    public function __construct($msg="",$subcode=XErrCode::UNDEFINE)
    {
        parent::__construct(403,$msg,$subcode);
    }
}


/**
 * @ingroup exception
 * @brief  没有实现
 */
class XNotImplemented extends XRuntimeException
{
    public function __construct($msg="",$code=XErrCode::UNDEFINE)
    {
        parent::__construct(501,$msg,$code);
    }
}

/**
 * @ingroup exception
 * @brief 业务异常
 */
class XBizException extends XRuntimeException
{
    public function __construct($msg="",$subcode=XErrCode::UNDEFINE)
    {
        parent::__construct(510,$msg,$subcode);
    }

}



/**
 * @ingroup exception
 * @brief  违背契约，开发者引入的BUG
 */
class XLogicException extends LogicException
{
    public function __construct($status_code,$msg,$subcode)
    {
        $this->status_code = $status_code;
        $this->sub_code    = $subcode ;
        $code              = "$status_code:$subcode" ;
        parent::__construct($msg,$code);
    }
}


/**
 * @ingroup exception
 * @brief  数据库异常
 */
class XDBException extends XLogicException
{
    public function __construct($msg,$subcode=XErrCode::UNDEFINE)
    {
        parent::__construct(503,$msg,$subcode);
    }
}



/**
 * @ingroup exception
 * @brief  配置错误，或违反约定
 */
class XConfigException extends XLogicException
{

    public function __construct($msg="",$subcode=XErrCode::CONF_ERROR)
    {
        parent::__construct(500,$msg,$subcode);
    }
}

/**
 * @ingroup exception
 * @brief  违背DBC 抛出的异常
 */
class XDBCException extends XLogicException
{
    public function __construct($msg="",$subcode=XErrCode::BUG)
    {
        parent::__construct(500,$msg,$subcode);
    }
}

