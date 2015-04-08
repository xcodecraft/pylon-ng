<?php
class Debug                                                                                                               
{                                                                                                                         
    static public function watch($file,$line,$var,$varName="UnKonw") 
    {
        echo "<br>*****************************DEBUG::WATCH******************************<br>\n";
        echo "<pre>";
        echo "$varName valure: <br>\n";
        var_dump($var);                                                                                                   
        echo "</pre>";
        echo "from: [$file $line] <br>\n";
        echo "***************************************************************************<br>\n";
    }                                                                                                                     
}                
?>
