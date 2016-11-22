#include "../include/lib_def.h"
#include "../include/smasher.h"
#include <string>
#include <sys/stat.h>
#include <map>
#include <boost/algorithm/string.hpp>
#include <fstream>
#include <sstream>
#include <vector>
#include <list>
#include <iostream>
#include <boost/foreach.hpp>
#include <boost/tokenizer.hpp>
#include "dicts.h"
#include "log_sysl.h"
#define  PLOG   log_kit::log_ins("_pylon")

using namespace std;
using namespace boost;

struct dict::impl
{/*{{{*/
    public:
        typedef std::map<std::string, std::string>          map_t ;
        typedef std::pair<std::string, std::string>         pair_t ;

        typedef std::map<std::string,time_t >               file_tags_t ;

        typedef std::vector<std::string>                    str_arr;
        typedef boost::shared_ptr< std::vector<std::string> >     str_arr_sptr;
        typedef std::map<std::string,str_arr_sptr >         keys_dict_t ;

        map_t               _dict;
        file_tags_t         _file_flags;
        keys_dict_t         _file_keys;

        void clear_dict(str_arr_sptr file_keys)
        {
            str_arr& the_keys = *file_keys ;
            BOOST_FOREACH(string key , the_keys )
            {
                _dict.erase(key);
            }
            file_keys->clear();
        }
        str_arr_sptr get_file_keys(const std::string& data_file)
        {
            keys_dict_t::iterator found = _file_keys.find(data_file);
            if(found == _file_keys.end())
            {
                _file_keys[data_file]   = str_arr_sptr(new str_arr);
                found                   = _file_keys.find(data_file);

            }

            return found->second;
        }
        bool data_need_update( const std::string& data_file, bool force )
        {

            file_tags_t::iterator found = _file_flags.find(data_file);
            // 已经load 的文件
            if( found  != _file_flags.end()  )
            {
                struct stat fileinfo;
                if (  stat(data_file.c_str(),&fileinfo) < 0 ) {
                    return false ;
                }
                time_t last = found->second ;
                //文件没有更新
                if ( force == false && (last >=  fileinfo.st_mtime)  )  {
                    return false ;
                }
            }
            return true ;
        }
        int using_data(    const std::string& data_file,
                const std::string& key_prefix,
                const std::string& data_prefix,bool force )
        {/*{{{*/

            if ( ! data_need_update( data_file, force)  ) return 0 ;


            ifstream data(data_file.c_str());
            if(!data.good()) return  0 ;

            str_arr_sptr file_keys = get_file_keys(data_file);
            clear_dict(file_keys);

            char buf [BUF_SIZE];
            int  update_cnt = 0 ;
            while(data.good())
            {
                memset(buf,0,BUF_SIZE);
                data.getline(buf,BUF_SIZE);
                str_arr strs;
                boost::split(strs,buf, boost::is_any_of(","));
                if ( 2 == strs.size() )
                {
                    boost::trim(strs[0] );
                    boost::trim(strs[1] );
                    string dict_key  = key_prefix  + strs[0]  ;
                    _dict[dict_key]  = data_prefix + strs[1] ;
                    file_keys->push_back(dict_key);
                    ++ update_cnt ;

                }
            }

            struct stat fileinfo;
            if (  stat(data_file.c_str(),&fileinfo) < 0 ) { return 0  ; }
            _file_flags[data_file] = fileinfo.st_mtime;
            LOG_INFO_S(PLOG)
                << "dict load data file : " << data_file
                <<  " key_prefix: '"        << key_prefix
                << "' data_prefix: '"       << data_prefix
                << "' update cnt : "        << update_cnt;
            return update_cnt ;
        }/*}}}*/


        bool find(const std::string& cls,char * buf , int buf_len)
        {/*{{{*/
            map_t::iterator found = _dict.find(cls) ;
            if( found == _dict.end())
            {
                LOG_DEBUG_S(PLOG) << "not found key: " << cls ;
                return  false;
            }
            memset(buf,0,buf_len);
            strncpy(buf,found->second.c_str(), buf_len);
            return true;
        }/*}}}*/

        bool has(const std::string& cls)
        {/*{{{*/
            map_t::iterator found = _dict.find(cls) ;
            return  found != _dict.end() ;
        }/*}}}*/

        int prompt_impl(const std::string& cls,char * buf , int buf_len, int cutlen = 4 )
        {
            if (cutlen < 3 )
                return 0 ;

            //            LOG_DEBUG(PLOG) <<  "cls : " << cls  ;
            int len = cls.size();
            vector<int> offsets;
            //分割
            for(int i = cutlen ; i< len - cutlen ; i+= cutlen)
            {
                offsets.push_back(i);
            }
            offsets.push_back(len+1);

            str_arr strs;
            offset_separator f(offsets.begin(), offsets.end());
            tokenizer<offset_separator> tok(cls,f);
            for(tokenizer<offset_separator>::iterator beg=tok.begin(); beg!=tok.end();++beg){
                strs.push_back(*beg);
            }
            str_arr data;
            BOOST_FOREACH(string i , strs)
            {
                search_key(i,data);
            }
            stringstream str_buf;
            str_buf << "all data " <<  _dict.size() << ", options : " ;
            BOOST_FOREACH(string i, data)
            {
                str_buf <<  i  << " " ;
            }
            strncpy(buf,str_buf.str().c_str(), buf_len);
            return data.size();
        }
        int prompt(const std::string& cls,char * buf , int buf_len)
        {/*{{{*/
            int cutlen =  10;
            int rescnt =  0;
            while(cutlen >= 3 )
            {
                rescnt = prompt_impl(cls,buf,buf_len,cutlen);
                if( rescnt  <= 7 )
                    return rescnt ;
                cutlen -- ;
            }
            return 0 ;

        }/*}}}*/
        void search_key(const std::string& tag, str_arr& data)
        {/*{{{*/
            BOOST_FOREACH(pair_t i , _dict)
            {
                if(boost::icontains( i.first,tag))
                {
                    data.push_back(i.first);
                }
            }
        }/*}}}*/
        int data_count()
        {
            return _dict.size();
        }
};/*}}}*/


int dict::using_data( const std::string& data_file,const std::string& key_prefix, const std::string& data_prefix, bool force)
{
    return  _pimpl->using_data(data_file,key_prefix,data_prefix, force);
}
bool dict::find(const std::string& cls,char * buf , int buf_len)
{
    return _pimpl->find(cls,buf,buf_len);
}
bool dict::has(const std::string& cls)
{
    return _pimpl->has(cls);
}
dict* dict::ins()
{
    static dict s_ins;
    return &s_ins;
}
int dict::prompt(const std::string& cls,char * buf , int buf_len)
{
    return  _pimpl->prompt(cls,buf,buf_len);
}
int dict::data_count()
{
    return _pimpl->data_count();
}
dict::dict():_pimpl(new dict::impl) {}
