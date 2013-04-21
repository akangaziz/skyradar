<?php

// fusebox41.transformer.php4.php

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
$myFusebox["version"]["transformer"] = "4.1.0";

// ok ready to start work on the actual fuseaction
if ( !isset($fb_["fuseQ"]) ) {
  $fb_["fuseQ"] = array();
}

// we can do all the plugins in one go here, for simplicity and less duplication
$fb_["xnPlugins"] = array();

foreach ( array_keys($application["fusebox"]["pluginphases"]) as $fb_["phase"] ) {
  
  $fb_["xnPlugins"][$fb_["phase"]] = array();
  if ( ( $fb_["phase"] == "processError" || $fb_["phase"] == "fuseactionException" ) && count($application["fusebox"]["pluginphases"][$fb_["phase"]]) > 0 ) {
  	$fb_["temp"] = array();
	$fb_["temp"]["xmlName"] = "beginCatch";
	$fb_["temp"]["circuit"] = $myFusebox["thisCircuit"];
	$fb_["temp"]["fuseaction"] = $myFusebox["thisFuseaction"];
	$fb_["temp"]["plugin"] = $application["fusebox"]["pluginphases"][$fb_["phase"]][$fb_["i"]];
    $fb_["temp"]["phase"] = $fb_["phase"];
    $fb_["temp"]["xmlAttributes"] = array();
    array_push($fb_["xnPlugins"][$fb_["phase"]], $fb_["temp"]);
  }
  for ( $fb_["i"] = 0 ; $fb_["i"] < count($application["fusebox"]["pluginphases"][$fb_["phase"]]) ; $fb_["i"]++ ) {
    // pass in this Plugin's parameters
    for ( $fb_["j"] = 0 ; $fb_["j"] < count($application["fusebox"]["pluginphases"][$fb_["phase"]][$fb_["i"]]["parameters"]) ; $fb_["j"]++ ) {
      $fb_["name"] = $application["fusebox"]["pluginphases"][$fb_["phase"]][$fb_["i"]]["parameters"][$fb_["j"]]["xmlAttributes"]["name"];
      $fb_["value"] = $application["fusebox"]["pluginphases"][$fb_["phase"]][$fb_["i"]]["parameters"][$fb_["j"]]["xmlAttributes"]["value"];
      $fb_["plugin"] = $application["fusebox"]["pluginphases"][$fb_["phase"]][$fb_["i"]]["name"];
      $fb_["temp"] = array();
      $fb_["temp"]["xmlName"] = "set";
      $fb_["temp"]["circuit"] = "";
      $fb_["temp"]["fuseaction"] = "";
      $fb_["temp"]["phase"] = $fb_["phase"];
      $fb_["temp"]["xmlAttributes"] = array();
      $fb_["temp"]["xmlAttributes"]["name"] = "myFusebox['plugins']['".$fb_["plugin"]."']['parameters']['".$fb_["name"]."']";
      $fb_["temp"]["xmlAttributes"]["value"] = $fb_["value"];
      array_push($fb_["xnPlugins"][$fb_["phase"]],$fb_["temp"]);
    }
    // and the Plugin itself
    $fb_["temp"] = array();
    if( $fb_["phase"] == "processError" ) {
      $fb_["temp"]["xmlName"] = "errorHandler";
    } else if ( $fb_["phase"] == "fuseactionException" ) {
      $fb_["temp"]["xmlName"] = "exceptionHandler";
    } else {
      $fb_["temp"]["xmlName"] = "plugin";
    }
    $fb_["temp"]["circuit"] = $myFusebox["thisCircuit"];
	$fb_["temp"]["fuseaction"] = $myFusebox["thisFuseaction"];
	$fb_["temp"]["plugin"] = $application["fusebox"]["pluginphases"][$fb_["phase"]][$fb_["i"]];
    $fb_["temp"]["phase"] = $fb_["phase"];
    $fb_["temp"]["xmlAttributes"] = array();
    $fb_["temp"]["xmlAttributes"]["name"] = $application["fusebox"]["pluginphases"][$fb_["phase"]][$fb_["i"]]["name"];
    array_push($fb_["xnPlugins"][$fb_["phase"]], $fb_["temp"]);
  }
  if ( ( $fb_["phase"] == "processError" || $fb_["phase"] == "fuseactionException" ) && count($application["fusebox"]["pluginphases"][$fb_["phase"]]) > 0 ) {
  	$fb_["temp"] = array();
	$fb_["temp"]["xmlName"] = "endCatch";
	$fb_["temp"]["circuit"] = $myFusebox["thisCircuit"];
	$fb_["temp"]["fuseaction"] = $myFusebox["thisFuseaction"];
	$fb_["temp"]["plugin"] = $application["fusebox"]["pluginphases"][$fb_["phase"]][$fb_["i"]];
    $fb_["temp"]["phase"] = $fb_["phase"];
    $fb_["temp"]["xmlAttributes"] = array();
    array_push($fb_["xnPlugins"][$fb_["phase"]], $fb_["temp"]);
  }

}


/* let's get started working on the fuseaction */
/* first add in all the preprocess fuseactions */
$fb_["phase"] = "preprocessFuseactions";

$fb_["xnPreprocessFA"] = $application["fusebox"]["globalfuseactions"]["preprocess"]["xml"]["xmlChildren"];
for ( $fb_["i"] = 0 ; $fb_["i"] < count($fb_["xnPreprocessFA"]) ; $fb_["i"]++ ) {
  // only calls to fuseactions via a <fuseaction action=""/>  (formerly only a <do> which has been deprecated!) are allowed here and it must have a fully qualified fuseaction
  if ($fb_["xnPreprocessFA"][$fb_["i"]]["xmlName"] == "fuseaction" || $fb_["xnPreprocessFA"][$fb_["i"]]["xmlName"] == "do") {
    if ( array_key_exists("action",$fb_["xnPreprocessFA"][$fb_["i"]]["xmlAttributes"]) && __ListLen($fb_["xnPreprocessFA"][$fb_["i"]]["xmlAttributes"]["action"], '.') >= 2 ) {
      $fb_["temp"] = array();
      $fb_["temp"]["xmlName"] = "do";
      $fb_["temp"]["circuit"] = __ListFirst($fb_["xnPreprocessFA"][$fb_["i"]]["xmlAttributes"]["action"], '.');
      $fb_["temp"]["fuseaction"] = __ListLast($fb_["xnPreprocessFA"][$fb_["i"]]["xmlAttributes"]["action"], '.');
      $fb_["temp"]["phase"] = $fb_["phase"];
      $fb_["temp"]["xmlAttributes"] = array();
      foreach ( array_keys($fb_["xnPreprocessFA"][$fb_["i"]]["xmlAttributes"]) as $fb_["anItem"] ) {
        $fb_["temp"]["xmlAttributes"][$fb_["anItem"]] = $fb_["xnPreprocessFA"][$fb_["i"]]["xmlAttributes"][$fb_["anItem"]];
    }
    array_push($fb_["fuseQ"], $fb_["temp"]);
  }
  }
}

