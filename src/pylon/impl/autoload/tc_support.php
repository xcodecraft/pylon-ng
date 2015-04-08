<?php
class TestCaseExec
{
    static public function batchExec($tclistfile)
    {
        ob_start();
        $test = new TestSuite('Union Tests');
        $tclists = file_get_contents($tclistfile);
        eval($tclists);
        foreach($tcs as $tc)
        {
            $test->add(trim($tc));
        }
        $test->run(new TextReporter());
        ob_end_flush();

    }
    static public function singleExec($tcfile)
    {
        ob_start();
        $test = new TestSuite('Union Tests');
        $test->addFile($tcfile);
        $test->run(new TextReporter());
        ob_end_flush();
    }
    static public function execTC($argv,$tclistfile)
    {
        $op=$argv[1]; 
        if($op == 'all')
        {
            TestCaseExec::batchExec($tclistfile);
        }
        else
        {
            $file=$argv[2];
            TestCaseExec::singleExec($file);
        }
    }
}
?>

