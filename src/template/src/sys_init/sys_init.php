<?php
include "pylon/pylon.php" ;
XSetting::$logMode   = XSetting::LOG_DEBUG_MODE ;
XSetting::$prjName   = "%{PRJ_NAME}" ;
XSetting::$logTag    = XSetting::ensureEnv("USER") ;
XSetting::$runPath   = XSetting::ensureEnv("RUN_PATH") ;
XPylon::useEnv() ;


$aps = XAppSession::begin();
$heroB          = Hero::bizCreate("erge") ;
$aps->commit() ;
