#ifndef __DICTS_HPP__
#define __DICTS_HPP__
#include "../include/lib_def.h"
#include "../include/smasher.h"
#include <boost/utility.hpp>
#include <boost/shared_ptr.hpp>
#include <string>
class dict  :  boost::noncopyable
{/*{{{*/
    public:
        int using_data( const std::string& data_file,
                const std::string& key_prefix, 
                const std::string& data_prefix, 
                bool force );
        bool find(const std::string& cls,char * buf , int buf_len);
        bool has(const std::string& cls);

        int prompt(const std::string& cls,char * buf , int buf_len);
        int data_count();

        static dict* ins();
    private:
        dict();
        struct impl;
        boost::shared_ptr<impl> _pimpl;
};/*}}}*/



class shared_dict
{/*{{{*/
    public:
        static void using_space(const std::string& space  );
        static void create_space(const std::string& space ,int size_m );

        int using_data( const std::string& data_file,
                const std::string& key_prefix, 
                const std::string& data_prefix,
                bool force);

        void chose_dict();
        bool find(const std::string& key,char * buf , int buf_len);

//        void prompt(const std::string& cls,char * buf , int buf_len);
        int data_count();

        static std::string mutex_name(const std::string space);
        static void remove_space(const char* space);
        static shared_dict* ins();
        static shared_dict* read_ins();
    private:
        struct impl;
        impl* _pimpl;
        shared_dict(impl* p);
        ~shared_dict();
};/*}}}*/
#endif 
