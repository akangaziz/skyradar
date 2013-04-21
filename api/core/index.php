<?php

ini_set('display_errors',1);

$FUSEBOX_APPLICATION_NAME = "cacheddata";
$FUSEBOX_APPLICATION_PATH = "";

// bring in the $application scope
@include($FUSEBOX_APPLICATION_PATH.'parsed/app_'.$FUSEBOX_APPLICATION_NAME.'.php');

require_once("relative/path/to/fusebox4.runtime.php4.php");
?>
