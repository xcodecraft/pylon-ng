CUR_OS="UNKNOW"
if test -e /etc/redhat-release  ; then 

    CONTENT=`cat /etc/redhat-release`
    if test  "$CONTENT" = "CentOS release 5.4 (Final)" ; then
        CUR_OS="centos-5.4-64.bit"
    fi
    if test "$CONTENT" = "CentOS release 6.2 (Final)" ; then 
        CUR_OS="centos-6.2-64.bit"
    fi

fi
if test "$CUR_OS" = "UNKNOW"  ;  then  
    echo "unknow this os , setup exit!"
    exit;
fi
export CUR_OS

cd $HOME/devspace/pylon/smasher/
$HOME/devspace/sun/bin/bjam $*
#../../sun/bin/bjam  $*
