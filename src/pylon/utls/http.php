<?php
/**
 * @brief  Conf 对象
 */
class XHttpConf
{
    /**
     * @brief 代理地址: 例如 "127.0.0.1:8086"
     */
    public  $proxy      = null;
    public  $logger     = null;
    public  $domain     = null;
    public  $timeout    = 2000;  //毫秒
    public  $gzip       = false;
    public  $port       = 8360;
    public  $server     = null;
    public  $caller     = "none";
    public  $exception  = false;
    public function __construct()
    {
    }

    /**
     * @brief  常用于本机测试
     *
     * @param $domain
     * @param $logger
     * @param $caller
     *
     * @return
     */
    static public function localSvc($domain,$port=8360,$caller="unknow")
    {
        $conf           = new XHttpConf ;;
        $conf->domain   = $domain ;
        $conf->server   = "127.0.0.1";
        $conf->logger   = XLogkit::logger("net") ;
        $conf->port     = $port ;
        return $conf;
    }
    /**
     * @brief
     *
     * @param $domain
     * @param $logger
     * @param $proxy
     * @param $caller
     *
     * @return
     */
    public function conf($domain,$port,$caller="unknow",$proxy=null,$logger=null)
    {
        $this->domain  = $domain;
        if($logger == null)
        {
            $logger = XLogkit::logger("net");
        }
        $this->proxy   = $proxy;
        $this->logger  = $logger;
        $this->caller  = $caller;
    }
}

class XRestResult
{
    static public function ok($response)
    {
        if($response->statusCode == 201 || $response->statusCode == 200 )
        {

            $data = json_decode($response->body(),true) ;
            if(! isset($data['errno']))
            {
                return $data ;

            }
        }
        return  null ;
    }
    static public function fail($response)
    {
        DBC::requireNotNull($response);

        $data = json_decode($response->body(),true) ;
        if(isset($data['errno']))
        {
            return $data ;

        }
        return  null ;
    }
    static public function matchFail($response, $statusCode,$errno=null)
    {
        if ($response->statusCode == $statusCode )
        {
            $err = self::fail($response) ;
            if ($errno == null | $err['errno'] == $errno) {
                return  true ;

            }

        }
        return  false;
    }

}

/**
 * @brief  接口调用的返回对象
 */
class XHttpResponse
{
    public $statusCode;
    public $rawBody;
    public function __construct($code,$body)
    {
        $this->statusCode = $code;
        $this->rawBody    = $body;
    }
    public function body()
    {
        return $this->rawBody ;
    }
}

function lineBody($body)
{
    return  str_replace(array("\r\n","\n","\r"),"|",$body);
}

/**
 * @brief
 */
class XHttpCaller
{
    private $ch;
    private $conf;

    public function  __construct($conf)
    {
        assert($conf != null);
        $this->conf    = $conf;
        $this->ch      = curl_init();
    }
    public function __destruct()
    {
        curl_close($this->ch);
    }

    private function makeURL($url)
    {
        $server = $this->conf->domain ;
        if(! empty($this->conf->server) )$server = $this->conf->server ;
        if($this->conf->port && $this->conf->port != 80){
            $url = "http://$server:{$this->conf->port}{$url}";
        }else{
            $url = "http://" . $server. $url;
        }
        return $url ;
    }
    /**
     * @brief GET 调用
     *
     * @param $url
     * @param $timeout ms
     *
     * @return
     */
    public function get($url,$timeout=0)
    {
        $url = $this->makeURL($url);
        curl_setopt($this->ch,CURLOPT_CUSTOMREQUEST,"GET");
        $r   = $this->callRemote('GET',$url,$timeout);
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
        if(is_array($data)){
            $data = http_build_query($data);
        }
        $url = $this->makeURL($url);
        curl_setopt($this->ch,CURLOPT_CUSTOMREQUEST,"PUT");
        curl_setopt($this->ch,CURLOPT_HTTPHEADER,array('Content-Length: '.strlen($data)));
        curl_setopt($this->ch,CURLOPT_POSTFIELDS,$data);
        $this->call_data = $data;
        return $this->callRemote('PUT',$url,$timeout);
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
        $url = $this->makeURL($url);
        curl_setopt($this->ch,CURLOPT_CUSTOMREQUEST,"POST");
        curl_setopt($this->ch,CURLOPT_HTTPHEADER,array('Content-Length: '.strlen($data)));
        curl_setopt($this->ch,CURLOPT_POSTFIELDS,$data);
        $this->call_data  = $data ;
        return $this->callRemote('POST',$url,$timeout);
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
        $url = $this->makeURL($url);
        curl_setopt($this->ch,CURLOPT_CUSTOMREQUEST,"DELETE");
        return $this->callRemote('DELETE',$url,$timeout);
    }

