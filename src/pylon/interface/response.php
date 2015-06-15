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
        $this->statusCode = $ex->status_code ;
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
        $err['code']       = $status_code ;
        $err['message']    = $errmsg ;
        $err['sub_code']   = $errno ;
        $this->error       = $err ;
        $this->status_code = $status_code ;
    }
    public function exception($ex)
    {
        $err['code']       = $ex->status_code ;
        $err['sub_code']   = $ex->getCode();
        $err['message']    = $ex->getMessage();
        $err['type']       = get_class($ex) ;
        $this->error       = $err ;
        $this->status_code = $ex->status_code ;
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

    }
    public function is_success()
    {
        return  $this->error == null ;
    }
    public function send($logger,$set_header=true)
    {
        if($set_header === true)
        {
            PYL_HttpHeader::out_header($this->status_code);
            header('Content-type: application/json');
        }

        $outdata = "";
        if($this->error == null )
        {
            $logger->info("status code: " . $this->status_code , "response" );
            $logger->info($outdata, "response");
            $outdata = json_encode($this->data);
        }
        else
        {
            $data['error'] = $this->error ;
            $outdata = json_encode($data);
            $logger->error("status code: " . $this->status_code , "response" );
            $logger->error($outdata, "response");
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
