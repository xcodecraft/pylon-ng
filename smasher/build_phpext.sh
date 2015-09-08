#!/bin/bash
export PHPBIN=/usr/local/php-$PHP_VER/bin/
export EXT_NAME_IMPL=pylonphp

function build_ext()
{

    TARGET="$EXT_NAME_IMPL.so"
    PHPIZE=${PHPBIN}/phpize
    if ! test -e $PHPIZE ; then
        echo $PHPIZE not exists!
        exit -1 ;
    fi
    cd $PRJ_ROOT/smasher/ext
    echo $PHPIZE
    $PHPIZE --clean ;
    $PHPIZE ;
    ./configure CC=g++  --with-php-config=$PHPBIN/php-config
    make clean
    make
    make test
    echo $PRJ_ROOT/smasher/ext/modules/$TARGET ;
    if ! test -e $PRJ_ROOT/smasher/ext/modules/$TARGET ; then
        echo "编译失败 "
        exit -1
    fi
    cp $PRJ_ROOT/smasher/ext/modules/$TARGET  $PRJ_ROOT/smasher/bin/$FULL_EXT

}


build_ext
