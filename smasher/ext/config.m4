dnl $Id$
dnl config.m4 for extension pylonphp

dnl Comments in this file start with the string 'dnl'.
dnl Remove where necessary. This file will not work
dnl without editing.

dnl If your extension references something external, use with:

PHP_ARG_WITH(pylonphp, for pylonphp support,
Make sure that the comment is aligned:
[  --with-pylonphp             Include pylonphp support])

dnl Otherwise use enable:

dnl PHP_ARG_ENABLE(pylonphp, whether to enable pylonphp support,
dnl Make sure that the comment is aligned:
dnl [  --enable-pylonphp           Enable pylonphp support])

 if test "$PHP_PYLONPHP" != "no"; then
  dnl Write more examples of tests here...

  dnl # --with-pylonphp -> check with-path
  dnl SEARCH_PATH="/usr/local /usr"     # you might want to change this
  dnl SEARCH_FOR="/include/pylonphp.h"  # you most likely want to change this
  dnl if test -r $PHP_PYLONPHP/$SEARCH_FOR; then # path given as parameter
  dnl   PYLONPHP_DIR=$PHP_PYLONPHP
  dnl else # search default path list
  dnl   AC_MSG_CHECKING([for pylonphp files in default path])
  dnl   for i in $SEARCH_PATH ; do
  dnl     if test -r $i/$SEARCH_FOR; then
  dnl       PYLONPHP_DIR=$i
  dnl       AC_MSG_RESULT(found in $i)
  dnl     fi
  dnl   done
  dnl fi
  dnl
  dnl if test -z "$PYLONPHP_DIR"; then
  dnl   AC_MSG_RESULT([not found])
  dnl   AC_MSG_ERROR([Please reinstall the pylonphp distribution])
  dnl fi

  dnl # --with-pylonphp -> add include path
  dnl PHP_ADD_INCLUDE($PYLONPHP_DIR/include)

  dnl # --with-pylonphp -> check for lib and symbol presence
  dnl LIBNAME=pylonphp # you may want to change this
  dnl LIBSYMBOL=pylonphp # you most likely want to change this

  dnl PHP_CHECK_LIBRARY($LIBNAME,$LIBSYMBOL,
  dnl [
  dnl   PHP_ADD_LIBRARY_WITH_PATH($LIBNAME, $PYLONPHP_DIR/lib, PYLONPHP_SHARED_LIBADD)
  dnl   AC_DEFINE(HAVE_PYLONPHPLIB,1,[ ])
  dnl ],[
  dnl   AC_MSG_ERROR([wrong pylonphp lib version or lib not found])
  dnl ],[
  dnl   -L$PYLONPHP_DIR/lib -lm -ldl
  dnl ])
  dnl

  PHP_ADD_INCLUDE(${PRJ_ROOT}/smasher/include)
  PHP_ADD_LIBRARY_WITH_PATH(pylon_smasher-${EXT_VER},$PRJ_ROOT/src/modules/${OS_VER}, PYLONPHP_SHARED_LIBADD)
  PHP_NEW_EXTENSION(pylonphp, pylonphp.c, $ext_shared)
  PHP_SUBST(PYLONPHP_SHARED_LIBADD)

 fi
