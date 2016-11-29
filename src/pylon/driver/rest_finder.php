<?php
namespace pylon\driver ;
use XIRouter ;
/**
 * @ingroup extends
* @brief
 */
class  FastRouter implements XIRouter
{
    public function __construct($data_file)
    {
        pylon_rest_data($data_file);
    }
    public function _find($uri)
    {
        $found = pylon_rest_find($uri);
        return  $found  ;
    }
}
