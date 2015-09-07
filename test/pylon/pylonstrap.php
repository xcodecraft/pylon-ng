<?php
require_once("config/used/config_test.php");

$logger = new logger("ut");
$logger->debug(" test begin ");
date_default_timezone_set('Asia/Chongqing');
if (XEnv::get('DEBUG') == "TRUE" )
    XHttpCaller::failDebug(true) ;
