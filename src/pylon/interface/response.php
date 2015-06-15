<?php
class XHtmlResp   implements XResponse
{

    public $statusCode = 500 ;
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
    public function tpl($_xc,$file)
    {
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
    public function send($logger,$set_header=true)
    {

        if($set_header === true)
        {
            if ($this->jumpURL != null)
            {

                header("location:  " . $this->jumpURL );

            }
            else{
                PYL_HttpHeader::out_header($this->statusCode);
            }
        }
    }
    public function error($errmsg,$errno = XErrCode::BIZ_UNKNOW,$statusCode = 500)
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
        $this->statusCode = $code ;
    }
}
class RespFail
{
    public $code     = 500 ;
    public $message  = "unset response data" ;
    public $type     = "" ;
    public $sub_code = 100;

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
    public $status_code = 500 ;
    public $error       = null ;
    public $jsonpEnable = false ;
    public $jsonpCall   = "";
    private $data       = array() ;
    public function __construct()
    {
        $this->error = new RespFail();
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
    public function error($errmsg,$errno = XErrCode::BIZ_UNKNOW,$status_code = 500)
    {
        $this->error->setError($status_code,$errno,$errmsg) ;
        $this->status_code = $status_code ;
    }
    public function exception($ex)
    {
        $this->error->setException($ex) ;
        $this->status_code =  $this->error->code ;
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
            PYL_HttpHeader::out_header($this->status_code);
            header('Content-type: application/json');
        }

        $outdata = "";
        if($this->error->isFail())
        {
            $data['error'] = get_object_vars($this->error) ;
            $outdata = json_encode($data);
            $logger->error("status code: " . $this->status_code , "response" );
            $logger->error($outdata, "response");
        }
        else
        {
            $logger->info("status code: " . $this->status_code , "response" );
            $logger->info($outdata, "response");
            $outdata = json_encode($this->data);
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
