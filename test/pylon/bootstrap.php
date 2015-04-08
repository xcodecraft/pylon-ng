<?php
require_once "pylon/pylon.php" ;

XSetting::$logMode   = XSetting::LOG_DEBUG_MODE ;
XSetting::$prjName   = "pylon" ;
XSetting::$logTag    = "pylon" ;
XSetting::$runPath   = "/home/zuowenjian/devspace/pylon/run/test/" ;
XSetting::$assembly  = "test_assembly" ;
date_default_timezone_set('PRC');
XPylon::useEnv() ;
