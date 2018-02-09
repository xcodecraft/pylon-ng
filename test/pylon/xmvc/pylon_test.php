<?php

use PHPUnit\Framework\TestCase;

class PylonTest extends TestCase
{

    public function testAutoload()
    {

        $result = class_exists("NOT_THIS_CLASS") ;
        $this->assertTrue(!$result);
    }

}
