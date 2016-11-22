/*
  +----------------------------------------------------------------------+
  | PHP Version 5                                                        |
  +----------------------------------------------------------------------+
  | Copyright (c) 1997-2010 The PHP Group                                |
  +----------------------------------------------------------------------+
  | This source file is subject to version 3.01 of the PHP license,      |
  | that is bundled with this package in the file LICENSE, and is        |
  | available through the world-wide-web at the following url:           |
  | http://www.php.net/license/3_01.txt                                  |
  | If you did not receive a copy of the PHP license and are unable to   |
  | obtain it through the world-wide-web, please send a note to          |
  | license@php.net so we can mail you a copy immediately.               |
  +----------------------------------------------------------------------+
  | Author:                                                              |
  +----------------------------------------------------------------------+
*/

/* $Id: header 297205 2010-03-30 21:09:07Z johannes $ */

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "php_ini.h"
#include "ext/standard/info.h"
#include "php_pylonphp.h"
#include "lib_def.h"
#include "smasher.h"
#include "log_sysl.h"
#include <string.h>
static int le_pylonphp;

zend_function_entry pylonphp_functions[] = {

    PHP_FE(pylon_dict_data,NULL)
    PHP_FE(pylon_dict_find,NULL)
    PHP_FE(pylon_dict_has,NULL)
    PHP_FE(pylon_dict_prompt,NULL)
    PHP_FE(pylon_dict_count,NULL)

    PHP_FE(pylon_rest_data,NULL)
    PHP_FE(pylon_rest_find,NULL)
	PHP_FE(confirm_pylonphp_compiled,	NULL)		/* For testing, remove later. */
	{NULL, NULL, NULL}	/* Must be the last line in pylonphp_functions[] */
};


#ifdef COMPILE_DL_PYLONPHP
BEGIN_EXTERN_C()
ZEND_GET_MODULE(pylonphp)
END_EXTERN_C()
#endif



PHP_MSHUTDOWN_FUNCTION(pylonphp)
{
	return SUCCESS;
}
PHP_RINIT_FUNCTION(pylonphp)
{
	return SUCCESS;
}
PHP_RSHUTDOWN_FUNCTION(pylonphp)
{
	return SUCCESS;
}
PHP_MINFO_FUNCTION(pylonphp)
{
	php_info_print_table_start();
	php_info_print_table_header(2, "pylonphp support", "enabled");
	php_info_print_table_end();

}

void cpy_zend_string(char* buf , zend_string* zstr , int buflen)
{
    memset(buf,  0,buflen) ;
    strncpy(buf,zstr->val,zstr->len);
}
PHP_FUNCTION(confirm_pylonphp_compiled)
{
	RETURN_STRING("YES") ;
}

PHP_FUNCTION(pylon_dict_data)
{

    zend_string * key_prefix ;
    zend_string * data_prefix ;
    zend_string * file ;

    int  argc = ZEND_NUM_ARGS();
    if (argc !=3 ) WRONG_PARAM_COUNT;
    if (zend_parse_parameters(ZEND_NUM_ARGS() , "SSS", &file , &key_prefix,&data_prefix) == FAILURE) return ;
    dict_data(file->val,key_prefix->val,data_prefix->val,false);
}

PHP_FUNCTION(pylon_rest_data)
{

    zend_string * file;

    int  argc = ZEND_NUM_ARGS();
    if (argc !=1 ) WRONG_PARAM_COUNT;
    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "S", &file ) == FAILURE) return ;
    rest_data(file->val);
}

PHP_FUNCTION(pylon_dict_find)
{

    zend_string * key ;
    int  argc = ZEND_NUM_ARGS();
    if (argc !=1 ) WRONG_PARAM_COUNT;
    if (zend_parse_parameters(ZEND_NUM_ARGS() , "S", &key) == FAILURE) return ;


    char buf[BUF_SIZE];
    if (dict_find(key->val,buf,BUF_SIZE) )
    {
        printf("xxxxx\n") ;
        printf(buf);
        printf("xxxxx\n") ;
        RETURN_STRING(buf) ;
    }
    else
    {
        RETURN_NULL();
    }
}

PHP_FUNCTION(pylon_rest_find)
{


    zend_string *key=NULL ;
    int  argc = ZEND_NUM_ARGS();
    if (argc !=1 ) WRONG_PARAM_COUNT;
    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "S", &key) == FAILURE) return ;

    char buf[BUF_SIZE];
    if(rest_find(key->val,buf,BUF_SIZE))
    {
        RETURN_STRING(buf) ;
    }
    else
    {
        RETURN_NULL();
    }
}

PHP_FUNCTION(pylon_dict_has)
{


    zend_string *key=NULL ;
    int  argc = ZEND_NUM_ARGS();
    if (argc !=1 ) WRONG_PARAM_COUNT;
    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "S", &key) == FAILURE) return ;
    if(dict_has(key->val))
    {
        RETURN_LONG(1);
    }
    else
    {
        RETURN_LONG(0);
    }
}

