<?
include("adodb/adodb.inc.php");
#include_once("adodb/adodb-pager.inc.php");
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "pr_iddp_v2.0";

$redishost	= "localhost";
$redisport	= "6379";

$conn = &ADONewConnection('mysql');
$conn->Connect("$dbhost","$dbuser","$dbpass","$dbname");
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
$conn->replaceQuote = "";
if($_GET['debug']==1)	$conn->debug  = 1;

$redis = new Redis();
$redis->connect($redishost,$redisport);

?>