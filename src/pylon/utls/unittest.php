<?php
class RestResult
{
    static public function ok($response)
    {
        if($response->status_code == 201 || $response->status_code == 200 )
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
        $data = json_decode($response->body(),true) ;
        if(isset($data['errno']))
        {
            return $data ;

        }
        return  null ;
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
