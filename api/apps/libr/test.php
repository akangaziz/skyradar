<?
include_once('adodb/adodb.inc.php');
include_once('adodb/adodb-pager.inc.php');
session_start();

$db = NewADOConnection('mysql');

$db->Connect('localhost','root','watashiwa','singasik');

$sql = "select * from content ";

$pager = new ADODB_Pager($db,$sql);
$pager->Render($rows_per_page=5);
?>