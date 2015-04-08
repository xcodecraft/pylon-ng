<?php
/** 
 * @ingroup utls
 * @brief 对业务结构进行验证，失败则抛出 BizException
 */
class XBizResult
{
    static public function ensureNull( $result,$info)
    {
        if(!is_null($result))
            throw new XBizException($info);
        return $result;
    }
    static public function ensureNotNull($result, $info)
    {
        if(is_null($result))
            throw new XBizException($info);
        return $result;
    }
    static public function ensureNotEmpty($result,$info)
    {
        if(empty($result))
            throw new XBizException($info);
        return $result;
    }
    static public function ensureNotFalse($result,$info)
    {
        if(!$result)
            throw new XBizException($info);
        return $result;
    }
}


/** 
 * @ingroup utls
 * @brief 同XBizResult
 */
class XBR
{
    static public function isNull( $result,$info)
    {
        if(!is_null($result))
            throw new XBizException($info);
        return $result;
    }
    static public function notNull($result, $info)
    {
        if(is_null($result))
            throw new XBizException($info);
        return $result;
    }
    static public function isEmpty($result,$info)
    {
        if(!empty($result))
            throw new XBizException($info);
        return $result;
    }
    static public function isTrue($result,$info)
    {
        if(!$result)
            throw new XBizException($info);
        return $result;
    }
}
