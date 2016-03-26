
# 框架日志记录设定

``` php 
  XSetting::$logMode  = XSetting::LOG_DEBUG_MODE ; //调试模式
  XSetting::$logMode  = XSetting::LOG_ONLINE_MODE ; //生产模式
  XSetting::$logMode  = XSetting::LOG_BENCHMARK_MODE ; //性能测试模式
  
  //单独设定 日志输出级别
  XSetting::logLevel($name,$level) ;
  XSetting::logLevel("_pylon",XSetting::LOG_DEBUG_LEVEL) ;
```




#扩展

除了框架defualt 进行日志记录外，你还可以自己扩展日志对象来记录。
``` php
  interface XIlogger
  {
      function debug($msg,$event = null ) ;
      function info($msg,$event = null ) ;
      function warn($msg,$event = null ) ;
      function error($msg,$event = null ) ;

  } 
  class MyLogger implements XIlogger 
  {
      public function __construct($name) {...}
      ......
  }
  XSetting::$logCls =  MyLogger ; 
```
