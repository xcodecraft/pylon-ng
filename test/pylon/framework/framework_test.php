<?php
class ArrayCache extends XProperty
{
    public function get($key)
    {
        if($this->have($key))
            return $this->$key;
        return null;
    }
    public function set($key,$val)
    {
        $this->$key=$val;
    }
    public function delete($key)
    {
        $this->remove($key);
    }
}

class FrameWorkTestCase extends PHPUnit_Framework_TestCase
{
    // public function testCacheOberver()
    // {
    //     return ;
    //     $cache    = new ArrayCache();
    //     $keyCache = new ArrayCache();
    //     $ob       = new CacheObserver($keyCache);
    //
    //     $data[]   = array('id'=>1);
    //     $data[]   = array('id'=>3);
    //     $data[]   = array('id'=>5);
    //     $data[]   = array('id'=>7);
    //     $cacheKey=100;
    //     $cache->set($cacheKey,"qihoo");
    //     $ob->regCachedlist($cacheKey,$data);
    //     $ob->invalidate(5,$cache);
    //     $this->assertEquals(0,count($cache->getPropArray()));
    //     $this->assertEquals(3,count($keyCache->getPropArray()));
    //
    //     $ret = $ob->isWriteCall("addObj");
    //     $this->assertTrue($ret);
    //     $ret = $ob->isWriteCall("xxaddObj");
    //     $this->assertFalse($ret);
    // }
    // public function testCacheOberver4Add()
    // {
    //     $cache    = new ArrayCache();
    //     $keyCache = new ArrayCache();
    //     $ob       = new CacheObserver($keyCache);
    //     $cacheKey = 100;
    //     $cache->set($cacheKey,"qihoo");
    //     $ob->regSensitive4Add('test',$cacheKey);
    //     $ob->invalidate4Add('test',$cache);
    //     $this->assertEquals(0,count($cache->getPropArray()));
    // }
    public function testAppSession()
    {
        $app = XAppSession::begin();
        $app->commit();
        $app =null;

    }

}

class TestDao
{
    public function listByCond($name)
    {
        $data[] = array("id"=>1,"name"=>"test","desc"=>"abc");
        $data[] = array("id"=>2,"name"=>"test","desc"=>"abc");
        $data[] = array("id"=>3,"name"=>"test","desc"=>"abc");
        return $data;
    }
    public function update($data)
    {}
    public function add($data)
    {}
}

// class CacheProxyTC extends PHPUnit_Framework_TestCase
// {
//     public function setUp()
//     {
//         $this->dao = $this->getMock('TestDao');
//     }
//     public function testNormal()
//     {
//
//         $data[] = array("id"=>1,"name"=>"test","desc"=>"abc1");
//         $data[] = array("id"=>2,"name"=>"test","desc"=>"abc2");
//         $data[] = array("id"=>3,"name"=>"test","desc"=>"abc3");
//
//         $cache    = new ArrayCache();
//         $keyCache = new ArrayCache();
//         $ob       = new CacheObserver($keyCache);
//
//
//         $this->dao->expects($this->once())->method('listByCond' )->will($this->returnValue($data));
//         $proxyDao = new RWCacheProxy($cache,$this->dao,'test',$ob);
//         $data1    = $proxyDao->listByCond("name");
//         $data2    = $proxyDao->listByCond("name");
//         $this->assertEquals($data1,$data2);
//
//     }
//
//     public function test4Update()
//     {
//         try
//         {
//             $data[] = array("id"=>1,"name"=>"test","desc"=>"abc1");
//             $data[] = array("id"=>2,"name"=>"test","desc"=>"abc2");
//             $data[] = array("id"=>3,"name"=>"test","desc"=>"abc3");
//
//             $cache        = new ArrayCache();
//             $keyCache     = new ArrayCache();
//             $ob           = new CacheObserver($keyCache);
//
//             $this->dao->expects($this->exactly(2))->method('listByCond')->will($this->returnValue($data));
//             $proxyDao     = new RWCacheProxy($cache,$this->dao,'test',$ob);
//             $data1        = $proxyDao->listByCond("name");
//             $data1        = $proxyDao->listByCond("name");
//             $data         = $data1[0];
//             $data["name"] = "bbs";
//             $proxyDao->update($data);
//             $data2        = $proxyDao->listByCond("name");
//             $data2        = $proxyDao->listByCond("name");
//             $this->assertEquals($data1,$data2);
//         }
//         catch(Exception $e)
//         {
//             echo  $e->getMessage();
//             echo $e->getTraceAsString();
//             throw $e;
//         }
//
//     }
//     public function test4Add()
//     {
//         try
//         {
//             $data[] = array("id"=>1,"name"=>"test","desc"=>"abc1");
//             $data[] = array("id"=>2,"name"=>"test","desc"=>"abc2");
//             $data[] = array("id"=>3,"name"=>"test","desc"=>"abc3");
//
//             $cache = new ArrayCache();
//             $keyCache = new ArrayCache();
//             $ob = new CacheObserver($keyCache);
//
//             $this->dao->expects($this->exactly(2))->method('listByCond')->will($this->returnValue($data));
//             $proxyDao= new RWCacheProxy($cache,$this->dao,'test',$ob);
//             $proxyDao->catch4add_listByCond("name");
//             $data1=$proxyDao->listByCond("name");
//             $data1=$proxyDao->listByCond("name");
//             $data = $data1[0];
//             $data["name"]="bbs";
//             $data["id"]="4";
//             $proxyDao->add($data);
//             $data2=$proxyDao->listByCond("name");
//             $data2=$proxyDao->listByCond("name");
//         }
//         catch(Exception $e)
//         {
//             echo  $e->getMessage();
//             echo $e->getTraceAsString();
//             throw $e;
//         }
//     }
// }