PHP_FUNCTION(pylon_dict_prompt)
{
    zend_string *key=NULL ;
    int argc = ZEND_NUM_ARGS();
    if (argc !=1 ) WRONG_PARAM_COUNT;
    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "S", &key) == FAILURE) return ;

    char buf[BUF_SIZE];
    if(dict_prompt(key->val,buf,BUF_SIZE))
    {
        RETURN_STRING(buf );
    }
    else
    {
        RETURN_NULL();
    }
}


PHP_FUNCTION(pylon_dict_count)
{

    int count = dict_count();
    RETURN_LONG(count);
}


zend_class_entry * log_kit_ce ;
zend_class_entry * logger_ce ;

struct log_kit_object
{
    zend_object std;
    log_kit*     obj;
};

struct logger_object
{
    zend_object std;
    logger_proxy*     log;
};

void logger_free_storage(void *object TSRMLS_DC){
    logger_object *o = (logger_object *)object;
    delete o->log;

    zend_hash_destroy(o->std.properties);
    FREE_HASHTABLE(o->std.properties);
    efree(o);
}


PHP_METHOD(log_kit,init)
{
    zend_string * name ;
    zend_string * tag ;
    long level = (long)log_kit::error ;
    int   argc = ZEND_NUM_ARGS();
    if (argc == 1 )
        if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "S", &name) == FAILURE) return ;
    if (argc == 2 )
        if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "SS", &name,&tag) == FAILURE) return ;
    if (argc == 3 )
        if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "SSl", &name,&tag,&level) == FAILURE) return ;
    log_kit::init(name->val,tag->val,(log_kit::level_t)level);
}
PHP_METHOD(log_kit,clear)
{
    log_kit::clear();
}

PHP_METHOD(log_kit,event)
{
    zend_string* name= NULL ;
    int   argc = ZEND_NUM_ARGS();
    if (argc == 1 )
    {
        if (zend_parse_parameters(argc TSRMLS_CC, "S", &name) == FAILURE)  return ;
        log_kit::event(name->val);
    }
}

PHP_METHOD(log_kit,level)
{
    zend_string * name = NULL ;
    long ratio = 1 ;
    long level  = (long) log_kit::undef ;
    int   argc  = ZEND_NUM_ARGS();
    if (argc == 2 )
    {
        if (zend_parse_parameters(argc , "Sl", &name,&level ) == FAILURE)  return ;
        log_kit::level(name->val,(log_kit::level_t)level,ratio);
    }
    if (argc == 3 )
    {
        if (zend_parse_parameters(argc , "Sll", &name,&level,&ratio ) == FAILURE)  return ;
        log_kit::level(name->val,(log_kit::level_t)level,ratio);
    }
}
PHP_METHOD(log_kit,tag)
{
    zend_string * name= NULL ;
    zend_string * tag  ;
    int   argc = ZEND_NUM_ARGS();
    if (argc == 2 )
    {
        if (zend_parse_parameters(argc TSRMLS_CC, "SS", &name,&tag) == FAILURE)  return ;
        log_kit::tag(name->val,tag->val);
    }
}

PHP_METHOD(log_kit,channel)
{
    long in_chn = 6;
    int   argc = ZEND_NUM_ARGS();
    if (argc == 1 )
    {
        if (zend_parse_parameters(argc TSRMLS_CC, "l", &in_chn ) == FAILURE)  return ;
        if( in_chn <= 7 && in_chn >=0 )
        {
            log_kit::channel_t chn =  (log_kit::channel_t)in_chn  ;
            log_kit::channel(chn);
        }
    }
}
PHP_METHOD(log_kit,toall)
{
    bool enable = false  ;
    int   argc = ZEND_NUM_ARGS();
    if (argc == 1 )
    {
        if (zend_parse_parameters(argc TSRMLS_CC, "b", &enable ) == FAILURE)  return ;
        log_kit::toall(enable );
    }
}



PHP_METHOD(logger, __construct)
{

    zend_string * name= NULL ;
    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "S", &name) == FAILURE)
    {
        RETURN_NULL();
    }

    zval *impl = zend_read_property(logger_ce, getThis(), ZEND_STRL("_impl"), 1, NULL);
    logger_proxy *p =  new logger_proxy(name->val);
    ZVAL_LONG(impl,(long)p);
}

PHP_METHOD(logger, __destruct)
{

    zval *impl = zend_read_property(logger_ce, getThis(), ZEND_STRL("_impl"), 1, NULL);
    logger_proxy* p = (logger_proxy * )impl->value.lval ;
    delete p ;
}

