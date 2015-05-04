import os
from pylon2py import *
from time import  *

def find_data(k):
    for i in range(20) :
        sleep(0.03)
        print k + " " + str(i) + ": " +  pylon_sdict_find("cls_sys") + " " + pylon_sdict_find("ee79a1185eabdca75ed4907ced1ae0f1")




if __name__ == '__main__':
    pylon_sdict_create("python_use_test")
    bigfile = "./gamedata_chk.txt"
    pylon_sdict_space("python_use_test",10)
    pylon_sdict_data("./data_1.txt","","")
    pylon_sdict_data("./data_2.txt","","")

    child_pid = os.fork()
    if child_pid == 0:
        for i in range(3) :
            child_pid = os.fork()
            if child_pid == 0:
                find_data("A" + str(i))
    else:
        sleep(0.5)
        print("load ....................")
        pylon_sdict_data(bigfile,"","")
        os.system(" echo '' >> " + bigfile);
        sleep(1)
        print("load ....................")
        pylon_sdict_data(bigfile,"","")
        os.system(" echo '' >> " + bigfile);
        sleep(1)
        pylon_sdict_remove("python_use_test")
        sleep(2)
    print(" remove pylon");
