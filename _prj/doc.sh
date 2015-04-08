OP=$1
if test "$OP" == "start"  ;then 
    cd $PRJ_ROOT/_prj/
    /usr/local/bin/doxygen  ./doc.doxygen
fi
