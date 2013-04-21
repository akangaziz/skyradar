<?php

// fusebox41.loader.php4.php

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

/* -------------------------------------------------------------+
| xmlParser function modified from source code found on php.net |
+--------------------------------------------------------------*/
function GetChildren($vals, &$i) {

   $children = array();

   while (++$i < sizeof($vals)) {

       // compair type
       switch ($vals[$i]['type']) {

           case 'cdata':
               $children[] = $vals[$i]['value'];
               break;
           case 'complete':
               $children[] = array(
                   'xmlName' => $vals[$i]['tag'], 
                   'xmlAttributes' => getAttributes($vals, $i), 
                   'xmlValue' => getValue($vals, $i),
				   'xmlChildren' => array()
               );
               break;
           case 'open':
               $children[] = array(
                   'xmlName' => $vals[$i]['tag'], 
                   'xmlAttributes' => getAttributes($vals, $i), 
                   'xmlValue' => getValue($vals, $i), 
                   'xmlChildren' => GetChildren($vals, $i)
               );        
               break;
           case 'close':
               return $children;
       }
   }
}

function getAttributes($vals, &$i){
	$attributes = array();
	if ( array_key_exists("attributes",$vals[$i]) ) {
		$attributes = $vals[$i]["attributes"];
	}
	return $attributes;
}

function getValue($vals, &$i){
	$value = "";
	if ( array_key_exists("value",$vals[$i]) ) {
		$value = $vals[$i]["value"];
	}
	return $value;
}

function xmlParse($data, $bList="") {

   $bArray = array();
	// if any attributes were passed to the function, add them to the array
	if ( strlen($bList) > 0 ) $bArray = explode(",",$bList);
	
   // by: waldo@wh-e.com - trim space around tags not within
   $data = eregi_replace(">"."[[:space:]]+"."<","><",$data);

   // XML functions
   $p = xml_parser_create();

   // by: anony@mous.com - meets XML 1.0 specification
   xml_parser_set_option($p, XML_OPTION_CASE_FOLDING, 0);
   xml_parse_into_struct($p, $data, $vals, $index);
   xml_parser_free($p);

	for ( $x = 0 ; $x < count($vals) ; $x++ ) {
		if ( array_key_exists("attributes",$vals[$x]) ) {
			foreach ( $vals[$x]["attributes"] as $thiskey=>$thisvalue ) {
				
				// if the attribute name exists in the "bList" then re-cast the string to a boolean
				if ( ( is_string($thisvalue) && array_search($thiskey,$bArray) !== false ) && ( strtolower($thisvalue) == "true" || strtolower($thisvalue) == "false" ) ) {
					$vals[$x]["attributes"][$thiskey] = (strtolower($thisvalue)=="true");
				}
			}
		}
	}
   
   $i = 0;
   $tree["xmlChildren"] = array();
   $tree["xmlChildren"][] = array(
       'xmlName' => $vals[$i]['tag'], 
       'xmlAttributes' => getAttributes($vals, $i), 
       'xmlValue' => getValue($vals, $i), 
       'xmlChildren' => GetChildren($vals, $i)
   );

   return $tree;
}
//=============================================================+

$found = array();
function array_search_r($needle, $haystack){
    global $found;
	foreach ($haystack as $key => $row) {
	     if (is_array($row)){
		 	array_search_r($needle, $row);
		 }
		 if ( $key == "xmlName" && $row == $needle) {
	        $found = array_merge(array(),$haystack);
			break;
		}
	}
} 

/** 
* provided by Alan Richmond, originally found in Mach-II for PHP
* Attempt to discover the document root.
*
* If it can't be determined from $_SERVER variables it is
* assumed to be the current working directory.
* @return string
*/

$myFusebox["version"]["loader"] = "4.1.0";

if ( !isset($GLOBALS["canonicalPath__"]) ) include($fb_["corerootdirectory"]."udf_canonicalpath.php");

// initialize the application.fusebox (available to be read by developers but not to be written to)
$fb_["application"]["fusebox"] = array();
$fb_["application"]["fusebox"]["isFullyLoaded"] = false;
$fb_["application"]["fusebox"]["circuits"] = array();
$fb_["application"]["fusebox"]["classes"] = array();
$fb_["application"]["fusebox"]["lexicons"] = array();
$fb_["application"]["fusebox"]["plugins"] = array();
$fb_["application"]["fusebox"]["pluginphases"] = array();

