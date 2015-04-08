<?php
require_once("config/used/config_test.php");

class test_assembly implements XAssembly
{
    public function setup()
    {
        $logger = new logger("ut");
        $logger->debug(" test begin ");
        date_default_timezone_set('Asia/Chongqing');
    }
}

