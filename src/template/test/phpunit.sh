
# PRJ_ROOT=${HOME}/devspace/agent
INI=${PRJ_ROOT}/conf/used/test_php.ini
PHPUNIT=/usr/local/php/bin/phpunit
XML=${PRJ_ROOT}/test/phpunit.xml
/usr/local/php/bin/php -c $INI $PHPUNIT --configuration $XML
