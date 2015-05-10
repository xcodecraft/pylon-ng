#include <cstring>
#include "log_sysl.h"
#include "smasher.h"


int main()
{
    shared_dict_create("sdict_ut", 10) ;
    int cnt = 0 ;
    cnt = shared_dict_data("../data/_autoload_clspath.idx","","", false);
    char buffer[1024] ;
    for( int i = 0 ; i < 1000000 ; ++ i)
    {
        memset( buffer,0,1024 ) ;
        int val =  shared_dict_find("XPylon",buffer,1024 );
    }
    shared_dict_remove ("sdict_ut" ) ;

}
