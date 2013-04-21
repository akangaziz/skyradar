<?
$url	= "https://api.flightstats.com/flex/airports/rest/v1/json/countryCode/ID?appId=$appId&appKey=$appKey";
$return	= tembak($url);
#echo $return;
#debug($return);

$data 		=  json_decode($return);
$newdata	= array();
foreach($data->airports as $k => $v){
	if($v->fs=='CGK' || $v->fs=='MES' || $v->fs=='BDO'){
		$v->letter	= substr($v->name,0,1);
		$newdata[]	= $v;	
	}
	#debug($v);
}
#debug($newdata);
if($_GET['callback']){
	echo $_GET['callback'] . '(' . json_encode($newdata). ');';
}else{
	echo json_encode($newdata);
}


?>