PHP_METHOD(logger,debug)
{
    zend_string * msg= NULL ;
    zend_string* event= NULL ;
    int   argc = ZEND_NUM_ARGS();
    if(argc == 1 )
        if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "S", &msg) == FAILURE) return ;
    if (argc ==2 )
        if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "SS", &msg,&event) == FAILURE) return ;
    zval *impl = zend_read_property(logger_ce, getThis(), ZEND_STRL("_impl"), 1, NULL);
    logger_proxy* p = (logger_proxy * )impl->value.lval ;
    p->debug(msg->val,event->val);
}
PHP_METHOD(logger,info)
{
    zend_string * msg= NULL ;
    zend_string * event= NULL ;
    int   argc = ZEND_NUM_ARGS();
    if(argc == 1 )
        if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "S", &msg) == FAILURE) return ;
    if (argc ==2 )
        if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "SS", &msg,&event) == FAILURE) return ;
    zval *impl = zend_read_property(logger_ce, getThis(), ZEND_STRL("_impl"), 1, NULL);
    logger_proxy* p = (logger_proxy * )impl->value.lval ;
    p->info(msg->val,event->val);
}

PHP_METHOD(logger,warn)
{
    zend_string * msg= NULL ;
    zend_string * event= NULL ;
    int   argc = ZEND_NUM_ARGS();
    if(argc == 1 )
        if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "S", &msg) == FAILURE) return ;
    if (argc ==2 )
        if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "SS", &msg,&event) == FAILURE) return ;

    zval *impl = zend_read_property(logger_ce, getThis(), ZEND_STRL("_impl"), 1, NULL);
    logger_proxy* p = (logger_proxy * )impl->value.lval ;
    p->warn(msg->val,event->val);
}
PHP_METHOD(logger,error)
{

    zend_string * msg= NULL ;
    zend_string * event= NULL ;
    int   argc = ZEND_NUM_ARGS();
    if(argc == 1 )
        if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "S", &msg) == FAILURE) return ;
    if (argc ==2 )
        if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "SS", &msg,&event) == FAILURE) return ;
    zval *impl = zend_read_property(logger_ce, getThis(), ZEND_STRL("_impl"), 1, NULL);
    logger_proxy* p = (logger_proxy * )impl->value.lval ;
    p->error(msg->val,event->val);
}


zend_function_entry log_kit_methods []  = {
    PHP_ME(log_kit,level,NULL,ZEND_ACC_STATIC| ZEND_ACC_PUBLIC)
    PHP_ME(log_kit,event,NULL,ZEND_ACC_STATIC| ZEND_ACC_PUBLIC)
    PHP_ME(log_kit,tag,NULL,ZEND_ACC_STATIC| ZEND_ACC_PUBLIC)
    PHP_ME(log_kit,clear,NULL,ZEND_ACC_STATIC| ZEND_ACC_PUBLIC)
    PHP_ME(log_kit,init,NULL,ZEND_ACC_STATIC| ZEND_ACC_PUBLIC)
    PHP_ME(log_kit,channel,NULL,ZEND_ACC_STATIC| ZEND_ACC_PUBLIC)
    PHP_ME(log_kit,toall,NULL,ZEND_ACC_STATIC| ZEND_ACC_PUBLIC)
    {NULL,NULL,NULL}

};

zend_function_entry logger_methods []  = {
    PHP_ME(logger,__construct,NULL,ZEND_ACC_PUBLIC| ZEND_ACC_CTOR )
    PHP_ME(logger,__destruct,NULL,ZEND_ACC_PUBLIC| ZEND_ACC_DTOR )
    PHP_ME(logger,debug,NULL,ZEND_ACC_PUBLIC )
    PHP_ME(logger,info,NULL,ZEND_ACC_PUBLIC )
    PHP_ME(logger,warn,NULL,ZEND_ACC_PUBLIC )
    PHP_ME(logger,error,NULL,ZEND_ACC_PUBLIC )
    {NULL,NULL,NULL}
};

/* {{{ PHP_MINIT_FUNCTION
 */
PHP_MINIT_FUNCTION(pylonphp)
{
    zend_class_entry ce ;
    INIT_CLASS_ENTRY(ce,"log_kit",log_kit_methods);
    log_kit_ce = zend_register_internal_class(&ce );


    INIT_CLASS_ENTRY(ce,"logger",logger_methods);
    logger_ce  =  zend_register_internal_class(&ce );
    zend_declare_property_null(logger_ce, ZEND_STRL("_impl"),  ZEND_ACC_PROTECTED);
	return SUCCESS;
}
/* }}} */



/* {{{ pylonphp_module_entry
 */
zend_module_entry pylonphp_module_entry = {
#if ZEND_MODULE_API_NO >= 20010901
	STANDARD_MODULE_HEADER,
#endif
	"pylonphp",
	pylonphp_functions,
	PHP_MINIT(pylonphp),
	PHP_MSHUTDOWN(pylonphp),
	PHP_RINIT(pylonphp),		/* Replace with NULL if there's nothing to do at request start */
	PHP_RSHUTDOWN(pylonphp),	/* Replace with NULL if there's nothing to do at request end */
	PHP_MINFO(pylonphp),
#if ZEND_MODULE_API_NO >= 20010901
	"0.1", /* Replace with version number for your extension */
#endif
	STANDARD_MODULE_PROPERTIES
};
/* }}} */
