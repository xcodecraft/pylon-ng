#!/bin/bash
POSTFIX="-5.6.8"
export PYLON_HOME=$HOME/devspace/pylon-ng
export PHPBIN=/usr/local/php$POSTFIX/bin/

function build_ext()
{

    POSTFIX=$1
    PHP_VER=$1
    # export EXT_VER=`cat ../src/version.txt  | sed  "s#\([0-9]\)\.\([0-9]\).*#\1\2#"`

    PHPIZE=${PHPBIN}/phpize
    if ! test -e $PHPIZE ; then
        echo $PHPIZE not exists!
        exit -1 ;
    fi
    cd $PYLON_HOME/smasher/pylonphp
    $PHPIZE --clean ;
    $PHPIZE ;
    ./configure CC=g++  --with-php-config=$PHPBIN/php-config
    make clean
    make
    make test

    if ! test -e ./.libs/pylon.so ; then
        echo "编译失败 "
        exit -1
    fi
    cp ./.libs/pylon.so  $PYLON_HOME/smasher/lib
    #
    # echo "pushd . ;  cd ../lib ; ./php_test.sh   $PHP_VER ; popd "
    # pushd . ;  cd ../lib ; ./php_test.sh  $PHP_VER ; popd
    #
    # DIST=src/lib/$OS_VER/php$POSTFIX
    # SO=pylon-$EXT_VER.so
    # mkdir -p ../../$DIST
    # cp ./.libs/pylon.so  ../../$DIST/$SO
    # cd ..
}

case $1 in
    _config)
        exit ;
        ;;
    _start)
        build_ext "-5.5"
        exit ;
        ;;
esac
