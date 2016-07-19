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
    public  $exception  = null ;
    public  $bHttps     = null;

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
    static public function localSvc($domain,$port=8360,$caller="unknow",$bHttps=null)
    {
        $conf           = new XHttpConf ;
        $conf->domain   = $domain ;
        $conf->server   = "127.0.0.1";
        $conf->logger   = XLogkit::logger("net") ;
        $conf->port     = $port ;
        $conf->bHttps   = $bHttps;
        $conf->caller   = $caller ;
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
    public function conf($domain,$port,$caller="unknow",$proxy=null,$logger=null,$bHttps=null)
    {
        $this->domain  = $domain;
        if($logger == null)
        {
            $logger = XLogkit::logger("net");
        }
        $this->proxy   = $proxy;
        $this->logger  = $logger;
        $this->caller  = $caller;
        $this->bHttps  = $bHttps;
        $this->port    = $port ;
    }
}

class XRestResult
{
    public static $failException = false  ;

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
        if (static::$failException)
        {
            throw new XRuntimeException($response->statusCode,$response->body()) ;

        }
        return  null ;
    }
    static public function fail($response)
    {
        DBC::requireNotNull($response);

        $data = json_decode($response->body(),true) ;
        if(isset($data['error']))
        {
            return $data['error'] ;

        }
        return  null ;
    }
    static public function matchFail($response, $statusCode,$errno=null)
    {
        if ($response->statusCode == $statusCode )
        {
            $err = static::fail($response) ;
            if ($errno == null | $err['sub_code'] == $errno) {
                return  true ;
            }
            else
            {
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
    protected static  $debug_echo = false ;

    public static function failDebug($on) {
        static::$debug_echo  = $on ;
    }

    public function  __construct($conf)
    {
        assert($conf != null);
        $this->conf        = $conf;
        $this->ch          = curl_init();
        $this->headers     = array();
        $this->keepHeaders = array();
    }
    public function __destruct()
    {
        curl_close($this->ch);
    }

    private function makeURL($url)
    {
        $server = $this->conf->domain ;
        if(! empty($this->conf->server) )
        {
            $server = $this->conf->server ;
        }
        if(!is_null($this->conf->bHttps))
        {
            return $url = "https://" . $server. $url;
        }
        if($this->conf->port && $this->conf->port != 80)
        {
            $url = "http://$server:{$this->conf->port}{$url}";
        }
        else{
            $url = "http://" . $server. $url;
        }
        return $url ;
    }

    public function useagent($data)
    {
        curl_setopt($this->ch, CURLOPT_USERAGENT, $data);

    }

    public function setFollowLocation($bFollow)
    {
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, $bFollow);
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
        $this->bindData($data) ;
        curl_setopt($this->ch,CURLOPT_CUSTOMREQUEST,"PUT");
        $url = $this->makeURL($url);
        return $this->callRemote('PUT',$url,$timeout);
    }
    private function bindData($data)
    {
        if(is_array($data)){
            $data = http_build_query($data);
        }
        array_push($this->headers,'Content-Length: '.strlen($data)) ;
        curl_setopt($this->ch,CURLOPT_POSTFIELDS,$data);
        $this->call_data = $data;
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
        $this->bindData($data) ;
        $url = $this->makeURL($url);
        curl_setopt($this->ch,CURLOPT_CUSTOMREQUEST,"POST");
        return $this->callRemote('POST',$url,$timeout);
    }
    public function setHeader($value,$mutiRequ=true)
    {
        if( $mutiRequ)
        {
            array_push($this->keepHeaders,$value) ;
        }
        else
        {
            array_push($this->headers,$value) ;
        }
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
        {
            return ;
        }
        $this->conf->logger->$level($msg,$event);
    }
    private function callRemote($method,$url,$timeout=0)
    {
        $url    = $this->bindCaller($url);
        array_push($this->headers,"Host:" . $this->conf->domain );
        //TID
        if(class_exists('XTid', false)){
            array_push($this->headers,'PYLON-TID: ' . XTid::get()) ;
        }
        if(!empty($this->keepHeaders))
        {
            $this->headers = array_merge($this->headers, $this->keepHeaders );
        }

        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->headers);
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
        if(!empty($this->conf->bHttps))
        {
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false);
            $this->log("debug","[https] ",$event);
        }

        $port      = $this->conf->port;
        $this->log("info",$reqestMsg,$event);
        $this->log("debug","curl -X $method -H\"Host:{$this->conf->domain}\" \"$url\" -d {$this->call_data} ",$event);
        $stime      = microtime(true);
        $r          = curl_exec($this->ch);
        $errono     = curl_errno($this->ch);
        $statusCode = curl_getinfo($this->ch,CURLINFO_HTTP_CODE);
        $response   = new XHttpResponse($statusCode,$r);
        $etime      = microtime(true);
        $usetime    = sprintf("%.3f", $etime-$stime);

        if ($errono !=0  || $statusCode > 300)
        {
            $errMsg = curl_error($this->ch);
            $body   = lineBody($response->body()) ;
            if (static::$debug_echo)
            {
                echo "$curl  failed! $errmsg,$event" ;
                echo "\n" ;
                echo "[reqest,$port:$method,timeout:$timeout_info] url: $url" ;
                echo "\n" ;
                echo "curl -X $method -H\"Host:{$this->conf->domain}\" \"$url\" " ;
                echo "\n" ;
                echo "[respons: {$response->statusCode} ($usetime s)] body: $body" ;
                echo "\n" ;
            }
            $this->log('error',"$url curlerr: ".$errMsg,$event);
            $this->log("error","[reqest,$port:$method,timeout:$timeout_info] url: $url",$event);
            $this->log("error","curl -X $method -H\"Host:{$this->conf->domain}\" \"$url\" ",$event);
            $this->log("error","[respons: {$response->statusCode} ($usetime s)] body: $body",$event);
            if(!empty($this->conf->exception))
            {
                $cls = $this->conf->exception ;
                throw new $cls("curl $rul failed!");
            }
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
        $this->headers = array();

        $this->call_data  = null ;;
        return $response ;
    }


}

