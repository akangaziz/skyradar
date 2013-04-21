<?php

// Fusebox.org thanks Barney Boisvert for contributing this code --->

/**
 * @param path		An absolute path to clean. (Required)
 * @return Returns a string. 
 *
 * I opted to not use the Java method for backwards compatibility,
 * and because we might need to do relative paths at some point.
 */
function canonicalPath__($path, $delim) {
	$delimAtFront = ( substr($path,0,1) == $delim );
	$delimAtEnd = ( substr($path,-1) == $delim );
	$dirArray = preg_split("[/\\\]",$path,-1,PREG_SPLIT_NO_EMPTY);
	$i = "";
	for ( $i = 0 ; $i < count($dirArray) ; $i++ ) { 
		// don't convert this loop to CFLOOP without thinking
		if ( $dirArray[$i] == "." ) {
			$dirArray = array_merge(array_slice($dirArray,0,$i),array_slice($dirArray,$i));
			$i--;
		} else if ( $dirArray[$i] == ".." ) {
			$dirArray = array_merge(array_slice($dirArray,0,$i),array_slice($dirArray,$i));
			if ( $i > 0 and substr($dirArray[$i-1],-1) != ":" ) {
				$dirArray = array_merge(array_slice($dirArray,0,$i-1),array_slice($dirArray,$i-1));
				$i--;
			}
			$i--;
		}
	}
	return str_replace( "$delim$delim", "$delim", ( $delimAtFront ? $delim : "" ) . implode($delim,$dirArray) . ( $delimAtEnd ? $delim : "" ) );
}

?>
