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
    if test  "$CONTENT" = "CentOS Linux release 7.2.1511 (Core) " ; then 
        OS="centos-7.0"
    fi
fi

if test "$OS" = "UNKNOW"  ;  then
    echo "unknow this os ,
    setup exit!"
    exit;
fi


rg start -ecentos,$OS,php70 -s ext