/* now add the actual fuseaction which is the target of this page request */
$fb_["phase"] = "requestedFuseaction";
$fb_["temp"] = array();
$fb_["temp"]["xmlName"] = "do";
$fb_["temp"]["circuit"] = $myFusebox["thisCircuit"];
$fb_["temp"]["fuseaction"] = $myFusebox["thisFuseaction"];
$fb_["temp"]["phase"] = $fb_["phase"];
$fb_["temp"]["xmlAttributes"] = array();
$fb_["temp"]["xmlAttributes"]["action"] = $myFusebox["originalCircuit"].".".$myFusebox["originalFuseaction"];
array_push($fb_["fuseQ"], $fb_["temp"]);

/* finally add in all the postprocess fuseactions */
$fb_["phase"] = "postprocessFuseactions";
$fb_["xnPostprocessFA"] = $application["fusebox"]["globalfuseactions"]["postprocess"]["xml"]["xmlChildren"];

for ( $fb_["i"] = 0 ; $fb_["i"] < count($fb_["xnPostprocessFA"]) ; $fb_["i"]++ ) {
  // only calls to fuseactions via a <do> are allowed here and it must have a fully qualified fuseaction
  if ( strtolower($fb_["xnPostprocessFA"][$fb_["i"]]["xmlName"]) == "do" ) {
  
  if ( array_key_exists("action",$fb_["xnPostprocessFA"][$fb_["i"]]["xmlAttributes"]) && __ListLen($fb_["xnPostprocessFA"][$fb_["i"]]["xmlAttributes"]["action"], '.') >= 2 ) {
    $fb_["temp"] = array();
    $fb_["temp"]["xmlName"] = "do";
    $fb_["temp"]["circuit"] = __ListFirst($fb_["xnPostprocessFA"][$fb_["i"]]["xmlAttributes"]["action"], '.');
    $fb_["temp"]["fuseaction"] = __ListLast($fb_["xnPostprocessFA"][$fb_["i"]]["xmlAttributes"]["action"], '.');
    $fb_["temp"]["phase"] = $fb_["phase"];
    $fb_["temp"]["xmlAttributes"] = array();
    foreach ( array_keys($fb_["xnPostprocessFA"][$fb_["i"]]["xmlAttributes"]) as $fb_["anItem"] ) {
    $fb_["temp"]["xmlAttributes"][$fb_["anItem"]] = $fb_["xnPostprocessFA"][$fb_["i"]]["xmlAttributes"][$fb_["anItem"]];
    }
    array_push($fb_["fuseQ"], $fb_["temp"]);
  }
  }
}

/* be sure to reset the myFusebox.thisCircuit to that of the originalCircuit */
$fb_["temp"] = array();
$fb_["temp"]["xmlName"] = "set";
$fb_["temp"]["circuit"] = $myFusebox["originalCircuit"];
$fb_["temp"]["fuseaction"] = $myFusebox["originalFuseaction"];
$fb_["temp"]["phase"] = $fb_["phase"];
$fb_["temp"]["xmlAttributes"] = array();
$fb_["temp"]["xmlAttributes"]["name"] = "myFusebox['thisCircuit']";
$fb_["temp"]["xmlAttributes"]["value"] = $myFusebox["originalCircuit"];
array_push($fb_["fuseQ"], $fb_["temp"]);

/* be sure to reset the myFusebox.thisFuseaction to that of the originalFuseaction */
$fb_["temp"] = array();
$fb_["temp"]["xmlName"] = "set";
$fb_["temp"]["circuit"] = $myFusebox["originalCircuit"];
$fb_["temp"]["fuseaction"] = $myFusebox["originalFuseaction"];
$fb_["temp"]["phase"] = $fb_["phase"];
$fb_["temp"]["xmlAttributes"] = array();
$fb_["temp"]["xmlAttributes"]["name"] = "myFusebox['thisFuseaction']";
$fb_["temp"]["xmlAttributes"]["value"] = $myFusebox["originalFuseaction"];
array_push($fb_["fuseQ"], $fb_["temp"]);

//now add in the plugins in the proper phases and order
$fb_["fuseQ"] = array_merge($fb_["xnPlugins"]["preProcess"],$fb_["fuseQ"],$fb_["xnPlugins"]["postProcess"],$fb_["xnPlugins"]["processError"]);


