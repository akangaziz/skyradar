<?php

// fusebox41.runtime.php4.php

/*
Fusebox Software License
Version 1.0

Copyright (c) 2003 The Fusebox Corporation. All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

2. Redistributions in binary form or otherwise encrypted form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

3. The end-user documentation included with the redistribution, if any, must include the following acknowledgment:

"This product includes software developed by the Fusebox Corporation (http://www.fusebox.org/)."

Alternately, this acknowledgment may appear in the software itself, if and wherever such third-party acknowledgments normally appear.

4. The names "Fusebox" and "Fusebox Corporation" must not be used to endorse or promote products derived from this software without prior written (non-electronic) permission. For written permission, please contact fusebox@fusebox.org.

5. Products derived from this software may not be called "Fusebox", nor may "Fusebox" appear in their name, without prior written (non-electronic) permission of the Fusebox Corporation. For written permission, please contact fusebox@fusebox.org.

If one or more of the above conditions are violated, then this license is immediately revoked and can be re-instated only upon prior written authorization of the Fusebox Corporation.

THIS SOFTWARE IS PROVIDED "AS IS" AND ANY EXPRESSED OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE FUSEBOX CORPORATION OR ITS CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

-------------------------------------------------------------------------------

This software consists of voluntary contributions made by many individuals on behalf of the Fusebox Corporation. For more information on Fusebox, please see <http://www.fusebox.org/>.

*/

function Location($URL, $addToken = 1) {
	$questionORamp = (strstr($URL, "?"))?"&":"?";
	$location = ( $addToken && substr($URL, 0, 7) != "http://" && defined('SID') ) ? $URL.$questionORamp.SID : $URL; //append the sessionID ($SID) by default
	//ob_end_clean(); //clear buffer, end collection of content
	if(headers_sent()) {
		print('<script type="text/javascript">( document.location.replace ) ? document.location.replace("'.$location.'") : document.location.href = "'.$location.'";</script>'."\n".'<noscript><meta http-equiv="Refresh" content="0;URL='.$location.'" /></noscript>');
	} else {
		header('Location: '.$location); //forward to another page
		exit; //end the PHP processing
	}
}

function ArrayInsertAt($array,$ky,$val) { 
	$n = $ky; 
	foreach($array as $key => $value) { 
		$backup_array[$key] = $array[$key]; 
	} 
	$upper_limit = count($array); 
	while($n <= $upper_limit) { 
		if($n == $ky) { 
			$array[$n] = $val; 
		} else { 
			$i = $n - "1"; 
			$array[$n] = $backup_array[$i]; 
		} 
		$n++; 
	} 
	return $array; 
} 

function __ListFindNoCase($inList, $inSubstr, $inDelim = ",") {
	$aryList = __listFuncs_PrepListAsArray($inList, $inDelim);
	$outIndex = 0;
	$intCounter = 0;
	foreach($aryList as $item) {
		$intCounter++;
		if(preg_match("/^" . preg_quote($inSubstr, "/") . "$/i", $item)) {
			$outIndex = $intCounter;
			break;
		}
	}
	return $outIndex;
}

function __ListLen($inList, $inDelim = ",") {
	$aryList = __listFuncs_PrepListAsArray($inList, $inDelim);
	$outInt = (strlen($inList)>0)?count($aryList):0;
	return $outInt;
}

function __ListFirst($inList, $inDelim = ",") {
	$aryList = __listFuncs_PrepListAsArray($inList, $inDelim);
	$outItem = array_shift($aryList);
	return $outItem;
}

function __ListLast($inList, $inDelim = ",") {
	$aryList = __listFuncs_PrepListAsArray($inList, $inDelim);
	$outItem = array_pop($aryList);
	return $outItem;
}

function __ListRest($inList, $inDelim = ",") {
	$aryList = __listFuncs_PrepListAsArray($inList, $inDelim);
	$outArray = array_slice($aryList,1);
	$outList = implode($inDelim,$outArray);
	return $outList;
}

