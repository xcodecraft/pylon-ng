#include "log_sysl.h"
#include <syslog.h>
#include <sstream>
#include <map>
#include <boost/foreach.hpp>
#include <boost/shared_ptr.hpp>
#include <iostream>
#include <boost/algorithm/string/replace.hpp>
using namespace std;
static string s_prj("unknow") ;

void log2sys(log_kit::level_t lev,const char* name, const string& msg, int channel)
{

    int level = 0;
    switch(lev)
    {
        case log_kit::debug:
            level= LOG_DEBUG;
            break;
        case log_kit::info:
            level= LOG_INFO;
            break;
        case log_kit::warn:
            level = LOG_WARNING;
        case log_kit::error:
            level= LOG_ERR;
            break;
        default:
            return ;
    }
    openlog(name ,LOG_PID,channel);
    //syslog 有 % 号的bug , 使用 %% 进行转义
    string safe_msg =  msg;
    boost::replace_all(safe_msg,"%","%%");
    syslog(level,safe_msg.c_str(),strlen(safe_msg.c_str()));
    closelog();
}


struct logger::impl 
{/*{{{*/
    typedef std::basic_string<char >               string_t ;
    typedef std::basic_stringstream<char >         stream_t ;
    typedef  boost::shared_ptr<stream_t>           stream_sptr;

    impl(const char * name ):_stream(new stream_t ),_name(name),_level(log_kit::undef){
        chose_log(1);
    }

    void chose_log( uint ratio)
    {/*{{{*/
        int  pid = getpid();
        _chosed =  pid % ratio  == 0 ? true : false ;
    }/*}}}*/
    basic_ostream<char > * stream(log_kit::level_t lev)
    {/*{{{*/

        return _stream.get();
    }/*}}}*/
    void   out_tag( stringstream * ss )
    {
        (*ss) << "tag[" ;
        if (_all_tag.size() != 0)
        {
            (*ss) <<  _all_tag   ;

            if (_tag.size() != 0)
                (*ss) <<   "," << _tag  ;
        }
        else
        {
            if (_tag.size() != 0)
                (*ss) <<   _tag   ;
        }
        (*ss) <<    "] ";
        (*ss) << "evt[" ;
        if (_all_event.size() != 0)
        {
            (*ss) <<  _all_event   ;

            if (_event.size() != 0)
                (*ss) <<   "," << _event  ;
        }
        else
        {
            if (_event.size() != 0)
                (*ss) <<   _event   ;
        }
        (*ss) <<    "] ";
    } 
    void   log_stream(const char* event=NULL)
    {/*{{{*/
        if(event != NULL) {
            _event  = event ;
        }
        else{
            _event = "";
        }


        stringstream out_ss;
        out_tag(&out_ss); 
        string_t msg= _stream->str();
        out_ss << msg ;


        log_kit::level_t cur_level = _level == log_kit::undef ? _all_level: _level ;
        string fullname =  s_prj  + "/" + _name ;

        log2sys(cur_level,fullname.c_str(),out_ss.str(),_channel);
        if( _toall)
        {
            string allname  =  s_prj  + "/_all"     ;
            log2sys(cur_level,allname.c_str(),out_ss.str(),_channel);
        }
        if (_extra == log_kit::console || _all_extra == log_kit::console )
        {
            std::cout << fullname << " : "  << out_ss.str() << endl;
        }
        
        _stream.reset(new stream_t);
    }/*}}}*/
    inline void level(log_kit::level_t level,uint ratio)
    {
        _level      = level; 
        chose_log(ratio);
    }
    inline void tag( const char* tag )
    {
        _tag = tag ;
    }
    inline void out(log_kit::outer_t extra )
    {
        _extra      = extra ;
    }
    stream_sptr     _stream   ;
    string          _name     ;
    string          _tag      ;
    string          _event    ;
    bool            _chosed   ;
    log_kit::level_t _level;
    log_kit::outer_t _extra;
    static   log_kit::level_t _all_level;
    static   log_kit::outer_t _all_extra;
    static   string           _all_tag   ;
    static   string           _all_event ;
    static   int              _channel   ;
    static   bool             _toall     ; 
};/*}}}*/

log_kit::level_t logger::impl::_all_level = log_kit::error;
log_kit::outer_t logger::impl::_all_extra = log_kit::none;
string           logger::impl::_all_tag   ;
string           logger::impl::_all_event ;
int              logger::impl::_channel   = LOG_LOCAL6 ;
bool             logger::impl::_toall     = true ;

