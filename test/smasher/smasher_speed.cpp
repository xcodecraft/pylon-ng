#include <cstring>
#include "log_sysl.h"
#include "smasher.h"


int main()
{
    char buffer[1024] ;
    for( int i = 0 ; i < 1000000 ; ++ i)
    {
        memset(buffer,0,1024 ) ;
        dict_data("./_autoload_clspath.idx","","", false);
        int val =  dict_find("XPylon",buffer,1024 );
    }

}
