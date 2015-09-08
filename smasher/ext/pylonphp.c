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
/*extern "C"*/
/*{*/
#include "log_sysl.h"
/*}*/

/* If you declare any globals in php_pylonphp.h uncomment this:
ZEND_DECLARE_MODULE_GLOBALS(pylonphp)
*/

/* True global resources - no need for thread safety here */
static int le_pylonphp;

/* {{{ pylonphp_functions[]
 *
 * Every user visible function must have an entry in pylonphp_functions[].
 */
zend_function_entry pylonphp_functions[] = {
    /* PHP_FE(pylon_sdict_using,NULL) */
    /* PHP_FE(pylon_sdict_create,NULL) */
    /* PHP_FE(pylon_sdict_remove,NULL) */
    /* PHP_FE(pylon_sdict_data,NULL) */
    /* PHP_FE(pylon_sdict_find,NULL) */
    /* PHP_FE(pylon_sdict_count,NULL) */

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
/* }}} */


#ifdef COMPILE_DL_PYLONPHP
BEGIN_EXTERN_C()
ZEND_GET_MODULE(pylonphp)
END_EXTERN_C()
#endif

/* {{{ PHP_INI
 */
/* Remove comments and fill if you need to have entries in php.ini
PHP_INI_BEGIN()
    STD_PHP_INI_ENTRY("pylonphp.global_value",      "42", PHP_INI_ALL, OnUpdateLong, global_value, zend_pylonphp_globals, pylonphp_globals)
    STD_PHP_INI_ENTRY("pylonphp.global_string", "foobar", PHP_INI_ALL, OnUpdateString, global_string, zend_pylonphp_globals, pylonphp_globals)
PHP_INI_END()
*/
/* }}} */

/* {{{ php_pylonphp_init_globals
 */
/* Uncomment this function if you have INI entries
static void php_pylonphp_init_globals(zend_pylonphp_globals *pylonphp_globals)
{
	pylonphp_globals->global_value = 0;
	pylonphp_globals->global_string = NULL;
}
*/
/* }}} */


/* {{{ PHP_MSHUTDOWN_FUNCTION
 */
PHP_MSHUTDOWN_FUNCTION(pylonphp)
{
	/* uncomment this line if you have INI entries
	UNREGISTER_INI_ENTRIES();
	*/
	return SUCCESS;
}
/* }}} */

/* Remove if there's nothing to do at request start */
/* {{{ PHP_RINIT_FUNCTION
 */
PHP_RINIT_FUNCTION(pylonphp)
{
	return SUCCESS;
}
/* }}} */

/* Remove if there's nothing to do at request end */
/* {{{ PHP_RSHUTDOWN_FUNCTION
 */
PHP_RSHUTDOWN_FUNCTION(pylonphp)
{
	return SUCCESS;
}
/* }}} */

/* {{{ PHP_MINFO_FUNCTION
 */
PHP_MINFO_FUNCTION(pylonphp)
{
	php_info_print_table_start();
	php_info_print_table_header(2, "pylonphp support", "enabled");
	php_info_print_table_end();

	/* Remove comments if you have entries in php.ini
	DISPLAY_INI_ENTRIES();
	*/
}
/* }}} */


/* Remove the following function when you have succesfully modified config.m4
   so that your module can be compiled into PHP, it exists only for testing
   purposes. */

/* Every user-visible function in PHP should document itself in the source */
/* {{{ proto string confirm_pylonphp_compiled(string arg)
   Return a string to confirm that the module is compiled in */
PHP_FUNCTION(confirm_pylonphp_compiled)
{
	char *arg = NULL;
	int arg_len, len;
	char *strg;

	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &arg, &arg_len) == FAILURE) {
		return;
	}

	len = spprintf(&strg, 0, "Congratulations! You have successfully modified ext/%.78s/config.m4. Module %.78s is now compiled into PHP.", "pylonphp", arg);
	RETURN_STRINGL(strg, len, 0);
}
/* }}} */
/* The previous line is meant for vim and emacs, so it can correctly fold and
   unfold functions in source code. See the corresponding marks just before
   function definition, where the functions purpose is also documented. Please
   follow this convention for the convenience of others editing your code.
*/


