<?php
/**
 * @ingroup utls
 * @brief  HttpResult
 */
class XHttpResult
{
    public $status_code;
    public $http_body;
    public $data;
    public $errno;
    public $errmsg;
    public $usetime;
    public function __construct($code,$body)
    {
        $this->status_code    = $code ;
        $this->http_body    = $body ;
    }
    public function rest_decode()
    {
        $result             = json_decode($this->http_body,true);
        $this->errno        =  3 ;
        $this->errmsg       =  "http code is $code";
        if($result !== null)
        {
            $this->errno  =  $result['errno'];
            $this->errmsg =  $result['errmsg'];
            $this->data   =  $result['data'];
        }
    }
    public function errno2exception()
    {
        if ($this->errno != 0 )
            throw new  RuntimeException($this->errmsg);
    }
    public function isSucess()
    {
        return  $this->errno == 0 ;
    }

}
/**
 * @ingroup utls
 * @brief  Http Call
 */
class XHttpCall
{
    public  $proxy;
    public  $logger;
    public  $host;
    public  $server;
    public  $timeout;
    public  $port;
    private $ch;
    protected $last_resp = null;

    protected function  __construct()
    {
        $this->ch      = curl_init();
    }
    /**
     * @brief 用于本地访问,即访问127.0.0.1
     *
     * @param $host
     * @param $port
     * @param $logname
     *
     * @return
     */
    public static function localIns($host,$port=8360,$logname="sdk_test")
    {
        $ins            = new XHttpCall();
        $ins->host      = $host ;
        $ins->server    = "127.0.0.1" ;
        $ins->logger    =  new XLogger($logname);
        $ins->port      = $port;
        return $ins;
    }
    public function __destruct()
    {
        curl_close($this->ch);
    }
    /**
     * @brief GET 调用
     *
     * @param $url
     * @param $timeout
     *
     * @return
     */
    public function get($url,$timeout=0)
    {
        if($this->port && $this->port != 80){
            $url = "http://{$this->server}:{$this->port}{$url}";
        }else{
            $url = "http://" . $this->server. $url;
        }
        // BUG
        //curl_setopt($this->ch, CURLOPT_HTTPGET, TRUE);
        curl_setopt($this->ch,CURLOPT_CUSTOMREQUEST,"GET");
        $r = $this->callRemote('get',$url,$timeout);
        return $r;
    }
    /**
     * @brief PUT 调用
     *
     * @param $url
     * @param $data
     * @param $timeout
     *
     * @return
     */
    public function put($url,$data,$timeout=0)
    {

        if($this->port && $this->port != 80){
            $url = "http://{$this->server}:{$this->port}{$url}";
        }else{
            $url = "http://" . $this->server. $url;
        }
        curl_setopt($this->ch,CURLOPT_CUSTOMREQUEST,"PUT");
        curl_setopt($this->ch,CURLOPT_HTTPHEADER,array('Content-Length: '.strlen($data)));
        curl_setopt($this->ch,CURLOPT_POSTFIELDS,$data);
        $this->logger->debug("[put] $data");
        return $this->callRemote('put',$url,$timeout);
    }
    /**
     * @brief POST　调用　
     *
     * @param $url
     * @param $data
     * @param $timeout
     *
     * @return
     */
    public function post($url,$data,$timeout=0)
    {
        if(is_array($data)){
            $data = http_build_query($data);
        }
        if($this->port && $this->port != 80){
            $url = "http://{$this->server}:{$this->port}{$url}";
        }else{
            $url = "http://" . $this->server. $url;
        }
        // BUG
        //curl_setopt($this->ch,CURLOPT_POST,1);
        curl_setopt($this->ch,CURLOPT_CUSTOMREQUEST,"POST");
        curl_setopt($this->ch,CURLOPT_HTTPHEADER,array('Content-Length: '.strlen($data)));
        curl_setopt($this->ch,CURLOPT_POSTFIELDS,$data);
        DBC::requireNotNull($this->logger,'logger');
        $this->logger->debug("[post] $url");
        $this->logger->debug("[post] $data");
        return $this->callRemote('post',$url,$timeout);
    }

