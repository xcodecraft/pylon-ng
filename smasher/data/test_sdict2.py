import os
from pylon2py import *
from time import  *

def find_data(k):
    for i in range(25) :
        sleep(0.3)
        print k + " " + str(i) + ": " +  pylon_sdict_find("10") 

def build_data(data_file ,prefix="data"):
    f = open(data_file, 'w')
    for i in range(500000):
        f.write(str(i) + "," + prefix + "_" + str(i) + "\n")

if __name__ == '__main__':
    data_file = '/tmp/pylon_data.txt'

    pylon_sdict_remove("python_use_test")
    pylon_sdict_create("python_use_test",100)

    child_pid = os.fork()
    if child_pid == 0:
        for i in range(3) :
            child_pid = os.fork()
            if child_pid == 0:
                find_data("A" + str(i))
    else:
        build_data(data_file,"x")
        pylon_sdict_data(data_file,"","")
        sleep(4)
        build_data(data_file,"y")
        pylon_sdict_data(data_file,"","")
        sleep(2)
        build_data(data_file,"z")
        pylon_sdict_data(data_file,"","")
        sleep(2)

