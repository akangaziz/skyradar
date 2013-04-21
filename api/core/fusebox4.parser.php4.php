<?php
// fusebox41.parser.php4.php

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

// which version of the parser is this?
$myFusebox["version"]["parser"] = "4.1.0";

// prepare to create the fuseaction file
$fb_["CRLF"] = chr(10);
$fb_["COMMENT_PHP_BEGIN"] = "/" . "* ";
$fb_["COMMENT_PHP_END"] = " *" . "/";	

$fb_["parsedfile"] = "<?php".str_repeat($fb_["CRLF"],2);

// this variable tracks the current level of indentation
$fb_["indentLevel"] = 0;
$fb_["indentBlock"] = chr(9);
// we'll just assume that no one is going to look at a parse file with
// more than 1000 levels of indentation.
$fb_["maxIndentLevel"] = ($application["fusebox"]["parseWithIndentation"] ? 1000 : 0);

// this variable tracks whether error handling is on or not. anything higher than zero will have error handling.
$fb_["errorLevel"] = -1;

// these functions that follow are used to generate the repeating content blocks throughout the parsed file
// developers should feel free to use them to make the creation and reading of their lexicons much easier

if ( !function_exists('fb_appendLine') ) {

	function fb_appendLine( $lineContent ) {
		global $fb_;
		fb_appendIndent();
		fb_appendSegment($lineContent);
		fb_appendNewline();
	}
	
	function fb_appendIndent() {
		global $fb_;
		fb_appendSegment(str_repeat($fb_['indentBlock'], min($fb_['maxIndentLevel'], $fb_['indentLevel'])));
	}
	
	function fb_appendSegment( $segmentContent ) {
		global $fb_;
		$fb_['parsedfile'] .= $segmentContent;
	}
	
	function fb_appendNewline() {
		global $fb_;
		fb_appendSegment($fb_['CRLF']);
	}
	
	function fb_increaseIndent() {
		global $fb_;
		$fb_['indentLevel']++;
	}
	
	function fb_decreaseIndent() {
		global $fb_;
		$fb_['indentLevel']--;
	}

}
// end parser functions


