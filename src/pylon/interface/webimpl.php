<?php

class XHtmlResp   implements XResponse
{

    public $status_code = 500 ;
    protected $root        = "" ;
    protected $jumpURL     = null ;
    public function setRoot($root)
    {
        $this->root = $root ;
    }
    public function location($url)
    {
        $this->jumpURL = $url ;
    }
    public function tpl($_xc,$file)
    {
        $this->status_code = 500 ;
        $file = $this->root . "/" . $file ;
        if(file_exists($file))
        {
            include($file);
            $this->status_code = 200 ;
        }
        else
        {
            throw new XNotFound($file) ;
        }
    }
    public function send($logger,$set_header=true)
    {

        if($set_header === true)
        {
            if ($this->jumpURL != null)
            {

                header("location:  " . $this->jumpURL );

            }
            else{
                PYL_HttpHeader::out_header($this->status_code);
            }
        }
    }
    public function error($errmsg,$errno = XErrCode::BIZ_UNKNOW,$status_code = 500)
    {
        $this->status_code = $status_code ;
    }
}
class XRestResp implements XResponse
{
    public $status_code = 500 ;
    public $errno     = 1 ;
    public $errmsg    = "unknown";
    private $data      = array() ;
    public function __construct()
    {
    }
    public function getData()
    {
        return  $this->data;

    }
    /**
     * @brief  设置错误
     *
     * @param $errMsg
     * @param $errNo
     * @param $status_code
     *
     * @return
     */
    public function error($errmsg,$errno = XErrCode::BIZ_UNKNOW,$status_code = 500)
    {
        $this->errno            = $errno ;
        $this->errmsg           = $errmsg ;
        $this->status_code      = $status_code ;
    }
    /**
     * @brief 设置成功
     *
     * @param $data  需要是数组
     * @param $status_code
     *
     * @return
     */
    public function success($data,$status_code = 200 )
    {
        $this->errno        = 0  ;
        $this->errmsg       = "" ;
        $this->status_code    = $status_code ;
        $this->data         = $data ;
    }
    public function is_success()
    {
        return  $this->errno == 0 ;
    }
    public function send($logger,$set_header=true)
    {

        if($set_header === true)
            PYL_HttpHeader::out_header($this->status_code);
        if($this->errno != 0 )
        {
            $datas['errno']     = $this->errno ;
            $datas['errmsg']    = $this->errmsg ;
            $datas['data']      = $this->data;
        }
        else
        {
            $datas = $this->data ;
        }
        $json_data          = json_encode($datas);

        if($this->errno == 0 )
        {
            $logger->info("status code: " . $this->status_code , "response" );
            $logger->info($json_data , "response");
        }
        else
        {
            $logger->error("status code: " . $this->status_code , "response" );
            $logger->error($json_data , "response");
        }
        echo $json_data;
    }
}
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

