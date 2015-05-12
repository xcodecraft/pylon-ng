<?php
require_once "pylon/pylon.php" ;
require_once "config_test.php" ;

XSetting::$logMode   = XSetting::LOG_DEBUG_MODE ;
XSetting::$prjName   = "pylon-ng" ;
XSetting::$logTag    = "pylon-ng" ;
XSetting::$runPath   = XSetting::ensureEnv("RUN_PATH") ;
XSetting::$bootstrap = "pylonstrap.php" ;
date_default_timezone_set('PRC');
XPylon::useEnv() ;
