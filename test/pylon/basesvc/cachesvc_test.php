<?php
class TestDaoStub 
{
    public  function getObj($id,$name) 
    {
        static $i=0;
        $testObj = PropertyObj::create();
        $testObj->id=1;
        $testObj->name='bidu';
        $i++;
        if($i%2 ==0 )
            $testObj->name='google';
        return $testObj;
    }
}
/** 
 * @brief 
 * @example  cachesvc_test.php
 */
class CacheSvcTest extends PHPUnit_Framework_TestCase
{
    public function testMemCache()
    {
        if(!MemCacheSvc::isEnable())
        {
            echo "\nno memcached  ".__CLASS__."::".__FUNCTION__." is ignore\n";
            return ;
        }
//        $svcImpl = new MemCacheSvc(MemCacheSvc::localhostConf());
//        $this->howtoUseWarpper($svcImpl);
//        $this->howtoUseCacheProxy($svcImpl);
//        $svcImpl->flush();
    }
    private function howtoUseWarpper($cacheSvc)
    {
        DBC::requireNotNull($cacheSvc,'$cacheSvc');
        $testObj = PropertyObj::create();
        $testObj->id=1;
        $testObj->name='baidu';
        $stg = new CacheStg(1,create_function('$value','return $value->id ;'));
        $cache = new PylCacheSvc("test",$cacheSvc,$stg);
        $x=$cache->set($testObj->id,$testObj);
        $obj = $cache->get($testObj->id);
        Debug::watch(__FILE__,__LINE__,$obj,'$obj');
        Debug::watch(__FILE__,__LINE__,$testObj,'$testObj');
        $this->assertEqual($testObj ,$obj);
    }
    private function howtoUseCacheProxy($cacheSvc)
    {
        $stg = new CacheStg(1,null);
        $cache = new CacheSvcWarpper("test",$cacheSvc,$stg);
        $dao = new TestDaoStub();
        $cacheDao = new CacheProxy($cache,$dao,'proxy');
        $obj1 = $cacheDao->getObj(1,'baidu');
        $obj2 = $cacheDao->getObj(1,'baidu');
        $obj3 = $dao->getObj(1,'baidu');
        $this->assertEqual($obj1,$obj2);
        $this->assertFalse($obj1 == $obj3);
    }
}
?>
