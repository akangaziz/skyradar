<?php 
/*
  V4.60 24 Jan 2005  (c) 2000-2005 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.
  
  Some pretty-printing by Chris Oxenreider <oxenreid@state.net>
*/ 
  
// specific code for tohtml
GLOBAL $gSQLMaxRows,$gSQLBlockRows;
	 
$gSQLMaxRows = 1000; // max no of rows to download
$gSQLBlockRows=20; // max no of rows per table block

// RecordSet to HTML Table
//------------------------------------------------------------
// Convert a recordset to a html table. Multiple tables are generated
// if the number of rows is > $gSQLBlockRows. This is because
// web browsers normally require the whole table to be downloaded
// before it can be rendered, so we break the output into several
// smaller faster rendering tables.
//
// $rs: the recordset
// $ztabhtml: the table tag attributes (optional)
// $zheaderarray: contains the replacement strings for the headers (optional)
//
//  USAGE:
//	include('adodb.inc.php');
//	$db = ADONewConnection('mysql');
//	$db->Connect('mysql','userid','password','database');
//	$rs = $db->Execute('select col1,col2,col3 from table');
//	rs2html($rs, 'BORDER=2', array('Title1', 'Title2', 'Title3'));
//	$rs->Close();
//
// RETURNS: number of rows displayed
function rs2html(&$rs,$ztabhtml=false,$zheaderarray=false,$htmlspecialchars=true,$echo = true)
{
$s ='';$rows=0;$docnt = false;
GLOBAL $gSQLMaxRows,$gSQLBlockRows;

	if (!$rs) {
		printf(ADODB_BAD_RS,'rs2html');
		return false;
	}
	
	if (! $ztabhtml) $ztabhtml = "border=\"1\" width=\"98%\"";
	//else $docnt = true;
	$typearr = array();
	$ncols = $rs->FieldCount();
	$hdr = "<table cols=\"$ncols\" $ztabhtml><tr>\n\n";
	for ($i=0; $i < $ncols; $i++) {	
		$field = $rs->FetchField($i);
		if ($zheaderarray) $fname = $zheaderarray[$i];
		else $fname = htmlspecialchars($field->name);	
		$typearr[$i] = $rs->MetaType($field->type,$field->max_length);
 		//print " $field->name $field->type $typearr[$i] ";
			
		if (strlen($fname)==0) $fname = '&nbsp;';
		$hdr .= "<th>$fname</th>";
	}
	$hdr .= "\n</tr>";
	if ($echo) print $hdr."\n\n";
	else $html = $hdr;
	
	// smart algorithm - handles ADODB_FETCH_MODE's correctly by probing...
	$numoffset = isset($rs->fields[0]) ||isset($rs->fields[1]) || isset($rs->fields[2]);
	while (!$rs->EOF) {
		$s .= "<tr valign=\"top\">\n";
		
		for ($i=0; $i < $ncols; $i++) {
			if ($i===0) $v=($numoffset) ? $rs->fields[0] : reset($rs->fields);
			else $v = ($numoffset) ? $rs->fields[$i] : next($rs->fields);
			
			$type = $typearr[$i];
			switch($type) {
			case 'D':
				if (!strpos($v,':')) {
					$s .= "	<td>".$rs->UserDate($v,"D d, M Y") ."&nbsp;</td>\n";
					break;
				}
			case 'T':
				$s .= "	<td>".$rs->UserTimeStamp($v,"D d, M Y, h:i:s") ."&nbsp;</td>\n";
			break;
			case 'I':
			case 'N':
				$s .= "	<td align=\"right\">".stripslashes((trim($v))) ."&nbsp;</td>\n";
			   	
			break;
			/*
			case 'B':
				if (substr($v,8,2)=="BM" ) $v = substr($v,8);
				$mtime = substr(str_replace(' ','_',microtime()),2);
				$tmpname = "tmp/".uniqid($mtime).getmypid();
				$fd = @fopen($tmpname,'a');
				@ftruncate($fd,0);
				@fwrite($fd,$v);
				@fclose($fd);
				if (!function_exists ("mime_content_type")) {
				  function mime_content_type ($file) {
				    return exec("file -bi ".escapeshellarg($file));
				  }
				}
				$t = mime_content_type($tmpname);
				$s .= (substr($t,0,5)=="image") ? " <td><img src='$tmpname' alt='$t'></td>\\n" : " <td><a
				href='$tmpname'>$t</a></td>\\n";
				break;
			*/

			default:
				if ($htmlspecialchars) $v = htmlspecialchars(trim($v));
				$v = trim($v);
				if (strlen($v) == 0) $v = '&nbsp;';
				$s .= "	<td>". str_replace("\n",'<br />',stripslashes($v)) ."</td>\n";
			  
			}
		} // for
		$s .= "</tr>\n\n";
			  
		$rows += 1;
		if ($rows >= $gSQLMaxRows) {
			$rows = "<p>Truncated at $gSQLMaxRows</p>";
			break;
		} // switch

		$rs->MoveNext();
	
	// additional EOF check to prevent a widow header
		if (!$rs->EOF && $rows % $gSQLBlockRows == 0) {
	
		//if (connection_aborted()) break;// not needed as PHP aborts script, unlike ASP
			if ($echo) print $s . "</table>\n\n";
			else $html .= $s ."</table>\n\n";
			$s = $hdr;
		}
	} // while

	if ($echo) print $s."</table>\n\n";
	else $html .= $s."</table>\n\n";
	
	if ($docnt) if ($echo) print "<h2>".$rows." Rows</h2>";
	
	return ($echo) ? $rows : $html;
 }
 
// pass in 2 dimensional array
function arr2html(&$arr,$ztabhtml='',$zheaderarray='')
{
	if (!$ztabhtml) $ztabhtml = 'border=\"1\"';
	
	$s = "<table $ztabhtml>";//';print_r($arr);

	if ($zheaderarray) {
		$s .= '<tr>';
		for ($i=0; $i<sizeof($zheaderarray); $i++) {
			$s .= "	<th>{$zheaderarray[$i]}</th>\n";
		}
		$s .= "\n</tr>";
	}
	
	for ($i=0; $i<sizeof($arr); $i++) {
		$s .= '<tr>';
		$a = &$arr[$i];
		if (is_array($a)) 
			for ($j=0; $j<sizeof($a); $j++) {
				$val = $a[$j];
				if (empty($val)) $val = '&nbsp;';
				$s .= "	<td>$val</td>\n";
			}
		else if ($a) {
			$s .=  '	<td>'.$a."</td>\n";
		} else $s .= "	<td>&nbsp;</td>\n";
		$s .= "\n</tr>\n";
	}
	$s .= '</table>';
	print $s;
}

?>
