#include "../../smasher/include/smasher.h"
#include <iostream>

using namespace std;
void find(const char * key)
{
    char buf[2048];
    if(shared_dict_find(key,buf,2048))
        cout << key  << buf << endl;
}


int main()
{
    shared_dict_using("PYLON_TEST");
    find("ee8ad032e984a4d1af27fb7b767d9b43");
    return 0;
}