function __ListGetAt($inList, $inPointer, $inDelim = ",") {
	$aryList = __listFuncs_PrepListAsArray($inList, $inDelim);
	$inPointer = ( $inPointer > 0 ) ? $inPointer - 1 : $inPointer;
	$outItem = ( count($aryList) < $inPointer ) ? false : $aryList[$inPointer];
	return $outItem;
}

function __listFuncs_PrepListAsArray($inList, $inDelim) {
	$inList = trim($inList);
	$inList = preg_replace("/^" . preg_quote($inDelim, "/") . "+/", "", $inList);
	$inList = preg_replace("/" . preg_quote($inDelim, "/") . "+$/", "", $inList);
	$outArray = preg_split("/" . preg_quote($inDelim, "/") . "+/", $inList);
	if(count($outArray) == 1 && $outArray[0] == "") {
		$outArray = array();
	}
	return $outArray;
}

function __cfthrow($_cfcatch){
	global $cfcatch;
	$cfcatch = $_cfcatch;
	if ( strlen($GLOBALS["FUSEBOX_APPLICATION_PATH"]) > 0 && substr($GLOBALS["FUSEBOX_APPLICATION_PATH"], -1) != "/" ) {
		$GLOBALS["FUSEBOX_APPLICATION_PATH"] .= "/";
	}
	die( (!@include($GLOBALS["FUSEBOX_APPLICATION_PATH"]."errortemplates/".$_cfcatch["type"].".php") ) ? $_cfcatch["detail"] : null );
}


// bring in the $application scope
/*
if ( file_exists("parsed/app_cacheddata.php") ) {
	$_parsedFileName = "parsed/app_cacheddata.php";
	$_parsedFileHandle = fopen($_parsedFileName, "r");
	$fb_["appData"] = fread($_parsedFileHandle, filesize($_parsedFileName));
	fclose($_parsedFileHandle);
	$application = unserialize($fb_["appData"]);
}
*/

// copy all FORM and URL variables to ATTRIBUTES scope
// here, FORM has precendence although this can be over-written later depending on the application's fusebox.xml setting.
if(!isset($attributes) || !is_array($attributes)) {
	$attributes = array();
	$attributes = array_merge($_GET,$_POST); 
}

// initialize the fusebox "working" structure (only for internal use of the core file(s) -- considered hands-off to developers
$fb_ = array();
$fb_["fuseQ"] = array();

$fb_["osdelimiter"] = DIRECTORY_SEPARATOR;
$fb_["corerootdirectory"] = dirname(__FILE__).DIRECTORY_SEPARATOR;

// initialize the myFusebox structure which is specific to a given request (can be read by the developer (and written to if creating plugins)
$myFusebox = array();
$myFusebox["version"] = array();
$myFusebox["version"]["runtime"]     = "unknown";
$myFusebox["version"]["loader"]      = "unknown";
$myFusebox["version"]["transformer"] = "unknown";
$myFusebox["version"]["parser"]      = "unknown";

$myFusebox["version"]["runtime"]     = "4.1.0";

$myFusebox["thisCircuit"]            = "";
$myFusebox["thisFuseaction"]         =  "";
$myFusebox["thisPlugin"]             = "";
$myFusebox["thisPhase"]              = "";
$myFusebox["plugins"]                = array();
$myFusebox["parameters"]             = array();

$myFusebox["parameters"]["load"]     = true;
$myFusebox["parameters"]["parse"]    = true;
$myFusebox["parameters"]["execute"]  = true;

$myFusebox["parameters"]["userProvidedLoadParameter"] = false;
$myFusebox["parameters"]["userProvidedParseParameter"] = false;
$myFusebox["parameters"]["userProvidedExecuteParameter"] = false;

// default myFusebox.parameters depending on "mode" of the application set in fusebox.xml
if ( isset($application["fusebox"]) && isset($application["fusebox"]["mode"]) ) {
	if ( $application["fusebox"]["mode"] == "development" ) {
		$myFusebox["parameters"]["load"] = true;
		$myFusebox["parameters"]["parse"] = true;
		$myFusebox["parameters"]["execute"] = true;
	}
	if ( $application["fusebox"]["mode"] == "production" ) {
		$myFusebox["parameters"]["load"] = false;
		$myFusebox["parameters"]["parse"] = false;
		$myFusebox["parameters"]["execute"] = true;
	}
}

