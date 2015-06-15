<?php

/**
 * @ingroup rest
 * @brief  简易实现 XService
 */
class XSimpleService implements XService
{
    public function _before($xcontext,$request,$response){
    }
    public function _after($xcontext,$request,$response){
    }
    public function _get($xcontext,$request,$response)
    {
        throw new XNotImplemented("Unimplemnt " . get_class($this) ."::" .  __FUNCTION__);
    }
    public function _post($xcontext,$request,$response)
    {
        throw new XNotImplemented("Unimplemnt " . get_class($this) ."::" .  __FUNCTION__);
    }
    public function _put($xcontext,$request,$response)
    {
        throw new XNotImplemented("Unimplemnt " . get_class($this) ."::" .  __FUNCTION__);
    }
    public function _delete($xcontext,$request,$response)
    {
        throw new XNotImplemented("Unimplemnt " . get_class($this) ."::" .  __FUNCTION__);
    }
    public function _help($xcontext,$request,$response)
    {
        throw new XNotImplemented("Unimplemnt " . get_class($this) ."::" .  __FUNCTION__);
    }
}

/**
 * @ingroup rest
 *
 * @brief  基于规则与配置调用的Rest   /$class/$method $method 必有并且在uri的最后
 *   1. 基于规则, 如： /user/profile/gets?   ==>  $class=user_prifile,$method=gets
 *   2. 基于配置, 如： /gexample/test2/xsd/score? + REST_RULE: /gexample/test2/$gkey/$method   ==>  $class=gexample_test2,$gkey=xsd,$method=score
 *
 */


class XRuleService implements XService
{
    public function _before($xcontext,$request,$response){
    }
    public function _after($xcontext,$request,$response){
    }
    private function callMethod($xcontext,$request,$response)
    {
        $method = $request->method ;
        if (empty($method))
            throw new XConfigException("method 为空") ;

        if (method_exists($this,$method))
        {
            $this->$method($xcontext,$request,$response);
            return ;
        }
        throw new XNotImplemented("Unimplemnt " . get_class($this) ."::" .  $method );
    }
    public function _get($xcontext,$request,$response)
    {
        $this->callMethod($xcontext,$request,$response) ;
    }
    public function _post($xcontext,$request,$response)
    {
        $this->callMethod($xcontext,$request,$response) ;
    }
    public function _put($xcontext,$request,$response)
    {
        $this->callMethod($xcontext,$request,$response) ;
    }
    public function _delete($xcontext,$request,$response)
    {
        $this->callMethod($xcontext,$request,$response) ;
    }
    public function _help($xcontext,$request,$response)
    {
        throw new XNotImplemented("Unimplemnt " . get_class($this) ."::" .  __FUNCTION__);
    }
}

