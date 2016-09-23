<?php

use pylon\impl\NullEntity ;
use pylon\impl\DaoFinderUtls ;

class EntityTest extends PHPUnit_Framework_TestCase
{
    private $session = null;
    public function __construct()
    {
        parent::__construct("Entitys Group Test init ");
    }
    public function __destruct()
    {
    }

    public function setUp()
    {
        UTAssemply::setup();
    }
    public function tearDown()
    {
        XBox::clean();
    }
    public function testUnitWorkException()
    {
        $author=null;
        $app  = XAppSession::begin();
        try
        {
            $author= Author::createByBiz('zwj','1975-10-18',"chi'nese");
            $book  = Book::createByBiz('c++',$author,'10.2','c++ std lib');
            $book->noAttr="xxx";

            $app->commit();
        }
        catch(Exception $e)
        {
            $this->assertTrue(true);
        }
        $found = XQueryObj::ins()->get_Author_by_id($author->id());
        $this->assertTrue($found == null);

    }
    public function testUnitWork()
    {
        $executer = XBox::must_get('SQLExecuter');
        $app      = XAppSession::begin();
        $author   = Author::createByBiz('zwj','1975-10-18','chinese');
        $author2  = Author::createByBiz('zwj2','1975-10-18','chinese');
        $book     = Book::createByBiz('c++',$author,'10.2','c++ std lib');
        $book2    = Book::createByBiz('java',$author,0,'java std lib');

        $book3 = Book2::createByBiz('java','10.2','java std lib',$author,$author2);
        $book4 = Book2::createByBiz('java','10.2','java std lib',$author, new NullEntity('Author'));

        $book5 = Book::createByBiz('php',$author,'10.2','java std lib');
        $app->commit();
        $this->assertTrue(true);
    }

}