if ( isset( $_SERVER['QUERY_STRING']) and strlen( $_SERVER['QUERY_STRING']) > 0 ) {
  // loop through query string to "fix" url variable names with dots in them
  $qs_array = split("[\&;]",$_SERVER['QUERY_STRING']);
  for ( $i = 0 ; $i < count( $qs_array ) ; $i++ ) {
    @list($thisname,$thisvalue) = explode("=",$qs_array[$i]);
    if ( !isset($attributes[$thisname]) ) $attributes[$thisname] = $thisvalue;
  }
}

// did the user pass in any special "fuseboxDOT" parameters for this request?
// If so, process them
// note: only if attributes.fusebox.password matches the application password
if ( !isset($attributes["fusebox.password"]) ) { $attributes["fusebox.password"] = ""; }
if ( isset($application["fusebox"]["password"]) && $application["fusebox"]["password"] == $attributes["fusebox.password"] ) {
	if ( isset($attributes["fusebox.load"]) && ($attributes["fusebox.load"] == "true" || $attributes["fusebox.load"] == "false") ) {
		$myFusebox["parameters"]["load"] = ($attributes["fusebox.load"] == "true");
		$myFusebox["parameters"]["userProvidedLoadParameter"] = true;

	}
	if ( isset($attributes["fusebox.parse"]) && ($attributes["fusebox.parse"] == "true" || $attributes["fusebox.parse"] == "false") ) {
		$myFusebox["parameters"]["parse"] = ($attributes["fusebox.parse"] == "true");
		$myFusebox["parameters"]["userProvidedParseParameter"] = true;

	}
	if ( isset($attributes["fusebox.execute"]) && ($attributes["fusebox.execute"] == "true" || $attributes["fusebox.execute"] == "false") ) {
		$myFusebox["parameters"]["execute"] = ($attributes["fusebox.execute"] == "true");
		$myFusebox["parameters"]["userProvidedExecuteParameter"] = true;

	}
}

// if application.fusebox doesn't already exist we definitely want to reload
if ( !isset($application["fusebox"]["isFullyLoaded"]) || !$application["fusebox"]["isFullyLoaded"] ) {
	$myFusebox["parameters"]["load"] = true;
}

// set up the appPath variable, which is the relative path from the web root to the app root
$fb_["appPathVarScope"] = $GLOBALS;
$fb_["appPathVarName"] = "FUSEBOX_APPLICATION_PATH";
if ( !isset($fb_["appPathVarScope"][$fb_["appPathVarName"]]) ) {
	__cfthrow(array(
		"type"=>"fusebox.missingAppPath",
		"message"=>$fb_["appPathVarName"]." not found.",
		"detail"=>"The required variable ".$fb_["appPathVarName"]." containing the relative path from the web root to the application root was not found.  If your web and application roots are the same directory, you can use the empty string as its value"
	));
}

$fb_["appPath"] = $fb_["appPathVarScope"][$fb_["appPathVarName"]];
// append a trailing slash, if needed
if ( substr($fb_["appPath"], -1) != "/" && strlen($fb_["appPath"]) > 0 ) {
	$fb_["appPath"] .= "/";
}

// if necessary, call the fusebox loader
if ( $myFusebox["parameters"]["load"] ) {
	$fb_["loaderFile"] = $fb_["corerootdirectory"]."fusebox4.loader.php4.php";
	if ( !@include($fb_["loaderFile"]) ) {
		__cfthrow(array(
			"type"=>"fusebox.missingCoreFile",
			"message"=>"core file not found.",
			"detail"=>"The core file ".$fb_["loaderFile"]." was not found. All core files should be of the same version as the calling Runtime core file."
		));
	} else if ( isset($fb_["errortype"]) && $fb_["errortype"] == "fusebox.LoadUnneeded" ) {
		// saving time!!
		$myFusebox["parameters"]["load"] = false;
	}
	// if we loaded the XML, we definitely want to parse
	$myFusebox["parameters"]["parse"] = true;
	$fb_["loaderForcedParse"] = true;
}

