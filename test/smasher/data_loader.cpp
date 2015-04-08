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
    const char * data1= "./gamedata_chk.txt";
    shared_dict_create("PYLON_TEST",40);
    shared_dict_data(data1,"","",true);
    find("ee8ad032e984a4d1af27fb7b767d9b43");
    return 0;
}