class XHttpSimulator
{
    static public function setup($bootstrap)
    {
        static $isSetup = false ;
        if ($isSetup) 
        {
            return  ;
        }
        $prjRoot = XSetting::ensureEnv('PRJ_ROOT') ;
        require "$prjRoot/$bootstrap" ;
        $isSetup = true ;
    }
    public function  debug()
    {
        $this->debugOn = true ;
    }
    private function headerSet($key,$prefix,$value)
    {
        $len = strlen($prefix) ;
        if(strncmp($value,$prefix,$len) == 0)
        {
            $value         = strpbrk($value, ":");
            $_SERVER[$key] = trim(strpbrk($value, " "));
        }

    }
    public function __construct()
    {
        $this->debugOn = false ;
    }
    public function  setHeader($value)
    {
        $this->headerSet("HTTP_USER_AGENT","User-Agent",$value) ;
        $this->headerSet("HTTP_NETWORK","Network",$value) ;
        $this->headerSet("PHP_AUTH_DIGEST","Authorization",$value) ;
    }
    public function post($uri,$data)
    {
        $_SERVER['REQUEST_URI']    = $uri ;
        $_SERVER['REQUEST_METHOD'] = "POST" ;
        $_REQUEST                  = $data;
        $_POST                     = $data;

        $query = parse_url($uri, PHP_URL_QUERY);
        $queryArr = explode('&',$query) ;
        foreach($queryArr as $q)
        {
            list($key,$val) = explode('=',$q) ;
            $_REQUEST[$key] = $val ;
        }


        ob_start() ;
        XPylon::serving(false);
        $data = ob_get_contents();
        ob_end_clean();
        if ($this->debugOn)
        {
            echo $data;
        }
        $response   = new XHttpResponse(200,$data);
        $_REQUEST                  = array();
        $_POST                     = array();
        return $response;


    }
    public function get($uri)
    {
        $_SERVER['REQUEST_URI']    = $uri ;
        $_SERVER['REQUEST_METHOD'] = "GET" ;
        $_REQUEST                  = array() ;
        $_GET                      = array() ;

        $query = parse_url($uri, PHP_URL_QUERY);
        $queryArr = explode('&',$query) ;
        foreach($queryArr as $q)
        {
            list($key,$val) = explode('=',$q) ;
            $_REQUEST[$key] = $val ;
            $_GET[$key]     = $val ;
        }

        ob_start() ;
        XPylon::serving(false);
        $data = ob_get_contents();
        ob_end_clean();
        if ($this->debugOn)
        {
            echo $data;
        }

        $response   = new XHttpResponse(200,$data);
        return $response;

    }
}