// make sure the correct structures are set up for myFusebox.plugins.{plugin-name} and any potential parameters it might have
foreach( array_keys($application["fusebox"]["plugins"]) as $fb_["aPlugin"] ) {
    $myFusebox["plugins"][$fb_["aPlugin"]] = array();
  }

// does this app give higher precedence to URL scope over FORM scope? If so, adjust
if( $application["fusebox"]["precedenceFormOrUrl"] == "URL" ) {
    $attributes = array_merge($_POST,$_GET);
  }

// if it exists, load the fusebox.init file in the application root
if ( file_exists($application["fusebox"]["WebRootToAppRootPath"]."fusebox.init.php") )
	include($application["fusebox"]["WebRootToAppRootPath"]."fusebox.init.php");

// how about a default fuseaction?
if( !isset($attributes[$application["fusebox"]["fuseactionVariable"]]) ) {
    $attributes[$application["fusebox"]["fuseactionVariable"]] = $application["fusebox"]["defaultFuseaction"];
  }

// copy the value of the fuseactionVariable into the variable "attributes.fuseaction" for standardization
$attributes["fuseaction"] = $attributes[$application["fusebox"]["fuseactionVariable"]];

//set the myFusebox.originalCircuit, myFusebox.originalFuseaction
if ( __ListLen($attributes["fuseaction"], '.') == 2 ) {
	$myFusebox["thisCircuit"]    = strtok($attributes["fuseaction"],".");
	$myFusebox["thisFuseaction"] = strtok(".");
	$myFusebox["originalCircuit"]    = $myFusebox["thisCircuit"];
	$myFusebox["originalFuseaction"] = $myFusebox["thisFuseaction"];
} else {
	__cfthrow(array(
		"type"=>"fusebox.malformedFuseaction",
		"message"=>"malformed Fuseaction",
		"detail"=>"You specified a malformed Fuseaction of ".$attributes["fuseaction"].".  A fully qualified Fuseaction must be in the form [Circuit].[Fuseaction]."
	));
}


// if the circuit specified by myFusebox.originalCircuit does not exist throw an error
// if the fuseaction specified by myFusebox.originalFuseaction does not exist throw an error
if(!isset($application["fusebox"]["circuits"][$myFusebox["originalCircuit"]]) ) {
	__cfthrow(array(
		"type"=>"fusebox.undefinedCircuit",
		"message"=>"undefined Circuit",
		"detail"=>"You specified a Circuit of ".$myFusebox["originalCircuit"]." which is not defined."
	));
} else {
	if( !isset($application["fusebox"]["circuits"][$myFusebox["originalCircuit"]]["fuseactions"][$myFusebox["originalFuseaction"]]) ) {
		__cfthrow(array(
			"type"=>"fusebox.undefinedFuseaction",
			"message"=>"undefined Fuseaction",
			"detail"=>"You specified a Fuseaction of ".$myFusebox["originalFuseaction"]." which is not defined in Circuit ".$myFusebox["originalCircuit"]."."
		));
	}
}

// ensure that the fuseaction has access="public"
if( $application["fusebox"]["circuits"][$myFusebox["originalCircuit"]]["fuseactions"][$myFusebox["thisFuseaction"]]["access"] != "public" ) {
	__cfthrow(array(
		"type"=>"fusebox.InvalidAccessModifier",
		"message"=>"Invalid Access Modifier",
		"detail"=>"You tried to access ".$myFusebox["originalCircuit"].".".$myFusebox["originalFuseaction"]." which does not have access modifier of public. A Fuseaction which is to be accessed from anywhere outside the application (such as called via an URL, or a FORM, or as a web service) must have an access modifier of public or if unspecified at least inherit such a modifier from its circuit."
	));
}

