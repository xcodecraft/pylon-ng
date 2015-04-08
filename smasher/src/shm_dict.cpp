#include "../include/smasher.h"
#include "../include/lib_def.h"
#include <sys/stat.h>
#include <string>
#include <map>
#include <boost/algorithm/string.hpp>
#include <fstream>
#include <sstream>
#include <vector>
#include <iostream>
#include <boost/shared_ptr.hpp>
#include <boost/foreach.hpp>
#include <boost/tokenizer.hpp>
#include <boost/interprocess/containers/vector.hpp>
#include <boost/interprocess/containers/map.hpp>
#include <boost/interprocess/allocators/allocator.hpp>
#include <boost/interprocess/managed_shared_memory.hpp>
#include <boost/interprocess/containers/string.hpp>
#include <boost/interprocess/sync/scoped_lock.hpp>
#include <boost/interprocess/sync/named_mutex.hpp>
#include <boost/interprocess/sync/sharable_lock.hpp>
#include <boost/interprocess/sync/upgradable_lock.hpp>
#include <boost/interprocess/sync/interprocess_upgradable_mutex.hpp>
#include <boost/interprocess/managed_xsi_shared_memory.hpp>
#include "dicts.h"
#include "log_sysl.h"
#define  PLOG   log_kit::log_ins("_pylon")
using namespace std;
using namespace boost;
namespace bi=boost::interprocess;


typedef bi::managed_shared_memory                                   shared_memory_t;
typedef bi::managed_shared_memory::segment_manager                  segment_manager_t;

template < typename T > 
T* init_unique_res( shared_memory_t* segment )
{
    std::pair< T* , std::size_t> res  = segment->find< T >(bi::unique_instance) ;
    T*  value      = res.first ;
    if(res.second  == 0 )
    {
        value  = segment->construct< T >(bi::unique_instance)() ;
    }
    return value ;

}

template < typename T > 
T* init_unique_res( shared_memory_t* segment, T init_value )
{
    std::pair< T* , std::size_t> res  = segment->find< T >(bi::unique_instance) ;
    T*  value      = res.first ;
    if(res.second  == 0 )
    {
        value  = segment->construct< T >(bi::unique_instance)() ;
        *value = init_value ;
    }
    return value ;

}

struct shared_dict::impl
{/*{{{*/
        typedef bi::allocator<void, segment_manager_t>                      shm_void_alloc;
        typedef bi::allocator<char, segment_manager_t >                     shm_c_alloc;


        typedef bi::basic_string<char, std::char_traits<char>, shm_c_alloc> shm_string;
        typedef bi::allocator<shm_string, shm_c_alloc>                      shm_str_alloc ;    

        //dict_v_t 一定需要 const  shm_string ,不然会出错 
        typedef std::pair<const shm_string, shm_string>                     dict_v_t;
        typedef bi::allocator<dict_v_t, segment_manager_t>                  dict_v_allocator;
        typedef bi::map<shm_string,shm_string ,std::less<shm_string>,       dict_v_allocator> dict_t ;

        typedef std::pair<const shm_string, time_t >                        loadtag_type;
        typedef bi::allocator<loadtag_type, segment_manager_t>              loadtag_type_allocator;


        typedef bi::map<shm_string,time_t,std::less<shm_string>,loadtag_type_allocator > loadtag_dict_t ;

        typedef std::vector<std::string> str_arr;

        typedef bi::interprocess_upgradable_mutex                       dict_mutex_t ;
        typedef boost::shared_ptr<dict_mutex_t>                         named_mutex_sptr;


        loadtag_dict_t*                 _load_flags;

        dict_t *                        _data_zone_1;
        dict_t *                        _data_zone_2;
        int*                            _data_tag;
        shared_memory_t*                _segment;
        shm_c_alloc                     _str_alloc ;
        shm_void_alloc                  _alloc_inst ;

        static  string                  _s_shared_space;
        static  int                     _s_space_size_m;
        dict_mutex_t*                   _mutex;