/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */
/* PHP_FUNCTION(pylon_sdict_create) */
/* { */
/*  */
/*    char *space =NULL ; */
/*    int space_len=0; */
/*    long size=1; */
/*    long dynload=1; */
/*    long loadpgs=1; */
/*    int  argc = ZEND_NUM_ARGS(); */
/*    if (argc == 1 ) */
/*        if (zend_parse_parameters(argc TSRMLS_CC, "s", &space,&space_len) == FAILURE)  return ; */
/*  */
/*    if (argc == 2 ) */
/*        if (zend_parse_parameters(argc TSRMLS_CC, "sl", &space,&space_len,&size) == FAILURE)  return ; */
/*    if (argc == 3 ) */
/*        if (zend_parse_parameters(argc TSRMLS_CC, "sll", &space,&space_len,&size,&dynload) == FAILURE)  return ; */
/*    if (argc == 4 ) */
/*        if (zend_parse_parameters(argc TSRMLS_CC, "slll", &space,&space_len,&size,&dynload,&loadpgs) == FAILURE)  return ; */
/*    shared_dict_create(space,size); */
/* } */

/* PHP_FUNCTION(pylon_sdict_using) */
/* { */
/*  */
/*    char *space =NULL ; */
/*    int space_len=0; */
/*    int  argc = ZEND_NUM_ARGS(); */
/*    if (argc !=1 ) WRONG_PARAM_COUNT; */
/*    if (argc == 1 ) */
/*        if (zend_parse_parameters(argc TSRMLS_CC, "s", &space,&space_len) == FAILURE)  return ; */
/*  */
/*    shared_dict_using(space); */
/* } */


/* PHP_FUNCTION(pylon_sdict_data) */
/* { */
/*  */
/*     char* key_prefix  = NULL ; */
/*     char* data_prefix = NULL ; */
/*     char* file; */
/*     int data_plen,key_plen,file_len ; */
/*  */
/*     int  argc = ZEND_NUM_ARGS(); */
/*     if (argc !=3 ) WRONG_PARAM_COUNT; */
/*     if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "sss", &file ,&file_len, */
/*                 &key_prefix,&key_plen,&data_prefix,&data_plen) == FAILURE) return ; */
/*  */
/* #<{(|    *(prefix + prefix_len) = 0 ;|)}># */
/* #<{(|    *(file   + file_len )  = 0 ;|)}># */
/*     shared_dict_data(file,key_prefix,data_prefix,false); */
/* } */
PHP_FUNCTION(pylon_dict_data)
{

    char* key_prefix  = NULL ;
    char* data_prefix = NULL ;
    char* file;
    int data_plen,key_plen,file_len ;

    int  argc = ZEND_NUM_ARGS();
    if (argc !=3 ) WRONG_PARAM_COUNT;
    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "sss", &file ,&file_len,
                &key_prefix,&key_plen,&data_prefix,&data_plen) == FAILURE) return ;

/*    *(prefix + prefix_len) = 0 ;*/
/*    *(file   + file_len )  = 0 ;*/
    dict_data(file,key_prefix,data_prefix,false);
}

PHP_FUNCTION(pylon_rest_data)
{

    char* file;
    int file_len ;

    int  argc = ZEND_NUM_ARGS();
    if (argc !=1 ) WRONG_PARAM_COUNT;
    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &file ,&file_len) == FAILURE) return ;
    rest_data(file);
}

