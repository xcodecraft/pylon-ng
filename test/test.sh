RG=/home/q/tools/pylon_rigger/rigger
$RG data
#$RG php -f test/pylon/unittest.sh

$RG phpunit -s test -f ./test/pylon 
#$RG phpunit -v PHP_INI=./test/pylon/config/used/php_test.ini  -f ./test/pylon/rest
#$RG phpunit -v PHP_INI=./test/pylon/config/used/php_test.ini  -f ./test/pylon
