<?php
class XBoxTest extends PHPUnit_Framework_TestCase
{

    public function testA()
    {
        XBox::regist("a",1,__METHOD__);
        $this->assertEquals(XBox::get("a"),1);
        $this->assertEquals(XBox::get("A"),null);

        XBox::regist("a",2,__METHOD__,"/my");
        $this->assertEquals(XBox::get("a","/my"),2);
        $this->assertEquals(XBox::get("a","/you"),1);

        XBox::regist("a",3,__METHOD__,"/my/home");
        $this->assertEquals(XBox::get("a","/my/home"),3);
        $this->assertEquals(XBox::get("a","/my"),2);
        $this->assertEquals(XBox::get("a","/my/room"),2);


        XBox::regist("b",1,__METHOD__);
        XBox::clean("a");
        $this->assertEquals(XBox::get("a"),null);
        $this->assertEquals(XBox::get("b"),1);

        XBox::clean();
        XBox::regist("a",1,__METHOD__);
        XBox::regist("a",2,__METHOD__,"/my");
        XBox::regist("a",3,__METHOD__,"/my/home");
        $a_objs = XBox::space_objs("a");
        $b_objs["/"] = 1 ;
        $b_objs["/my"] = 2 ;
        $b_objs["/my/home"] = 3 ;
        $this->assertEquals($a_objs,$b_objs);
    }
    public function testFail()
    {
        try{
            XBox::regist("ka",1,__METHOD__,"/");
            XBox::regist("ka",1,__METHOD__,"/");
            $this->assertTrue(false);
        }
        catch( Exception $e)
        {
            echo "\n" . $e->getMessage();
            $this->assertTrue(true);
        }
    }
}