/* PHP_FUNCTION(pylon_sdict_find) */
/* { */
/*  */
/*  */
/*     char *key=NULL ; */
/*     int len=0; */
/*     int  argc = ZEND_NUM_ARGS(); */
/*     if (argc !=1 ) WRONG_PARAM_COUNT; */
/*     if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &key,&len ) == FAILURE) return ; */
/*  */
/* #<{(|    *(key+ len) = 0 ;|)}># */
/*     static char buf[BUF_SIZE]; */
/*     memset(buf,0,BUF_SIZE); */
/*     if(shared_dict_find(key,buf,BUF_SIZE)) */
/*     { */
/*         RETURN_STRING(buf, 1); */
/*     } */
/*     else */
/*     { */
/*         RETURN_NULL(); */
/*     } */
/* } */
PHP_FUNCTION(pylon_dict_find)
{


    char *key=NULL ;
    int len=0;
    int  argc = ZEND_NUM_ARGS();
    if (argc !=1 ) WRONG_PARAM_COUNT;
    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &key,&len ) == FAILURE) return ;

/*    *(key+ len) = 0 ;*/
    static char buf[BUF_SIZE];
    memset(buf,0,BUF_SIZE);
    if(dict_find(key,buf,BUF_SIZE))
    {
        RETURN_STRING(buf, 1);
    }
    else
    {
        RETURN_NULL();
    }
}

PHP_FUNCTION(pylon_rest_find)
{


    char *key=NULL ;
    int len=0;
    int  argc = ZEND_NUM_ARGS();
    if (argc !=1 ) WRONG_PARAM_COUNT;
    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &key,&len ) == FAILURE) return ;

/*    *(key+ len) = 0 ;*/
    static char buf[BUF_SIZE];
    memset(buf,0,BUF_SIZE);
    if(rest_find(key,buf,BUF_SIZE))
    {
        RETURN_STRING(buf, 1);
    }
    else
    {
        RETURN_NULL();
    }
}

PHP_FUNCTION(pylon_dict_has)
{


    char *key=NULL ;
    int len=0;
    int  argc = ZEND_NUM_ARGS();
    if (argc !=1 ) WRONG_PARAM_COUNT;
    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &key,&len ) == FAILURE) return ;

    if(dict_has(key))
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
    char *key=NULL ;
    int len=0;
    int argc = ZEND_NUM_ARGS();
    if (argc !=1 ) WRONG_PARAM_COUNT;
    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &key,&len ) == FAILURE) return ;

    static char buf[BUF_SIZE];
    memset(buf,0,BUF_SIZE);
    if(dict_prompt(key,buf,BUF_SIZE))
    {
        RETURN_STRING(buf, 1);
    }
    else
    {
        RETURN_NULL();
    }
}

/* PHP_FUNCTION(pylon_sdict_count) */
/* { */
/*  */
/*     int count = shared_dict_count(); */
/*     RETURN_LONG(count); */
/* } */

PHP_FUNCTION(pylon_dict_count)
{

    int count = dict_count();
    RETURN_LONG(count);
}

/* PHP_FUNCTION(pylon_sdict_remove) */
/* { */
/*     char* space= NULL ; */
/*     int   space_len; */
/*  */
/*     int  argc = ZEND_NUM_ARGS(); */
/*     if (argc !=1 ) WRONG_PARAM_COUNT; */
/*     if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &space,&space_len) == FAILURE) return ; */
/*     shared_dict_remove(space); */
/* } */



zend_object_handlers logger_object_handlers;
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

zend_object_value logger_create_handler(zend_class_entry *type TSRMLS_DC){
    zval *tmp;
    zend_object_value retval;

    logger_object *object = (logger_object *)emalloc(sizeof(logger_object));
    memset(object,0,sizeof(logger_object));

    object->std.ce = type;
    ALLOC_HASHTABLE(object->std.properties);
    zend_hash_init(object->std.properties, 0, NULL, ZVAL_PTR_DTOR, 0);

#if PHP_VERSION_ID >= 50400
    object_properties_init((zend_object* ) object,type) ;
#else
    zend_hash_copy(object->std.properties, &type->default_properties,
            (copy_ctor_func_t)zval_add_ref, (void *)&tmp, sizeof(zval *));

#endif
    retval.handle = zend_objects_store_put(object, NULL, logger_free_storage, NULL TSRMLS_CC);


    retval.handlers = &logger_object_handlers;

    return retval;
}



