cd ${HOME}/devspace/ayi_sdks/test
INCLUDE=$HOME/devspace/ayi_sdks/src
PHPUNIT="/usr/local/php/bin/phpunit  --include-path=$INCLUDE  "
$PHPUNIT -c phpunit.xml
