#

#XHttpCaller

``` php

$user = $_SERVER['USER'] ;
$conf = XHttpConf::localSvc("$user.demo.pylon360.com",80);
$curl = new XHttpCaller($conf);
$resp = $curl->get("/mygame/1234");
$data = XRestResult::ok($resp) ;


```
