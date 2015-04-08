#include "log_sysl.h"
#include "smasher.h"
#define BOOST_TEST_MODULE TEST_ERROR_INS 
#include <boost/test/included/unit_test.hpp>
#define  PLOG   log_kit::log_ins("pylon")

using namespace std;

#if defined OS_UNIX
    #define ROOT_PATH   L"/tmp"
    #define C         
#else
    #define ROOT_PATH   L"D:/"
    #define C       L      
#endif



BOOST_AUTO_TEST_CASE(test_log)
{
    log_kit::init("smasher","",log_kit::debug);
    log_kit::level("test1" , log_kit::debug);
    log_kit::level("test2" , log_kit::info);
    log_kit::level("test3" , log_kit::debug,10);
    log_kit::level("test4" , log_kit::warn);
    log_kit::event("evt1");
    log_kit::toall(true);
    log_kit::channel(log_kit::ch6);
    INS_LOG(log1,"test1");
    INS_LOG(log2,"test2");
    INS_LOG(log3,"test3");
    INS_LOG(log4,"test4");
    LOG_DEBUG(log1) << "debug";
    ELOG_DEBUG(log1,"evt2") << "debug11";
    LOG_DEBUG(log2) << "debug";
    LOG_DEBUG(log3) << "debug";
    LOG_DEBUG(log4) << "debug";
    LOG_INFO(log2) << "info";
    LOG_INFO(log2) << "haha%";
    LOG_INFO(log2) << "haha%shaha";
    LOG_INFO(log3) << "haha%shaha";
    log_kit::toall(false);
    LOG_INFO(log4) << "info";
    LOG_WARN(log4) << "warn";
    LOG_ERROR(log4) << "error";


    log_kit::clear();
}

const char* make_rest_reuslt(const char* rule, const char* cls, const char* ukey,const char* uval)
{
    static char buffer[1024];
    memset(buffer,0,1024);
    sprintf(buffer,"{ \"rule\" : \"%s\",  \"cls\" : \"%s\"   , \"uri\": {\"%s\" : \"%s\"} }",rule,cls,ukey,uval);
    return buffer;
}
BOOST_AUTO_TEST_CASE(test_rule)
{
    log_kit::init("pylon","",log_kit::debug);
    rest_data("./rest_1.txt");
    char buffer[1024];
    BOOST_CHECK(rest_find("/mygoods1/l234", buffer,1024));
    BOOST_CHECK_EQUAL("{ \"rule\" : \"/mygoods1/$uid\",  \"cls\" : \"mygoods1\"   , \"uri\": {\"uid\" : \"l234\"} }",buffer) ;
    cout << buffer << endl ;
    BOOST_CHECK(rest_find("/mygoods1/l234/", buffer,1024));
    cout << buffer << endl ;
    BOOST_CHECK(rest_find("/mygoods/l234/sn/223", buffer,1024));
    cout << buffer << endl ;
    BOOST_CHECK(rest_find("/mygoods/l234/sn/223", buffer,1024));
    cout << buffer << endl ;
    BOOST_CHECK(rest_find("/mygoods4/1234", buffer,1024));
    cout << buffer << endl ;
    BOOST_CHECK_EQUAL(buffer,make_rest_reuslt("/mygoods4/$uid","mygoods4","uid","1234"));
    BOOST_CHECK(rest_find("/mygoods4/1234?a=x", buffer,1024));
    cout << buffer << endl ;
    BOOST_CHECK_EQUAL(buffer,make_rest_reuslt("/mygoods4/$uid","mygoods4","uid","1234"));
    BOOST_CHECK(rest_find("/mygoods5/1234", buffer,1024));
    cout << buffer << endl ;
    BOOST_CHECK_EQUAL(buffer,make_rest_reuslt("/mygoods5/$uid","mygoods5","uid","1234"));
    BOOST_CHECK(rest_find("/mygoods6/1234", buffer,1024));
    cout << buffer << endl ;
    BOOST_CHECK_EQUAL(buffer,make_rest_reuslt("/mygoods6/$uid","mygoods5","uid","1234"));

    BOOST_CHECK(rest_find("/mygoods6/1234?xxx=1", buffer,1024));
    cout << buffer << endl ;
    BOOST_CHECK_EQUAL(buffer,make_rest_reuslt("/mygoods6/$uid","mygoods5","uid","1234"));
}

BOOST_AUTO_TEST_CASE( test_dict )
{

    log_kit::init("pylon","",log_kit::debug);
    char buffer[1024] ;
    memset(buffer,0,1024 ) ;
    int cnt = 0 ;
    cnt = dict_data("./_autoload_clspath.idx","","", false);
    cout << "load data cnt : " << cnt  << endl ;
    BOOST_CHECK( cnt >  0 ) ;

    cnt = dict_data("./not_found.idx","","",true);
    BOOST_CHECK( cnt == 0 ) ;

    cnt = dict_data("./_autoload_clspath.idx","","", false);
    BOOST_CHECK( cnt == 0 ) ;

    cnt = dict_data("./_autoload_clspath.idx","","", true);
    BOOST_CHECK( cnt >  0 ) ;

    int val =  dict_find("XPylon",buffer,1024 );
    BOOST_CHECK(val);
    cout << "find: " << buffer <<  " return : " << val << endl;
    memset(buffer,0,1024 ) ;
    val = dict_prompt("ylon",buffer,1024);
    BOOST_CHECK(val);
    cout << "prompt: " << buffer << endl;

    memset(buffer,0,1024 ) ;
    val = dict_prompt("lon",buffer,1024);
    BOOST_CHECK(val);
    cout << "prompt: " << buffer << endl;

    memset(buffer,0,1024 ) ;
    val = dict_prompt("l",buffer,1024);
    BOOST_CHECK(val == 0);

    memset(buffer,0,1024 ) ;
    val = dict_prompt("",buffer,1024);
    BOOST_CHECK(val == 0);
}

//    void shared_dict_create(const char*  proc_space, int msize);

//    void shared_dict_using(const char*  proc_space );

//    void shared_dict_remove(const char*  proc_space);

//    void shared_dict_data(const char * data_file,const char* key_prefix , const char* data_prefix);

//    int  shared_dict_find(const char* cls, char * buf , int buf_len);

//    int  shared_dict_count();

BOOST_AUTO_TEST_CASE( test_sdict )
{
    log_kit::init("pylon","",log_kit::debug);
    char buffer[1024] ;
    memset(buffer,0,1024 ) ;
    shared_dict_create("sdict_ut", 10) ;

    int cnt = 0 ;
    cnt = shared_dict_data("./_autoload_clspath.idx","","", false);
    cout << "shared load data cnt : " << cnt  << endl ;
    BOOST_CHECK( cnt >  0 ) ;

    cnt = shared_dict_data("./not_found.idx","","",true);
    BOOST_CHECK( cnt == 0 ) ;

    cnt = shared_dict_data("./_autoload_clspath.idx","","", false);
    BOOST_CHECK( cnt == 0 ) ;

    cnt = shared_dict_data("./_autoload_clspath.idx","","", true);
    BOOST_CHECK( cnt >  0 ) ;

    int val =  shared_dict_find("XPylon",buffer,1024 );
    BOOST_CHECK(val);
    cout << "shared find: " << buffer <<  " return : " << val << endl;
    shared_dict_remove ("sdict_ut" ) ;
}

