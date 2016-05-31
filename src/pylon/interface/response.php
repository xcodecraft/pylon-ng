<?php

abstract class XBaseResp  implements  XResponse 
{

    public function out($msg,$code=200)
    {
        $this->statusCode = $code ;
        echo $msg ;
    }

    public function send($logger,$set_header=true)
    {

        if($set_header === true)
        {
            foreach($this->headers as $name => $value)
            {
                header("$name: " . $value);
            }

            if ($this->jumpURL != null)
            {

                header("location:  " . $this->jumpURL );

            }
            else{
                PYL_HttpHeader::out_header((int)$this->statusCode);
            }
        }
    }
    public function error($errmsg,$errno = XErrCode::UNKNOW,$statusCode = 510)
    {
        $this->statusCode = $statusCode ;
    }
    public function exception($ex)
    {
        $code = $ex->status_code ;
        if(empty($code) )
        {
            $code = $this->status_code ;
        }
        if(!empty($ex->headers))
        {
            $this->headers = $ex->headers ;
        }
        $this->statusCode = $code ;
    }

}


class XEchoResp   extends XBaseResp
{
    public $statusCode = 500 ;
    public $headers     = array() ;
    protected $jumpURL  = null ;
    public function location($url)
    {
        $this->jumpURL = $url ;
    }
}
class XHtmlResp   extends XBaseResp
{
    public $statusCode = 500 ;
    public $headers     = array() ;
    protected $root     = "" ;
    protected $jumpURL  = null ;
    public function setRoot($root)
    {
        $this->root = $root ;
    }
    public function location($url)
    {
        $this->jumpURL = $url ;
    }
    public function tpl($_xc,$file,$extract=false)
    {
        if ($extract)
        {
            extract($_xc->toArr());
        }
        $this->statusCode = 500 ;
        $file = $this->root . "/" . $file ;
        if(file_exists($file))
        {
            include($file);
            $this->statusCode = 200 ;
        }
        else
        {
            throw new XNotFound($file) ;
        }
    }
}
class XRespFail
{
    public $code        = 500 ;
    public $message     = "unset response data" ;
    public $type        = "" ;
    public $sub_code    = 100;
    public $prompt_info = "" ;
    public $prompt_type = "" ;

    public function setException($ex)
    {
        $code = $ex->status_code ;
        if(empty($code) )
        {
            $code = $this->code ;
        }
        $this->code     = $code ;
        $this->sub_code = $ex->getCode();
        $this->message  = $ex->getMessage();
        $this->type     = get_class($ex) ;

    }
    public function setError($code,$sub_code,$message)
    {
        $this->code     = $code ;
        $this->sub_code = $sub_code ;
        $this->message  = $message ;
    }
    public function noFail()
    {
        $this->code     = 0 ;
    }
    public function isFail()
    {
        return $this->code != 0 ;
    }
}

class XRestResp implements XResponse
{

    const  RESPONSE_TAG = "response" ;
    public $status_code = 500 ;
    public $headers     = array() ;
    public $error       = null ;
    public $jsonpEnable = false ;
    public $jsonpCall   = "";
    private $data       = array() ;
    public function __construct()
    {
        $this->error = new XRespFail();
    }
    public function jsonp($fun)
    {
        $this->jsonpEnable     =  true ;
        $this->jsonpCall =  $fun ;
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
    public function error($errmsg,$errno = XErrCode::UNKNOW,$status_code = 510)
    {
        $this->error->setError($status_code,$errno,$errmsg) ;
        $this->status_code = $status_code ;
    }
    public function errorPrompt($info,$type="")
    {
        $this->error->prompt_info = $info ;
        $this->error->prompt_type = $type ;
    }
    public function exception($ex)
    {
        $this->error->setException($ex) ;
        if(!empty($ex->headers))
        {
            $this->headers = $ex->headers ;
        }

        $this->status_code        = $this->error->code ;
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
        $this->status_code = $status_code ;
        $this->data        = $data ;
        $this->error->noFail();

    }

    public function is_success()
    {
        return  !$this->error->isFail();
    }
    public function send($logger,$set_header=true)
    {

        if($set_header === true)
        {
            DBC::requireNotNull($this->status_code);
            foreach($this->headers as $name => $value)
            {
                header("$name: " . $value);
            }
            PYL_HttpHeader::out_header((int)$this->status_code);
            header('Content-type: application/json');
        }

        $outdata = "";
        if($this->error->isFail())
        {
            $data['error'] = get_object_vars($this->error) ;
            $outdata       = json_encode($data);
            $logger->error("status code: " . $this->status_code , RESPONSE_TAG );
            $logger->error($outdata, RESPONSE_TAG );
        }
        else
        {
            $outdata = json_encode($this->data);
            $logger->info("status code: " . $this->status_code , RESPONSE_TAG );
            $logger->info($outdata, RESPONSE_TAG );
        }
        if ($this->jsonpEnable == true )
        {
            echo  $this->jsonpCall . "($outdata)" ;
        }
        else
        {
            echo $outdata;
        }
    }

}
