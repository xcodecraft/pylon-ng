<?php
require_once "pylon/pylon.php" ;
require_once "config_test.php" ;

XSetting::$logMode   = XSetting::LOG_DEBUG_MODE ;
XSetting::$prjName   = "pylon-ng" ;
XSetting::$logTag    = "pylon-ng" ;
XSetting::$runPath   = "/home/zuowenjian/devspace/pylon-ng/run/test/" ;
XSetting::$assembly  = "test_assembly" ;
date_default_timezone_set('PRC');
XPylon::useEnv() ;
