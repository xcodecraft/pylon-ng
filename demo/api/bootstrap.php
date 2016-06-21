<?php
$dsn = "http://3eda24fc940f45a39ffe96fbb513f37d:a477d0339f0f4d26a48ac57a0e957c07@status.demo.ayibang.cn/10" ;
RavenSetting::setup($dsn) ;
XAop::append_by_match_uri_method("/mygame/.*",        "get", new SucIntcpt());
XAop::append_by_match_uri_method("/mygame/exception", "post", new ErrIntcpt());
XAop::append_by_match_uri("/game/.*",                 new ErrIntcpt());
XAop::append_by_match_uri_method("/error/after/.*",   "get", new AfterErrItc());

