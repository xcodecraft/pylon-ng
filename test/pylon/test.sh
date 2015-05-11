# PRJ_ROOT=${HOME}/devspace/pylon-ng
INI=${PRJ_ROOT}/test/pylon/config/used/php_test.ini
PHPUNIT=/usr/local/php/bin/phpunit
XML=${PRJ_ROOT}/test/pylon/phpunit.xml
/usr/local/php/bin/php -c $INI $PHPUNIT  -c $XML
