#

#XHttpCaller

``` php

$user       = $_SERVER['USER'] ;
$conf       = XHttpConf::localSvc("$user.demo.pylon360.com",80);
$this->curl = new XHttpCaller($conf);
$x          = $this->curl->get("/mygame/1234");

```
