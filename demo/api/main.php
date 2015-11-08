<?php
include "pylon/pylon.php" ;

XSetting::$logMode  = XSetting::LOG_DEBUG_MODE ;
//XSetting::$logMode   = XSetting::LOG_BENCHMARK_MODE ;
XSetting::$prjName    = "pylon-ng" ;
XSetting::$logTag     = XSetting::ensureEnv("USER") ;
XSetting::$runPath    = XSetting::ensureEnv("RUN_PATH") ;
XSetting::$bootstrap  = "api/bootstrap.php" ;
XSetting::$respInsFun = function ($uri) {  return new XRestResp(); };
XPylon::serving();
