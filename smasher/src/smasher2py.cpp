#include "../include/lib_def.h"
#include "../include/smasher.h"
#include <boost/python.hpp>
using namespace boost::python;
std::string pylon_dict_find(const char* cls)
{
    char buf[BUF_SIZE];
    memset(buf,0,BUF_SIZE);
    dict_find(cls,buf,BUF_SIZE); 
    return std::string(buf);
}
std::string pylon_sdict_find(const char* cls)
{
    char buf[BUF_SIZE];
    memset(buf,0,BUF_SIZE);
    shared_dict_find(cls,buf,BUF_SIZE); 
    return std::string(buf);
}

std::string pylon_dict_prompt(const char* cls)
{
    char buf[BUF_SIZE];
    memset(buf,0,BUF_SIZE);
    dict_prompt(cls,buf,BUF_SIZE); 
    return std::string(buf);
}

void pylon_sdict_using(const char*  proc_space )
{
    shared_dict_using(proc_space);
}
void pylon_sdict_create(const char*  proc_space, int msize=1,int dynload=1, int loadpgs=1)
{
    shared_dict_create(proc_space,msize,dynload,loadpgs);
}

void pylon_sdict_remove(const char * space)
{
    shared_dict_remove(space);
}
void pylon_dict_data(const char * data_file,const char* key_prefix ="" , const char* data_prefix="")
{
    dict_data( data_file, key_prefix ,  data_prefix);
}
void pylon_sdict_data(const char * data_file,const char* key_prefix ="" , const char* data_prefix="")
{
    shared_dict_data( data_file, key_prefix ,  data_prefix);
}

int pylon_dict_count()
{
    return dict_count();
}
int pylon_sdict_count()
{
    return shared_dict_count();
}

BOOST_PYTHON_FUNCTION_OVERLOADS(pylon_sdict_create_ovld, pylon_sdict_create, 1, 4)
BOOST_PYTHON_FUNCTION_OVERLOADS(pylon_sdict_data_ovld, pylon_sdict_data, 1, 3)
BOOST_PYTHON_MODULE(pylon2py)
{
    
    def("pylon_sdict_using", pylon_sdict_using);
    def("pylon_sdict_create", pylon_sdict_create,pylon_sdict_create_ovld());

    def ("pylon_sdict_remove",pylon_sdict_remove);

    def ("pylon_sdict_data",pylon_sdict_data,pylon_sdict_data_ovld());

    def ("pylon_sdict_find",pylon_sdict_find);


    def ("pylon_sdict_count",pylon_sdict_count);

    def ("pylon_dict_data",pylon_dict_data);

    def ("pylon_dict_find",pylon_dict_find);

    def ("pylon_dict_prompt",pylon_dict_prompt);

    def ("pylon_dict_count",pylon_dict_count);

}
