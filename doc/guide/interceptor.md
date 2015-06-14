# PYLON3.0 拦截器

## 接口

``` php
abstract class XInterceptor
{
    public function _before($xcontext,$request,$response) {}
    public function _after($xcontext,$request,$response) {}
    public function _exception($e,$xcontext,$request,$response) {}
}
```

## 异常处理
###  拦截器调用

``` php

$phpitc = getInterceptor() ;
try {
    $itc->_before() ;
    some_service();
}
catch(Exception $e) {
    $itc->_exception() ;
}
$itc->_after() ;

```

### 多拦截器运行逻辑

``` php

class XInterceptorRuner extends XInterceptor
{
    private $beforedItcs = null ;
    private $allItcs  = null ;
    private $plog     = null ;
    public function __construct($itcs)
    {
        $this->allItcs     = $itcs ;
        $this->beforedItcs = array() ;
        $this->plog     = new Logger("_pylon");
    }
    public function _before($xcontext,$request,$response)
    {
        foreach($this->allItcs as $i)
        {
            array_unshift($this->beforedItcs,$i) ;
            $i->_before($xcontext,$request,$response);
        }

    }
    public function _exception($e,$xcontext,$request,$response)
    {
            self::defaultException($this->plog,$e,$response) ;
            self::doException($this->beforedItcs,$e,$xcontext,$request,$response) ;
    }

    static private function doException($intcs,$e,$xcontext,$request,$response)
    {
            foreach( $intcs  as $itc )
            {
                $end = $itc->_exception($e,$xcontext,$request,$response) ;
                if ($end === true) break ;
            }
    }


    public function _after($xcontext,$request,$response)
    {
        $unAfterItcs = $this->beforedItcs ;
        foreach( $this->beforedItcs as $itc )
        {
            try
            {
                $itc->_after($xcontext,$request,$response) ;
                array_shift($unAfterItcs) ;
            }
            catch(Exception $e)
            {
                self::defaultException($this->plog,$e,$response) ;
                self::doException($unAfterItcs,$e,$xcontext,$request,$response) ;
            }
        }

    }

    private function defaultException($plog,$e,$response)
    {
        $plog->error(get_class($e) ." : " .$e->getMessage());
        $plog->error(XExceptionUtls::simple_trace($e));
        if (is_a($e,XLogicException)  || is_a($e,XRuntimeException))
        {
            $response->error($e->getMessage(),$e->getCode(),$e->status_code);
        }
        else
        {
            $response->error($e->getMessage(),XErrCode::SYS_UNKNOW,500);
        }
    }
}
```
