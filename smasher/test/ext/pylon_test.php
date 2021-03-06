<?php
$br = (php_sapi_name() == "cli")? "":"<br>";

if(!extension_loaded('pylonphp')) {
	dl('pylonphp.' . PHP_SHLIB_SUFFIX);
}
$module = 'pylonphp';
$functions = get_extension_funcs($module);
echo "Functions available in the test extension:$br\n";
foreach($functions as $func) {
    echo $func."$br\n";
}
echo "$br\n";
$function = 'confirm_' . $module . '_compiled';
if (extension_loaded($module)) {
    $str = $function($module);
} else {
    $str = "Module $module is not compiled into PHP";
}
echo "$str\n";
log_kit::init("pylon-ng","tagx",0);
log_kit::toall(false);
log_kit::channel(6);
log_kit::tag("_pylon","tagy");
log_kit::level("_pylon",1,2);
log_kit::event("phpevent");
for($i = 0 ; $i< 1000 ; $i++ )
{
    $l2 = new logger("_pylon");
    $l2->debug("xxxxdebug","r");
    $l2->debug("xxxxdebug","w");
   log_kit::tag("_pylon","tagz");
    $l1 = new logger("_pylon2");
    $l1->debug("xxxxinfo");
    $l1->info("xxxxinfo");
    $l3 = new logger("event");
    $l3->debug("xxxxdebug","r");
    $l3->info("xxxxinfo","w");
}
$root=$_SERVER['HOME'] . "/devspace/pylon-ng/smasher/data/" ;
pylon_dict_data("$root/data_2.txt","=","****");
assert(pylon_dict_count() == 213 );
assert(pylon_dict_find("=XAop") == "****/pylon/xmvc/xmvc.php");
pylon_rest_data("$root/_router.idx");
$ex1 ='{ "rule" : "/web/html/abc",  "cls" : "Html2svc"   }' ;
$found = pylon_rest_find("/web/html/abc");
assert($ex1 == $found) ;
$ex2 = '{ "rule" : "/web/html/$sub",  "cls" : "Htmlsvc"   , "uri": {"sub" : "xyz"} }' ;
$found = pylon_rest_find("/web/html/xyz");
assert($ex2 == $found) ;
$found = pylon_rest_find("/gexample/test2/sxd/score?uid=5555&name=yangwm&type=g");
$ex3 = '{ "rule" : "/gexample/test2/$gkey/$method",  "cls" : "gexample_test2"   , "uri": {"gkey" : "sxd","method" : "score"} }' ;
assert($ex3 == $found) ;
