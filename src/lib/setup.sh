OS="UNKNOW"
if test -e /etc/redhat-release  ; then

    CONTENT=`cat /etc/redhat-release`
    if test  "$CONTENT" = "CentOS release 5.4 (Final)" ; then
        OS="centos-5.4-64.bit"
    fi
    if test "$CONTENT" = "CentOS release 6.2 (Final)" ; then
        OS="centos-6.2-64.bit"
    fi

fi
if test "$OS" = "UNKNOW"  ;  then
    echo "unknow this os , setup exit!"
    exit;
fi
adirname() { odir=`pwd`; cd `dirname $1`; pwd; cd "${odir}"; }
MYDIR=`dirname "$0"`
cd $MYDIR
cd $OS
cp -u ./libpylon_smasher*.so  /usr/local/lib/
function deploy_phpext ()
{
    VER=$1
    if test -d /usr/local/php-$VER/extensions/
    then
        cp -u ./php-$VER/pylonphp*.so   /usr/local/php-$VER/extensions/
    fi

}
deploy_phpext 5.6
deploy_phpext 5.3
deploy_phpext 5.2
/sbin/ldconfig
