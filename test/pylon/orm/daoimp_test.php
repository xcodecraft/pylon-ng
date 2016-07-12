<?php

use pylon\impl\EmptyUnitWork ;
use pylon\impl\Daoimp;
use pylon\impl\SimpleMapping ;
use pylon\impl\DynCallParser ;
use pylon\impl\DQLObj ;

class UTAssemply
{
    static public function setup()
    {
        // echo "---------------------------------0------------------------------" ;
        $dbConf =  Conf::getDBConf();
        $executer = new FastSQLExecutor($dbConf->host,$dbConf->user,$dbConf->password,$dbConf->name);
        XBox::regist(XBox::SQLE,$executer,__METHOD__);
        XBox::regist(XBox::IDG, new MySqlIDGenerator($executer),__METHOD__);
        // echo "---------------------------------1------------------------------" ;

        XEntEnv::simpleSetup();
        XEntEnv::configDao('Book2','book2',"std");
        XEntEnv::configDao('BuyItem','car_item');
        // echo "---------------------------------2------------------------------" ;

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
        $this->app = XAppSession::begin(new EmptyUnitWork());
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
            $author    = Author::createByBiz('zwj','1975-10-18','chinese');
            $authorDao = DaoImp::simpleDao('Author',$executer);
            XEntEnv::registerDao($authorDao,'Author');
            $this->daoTestTplImp( $authorDao,$author,'name','qq');
            $authorDao->add($author);


            $book      = Book::createByBiz('c++',$author,'10.2','c++ std lib');
            $book2     = Book::createByBiz('c++',$author,'10.1',null);
            $book3     = Book::createByBiz('c++',$author,'10.3',"xxx'xxx");
            $bookDao   = DaoImp::simpleDao('Book',$executer);
            XEntEnv::registerDao($bookDao,'Book');
            $this->daoTestTplImp( $bookDao,$book,'name','java');
            $this->daoTestTplImp( $bookDao,$book2,'name','java');
            $this->daoTestTplImp( $bookDao,$book3,'name','java');

            $bookDao->add($book);
            $bookDao->add($book2);
            $bookDao->add($book3);
            $this->app->commit();
            unset($this->app) ;
            $this->app = XAppSession::begin(new EmptyUnitWork()) ;

            $books = XQuery::obj()->list_Book_by_price(QL('# > 10 and # < 10.5 ','#'));
            $this->assertTrue(count($books)>=3);
            $books2 = XQuery::obj()->list_Book_by_name(QL('? like "c%"'));
            $this->assertTrue(count($books2)>=3);

            $books3 = XQuery::obj()->list_Book_by_name_price(QL('? like "c%"'),"10.3");


            $books = XQuery::obj()->list_Book_by_price(QL('? > 10 and ? < 10.5 '));
            $this->assertTrue(count($books)>=3);

            $books = XQuery::obj()->list_Book_by_price(QL('? > 10 and ? < 10.5 '));
            $this->assertTrue(count($books)>=3);

            $books = XQuery::obj()->list_Book_by_price(QL('? > 10 and ? < 10.5 '));
            $this->assertTrue(count($books)>=3);
            DaoFinderUtls::clearBinder();

            XWriter::ins()->update_Book_set_price_by_name("15",QL(' ? like "c%"'));
            $books4 = XQuery::arr()->list_Book_by_price(QL('? > "12" '));
            $this->assertTrue(count($books4)>0);

            // mongo style test
            XWriter::update_Book(array("name"=>"c++","price"=>10.1),   array("id"=>$book->id()));
            XWriter::update_Book(array("name"=>"python","price"=>10.2),array("id"=>$book2->id()));
            XWriter::update_Book(array("name"=>"c++","price"=>10.3),   array("id"=>$book3->id()));
            $book  = Book::createByBiz('todel',$author,'10','to del');
            $book1 = Book::createByBiz('c',$author,'10.2','c language');
            $book2 = Book::createByBiz('go',$author,'11','go language');
            $bookDao->add($book);
            $bookDao->add($book1);
            $bookDao->add($book2);
            XWriter::del_Book(array("name"=>"todel"));
            $this->app->commit();
            $where = array(
                "name"  => QL("? != 'c'"),
                "price" => QL("? < 11"),
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
            $authorDao = DaoImp::simpleDao('Author',$executer);
//            $authorDao->updateLoadStg(Entity::IMMED_LOADER);
            $bookDao = DaoImp::simpleDao('Book',$executer);
//            $bookDao->updateLoadStg(Entity::IMMED_LOADER);
            $carDao = DaoImp::simpleDao('BuyCar',$executer);
//            $carDao->updateLoadStg(Entity::IMMED_LOADER);
            $buyItemDao = new DaoImp('BuyItem',$executer,'car_item',SimpleMapping::ins());
//            $buyItemDao->updateLoadStg(Entity::IMMED_LOADER);
            XEntEnv::registerDaos($authorDao,$bookDao,$carDao,$buyItemDao);


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
        try{

            $user1= User::createByBiz('sgtuser1','sgt');
            $user2= User::createByBiz('sgtuser2','sgt');
            $user3= User::createByBiz('sgtuser3','sgt');
            $userDao = new DaoImp('User',$executer,null,SimpleMapping::ins(),array('StoreStg','userStore'));
            XEntEnv::registerDao($userDao,'User');
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
           exit;
        }
    }
    public function daoTestTplImp($objDao, $obj,$chkey=null,$chval=null)
    {
        try{
            $objDao->add($obj);
            $getedObj = $objDao->getByID($obj->id());
            $this->assertEquals($obj,$getedObj);

            if(!is_null($chkey))
                $getedObj->$chkey=$chval;
            $objDao->update($getedObj);
            $getedObj2 = $objDao->getByID($obj->id());
            $this->assertEquals($getedObj,$getedObj2);
            $objDao->del($obj);
            $found= $objDao->getByID($obj->id());
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
        $cls='';
        $oparam=null;
        extract(DynCallParser::condObjParse("get_user_by_name_age_obj__id"));
        $prop = DynCallParser::buildCondProp($condnames,array("a","b","c"),$oparam);
        $this->assertEquals($cls,"user");
        $this->assertEquals($op,"get");
        $this->assertEquals($prop->name,"a");
        $this->assertEquals($prop->age,"b");
        $this->assertEquals($prop->obj__id,"c");
        $this->assertEquals($cls,"user");
        $this->assertEquals(count($oparam),0);


        extract(DynCallParser::condObjParse("list_user_by_name"));
        $prop = DynCallParser::buildCondProp($condnames,array("a"),$oparam);
        $this->assertEquals($cls,"user");
        $this->assertEquals($op,"list");
        $this->assertEquals($prop->name,"a");


        extract(DynCallParser::condObjParse("list_user"));
        $prop = DynCallParser::buildCondProp($condnames,array(),$oparam);
        $this->assertEquals($cls,"user");
        $this->assertEquals($op,"list");

//        $dda->list_user_by_age('? >18 or ? <20 ');
        extract(DynCallParser::condObjParse("list_user_by_age"));
        $prop = DynCallParser::buildCondProp($condnames,array(QL("? > 18 or ? < 20 ")),$oparam);
        $this->assertEquals($cls,"user");
        $this->assertEquals($op,"list");
        $this->assertEquals($prop->age,new DQLObj("? > 18 or ? < 20 "));



        extract(DynCallParser::condObjParse("get_user_by2_name__age__obj_id"));
        $prop = DynCallParser::buildCondProp($condnames,array("a","b","c"),$oparam);

        $this->assertEquals($cls,"user");
        $this->assertEquals($op,"get");
        $this->assertEquals($prop->name,"a");
        $this->assertEquals($prop->age,"b");
        $this->assertEquals($prop->obj_id,"c");
        $this->assertEquals($cls,"user");
        $this->assertEquals(count($oparam),0);


        extract(DynCallParser::condObjParse("get_user_by3_name___ag_e___obj__id"));
        $prop = DynCallParser::buildCondProp($condnames,array("a","b","c"),$oparam);
        $this->assertEquals($cls,"user");
        $this->assertEquals($op,"get");
        $this->assertEquals($prop->name,"a");
        $this->assertEquals($prop->ag_e,"b");
        $this->assertEquals($prop->obj__id,"c");
        $this->assertEquals($cls,"user");
        $this->assertEquals(count($oparam),0);

        $page = new XDataPage(10);
        extract(DynCallParser::condObjParse("get_user_by_name_age"));
        $prop = DynCallParser::buildCondProp($condnames,array("a","b",$page),$oparam);
        $this->assertEquals(count($oparam),1);

        extract(DynCallParser::condUpdateObjParse("update_user_set_name_by_age"));
        $this->assertEquals($cls,"user");
        $this->assertEquals($by,"by");
        $this->assertEquals($updatenames[0],"name");
        $this->assertEquals($condnames[0],"age");

        extract(DynCallParser::condUpdateObjParse("update_user_set2_name_by_age"));
        $this->assertEquals($cls,"user");
        $this->assertEquals($by,"by");
        $this->assertEquals($updatenames[0],"name");
        $this->assertEquals($condnames[0],"age");

        extract(DynCallParser::condUpdateObjParse("update_user_set3_name_by_age"));
        $this->assertEquals($cls,"user");
        $this->assertEquals($by,"by");
        $this->assertEquals($updatenames[0],"name");
        $this->assertEquals($condnames[0],"age");

        extract(DynCallParser::condUpdateObjParse("update_user_set_name_age_by2_age"));
        $this->assertEquals($cls,"user");
        $this->assertEquals($by,"by2");
        $this->assertEquals($updatenames[0],"name");
        $this->assertEquals($updatenames[1],"age");
        $this->assertEquals($condnames[0],"age");

        extract(DynCallParser::condUpdateObjParse("update_user_set2_name_x__age_by2_age"));
        $this->assertEquals($cls,"user");
        $this->assertEquals($by,"by2");
        $this->assertEquals($updatenames[0],"name_x");
        $this->assertEquals($updatenames[1],"age");
        $this->assertEquals($condnames[0],"age");

        extract(DynCallParser::condUpdateObjParse("update_user_set3_name__x_y___age__x_y_by2_age"));
        $this->assertEquals($cls,"user");
        $this->assertEquals($by,"by2");
        $this->assertEquals($updatenames[0],"name__x_y");
        $this->assertEquals($updatenames[1],"age__x_y");
        $this->assertEquals($condnames[0],"age");


        extract(DynCallParser::condUpdateObjParse("update_user_set_name_age"));
        $this->assertEquals($cls,"user");
        $this->assertEquals($by,"");
        $this->assertEquals($updatenames[0],"name");
        $this->assertEquals($updatenames[1],"age");
    }
}
