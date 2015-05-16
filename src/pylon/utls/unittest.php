<?php

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
