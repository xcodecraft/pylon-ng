import os
from pylon2py import *
from time import  *

def find_data(k):
    for i in range(10) :
        sleep(0.05)
        print k + " " + str(i) + ": " +  pylon_sdict_find("cls_sys") + " " + pylon_sdict_find("ee79a1185eabdca75ed4907ced1ae0f1")


if __name__ == '__main__':
    os.system(" echo '' > ./test_data.txt")
    sleep(1)
    pylon_dict_data("./test_data.txt","","")
    print pylon_dict_find("cls_file")
    print pylon_dict_find("File")
    os.system(" cat ./data_1.txt  >> ./test_data.txt");
    sleep(1)
    pylon_dict_data("./test_data.txt","","")
    print pylon_dict_find("cls_file")
    print pylon_dict_find("File")


    os.system(" cat ./data_2.txt  >> ./test_data.txt");
    sleep(1)
    pylon_dict_data("./test_data.txt","","")
    print pylon_dict_find("cls_file")
    print pylon_dict_find("File")