// compute the current directory.  This is the same as the
// fb_.rootdir variable that gets set later, but this variable is needed
// for ALL requests, not just for the loader.
$fb_["application"]["fusebox"]["webrootdirectory"] = getcwd().DIRECTORY_SEPARATOR;
if ( file_exists($fb_["appPath"]."fusebox.xml") ) {
	$fb_["application"]["fusebox"]["approotdirectory"] = dirname(realpath($fb_["appPath"]."fusebox.xml")).$fb_["osdelimiter"];
} else if ( file_exists($fb_["appPath"]."fusebox.xml.php") ) {
	$fb_["application"]["fusebox"]["approotdirectory"] = dirname(realpath($fb_["appPath"]."fusebox.xml.php")).$fb_["osdelimiter"];
} else {
	$fb_["application"]["fusebox"]["approotdirectory"] = dirname(getcwd().DIRECTORY_SEPARATOR.$fb_["appPath"]."dummy.file").$fb_["osdelimiter"];
}
// for 4.0 compatibility
$fb_["application"]["fusebox"]["rootdirectory"] = $fb_["application"]["fusebox"]["approotdirectory"];
// the file separator for the platform
$fb_["application"]["fusebox"]["osdelimiter"] = DIRECTORY_SEPARATOR;

// compute the relative path from the core files back to the application dir
if ( !isset($GLOBALS["relativeFilePath__"]) ) {
	include($fb_["corerootdirectory"]."udf_relativefilepath.php");
}
$fb_["application"]["fusebox"]["CoreToAppRootPath"] = dirname(relativeFilePath__(realpath(__FILE__),canonicalPath__($fb_["application"]["fusebox"]["approotdirectory"]."dummy.file", $fb_["osdelimiter"])))."/";
$fb_["application"]["fusebox"]["AppRootToCorePath"] = dirname(relativeFilePath__(canonicalPath__($fb_["application"]["fusebox"]["approotdirectory"]."dummy.file", $fb_["osdelimiter"]),realpath(__FILE__)))."/";
$fb_["application"]["fusebox"]["CoreToWebRootPath"] = dirname(relativeFilePath__(realpath(__FILE__),canonicalPath__($fb_["application"]["fusebox"]["webrootdirectory"]."dummy.file", $fb_["osdelimiter"])))."/";
$fb_["application"]["fusebox"]["WebRootToCorePath"] = dirname(relativeFilePath__(canonicalPath__($fb_["application"]["fusebox"]["webrootdirectory"]."dummy.file", $fb_["osdelimiter"]),realpath(__FILE__)))."/";
$fb_["application"]["fusebox"]["WebRootToAppRootPath"] = ( $fb_["appPath"] != "/" ) ? $fb_["appPath"] : "";
// location of parsed files
$fb_["application"]["fusebox"]["parsePath"] = "parsed" . $fb_["application"]["fusebox"]["osdelimiter"];

// location of plugins
$fb_["application"]["fusebox"]["pluginsPath"] = "plugins" . $fb_["application"]["fusebox"]["osdelimiter"];

// location of lexicon
$fb_["application"]["fusebox"]["lexiconPath"] = "lexicon" . $fb_["application"]["fusebox"]["osdelimiter"];

// location of error templates
$fb_["application"]["fusebox"]["errortemplatesPath"] = "errortemplates" . $fb_["application"]["fusebox"]["osdelimiter"];

// make sure the "parsed" and "plugins" directorys exist
@mkdir($fb_["application"]["fusebox"]["approotdirectory"]."parsed",0777);
@mkdir($fb_["application"]["fusebox"]["approotdirectory"]."plugins",0777);

// now that we've got all the paths we need, see if everything is up to date and abort the loading process
// we should assume that if there are no circuits in application, the fusebox xml file hasn't been processed yet, so we need a full load