    private function bindCaller($url)
    {
        if(strstr($url,'?'))
        {
            $url = "$url&_caller=" . $this->conf->caller ;
        }
        else
        {
            $url = $url . "?_caller=" . $this->conf->caller;
        }
        return $url;
    }
    private function log($level,$msg,$event)
    {
        if(empty($this->conf->logger))
            return ;
        $this->conf->logger->$level($msg,$event);
    }
    private function callRemote($method,$url,$timeout=0)
    {/*{{{*/
        $url    = $this->bindCaller($url);
        $header_arr = array("Host:" . $this->conf->domain);
        //TID
        if(class_exists('XTid', false)){
            $header_arr[] = 'PYLON-TID: ' . XTid::get();
        }

        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header_arr);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($this->ch, CURLOPT_URL,             $url);
        curl_setopt($this->ch, CURLOPT_PORT,            $this->conf->port);

        //cURL小于7.16.2版本，不支持毫秒超时
        if ($this->conf->timeout_ms > 0 && ! defined('CURLOPT_TIMEOUT_MS'))
        {
            $this->conf->timeout_ms = null;
            $this->log('error',"TIMEOUT_MS need cURL 7.16.2.", '');
        }

        $timeout = $timeout > 0 ? $timeout : $this->conf->timeout;

        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT_MS, $this->conf->timeout_ms);
        curl_setopt($this->ch, CURLOPT_TIMEOUT_MS, $this->conf->timeout_ms);
        // curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        // curl_setopt($this->ch, CURLOPT_TIMEOUT, $timeout);
        $timeout_info = $timeout.'(ms)';

        if ($this->conf->gzip)
        {
            curl_setopt($this->ch, CURLOPT_ENCODING, 'gzip,deflate');
        }

        $reqestMsg = "[ reqest,$port:$method,timeout:$timeout_info] url:$url , data:{$this->call_data}";
        if(!empty($this->conf->proxy))
        {
            curl_setopt($this->ch, CURLOPT_PROXY, $this->conf->proxy);
            $this->log("debug","[proxy] ".$this->conf->proxy,$event);
        }

        $port      = $this->conf->port;
        $this->log("info",$reqestMsg,$event);
        $this->log("debug","curl -X $method -H\"Host:{$this->conf->domain}\" \"$url\" -d {$this->call_data} ",$event);
        $stime       = microtime(true);
        $r           = curl_exec($this->ch);
        $errono      = curl_errno($this->ch);
        $statusCode = curl_getinfo($this->ch,CURLINFO_HTTP_CODE);
        $response    = new XHttpResponse($statusCode,$r);
        $etime       = microtime(true);
        $usetime     = sprintf("%.3f", $etime-$stime);

        if ($errono !=0  || $statusCode > 300)
        {
            $errMsg = curl_error($this->ch);
            $body   = lineBody($response->body()) ;
            $this->log('error',"$url curlerr: ".$errMsg,$event);
            $this->log("error","[reqest,$port:$method,timeout:$timeout_info] url: $url",$event);
            $this->log("error","curl -X $method -H\"Host:{$this->conf->domain}\" \"$url\" ",$event);
            $this->log("error","[respons: {$response->statusCode} ($usetime s)] body: $body",$event);
        }
        else {
            $body   = lineBody($response->body()) ;
            $this->log("info","[respons: {$response->statusCode} ($usetime s)] body: $body",$event);
        }

        if ($usetime > 0.5)
        {
            $slowmsg = "[slow] usetime: $usetime(s), code: $statusCode, timeout: $timeout_info, port: $port, method: $method, url: $url ";
            $this->log('warn', $slowmsg, $event);
        }

        $this->call_data  = null ;;
        return $response ;
    }/*}}}*/


}

