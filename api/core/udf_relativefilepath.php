<?php
// Fusebox.org thanks Massimo Foti for contributing this code --->
/**
 * @param startFile 	 First file. (Required)
 * @param endFile 	 Second file. (Required)
 * @return Returns a string. 
 */
function relativeFilePath__($startFile,$endFile){
	//In case we have absolute local paths, turn backward to forward slashes
	$startpath = str_replace("\\","/",$startFile); 
	$endPath = str_replace("\\","/",$endFile); 
	//Declare variables
	$i = 0;
	$j = 0;
	$endStr = "";
	$commonStr = "";
	$retVal = "";
	$whatsLeft = "";
	$slashPos = "";
	$slashCount = 0;
	$dotDotSlash = "";
	//Be sure the paths aren't equal
	if ( $startpath != $endPath ) {
		//If the starting path is longer, the destination path is our starting point
		if ( strlen($startpath) > strlen($endPath) ) {
			$endStr = strlen($endPath);
		}
		//Else the starting point is the start path
		else {
			$endStr = strlen($startpath);
		}
		//Check if the two paths share a base path and store it into the commonStr variable
		for ( $i ; $i < $endStr; $i++ ) {
			//Compare one character at time
			if ( substr($startpath,$i,1) == substr($endPath,$i,1) ) {
				$commonStr .= substr($startpath,$i,1);
			}
			else {
				break;
			}
		}
		//We just need the base directory
		$commonStr = ereg_replace("[^/]*$","",$commonStr);
		//If there is a common base path, remove it
		if ( strlen($commonStr) > 0 ) {
			$whatsLeft = substr($startpath,strlen($commonStr),strlen($startpath));
		}
		else {
			$whatsLeft = $startpath;
		}
		$slashPos = strpos($startpath,"/");
		//Count how many directories we have to climb
		while ( $slashPos !== false ) {
			$slashCount++;
			$slashPos = strpos($whatsLeft,"/",$slashPos+1);
		}
		//Append "../" for each directory we have to climb
		for ( $j ; $j < $slashCount ; $j++ ) {
			$dotDotSlash .= "../";
		}
		//Assemble the final path
		$retVal = $dotDotSlash . substr($endPath,strlen($commonStr),strlen($endPath));
		
		if ( strpos($retVal,"/") === false )
			$retVal = "./" . $retVal;
	}
	//Paths are the same
	else {
		$retVal = "";
	}
	return $retVal;
}
?>