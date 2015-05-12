root=$HOME/devspace/pylon-ng
cd $root
rm -rf prj/cscope.*
find $root/ -name "*.py"  > _prj/cscope.files
find $root/ -name "*.php" >> _prj/cscope.files
find $root/ -name "*.sh"  >> _prj/cscope.files
find $root/ -name "*.h"     >> _prj/cscope.files
find $root/ -name "*.cpp"   >> _prj/cscope.files
cd _prj
cscope -b