/*#include <iostream>*/
/*PHP_METHOD(log_kit,log_ins)*/
/*{*/
/*    char* name= NULL ;*/
/*    int   name_len;*/
/*    logger* plog;*/
/*    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &name,&name_len) == FAILURE) return ;*/

/*    plog = log_kit::log_ins(name);*/
/*    logger_object * object ;*/
/*    if(object_init_ex(return_value,logger_ce) != SUCCESS)*/
/*    {*/
/*    }*/
/*    object = (logger_object *) zend_object_store_get_object(return_value TSRMLS_CC);*/
/*    assert (object != NULL); //should not happen; object was just created*/
/*    object->obj =  plog ;*/
/*}*/

PHP_METHOD(log_kit,init)
{
    const char* name= "unknow" ;
    int   name_len;
    const char* tag= ""  ;
    int   tag_len;
    long level = (long)log_kit::error ;
    int   argc = ZEND_NUM_ARGS();
    if (argc == 1 )
        if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &name,&name_len) == FAILURE) return ;
    if (argc == 2 )
        if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "ss", &name,&name_len,&tag,&tag_len) == FAILURE) return ;
    if (argc == 3 )
        if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "ssl", &name,&name_len,&tag,&tag_len,&level) == FAILURE) return ;
    log_kit::init(name,tag,(log_kit::level_t)level);
}
PHP_METHOD(log_kit,clear)
{
    log_kit::clear();
}

PHP_METHOD(log_kit,event)
{
    char* name= NULL ;
    int   name_len;
    int   argc = ZEND_NUM_ARGS();
    if (argc == 1 )
    {
        if (zend_parse_parameters(argc TSRMLS_CC, "s", &name,&name_len ) == FAILURE)  return ;
        log_kit::event(name);
    }
}

PHP_METHOD(log_kit,level)
{
    char* name= NULL ;
    int   name_len;
    int   ratio = 1 ;
    long level = (long) log_kit::undef ;
    int   argc = ZEND_NUM_ARGS();
    if (argc == 2 )
    {
        if (zend_parse_parameters(argc TSRMLS_CC, "sl", &name,&name_len ,&level ) == FAILURE)  return ;
        log_kit::level(name,(log_kit::level_t)level,ratio);
    }
    if (argc == 3 )
    {
        if (zend_parse_parameters(argc TSRMLS_CC, "sll", &name,&name_len ,&level,&ratio ) == FAILURE)  return ;
        log_kit::level(name,(log_kit::level_t)level,ratio);
    }
}
PHP_METHOD(log_kit,tag)
{
    char* name= NULL ;
    int   name_len;
    const char* tag= ""  ;
    int   tag_len;
    int   argc = ZEND_NUM_ARGS();
    if (argc == 2 )
    {
        if (zend_parse_parameters(argc TSRMLS_CC, "ss", &name,&name_len ,&tag,&tag_len) == FAILURE)  return ;
        log_kit::tag(name,tag);
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


PHP_METHOD(log_kit,out)
{
    char* name= NULL ;
    int   name_len;
    long extra = 0 ;
    int   argc = ZEND_NUM_ARGS();
    if (argc == 2 )
    {
        if (zend_parse_parameters(argc TSRMLS_CC, "sl", &name,&name_len ,&extra ) == FAILURE)  return ;
        log_kit::out(name,(log_kit::outer_t)extra );
    }
}

PHP_METHOD(logger, __construct)
{

    char* name= NULL ;
    int   name_len;
    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &name,&name_len) == FAILURE)
    {
        RETURN_NULL();
    }

    logger_object *obj = (logger_object*)zend_object_store_get_object(getThis() TSRMLS_CC);
    obj->log=  new logger_proxy(name);
}

