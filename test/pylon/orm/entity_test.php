<?php


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
        XEntEnv::useNamespace("Pylon") ;
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
            $author= Pylon\Author::createByBiz('zwj','1975-10-18',"chi'nese");
            $book  = Pylon\Book::createByBiz('c++',$author,'10.2','c++ std lib');
            $book->noAttr="xxx";

            $app->commit();
        }
        catch(Exception $e)
        {
            $this->assertTrue(true);
        }
        XEntEnv::useNamespace("Pylon") ;
        $found = XQueryObj::ins()->get_Author_by_id($author->id());
        $this->assertTrue($found == null);

    }
    public function testUnitWork()
    {
        $executer = XBox::must_get('SQLExecuter');
        $app      = XAppSession::begin();
        $author   = Pylon\Author::createByBiz('zwj','1975-10-18','chinese');
        $author2  = Pylon\Author::createByBiz('zwj2','1975-10-18','chinese');
        $book     = Pylon\Book::createByBiz('c++',$author,'10.2','c++ std lib');
        $book2    = Pylon\Book::createByBiz('java',$author,0,'java std lib');

        // echo "\nauthor: " . $author->id() ;
        // echo "\nbook  : " . $book->id() ;
        $book3 = Pylon\Book2::createByBiz('java','10.2','java std lib',$author,$author2);
        $book4 = Pylon\Book2::createByBiz('java','10.2','java std lib',$author, new NullEntity('Author'));


        $car = Pylon\BuyCar::createByBiz('zwj');
        $car->addBook($book,1);
        $car->addBook($book2,3);
        $app->commit();
        //TODO: 需要验证产生对象的顺序

        // $this->assertRegExp("/insert author/",$msgs[0]);
        // $this->assertRegExp("/insert author/",$msgs[1]);
        // $this->assertRegExp("/insert book/",$msgs[2]);
        // $this->assertRegExp("/insert book/",$msgs[3]);
        // $this->assertRegExp("/insert book2/",$msgs[4]);
        // $this->assertRegExp("/insert book2/",$msgs[5]);
        // $this->assertRegExp("/insert buycar/",$msgs[6]);
        // $this->assertRegExp("/insert car_item/",$msgs[7]);
        // $this->assertRegExp("/insert car_item/",$msgs[8]);

        $mycar = DaoFinderUtls::find($car)->getByID($car->id());
        $book3 = Pylon\Book::createByBiz('php',$author,'10.2','java std lib');
        $mycar->addBook($book3,3);
        $mycar->removeBook($book,1);
        $app->commit();
        // $this->assertRegExp("/insert book/",$msgs[0]);
        // $this->assertRegExp("/update buycar/",$msgs[1]);
        // $this->assertRegExp("/insert car_item/",$msgs[2]);
        // $this->assertRegExp("/delete from car_item/",$msgs[3]);
    }

}
