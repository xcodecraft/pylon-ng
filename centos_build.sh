#!/bin/bash
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
    if test  "$CONTENT" = "CentOS Linux release 7.3.1611 (Core) " ; then
        OS="centos-7.3"
    fi
    if test  "$CONTENT" = "CentOS Linux release 7.4.1708 (Core) " ; then
        OS="centos-7.4"
    fi
fi

if test "$OS" = "UNKNOW"  ;  then
    echo "unknow this os ,
    setup exit!"
    exit;
fi


/data/x/tools/rigger-ng/rg conf,start -ecentos,$OS,php71 -s ext

