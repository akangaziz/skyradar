<?
if(!isset($status)){
	$data	= array('status' => '0', 'message' => "Oops, what are you looking for?");
}else{
	$data	= array('status' => $status, 'message' => $msg, 'data' => $data);
} 
savetoInternalLog($data);
$time_end = microtime_float();
$data['time']	= $time_end-$time_start;
if($_GET['type']=='xml'){
	$xml['@attributes'] = array(
	    'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
	    'xsi:noNamespaceSchemaLocation' => 'http://www.example.com/schmema.xsd',
	    'lastUpdated' => date('c')  // dynamic values
	);
	$xml = Array2XML::createXML('root', $data);
	echo $xml->saveXML();
}else{
	echo jsoncallback($data);
}
?>