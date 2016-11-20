<?php
class DBCTestCls
{
    private $a;
    private $b;
    private $c;
    public function __construct($a,$b,$c)
    {
        $this->a= XDBC::requireNotNull($a);
        $this->b= XDBC::requireNotNull($b);
        $this->c= XDBC::requireNotNull($c);
    }
    public function fun1($a,$b,$c)
    {
        XDBC::requireNotNull($a);
        XDBC::requireNotNull($b);
        XDBC::requireNotNull($c);
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
        XDBC::requireTrue(false);
    }
    /** 
     * @brief 
     * @expectedException  XDBCException
     * @return 
     */
    public function testEquals()
    {
        $b= "mytest";
        XDBC::requireEquals($b,"text",'$b');
    }
    /** 
     * @brief 
     * @expectedException  XDBCException
     * @return 
     */
    public function testNotEquals()
    {
        $b= "text";
        XDBC::requireNotEquals($b,"text");
    }
    /** 
     * @brief 
     * @expectedException  XDBCException
     * @return 
     */
    public function testUnExpect()
    {
        XDBC::unExpect($b);
    }
    /** 
     * @brief 
     * @expectedException  XDBCException
     * @return 
     */
    public function testNotNull()
    {
        XDBC::requireNotNull(null);
    }
    /** 
     * @brief 
     * @expectedException  XDBCException
     * @return 
     */
    public function testNull()
    {
        XDBC::requireNull("xx");
    }
    public function testUsecase1()
    {
        $obj = new DBCTestCls("a","b","c");
        $obj->fun1("a","b","c");
    }
}