// now loop thru fb_.fuseQ and see if there are any <do>s to process or other special transformations such as <if> or <loop>  
$fb_["doMore"] = true;
while ( $fb_["doMore"] ) {
  for ( $fb_["pointer"] = 0 ; $fb_["pointer"] < count($fb_["fuseQ"]) ; $fb_["pointer"]++ ) {
    
	$fb_["phase"] = "requestedFuseaction";
    
    if ( !isset($fb_["fuseQ"][$fb_["pointer"]]["circuit"]) ) $fb_["fuseQ"][$fb_["pointer"]]["circuit"] = $myFusebox["thisCircuit"];
    if ( !isset($fb_["fuseQ"][$fb_["pointer"]]["fuseaction"]) ) $fb_["fuseQ"][$fb_["pointer"]]["fuseaction"] = $myFusebox["thisFuseaction"];
    if ( !isset($fb_["fuseQ"][$fb_["pointer"]]["phase"]) ) $fb_["fuseQ"][$fb_["pointer"]]["phase"] = $fb_["phase"];
    
  }
  for ( $fb_["pointer"] = 0 ; $fb_["pointer"] < count($fb_["fuseQ"]) ; $fb_["pointer"]++ ) {
  
    $fb_["doMore"] = false;
    
	switch( strtolower($fb_["fuseQ"][$fb_["pointer"]]["xmlName"]) ) {
    
      case "do" :
        $fb_["doMore"] = true;
        $fb_["aFuseaction"] = $fb_["fuseQ"][$fb_["pointer"]]["xmlAttributes"]["action"];
        if ( __ListLen($fb_["aFuseaction"], '.') == 1 ) {
          // assume no circuit means current circuit
          $myFusebox["thisCircuit"] = $fb_["fuseQ"][$fb_["pointer"]]["circuit"];
          $myFusebox["thisFuseaction"] = __ListFirst($fb_["aFuseaction"], '.');
        } else {
          // parse new FA
          $myFusebox["thisCircuit"]  = __ListFirst($fb_["aFuseaction"], '.');
          $myFusebox["thisFuseaction"] = __ListLast($fb_["aFuseaction"], '.');
        }
          // catch any last minute problems with non-existant circuits  
        if ( !isset($application["fusebox"]["circuits"][$myFusebox["thisCircuit"]]) ) {
          die("You specified a Circuit of ".$myFusebox["thisCircuit"]." which is not defined.");
        }
        // catch any last minute problems with non-existant or overloaded fuseactions  
        //<cfset fb_.xnAccess = xmlSearch(application.fusebox.circuits[myFusebox.thisCircuit].xml, "//circuit/fuseaction[@name='#fuseaction#']")>
        $fb_["fcount"] = 0;
        foreach ( array_keys($application["fusebox"]["circuits"][$myFusebox["thisCircuit"]]["fuseactions"]) as $fb_["tempfa"] ) {
          if ( $fb_["tempfa"] == $myFusebox["thisFuseaction"] ) {
            $fb_["fcount"]++;
          }
        }
        if ( $fb_["fcount"] == 0 ) {
          die("You referenced a fuseaction, ".$myFusebox["thisFuseaction"].", which does not exist in the circuit ".$myFusebox["thisCircuit"].".");
		}
        if ( $fb_["fcount"] > 1 ) {
          die("You referenced a fuseaction, ".$myFusebox["thisFuseaction"].", which has been defined multiple times in circuit ".$myFusebox["thisCircuit"].". Fusebox does not allow overloaded methods.");
        }
  
        // check this fuseaction's access permissions  
        $fb_["access"] = $application["fusebox"]["circuits"][$myFusebox["thisCircuit"]]["fuseactions"][$myFusebox["thisFuseaction"]]["access"];
        if ( strtolower($fb_["access"]) == "private" && $myFusebox["thisCircuit"] != $fb_["fuseQ"][$fb_["pointer"]]["circuit"] ) {
          __cfthrow(array(
			  "type"=>"fusebox.invalidAccessModifier",
			  "message"=>"invalid access modifier",
			  "detail"=>"The fuseaction '".$myFusebox["thisCircuit"].".".$myFusebox["thisFuseaction"]."' has an access modifier of private and can only be called from within its own circuit. Use an access modifier of internal or public to make it available outside its immediate circuit."
          ));
        }
        // set the value of myFusebox.thisCircuit
        // (both here and at the end of the parsing of this <do> so that we always return to the right value of myFusebox.thisCircuit)
        $fb_["xnInitFA"] = array();
        $fb_["xnCloseFA"] = array();
        
        $fb_["temp"] = array();
        $fb_["temp"]["xmlName"] = "set";
        $fb_["temp"]["circuit"] = $myFusebox["thisCircuit"];
        $fb_["temp"]["fuseaction"] = $myFusebox["thisFuseaction"];
        $fb_["temp"]["phase"] = $fb_["phase"];
        $fb_["temp"]["xmlAttributes"] = array();
        $fb_["temp"]["xmlAttributes"]["name"] = "myFusebox['thisCircuit']";
        $fb_["temp"]["xmlAttributes"]["value"] = $myFusebox["thisCircuit"];
        array_push($fb_["xnInitFA"], $fb_["temp"]);
        $fb_["temp"]["circuit"] = $fb_["fuseQ"][$fb_["pointer"]]["circuit"];
		$fb_["temp"]["fuseaction"] = $fb_["fuseQ"][$fb_["pointer"]]["fuseaction"];
		$fb_["temp"]["xmlAttributes"]["value"] = $fb_["fuseQ"][$fb_["pointer"]]["circuit"];
		array_push($fb_["xnCloseFA"], $fb_["temp"]);
        
        // set the value of myFusebox.thisFuseaction
        // (both here and at the end of the parsing of this <do> so that we always return to the right value of myFusebox.thisFuseaction)
        $fb_["temp"] = array();
        $fb_["temp"]["xmlName"] = "set";
        $fb_["temp"]["circuit"] = $myFusebox["thisCircuit"];
        $fb_["temp"]["fuseaction"] = $myFusebox["thisFuseaction"];
        $fb_["temp"]["phase"] = $fb_["phase"];
        $fb_["temp"]["xmlAttributes"] = array();
        $fb_["temp"]["xmlAttributes"]["name"] = "myFusebox['thisFuseaction']";
        $fb_["temp"]["xmlAttributes"]["value"] = $myFusebox["thisFuseaction"];
        array_push($fb_["xnInitFA"], $fb_["temp"]);
        $fb_["temp"]["circuit"] = $fb_["fuseQ"][$fb_["pointer"]]["circuit"];
		$fb_["temp"]["fuseaction"] = $fb_["fuseQ"][$fb_["pointer"]]["fuseaction"];
		$fb_["temp"]["xmlAttributes"]["value"] = $fb_["fuseQ"][$fb_["pointer"]]["fuseaction"];
		array_push($fb_["xnCloseFA"], $fb_["temp"]);
        
        // if this fuseaction has an exceptionHandler then insert space-holders for opening and closing <CFTRY></CFTRY> tags
        if ( count($application["fusebox"]["pluginphases"]["fuseactionException"]) > 0 ) {
          $fb_["temp"] = array();
          $fb_["temp"]["xmlName"] = "beginExceptionHandler";
          $fb_["temp"]["circuit"] = $myFusebox["thisCircuit"];
          $fb_["temp"]["fuseaction"] = $myFusebox["thisFuseaction"];
          $fb_["temp"]["phase"] = $fb_["phase"];
          $fb_["temp"]["xmlAttributes"] = array();
          $fb_["temp"]["xmlAttributes"]["name"] = "myFusebox['thisCircuit']";
          $fb_["temp"]["xmlAttributes"]["value"] = $myFusebox["thisCircuit"];
          array_push($fb_["xnInitFA"], $fb_["temp"]);
        }
        
        // determine what this fuseaction's code is meant to do
        // note this means (any preFuseaction fuseactions) + this Fuseaction + (any postFuseaction fuseactions)
        $fb_["CircuitXML"] = $application["fusebox"]["circuits"][$myFusebox["thisCircuit"]]["xml"];
        $fb_["phase"] = "requestedFuseaction";
        
        // first handle any preFuseaction fuseactions
        $fb_["xnPreFA"] = array();
        $fb_["xnAnyPreFA"] = $application["fusebox"]["circuits"][$myFusebox["thisCircuit"]]["prefuseaction"]["xml"];
        
        //  extra xmlChildren check to compensate for CF5 xml structures
        if ( ( array_key_exists( "xmlChildren", $fb_["xnAnyPreFA"] ) && count($fb_["xnAnyPreFA"]["xmlChildren"]) ) || $application["fusebox"]["circuits"][$myFusebox["thisCircuit"]]["prefuseaction"]["callsuper"] ) {
          if ( $application["fusebox"]["circuits"][$myFusebox["thisCircuit"]]["prefuseaction"]["callsuper"] ) {
            // the circuit's super must be called first
            $fb_["xnPreFA"] = array();
            // loop over the circuitTrace for this circuit
            for ( $fb_["k"] = 0 ; $fb_["k"] < count($application["fusebox"]["circuits"][$myFusebox["thisCircuit"]]["circuitTrace"] ) ; $fb_["k"]++ ) {
              $fb_["aCircuit"] = $application["fusebox"]["circuits"][$myFusebox["thisCircuit"]]["circuitTrace"][$fb_["k"]];
              // grab aCircuit's common super code
              $fb_["xnSuperPreFA"] = $application["fusebox"]["circuits"][$fb_["aCircuit"]]["prefuseaction"]["xml"];
              // loop through any prefuseaction tags
              // loop thru each entry from the super and prepend it
              if ( array_key_exists( "xmlChildren", $fb_["xnSuperPreFA"]) ) {
                for ( $fb_["i"] = count($fb_["xnSuperPreFA"]["xmlChildren"])-1 ; $fb_["i"] >= 0 ; $fb_["i"]-- ) {
                  // remember that any <include> needs to know its local circuit as an attribute
                  if ( strtolower($fb_["xnSuperPreFA"]["xmlChildren"][$fb_["i"]]["xmlName"]) == "include" ) {
                    $fb_["xnSuperPreFA"]["xmlChildren"][$fb_["i"]]["xmlAttributes"]["circuit"] = $fb_["aCircuit"];
                  }
                  // some special handling for do's
                  if ( strtolower($fb_["xnSuperPreFA"]["xmlChildren"][$fb_["i"]]["xmlName"]) == "do" && __ListLen($fb_["xnSuperPreFA"]["xmlChildren"][$fb_["i"]]["xmlAttributes"]["action"], "." ) == 1 ) {
                    $fb_["xnSuperPreFA"]["xmlChildren"][$fb_["i"]]["xmlAttributes"]["action"] = $fb_["aCircuit"] . "." . $fb_["xnSuperPreFA"]["xmlChildren"][$fb_["i"]]["xmlAttributes"]["action"];
                  }
                  
                  if ( !isset($fb_["xnSuperPreFA"]["xmlChildren"][$fb_["i"]]["circuit"]) ) $fb_["xnSuperPreFA"]["xmlChildren"][$fb_["i"]]["circuit"] = $fb_["aCircuit"];
                  if ( !isset($fb_["xnSuperPreFA"]["xmlChildren"][$fb_["i"]]["fuseaction"]) ) $fb_["xnSuperPreFA"]["xmlChildren"][$fb_["i"]]["fuseaction"] = $myFusebox["thisFuseaction"];
                  
                  // prepend it to what came from the child circuit
                  array_unshift( $fb_["xnPreFA"], $fb_["xnSuperPreFA"]["xmlChildren"][$fb_["i"]] );
                }
                // see if it calls *its* super; if not, then break out of this loop
                if( !$application["fusebox"]["circuits"][$fb_["aCircuit"]]["prefuseaction"]["callsuper"] ) {
                  break;
                }
              }
              // make sure right value for myFusebox.thisCircuit is set
              $fb_["temp"] = array();
              $fb_["temp"]["xmlName"] = "set";
              $fb_["temp"]["circuit"] = $myFusebox["thisCircuit"];
              $fb_["temp"]["fuseaction"] = $myFusebox["thisFuseaction"];
              $fb_["temp"]["phase"] = $fb_["phase"];
              $fb_["temp"]["xmlAttributes"] = array();
              $fb_["temp"]["xmlAttributes"]["name"] = "myFusebox['thisCircuit']";
              $fb_["temp"]["xmlAttributes"]["value"] = $fb_["aCircuit"];
              array_unshift( $fb_["xnPreFA"], $fb_["temp"] );
            }
            // since prefuseaction calls to super would have overwritten the myFusebox.thisCircuit we need to reset it again
            $fb_["temp"] = array();
            $fb_["temp"]["xmlName"] = "set";
            $fb_["temp"]["circuit"] = $myFusebox["thisCircuit"];
            $fb_["temp"]["fuseaction"] = $myFusebox["thisFuseaction"];
            $fb_["temp"]["phase"] = $fb_["phase"];
            $fb_["temp"]["xmlAttributes"] = array();
            $fb_["temp"]["xmlAttributes"]["name"] = "myFusebox['thisCircuit']";
            $fb_["temp"]["xmlAttributes"]["value"] = $myFusebox["thisCircuit"];
            array_push( $fb_["xnPreFA"], $fb_["temp"] );
            // since prefuseaction calls to super would have overwritten the myFusebox.thisFuseaction we need to reset it again
            $fb_["temp"] = array();
            $fb_["temp"]["xmlName"] = "set";
            $fb_["temp"]["circuit"] = $myFusebox["thisCircuit"];
            $fb_["temp"]["fuseaction"] = $myFusebox["thisFuseaction"];
            $fb_["temp"]["phase"] = $fb_["phase"];
            $fb_["temp"]["xmlAttributes"] = array();
            $fb_["temp"]["xmlAttributes"]["name"] = "myFusebox['thisFuseaction']";
            $fb_["temp"]["xmlAttributes"]["value"] = $myFusebox["thisFuseaction"];
            array_push( $fb_["xnPreFA"], $fb_["temp"] );
          } else {
            $fb_["xnPreFA"] = $application["fusebox"]["circuits"][$myFusebox["thisCircuit"]]["prefuseaction"]["xml"]["xmlChildren"];
            for ( $fb_["j"] = 0 ; $fb_["j"] < count($fb_["xnPreFA"]) ; $fb_["j"]++ ) {
              
    		  if ( !isset($fb_["xnPreFA"][$fb_["j"]]["circuit"]) ) $fb_["xnPreFA"][$fb_["j"]]["circuit"] = $myFusebox["thisCircuit"];
    		  if ( !isset($fb_["xnPreFA"][$fb_["j"]]["fuseaction"]) ) $fb_["xnPreFA"][$fb_["j"]]["fuseaction"] = $myFusebox["thisFuseaction"];
    		  
    		  // remember that any <include> needs to know its local circuit as an attribute
              if ( strtolower($fb_["xnPreFA"][$fb_["j"]]["xmlName"]) == "include" ) {
                $fb_["xnPreFA"][$fb_["j"]]["xmlAttributes"]["circuit"] = $myFusebox["thisCircuit"];
              }
              
              // some special handling for do's
              if ( strtolower($fb_["xnPreFA"][$fb_["j"]]["xmlName"]) == "do" ) {
                if ( __ListLen($fb_["xnPreFA"][$fb_["j"]]["xmlAttributes"]["action"], '.') == 1 ) {
                  // remember that any <do> might have only a fuseaction specified and only imply its local circuit do clarify all <do>s with explicit circuits
                  $fb_["xnPreFA"][$fb_["j"]]["xmlAttributes"]["action"] = $myFusebox["thisCircuit"] . "." . $fb_["xnPreFA"][$fb_["j"]]["xmlAttributes"]["action"];
                }
              }
            }
          }
        }
        
        /* second handle the actual fuseaction */
        $fb_["xnThisFA"] = $application["fusebox"]["circuits"][$myFusebox["thisCircuit"]]["fuseactions"][$myFusebox["thisFuseaction"]]["xml"]["xmlChildren"];
        for ( $fb_["j"] = 0 ; $fb_["j"] < count($fb_["xnThisFA"]) ; $fb_["j"]++ ) {
          
		  if ( !isset($fb_["xnThisFA"][$fb_["j"]]["circuit"]) ) $fb_["xnThisFA"][$fb_["j"]]["circuit"] = $myFusebox["thisCircuit"];
		  if ( !isset($fb_["xnThisFA"][$fb_["j"]]["fuseaction"]) ) $fb_["xnThisFA"][$fb_["j"]]["fuseaction"] = $myFusebox["thisFuseaction"];
		  
		  // remember that any <include> needs to know its local circuit as an attribute
          if ( strtolower($fb_["xnThisFA"][$fb_["j"]]["xmlName"]) == "include" && !array_key_exists("circuit",$fb_["xnThisFA"][$fb_["j"]]["xmlAttributes"]) ) {
            $fb_["xnThisFA"][$fb_["j"]]["xmlAttributes"]["circuit"] = $myFusebox["thisCircuit"];
          }
          
          // some special handling for do's
          if ( strtolower($fb_["xnThisFA"][$fb_["j"]]["xmlName"]) == "do" ) {
            if ( __ListLen($fb_["xnThisFA"][$fb_["j"]]["xmlAttributes"]["action"], '.') == 1 ) {
              // remember that any <do> might have only a fuseaction specified and only imply its local circuit do clarify all <do>s with explicit circuits
              $fb_["xnThisFA"][$fb_["j"]]["xmlAttributes"]["action"] = $myFusebox["thisCircuit"] . "." . $fb_["xnThisFA"][$fb_["j"]]["xmlAttributes"]["action"];
            }
          }
        }
        
        /* last handle any postFuseaction fuseactions */
        $fb_["xnPostFA"] = array();
        $fb_["xnAnyPostFA"] = $application["fusebox"]["circuits"][$myFusebox["thisCircuit"]]["postfuseaction"]["xml"];
            
        if ( ( array_key_exists( "xmlChildren", $fb_["xnAnyPostFA"] ) && count( $fb_["xnAnyPostFA"]["xmlChildren"] ) ) || $application["fusebox"]["circuits"][$myFusebox["thisCircuit"]]["postfuseaction"]["callsuper"] ) {
          if ( $application["fusebox"]["circuits"][$myFusebox["thisCircuit"]]["postfuseaction"]["callsuper"] ) {
            // the circuit's super must be called *last*
            $fb_["xnPostFA"] = array();
            // loop over the circuitTrace for this circuit
            for ( $fb_["k"] = 0 ; $fb_["k"] < count( $application["fusebox"]["circuits"][$myFusebox["thisCircuit"]]["circuitTrace"]) ; $fb_["k"]++ ) {
              $fb_["aCircuit"] = $application["fusebox"]["circuits"][$myFusebox["thisCircuit"]]["circuitTrace"][$fb_["k"]];
              // grab aCircuit's common super code
              $fb_["xnSuperPostFA"] = $application["fusebox"]["circuits"][$fb_["aCircuit"]]["postfuseaction"]["xml"];
              // make sure right value for myFusebox.thisCircuit is set
              $fb_["temp"] = array();
              $fb_["temp"]["xmlName"] = "set";
              $fb_["temp"]["circuit"] = $myFusebox["thisCircuit"];
              $fb_["temp"]["fuseaction"] = $myFusebox["thisFuseaction"];
              $fb_["temp"]["phase"] = $fb_["phase"];
              $fb_["temp"]["xmlAttributes"] = array();
              $fb_["temp"]["xmlAttributes"]["name"] = "myFusebox['thisCircuit']";
              $fb_["temp"]["xmlAttributes"]["value"] = $fb_["aCircuit"];
              array_push( $fb_["xnPostFA"], $fb_["temp"] );
              // loop thru each entry from the super AND append it
              if ( array_key_exists( "xmlChildren", $fb_["xnSuperPostFA"] ) ) {
                for ( $fb_["i"] = 0 ; $fb_["i"] < count($fb_["xnSuperPostFA"]["xmlChildren"]) ; $fb_["i"]++ ) {
                  // remember that any <include> needs to know its local circuit as an attribute
                  if ( strtolower($fb_["xnSuperPostFA"]["xmlChildren"][$fb_["i"]]["xmlName"]) == "include" ) {
                    $fb_["xnSuperPostFA"]["xmlChildren"][$fb_["i"]]["xmlAttributes"]["circuit"] = $fb_["aCircuit"];
                  }
                  // remember that any <do> might have only a fuseaction specified AND only imply its local circuit do clarify all <do>s with explicit circuits
                  if ( ( strtolower($fb_["xnSuperPostFA"]["xmlChildren"][$fb_["i"]]["xmlName"]) == "do" ) && ( __ListLen($fb_["xnSuperPostFA"]["xmlChildren"][$fb_["i"]]["xmlAttributes"]["action"], "." ) == 1 ) ) {
                    $fb_["xnSuperPostFA"]["xmlChildren"][$fb_["i"]]["xmlAttributes"]["action"] = $fb_["aCircuit"] . "." . $fb_["xnSuperPostFA"]["xmlChildren"][$fb_["i"]]["xmlAttributes"]["action"];
                  }
                  
                  if ( !isset($fb_["xnSuperPostFA"]["xmlChildren"][$fb_["i"]]["circuit"]) ) $fb_["xnSuperPostFA"]["xmlChildren"][$fb_["i"]]["circuit"] = $fb_["aCircuit"];
                  if ( !isset($fb_["xnSuperPostFA"]["xmlChildren"][$fb_["i"]]["fuseaction"]) ) $fb_["xnSuperPostFA"]["xmlChildren"][$fb_["i"]]["fuseaction"] = $myFusebox["thisFuseaction"];
                  
                  // append it to what came from the child circuit
                  array_push( $fb_["xnPostFA"], $fb_["xnSuperPostFA"]["xmlChildren"][$fb_["i"]] );
                }
                // see if it calls *its* super; if not, then break out of this loop
                if ( !$application["fusebox"]["circuits"][$fb_["aCircuit"]]["postfuseaction"]["callsuper"] ) {
                  break;
                }
              }
            }
            
            // since postfuseaction calls to super would have overwritten the myFusebox.thisCircuit we need to reset it again
            $fb_["temp"] = array();
            $fb_["temp"]["xmlName"] = "set";
            $fb_["temp"]["circuit"] = $myFusebox["thisCircuit"];
            $fb_["temp"]["fuseaction"] = $myFusebox["thisFuseaction"];
            $fb_["temp"]["phase"] = $fb_["phase"];
            $fb_["temp"]["xmlAttributes"] = array();
            $fb_["temp"]["xmlAttributes"]["name"] = "myFusebox['thisCircuit']";
            $fb_["temp"]["xmlAttributes"]["value"] = $myFusebox["thisCircuit"];
            array_push( $fb_["xnPostFA"], $fb_["temp"] );
            
            // since postfuseaction calls to super would have overwritten the myFusebox.thisFuseaction we need to reset it again
            $fb_["temp"] = array();
            $fb_["temp"]["xmlName"] = "set";
            $fb_["temp"]["circuit"] = $myFusebox["thisCircuit"];
            $fb_["temp"]["fuseaction"] = $myFusebox["thisFuseaction"];
            $fb_["temp"]["phase"] = $fb_["phase"];
            $fb_["temp"]["xmlAttributes"] = array();
            $fb_["temp"]["xmlAttributes"]["name"] = "myFusebox['thisFuseaction']";
            $fb_["temp"]["xmlAttributes"]["value"] = $myFusebox["thisFuseaction"];
            array_push( $fb_["xnPostFA"], $fb_["temp"] );
          } else {
            $fb_["xnPostFA"] = $application["fusebox"]["circuits"][$myFusebox["thisCircuit"]]["postfuseaction"]["xml"]["xmlChildren"];
			for ( $fb_["j"] = 0 ; $fb_["j"] < count($fb_["xnPostFA"]) ; $fb_["j"]++ ) {
            
		      if ( !isset($fb_["xnPostFA"][$fb_["j"]]["circuit"]) ) $fb_["xnPostFA"][$fb_["j"]]["circuit"] = $myFusebox["thisCircuit"];
		      if ( !isset($fb_["xnPostFA"][$fb_["j"]]["fuseaction"]) ) $fb_["xnPostFA"][$fb_["j"]]["fuseaction"] = $myFusebox["thisFuseaction"];
              
              // remember that any <include> needs to know its local circuit as an attribute
              if ( strtolower($fb_["xnPostFA"][$fb_["j"]]["xmlName"]) == "include" ) {
                $fb_["xnPostFA"][$fb_["j"]]["xmlAttributes"]["circuit"] = $myFusebox["thisCircuit"];
              }
              
              // some special handling for do's
              if ( strtolower($fb_["xnPostFA"][$fb_["j"]]["xmlName"]) == "do" ) {
                if ( __ListLen($fb_["xnPostFA"][$fb_["j"]]["xmlAttributes"]["action"], '.') == 1 ) {
                  // remember that any <do> might have only a fuseaction specified and only imply its local circuit do clarify all <do>s with explicit circuits
                  $fb_["xnPostFA"][$fb_["j"]]["xmlAttributes"]["action"] = $myFusebox["thisCircuit"] . "." . $fb_["xnPostFA"][$fb_["j"]]["xmlAttributes"]["action"];
                }
              }
            }
          }
        }
          
        // now assemble all these together
        $fb_["xnFA"] = array();
        $fb_["xnFA"] = array_merge($fb_["xnInitFA"],$fb_["xnPlugins"]["preFuseaction"],$fb_["xnPreFA"], $fb_["xnThisFA"], $fb_["xnPostFA"],$fb_["xnPlugins"]["postFuseaction"],$fb_["xnPlugins"]["fuseactionException"],$fb_["xnCloseFA"]);
        
        // if this fuseaction has an exceptionHandler then insert space-holders for opening and closing <CFTRY></CFTRY> tags
        // we also did this when first parsing this <do>
        if ( count($application["fusebox"]["pluginphases"]["fuseactionException"]) > 0 ) {
          $fb_["temp"] = array();
          $fb_["temp"]["xmlName"] = "endExceptionHandler";
          $fb_["temp"]["circuit"] = $myFusebox["thisCircuit"];
          $fb_["temp"]["fuseaction"] = $myFusebox["thisFuseaction"];
          $fb_["temp"]["phase"] = $fb_["phase"];
          $fb_["temp"]["xmlAttributes"] = array();
          $fb_["temp"]["xmlAttributes"]["name"] = "myFusebox['thisCircuit']";
          $fb_["temp"]["xmlAttributes"]["value"] = $myFusebox["thisCircuit"];
          array_push($fb_["xnFA"],$fb_["temp"]);
        }
        
        // this is where a begin/end tag for contentvariables via a <do> would be put
        if ( array_key_exists("contentvariable",$fb_["fuseQ"][$fb_["pointer"]]["xmlAttributes"]) && $fb_["fuseQ"][$fb_["pointer"]]["xmlName"] != "contentvariable" ) {
          $fb_["temp"] = array();
          $fb_["temp"]["xmlName"] = "contentvariable";
          $fb_["temp"]["circuit"] = $myFusebox["thisCircuit"];
          $fb_["temp"]["fuseaction"] = $myFusebox["thisFuseaction"];
          $fb_["temp"]["phase"] = $fb_["phase"];
          $fb_["temp"]["xmlAttributes"] = array();
          $fb_["temp"]["xmlAttributes"]["contentvariable"] = $fb_["fuseQ"][$fb_["pointer"]]["xmlAttributes"]["contentvariable"];
          $fb_["temp"]["xmlAttributes"]["mode"] = "begin";
          if ( array_key_exists("append",$fb_["fuseQ"][$fb_["pointer"]]["xmlAttributes"]) ) {
            $fb_["temp"]["xmlAttributes"]["append"] = $fb_["fuseQ"][$fb_["pointer"]]["xmlAttributes"]["append"];
          }
          else {
            $fb_["temp"]["xmlAttributes"]["append"] = false;
          }
          if ( array_key_exists("prepend",$fb_["fuseQ"][$fb_["pointer"]]["xmlAttributes"]) ) {
            $fb_["temp"]["xmlAttributes"]["prepend"] = $fb_["fuseQ"][$fb_["pointer"]]["xmlAttributes"]["prepend"];
          }
          else {
            $fb_["temp"]["xmlAttributes"]["prepend"] = false;
          }
          if ( array_key_exists("overwrite",$fb_["fuseQ"][$fb_["pointer"]]["xmlAttributes"]) ) {
            $fb_["temp"]["xmlAttributes"]["overwrite"] = $fb_["fuseQ"][$fb_["pointer"]]["xmlAttributes"]["overwrite"];
          }
          else {
            $fb_["temp"]["xmlAttributes"]["overwrite"] = true;
          }
          array_unshift($fb_["xnFA"],$fb_["temp"]);
          
          $fb_["temp"]["xmlAttributes"]["mode"] = "end";
          array_push($fb_["xnFA"],$fb_["temp"]);
        }
		$myFusebox["thisCircuit"] = $fb_["fuseQ"][$fb_["pointer"]]["circuit"];
		$myFusebox["thisFuseaction"] = $fb_["fuseQ"][$fb_["pointer"]]["fuseaction"];
        
        break;
      
      case "if" :
        $fb_["doMore"] = true;
        // handle the opening of the conditional
        $fb_["xnBegin"] = array();
        $fb_["temp"] = array();
        $fb_["temp"]["xmlName"] = "conditional";
        $fb_["temp"]["circuit"] = $myFusebox["thisCircuit"];
        $fb_["temp"]["fuseaction"] = $myFusebox["thisFuseaction"];
        $fb_["temp"]["phase"] = $fb_["phase"];
        $fb_["temp"]["xmlAttributes"] = array();
        $fb_["temp"]["xmlAttributes"]["mode"] = "begin";
        foreach ( array_keys($fb_["fuseQ"][$fb_["pointer"]]["xmlAttributes"]) as $fb_["anItem"] ) {
          $fb_["temp"]["xmlAttributes"][$fb_["anItem"]] = $fb_["fuseQ"][$fb_["pointer"]]["xmlAttributes"][$fb_["anItem"]];
        }
        array_push($fb_["xnBegin"],$fb_["temp"]);
        
        // insert all the statements when conditional is TRUE
        $fb_["xnTrue"] = array();
        for ( $fb_["m"] = 0 ; $fb_["m"] < count($fb_["fuseQ"][$fb_["pointer"]]["xmlChildren"]) ; $fb_["m"]++ ) {
          if ( strtolower($fb_["fuseQ"][$fb_["pointer"]]["xmlChildren"][$fb_["m"]]["xmlName"]) == "true" ) {
            $fb_["xnTrue"] = $fb_["fuseQ"][$fb_["pointer"]]["xmlChildren"][$fb_["m"]]["xmlChildren"];
			if ( count($fb_["xnTrue"]) > 0 ) {
			  for ( $fb_["n"] = 0 ; $fb_["n"] < count($fb_["xnTrue"]) ; $fb_["n"]++ ) {
			    
				if ( !isset($fb_["xnTrue"][$fb_["n"]]["circuit"]) ) $fb_["xnTrue"][$fb_["n"]]["circuit"] = $fb_["fuseQ"][$fb_["pointer"]]["circuit"];
				if ( !isset($fb_["xnTrue"][$fb_["n"]]["fuseaction"]) ) $fb_["xnTrue"][$fb_["n"]]["fuseaction"] = $fb_["fuseQ"][$fb_["pointer"]]["fuseaction"];
				
				// remember that any <include> has to know its local circuit
				if ( $fb_["xnTrue"][$fb_["n"]]["xmlName"] == "include" && !isset($fb_["xnTrue"][$fb_["n"]]["xmlAttributes"]["circuit"]) )
				  $fb_["xnTrue"][$fb_["n"]]["xmlAttributes"]["circuit"] = $fb_["fuseQ"][$fb_["pointer"]]["circuit"];
			  }
			}
          }
        }
        
        // insert all the statements when conditional is FALSE
        $fb_["xnFalse"] = array();
        for ( $fb_["m"] = 0 ; $fb_["m"] < count($fb_["fuseQ"][$fb_["pointer"]]["xmlChildren"]) ; $fb_["m"]++ ) {
          if ( strtolower($fb_["fuseQ"][$fb_["pointer"]]["xmlChildren"][$fb_["m"]]["xmlName"]) == "false" ) {
            $fb_["xnFalse"] = $fb_["fuseQ"][$fb_["pointer"]]["xmlChildren"][$fb_["m"]]["xmlChildren"];
			if ( count($fb_["xnFalse"]) > 0 ) {
			  for ( $fb_["n"] = 0 ; $fb_["n"] < count($fb_["xnFalse"]) ; $fb_["n"]++ ) {
			    
				if ( !isset($fb_["xnFalse"][$fb_["n"]]["circuit"]) ) $fb_["xnFalse"][$fb_["n"]]["circuit"] = $fb_["fuseQ"][$fb_["pointer"]]["circuit"];
				if ( !isset($fb_["xnFalse"][$fb_["n"]]["fuseaction"]) ) $fb_["xnFalse"][$fb_["n"]]["fuseaction"] = $fb_["fuseQ"][$fb_["pointer"]]["fuseaction"];
				
			    // remember that any <include> has to know its local circuit
				if ( $fb_["xnFalse"][$fb_["n"]]["xmlName"] == "include" && !isset($fb_["xnFalse"][$fb_["n"]]["xmlAttributes"]["circuit"]) )
				  $fb_["xnFalse"][$fb_["n"]]["xmlAttributes"]["circuit"] = $fb_["fuseQ"][$fb_["pointer"]]["circuit"];
			  }
			}
          }
        }
        
        // handle the alternate of the conditional
        $fb_["xnElse"] = array();
        if ( count($fb_["xnFalse"]) > 0 ) {
          $fb_["temp"] = array();
          $fb_["temp"]["xmlName"] = "conditional";
          $fb_["temp"]["circuit"] = $myFusebox["thisCircuit"];
          $fb_["temp"]["fuseaction"] = $myFusebox["thisFuseaction"];
          $fb_["temp"]["phase"] = $fb_["phase"];
          $fb_["temp"]["xmlAttributes"] = array();
          $fb_["temp"]["xmlAttributes"]["mode"] = "else";
          array_push($fb_["xnElse"],$fb_["temp"]);
        }
        
        // handle the closing of the conditional
        $fb_["xnEnd"] = array();
        $fb_["temp"] = array();
        $fb_["temp"]["xmlName"] = "conditional";
        $fb_["temp"]["circuit"] = $myFusebox["thisCircuit"];
        $fb_["temp"]["fuseaction"] = $myFusebox["thisFuseaction"];
        $fb_["temp"]["phase"] = $fb_["phase"];
        $fb_["temp"]["xmlAttributes"] = array();
        $fb_["temp"]["xmlAttributes"]["mode"] = "end";
        array_push($fb_["xnEnd"],$fb_["temp"]);
        
        // now assemble all these together
        $fb_["xnFA"] = array();
        $fb_["xnFA"] = array_merge($fb_["xnBegin"], $fb_["xnTrue"], $fb_["xnElse"], $fb_["xnFalse"], $fb_["xnEnd"]);
        
        break;
        
      case "loop" :
        if ( array_key_exists("xmlChildren",$fb_["fuseQ"][$fb_["pointer"]]) ) {
		
			$fb_["doMore"] = true;
	        
	        // handle the opening of the loop
	        $fb_["xnBegin"] = array();
	        $fb_["temp"] = array();
	        $fb_["temp"]["xmlName"] = "loop";
	        $fb_["temp"]["circuit"] = $myFusebox["thisCircuit"];
	        $fb_["temp"]["fuseaction"] = $myFusebox["thisFuseaction"];
	        $fb_["temp"]["phase"] = $fb_["phase"];
	        $fb_["temp"]["xmlAttributes"] = array();
	        $fb_["temp"]["xmlAttributes"]["mode"] = "begin";
	        foreach ( array_keys($fb_["fuseQ"][$fb_["pointer"]]["xmlAttributes"]) as $fb_["anItem"] ) {
	          $fb_["temp"]["xmlAttributes"][$fb_["anItem"]] = $fb_["fuseQ"][$fb_["pointer"]]["xmlAttributes"][$fb_["anItem"]];
	        }
	        array_push($fb_["xnBegin"],$fb_["temp"]);
	        
	        // insert all the statements within the loop
	        $fb_["xnLoop"] = $fb_["fuseQ"][$fb_["pointer"]]["xmlChildren"];
			if ( count($fb_["xnLoop"]) > 0 ) {
			  for ( $fb_["n"] = 0 ; $fb_["n"] < count($fb_["xnLoop"]) ; $fb_["n"]++ ) {
			    
				if ( !isset($fb_["xnLoop"][$fb_["n"]]["circuit"]) ) $fb_["xnLoop"][$fb_["n"]]["circuit"] = $fb_["fuseQ"][$fb_["pointer"]]["circuit"];
				if ( !isset($fb_["xnLoop"][$fb_["n"]]["fuseaction"]) ) $fb_["xnLoop"][$fb_["n"]]["fuseaction"] = $fb_["fuseQ"][$fb_["pointer"]]["fuseaction"];
				
			    // remember that any <include> has to know its local circuit
				if ( $fb_["xnLoop"][$fb_["n"]]["xmlName"] == "include" && !isset($fb_["xnLoop"][$fb_["n"]]["xmlAttributes"]["circuit"]) )
				  $fb_["xnLoop"][$fb_["n"]]["xmlAttributes"]["circuit"] = $fb_["fuseQ"][$fb_["pointer"]]["circuit"];
			  }
			}
	        
	        // handle the closing of the loop
	        $fb_["xnEnd"] = array();
	        $fb_["temp"] = array();
	        $fb_["temp"]["xmlName"] = "loop";
	        $fb_["temp"]["circuit"] = $myFusebox["thisCircuit"];
	        $fb_["temp"]["fuseaction"] = $myFusebox["thisFuseaction"];
	        $fb_["temp"]["phase"] = $fb_["phase"];
	        $fb_["temp"]["xmlAttributes"] = array();
	        $fb_["temp"]["xmlAttributes"]["mode"] = "end";
	        array_push($fb_["xnEnd"],$fb_["temp"]);
	        
	        // now assemble all these together
	        $fb_["xnFA"] = array();
	        $fb_["xnFA"] = array_merge($fb_["xnBegin"], $fb_["xnLoop"], $fb_["xnEnd"]);
        }
		
        break;
    }
    
    if ( $fb_["doMore"] ) {
      // we're done substituting for this "do", so we kill it off
      if ( $fb_["pointer"] == 0 ) {
        $fb_["fuseQ"] = array_merge($fb_["xnFA"],array_slice($fb_["fuseQ"],1));
      } else if ( $fb_["pointer"] + 1 < count($fb_["fuseQ"]) ) {
        $fb_["fuseQ"] = array_merge(array_slice($fb_["fuseQ"],0,$fb_["pointer"]),$fb_["xnFA"],array_slice($fb_["fuseQ"],$fb_["pointer"]+1));
      } else {
        $fb_["fuseQ"] = array_merge(array_slice($fb_["fuseQ"],0,$fb_["pointer"]),$fb_["xnFA"]);
      }
      break;
    }
  }//end for
}//end while

?>