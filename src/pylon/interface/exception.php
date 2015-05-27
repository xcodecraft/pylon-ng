<?php

/**
 * @ingroup exception
 * @brief  错误码
 */
class XErrCode
{
    const SUCCESS      = 0 ;
    const NOT_FOUND    = 404 ;
    const UNDEFINE     = 1000 ;
    const SYS_UNKNOW   = 1001 ;
    const LOGIC_BUG    = 1002 ;
    const CONF_BUG    = 1003 ;
    const DB_ERR       = 1003 ;
    const BIZ_UNKNOW   = 1100 ;

    static public function bizcode($code)
    {
        return $code + self::BIZ_UNKNOW ;
    }
}

/**
    * @brief 用户输入异常
 */
class XUserInputException extends RuntimeException
{
    public function __construct($message)
    {
        parent::__construct($message );
    }
}
/**
 * @ingroup exception
 * @brief  Pylon 的运行时异常基类
 */
class XRuntimeException extends RuntimeException
{
    public function __construct($status_code,$msg,$code)
    {
        $this->status_code = $status_code;
        parent::__construct($msg,$code);
    }
}
/**
 * @ingroup exception
 * @brief   404 没有找到
 */
class XNotFound extends XRuntimeException
{
    public function __construct($msg="",$code=XErrCode::BIZ_UNKNOW)
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
    public function __construct($msg="",$code=XErrCode::BIZ_UNKNOW)
    {
        parent::__construct(401,$msg,$code);
    }
}


/**
 * @ingroup exception
 * @brief  没有实现
 */
class XNotImplemented extends XRuntimeException
{
    public function __construct($msg="",$code=XErrCode::BIZ_UNKNOW)
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
    public function __construct($msg="",$code=XErrCode::BIZ_UNKNOW)
    {
        parent::__construct(500,$msg,$code);
    }

}

/**
 * @ingroup exception
 * @brief  数据库异常
 */
class XDBException extends XRuntimeException
{
    public function __construct($msg)
    {
        parent::__construct(510,$msg,XErrCode::DB_ERR);
    }
}


/**
 * @ingroup exception
 * @brief  违背契约，开发者引入的BUG
 */
class XLogicException extends LogicException
{

    public function __construct($msg,$code=XErrCode::LOGIC_BUG,$status_code=500)
    {
        $this->status_code = $status_code;
        parent::__construct($msg,$code );
    }
}

/**
 * @ingroup exception
 * @brief  配置错误，或违反约定
 */
class XConfigException extends XLogicException
{

    public function __construct($msg="",$code=XErrCode::CONF_BUG)
    {
        parent::__construct($msg,$code,500);
    }
}

/**
 * @ingroup exception
 * @brief  违背DBC 抛出的异常
 */
class XDBCException extends XLogicException
{
    public function __construct($msg="",$code=XErrCode::LOGIC_BUG)
    {
        parent::__construct($msg,$code,500);
    }
}

