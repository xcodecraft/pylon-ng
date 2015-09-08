#include "../include/lib_def.h"
#include "../include/smasher.h"
#include <string>
#include <map>
#include <boost/algorithm/string.hpp>
#include <fstream>
#include <sstream>
#include <vector>
#include <iostream>
#include <boost/foreach.hpp>
#include<boost/tokenizer.hpp>
#include "dicts.h"
#include "rest.h"
using namespace std;
using namespace boost;


// void shared_dict_using(const char*  proc_space )
// {
//     shared_dict::using_space(proc_space);
// }
//
// void shared_dict_create(const char*  proc_space, int msize)
// {
//     shared_dict::create_space(proc_space,msize);
// }
//
// void shared_dict_remove(const char*  space)
// {
//     shared_dict::remove_space(space);
//
// }
//
// int shared_dict_data(const char * data_file,const char* key_prefix , const char* data_prefix, bool force)
// {
//     return shared_dict::ins()->using_data(data_file,key_prefix,data_prefix,force);
// }
//
// int shared_dict_find(const char* cls,char * buf , int buf_len)
// {
//     return shared_dict::read_ins()->find(cls,buf,buf_len) ? 1: 0;
// }
//

// int shared_dict_count()
// {
//    try{
//        return shared_dict::read_ins()->data_count();
//    }
//    catch(boost::interprocess::interprocess_exception& e){
//        std::cout<< "error: " <<  e.what();
//    }
//     return 0;
// }

int dict_data(const char * data_file,const char* key_prefix , const char* data_prefix,bool force )
{
    return dict::ins()->using_data(data_file,key_prefix,data_prefix,force);
}
int  dict_find(const char* cls, char * buf , int buf_len)
{
    return dict::ins()->find(cls,buf,buf_len);
}

bool dict_has(const char* cls)
{
    return dict::ins()->has(cls);
}
int dict_prompt(const char* cls, char * buf , int buf_len)
{
    return dict::ins()->prompt(cls,buf,buf_len) ;
}

int dict_count()
{
//    return 0;
    return dict::ins()->data_count();
}

static rest_finder s_rest_finder ;
void rest_data(const char* data_file)
{
    s_rest_finder.using_data(data_file);
}
bool rest_find(const char* uri, char* buf , int buf_len)
{
    return s_rest_finder.find(uri,buf,buf_len);
}
