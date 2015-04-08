<?php
function auto_load($name)
{
    print "[[ $name  ]]" ;
}
function auto_load2($name)
{
    print "[[ $name   2 ]]" ;
}
class AutoloadTest extends PHPUnit_Framework_TestCase
{

    public function testLoad()
    {
//        spl_autoload_register(auto_load);
//        spl_autoload_register(auto_load2);
//        new HelloObject;
    }
}