// here is the file to be parsed
$fb_["file2Parse"] = trim($application["fusebox"]["parsePath"] . strtolower($myFusebox["originalCircuit"].".".$myFusebox["originalFuseaction"].".".$application["fusebox"]["scriptFileDelimiter"]));
$fb_["assertedfile2Parse"] = trim($application["fusebox"]["parsePath"] . "_" . strtolower($myFusebox["originalCircuit"].".".$myFusebox["originalFuseaction"].".".$application["fusebox"]["scriptFileDelimiter"]));
$fb_["file2ParsePath"] = $application["fusebox"]["approotdirectory"] . $fb_["file2Parse"];
	
if ( !file_exists($fb_["file2ParsePath"]) ) $myFusebox["parameters"]["parse"] = true;

// see if we can avoid the parse file generation
if ( isset($application["fusebox"]["conditionalParse"]) && $application["fusebox"]["conditionalParse"] && $myFusebox["parameters"]["parse"] ) {
	$fb_["errortype"] = "";
	$fb_["keepGoing"] = true;
	while ($fb_["keepGoing"]) {
		// if we're in production mode, do the parse
		if ( $application["fusebox"]["mode"] != "development" ) {
			$fb_["errortype"] = "fusebox.forceParseException.production";
			$fb_["keepGoing"] = false;
			break;
		}
		
		// the XML was reloaded, so we need to parse
		if ( isset($fb_["loaderForcedParse"]) && $fb_["loaderForcedParse"] ) {
			$fb_["errortype"] = "fusebox.forceParseException.loaderForcedParse";
			$fb_["keepGoing"] = false;
			break;
		}
		
		// the user requested a parse
		if ( $myFusebox["parameters"]["userProvidedParseParameter"] && $attributes["fusebox.parse"] ) {
			$fb_["errortype"] = "fusebox.forceParseException.userRequestedParse";
			$fb_["keepGoing"] = false;
			break;
		}
		
		// see if the parse file is older than the last full load
		$fb_["parseFileTimestamp"] = filemtime($fb_["file2ParsePath"]);
		if ( $fb_["parseFileTimestamp"] === false ) {
			$fb_["errortype"] = "fusebox.forceParseException.parseFileNotFound";
			$fb_["keepGoing"] = false;
			break;
		}
		if ( $application["fusebox"]["dateLastLoaded"] > $fb_["parseFileTimestamp"] ) {
			$fb_["errortype"] = "fusebox.forceParseException.InMemoryIsNewer";
			$fb_["keepGoing"] = false;
			break;
		}
		
		// see if the parse file is older than any plugin file
		$fb_["scannedDirectories"] = "";
		foreach( array_keys(array("fuseactionException"=>"","processError"=>"")) as $fb_["phase"] ) {
			for( $fb_["plugin"] = 0 ; $fb_["plugin"] < count($application["fusebox"]["pluginphases"][$fb_["phase"]]) ; $fb_["plugin"]++ ) {
				$fb_["path"] = $application["fusebox"]["pluginphases"][$fb_["phase"]][$fb_["plugin"]]["path"].$application["fusebox"]["pluginphases"][$fb_["phase"]][$fb_["plugin"]]["template"];
				if ( __ListFindNoCase($fb_["scannedDirectories"], $fb_["path"], chr(5)) == 0 ) {
					if ($fb_["dirlist"] = opendir(dirname($application["fusebox"]["approotdirectory"].$fb_["path"])) ) {
						while (false !== ($file = readdir($fb_["dirlist"]))) {
							if ($file != "." && $file != "..") {
								$fb_["completepath"] = realpath($file);
								if( is_file($fb_["completepath"]) && filemtime($fb_["completepath"]) > $fb_["parseFileTimestamp"] ) {
									$fb_["errortype"] = "fusebox.forceParseException.pluginIsNewer";
									$fb_["keepGoing"] = false;
								}
							}
						}
						closedir($fb_["dirlist"]);
					}
					if( !$fb_["keepGoing"] ) break;
					$fb_["scannedDirectories"] .= chr(5) . $fb_["path"];
				}
			}
		}
		
		// see if the parse file is older than any lexicon file
		$fb_["scannedDirectories"] = "";
		foreach( array_keys($application["fusebox"]["lexicons"]) as $fb_["lex"] ) {
			$fb_["path"] = $application["fusebox"]["lexiconPath"] . $application["fusebox"]["lexicons"][$fb_["lex"]]["path"];
			if ( __ListFindNoCase($fb_["scannedDirectories"], $fb_["path"], chr(5)) == 0 ) {
				if ($fb_["dirlist"] = opendir(dirname($application["fusebox"]["approotdirectory"].$fb_["path"])) ) {
					while (false !== ($file = readdir($fb_["dirlist"]))) {
						if ($file != "." && $file != "..") {
							$fb_["completepath"] = realpath($file);
							if( is_file($fb_["completepath"]) && filemtime($fb_["completepath"]) > $fb_["parseFileTimestamp"] ) {
								$fb_["errortype"] = "fusebox.forceParseException.lexiconIsNewer";
								$fb_["keepGoing"] = false;
							}
						}
					}
					closedir($fb_["dirlist"]);
				}
				if( !$fb_["keepGoing"] ) break;
				$fb_["scannedDirectories"] .= chr(5) . $fb_["path"];
			}
		}
	
		// check the core files and see if any are newer than the parse file
		/* this is theoretically unneeded since it exists in the conditionalLoad as well,
			and if a load is performed a parse will be forced, but it's here as well
			for completeness' sake
		 */
		if ( $fb_["dirlist"] = opendir(dirname(realpath(__FILE__))) ) {
			while (false !== ($file = readdir($fb_["dirlist"]))) {
				if ($file != "." && $file != "..") {
					$fb_["completepath"] = realpath($file);
					if( is_file($fb_["completepath"]) && filemtime($fb_["completepath"]) > $fb_["parseFileTimestamp"] ) {
						$fb_["errortype"] = "fusebox.forceParseException.coreFileIsNewer";
						$fb_["keepGoing"] = false;
					}
				}
			}
			closedir($fb_["dirlist"]);
		}
		if( !$fb_["keepGoing"] ) break;
		
		// if we've gotten this far, the parse file is present and up to date, so skip the parse
		$fb_["errortype"] = "fusebox.parseUnneededException";
		$fb_["keepGoing"] = false;
		break;
	}
	if ( $fb_["errortype"] == "fusebox.forceParseException" ) $myFusebox["parameters"]["parse"] = true;
	if ( $fb_["errortype"] == "fusebox.parseUnneededException" ) $myFusebox["parameters"]["parse"] = false;
}

