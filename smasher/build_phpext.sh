POSTFIX="-5.3"
export PYLON_HOME=$HOME/devspace/pylon

function os_ver()
{
    CONTENT=`cat /etc/redhat-release`
    if test  "$CONTENT" = "CentOS release 5.4 (Final)" ; then
        OS="centos-5.4-64.bit"
    fi
    if test  "$CONTENT" = "CentOS release 6.2 (Final)" ; then
        OS="centos-6.2-64.bit"
    fi
    echo $OS
}

function build_ext()
{

    POSTFIX=$1
    PHP_VER=$1
    echo "是否生成 PHP$POSTFIX 扩展 ? (y/N)"
    read NEED
    if ! test "$NEED" = "y" ; then
        return
    fi
    export EXT_VER=`cat ../src/version.txt  | sed  "s#\([0-9]\)\.\([0-9]\).*#\1\2#"`
    export OSVER=$(os_ver)
    PHPIZE=/usr/local/php$POSTFIX/bin/phpize
    if ! test -e $PHPIZE ; then
        echo $PHPIZE not exists!
        exit;
    fi
    cd pylonphp
    $PHPIZE --clean ;
    $PHPIZE ;
    ./configure CC=g++  --with-php-config=/usr/local/php$POSTFIX/bin/php-config
    make clean
    make
    #pylonphp.so

    if ! test -e ./.libs/pylonphp.so ; then
        echo "编译失败 "
        return
    fi
    cp ./.libs/pylonphp.so ./modules/
    # echo "pushd . ;  cd ../lib ; ./php${POSTFIX}_test.sh ; popd "
    # pushd . ;  cd ../lib ; ./php${POSTFIX}_test.sh ; popd

    echo "pushd . ;  cd ../lib ; ./php_test.sh   $PHP_VER ; popd "
    pushd . ;  cd ../lib ; ./php_test.sh  $PHP_VER ; popd

    DIST=src/lib/$OSVER/php$POSTFIX
    SO=pylonphp-$EXT_VER.so
    mkdir -p ../../$DIST
    cp ./.libs/pylonphp.so  ../../$DIST/$SO
    cd ..
}
build_ext ""
build_ext "-5.3"
build_ext "-5.6"
echo "是否安装到 PHP 扩展目录 ? (y/N ) "
read NEED
if test "$NEED" = "y" ; then

    sudo $PYLON_HOME/src/lib/setup.sh
fi