// if any plugins were defined for the processError phase then insert an opening tag for <cftry> here
if ( count($application["fusebox"]["pluginphases"]["processError"]) > 0 ) {
	$fb_["errorLevel"] = 1;
	fb_appendLine('do {');
	fb_increaseIndent();
	fb_appendLine("ini_set('track_errors','1');");
	//$fb_["parsedfile"] .= str_repeat($fb_["indentBlock"], min($fb_["maxIndentLevel"], $fb_["indentLevel"])) . "ini_set('display_errors','0');" . $fb_["CRLF"];
	fb_appendLine("\$php_errormsg = false;");
}
// now parse the Fusebox XML grammar
for ( $fb_["i"] = 0 ; $fb_["i"]  < count($fb_["fuseQ"]) ; $fb_["i"]++ ) {
	if ( __ListLen($fb_["fuseQ"][$fb_["i"]]["xmlName"], '.') > 1) {
		$fb_["lexicon"] = __ListFirst($fb_["fuseQ"][$fb_["i"]]["xmlName"], '.');
		$fb_["lexiconVerb"] = __ListRest($fb_["fuseQ"][$fb_["i"]]["xmlName"], '.');
	} else {
		$fb_["lexiconVerb"] = $fb_["fuseQ"][$fb_["i"]]["xmlName"];
		$fb_["lexicon"] = 'fusebox';
	}
	if ( $application["fusebox"]["parseWithComments"] ) {
		$fb_["parsedComment"] = '';
		$fb_["parsedComment"] .= $fb_["fuseQ"][$fb_["i"]]["circuit"].'.'.$fb_["fuseQ"][$fb_["i"]]["fuseaction"].': ';
		$fb_["parsedComment"] .= '<'.strtolower($fb_["lexicon"]).':'.strtolower($fb_["lexiconVerb"]);
		foreach ( array_keys($fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]) as $fb_["attr"] ) {
			if ( $fb_["attr"] != "circuit" )
				$fb_["parsedComment"] .= ' '.$fb_["attr"].'="'.$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"][$fb_["attr"]].'"';
		}
		$fb_["parsedComment"] .= '>';
		fb_appendLine($fb_["COMMENT_PHP_BEGIN"] . str_pad($fb_["parsedComment"], 75) . $fb_["COMMENT_PHP_END"]);
	}

	switch ( $fb_["fuseQ"][$fb_["i"]]["xmlName"] ) {

		case "set" :
		case "xfa" :
			// if no "name" attribute is passed in then set a throw-away variable in the "fb_" structure as the "name" attribute
			// note: this effectively converts an <xfa> to a <set>
			if ( !array_key_exists("name",$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]) || strlen($fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["name"]) == 0 ) {
				$fb_["fuseQ"][$fb_["i"]]["xmlName"] = "set";
				$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["name"] = "fb_[\"".uniqid("")."\"]";
			}
		  	// bit of massaging if this is the <xfa> verb
		  	if ( $fb_["fuseQ"][$fb_["i"]]["xmlName"] == "xfa" ) {
				//prepend "xfa." to the value of the "name" attribute
				$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["name"] = "XFA[\"" . $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["name"] . "\"]";
				//assume no circuit specified means the current circuit
				if ( __ListLen($fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["value"], ".") <= 1 ) {
					$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["value"] = $fb_["fuseQ"][$fb_["i"]]["circuit"] . "." . $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["value"];
				}
			}
			// if the attribute 'overwrite' is FALSE then treat this like a PARAM
			if ( array_key_exists("overwrite",$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]) && !$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["overwrite"] ) {
				fb_appendLine("if ( !isset($" . $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["name"] . ") ) {");
				fb_increaseIndent();
			}
			// if the 'name' attribute has any dollarsigns ( that is, chr(36) )signs in it, we'll need curly braces to evaluate the dynamic variable name
			fb_appendIndent();
			fb_appendSegment("\$");
			if ( $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["name"]{0} == chr(36) ) {
				fb_appendSegment("{".$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["name"]."}");	  
			}
			else {
				fb_appendSegment($fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["name"]);
			}
			fb_appendSegment(" = ");	 
			// if the attributes 'evaluate' is TRUE the use the evaluate function
			if ( array_key_exists("evaluate",$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]) && $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["evaluate"] ) {
				fb_appendSegment("eval(\"" . $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["value"] . "\");");
			}
			else {
				fb_appendSegment("\"" . $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["value"] . "\";");
			}
			fb_appendNewLine();
			// if the attribute 'overwrite' is FALSE then treat this like a PARAM
			if ( array_key_exists("overwrite",$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]) && !$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["overwrite"] ) {
				fb_decreaseIndent();
				fb_appendLine("}");
			}
			break;
		
		case "invoke" :
			// if we are invokeing a class not an object we may need the class file.
			if (array_key_exists("class", $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"])) {
				// include the class file
				fb_appendLine('include_once($fb_["application"]["WebRootToAppRootPath"]."' . $application["fusebox"]["classes"][$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["class"]]["classpath"] . '"); ');
			}
			// if returning a value and the attribute 'overwrite' is FALSE then dont overwrite the orginal
			if (array_key_exists("returnvariable", $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]) && strlen($fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["returnvariable"]) && array_key_exists("overwrite", $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]) && (!$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["overwrite"])) {
				fb_appendLine("if ( !isset(\$" . $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["returnvariable"] . ") ) {");
				fb_increaseIndent();
			}
			// if the 'returnvariable' attribute has any dollarsigns ( that is, chr(36) )signs in it, we'll need curly braces to evaluate the dynamic variable name
			fb_appendIndent();
			fb_appendSegment("\$");
			if (array_key_exists("returnvariable", $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]) && strlen($fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["returnvariable"])) {
				if ( $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["returnvariable"]{0} == chr(36) ) {
					fb_appendSegment("{".$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["returnvariable"]."}");	  
				} else {
					fb_appendSegment($fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["returnvariable"]);
				}
				fb_appendSegment(' = $'); 
			}
			
			if (array_key_exists("object", $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"])) {
				fb_appendSegment($fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["object"] . '->' . $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["methodcall"] . ';');
				fb_appendNewLine();
 			}
			// TODO Need to throw an error here if they didn't pass in a return variable;
			else if (array_key_exists("class", $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"])) {
				fb_appendSegment("new " . $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["class"]);
				if (array_key_exists("arguments", $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]) && strlen($fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["arguments"])) {
					fb_appendSegment('(' . $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["arguments"] . ');');
					fb_appendNewLine();
				} else {
					fb_appendSegment(';');
					fb_appendNewLine();
				}
				
				// if the 'returnvariable' attribute has any dollarsigns ( that is, chr(36) )signs in it, we'll need curly braces to evaluate the dynamic variable name
				fb_appendIndent();
				fb_appendSegment("\$");
				if (array_key_exists("returnvariable", $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]) && strlen($fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["returnvariable"])) {
					if ( $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["returnvariable"]{0} == chr(36) ) {
						fb_appendSegment("{".$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["returnvariable"]."}");	  
					} else {
						fb_appendSegment($fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["returnvariable"]);
					} 
				}
				fb_appendSegment($fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["object"] . '->' . $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["methodcall"] . ';');
				fb_appendNewLine();
				
			}
			/*
			else if (StructKeyExists(fb_.fuseQ[fb_.i].xmlAttributes, "webservice")) {
				fb_.parsedfile = fb_.parsedfile & fb_.fuseQ[fb_.i].xmlAttributes.webservice & '.' & fb_.fuseQ[fb_.i].xmlAttributes.methodcall & ' />';
			}
			*/
			
			// if returning a value and the attribute 'overwrite' is FALSE and the 
			if (array_key_exists("returnvariable", $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]) && strlen($fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["returnvariable"]) && array_key_exists("overwrite", $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]) && (!$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["overwrite"])) {
				fb_decreaseIndent();
				fb_appendLine('}');
			}
			break;
			
			
			// array_key_exists("", $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"])
			
		case "instantiate" :
			// include the class file
			fb_appendLine('include_once($application["fusebox"]["WebRootToAppRootPath"]."' . $application["fusebox"]["classes"][$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["class"]]["classpath"] . '");');
			
			// give empty value for arguments if not specified
			if (!array_key_exists("arguments", $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]))
			{
				$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["arguments"] = "";
			}
			
			// if returning a value and the attribute 'overwrite' is FALSE then treat this like a CFPARAM
			if (array_key_exists("overwrite", $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]) && !$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["overwrite"])
			{
				fb_appendLine("if ( !isset(\$" . $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["object"] . ") ) {");
				fb_increaseIndent();
			}
			
			// if the 'object' attribute has any dollarsigns ( that is, chr(36) )signs in it, we'll need curly braces to evaluate the dynamic variable name
			fb_appendIndent();
			fb_appendSegment("\$");
			if ( $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["object"]{0} == chr(36) )
			{
				fb_appendSegment("{".$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["object"]."}");
			}
			else 
			{
				fb_appendSegment($fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["object"]);
			}
			fb_appendSegment(' = ');
			
			if (array_key_exists("class", $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]) && strlen($fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["class"]))
			{
				// create an instance of the class
				fb_appendSegment("new " . $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["class"]);
				
				if (array_key_exists("arguments", $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]) && strlen($fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["arguments"]))
				{
					// We may want to add single quotes around their arguments but I wasn't sure if that was a good idea
					fb_appendSegment('(' . $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["arguments"] . ');');
					fb_appendNewLine();
				}
				else
				{
					fb_appendSegment(';');
					fb_appendNewLine();
				}
				
			}
			/*
			else if ( StructKeyExists(fb_.fuseQ[fb_.i].xmlAttributes, "webservice") AND Len(fb_.fuseQ[fb_.i].xmlAttributes.webservice) ) {
				// else creating a webservice
				fb_.parsedfile = fb_.parsedfile &  "createObject('webservice', '" & fb_.fuseQ[fb_.i].xmlAttributes.webservice & "')";
			}
			*/
			
			// if returning a value and the attribute 'overwrite' is FALSE then treat this like a CFPARAM
			if (array_key_exists("overwrite", $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]) && !$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["overwrite"])
			{
				fb_decreaseIndent();
				fb_appendLine('}');
			}
			break;
		
		
		case "include" :
			$fb_["template"] = $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["template"];
			$fb_["templateDelimiter"] = __ListLast($fb_["template"], '.');
			
			if ( !(__ListFindNoCase($application["fusebox"]["maskedFileDelimiters"], $fb_["templateDelimiter"], ',') ||
				  __ListFindNoCase($application["fusebox"]["maskedFileDelimiters"], '*', ',')) ) {
				$fb_["template"] .= "." . $application["fusebox"]["scriptFileDelimiter"];
			}
			if ( !array_key_exists("required",$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]) ) {
				$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["required"] = true;
			}
			if ( !array_key_exists("contentvariable",$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"])) {
				$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["contentvariable"] = "";
			}
			if ( (!array_key_exists("overwrite",$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"])) || $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["overwrite"] ) {
				$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["overwrite"] = true;
			}
			if ( (!array_key_exists("append",$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"])) || !$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["overwrite"] ) {
				$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["append"] = false;
			}
			if ( strlen($fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["contentvariable"]) > 0 && !$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["overwrite"] ) {
				fb_appendLine('if ( !isset($'.$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["contentvariable"].') ) {');
				fb_increaseIndent();
			}
			if ( strlen($fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["contentvariable"]) > 0 && $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["append"] ) {
				fb_appendLine('if ( !isset($'.$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["contentvariable"].') ) $'.$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["contentvariable"].' = "";');
			}
			if ( strlen($fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["contentvariable"]) > 0 ) $fb_["parsedfile"] .= str_repeat($fb_["indentBlock"], min($fb_["maxIndentLevel"], $fb_["indentLevel"])) . 'ob_start();' . $fb_["CRLF"];
			fb_appendLine('if ( file_exists($application["fusebox"]["WebRootToAppRootPath"]."'.$application["fusebox"]["circuits"][$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["circuit"]]["path"] . $fb_["template"].'") ) {');
			fb_increaseIndent();
			fb_appendLine('include($application["fusebox"]["WebRootToAppRootPath"]."'.$application["fusebox"]["circuits"][$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["circuit"]]["path"] . $fb_["template"].'");');
			fb_decreaseIndent();
			fb_appendLine('}');
			if ( $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["required"] ) {
				fb_appendLine(' else {');
				fb_increaseIndent();
				if ( strlen($fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["contentvariable"]) > 0 ) $fb_["parsedfile"] .= str_repeat($fb_["indentBlock"], min($fb_["maxIndentLevel"], $fb_["indentLevel"])) . 'ob_end_clean();' . $fb_["CRLF"];
				fb_appendLine('__cfthrow(array("type"=>"fusebox.missingFuse", "message"=>"missing Fuse", "detail"=>"You tried to include a fuse '.$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["template"].' in circuit '.$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["circuit"].' which does not exist."));');
				fb_decreaseIndent();
				fb_appendLine('}');
			}
			if ( strlen($fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["contentvariable"]) > 0 ) {
				fb_appendIndent();
				fb_appendSegment('$'.$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["contentvariable"].' ');
				if ( $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["append"] ) fb_appendSegment('.');
				fb_appendSegment('= ob_get_contents();');
				fb_appendNewLine();
				fb_appendLine('ob_end_clean();');
			}
			if ( !$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["overwrite"] ) {
				fb_decreaseIndent();
				fb_appendLine('}');
			}
			break;

		case "plugin" :
			$fb_["template"] = $fb_["fuseQ"][$fb_["i"]]["plugin"]["template"];
			$fb_["templateDelimiter"] = __ListLast($fb_["template"], '.');
			if ( !(__ListFindNoCase($application["fusebox"]["maskedFileDelimiters"], $fb_["templateDelimiter"], ',') ||
					  __ListFindNoCase($application["fusebox"]["maskedFileDelimiters"], '*', ',')) ) {
			  $fb_["template"] .= "." . $application["fusebox"]["scriptFileDelimiter"];
			}
			fb_appendLine("\$myFusebox['thisPlugin']  = '" . $fb_["fuseQ"][$fb_["i"]]["plugin"]["name"] . "';");
			fb_appendLine("\$myFusebox['thisPhase']  = '" . $fb_["fuseQ"][$fb_["i"]]["phase"] . "';");
			fb_appendLine('include($application["fusebox"]["WebRootToAppRootPath"]."' . str_replace($application["fusebox"]["osdelimiter"],"/",$fb_["fuseQ"][$fb_["i"]]["plugin"]["path"]) . $fb_["template"] . '");');
			break;

		case "beginCatch" :
			$fb_["errorLevel"]--;
			if ( $fb_["indentLevel"] > 0 ) fb_decreaseIndent();
			fb_appendLine("} while ( false );");
			fb_appendLine("if ( \$php_errormsg ) {");
			fb_increaseIndent();
			break;
		
		case "endCatch" :
			fb_decreaseIndent();
			fb_appendLine("}");
			break;
		
		case "errorHandler" :
		case "exceptionHandler" :
			$fb_["template"] = $fb_["fuseQ"][$fb_["i"]]["plugin"]["template"];
			$fb_["templateDelimiter"] = __ListLast($fb_["template"], '.');
			if ( !(__ListFindNoCase($application["fusebox"]["maskedFileDelimiters"], $fb_["templateDelimiter"], ',') ||
					  __ListFindNoCase($application["fusebox"]["maskedFileDelimiters"], '*', ',')) ) {
			  $fb_["template"] .= "." . $application["fusebox"]["scriptFileDelimiter"];
			}
			$fb_["handlerfile"] = $application["fusebox"]["rootdirectory"] . $application["fusebox"]["osdelimiter"] . $fb_["fuseQ"][$fb_["i"]]["plugin"]["path"] . $fb_["template"];
			$fp = fopen($fb_["handlerfile"],"r");
			$fb_["handlervariable"] = fread($fp, filesize($fb_["handlerfile"]));
			fclose($fp);
	  		$fb_["handlervariable"] = str_replace($fb_["CRLF"],$fb_["CRLF"].str_repeat($fb_["indentBlock"], min($fb_["maxIndentLevel"], $fb_["indentLevel"])),$fb_["handlervariable"]);
			fb_appendLine($fb_["handlervariable"]);
			break;
		
		case "beginExceptionHandler" :
			$fb_["errorLevel"]++;
			if ( $fb_["errorLevel"] == 0 ) $fb_["errorLevel"]++;
			fb_appendLine("do {");
			fb_increaseIndent();
			fb_appendLine("ini_set('track_errors','1');");
			//$fb_["parsedfile"] .= str_repeat($fb_["indentBlock"], min($fb_["maxIndentLevel"], $fb_["indentLevel"])) . "ini_set('display_errors','0');" . $fb_["CRLF"];
			fb_appendLine('$php_errormsg = false;');
			break;
		
		case "endExceptionHandler" :
			$fb_["errorLevel"]--;
			fb_appendLine("// end try");
			break;
		
		case "conditional" :
			if ( array_key_exists("mode",$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]) ) {
				if ( strtolower($fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["mode"]) == "begin" ) {
					fb_appendLine("if ( " . $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["condition"] . " ) {");
					fb_increaseIndent();
				} else if ( strtolower($fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["mode"]) == "else") {
					fb_decreaseIndent();
					fb_appendLine("} else {");
					fb_increaseIndent();
				} else if ( strtolower($fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["mode"]) == "end" ) {
					fb_decreaseIndent();
					fb_appendLine("}");
				}
			}
			break;
		
		case "loop" :
			if ( array_key_exists("mode",$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]) ) {
				if ( strtolower($fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["mode"]) == "begin" ) {
					if ( array_key_exists("condition",$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]) && strlen($fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["condition"]) > 0 ) {
						fb_appendLine("while ( ".$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["condition"] ." ) {");
						fb_increaseIndent();
					} else if ( array_key_exists("query",$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]) && strlen($fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["query"]) > 0 ) {
						$fb_["qryname"] = "fb_[\"".uniqid("")."\"]";
						fb_appendLine('$fb_["dbConn"] = "'.$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["query"].'";');
						fb_appendLine('$fb_["connectiontest"] = false;');
						fb_appendLine('if ( is_object($$fb_["dbConn"]) && method_exists($$fb_["dbConn"],"fetchRow") ) {');
						fb_increaseIndent();
						fb_appendLine('$fb_["connectiontest"] = "return \$".$fb_["dbConn"]."->fetchRow(DB_FETCHMODE_ASSOC);";');
						fb_decreaseIndent();
						fb_appendLine('} elseif ( is_resource($$fb_["dbConn"]) ) {');
						fb_increaseIndent();
						fb_appendLine('$fb_["connectiontest"] = "return mysql_fetch_assoc(\$".$fb_["dbConn"].");";');
						fb_decreaseIndent();
						fb_appendLine('}');
						fb_appendLine('while ( $'.$fb_["qryname"].' = eval($fb_["connectiontest"]) ) {');
						fb_increaseIndent();
						fb_appendLine('foreach ( array_keys($'.$fb_["qryname"].') as $fb_["thisColumn"] ) {');
						fb_increaseIndent();
						fb_appendLine('$$fb_["thisColumn"] = $'.$fb_["qryname"].'[$fb_["thisColumn"]];');
						fb_decreaseIndent();
						fb_appendLine('}');
					} else if ( array_key_exists('from', $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]) && array_key_exists('to', $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]) && array_key_exists('index', $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]) ) {
						if ( !array_key_exists("step",$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]) || !is_numeric($fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["step"]) ) {
							$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["step"] = 1;
							$fb_["operator"] = "<=";
						} else {
							$fb_["operator"] = ( $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["step"] > 0 ) ? "<=" : ">=";
						}
						if ( $fb_["operator"] == "<=" && substr($fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["step"],0,1) != "+" ) $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["step"] = "+" . $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["step"];
						fb_appendLine('for ( $'.$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["index"].'='.$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["from"].' ; $'.$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["index"].$fb_["operator"].$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["to"].' ; $'.$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["index"].'=$'.$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["index"].$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["step"].' ) {');
						fb_increaseIndent();
					} else {
						fb_appendLine("while ( false ) {");
						fb_increaseIndent();
					}
				} else if ( strtolower($fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["mode"]) == "end" ) {
					fb_decreaseIndent();
					fb_appendLine("}");
				}
			}
			break;

		case "contentvariable" :
			if ( array_key_exists("mode",$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]) ) {
				if ( strtolower($fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["mode"]) == "begin" ) {
					if ( !$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["overwrite"] ) {
						fb_appendLine("if ( !isset(\$" . $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["contentvariable"] . ") ) {");
						fb_increaseIndent();
					}
					fb_appendLine("ob_start();");
					if ( $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["append"] ) {
						fb_appendLine("if ( !isset(\$" . $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["contentvariable"] . ") ) { \$" . $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["contentvariable"] . " = ''; }");
						fb_appendLine("echo \$".$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["contentvariable"].";");
					}
				} else { //mode=end
					if ( $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["prepend"] ) {
						fb_appendLine("if ( !isset(\$" . $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["contentvariable"] . ") ) { \$" . $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["contentvariable"] . " = ''; }");
						fb_appendLine("echo \$".$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["contentvariable"].";");
					}
					fb_appendLine("\$".$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["contentvariable"]." = ob_get_contents();");
					fb_appendLine("ob_end_clean();");
					if ( !$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["overwrite"] ) {
						fb_decreaseIndent();
						fb_appendLine("}");
					}
				}
			}
			break;
		
		case "relocate" :
			if ( array_key_exists("addtoken",$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]) && $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["addtoken"] ) {
				$fb_["addtoken"] = "1";
			} else {
				$fb_["addtoken"] = "0";
			}
			fb_appendLine("Location(\"" . $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["url"] . "\"," . $fb_["addtoken"] . ");");
			break;
		
		case "assert" :
			if ( !array_key_exists("message", $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]) ) {
				$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["message"] = "The assertion failed.";
			}
			fb_appendIndent();
			fb_appendSegment('/'.'* <assertion>');
			
			fb_appendSegment('if ( !(' . $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["expression"] . ') ) {'); 
			/*
			fb_.indentLevel = fb_.indentLevel + 1;
			fb_.parsedfile = fb_.parsedfile & fb_.CRLF & repeatString(fb_.indentBlock, min(fb_.maxIndentLevel, fb_.indentLevel)) & '<!-'&'-- assertion is TRUE; do nothing --'&'->';
			fb_.indentLevel = fb_.indentLevel - 1;
			fb_.parsedfile = fb_.parsedfile & fb_.CRLF & repeatString(fb_.indentBlock, min(fb_.maxIndentLevel, fb_.indentLevel)) & '<cfelse>'; 
			fb_.indentLevel = fb_.indentLevel + 1;
			*/
			fb_appendSegment('__cfthrow(array("type"=>"fusebox.failedAssertion", "message"=>"'.$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["message"].'", "detail"=>"The assertion, '.str_replace("\$","\\\$",$fb_["fuseQ"][$fb_["i"]]["xmlAttributes"]["expression"]).', failed at run-time in fuseaction '.$fb_["fuseQ"][$fb_["i"]]["circuit"].'.'.$fb_["fuseQ"][$fb_["i"]]["fuseaction"].'."));'); 
			/*
			fb_.indentLevel = fb_.indentLevel - 1;
			*/
			fb_appendSegment('}');
			
			fb_appendSegment('</assertion> *'.'/');
			fb_appendNewLine(); 
			$fb_["hasAssertions"] = true;
			break;
		
		default :
			if ( $application["fusebox"]["allowLexicon"] && __ListLen($fb_["fuseQ"][$fb_["i"]]["xmlName"], '.') > 1 ) {
				$fb_["lexicon"] = __ListFirst($fb_["fuseQ"][$fb_["i"]]["xmlName"], '.');
				$fb_["lexiconVerb"] = __ListRest($fb_["fuseQ"][$fb_["i"]]["xmlName"], '.');
				
				$fb_["verbInfo"] = array();
				$fb_["verbInfo"]["lexicon"] = $fb_["lexicon"];
				$fb_["verbInfo"]["verb"] = $fb_["lexiconVerb"];
				$fb_["verbInfo"]["attributes"] = $fb_["fuseQ"][$fb_["i"]]["xmlAttributes"];
				if ( array_key_exists($fb_["lexicon"], $application["fusebox"]["lexicons"]) ) {
					if ( file_exists($application["fusebox"]["approotdirectory"].$application["fusebox"]["lexiconPath"].$application["fusebox"]["lexicons"][$fb_["lexicon"]]["path"].$fb_["lexiconVerb"].'.'.$application["fusebox"]["scriptFileDelimiter"]) ) {
						include(ereg_replace("[\\/]+","/",$application["fusebox"]["WebRootToAppRootPath"].$application["fusebox"]["lexiconPath"].$application["fusebox"]["lexicons"][$fb_["lexicon"]]["path"].$fb_["lexiconVerb"].'.'.$application["fusebox"]["scriptFileDelimiter"]));
					} else {
						if ( !$application["fusebox"]["ignoreBadGrammar"] ) {
							__cfthrow(array(
								"type"=>"fusebox.badGrammar",
								"message"=>"Bad Grammar verb in circuit file",
								"detail"=>"The implementation file for the '".$fb_["lexiconVerb"]."' verb from the '".$fb_["lexicon"]."' custom lexicon could not be found.  It is used in the '".$fb_["fuseQ"][$fb_["i"]]["circuit"]."' circuit."
							));
						} else {
							// do nothing
						}
					}
				} else if ( !$application["fusebox"]["ignoreBadGrammar"] ) {
					__cfthrow(array(
						"type"=>"fusebox.badGrammar",
						"message"=>"Bad Grammar verb in circuit file",
						"detail"=>"The '".$fb_["lexicon"]."' lexicon is not registered in fusebox.xml, but is used in the '".$fb_["fuseQ"][$fb_["i"]]["circuit"]."' circuit."
					));
				}
			} else {
				if ( $application["fusebox"]["parseWithComments"] ) {
				  fb_appendLine("// generated by fuseQ[".$fb_["i"]."]  UNKNOWN VERB: ".$fb_["fuseQ"][$fb_["i"]]["xmlName"]);
				}
				if ( !$application["fusebox"]["ignoreBadGrammar"] ) {
					die("A bad grammar construct was encountered in the circuit ".$fb_["fuseQ"][$fb_["i"]]["circuit"]." caused by the unknown or misspelled Fusebox grammar verb ".$fb_["fuseQ"][$fb_["i"]]["xmlName"].".");
				}
			}
			break;
	}
	
	
	if ( $fb_["errorLevel"] > 0 ) {
		fb_appendLine("if ( \$php_errormsg ) break;");
	} else if ( $fb_["errorLevel"] == 0 ) {
		fb_appendLine("ini_restore('track_errors');");
		fb_appendLine("ini_restore('display_errors');");
		$fb_["errorLevel"]--;
	}
}

// finished
fb_appendNewLine();
fb_appendSegment("?>");

?>