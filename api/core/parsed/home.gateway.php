<?php

$myFusebox['thisCircuit'] = "home";
$myFusebox['thisFuseaction'] = "gateway";
$myFusebox['thisCircuit'] = "lib";
$myFusebox['thisFuseaction'] = "function";
if ( file_exists($application["fusebox"]["WebRootToAppRootPath"]."../apps/libr/actFunction.php") ) {
	include($application["fusebox"]["WebRootToAppRootPath"]."../apps/libr/actFunction.php");
}
 else {
	__cfthrow(array("type"=>"fusebox.missingFuse", "message"=>"missing Fuse", "detail"=>"You tried to include a fuse actFunction in circuit lib which does not exist."));
}
if ( file_exists($application["fusebox"]["WebRootToAppRootPath"]."../apps/libr/Array2XML.php") ) {
	include($application["fusebox"]["WebRootToAppRootPath"]."../apps/libr/Array2XML.php");
}
 else {
	__cfthrow(array("type"=>"fusebox.missingFuse", "message"=>"missing Fuse", "detail"=>"You tried to include a fuse Array2XML in circuit lib which does not exist."));
}
if ( file_exists($application["fusebox"]["WebRootToAppRootPath"]."../apps/libr/actNetwork.php") ) {
	include($application["fusebox"]["WebRootToAppRootPath"]."../apps/libr/actNetwork.php");
}
 else {
	__cfthrow(array("type"=>"fusebox.missingFuse", "message"=>"missing Fuse", "detail"=>"You tried to include a fuse actNetwork in circuit lib which does not exist."));
}
$myFusebox['thisCircuit'] = "home";
$myFusebox['thisFuseaction'] = "gateway";
$myFusebox['thisCircuit'] = "lib";
$myFusebox['thisFuseaction'] = "adodb";
$myFusebox['thisCircuit'] = "home";
$myFusebox['thisFuseaction'] = "gateway";
if ( file_exists($application["fusebox"]["WebRootToAppRootPath"]."../apps/home/actGateway.php") ) {
	include($application["fusebox"]["WebRootToAppRootPath"]."../apps/home/actGateway.php");
}
 else {
	__cfthrow(array("type"=>"fusebox.missingFuse", "message"=>"missing Fuse", "detail"=>"You tried to include a fuse actGateway in circuit home which does not exist."));
}
$myFusebox['thisCircuit'] = "home";
$myFusebox['thisFuseaction'] = "gateway";
$myFusebox['thisCircuit'] = "home";
$myFusebox['thisFuseaction'] = "gateway";

?>