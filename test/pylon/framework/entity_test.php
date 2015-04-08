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
    }
    public function tearDown()
    {
//        DaoFinder::clean();
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
        $executer   =  XBox::must_get('SQLExecuter');
        $logImpl     = new MemCollectLogger();
//        $log = ScopeSqlLog::echoCollectWLog($executer,$logImpl);
        $log = ScopeSqlLog::collectWLog($executer,$logImpl);

        $app        = XAppSession::begin();
        try
        {
            $author= Author::createByBiz('zwj','1975-10-18','chinese');
            $author2= Author::createByBiz('zwj2','1975-10-18','chinese');
            $book  = Book::createByBiz('c++',$author,'10.2','c++ std lib');
            $book2 = Book::createByBiz('java',$author,0,'java std lib');

            $book3 = Book2::createByBiz('java','10.2','java std lib',$author,$author2);
            $book4 = Book2::createByBiz('java','10.2','java std lib',$author, new NullEntity('Author'));


            $car = BuyCar::createByBiz('zwj');
            $car->addBook($book,1);
            $car->addBook($book2,3);
            $app->commit();

            $msgs  = $logImpl->logMsgs;
            $this->assertRegExp("/insert author/",$msgs[0]);
            $this->assertRegExp("/insert author/",$msgs[1]);
            $this->assertRegExp("/insert book/",$msgs[2]);
            $this->assertRegExp("/insert book/",$msgs[3]);
            $this->assertRegExp("/insert book2/",$msgs[4]);
            $this->assertRegExp("/insert book2/",$msgs[5]);
            $this->assertRegExp("/insert buycar/",$msgs[6]);
            $this->assertRegExp("/insert car_item/",$msgs[7]);
            $this->assertRegExp("/insert car_item/",$msgs[8]);

            $log=null;
            $logImpl= new MemCollectLogger();
            $log = ScopeSqlLog::collectWLog($executer,$logImpl);
            //        $log = ScopeSqlLog::echoCollectWLog($executer,$logImpl);
            $mycar= DaoFinderUtls::find($car)->getByID($car->id());

            $book3 = Book::createByBiz('php',$author,'10.2','java std lib');
            $mycar->addBook($book3,3);
            $mycar->removeBook($book,1);
            $app->commit();
//            Entity::unitWork()->commit();
//            $this->session->commit();
            $msgs  = $logImpl->logMsgs;
            $this->assertRegExp("/insert book/",$msgs[0]);
            $this->assertRegExp("/update buycar/",$msgs[1]);
            $this->assertRegExp("/insert car_item/",$msgs[2]);
            $this->assertRegExp("/delete from car_item/",$msgs[3]);
        }
        catch(Exception $e)
        {
            echo $e->getTraceAsString();
            throw $e;
        }
    }
    public function t1estUnitWork2Session()
    {
        $executer =  XBox::must_get('SQLExecuter');
        $logImpl= new MemCollectLogger();
        //        $log = ScopeSqlLog::echoCollectLog($executer,$logImpl);
        $log = ScopeSqlLog::collectLog($executer,$logImpl);
        $unitwork = new UnitWorkImpl();
        EntityUtls::assembly($unitwork);
        $author= Author::createByBiz('zwj','1975-10-18','chinese');
        $book  = Book::createByBiz('c++',$author,'10.2','c++ std lib');
        $book2 = Book::createByBiz('java',$author,'10.2','java std lib');

        $car = BuyCar::createByBiz('zwj');
        $car->addBook($book,1);
        $car->addBook($book2,3);
        $keepdata= serialize($unitwork);


        $unitwork2 = unserialize($keepdata);
        EntityUtls::assembly($unitwork2);
        $this->assertEquals($unitwork,$unitwork2);
        $this->assertTrue($unitwork->equal($unitwork2));
        $unitwork=null;
        $book3 = Book::createByBiz('javax',$author,'10.2','java std libxx');
        $car->addBook($book3,3);
        $unitwork2->commit();

        $msgs  = array_slice($logImpl->logMsgs,count($logImpl)-9, 8);
        $this->assertRegExp("/insert author/",$msgs[0]);
        $this->assertRegExp("/insert book/",$msgs[1]);
        $this->assertRegExp("/insert book/",$msgs[2]);
        $this->assertRegExp("/insert buycar/",$msgs[3]);
        $this->assertRegExp("/insert car_item/",$msgs[4]);
        $this->assertRegExp("/insert car_item/",$msgs[5]);
    }

}
?>
