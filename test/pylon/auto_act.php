<?php

abstract class xaction
{
    abstract public function  do_get($vars,$xcontext,$arg1,$arg2);

    public function setup($vars,$xcontext,$arg1,$arg2){}
    public function tearDown($vars,$xcontext,$arg1,$arg2){}
    public function run($vars,$xcontext,$arg1,$arg2)
    {
        $this->setup($vars,$xcontext,$arg1,$arg2);
        $this->do_get($vars,$xcontext,$arg1,$arg2);
        $this->tearDown($vars,$xcontext,$arg1,$arg2);
    }
}

class  action_base  extends xaction
{
    public static $success =  "T:cviews/admin_success.html";
    public static $failure =  "T:cviews/admin_error.html";

    public function do_get($vars,$xcontext,$dda,$arg2)
    {}
}

class action_login  extends action_base
{
    public static $success =  "N:main";
    public static $failure =  "T:AUTO";
    public static $_name   =  "LOGIN";
    public static $_tags   =  "login,user";

    public function  do_get($vars,$xcontext,$dda,$arg2)
    {
    }

    public function  do_post($vars,$xcontext,$dda,$arg2)
    {

    }
}

class intercept_conf
{
    regex_rule("name",".*",XConst::LOGIC_SCOPE_IPOS, new AdminCommonIntc(),new AutoCommit());
    regex_rule("path","admin/.*",XConst::LOGIC_SCOPE_IPOS, new AdminCommonIntc(),new AutoCommit());
}

$cls     = new ReflectionClass("action_login");
foreach ( $cls->getStaticProperties()  as $k => $v )
{
    print $k  . " | " . $v . "\n" ;
}