$fb_["keepGoing"] = ( count($fb_["application"]["fusebox"]["circuits"]) > 0 || !isset($application) ) ? false : true;
$fb_["errortype"] = "";
while ( $fb_["keepGoing"] ) {
	// if we're production, do the full load
	if ( $application["fusebox"]["mode"] != "development" ) {
		$fb_["errortype"] = "fusebox.forceLoadException";  //production
		$fb_["keepGoing"] = false;
		break;
	}
	
	// the user requested a full load
	if ( $myFusebox["parameters"]["userProvidedLoadParameter"] && strtolower($attributes["fusebox.load"]) == "true" ) {
		$fb_["errortype"] = "fusebox.forceLoadException"; //userRequestedLoad
		$fb_["keepGoing"] = false;
		break;
	}
	
	// we started a load, but it wasn't completed
	if ( !isset($application["fusebox"]["isFullyLoaded"]) || !$application["fusebox"]["isFullyLoaded"] ) {
		$fb_["errortype"] = "fusebox.forceLoadException"; //partialLoad
		$fb_["keepGoing"] = false;
		break;
	}
	
	// the app root has changed, meaning all the XML has changed
	if ( isset($application["fusebox"]["approotdirectory"]) && $application["fusebox"]["approotdirectory"] != $fb_["application"]["fusebox"]["approotdirectory"] ) {
		$fb_["errortype"] = "fusebox.forceLoadException"; //newAppRoot
		$fb_["keepGoing"] = false;
		break;
	}
	
	// check if fusebox.xml has been touched since it was last loaded
	$fb_["filecount"] = 0;
	if ($fb_["dirlist"] = opendir($fb_["application"]["fusebox"]["approotdirectory"]) ) {
		while (false !== ($file = readdir($fb_["dirlist"]))) {
			if ( strlen($file) >= 12 && strtolower(substr($file,0,11)) == "fusebox.xml" ) {
				$fb_["_dirlist"]["datelastmodified"] = filemtime(realpath($file));
				$fb_["filecount"]++;
			}
		}
		closedir($fb_["dirlist"]);
	}
	if ( $fb_["filecount"] != 1 ) {
		$fb_["errortype"] = "fusebox.forceLoadException"; //noFuseboxXml
		$fb_["keepGoing"] = false;
		break;
	}
	
	if ( $fb_["_dirlist"]["datelastmodified"] > $application["fusebox"]["timestamp"] ) {
		$fb_["errortype"] = "fusebox.forceLoadException"; //fuseboxXmlIsNewer
		$fb_["keepGoing"] = false;
		break;
	}
	
	// loop over the circuits and see if any have been touched since they were last loaded
	foreach( array_keys($application["fusebox"]["circuits"]) as $fb_["i"] ) {
		$fb_["filecount"] = 0;
		if ($fb_["dirlist"] = opendir($fb_["application"]["fusebox"]["approotdirectory"].$application["fusebox"]["circuits"][$fb_["i"]]["path"]) ) {
			while (false !== ($file = readdir($fb_["dirlist"]))) {
				if ( strlen($file) >= 12 && strtolower(substr($file,0,11)) == "circuit.xml" ) {
					$mydate = filemtime(realpath($fb_["application"]["fusebox"]["approotdirectory"].$application["fusebox"]["circuits"][$fb_["i"]]["path"].'/'.$file));
					$fb_["filecount"]++;
				}
			}
			closedir($fb_["dirlist"]);
		}
		if ( $fb_["filecount"] != 1 ) {
			$fb_["errortype"] = "fusebox.forceLoadException"; //noCircuitXml
			$fb_["keepGoing"] = false;
			break;
		}
		// NEXT LINE IS DEBUG ONLY
		if ( $mydate > $application["fusebox"]["circuits"][$fb_["i"]]["timestamp"] ) {
			$fb_["errortype"] = "fusebox.forceLoadException"; //CircuitXmlIsNewer
			$fb_["keepGoing"] = false;
			break;
		}
		if ( !$fb_["keepGoing"] ) break;
	}
	if ( !$fb_["keepGoing"] ) break;
	
	// check the core files and see if any are newer than the last load
	if ($fb_["dirlist"] = opendir(dirname(realpath(__FILE__))) ) {
		while (false !== ($file = readdir($fb_["dirlist"]))) {
			if ( filemtime(realpath($file)) > $application["fusebox"]["dateLastLoaded"] ) {
				$fb_["errortype"] = "fusebox.forceLoadException"; //coreFileIsNewer
				$fb_["keepGoing"] = false;
				break;
			}
		}
		closedir($fb_["dirlist"]);
	}
	
	if ( !$fb_["keepGoing"] ) break;

	// if we've gotten this far, the in-memory cache is up to date, so abort the the load
	
	$fb_["errortype"] = "fusebox.LoadUnneeded";
	$fb_["keepGoing"] = false;
	break;
}

