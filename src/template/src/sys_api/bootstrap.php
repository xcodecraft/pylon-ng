<?php
#XAop::append_by_match_uri_method("/demo/.*" , "get", new SucIntcpt());
#XAop::append_by_match_uri("/demo/.*"          , new ErrIntcpt());
