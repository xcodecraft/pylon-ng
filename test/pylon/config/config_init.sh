source $SGT_HOME/project/envsetup_module.sh

configInitUseage $*
OWNER=$1
OP=$2
ROOT=`pwd`

####### BUILD CONF###############
ROOT=$SGT_HOME
BUILD_CONF=$ROOT/project/build_conf.php
function buildConf
{
#{{{
    TPL_FILE=$1
    OUT_FILE=$2
    php $BUILD_CONF  $TPL_FILE $OUT_FILE $OWNER {owner}
    php $BUILD_CONF  $OUT_FILE $OUT_FILE $ROOT {prj_path}
#}}}
} 

case $OWNER in 
    *)
    buildConf options/php_tpl.ini options/php_$OWNER.ini
    buildConf options/conf_tpl.php options/conf_$OWNER.php
    ;;

esac
#if ! test $OWNER = 'online' && ! test $OWNER = 'dev' && ! test $OWNER = 'test'
#then
#fi

autoProcLinkConf  $OP  'options/conf_'$OWNER'.php'  'config.php'
autoProcLinkConf  $OP  'options/php_'$OWNER'.ini'  'php.ini'
