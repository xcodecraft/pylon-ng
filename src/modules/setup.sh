OS="UNKNOW"
if test -e /etc/redhat-release  ; then

    CONTENT=`cat /etc/redhat-release`
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
    if test  "$CONTENT" = "CentOS Linux release 7.4.1708 (Core) " ; then
        OS="centos-7.4"
    fi

fi
if test "$OS" = "UNKNOW"  ;  then
    echo "unknow this os ,
    setup exit!"
    exit;
fi
adirname() { odir=`pwd`; cd `dirname $1`; pwd; cd "${odir}"; }
MYDIR=`dirname "$0"`
cd $MYDIR
cd $OS
cp -u ./libpylon_smasher*.so /usr/local/lib/
function deploy_phpext ()
{
    VER=$1
    DST=/usr/local/php-$VER/extensions
    if test -d $DST ; then
        if test -d ./$VER/ ; then
            cp -u ./$VER/pylon*.so $DST
        fi
    fi

}

deploy_phpext 7.1
/sbin/ldconfig
