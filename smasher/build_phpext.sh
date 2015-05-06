#!/bin/bash
POSTFIX="-5.6.8"
export PYLON_HOME=$HOME/devspace/pylon-ng
export PHPBIN=/usr/local/php$POSTFIX/bin/

function build_ext()
{

    PHP_VER=$1
    # export EXT_VER=`cat ../src/version.txt  | sed  "s#\([0-9]\)\.\([0-9]\).*#\1\2#"`

    PHPIZE=${PHPBIN}/phpize
    if ! test -e $PHPIZE ; then
        echo $PHPIZE not exists!
        exit -1 ;
    fi
    cd $PYLON_HOME/smasher/pylonphp
    echo $PHPIZE
    exit;
    $PHPIZE --clean ;
    $PHPIZE ;
    ./configure CC=g++  --with-php-config=$PHPBIN/php-config
    make clean
    make
    make test


    if ! test -e $PYLON_HOME/smasher/pylonphp/modules/pylonphp.so ; then
        echo "编译失败 "
        exit -1
    fi

    cp $PYLON_HOME/smasher/pylonphp/modules/pylonphp.so $PYLON_HOME/smasher/lib

     echo "pushd . ;  cd ../lib ; ./php_test.sh   $PHP_VER ; popd "
     pushd . ;  cd ../lib ; ./php_test.sh  $PHP_VER ; popd

}

case $1 in
    _config)
        exit ;
        ;;
    _start)
        build_ext $POSTFIX
        exit ;
        ;;
esac
