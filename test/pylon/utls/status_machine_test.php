<?php
class  TestStatus 
{
    const  LIVED = 0;
    const  DEATH  = 1;
    const  SON    = 2 ;
    const  FATHER    = 3 ;
    const  GRANDFATHER    = 4;
}

class StatusMachineTest  extends PHPUnit_Framework_TestCase
{


    private $stats;
    public function setUp() 
    {

        $this->stats = new XStatusMachine();
        $this->stats->add(TestStatus.LIVED,TestStatus.DEATH);
        $this->stats->addSon(TestStatus.LIVED, TestStatus.SON,
            TestStatus.FATHER,TestStatus.GRANDFATHER);
        $this->stats->moveable(TestStatus.LIVED,TestStatus.DEATH);
        $this->stats->moveable(TestStatus.SON,TestStatus.FATHER,TestStatus.GRANDFATHER);
        $this->stats->setCurrentStatus(TestStatus.SON);
    } 
    public function testSuccessToNextState()
    {
        
        $this->stats->moveTo(TestStatus.FATHER);
        $this->stats->undo();
        $this->stats->moveTo(TestStatus.FATHER);
        $this->stats->moveTo(TestStatus.GRANDFATHER);
        $this->stats->moveTo(TestStatus.DEATH);
    }
    public function testFailToNextState()
    {
        $this->assertFalse($this->stats->canMoveTo(TestStatus.SON));
        $this->assertFalse($this->stats->canMoveTo(TestStatus.GRANDFATHER));
        $this->stats->moveTo(TestStatus.FATHER);
        $this->assertFalse($this->stats->canMoveTo(TestStatus.SON));
        $this->stats->moveTo(TestStatus.GRANDFATHER);
        $this->assertFalse($this->stats->canMoveTo(TestStatus.SON));
        $this->assertFalse($this->stats->canMoveTo(TestStatus.FATHER));
        $this->stats->moveTo(TestStatus.DEATH);
        $this->assertFalse($this->stats->canMoveTo(TestStatus.SON));
    }
    public function testSonToDeath()
    {
        $this->stats->moveTo(TestStatus.DEATH);
    }
    public function testFatherToDeath()
    {
        $this->stats->moveTo(TestStatus.FATHER);
        $this->stats->moveTo(TestStatus.DEATH);
    }

    public function testFailed()
    {
        DBC::$failAction = DBC::DO_EXCEPTION;
        try
        {
            $this->stats->moveTo(TestStatus.FATHER);
            $this->stats->moveTo(TestStatus.SON);
            $this->assertFalse(true);
        }
        catch(Exception $e)
        {
            $this->assertTrue(true);
        }
    }

}
?>