if ( $fb_["errortype"] != "fusebox.LoadUnneeded" ) {

	if ( $fb_["errortype"] == "fusebox.forceLoadException" ) {
		// a test of up-to-dateness failed
	}
	
	// an interim write to the application.fusebox structure so we have the minimum needed in case there's a problem with the XML files
	$application["fusebox"] = array_merge($fb_["application"]["fusebox"],array());
	
	// read the fusebox.xml file
	$fb_["fuseboxXMLfile"] = $fb_["application"]["fusebox"]["approotdirectory"]."fusebox.xml.php";
    if ( !file_exists($fb_["fuseboxXMLfile"]) ) {
        if ( file_exists($fb_["application"]["fusebox"]["approotdirectory"]."fusebox.xml") ) {
            $fb_["fuseboxXMLfile"] = $fb_["application"]["fusebox"]["approotdirectory"]."fusebox.xml";
        } else {
            __cfthrow(array(
                "type"=>"fusebox.missingFuseboxXML",
                "message"=>"missing fusebox.xml",
                "detail"=>"The file ".$fb_["application"]["fusebox"]["approotdirectory"]."fusebox.xml could not be found."
            ));
        }
    }
    
	$fp = fopen($fb_["fuseboxXMLfile"],"r");
	if ( !flock($fp,LOCK_SH) ) {
		die("Could not get exclusive lock to fusebox xml file");
	} else {
		
		$fb_["fuseboxXMLcode"] = fread($fp, filesize($fb_["fuseboxXMLfile"]));
		
		// pull out the character encoding
		/* I don't know if we need this right now, so I haven't done it */
		
		// now reload the fusebox.xml file using the known characterEncoding in case anything else in it requires some special encoding
		$fb_["application"]["fusebox"]["xml"] = xmlParse($fb_["fuseboxXMLcode"],"value,overwrite,append");
		
		// give this memory structure a timestamp
		$fb_["timestamp"] = getdate();
		$fb_["application"]["fusebox"]["timestamp"] = $fb_["timestamp"][0];
		
		// parse the "application.fusebox" fusebox parameters properties
		array_search_r("parameters",$fb_["application"]["fusebox"]["xml"]);
		
		$parameters = $found["xmlChildren"];
	    foreach( $parameters as $thisparam ) {
			$fb_["application"]["fusebox"][$thisparam["xmlAttributes"]["name"]] = $thisparam["xmlAttributes"]["value"];
		}
		if ( !isset($fb_["application"]["fusebox"]["precedenceFormOrUrl"]) ) $fb_["application"]["fusebox"]["precedenceFormOrUrl"] = "form";
		if ( !isset($fb_["application"]["fusebox"]["defaultFuseaction"]) ) $fb_["application"]["fusebox"]["defaultFuseaction"] = "";
		if ( !isset($fb_["application"]["fusebox"]["fuseactionVariable"]) ) $fb_["application"]["fusebox"]["fuseactionVariable"] = "fuseaction";
		if ( !isset($fb_["application"]["fusebox"]["parseWithComments"]) ) $fb_["application"]["fusebox"]["parseWithComments"] = false;
		if ( !isset($fb_["application"]["fusebox"]["ignoreBadGrammar"]) ) $fb_["application"]["fusebox"]["ignoreBadGrammar"] = true;
		if ( !isset($fb_["application"]["fusebox"]["allowLexicon"]) ) $fb_["application"]["fusebox"]["allowLexicon"] = true;
		if ( !isset($fb_["application"]["fusebox"]["useAssertions"]) ) $fb_["application"]["fusebox"]["useAssertions"] = true;
		if ( !isset($fb_["application"]["fusebox"]["conditionalParse"]) ) $fb_["application"]["fusebox"]["conditionalParse"] = false;
		
		if ( !isset($fb_["application"]["fusebox"]["password"]) ) $fb_["application"]["fusebox"]["password"] = "";
		if ( !isset($fb_["application"]["fusebox"]["mode"]) ) $fb_["application"]["fusebox"]["mode"] = "production";
		if ( !isset($fb_["application"]["fusebox"]["scriptLanguage"]) ) $fb_["application"]["fusebox"]["scriptLanguage"] = "php4";
		if ( !isset($fb_["application"]["fusebox"]["scriptFileDelimiter"]) ) $fb_["application"]["fusebox"]["scriptFileDelimiter"] = "php";
		if ( !isset($fb_["application"]["fusebox"]["maskedFileDelimiters"]) ) $fb_["application"]["fusebox"]["maskedFileDelimiters"] = "htm,cfm,cfml,php,php4,asp,aspx,class,inc";	
		if ( !isset($fb_["application"]["fusebox"]["parseWithIndentation"]) ) $fb_["application"]["fusebox"]["parseWithIndentation"] = $fb_["application"]["fusebox"]["parseWithComments"];
		
		//parse the global fuseactions, both preprocess and postprocess
		array_search_r("preprocess",$fb_["application"]["fusebox"]["xml"]);
		$fb_["application"]["fusebox"]["globalfuseactions"]["preprocess"]["xml"] = $found;
		array_search_r("postprocess",$fb_["application"]["fusebox"]["xml"]);
		$fb_["application"]["fusebox"]["globalfuseactions"]["postprocess"]["xml"] = $found;
		
		// parse the class definitions
		array_search_r("classes",$fb_["application"]["fusebox"]["xml"]);
		$fb_["xnClasses"] = $found["xmlChildren"];
		
		for ($fb_["i"] = 0; $fb_["i"] < count($fb_["xnClasses"]); $fb_["i"]++)
		{
			$fb_["alias"] = $fb_["xnClasses"][$fb_["i"]]["xmlAttributes"]["alias"];
			$fb_["type"] = ( isset($fb_["xnClasses"][$fb_["i"]]["xmlAttributes"]["type"]) ) ? $fb_["xnClasses"][$fb_["i"]]["xmlAttributes"]["type"] : "";
			$fb_["classpath"] = $fb_["xnClasses"][$fb_["i"]]["xmlAttributes"]["classpath"];
			if (array_key_exists('constructor',$fb_["xnClasses"][$fb_["i"]]["xmlAttributes"]) && strlen($fb_["xnClasses"][$fb_["i"]]["xmlAttributes"]["constructor"]))
			{
				$fb_["constructor"] = $fb_["xnClasses"][$fb_["i"]]["xmlAttributes"]["constructor"];
			} 
			else
			{
				$fb_["constructor"] = "init";
			}
			
			$fb_["application"]["fusebox"]["classes"][$fb_['alias']] = array();
			$fb_["application"]["fusebox"]["classes"][$fb_['alias']]['classpath'] = $fb_["classpath"];
			$fb_["application"]["fusebox"]["classes"][$fb_['alias']]['type'] = $fb_["type"];
			$fb_["application"]["fusebox"]["classes"][$fb_['alias']]['constructor'] = $fb_["constructor"];
		}
		
		
		// parse the lexicon definitions
		array_search_r("lexicons",$fb_["application"]["fusebox"]["xml"]);
		$fb_["xnLexicons"] = $found["xmlChildren"];
		$fb_["namespace"] = ' xmlns="http://www.fusebox.org/"';
		for ( $fb_["i"] = 0 ; $fb_["i"] < count($fb_["xnLexicons"]) ; $fb_["i"]++ ) {
			
			$fb_["xmlns"] = $fb_["xnLexicons"][$fb_["i"]]["xmlAttributes"]["namespace"];
			$fb_["path"]  = $fb_["xnLexicons"][$fb_["i"]]["xmlAttributes"]["path"];
			
			$fb_["application"]["fusebox"]["lexicons"][$fb_["xmlns"]] = array();
			$fb_["application"]["fusebox"]["lexicons"][$fb_["xmlns"]]["path"] = $fb_["path"];
			$fb_["namespace"] .' xmlns:'.$fb_["xmlns"].'="lexicon/'.$fb_["path"].'"';
		}
		  
		// an interim write to the application.fusebox structure 
		$application["fusebox"] = array_merge($fb_["application"]["fusebox"],array());
		
		// parse the circuit definitions
		array_search_r("circuits",$fb_["application"]["fusebox"]["xml"]);
		$circuits = $found["xmlChildren"];
	    foreach( $circuits as $thiscircuit ) {
			
			// if no attribute for path then insert it as empty string
			if ( !isset($thiscircuit["xmlAttributes"]["path"]) ) {
				$thiscircuit["xmlAttributes"]["path"] = "";
			}
			// if no attribute for parent then insert it as empty string
			if ( !isset($thiscircuit["xmlAttributes"]["parent"]) ) {
				$thiscircuit["xmlAttributes"]["parent"] = "";
			}
			
			$fb_["path"] = $thiscircuit["xmlAttributes"]["path"];
			$fb_["parent"] = $thiscircuit["xmlAttributes"]["parent"];
			$fb_["alias"] = $thiscircuit["xmlAttributes"]["alias"];
			
			$fb_["application"]["fusebox"]["circuits"][$fb_["alias"]]["path"] = $fb_["path"];
			$fb_["application"]["fusebox"]["circuits"][$fb_["alias"]]["parent"] = $fb_["parent"];
			
			$fb_["rootpath"] = array();
			$fb_["rootdir"] = explode($fb_["application"]["fusebox"]["osdelimiter"],realpath("."));
			
			if( strpos($thiscircuit["xmlAttributes"]["path"],"/") !== false ){
				$fb_["aDirs"] = explode("/",$thiscircuit["xmlAttributes"]["path"]);
				foreach( $fb_["aDirs"] as $fb_["j"] ) {
					if( $fb_["j"] == ".." ) {
						array_unshift($fb_["rootpath"],array_pop($fb_["rootdir"]));
					}
					elseif( strlen($fb_["j"]) > 0 ) {
						array_unshift($fb_["rootpath"],"..");
					}
				}
			}
			$fb_["rootpath"] = implode("/",$fb_["rootpath"]);
			if ( strlen($fb_["rootpath"]) > 0 ) {
				$fb_["rootpath"] .= "/";
			}
			$fb_["application"]["fusebox"]["circuits"][$thiscircuit["xmlAttributes"]["alias"]]["rootpath"] = $fb_["rootpath"];
			
			// read in the circuit.xml file
			$fb_["circuitXMLfile"] = $fb_["application"]["fusebox"]["approotdirectory"].ereg_replace("\\/",$fb_["application"]["fusebox"]["osdelimiter"],$fb_["application"]["fusebox"]["circuits"][$thiscircuit["xmlAttributes"]["alias"]]["path"])."circuit.xml.php";
            if ( !file_exists($fb_["circuitXMLfile"]) ) {
				if ( file_exists($fb_["application"]["fusebox"]["approotdirectory"].ereg_replace("\\/",$fb_["application"]["fusebox"]["osdelimiter"],$fb_["application"]["fusebox"]["circuits"][$thiscircuit["xmlAttributes"]["alias"]]["path"])."circuit.xml") ) {
    				$fb_["circuitXMLfile"] = $fb_["application"]["fusebox"]["approotdirectory"].ereg_replace("\\/",$fb_["application"]["fusebox"]["osdelimiter"],$fb_["application"]["fusebox"]["circuits"][$thiscircuit["xmlAttributes"]["alias"]]["path"])."circuit.xml";
    			} else {
    				__cfthrow(array(
    					"type"=>"fusebox.missingCircuitXML",
    					"message"=>"missing circuit.xml",
    					"detail"=>"The circuit xml file, ".$fb_["circuitXMLfile"].", for circuit ".$fb_["alias"]." could not be found."
    				));
                }
			}
			$cp = fopen($fb_["circuitXMLfile"],"r");
			$fb_["circuitXMLcode"] = fread($cp, filesize($fb_["circuitXMLfile"]));
			$fb_["application"]["fusebox"]["circuits"][$thiscircuit["xmlAttributes"]["alias"]]["xml"] = xmlParse($fb_["circuitXMLcode"],"overwrite,evaluate,append,addtoken,required,callsuper");
			$fb_["application"]["fusebox"]["circuits"][$fb_["alias"]]["timestamp"] = $fb_["timestamp"][0];
			fclose($cp);
		}
		
		// an interim write to the application.fusebox structure 
		$application["fusebox"] = array_merge($fb_["application"]["fusebox"],array());
		
		// loop over all circuits to determine each circuit's "circuitTrace"
		foreach( array_keys($fb_["application"]["fusebox"]["circuits"]) as $fb_["aCircuit"] ) {
			$fb_["application"]["fusebox"]["circuits"][$fb_["aCircuit"]]["circuitTrace"] = array();
			array_push($fb_["application"]["fusebox"]["circuits"][$fb_["aCircuit"]]["circuitTrace"], $fb_["aCircuit"]);
			$fb_["thisCircuit"] = $fb_["application"]["fusebox"]["circuits"][$fb_["aCircuit"]]["parent"];
			while (strlen(trim($fb_["thisCircuit"])) > 0) {
				array_push($fb_["application"]["fusebox"]["circuits"][$fb_["aCircuit"]]["circuitTrace"], $fb_["thisCircuit"]);
				$fb_["thisCircuit"] = $fb_["application"]["fusebox"]["circuits"][$fb_["thisCircuit"]]["parent"];
			}
		}
	    
		// loop over all circuits to determine its attributes and its fuseactions
		foreach( array_keys($fb_["application"]["fusebox"]["circuits"]) as $fb_["aCircuit"] ) {
			$fb_["xnCircuit"] = $fb_["application"]["fusebox"]["circuits"][$fb_["aCircuit"]]["xml"]["xmlChildren"];
			
			// determine the circuit's access modifier
			if( array_key_exists("access",$fb_["xnCircuit"][0]["xmlAttributes"]) ) {
				$fb_["application"]["fusebox"]["circuits"][$fb_["aCircuit"]]["access"] = $fb_["xnCircuit"][0]["xmlAttributes"]["access"];
			} else {
				// by default, a circuit's access modifier is "internal" (accessible anywhere from inside the app but not from outside)
				// note: this is important since any of a circuit's fuseactions who do not have an access modifier will inherit the access modifier of its circuit
				$fb_["application"]["fusebox"]["circuits"][$fb_["aCircuit"]]["access"] = "internal";
			}
			
			// determine the circuit's permissions
			if( array_key_exists("permissions",$fb_["xnCircuit"][0]["xmlAttributes"]) ) {
				$fb_["application"]["fusebox"]["circuits"][$fb_["aCircuit"]]["permissions"] = $fb_["xnCircuit"][0]["xmlAttributes"]["permissions"];
			} else {
				$fb_["application"]["fusebox"]["circuits"][$fb_["aCircuit"]]["permissions"] = "";
			}
			
			// determine all the circuit's fuseactions, prefuseactions, and postfuseactions
			$fb_["application"]["fusebox"]["circuits"][$fb_["aCircuit"]]["fuseactions"] = array();
			$fb_["application"]["fusebox"]["circuits"][$fb_["aCircuit"]]["prefuseaction"] = array();
			$fb_["application"]["fusebox"]["circuits"][$fb_["aCircuit"]]["postfuseaction"] = array();
			
			$fb_["application"]["fusebox"]["circuits"][$fb_["aCircuit"]]["prefuseaction"]["xml"] = array();
			$fb_["application"]["fusebox"]["circuits"][$fb_["aCircuit"]]["postfuseaction"]["xml"] = array();
			
			$fb_["application"]["fusebox"]["circuits"][$fb_["aCircuit"]]["prefuseaction"]["callsuper"] = false;
			$fb_["application"]["fusebox"]["circuits"][$fb_["aCircuit"]]["postfuseaction"]["callsuper"] = false;
	
			if( isset($fb_["xnCircuit"][0]["xmlChildren"]) ){
				foreach( $fb_["xnCircuit"][0]["xmlChildren"] as $fb_["i"] ) {
					/* the fuseactions */
					if( $fb_["i"]["xmlName"] == "fuseaction" ){
						$fb_["name"] = $fb_["i"]["xmlAttributes"]["name"];
						$fb_["application"]["fusebox"]["circuits"][$fb_["aCircuit"]]["fuseactions"][$fb_["name"]] = array();
						$fb_["application"]["fusebox"]["circuits"][$fb_["aCircuit"]]["fuseactions"][$fb_["name"]]["xml"] = $fb_["i"];
	          
						// determine the fuseaction's access modifier
						if( array_key_exists("access",$fb_["i"]["xmlAttributes"]) ) {
							$fb_["application"]["fusebox"]["circuits"][$fb_["aCircuit"]]["fuseactions"][$fb_["name"]]["access"] = $fb_["i"]["xmlAttributes"]["access"];
						} else {
							// by default, a fuseaction has no access modifier then it inherits that of its parent
							$fb_["application"]["fusebox"]["circuits"][$fb_["aCircuit"]]["fuseactions"][$fb_["name"]]["access"] = $fb_["application"]["fusebox"]["circuits"][$fb_["aCircuit"]]["access"];
						}
						
						// determine the fuseaction's permissions
						if( array_key_exists("permissions",$fb_["i"]["xmlAttributes"]) ) {
							$fb_["application"]["fusebox"]["circuits"][$fb_["aCircuit"]]["fuseactions"][$fb_["name"]]["permissions"] = $fb_["i"]["xmlAttributes"]["permissions"];
						} else {
							// by default, a fuseaction's permissions is the empty string
							$fb_["application"]["fusebox"]["circuits"][$fb_["aCircuit"]]["fuseactions"][$fb_["name"]]["permissions"] = "";
						}
					}
					/* the prefuseactions */
					else if ( $fb_["i"]["xmlName"] == "prefuseaction") {
						$fb_["application"]["fusebox"]["circuits"][$fb_["aCircuit"]]["prefuseaction"]["xml"] = $fb_["i"];
						if ( (isset($fb_["i"]["xmlAttributes"]["callsuper"])) && $fb_["i"]["xmlAttributes"]["callsuper"] ) {
							$fb_["application"]["fusebox"]["circuits"][$fb_["aCircuit"]]["prefuseaction"]["callsuper"] = true;
						}
					}
					
					/* the postfuseactions */
					else if ( $fb_["i"]["xmlName"] == "postfuseaction") {
						$fb_["application"]["fusebox"]["circuits"][$fb_["aCircuit"]]["postfuseaction"]["xml"] = $fb_["i"];
						if ( (isset($fb_["i"]["xmlAttributes"]["callsuper"])) && $fb_["i"]["xmlAttributes"]["callsuper"] ) {
							$fb_["application"]["fusebox"]["circuits"][$fb_["aCircuit"]]["postfuseaction"]["callsuper"] = true;
						}
					}
				}
			}
			// unset the xml data for the circuit's xmlChildren (fuseactions) - this will reduce $application size by almost half
			unset($fb_["application"]["fusebox"]["circuits"][$fb_["aCircuit"]]["xml"]['xmlChildren'][0]['xmlChildren']);
		}
		
		/* determine application.fusebox.parseRootPath, the inverse path of application.fusebox.parsePath */
		$fb_["application"]["fusebox"]["parseRootPath"] = array();
		$fb_["rootdir"] = explode("/",str_replace($fb_["application"]["fusebox"]["osdelimiter"],"/",realpath(".")));
		$fb_["aDirs"] = explode($fb_["application"]["fusebox"]["osdelimiter"],$fb_["application"]["fusebox"]["parsePath"]);
		for( $fb_["i"] = 0 ; $fb_["i"] < count($fb_["aDirs"]) ; $fb_["i"]++ ) {
			if( $fb_["aDirs"][$fb_["i"]] == ".." ) {
				array_unshift($fb_["application"]["fusebox"]["parseRootPath"],array_pop($fb_["rootdir"]));
			}
			elseif( strlen($fb_["aDirs"][$fb_["i"]]) ) {
				array_unshift($fb_["application"]["fusebox"]["parseRootPath"],"..");
			}
		}
		$fb_["application"]["fusebox"]["parseRootPath"] = implode($fb_["application"]["fusebox"]["parseRootPath"],$fb_["application"]["fusebox"]["osdelimiter"]);
		if( strlen($fb_["application"]["fusebox"]["parseRootPath"]) > 0 ) {
			$fb_["application"]["fusebox"]["parseRootPath"] .= $fb_["application"]["fusebox"]["osdelimiter"];
		}
		
		/*  parse the plugins 
			sometimes we'll need to refer to the plugins by Name and sometimes by Phase
		*/
		array_search_r("plugins",$fb_["application"]["fusebox"]["xml"]);
		$fb_["xnPluginPhases"] = $found["xmlChildren"];
		
		// loop over all the plugin phases
		foreach( $fb_["xnPluginPhases"] as $fb_["i"] ) {
			$fb_["phase"] = $fb_["i"]["xmlAttributes"]["name"];
			$fb_["application"]["fusebox"]["pluginphases"][$fb_["phase"]] = array();
			$fb_["xnPlugins"] = $fb_["i"];
			
			// loop over all the plugins for a given phase
			if( isset($fb_["xnPlugins"]["xmlChildren"][0]) ){
				foreach( $fb_["xnPlugins"]["xmlChildren"] as $fb_["j"] ) {
					$fb_["name"] = $fb_["j"]["xmlAttributes"]["name"];
					$fb_["template"] = $fb_["j"]["xmlAttributes"]["template"];
					$fb_["path"] = $fb_["application"]["fusebox"]["pluginsPath"];
					if( array_key_exists("path",$fb_["j"]["xmlAttributes"]) ) {
						$fb_["path"] .= $fb_["j"]["xmlAttributes"]["path"];
					}
					
					if( !array_key_exists($fb_["name"],$fb_["application"]["fusebox"]["plugins"]) ) {
						$fb_["application"]["fusebox"]["plugins"][$fb_["name"]] = array();
					}
					$fb_["application"]["fusebox"]["plugins"][$fb_["name"]][$fb_["phase"]] = array();
					
					$fb_["rootpath"] = array();
					$fb_["rootdir"] = explode($fb_["application"]["fusebox"]["osdelimiter"],realpath("."));
					$fb_["aDirs"] = explode("/",$fb_["path"]);
					for( $fb_["k"] = 0 ; $fb_["k"] < count($fb_["aDirs"]) ; $fb_["k"]++ ) {
						if( $fb_["aDirs"][$fb_["k"]] == ".." ) {
							array_unshift($fb_["rootpath"],array_pop($fb_["rootdir"]));
						} elseif( strlen($fb_["aDirs"][$fb_["k"]]) > 0 ) {
							array_unshift($fb_["rootpath"],"..");
						}
					}
					$fb_["rootpath"] = implode("/",$fb_["rootpath"]);
					if ( strlen($fb_["rootpath"]) > 0 ) {
						$fb_["rootpath"] .= "/";
					}
					$fb_["application"]["fusebox"]["plugins"][$fb_["name"]][$fb_["phase"]]["path"] = $fb_["path"];
					$fb_["application"]["fusebox"]["plugins"][$fb_["name"]][$fb_["phase"]]["template"] = $fb_["template"];
					$fb_["application"]["fusebox"]["plugins"][$fb_["name"]][$fb_["phase"]]["rootpath"] = $fb_["rootpath"];
					
					$fb_["application"]["fusebox"]["pluginphases"][$fb_["phase"]][] = array();
					$thisphase = count($fb_["application"]["fusebox"]["pluginphases"][$fb_["phase"]]) - 1;
					$fb_["application"]["fusebox"]["pluginphases"][$fb_["phase"]][$thisphase]["name"] = $fb_["name"];
					$fb_["application"]["fusebox"]["pluginphases"][$fb_["phase"]][$thisphase]["path"] = $fb_["path"];
					$fb_["application"]["fusebox"]["pluginphases"][$fb_["phase"]][$thisphase]["template"] = $fb_["template"];
					$fb_["application"]["fusebox"]["pluginphases"][$fb_["phase"]][$thisphase]["rootpath"] = $fb_["rootpath"];
					$fb_["application"]["fusebox"]["pluginphases"][$fb_["phase"]][$thisphase]["parameters"] = $fb_["j"]["xmlChildren"];
				}
			}
		}
		
		// delete the raw fusebox xml, since it's no longer needed and adds file size
		unset($fb_["application"]["fusebox"]["xml"]);
		
		$fb_["application"]["fusebox"]["dateLastLoaded"] = $fb_["timestamp"][0];
		$fb_["application"]["fusebox"]["isFullyLoaded"] = true;
		
		// now, finally, copy the entire fb_.application.fusebox structure to the application.fusebox structure...
		$application = (isset($application) && is_array($application)) ? array_merge($application,$fb_["application"]) : $fb_["application"];
		
		// ... and save the application data to disk
		if ( !isset($FUSEBOX_APPLICATION_NAME) ) $FUSEBOX_APPLICATION_NAME = "cacheddata";
		$fb_["appData"] = var_export($application,true);
		$fa = fopen($application["fusebox"]["approotdirectory"]."parsed/app_".$FUSEBOX_APPLICATION_NAME.".php","w");
		if(!flock($fa,LOCK_EX)){
			die("Could not get exclusive lock to application data file");
		}
		$fb_['appDataFile']="<?php\n\$application = {$fb_['appData']};\n?>\n";
		if(!fwrite($fa,$fb_["appDataFile"])){
			die("An Error occured during write of application data file.");
		}
		flock($fa,LOCK_UN);
		fclose($fa);
	}
	
	flock($fp,LOCK_UN);
	
	fclose($fp);
}

?>