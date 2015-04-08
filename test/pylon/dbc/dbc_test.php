<?php
class DBCTestCls
{
    private $a;
    private $b;
    private $c;
    public function __construct($a,$b,$c)
    {
        $this->a= DBC::requireNotNull($a);
        $this->b= DBC::requireNotNull($b);
        $this->c= DBC::requireNotNull($c);
    }
    public function fun1($a,$b,$c)
    {
        DBC::requireNotNull($a);
        DBC::requireNotNull($b);
        DBC::requireNotNull($c);
    }
}
class DBCTestCase extends PHPUnit_Framework_TestCase
{
    /** 
     * @brief 
     * @expectedException  XDBCException
     * @return 
     */
    public function testTrue()
    {
        DBC::requireTrue(false);
    }
    /** 
     * @brief 
     * @expectedException  XDBCException
     * @return 
     */
    public function testEquals()
    {
        $b= "mytest";
        DBC::requireEquals($b,"text",'$b');
    }
    /** 
     * @brief 
     * @expectedException  XDBCException
     * @return 
     */
    public function testNotEquals()
    {
        $b= "text";
        DBC::requireNotEquals($b,"text");
    }
    /** 
     * @brief 
     * @expectedException  XDBCException
     * @return 
     */
    public function testUnExpect()
    {
        DBC::unExpect($b);
    }
    /** 
     * @brief 
     * @expectedException  XDBCException
     * @return 
     */
    public function testNotNull()
    {
        DBC::requireNotNull(null);
    }
    /** 
     * @brief 
     * @expectedException  XDBCException
     * @return 
     */
    public function testNull()
    {
        DBC::requireNull("xx");
    }
    public function testUsecase1()
    {
        $obj = new DBCTestCls("a","b","c");
        $obj->fun1("a","b","c");
    }
}

