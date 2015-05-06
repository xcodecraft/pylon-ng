<?php
function log_ins($name)
{
    return new logger($name);
}
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
log_kit::init("unknow1","tagx",0);
log_kit::toall(false);
log_kit::channel(6);
log_kit::tag("_pylon","tagy");
log_kit::level("_pylon",1,2);
log_kit::out("_pylon",1);
log_kit::event("phpevent");
$l2 = new logger("_pylon");
$l2->debug("xxxxdebug","r");
log_kit::tag("_pylon","tagz");
$l2->debug("xxxxdebug","w");
$l1 = new logger("_pylon");
$l1->info("xxxxinfo");
$l1->info("xxxxinfo");


$l3 = new logger("event");
$l3->debug("xxxxdebug","r");
$l3->info("xxxxinfo","w");

pylon_sdict_remove("php_test");
pylon_sdict_create("php_test",1);
pylon_sdict_create("php_test",1,1);
pylon_sdict_create("php_test",1,1,1);
pylon_sdict_data("/home/luopan/devspace/pylon-ng/smasher/lib/data_1.txt","=","****");
var_dump(pylon_sdict_find("=cls_xaop"));
var_dump(pylon_sdict_count());


pylon_dict_data("/home/luopan/devspace/pylon-ng/smasher/lib/data_2.txt","=","****");
var_dump(pylon_dict_find("=XAop"));
var_dump(pylon_dict_count());

pylon_dict_data("/home/luopan/devspace/pylon-ng/smasher/lib/data_2.txt","=","****");
var_dump(pylon_dict_find("=XAop"));
var_dump(pylon_dict_count());
log_ins("test")->debug("xxxxdebug");
//log_kit::conf("all",0,1);
log_ins("test")->debug("xxxxdebug");
log_ins("_pylon")->debug("xxxxdebug");


//pylon_rest_data("/home/luopan/devspace/pylon-ng/smasher/lib/rest_1.txt");
//echo pylon_rest_find("/mygoods1/1234");
//pylon_sdict_remove("php_test");
//log_kit::clear();
?>
