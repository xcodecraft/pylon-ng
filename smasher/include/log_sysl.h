#ifndef __LOG_SYSLOG_H__
#define __LOG_SYSLOG_H__
#include <ostream>
typedef unsigned int uint ;
class logger;
class log_kit
{
    public:
        enum level_t    { debug = 0, info, warn,error,undef=99};
        enum channel_t  { ch0   = 0, ch1,ch2,ch3,ch4,ch5,ch6,ch7 }  ;
        enum outer_t    { none  = 0 ,console=1 };

        static logger* log_ins(const char* name);

        static void init(const char * prjname,const char* tag ,level_t l);

        static void level(const char * name , level_t l, uint ratio=1);

        static void tag(const char * name , const char* tag );

        static void event(const char * event);

        static void out(const char * name , outer_t extra_out); 

        static void clear();

        static void channel( log_kit::channel_t  );

        static void toall( bool );
        struct impl;
};

class logger
{
    public:
        bool need_log(int level);
        void level(log_kit::level_t l,uint ratio=1);
        void tag(   const char* tag );
        void out(  log_kit::outer_t extra_out); 
        std::ostream*  stream(log_kit::level_t lev);
        void log_stream(const char* event=NULL);

        void debug(const char * msg,const char* event=NULL );
        
        void info(const char * msg ,const char* event=NULL);

        void warn(const char * msg ,const char* event=NULL);

        void error(const char * msg ,const char* event=NULL);

        logger(const char* name);

        ~logger();
        struct impl;
    private :
        impl*  _pimpl;
};

class logger_proxy
{
    public:
        void debug(const char * msg ,const char* event)
        {
            _l->debug(msg,event);
        }
        void info(const char * msg,const char* event )
        {
            _l->info(msg,event);
        }
        void warn(const char * msg,const char* event )
        {
            _l->warn(msg,event);
        }
        void error(const char * msg,const char* event )
        {
            _l->error(msg,event);
        }
        logger_proxy(const char* name):_l(log_kit::log_ins(name)){}
        logger*  _l;
};

#define LOG_WHERE  __PRETTY_FUNCTION__ << ":" << __LINE__  
#define LOG_DEBUG(x) if (x->need_log(log_kit::debug)) for(int i = 1; i > 0 ; i = 0 , x->log_stream() ) \
                                                                  *( x->stream(log_kit::debug) ) <<  LOG_WHERE  
#define ELOG_DEBUG(x,y) if (x->need_log(log_kit::debug)) for(int i = 1; i > 0 ; i = 0 , x->log_stream(y) ) \
                                                                  *( x->stream(log_kit::debug) ) <<  LOG_WHERE  
#define LOG_DEBUG_S(x) if (x->need_log(log_kit::debug)) for(int i = 1; i > 0 ; i = 0 , x->log_stream() ) \
                                                                  *( x->stream(log_kit::debug) ) 

#define LOG_INFO(x)   if (x->need_log(log_kit::info)) for(int i = 1; i > 0 ; i = 0 , x->log_stream() ) \
                                                                *( x->stream(log_kit::info) ) <<  LOG_WHERE  
#define LOG_INFO_S(x) if (x->need_log(log_kit::info)) for(int i = 1; i > 0 ; i = 0 , x->log_stream() ) \
                                                                 *( x->stream(log_kit::info) ) 
#define LOG_WARN(x) if (x->need_log(log_kit::warn)) for(int i = 1; i > 0 ; i = 0 , x->log_stream() ) \
                                                                  *( x->stream(log_kit::warn) ) <<  LOG_WHERE  

#define LOG_WARN_S(x) if (x->need_log(log_kit::warn)) for(int i = 1; i > 0 ; i = 0 , x->log_stream() ) \
                                                                    *( x->stream(log_kit::warn) ) 

#define LOG_ERROR(x) if (x->need_log(log_kit::error)) for(int i = 1; i > 0 ; i = 0 , x->log_stream() ) \
                                                                  *( x->stream(log_kit::error) ) <<  LOG_WHERE  

#define LOG_ERROR_S(x) if (x->need_log(log_kit::error)) for(int i = 1; i > 0 ; i = 0 , x->log_stream() ) \
                                                                    *( x->stream(log_kit::error) ) 


#define INS_LOG(x,logname)    logger* x=log_kit::log_ins(logname);

#endif

