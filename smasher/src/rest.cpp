//#include "../include/lib_def.h"
//#include "../include/smasher.h"
#include <string>
#include <sys/stat.h>
#include <map>
#include <boost/algorithm/string.hpp>
#include <boost/shared_ptr.hpp>
#include <fstream>
#include <sstream>
#include <list>
#include <iostream>
#include <boost/foreach.hpp>
#include <boost/tokenizer.hpp>
#include "rest.h"
#include "log_sysl.h"
#include <algorithm>  
#define  PLOG   log_kit::log_ins("_pylon")

using namespace std;
using namespace boost;

struct rest_dto
{
    typedef boost::shared_ptr<rest_dto> ptr;
    string rule;
    string cls ;

};
typedef std::pair<string,string> key_value_t;
typedef std::map<string,string>  kv_map_t  ;

struct rule_comparer
{
    enum status { VALUE_MATCH,RULE_MATCH };
    kv_map_t*       _dict ; 
    rule_comparer(kv_map_t* dict): _dict(dict){}
    int compare(const char* uri, const char* rule)
    {
        int match_level = 9 ;
        status st = VALUE_MATCH ;
        while(true)
        {
            if( (*uri == 0 || *uri == '?' )   && *rule == 0 ) return match_level ;
            //uri < rule 
            if( *uri == 0 || *uri == '?' ) return -1;
            //uri > rule 
            if( *rule == 0 ) return -1 ;
            switch(st)
            {
                case VALUE_MATCH :
                    if (*uri == *rule)
                    {
                        ++uri ;
                        ++rule ;
                        if ( *rule == '$') 
                        {
                            st = RULE_MATCH ;
                            //移动到 $ 后
                            ++rule;
                            continue ;
                        }
                    }
                    else
                    {
                        return -1 ;
                    }
                    break; 

                case RULE_MATCH :
                    string key;
                    while( *rule != '/'  && *rule != 0 ) 
                    {
                        key.push_back( *rule);
                        ++ rule ; 
                    }
                    string value;
                    while (*uri != '/' && *uri != 0  && *uri !='?' )
                    {
                        value.push_back( *uri);
                        ++ uri; 
                    }
                    (*_dict)[key] = value ;
                    st = VALUE_MATCH;
                    match_level  = 1 ;
                    break;
            }
        }
    }
};

struct rest_finder::impl
{
    typedef std::map<std::string,time_t >           loadtag_dict_t ;
    typedef std::vector<rest_dto::ptr >             rules_t ;  
    typedef std::vector<std::string>                str_arr;


    rules_t             _rules;
    loadtag_dict_t      _load_flags;

    void using_data( const std::string& data_file)
    {
        loadtag_dict_t::iterator found =  _load_flags.find(data_file);
        struct stat fileinfo;
        if( found  != _load_flags.end()  )
        {
            if (  stat(data_file.c_str(),&fileinfo) < 0 ) { return ; }
            //没有变化
            if (found->second >=  fileinfo.st_mtime ) return ;
        } 
        ifstream data(data_file.c_str()); 
        if(!data.good()) return ;


        char buf[BUF_SIZE];
        memset(buf,0,BUF_SIZE);
        int line_count = 0 ;
        while(data.good())
        {
            data.getline(buf,BUF_SIZE);
            str_arr strs;
            boost::split(strs,buf, boost::is_any_of(":"));
            if ( 2 == strs.size() )
            {
                boost::trim(strs[0]);
                boost::trim(strs[1]);
                str_arr rules;
                boost::split(rules,strs[0],boost::is_any_of(","));
                BOOST_FOREACH( string r  ,rules)
                {
                    rest_dto::ptr new_one(new rest_dto); 
                    boost::trim(r);
                    new_one->rule    = r ;
                    new_one->cls     = strs[1];
                    LOG_INFO_S(PLOG) << "add rule : " << r  << " cls : " << new_one->cls ;
                    _rules.push_back(new_one);
                }
                ++ line_count ;

            }
        }
        LOG_INFO_S(PLOG) << "dict load data file : " << data_file    << " load data " << line_count  ;
        sort(_rules.begin(),_rules.end());

        if (  stat(data_file.c_str(),&fileinfo) < 0 ) { return ; }
        _load_flags[data_file] = fileinfo.st_mtime;
    }


    bool find(const std::string& uri,char * buf , int buf_len)
    {
        rest_dto::ptr target(new rest_dto); 
        target->rule = uri;
        kv_map_t      var_dict;
        rest_dto::ptr found ;
        kv_map_t      found_dict ;
        int found_mlevel = 0 ;
        rule_comparer compare_obj(&var_dict);

        
        BOOST_FOREACH(rest_dto::ptr item, _rules) 
        {
            var_dict.clear() ;
            int mlevel =   compare_obj.compare(uri.c_str(),item->rule.c_str()) ;
            if (mlevel > 0 && mlevel > found_mlevel)
            {
                found_mlevel = mlevel ;
                found        = item ;
                found_dict   = var_dict ;
            }

        }
        if(found_mlevel > 0 )
        {
            LOG_DEBUG_S(PLOG) << "matched rule :" << found->rule << " cls : " << found->cls ;
            stringstream ss ;
            ss << "{ \"rule\" : \"" <<  found->rule << "\",  \"cls\" : \""  << found->cls   << "\"  " ;
            bool have_uri = false;
            BOOST_FOREACH(key_value_t i  , found_dict)
            {
                if (!have_uri)
                {
                    have_uri = true;
                    ss << " , \"uri\": {" ;
                }
                else 
                {
                    ss << "," ;
                }
                ss <<  "\"" << i.first << "\" : \"" << i.second  << "\"" ;
            }
            if(have_uri) ss << "}"   ;
            ss << " }" ;
            memset(buf,0,buf_len);
            strncpy(buf,ss.str().c_str(), buf_len);
            return true ;
        }
        LOG_INFO_S(PLOG) << "unfound uri match rule: " << uri;
        return false ;
    }


};

rest_finder::rest_finder():_pimpl(new rest_finder::impl) {}
rest_finder::~rest_finder() { delete _pimpl; }
void rest_finder::using_data(const char * data_file)
{
    _pimpl->using_data(data_file);

}
bool rest_finder::find(const char *uri,char * buf , int buf_len)
{
    return _pimpl->find(uri,buf,buf_len);
}

