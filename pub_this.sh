TAG=`cat src/version.txt`
echo $TAG ;
/data/x/tools/xcc_pub/rocket_pub.sh  --prj pylon-ng --tag $TAG --host $*;
