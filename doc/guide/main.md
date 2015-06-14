# PYLON  WEB 框架

## WEB入口
``` php

include "pylon/pylon.php" ;

XSetting::$logMode   = XSetting::LOG_DEBUG_MODE ;
XSetting::$prjName   = "pylon" ;
XSetting::$logTag    = XSetting::ensureEnv("USER") ;
XSetting::$runPath   = XSetting::ensureEnv("RUN_PATH") ;
XSetting::$respClass = "XHtmlResp" ;
XPylon::serving();

```

## 测试 入口
``` php
require_once "pylon/pylon.php" ;
XSetting::$logMode   = XSetting::LOG_DEBUG_MODE ;
XSetting::$prjName   = "<project>" ;
XSetting::$logTag    = "<project>" ;
XSetting::$runPath   = XSetting::ensureEnv("RUN_PATH") ;
XSetting::$bootstrap = "pylonstrap.php" ;
XPylon::useEnv() ;
```
