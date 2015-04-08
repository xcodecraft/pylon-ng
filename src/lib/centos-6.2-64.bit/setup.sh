cp ./libpylon_smasher.so  /usr/local/lib/
cp ./pylon2py.so   /usr/local/lib/
cp ./libboost_python.so.1.46.0  /usr/local/lib/
if test -d /usr/local/php/extensions/
then 
    cp ./php/pylonphp.so   /usr/local/php/extensions/
fi
if test -d /usr/local/php-5.3/extensions/
then
    cp ./php-5.3/pylonphp.so   /usr/local/php-5.3/extensions/
fi
