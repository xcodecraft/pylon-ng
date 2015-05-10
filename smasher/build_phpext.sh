#!/bin/bash
export PHPBIN=/usr/local/php-$PHP_VER/bin/

function build_ext()
{

    TARGET="pylon${EXT_VER}_${PHP_VER_TAG}.so"
    PHPIZE=${PHPBIN}/phpize
    if ! test -e $PHPIZE ; then
        echo $PHPIZE not exists!
        exit -1 ;
    fi
    cd $PRJ_ROOT/smasher/pylonphp
    echo $PHPIZE
    $PHPIZE --clean ;
    $PHPIZE ;
    ./configure CC=g++  --with-php-config=$PHPBIN/php-config
    make clean
    make
    make test
    echo $PRJ_ROOT/smasher/pylonphp/modules/$TARGET ;
    if ! test -e $PRJ_ROOT/smasher/pylonphp/modules/$TARGET ; then
        echo "编译失败 "
        exit -1
    fi
    cp $PRJ_ROOT/smasher/pylonphp/modules/$TARGET  $PRJ_ROOT/smasher/bin

}

case $1 in
    _config)
        exit ;
        ;;
    _start)
        build_ext
        exit ;
        ;;
esac
