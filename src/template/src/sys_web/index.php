<?php
include "pylon/pylon.php" ;

XSetting::$logMode  = XSetting::LOG_DEBUG_MODE ;
//XSetting::$logMode   = XSetting::LOG_BENCHMARK_MODE ;
XSetting::$prjName   = "%{PRJ_NAME}" ;
XSetting::$logTag    = XSetting::ensureEnv("USER") ;
XSetting::$runPath   = XSetting::ensureEnv("RUN_PATH") ;
XSetting::$respClass = "XHtmlResp" ;
XPylon::serving();
