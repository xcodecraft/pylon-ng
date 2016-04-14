<?php
XAop::append_by_match_uri_method("/mygame/.*",        "get", new SucIntcpt());
XAop::append_by_match_uri_method("/mygame/exception", "post", new ErrIntcpt());
XAop::append_by_match_uri("/game/.*",                 new ErrIntcpt());
XAop::append_by_match_uri_method("/error/after/.*",   "get", new AfterErrItc());

