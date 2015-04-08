<?php
include "pylon/pylon.php" ;

XSetting::$logMode   = XSetting::LOG_DEBUG_MODE ;
// XSetting::$logMode   = XSetting::LOG_BENCHMARK_MODE ;
XSetting::$prjName   = "pylon" ;
XSetting::$logTag    = XSetting::ensureEnv("USER") ;
XSetting::$runPath   = XSetting::ensureEnv("RUN_PATH") ;
XSetting::$assembly  = "ApiAssembly" ;

XPylon::serving();
