<?php

class PylonTest extends PHPUnit_Framework_TestCase
{

    public function testAutoload()
    {
        
        $result = class_exists("NOT_THIS_CLASS") ;
        $this->assertTrue(!$result);
    }

}
