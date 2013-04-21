<?php
if(($argv)){
	foreach($argv as $k => $v){
		if($k>0){
			$x	= explode('=',$v);
			$_GET[$x[0]]	= $x[1];
		}
	}
}
ob_start();
$uri = explode("index.php/", $_SERVER["REQUEST_URI"]);
if (count($uri) > 1) {
  $qs = explode("/",$uri[1]);
  $i = 0;
  for($i=0; $i<count($qs); $i++){
    if($i == 0 && isset($qs[0]))
      $_GET["fa"] = $qs[0];
    else{
      if(isset($qs[$i])){
        $idx = $qs[$i];
        $_GET[$idx] = "";
        $i++;
        $val = isset($qs[$i]) ? $qs[$i] : "";
        $_GET[$idx] = $val;
      }
    }
  }
}
$FUSEBOX_APPLICATION_NAME = "skyradar.api";
$FUSEBOX_APPLICATION_PATH = "../core/";
// bring in the $application scope
@include($FUSEBOX_APPLICATION_PATH.'parsed/app_'.$FUSEBOX_APPLICATION_NAME.'.php');
require_once($FUSEBOX_APPLICATION_PATH."fusebox4.runtime.php4.php");
ob_end_flush();
?>