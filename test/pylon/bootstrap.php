<?php
require_once "pylon/pylon.php" ;
// require_once "config_test.php" ;

XSetting::$logMode   = XSetting::LOG_DEBUG_MODE ;
XSetting::$version   = "v2" ;
XSetting::$prjName   = "pylon-ng" ;
XSetting::$logTag    = "pylon-ng" ;
XSetting::$runPath   = XSetting::ensureEnv("RUN_PATH") ;
XSetting::$bootstrap = "pylonstrap.php" ;
date_default_timezone_set('PRC');
XPylon::useEnv() ;
XHttpSimulator::setup("demo/api/bootstrap.php") ;
