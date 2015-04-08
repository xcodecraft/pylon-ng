<?php
function confMaps1()
{
    $maps = new XProperty();
    $xmvc="pylon/test/xmvc";
    $prototype =  new ActionConf("prototype","","T:$xmvc/stdpage.php");
    $prototype->success_failure( "T:$xmvc/success.html", "T:$xmvc/error.html");

    $builder = new XConfBuilder($maps,$prototype);

    $xtest = $builder->xtest("","XTest2Action","T:$xmvc/xtest.html");
    $xtest->setInteceptors(XConst::SCOPE_IPOS,new TestIntcpt(),new TestIntcpt());

    $xtest1 = $builder->xtest1("","XTest2Action","","T:$xmvc/xtest.html");
    $xtest1->setInteceptors(XConst::SCOPE_IPOS, new TestIntcpt(),new TestIntcpt2());
    return $maps;
}
?>
