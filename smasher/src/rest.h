#ifndef __REST_HPP__
#define __REST_HPP__
#include "../include/lib_def.h"
#include "../include/smasher.h"
#include <string>
#include "dicts.h"
class rest_finder
{/*{{{*/
    public:
        void using_data( const char* data_file);
        bool find(const char* uri,char * buf , int buf_len);
        rest_finder();
        ~rest_finder();
        struct impl;
    private:
        impl* _pimpl;
};/*}}}*/
#endif
