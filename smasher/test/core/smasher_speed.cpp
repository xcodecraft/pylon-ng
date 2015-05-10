#include <cstring>
#include "log_sysl.h"
#include "smasher.h"
#include <iostream>


int main()
{
    char buffer[1024] ;
    for( int i = 0 ; i < 1000000 ; ++ i)
    {
        memset(buffer,0,1024 ) ;
        dict_data("../data/_autoload_clspath.idx","","", false);
        dict_find("XPylon",buffer,1024 );
    }

}