        dict_t*   init_data_zone( const char * zone_name)
        {
            std::pair< dict_t* , std::size_t> res  = _segment->find<dict_t>(zone_name);
            dict_t * zone  = res.first ;
            if(res.second  == 0 )
                zone  = _segment->construct<dict_t>(zone_name)(std::less<shm_string>(),_alloc_inst);
            return zone ;
        }

        void  init_used_zone_flag()
        {

            _data_tag   = init_unique_res< int > ( _segment, 1 ) ; 
            _mutex      = init_unique_res< dict_mutex_t > ( _segment ) ; 
            std::pair< loadtag_dict_t* , std::size_t> flag_res  = _segment->find<loadtag_dict_t>(bi::unique_instance);
            _load_flags = flag_res.first;
            if(flag_res.second == 0 )
                _load_flags = _segment->construct<loadtag_dict_t>(bi::unique_instance)(std::less<shm_string>(),_alloc_inst);

        }
        impl(shared_memory_t* segment )
            :_segment(segment),_str_alloc(segment->get_segment_manager()),_alloc_inst(segment->get_segment_manager())
        {

            _data_zone_1 = init_data_zone( "dict1" ) ;
            _data_zone_2 = init_data_zone( "dict2" ) ;
            init_used_zone_flag() ; 
        }

        static void using_space(const std::string& space  )
        {
            _s_shared_space       = space;
        }
        static void create_space(const std::string& space ,int size_m )
        {
            _s_shared_space       = space;
            _s_space_size_m       = size_m;
        }
        void  proc_line_data( dict_t* dict ,const char* buf, const std::string& key_prefix, const std::string& data_prefix )
        {
            str_arr strs;
            boost::split(strs,buf, boost::is_any_of(","));
            if ( 2 == strs.size() )
            {
                boost::trim(strs[0]);
                boost::trim(strs[1]);

                std::string dict_key  = key_prefix  + strs[0]  ;
                std::string dict_val  = data_prefix + strs[1] ;

                dict_v_t value(  to_shm(dict_key), to_shm(dict_val));
                dict->insert(value);

            }
        }
        int load_file( dict_t* dict ,const std::string& data_file,const std::string& key_prefix, const std::string& data_prefix)
        {/*{{{*/
            LOG_INFO_S(PLOG)  << "shm_dict load file: " << data_file  ;

            int line=0;
            ifstream data(data_file.c_str()); 
            if (!data.good()) return line;
            dict->clear();

            char buf [ BUF_SIZE ] ;
            while(data.good())
            {
                memset( buf,BUF_SIZE,0 ) ;
                data.getline(buf,BUF_SIZE) ;

                proc_line_data(dict, buf,key_prefix,data_prefix) ;

                line ++ ;
            }
            LOG_INFO_S(PLOG)  << "shm_dict have load  : " <<   data_file << "  data cnt : " << line ;

            return line;

        }/*}}}*/

        shm_string to_shm(const std::string& str)
        {
            return shm_string(str.c_str(),_alloc_inst);
        }
        bool need_update( const string& data_file ,bool force )
        {
            loadtag_dict_t::iterator found =  _load_flags->find(to_shm(data_file));
            struct stat fileinfo;
            //have load file!!!
            if( found  != _load_flags->end()  )
            {
                if (  stat(data_file.c_str(),&fileinfo) < 0 ) { return  false ; }
                if (force  == false && found->second >=  fileinfo.st_mtime ) return  false ;
            } 
            return true ;
        }
        dict_t* idle_data_zone( )
        {
            dict_t* dict=NULL;
            if ( * _data_tag == 1 )
            {
                dict  = _data_zone_2;
            }
            else
            {
                dict  = _data_zone_1;
            }
            return dict ;
        }
        void swap_data_zone()
        {
            int  loaded_tag =  0;
            if ( * _data_tag == 1 )
            {
                loaded_tag = 2 ;
            }
            else
            {
                loaded_tag = 1 ;
            }
            * _data_tag  = loaded_tag ;
        }