PHP_METHOD(logger,debug)
{
    char* msg= NULL ;
    int   msg_len;
    char* event= NULL ;
    int   event_len;
    int   argc = ZEND_NUM_ARGS();
    if(argc == 1 )
        if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &msg,&msg_len) == FAILURE) return ;
    if (argc ==2 )
        if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "ss", &msg,&msg_len,&event,&event_len) == FAILURE) return ;
    logger_object *object = (logger_object *)zend_object_store_get_object(getThis() TSRMLS_CC);
    object->log->debug(msg,event);
}
PHP_METHOD(logger,info)
{
    char* msg= NULL ;
    int   msg_len;
    char* event= NULL ;
    int   event_len;
    int   argc = ZEND_NUM_ARGS();
    if(argc == 1 )
        if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &msg,&msg_len) == FAILURE) return ;
    if (argc ==2 )
        if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "ss", &msg,&msg_len,&event,&event_len) == FAILURE) return ;
    logger_object *object = (logger_object *)zend_object_store_get_object(getThis() TSRMLS_CC);
    object->log->info(msg,event);
}

PHP_METHOD(logger,warn)
{
    char* msg= NULL ;
    int   msg_len;
    char* event= NULL ;
    int   event_len;
    int   argc = ZEND_NUM_ARGS();
    if(argc == 1 )
        if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &msg,&msg_len) == FAILURE) return ;
    if (argc ==2 )
        if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "ss", &msg,&msg_len,&event,&event_len) == FAILURE) return ;

    logger_object *object = (logger_object *)zend_object_store_get_object(getThis() TSRMLS_CC);
    object->log->warn(msg,event);
}
PHP_METHOD(logger,error)
{
    char* msg= NULL ;
    int   msg_len;
    char* event= NULL ;
    int   event_len;
    int   argc = ZEND_NUM_ARGS();
    if(argc == 1 )
        if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &msg,&msg_len) == FAILURE) return ;
    if (argc ==2 )
        if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "ss", &msg,&msg_len,&event,&event_len) == FAILURE) return ;

    logger_object *object = (logger_object *)zend_object_store_get_object(getThis() TSRMLS_CC);
    object->log->error(msg,event);
}


zend_function_entry log_kit_methods []  = {
/*    PHP_ME(log_kit,log_ins,NULL,ZEND_ACC_STATIC| ZEND_ACC_PUBLIC)*/
    PHP_ME(log_kit,level,NULL,ZEND_ACC_STATIC| ZEND_ACC_PUBLIC)
    PHP_ME(log_kit,event,NULL,ZEND_ACC_STATIC| ZEND_ACC_PUBLIC)
    PHP_ME(log_kit,tag,NULL,ZEND_ACC_STATIC| ZEND_ACC_PUBLIC)
    PHP_ME(log_kit,out,NULL,ZEND_ACC_STATIC| ZEND_ACC_PUBLIC)
    PHP_ME(log_kit,clear,NULL,ZEND_ACC_STATIC| ZEND_ACC_PUBLIC)
    PHP_ME(log_kit,init,NULL,ZEND_ACC_STATIC| ZEND_ACC_PUBLIC)
    PHP_ME(log_kit,channel,NULL,ZEND_ACC_STATIC| ZEND_ACC_PUBLIC)
    PHP_ME(log_kit,toall,NULL,ZEND_ACC_STATIC| ZEND_ACC_PUBLIC)
    {NULL,NULL,NULL}

};

zend_function_entry logger_methods []  = {
    PHP_ME(logger,__construct,NULL,ZEND_ACC_PUBLIC| ZEND_ACC_CTOR )
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
	/* If you have INI entries, uncomment these lines
	REGISTER_INI_ENTRIES();
	*/
    zend_class_entry ce ;
    INIT_CLASS_ENTRY(ce,"log_kit",log_kit_methods);
    log_kit_ce = zend_register_internal_class(&ce TSRMLS_CC);
    INIT_CLASS_ENTRY(ce,"logger",logger_methods);
    logger_ce  =  zend_register_internal_class(&ce TSRMLS_CC);

    logger_ce->create_object = logger_create_handler;
    memcpy(&logger_object_handlers, zend_get_std_object_handlers(), sizeof(zend_object_handlers));
    logger_object_handlers.clone_obj = NULL;

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
