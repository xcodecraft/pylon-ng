<?php
use \Pylon\EmptyUnitWork as EmptyUnitWork ;

class UTAssemply
{
    static public function setup()
    {
        $dbConf   = Conf::getDBConf();
        $executer = new \Pylon\FastSQLExecutor($dbConf->host,$dbConf->user,$dbConf->password,$dbConf->name);

        XEntEnv::simpleSetup($executer);
        XEntEnv::useNamespace("XCode") ;
        XEntEnv::configDao('XCode\Book2','book2',"std");
        XEntEnv::configDao('XCode\BuyItem','car_item');

    }
}
class StoreStg
{
    static public function userStore($id)
    {
        DBC::requireNotNull($id);
        if($id%2 == 1)
        {
            return "user_1";
        }
        return "user_2";
    }
}
class DaoImpTest extends PHPUnit_Framework_TestCase
{
    protected $oldXEntEnv=null;
    private $app= null;
    public function __construct()
    {
        self::init();
        parent::__construct();
    }
    static public function init()
    {
        XEntEnv::clean();
    }
    public function setUp()
    {
        UTAssemply::setup();
        $this->app = XAppSession::begin(new \Pylon\EmptyUnitWork());
    }
    public function tearDown()
    {
        $this->app->commit();
        XEntEnv::clean();
        XBox::clean();
    }
    public function testSimpleObjDao()
    {
        $executer =  XBox::must_get(XBox::SQLE);
        XSetting::$entLazyload = false ;
            $author    = \XCode\Author::createByBiz('zwj','1975-10-18','chinese');
            $authorDao = \Pylon\DaoImp::simpleDao(get_class($author),$executer);
            XEntEnv::registDao($authorDao,'\XCode\Author');
            $this->daoTestTplImp( $authorDao,$author,'name','qq');
            $authorDao->add($author);


            $book      = \XCode\Book::createByBiz('c++',$author,'10.2','c++ std lib');
            $book2     = \XCode\Book::createByBiz('c++',$author,'10.1',null);
            $book3     = \XCode\Book::createByBiz('c++',$author,'10.3',"xxx'xxx");
            $bookDao   = \Pylon\DaoImp::simpleDao(get_class($book),$executer);
            XEntEnv::registDao($bookDao,'Book');
            $this->daoTestTplImp( $bookDao,$book,'name','java');
            $this->daoTestTplImp( $bookDao,$book2,'name','java');
            $this->daoTestTplImp( $bookDao,$book3,'name','java');

            $bookDao->add($book);
            $bookDao->add($book2);
            $bookDao->add($book3);
            $this->app->commit();
            unset($this->app) ;
            $this->app = XAppSession::begin(new EmptyUnitWork()) ;

            $books = XQuery::obj()->list_Book_by_price(XEntEnv::QL('# > 10 and # < 10.5 ','#'));
            $this->assertTrue(count($books)>=3);
            $books2 = XQuery::obj()->list_Book_by_name(XEntEnv::QL('? like "c%"'));
            $this->assertTrue(count($books2)>=3);

            $books3 = XQuery::obj()->list_Book_by_name_price(XEntEnv::QL('? like "c%"'),"10.3");


            $books = XQuery::obj()->list_Book_by_price(XEntEnv::QL('? > 10 and ? < 10.5 '));
            $this->assertTrue(count($books)>=3);

            $books = XQuery::obj()->list_Book_by_price(XEntEnv::QL('? > 10 and ? < 10.5 '));
            $this->assertTrue(count($books)>=3);

            $books = XQuery::obj()->list_Book_by_price(XEntEnv::QL('? > 10 and ? < 10.5 '));
            $this->assertTrue(count($books)>=3);
            \Pylon\DaoFinderUtls::clearBinder();

            XWriter::ins()->update_Book_set_price_by_name("15",XEntEnv::QL(' ? like "c%"'));
            $books4 = XQuery::arr()->list_Book_by_price(XEntEnv::QL('? > "12" '));
            $this->assertTrue(count($books4)>0);

            // mongo style test
            XWriter::update_Book(array("name"=>"c++","price"=>10.1),   array("id"=>$book->id()));
            XWriter::update_Book(array("name"=>"python","price"=>10.2),array("id"=>$book2->id()));
            XWriter::update_Book(array("name"=>"c++","price"=>10.3),   array("id"=>$book3->id()));
            $book  = \XCode\Book::createByBiz('todel',$author,'10','to del');
            $book1 = \XCode\Book::createByBiz('c',$author,'10.2','c language');
            $book2 = \XCode\Book::createByBiz('go',$author,'11','go language');
            $bookDao->add($book);
            $bookDao->add($book1);
            $bookDao->add($book2);
            XWriter::del_Book(array("name"=>"todel"));
            $this->app->commit();
            $where = array(
                "name"  => XEntEnv::QL("? != 'c'"),
                "price" => XEntEnv::QL("? < 11"),
            );
            $order = array(
                "name"  => "",
                "price" => "desc",
            );
            $books = XQuery::arr()->list_Book($where,$order);
            $this->assertTrue(count($books)==3  );
            $books = XQuery::arr()->list_Book_by_name("c++",null,"price","desc");
            $this->assertTrue(count($books)==2 );
            $book = XQuery::arr()->get_Book($where,$order);
            $this->assertTrue($book != null);
    }
    public function t1estComplexObjDao()
    {

        XEntEnv::clean();
        try
        {
            $executer =  XBox::must_get(XBox::SQLE);
            $authorDao = DaoImp::simpleDao('\XCode\Author',$executer);
//            $authorDao->updateLoadStg(Entity::IMMED_LOADER);
            $bookDao = DaoImp::simpleDao('\XCode\Book',$executer);
//            $bookDao->updateLoadStg(Entity::IMMED_LOADER);
            $carDao = DaoImp::simpleDao('\XCode\BuyCar',$executer);
//            $carDao->updateLoadStg(Entity::IMMED_LOADER);
            $buyItemDao = new DaoImp('\XCode\BuyItem',$executer,'car_item',SimpleMapping::ins());
//            $buyItemDao->updateLoadStg(Entity::IMMED_LOADER);
            XEntEnv::registerDaos($authorDao,$bookDao,$carDao,$buyItemDao);

//            $log =  new  ScopeEchoLog($executer);

            $author= Author::createByBiz('zwj','1975-10-18','chinese');
            $authorDao->add($author);
            $book = Book::createByBiz('c++',$author,'10.2','c++ std lib');
            $bookDao->add($book);
            $book2 = Book::createByBiz('java',$author,'10.2','java std lib');
            $bookDao->add($book2);

            $car = BuyCar::createByBiz('test');
            $car->addBook($book,1);
            $car->addBook($book2,3);
            $carDao->add($car);
            $getedObj=$carDao->getByID($car->id());
            $this->assertEquals($car->entityID,$getedObj->entityID);
            $data1 = $car->buyItemSet->items();
            $data2 = $getedObj->buyItemSet->items();
            $this->assertEquals($car->buyItemSet->items(),$getedObj->buyItemSet->items());
            $this->assertTrue($car->buyItemSet->equal($getedObj->buyItemSet));

            $getedObj->removeBook($book2,1);
            $carDao->update($getedObj);
            $getedObj2=$carDao->getByID($getedObj->id());
            $this->assertEquals($getedObj2->entityID,$getedObj->entityID);
            $this->assertEquals($getedObj2->buyItemSet->items(),$getedObj->buyItemSet->items());
            $this->assertTrue($getedObj2->buyItemSet->equal($getedObj->buyItemSet));
            $this->assertFalse($getedObj2->buyItemSet->equal($car->buyItemSet));
            $this->assertEquals(count($getedObj2->buyItemSet->items()),count($car->buyItemSet->items()));

            $getedObj2->removeBook($book,1);
            $carDao->update($getedObj2);
            $getedObj3=$carDao->getByID($getedObj->id());
            $this->assertEquals($getedObj3->entityID,$car->entityID);
            $this->assertFalse($getedObj3->buyItemSet->equal($car->buyItemSet));
            $this->assertEquals($getedObj3->buyItemSet->items(),$getedObj2->buyItemSet->items());
        }
        catch( Exception $e)
        {
            echo $e->getTraceAsString();
            throw $e;
        }


    }
    public function testHashStoreDao()
    {
        $executer =  XBox::must_get('SQLExecuter');
//        $log =  new  ScopeEchoLog($executer);
        try{

            $user1= \XCode\User::createByBiz('sgtuser1','sgt');
            $user2= \XCode\User::createByBiz('sgtuser2','sgt');
            $user3= \XCode\User::createByBiz('sgtuser3','sgt');
            $userDao = new \Pylon\DaoImp(get_class($user1),$executer,null,\Pylon\SimpleMapping::ins(),array('StoreStg','userStore'));
            XEntEnv::registDao($userDao,'User');
            $userDao->setHashStoreKey($user1->hashStoreKey());
            $this->daoTestTplImp( $userDao,$user1,'name','qq');
            $userDao->setHashStoreKey($user2->hashStoreKey());
            $this->daoTestTplImp( $userDao,$user2,'name','qq');
            $userDao->setHashStoreKey($user3->hashStoreKey());
            $this->daoTestTplImp( $userDao,$user3,'name','qq');
        }
        catch(Exception $e)
        {
            echo $e->getMessage()."\n";
            echo $e->getTraceAsString();
            $this->assertTrue(false);
        }
    }
    public function daoTestTplImp($objDao, $obj,$chkey=null,$chval=null)
    {
        try
        {
            $objDao->add($obj);
            $getedObj = $objDao->getByID($obj->id());
            $this->assertEquals($obj,$getedObj);

            if(!is_null($chkey))
                $getedObj->$chkey=$chval;
            $objDao->update($getedObj);
            $getedObj2 = $objDao->getByID($obj->id());
            $this->assertEquals($getedObj,$getedObj2);
            $objDao->del($obj);
            $found     = $objDao->getByID($obj->id());
            $this->assertTrue($found == null);
        }
        catch ( Exception $e)
        {
            echo $e->getMessage() ."\n";
            echo $e->getTraceAsString();
            $this->assertTrue(false);
        }

    }
    public function testDynQuery()
    {
        // $dda = new XQobj;
        $cls    = '';
        $oparam = null;
        extract(\Pylon\DynCallParser::condObjParse("get_user_by_name_age_obj__id"));
        $prop   = \Pylon\DynCallParser::buildCondProp($condnames,array("a","b","c"),$oparam);
        $this->assertEquals($cls,"user");
        $this->assertEquals($op,"get");
        $this->assertEquals($prop->name,"a");
        $this->assertEquals($prop->age,"b");
        $this->assertEquals($prop->obj__id,"c");
        $this->assertEquals($cls,"user");
        $this->assertEquals(count($oparam),0);

        extract(\Pylon\DynCallParser::condObjParse("list_user_by_name"));
        $prop = \Pylon\DynCallParser::buildCondProp($condnames,array("a"),$oparam);
        $this->assertEquals($cls,"user");
        $this->assertEquals($op,"list");
        $this->assertEquals($prop->name,"a");

        extract(\Pylon\DynCallParser::condObjParse("list_user"));
        $prop = \Pylon\DynCallParser::buildCondProp($condnames,array(),$oparam);
        $this->assertEquals($cls,"user");
        $this->assertEquals($op,"list");

//        $dda->list_user_by_age('? >18 or ? <20 ');
        extract(\Pylon\DynCallParser::condObjParse("list_user_by_age"));
        $prop = \Pylon\DynCallParser::buildCondProp($condnames,array(XEntEnv::QL("? > 18 or ? < 20 ")),$oparam);
        $this->assertEquals($cls,"user");
        $this->assertEquals($op,"list");
        $this->assertEquals($prop->age,new \Pylon\DQLObj("? > 18 or ? < 20 "));

        extract(\Pylon\DynCallParser::condObjParse("get_user_by2_name__age__obj_id"));
        $prop = \Pylon\DynCallParser::buildCondProp($condnames,array("a","b","c"),$oparam);

        $this->assertEquals($cls,"user");
        $this->assertEquals($op,"get");
        $this->assertEquals($prop->name,"a");
        $this->assertEquals($prop->age,"b");
        $this->assertEquals($prop->obj_id,"c");
        $this->assertEquals($cls,"user");
        $this->assertEquals(count($oparam),0);


        extract(\Pylon\DynCallParser::condObjParse("get_user_by3_name___ag_e___obj__id"));
        $prop = \Pylon\DynCallParser::buildCondProp($condnames,array("a","b","c"),$oparam);
        $this->assertEquals($cls,"user");
        $this->assertEquals($op,"get");
        $this->assertEquals($prop->name,"a");
        $this->assertEquals($prop->ag_e,"b");
        $this->assertEquals($prop->obj__id,"c");
        $this->assertEquals($cls,"user");
        $this->assertEquals(count($oparam),0);

        $page = new XDataPage(10);
        extract(\Pylon\DynCallParser::condObjParse("get_user_by_name_age"));
        $prop = \Pylon\DynCallParser::buildCondProp($condnames,array("a","b",$page),$oparam);
        $this->assertEquals(count($oparam),1);

        extract(\Pylon\DynCallParser::condUpdateObjParse("update_user_set_name_by_age"));
        $this->assertEquals($cls,"user");
        $this->assertEquals($by,"by");
        $this->assertEquals($updatenames[0],"name");
        $this->assertEquals($condnames[0],"age");

        extract(\Pylon\DynCallParser::condUpdateObjParse("update_user_set2_name_by_age"));
        $this->assertEquals($cls,"user");
        $this->assertEquals($by,"by");
        $this->assertEquals($updatenames[0],"name");
        $this->assertEquals($condnames[0],"age");

        extract(\Pylon\DynCallParser::condUpdateObjParse("update_user_set3_name_by_age"));
        $this->assertEquals($cls,"user");
        $this->assertEquals($by,"by");
        $this->assertEquals($updatenames[0],"name");
        $this->assertEquals($condnames[0],"age");

        extract(\Pylon\DynCallParser::condUpdateObjParse("update_user_set_name_age_by2_age"));
        $this->assertEquals($cls,"user");
        $this->assertEquals($by,"by2");
        $this->assertEquals($updatenames[0],"name");
        $this->assertEquals($updatenames[1],"age");
        $this->assertEquals($condnames[0],"age");

        extract(\Pylon\DynCallParser::condUpdateObjParse("update_user_set2_name_x__age_by2_age"));
        $this->assertEquals($cls,"user");
        $this->assertEquals($by,"by2");
        $this->assertEquals($updatenames[0],"name_x");
        $this->assertEquals($updatenames[1],"age");
        $this->assertEquals($condnames[0],"age");

        extract(\Pylon\DynCallParser::condUpdateObjParse("update_user_set3_name__x_y___age__x_y_by2_age"));
        $this->assertEquals($cls,"user");
        $this->assertEquals($by,"by2");
        $this->assertEquals($updatenames[0],"name__x_y");
        $this->assertEquals($updatenames[1],"age__x_y");
        $this->assertEquals($condnames[0],"age");

        extract(\Pylon\DynCallParser::condUpdateObjParse("update_user_set_name_age"));
        $this->assertEquals($cls,"user");
        $this->assertEquals($by,"");
        $this->assertEquals($updatenames[0],"name");
        $this->assertEquals($updatenames[1],"age");
    }
    public function testCacheDA()
    {

        if(!MemCacheSvc::isEnable())
        {
            echo "\nno memcached  ".__CLASS__."::".__FUNCTION__." is ignore\n";
            return ;
        }
        $cacheDriver = new \Pylon\MemCacheSvc(MemCacheSvc::localhostConf());
        \Pylon\CacheAdmin::setup($cacheDriver,new CacheStg(600));
        \XCodeCtrl::switchDaoCache(\XCodeCtrl::ON);

        $executer =  XBox::must_get('SQLExecuter');
        \XCodeCtrl::switchLazyLoad(\XCodeCtrl::OFF);
//        $log =  new  ScopeEchoLog($executer);
        $app    = AppSession::begin();
        $author = Author::createByBiz('zwj_test','1975-10-18','chinese');
        $app->commit();

        $app   = AppSession::begin();
        $found = DDA::ins()->get_Author_by_id($author->id());
        $found->lang="yyy";
        $app->commit();
        $this->assertEquals($found->ver() , 2);


        $found = DDA::ins()->get_Author_by_id($author->id());
        $found->lang="xxx";
        $app->commit();
        $this->assertEquals($found->ver() , 3);
        $this->assertTrue(true);
    }
}