        int using_data( const std::string& data_file,const std::string& key_prefix, const std::string& data_prefix, bool force)
        {/*{{{*/


            if ( ! need_update( data_file , force) ) return  0 ;

            //只有一个进程加载数据
            bi::scoped_lock<dict_mutex_t> lock(*_mutex,bi::try_to_lock);
            if(!lock) return  0 ;

            dict_t* dict=  idle_data_zone( ) ;
            int line =  load_file(dict,data_file,key_prefix,data_prefix ) ;
            if (line > 0 )
            {

                update_load_flag( data_file ) ;
                swap_data_zone();
            }
            return  line ;
        }/*}}}*/
        void update_load_flag(const string& data_file)
        {
            struct stat fileinfo;
            //记录读取文件的时间;
            if (   stat(data_file.c_str(),&fileinfo) < 0 )  return ;

            loadtag_type load_tag(  to_shm(data_file),fileinfo.st_mtime );
            loadtag_dict_t::iterator  fload =  _load_flags->find( load_tag.first);

            if ( fload == _load_flags->end())
                _load_flags->insert(load_tag);
            else
                fload->second = load_tag.second;
        }


        inline dict_t* chose_dict()
        {/*{{{*/
            if ( * _data_tag  ==  1 )
            {
                return  _data_zone_1;
            }
            else
            {
                return  _data_zone_2;
            }
        }/*}}}*/
        bool find(const std::string& key,char * buf , int buf_len)
        {/*{{{*/
            dict_t* dict = chose_dict();

            const shm_string shm_key(key.c_str(),_alloc_inst);   
            dict_t::iterator found = dict->find(shm_key) ;
            if( found == dict->end()) return  false;
            memset(buf,0,buf_len);
            strncpy(buf,found->second.c_str(), buf_len);
            return true;
        }/*}}}*/
        static shared_memory_t* ins()
        {/*{{{*/
            bi::permissions permit;
            permit.set_unrestricted();
            static shared_memory_t segment(bi::open_or_create , 
                    _s_shared_space.c_str() ,  
                    _s_space_size_m * 1024 * 1024 , 0 ,permit);
            return &segment;
        }/*}}}*/
        static shared_memory_t* read_ins()
        {/*{{{*/
            static shared_memory_t read_segment(bi::open_only, _s_shared_space.c_str()  );
            return &read_segment;
        }/*}}}*/



        int data_count()
        {/*{{{*/
            dict_t* dict = chose_dict();
            return dict->size();
        }/*}}}*/
        static std::string mutex_name(const std::string space)
        {/*{{{*/
            return  space + "_mutex";
        }/*}}}*/
        static void remove_space(const char* space)
        {/*{{{*/

            LOG_INFO_S(PLOG) << "clear shm_dict  " <<  space ;
            bi::shared_memory_object::remove(space);

        }/*}}}*/
};/*}}}*/
std::string  shared_dict::impl::_s_shared_space;
int          shared_dict::impl::_s_space_size_m = 1 ;



shared_dict::shared_dict(impl* p):_pimpl(p)
{}
void shared_dict::using_space(const std::string& space  )
{
    impl::using_space(space);
}
void shared_dict::create_space(const std::string& space ,int size_m )
{
    impl::create_space(space,size_m);
}
int shared_dict::using_data( const std::string& data_file,const std::string& key_prefix, const std::string& data_prefix, bool force )
{
    return _pimpl->using_data(data_file,key_prefix,data_prefix, force);
}
void shared_dict::chose_dict()
{
    _pimpl->chose_dict();
}
bool shared_dict::find(const std::string& key,char * buf , int buf_len)
{
    return _pimpl->find(key,buf,buf_len);
}
shared_dict* shared_dict::ins()
{
    shared_memory_t* shm = impl::ins();
    static shared_dict instance(new impl(shm));
    return &instance;
}
shared_dict* shared_dict::read_ins()
{
    shared_memory_t* shm = impl::read_ins();
    static shared_dict instance(new impl(shm));
    return &instance;
}
int shared_dict::data_count()
{
    return _pimpl->data_count();
}
std::string shared_dict::mutex_name(const std::string space)
{
    return impl::mutex_name(space);
}
void shared_dict::remove_space(const char* space)
{
    impl::remove_space(space);
}
shared_dict::~shared_dict(){ delete _pimpl ; }

