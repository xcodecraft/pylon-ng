<?php
class XPhpunitTest extends PHPUnit_Framework_TestCase
{
    public function testLogin()
    {
        $request['name'] = 'world'; //request
        $response = XActionTester::runAction('login',$request);
        $this->assertTrue($response->echo=='hello,world');
    }
}