// if we need to re-parse, call the Transformer and Parser
if ( $myFusebox["parameters"]["parse"] ) {
	// call the Transformer
	$fb_["transformerFile"] = $fb_["corerootdirectory"]."fusebox4.transformer.".$application["fusebox"]["scriptLanguage"].".php";
	if ( !@include($fb_["transformerFile"]) ) {
		__cfthrow(array(
			"type"=>"fusebox.missingCoreFile",
			"message"=>"core file not found.",
			"detail"=>"The core file ".$fb_["transformerFile"]." was not found. All core files should be of the same version as the calling Runtime core file."
		));
	}

	// call the Parser
	$fb_["parserFile"] = $fb_["corerootdirectory"]."fusebox4.parser.".$application["fusebox"]["scriptLanguage"].".php";
	if ( !@include($fb_["parserFile"]) ) {
		__cfthrow(array(
			"type"=>"fusebox.missingCoreFile",
			"message"=>"core file not found.",
			"detail"=>"The core file ".$fb_["parserFile"]." was not found. All core files should be of the same version as the calling Runtime core file."
		));
	}
	$fb_["parsedfilecontents"] = $fb_["parsedfile"];
	$fb_["devparsedfilecontents"] = $fb_["parsedfile"];
	/* (old version)
	// strip out the comments around the assertion flags for development mode parsed file
	fb_.devparsedfilecontents = ReplaceNoCase(fb_.devparsedfilecontents, '<!-'&'--<assertion>', '', 'ALL');
	fb_.devparsedfilecontents = ReplaceNoCase(fb_.devparsedfilecontents, '</assertion>--'&'->', '', 'ALL');
	  (/old version)
	*/
	// for production mode file, strip out the entire assertion
	$fb_["assertionRegex"] = "/(\/\* <assertion>)(.*?)(<\/assertion> \*\/)/";
	$fb_["parsedfilecontents"] = preg_replace($fb_["assertionRegex"],"",$fb_["parsedfilecontents"]);
	// for development mode file, strip out the <!- --<assertion> (and its closing tag) but leave the content in-between
	$fb_["devparsedfilecontents"] = preg_replace($fb_["assertionRegex"],"\\2",$fb_["devparsedfilecontents"]);
	
	// delete the old parsed file
	if( file_exists($application["fusebox"]["rootdirectory"].$application["fusebox"]["osdelimiter"].$fb_["file2Parse"]) ) {
		while( !unlink($application["fusebox"]["rootdirectory"].$application["fusebox"]["osdelimiter"].$fb_["file2Parse"]) );
	}

	if ( $application["fusebox"]["mode"] != "production" ) {
		// delete the old dev parsed file
		if( file_exists($application["fusebox"]["approotdirectory"] . $fb_["assertedfile2Parse"]) ) {
		while( !unlink($application["fusebox"]["approotdirectory"] . $fb_["assertedfile2Parse"]) );
		}
	}
	
	// write out the parsed file
	$fp = fopen($application["fusebox"]["approotdirectory"].$fb_["file2Parse"],"w");
	if ( !flock($fp,LOCK_EX) ) {
		die("Could not get exclusive lock to Parsed File file");
	}
	if ( !fwrite($fp,$fb_["parsedfile"]) ) {
		__cfthrow(array(
			"type"=>"fusebox.errorWritingParsedFile",
			"message"=>"An Error during write of Parsed File or Parsing Directory not found.",
			"detail"=>"Attempting to write the parsed file '".$fb_["file2Parse"]."' threw an error. This can also occur if the parsed file directory cannot be found."
		));
	}
	flock($fp,LOCK_UN);
	fclose($fp);
	
	if ( !isset($fb_["hasAssertions"]) ) $fb_["hasAssertions"] = false;
	if ( $application["fusebox"]["mode"] != "production" ) {
		// write out the devparsed file
		if ( $fb_["hasAssertions"] ) {
			$fp = fopen($application["fusebox"]["approotdirectory"].$fb_["assertedfile2Parse"],"w");
			if ( !flock($fp,LOCK_EX) ) {
				die("Could not get exclusive lock to Parsed File file");
			}
			if ( !fwrite($fp,$fb_["devparsedfilecontents"]) ) {
				__cfthrow(array(
					"type"=>"fusebox.errorWritingParsedFile",
					"message"=>"An Error during write of Parsed File or Parsing Directory not found.",
					"detail"=>"Attempting to write the parsed file '".$fb_["assertedfile2Parse"]."' threw an error. This can also occur if the parsed file directory cannot be found."
				));
			}
			flock($fp,LOCK_UN);
			fclose($fp);
		}
	}
	
}
// OK, now execute everything
if ( $myFusebox["parameters"]["execute"] ) {
	if ( $application["fusebox"]["useAssertions"] == true && file_exists($application["fusebox"]["approotdirectory"] . $fb_["assertedfile2Parse"]) ) {
		$fb_["file2Execute"] = $application["fusebox"]["WebRootToAppRootPath"].$fb_["assertedfile2Parse"];
	} else {
		$fb_["file2Execute"] = $application["fusebox"]["WebRootToAppRootPath"].$fb_["file2Parse"];
	}
	if ( !include($fb_["file2Execute"]) ) {
		__cfthrow(array(
			"type"=>"fusebox.missingParsedFile",
			"message"=>"Parsed File or Directory not found.",
			"detail"=>"Attempting to execute the parsed file ".$fb_["file2Execute"]." threw an error. This can occur if the parsed file does not exist in the parsed directory or if the parsed directory itself is missing."
		));
	}
}

?>
