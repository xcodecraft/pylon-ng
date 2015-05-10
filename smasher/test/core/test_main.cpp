#include "../../smasher/include/smasher.h"
#include "../../smasher/include/lib_def.h"
#include <iostream>

using namespace std;
void find(const char * key)
{
    char buf[BUF_SIZE];
    if(shared_dict_find(key,buf,BUF_SIZE))
        cout << key  << buf << endl;
}

void prompt(const char * key)
{
    char buf[BUF_SIZE];
    shared_dict_prompt(key,buf,BUF_SIZE);
    cout << key  << buf << endl;
}

int main()
{
    const char * data1= "../data/gamedata_chk.txt";
    const char * data2= "../data/data_2.txt";
//    const char * data1= "./data_1.txt";
//    const char * data2= "./data_2.txt";
    shared_dict_create("PYLON_TEST",10);


    shared_dict_data(data1,"","/X");
    shared_dict_data(data2,"","/Y");
    find("xxx");
    find("XAop");
    find("XTools");
//    prompt("XTools");
//    prompt("cls_file");
//    remove_dict_space();
    return 0;
}