    /**
     * @brief del 调用
     *
     * @param $url
     * @param $timeout
     *
     * @return
     */
    public function delete($url,$timeout=0)
    {
        if($this->port && $this->port != 80){
            $url = "http://{$this->server}:{$this->port}{$url}";
        }else{
            $url = "http://" . $this->server. $url;
        }
        curl_setopt($this->ch,CURLOPT_CUSTOMREQUEST,"DELETE");
        return $this->callRemote('delete',$url,$timeout);
    }

    public function complex_delete($url,$data,$timeout=0)
    {
        if($this->port && $this->port != 80){
            $url = "http://{$this->server}:{$this->port}{$url}";
        }else{
            $url = "http://" . $this->server. $url;
        }
        curl_setopt($this->ch,CURLOPT_CUSTOMREQUEST,"DELETE");
        curl_setopt($this->ch,CURLOPT_HTTPHEADER,array('Content-Length: '.strlen($data)));
        curl_setopt($this->ch,CURLOPT_POSTFIELDS,$data);
        return $this->callRemote('delete',$url,$timeout);
    }
    private function local_settting($url)
    {

        $host = strstr($url,'/');
        curl_setopt($this->ch, CURLOPT_HEADER, $host);
        return $url;
    }
    private function callRemote($method,$url,$timeout=0)
    {
        $stime=microtime(true);
        $timeout = $timeout > 0 ? $timeout : $this->timeout;
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, array("Host:" .$this->host));
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_PORT, $this->port);
        if(!empty($proxy))
        {
            curl_setopt($this->ch, CURLOPT_PROXY, $this->proxy);
            $this->logger->debug("[proxy] ".$this->proxy);
        }

        $port =  $this->port;
        DBC::requireNotNull($this->logger,'logger');
        $this->logger->debug("[$port:$method,timeout:$timeout(s)] url: $url");

        if (!empty($this->proxy))
        {
            curl_setopt($this->ch, CURLOPT_PROXY, $this->proxy);
        }
        $r          = curl_exec($this->ch);
        $this->last_resp = $r;
        $errono     = curl_errno($this->ch);
        if ($errono !=0 )
        {
            if(TIMEOUT_ERROR == $errono)
            {
                $errMsg = curl_error($this->ch);
                $this->logger->error("$url timeout: ".$errMsg);
            }
            else
            {
                $errMsg = curl_error($this->ch);
                $this->logger->error("$url curlerr: ".$errMsg);
            }
            $this->logger->error("[slow] errmsg: $errMsg, timeout: $timeout(s),  port: $port, method: $method, url: $url ");

            $restResult = new XHttpResult( 0,$r);
            return $restResult;
        }

        $status_code  = curl_getinfo($this->ch,CURLINFO_HTTP_CODE);
        $restResult = new XHttpResult( $status_code,$r);
        $etime      = microtime(true);
        $usetime    = sprintf("%.3f", $etime-$stime);
        $restResult->usetime = $usetime;
        if($usetime > 0.5 )
        {
            $this->logger->error("[slow] usetime: $usetime(s), code: $status_code, timeout: $timeout(s), port: $port, method: $method, url: $url ");
        }
        if ($restResult->isSucess())
        {
            $this->logger->debug("[response] code: $status_code, usetime: $usetime, url:$url ,body: $r ");
        }
        else
        {
            $this->logger->error("[response] code: $status_code, usetime: $usetime, url:$url ,body: $r ");
        }
        return $restResult ;
    }

}


/**
 * @ingroup utls
 * @brief  UnitIntecept
 */
class XUnitTestIntecept extends XInterceptor
{
    public $__response = null;
    public function _before($xcontext,$request,$response)
    {
    }
    public function _after($xcontext,$request,$response)
    {
        $this->__response = null;
        $this->__response = $xcontext;
    }
    public function _exception($e,$xcontext,$request,$response)
    {}
}

/**
 * @ingroup utls
 * @brief XUnitViewIntecept
 */
class XUnitViewIntecept extends XInterceptor
{
    public function _before($xcontext,$request,$response)
    {
        $xcontext->_view= "";
    }
    public function _after($xcontext,$request,$response)
    {
    }
    public function _exception($e,$xcontext,$request,$response)
    {}
}
