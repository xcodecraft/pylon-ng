OS="UNKNOW"
OS_FILE=/etc/redhat-release 
if test -e $OS_FILE  ; then
    CONTENT=`cat $OS_FILE `
    if test  "$CONTENT" = "CentOS release 5.4 (Final)" ; then
        OS="centos-5.4"
    fi
    if test  "$CONTENT" = "CentOS release 6.2 (Final)" ; then
        OS="centos-6.2"
    fi
    if test  "$CONTENT" = "CentOS release 6.6 (Final)" ; then
        OS="centos-6.6"
    fi
    if test  "$CONTENT" = "CentOS release 6.8 (Final)" ; then
        OS="centos-6.8"
    fi

fi
OS_FILE=/etc/os-release 
if test -e $OS_FILE  ; then
    CONTENT=`cat $OS_FILE `
    if test  "$CONTENT" = "CentOS release 7.0 (Final)" ; then
        OS="centos-5.4"
    fi
fi
if test "$OS" = "UNKNOW"  ;  then
    echo "unknow this os ,
    setup exit!"
    exit;
fi


rg start -ecentos,$OS,php70 -s ext
# rg start -ecentos,$OS,php56 -s ext
