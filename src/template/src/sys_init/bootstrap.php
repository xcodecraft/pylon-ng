<?php
$host     = XEnv::get("DB_HOST") ;
$name     = XEnv::get("DB_NAME") ;
$user     = XEnv::get("DB_USER") ;
$pwd      = XEnv::get("DB_PWD" ) ;
$executer = new LZLExecutor($host,$user,$pwd,$name,
                            FastSQLExecutor::SHORT_CONN,'utf8',"FastSQLExecutor");

XEntEnv::simpleSetup($executer) ;
