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

