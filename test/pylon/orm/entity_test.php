<?php
class EntityTest extends PHPUnit_Framework_TestCase
{
    private $session = null;
    public function __construct()
    {
        parent::__construct("Entitys Group Test init ");
    }

    public function setUp()
    {
        UTAssemply::setup();
        XEntEnv::useNamespace("XCode") ;
    }
    public function tearDown()
    {
        XBox::clean();
    }
    public function testUnitWorkException()
    {
        $author = null;
        $app    = XEntEnv::beginSession();
        try
        {
            $author= \XCode\Author::createByBiz('zwj','1975-10-18',"chi'nese");
            $book  = \XCode\Book::createByBiz('c++',$author,'10.2','c++ std lib');
            $book->noAttr="xxx";

            $app->commit();
        }
        catch(Exception $e)
        {
            $this->assertTrue(true);
        }
        $found = XQuery::obj()->get_Author_by_id($author->id());
        $this->assertTrue($found == null);

    }
    public function testUnitWork()
    {
        $executer = XBox::must_get('SQLExecuter');
        $app      = XEntEnv::beginSession();
        $author   = \XCode\Author::createByBiz('zwj','1975-10-18','chinese');
        $author2  = \XCode\Author::createByBiz('zwj2','1975-10-18','chinese');
        $book     = \XCode\Book::createByBiz('c++',$author,'10.2','c++ std lib');
        $book2    = \XCode\Book::createByBiz('java',$author,0,'java std lib');

        // echo "\nauthor: " . $author->id() ;
        // echo "\nbook  : " . $book->id() ;
        $book3 = \XCode\Book2::createByBiz('java','10.2','java std lib',$author,$author2);
        $book4 = \XCode\Book2::createByBiz('java','10.2','java std lib',$author, new NullEntity('Author'));


        $car = \XCode\BuyCar::createByBiz('zwj');
        $car->addBook($book,1);
        $car->addBook($book2,3);
        $app->commit();

        $mycar = DaoFinderUtls::find($car)->getByID($car->id());
        $book3 = \XCode\Book::createByBiz('php',$author,'10.2','java std lib');
        $mycar->addBook($book3,3);
        $mycar->removeBook($book,1);
        $app->commit();
    }

}
