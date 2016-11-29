extern "C"
{
    int  dict_data(const char * data_file,const char* key_prefix , const char* data_prefix, bool force   );
    int  dict_find(const char* cls, char * buf , int buf_len);
    bool dict_has(const char* cls);
    int  dict_prompt(const char* cls, char * buf , int buf_len);
    int  dict_count();
    void rest_data(const char* data_file);
    bool rest_find(const char* uri, char* buf , int buf_len);
}
