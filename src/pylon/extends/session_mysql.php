<?php
/** @ingroup extends
 * @{
 */
class MySqlSessDriver
{
   public function __construct($dbhost,$dbname,$dbuser,$dbpasswd,$life=1440) 
   {
       global $sess_dbhost ;
       global $sess_dbname ; 	// SESS_DBNAME_S;
       global $sess_dbuser ; 	// ESS_DBUSER_S;
       global $sess_dbpass ; 	// ESS_DBPASS_S;
       global $sess_life;

       $sess_dbhost = $dbhost;
       $sess_dbname = $dbname;
       $sess_dbuser = $dbuser;
       $sess_dbpass = $dbpasswd;
       $sess_life = $life;
       
   }
   public function init()
   {
        session_set_save_handler(  
        "sess_open",  
        "sess_close",  
        "sess_read",  
        "sess_write",  
        "sess_destroy",  
        "sess_gc");  
   }
}
function sess_init($dbhost,$dbname,$dbuser,$dbpasswd,$life=1440)
{
	global $sess_dbhost ;
	global $sess_dbname ; 	// SESS_DBNAME_S;
	global $sess_dbuser ; 	// ESS_DBUSER_S;
	global $sess_dbpass ; 	// ESS_DBPASS_S;
	global $sess_life;

	$sess_dbhost = $dbhost;
	$sess_dbname = $dbname;
	$sess_dbuser = $dbuser;
	$sess_dbpass = $dbpasswd;
	$sess_life = $life;
	
	session_set_save_handler(  
	"sess_open",  
	"sess_close",  
	"sess_read",  
	"sess_write",  
	"sess_destroy",  
	"sess_gc");  
}

function sess_open($save_path="", $session_name="") 
{  
	global $sess_dbhost ;
	global $sess_dbname ; 	// SESS_DBNAME_S;
	global $sess_dbuser ; 	// ESS_DBUSER_S;
	global $sess_dbpass ; 	// ESS_DBPASS_S;
	global $sess_dbh; 		// SESS_DBH_S;  
	global $sess_life;

	if (! $sess_dbh = mysql_connect($sess_dbhost, $sess_dbuser, $sess_dbpass)) 
	{  
		echo "<li>Can't connect to $sess_dbhost as $sess_dbuser";  
		echo "<li>MySQL Error: ", mysql_error();  
		die;  
	}  

	if (! mysql_select_db($sess_dbname, $sess_dbh)) 
	{  
		echo "<li>Unable to select database $sess_dbname";  
		die;  
	}  

	return true;  
}  

function sess_close() 
{  
	return true;  
}  

function sess_read($key) 
{  
	global $sess_dbh, $sess_life;  

    $qry = "SELECT value FROM sessions WHERE sesskey = '$key' AND expiry > " . time();  
	$qid = mysql_query($qry, $sess_dbh);  
	if (list($value) = mysql_fetch_row($qid)) 
	{  
		return $value;  
	}  

	return false;  
}  

function sess_write($key, $val) 
{  
	global $sess_dbh, $sess_life; 

	$expiry = time() + $sess_life;  
	//$value = addslashes($val);  

	$key   = mysql_escape_string($key);
	$value = mysql_escape_string($val);

	$qry = "INSERT INTO sessions VALUES ('$key', $expiry, '$value')";  
	$qid = mysql_query($qry, $sess_dbh);  

	if (! $qid) 
	{  
		//$qry = "UPDATE sessions SET expiry = $expiry, value = '$value' WHERE sesskey = '$key' AND expiry > " . time();  
		$qry = "UPDATE sessions SET expiry = $expiry, value = '$value' WHERE sesskey = '$key'";  
		$qid = mysql_query($qry, $sess_dbh);  
	}  

	return $qid;  
}  

function sess_destroy($key) 
{  
	global $sess_dbh;  

	$qry = "DELETE FROM sessions WHERE sesskey = '$key'";  
	$qid = mysql_query($qry, $sess_dbh);  

	return $qid;  
}  

function sess_gc($maxlifetime) 
{  
	global $sess_dbh;  

	$qry = "DELETE FROM sessions WHERE expiry < " . time();  
	$qid = mysql_query($qry, $sess_dbh);  

	return mysql_affected_rows($sess_dbh);  
}  

/** 
 *  @}
 */
?>