struct log_kit::impl  
{
    public :
        typedef  std::map<string  ,logger*>   logger_map_t;
        typedef  std::pair<string ,logger*>   logger_pair_t;
        impl(){}
        logger* log_ins(const char* name)
        {   
            logger_map_t::iterator found =  _log_dict.find(name);
            if (found != _log_dict.end())
            {
                return found->second;
            }
            else
            {
                logger* one= new logger(name);
                _log_dict[name] = one ;
                return one ;
            }
        }

        void clear()
        {
            BOOST_FOREACH(logger_pair_t kv , _log_dict)
            {
                delete kv.second ;
            }
            _log_dict.clear();
        }
        logger_map_t _log_dict;
};


logger::logger(const char* name): _pimpl(new impl(name))
{}
logger::~logger()
{
    delete _pimpl;
}
ostream* logger::stream(log_kit::level_t lev)
{
    return _pimpl->stream(lev);
}
void   logger::log_stream(const char* event)
{
    _pimpl->log_stream(event);
}
void logger::level(log_kit::level_t l,uint ratio)
{
    _pimpl->level(l,ratio);
} 
void logger::tag(const char* tag )
{
    _pimpl->tag(tag);
} 
void logger::out(log_kit::outer_t extra)
{
    _pimpl->out(extra);
} 

bool logger::need_log(int level) 
{ 
    if (_pimpl->_level == log_kit::undef)
    {
        return  level >= logger::impl::_all_level ? _pimpl->_chosed : false ;
    } 
    else
    {
        return  level >= _pimpl->_level  ? _pimpl->_chosed : false ;
    }
}
void logger::debug(const char* msg,const char* event )
{
    if(need_log(log_kit::debug)) 
    {
        *stream(log_kit::debug) <<"[debug] " <<  msg ;
        log_stream(event);
    }
}
void logger::info(const char* msg,const char* event)
{
    if(need_log(log_kit::info)) 
    {
        *stream(log_kit::info) <<"[info] " <<  msg ;
        log_stream(event);
    }
}

void logger::warn(const char* msg,const char* event)
{
    if(need_log(log_kit::warn)) 
    {
        *stream(log_kit::warn) <<"[warn] " <<  msg ;
        log_stream(event);
    }
}

void logger::error(const char* msg,const char* event)
{
    if(need_log(log_kit::error)) 
    {
        *stream(log_kit::error) <<"[error] " <<  msg ;
        log_stream(event);
    }
}

log_kit::impl* impl_ins()
{
    static log_kit::impl   s_impl_ins;
    return &s_impl_ins ;

}

void log_kit::init(const char * prj,const char* tag , log_kit::level_t l)
{
    s_prj = prj;
    logger::impl::_all_tag   = tag ;
    logger::impl::_all_level = l ;
}
void log_kit::event(const char* evt)
{
    logger::impl::_all_event = evt ;
}
logger* log_kit::log_ins(const char* name)
{
    return impl_ins()->log_ins(name);
}

void  log_kit::level(const char* name , log_kit::level_t l,uint ratio)
{
    log_ins(name)->level(l,ratio);
}

void  log_kit::tag(const char* name , const char* tag )
{
    log_ins(name)->tag(tag);
}
void  log_kit::out(const char* name , log_kit::outer_t extra_out)
{
    log_ins(name)->out(extra_out);
}

void log_kit::clear()
{
    impl_ins()->clear();
}
void log_kit::channel( log_kit::channel_t  ch)
{
    return ;
    switch(ch)
    {
        case log_kit::ch0 :
            logger::impl::_channel = LOG_LOCAL0 ;
            break;
        case log_kit::ch1 :
            logger::impl::_channel = LOG_LOCAL1 ;
            break;
        case log_kit::ch2 :
            logger::impl::_channel = LOG_LOCAL2 ;
            break;
        case log_kit::ch3 :
            logger::impl::_channel = LOG_LOCAL3 ;
            break;
        case log_kit::ch4 :
            logger::impl::_channel = LOG_LOCAL4 ;
            break;
        case log_kit::ch5 :
            logger::impl::_channel = LOG_LOCAL5 ;
            break;
        case log_kit::ch6 :
            logger::impl::_channel = LOG_LOCAL6 ;
            break;
        case log_kit::ch7 :
            logger::impl::_channel = LOG_LOCAL7 ;
            break;
    }
}
void log_kit::toall( bool enable)
{

    logger::impl::_toall =  enable ;
}


