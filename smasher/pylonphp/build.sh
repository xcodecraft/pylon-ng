POSTFIX="-5.3"
echo "What PHP version ? (5.3)"
read VER
if ! test -z $VER ; then 
    POSTFIX="-$VER"
fi
/usr/local/php$POSTFIX/bin/phpize
./configure CC=g++  --with-php-config=/usr/local/php$POSTFIX/bin/php-config
make clean
make

cp ./.libs/pylonphp2.so ./modules/
echo "pushd . ;  cd ../lib ; ./php${POSTFIX}_test.sh ; popd "
pushd . ;  cd ../lib ; ./php${POSTFIX}_test.sh ; popd 

echo "Do need copy pylonphp2.so to src/lib/centos-5.4-64.bit/php$POSTFIX ? (y/N)"
read NEED

if test $NEED == "y" ; then 
    mkdir -p ../../src/lib/centos-5.4-64.bit/php$POSTFIX/
    cp ./.libs/pylonphp2.so  ../../src/lib/centos-5.4-64.bit/php$POSTFIX/
fi
