<?
function indodate(){
	
	$arrDay 	= array("Sunday"=>"Minggu","Monday"=>"Senin","Tuesday"=>"Selasa","Wednesday"=>"Rabu","Thursday"=>"Kamis","Friday"=>"Jumat","Saturday"=>"Sabtu");
	$arrMonth 	= array("","Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember");
	$dayOfWeek 	= date("l",mktime());
	$month 		= date("n");
	return $arrDay[$dayOfWeek].", ".date("j")." ".$arrMonth[$month]." ".date("Y");
}


function indodatetime($ts, $namedformat)
{
	define("EW_DATE_SEPARATOR", "/", true);
	$DefDateFormat = str_replace("yyyy", "%Y", "dd/mm/yyyy");
	$DefDateFormat = str_replace("mm", "%m", $DefDateFormat);
	$DefDateFormat = str_replace("dd", "%d", $DefDateFormat);
	if (is_numeric($ts)) // timestamp
	{
		switch (strlen($ts)) {
			case 14:
				$patt = '/(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/';
				break;
			case 12:
				$patt = '/(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/';
				break;
			case 10:
				$patt = '/(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/';
				break;
			case 8:
				$patt = '/(\d{4})(\d{2})(\d{2})/';
				break;
			case 6:
				$patt = '/(\d{2})(\d{2})(\d{2})/';
				break;
			case 4:
				$patt = '/(\d{2})(\d{2})/';
				break;
			case 2:
				$patt = '/(\d{2})/';
				break;
			default:
				return $ts;
		}
		if ((isset($patt))&&(preg_match($patt, $ts, $matches)))
		{
			$year = $matches[1];
			$month = @$matches[2];
			$day = @$matches[3];
			$hour = @$matches[4];
			$min = @$matches[5];
			$sec = @$matches[6];
		}
		if (($namedformat==0)&&(strlen($ts)<10)) $namedformat = 2;
	}
	elseif (is_string($ts))
	{
		if (preg_match('/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/', $ts, $matches)) // datetime
		{
			$year = $matches[1];
			$month = $matches[2];
			$day = $matches[3];
			$hour = $matches[4];
			$min = $matches[5];
			$sec = $matches[6];
		}
		elseif (preg_match('/(\d{4})-(\d{2})-(\d{2})/', $ts, $matches)) // date
		{
			$year = $matches[1];
			$month = $matches[2];
			$day = $matches[3];
			if ($namedformat==0) $namedformat = 2;
		}
		elseif (preg_match('/(^|\s)(\d{2}):(\d{2}):(\d{2})/', $ts, $matches)) // time
		{
			$hour = $matches[2];
			$min = $matches[3];
			$sec = $matches[4];
			if (($namedformat==0)||($namedformat==1)) $namedformat = 3;
			if ($namedformat==2) $namedformat = 4;
		}
		else
		{
			return $ts;
		}
	}
	else
	{
		return $ts;
	}
	if (!isset($year)) $year = 0; // dummy value for times
	if (!isset($month)) $month = 1;
	if (!isset($day)) $day = 1;
	if (!isset($hour)) $hour = 0;
	if (!isset($min)) $min = 0;
	if (!isset($sec)) $sec = 0;
	$uts = @mktime($hour, $min, $sec, $month, $day, $year);
	if ($uts < 0) { // failed to convert
		$year = substr_replace("0000", $year, -1 * strlen($year));
		$month = substr_replace("00", $month, -1 * strlen($month));
		$day = substr_replace("00", $day, -1 * strlen($day));
		$hour = substr_replace("00", $hour, -1 * strlen($hour));
		$min = substr_replace("00", $min, -1 * strlen($min));
		$sec = substr_replace("00", $sec, -1 * strlen($sec));
		$DefDateFormat = str_replace("yyyy", $year, DEFAULT_DATE_FORMAT);
		$DefDateFormat = str_replace("mm", $month, $DefDateFormat);
		$DefDateFormat = str_replace("dd", $day, $DefDateFormat);
		switch ($namedformat) {
			case 0:
				return $DefDateFormat." $hour:$min:$sec";
				break;
			case 1://unsupported, return general date
				return $DefDateFormat." $hour:$min:$sec";
				break;
			case 2:
				return $DefDateFormat;
				break;
			case 3:
				if (intval($hour)==0)
					return "12:$min:$sec AM";
				elseif (intval($hour)>0 && intval($hour)<12)
					return "$hour:$min:$sec AM";
				elseif (intval($hour)==12)
					return "$hour:$min:$sec PM";
				elseif (intval($hour)>12 && intval($hour)<=23)
					return (intval($hour)-12).":$min:$sec PM";
				else
					return "$hour:$min:$sec";
				break;
			case 4:
				return "$hour:$min:$sec";
				break;
			case 5:
				return "$year". EW_DATE_SEPARATOR . "$month" . EW_DATE_SEPARATOR . "$day";
				break;
			case 6:
				return "$month". EW_DATE_SEPARATOR ."$day" . EW_DATE_SEPARATOR . "$year";
				break;
			case 7:
				return "$day" . EW_DATE_SEPARATOR ."$month" . EW_DATE_SEPARATOR . " $year $hour:$min:$sec WIB";
				break;
		}
	} else {
		switch ($namedformat) {
			case 0:
				return strftime($DefDateFormat." %H:%M:%S", $uts);
				break;
			case 1:
				return strftime("%A, %B %d, %Y", $uts);
				break;
			case 2:
				return strftime($DefDateFormat, $uts);
				break;
			case 3:
				return strftime("%I:%M:%S %p", $uts);
				break;
			case 4:
				return strftime("%H:%M:%S", $uts);
				break;
			case 5:
				return strftime("%Y" . EW_DATE_SEPARATOR . "%m" . EW_DATE_SEPARATOR . "%d", $uts);
				break;
			case 6:
				return strftime("%m" . EW_DATE_SEPARATOR . "%d" . EW_DATE_SEPARATOR . "%Y", $uts);
				break;
			case 7:
				return strftime("%d" . EW_DATE_SEPARATOR . "%m" . EW_DATE_SEPARATOR . "%Y %H:%M:%S WIB", $uts);
				break;
		}
	}
}


function debug($var){
  echo "<pre>";print_r($var);echo "</pre>";
}

function formaturl($str){
        
        $str = strip_tags(strtolower(trim($str)));
        $forbiddenstr   = array(" ","'",'"',":",".",",","&","?","--","%","<i>","<b>","!", "@", "$", "^", "*", "(",")","+","=", "{", "}", "[", "]", ";","<",">", "|", "/", "\\", "~","`", "_");
        $replacementstr = array("-", "",  "","", "","", "","","-","","","", "", "-", "-","-","-","-","-","-","","-","-","-","-","","-","-","" ,"-","-", "", "", "");
        $str = str_replace($forbiddenstr,$replacementstr,$str);
        return $str;
}

 


function limitword($word,$limit){
	$kata = "";
	$a = explode(" ",strip_tags($word));#echo "<pre>";print_r($a);echo "</pre><hr>";
	for($i=0;$i<count($a);$i++){
		$kata .= $a[$i]." ";
		if($i+1 == $limit) break;
	}
	$temp = trim($kata);
	if(substr($temp,-1,1) == "."){
	  return $kata."";
	}else{
	  return $kata." [...]";
	}
}

function limitwordbystring($word,$limit){
  if(strlen($word)>$limit){
    return substr($word, 0, $limit)."..";
  }else{
    return $word;
  }
}

function strip_html_tags( $text )
{
    $text = preg_replace(
        array(
          // Remove invisible content
            '@<head[^>]*?>.*?</head>@siu',
            '@<style[^>]*?>.*?</style>@siu',
            '@<script[^>]*?.*?</script>@siu',
            '@<object[^>]*?.*?</object>@siu',
            '@<embed[^>]*?.*?</embed>@siu',
            '@<applet[^>]*?.*?</applet>@siu',
            '@<noframes[^>]*?.*?</noframes>@siu',
            '@<noscript[^>]*?.*?</noscript>@siu',
            '@<noembed[^>]*?.*?</noembed>@siu',
          // Add line breaks before and after blocks
            '@</?((address)|(blockquote)|(center)|(del))@iu',
            '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
            '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
            '@</?((table)|(th)|(td)|(caption))@iu',
            '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
            '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
            '@</?((frameset)|(frame)|(iframe))@iu',
        ),
        array(
            ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
            "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
            "\n\$0", "\n\$0",
        ),
        $text );
    return strip_tags( $text );
}

 
function smallname($filename, $name){
	$file_tmp	= explode(".",$filename);
	$file		= $file_tmp[0].($name!="" ? "-$name." : "$name.").$file_tmp[1];
	return $file;
}
 
// FormatCurrency
//ew_FormatCurrency(Expression[,NumDigitsAfterDecimal [,IncludeLeadingDigit
// [,UseParensForNegativeNumbers [,GroupDigits]]]])
//NumDigitsAfterDecimal is the numeric value indicating how many places to the
//right of the decimal are displayed
//-1 Use Default
//The IncludeLeadingDigit, UseParensForNegativeNumbers, and GroupDigits
//arguments have the following settings:
//-1 True
//0 False
//-2 Use Default
function ew_FormatCurrency($amount, $NumDigitsAfterDecimal, $IncludeLeadingDigit = -2, $UseParensForNegativeNumbers = -2, $GroupDigits = -2) {
	define("EW_USE_DEFAULT_LOCALE", FALSE, TRUE);
	define("DEFAULT_DECIMAL_POINT", ",", TRUE);
	define("DEFAULT_THOUSANDS_SEP", ".", TRUE);
	define("DEFAULT_CURRENCY_SYMBOL", "Rp. ", TRUE);
	define("DEFAULT_MON_DECIMAL_POINT", ",", TRUE);
	define("DEFAULT_MON_THOUSANDS_SEP", ".", TRUE);
	define("DEFAULT_POSITIVE_SIGN", "", TRUE);
	define("DEFAULT_NEGATIVE_SIGN", "-", TRUE);
	define("DEFAULT_FRAC_DIGITS", 2, TRUE);
	define("DEFAULT_P_CS_PRECEDES", TRUE, TRUE);
	define("DEFAULT_P_SEP_BY_SPACE", FALSE, TRUE);
	define("DEFAULT_N_CS_PRECEDES", TRUE, TRUE);
	define("DEFAULT_N_SEP_BY_SPACE", FALSE, TRUE);
	define("DEFAULT_P_SIGN_POSN", 3, TRUE);
	define("DEFAULT_N_SIGN_POSN", 3, TRUE);
	// export the values returned by localeconv into the local scope
	if (!EW_USE_DEFAULT_LOCALE) extract(localeconv()); // PHP 4 >= 4.0.5
	
	// set defaults if locale is not set
	$decimal_point = DEFAULT_DECIMAL_POINT;
	$thousands_sep = DEFAULT_THOUSANDS_SEP;
	$currency_symbol = DEFAULT_CURRENCY_SYMBOL;
	$mon_decimal_point = DEFAULT_MON_DECIMAL_POINT;
	$mon_thousands_sep = DEFAULT_MON_THOUSANDS_SEP;
	$positive_sign = DEFAULT_POSITIVE_SIGN;
	$negative_sign = DEFAULT_NEGATIVE_SIGN;
	if (empty($frac_digits) || $frac_digits == CHAR_MAX) $frac_digits = DEFAULT_FRAC_DIGITS;
	if (empty($p_cs_precedes) || $p_cs_precedes == CHAR_MAX) $p_cs_precedes = DEFAULT_P_CS_PRECEDES;
	if (empty($p_sep_by_space) || $p_sep_by_space == CHAR_MAX) $p_sep_by_space = DEFAULT_P_SEP_BY_SPACE;
	if (empty($n_cs_precedes) || $n_cs_precedes == CHAR_MAX) $n_cs_precedes = DEFAULT_N_CS_PRECEDES;
	if (empty($n_sep_by_space) || $n_sep_by_space == CHAR_MAX) $n_sep_by_space = DEFAULT_N_SEP_BY_SPACE;
	if (empty($p_sign_posn) || $p_sign_posn == CHAR_MAX) $p_sign_posn = DEFAULT_P_SIGN_POSN;
	if (empty($n_sign_posn) || $n_sign_posn == CHAR_MAX) $n_sign_posn = DEFAULT_N_SIGN_POSN;

	// check $NumDigitsAfterDecimal
	if ($NumDigitsAfterDecimal > -1)
		$frac_digits = $NumDigitsAfterDecimal;

	// check $UseParensForNegativeNumbers
	if ($UseParensForNegativeNumbers == -1) {
		$n_sign_posn = 0;
		if ($p_sign_posn == 0) {
			if (DEFAULT_P_SIGN_POSN != 0)
				$p_sign_posn = DEFAULT_P_SIGN_POSN;
			else
				$p_sign_posn = 3;
		}
	} elseif ($UseParensForNegativeNumbers == 0) {
		if ($n_sign_posn == 0)
			if (DEFAULT_P_SIGN_POSN != 0)
				$n_sign_posn = DEFAULT_P_SIGN_POSN;
			else
				$n_sign_posn = 3;
	}

	// check $GroupDigits
	if ($GroupDigits == -1) {
		$mon_thousands_sep = DEFAULT_MON_THOUSANDS_SEP;
	} elseif ($GroupDigits == 0) {
		$mon_thousands_sep = "";
	}

	// start by formatting the unsigned number
	$number = number_format(abs($amount),
							$frac_digits,
							$mon_decimal_point,
							$mon_thousands_sep);

	// check $IncludeLeadingDigit
	if ($IncludeLeadingDigit == 0) {
		if (substr($number, 0, 2) == "0.")
			$number = substr($number, 1, strlen($number)-1);
	}
	if ($amount < 0) {
		$sign = $negative_sign;

		// "extracts" the boolean value as an integer
		$n_cs_precedes  = intval($n_cs_precedes  == true);
		$n_sep_by_space = intval($n_sep_by_space == true);
		$key = $n_cs_precedes . $n_sep_by_space . $n_sign_posn;
	} else {
		$sign = $positive_sign;
		$p_cs_precedes  = intval($p_cs_precedes  == true);
		$p_sep_by_space = intval($p_sep_by_space == true);
		$key = $p_cs_precedes . $p_sep_by_space . $p_sign_posn;
	}
	$formats = array(

	  // currency symbol is after amount
	  // no space between amount and sign

	  '000' => '(%s' . $currency_symbol . ')',
	  '001' => $sign . '%s ' . $currency_symbol,
	  '002' => '%s' . $currency_symbol . $sign,
	  '003' => '%s' . $sign . $currency_symbol,
	  '004' => '%s' . $sign . $currency_symbol,

	  // one space between amount and sign
	  '010' => '(%s ' . $currency_symbol . ')',
	  '011' => $sign . '%s ' . $currency_symbol,
	  '012' => '%s ' . $currency_symbol . $sign,
	  '013' => '%s ' . $sign . $currency_symbol,
	  '014' => '%s ' . $sign . $currency_symbol,

	  // currency symbol is before amount
	  // no space between amount and sign

	  '100' => '(' . $currency_symbol . '%s)',
	  '101' => $sign . $currency_symbol . '%s',
	  '102' => $currency_symbol . '%s' . $sign,
	  '103' => $sign . $currency_symbol . '%s',
	  '104' => $currency_symbol . $sign . '%s',

	  // one space between amount and sign
	  '110' => '(' . $currency_symbol . ' %s)',
	  '111' => $sign . $currency_symbol . ' %s',
	  '112' => $currency_symbol . ' %s' . $sign,
	  '113' => $sign . $currency_symbol . ' %s',
	  '114' => $currency_symbol . ' ' . $sign . '%s');

  // lookup the key in the above array
	return sprintf($formats[$key], $number);
}

function safeSQL($string) {
	$string = stripslashes($string);
	$string = strip_tags($string);
	$string = mysql_real_escape_string($string);
	return $string;
} 

function get_admin_level_by_id($id){
GLOBAL $conn;
	$rs	= $conn->Execute("SELECT admin_level FROM admin_levels WHERE id='$id'");
	return $rs->fields['admin_level'];
}

function get_area_by_id($id){
GLOBAL $conn;
	$rs	= $conn->Execute("SELECT area FROM areas WHERE id='$id'");
	return $rs->fields['area'];
}

function get_sales_area_by_id($id){
GLOBAL $conn;
	$rs	= $conn->Execute("SELECT sales_area FROM sales_areas WHERE id='$id'");
	return $rs->fields['sales_area'];
}

function get_cluster_by_id($id){
GLOBAL $conn;
	$rs	= $conn->Execute("SELECT cluster FROM clusters WHERE id='$id'");
	return $rs->fields['cluster'];
}

function get_permission_by_level_id($id){
GLOBAL $conn;
	$rs	= $conn->Execute("SELECT permission FROM admin_levels WHERE id='$id'");
	return $rs->fields['permission'];
}

function get_app_path(){
  #debug($_SERVER);
  $t	= explode("/",$_SERVER['REQUEST_URI']);
  for($i=0;$i<count($t)-1;$i++){
    $app .= $t[$i]."/";
  }
  $app 	= "http://".$_SERVER['HTTP_HOST'].str_replace("/admin/","",$app);
  return $app;
}

function get_app_folder_path(){
  #debug($_SERVER);
  $t	= explode("/",$_SERVER['SCRIPT_FILENAME']);
  for($i=0;$i<count($t)-1;$i++){
    $app .= $t[$i]."/";
  }
  return $app;
}

function smart_resize_image( $file, $width = 0, $height = 0, $proportional = false, $output = 'file', $delete_original = true, $use_linux_commands = false )
  {
    if ( $height <= 0 && $width <= 0 ) {
      return false;
    }
 
    $info = getimagesize($file);
    $image = '';
 
    $final_width = 0;
    $final_height = 0;
    list($width_old, $height_old) = $info;
 
    if ($proportional) {
      if ($width == 0) $factor = $height/$height_old;
      elseif ($height == 0) $factor = $width/$width_old;
      else $factor = min ( $width / $width_old, $height / $height_old);   
 
      $final_width = round ($width_old * $factor);
      $final_height = round ($height_old * $factor);
 
    }
    else {
      $final_width = ( $width <= 0 ) ? $width_old : $width;
      $final_height = ( $height <= 0 ) ? $height_old : $height;
    }
 
    switch ( $info[2] ) {
      case IMAGETYPE_GIF:
        $image = imagecreatefromgif($file);
      break;
      case IMAGETYPE_JPEG:
        $image = imagecreatefromjpeg($file);
      break;
      case IMAGETYPE_PNG:
        $image = imagecreatefrompng($file);
      break;
      default:
        return false;
    }
 
    $image_resized = imagecreatetruecolor( $final_width, $final_height );
 
    if ( ($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG) ) {
      $trnprt_indx = imagecolortransparent($image);
 
      // If we have a specific transparent color
      if ($trnprt_indx >= 0) {
 
        // Get the original image's transparent color's RGB values
        $trnprt_color    = imagecolorsforindex($image, $trnprt_indx);
 
        // Allocate the same color in the new image resource
        $trnprt_indx    = imagecolorallocate($image_resized, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
 
        // Completely fill the background of the new image with allocated color.
        imagefill($image_resized, 0, 0, $trnprt_indx);
 
        // Set the background color for new image to transparent
        imagecolortransparent($image_resized, $trnprt_indx);
 
 
      } 
      // Always make a transparent background color for PNGs that don't have one allocated already
      elseif ($info[2] == IMAGETYPE_PNG) {
 
        // Turn off transparency blending (temporarily)
        imagealphablending($image_resized, false);
 
        // Create a new transparent color for image
        $color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
 
        // Completely fill the background of the new image with allocated color.
        imagefill($image_resized, 0, 0, $color);
 
        // Restore transparency blending
        imagesavealpha($image_resized, true);
      }
    }
 
    imagecopyresampled($image_resized, $image, 0, 0, 0, 0, $final_width, $final_height, $width_old, $height_old);
 
    if ( $delete_original ) {
      if ( $use_linux_commands )
        exec('rm '.$file);
      else
        @unlink($file);
    }
 
    switch ( strtolower($output) ) {
      case 'browser':
        $mime = image_type_to_mime_type($info[2]);
        header("Content-type: $mime");
        $output = NULL;
      break;
      case 'file':
        $output = $file;
      break;
      case 'return':
        return $image_resized;
      break;
      default:
      break;
    }
 
    switch ( $info[2] ) {
      case IMAGETYPE_GIF:
        imagegif($image_resized, $output);
      break;
      case IMAGETYPE_JPEG:
        imagejpeg($image_resized, $output);
      break;
      case IMAGETYPE_PNG:
        imagepng($image_resized, $output);
      break;
      default:
        return false;
    }
 
    return true;
}

function resize($destfile,$width,$height,$name){
    $dest	= explode("/",$destfile);
    for($i=0;$i<count($dest)-1;$i++){
		$pathx	.= $dest[$i]."/";
    }
    $pathx	= $path.$pathx;
    $filename 	= $dest[count($dest)-1];
    $file_new	= $pathx.smallname($filename,$name);
    smart_resize_image( $destfile, $width,$height, false, $file_new, false, false );
}

function sendSMS($to,$sms){
GLOBAL $conn, $apihost;

	$data	= array('to' => $to, 'sms' => $sms);
	error_log(
			date("Y-m-d H:i:s") .  print_r($data, 1). "\n",
			3, '/tmp/sms_out.log'
		);	
	$parameter	= "to=".urlencode($to)."&text=".urlencode($sms);
	$return 	= @file_get_contents($apihost['sms']."&$parameter");
	/*save API response to log_externals*/
	$conn->Execute("INSERT INTO log_externals VALUES('','#trxID#', NULL, '0',NOW(),'$url_api','".$parameter."','".$return."','status','trxidext','-')");
}

function pushSMS($id){
GLOBAL $conn;
	$rs	= $conn->Execute("SELECT * FROM pushes WHERE id='$id'");	
	$time		= $rs->fields['time_pushed'];
	$time_now	= date('Y-m-d H:i:s');

	if($time_now >= $time && $rs->fields['fg_push']!=1){#di kirim skrg
		$x	= explode(',',$rs->fields['outlet_user_id']);
		foreach($x as $k => $v){
			$rso	= $conn->Execute("SELECT name, msisdn FROM users WHERE id='".$v."'");
			$rsi	= $conn->Execute("INSERT INTO push_reports VALUES('','$id','$v','".$rso->fields['msisdn']."','".$rs->fields['message']."','SENT','$time')");
			sendSMS($rso->fields['msisdn'],$rs->fields['message']);
		}
		$conn->Execute("UPDATE pushes SET fg_push='1' WHERE id='$id'");
	}
}

function get_user_name_by_id($id){
GLOBAL $conn;
	$rs	= $conn->Execute("SELECT name FROM users WHERE id='$id'");
	return $rs->fields['name'];
}

function get_paket_by_id($id){
GLOBAL $conn;
	$rs	= $conn->Execute("SELECT title FROM promo_distributors WHERE id='$id'");
	return $rs->fields['title'];
}

function get_telco_by_id($id){
GLOBAL $conn;
	$rs	= $conn->Execute("SELECT telco FROM telcos WHERE id='$id'");
	return $rs->fields['telco'];
}

function get_service_type_by_id($id){
GLOBAL $conn;
	$rs	= $conn->Execute("SELECT inventory_service_type FROM inventory_service_types WHERE id='$id'");
	return $rs->fields['inventory_service_type'];
}

function get_denom_by_id($id){
GLOBAL $conn;
	$rs	= $conn->Execute("SELECT pulsa_denom FROM pulsa_denoms WHERE id='$id'");
	return $rs->fields['pulsa_denom'];
}

function get_trx_status_by_id($id){
GLOBAL $conn;
	$rs	= $conn->Execute("SELECT trx_status FROM trx_status WHERE id='$id'");
	return $rs->fields['trx_status'];
}

function get_inventoryname_by_table_and_id($table,$inventoryid){
#echo $table."$inventoryid";
GLOBAL $conn, $field_table,$jenis_inventory;
	$item	= $jenis_inventory[$table].": ";
	$rsx	= $conn->Execute("SELECT ".$field_table[$table]." FROM ".$table." WHERE id='".$inventoryid."'");
  	if($table=='inventory_services'){
  		$rsit	= $conn->Execute("SELECT inventory_service_type FROM inventory_service_types WHERE id='".$rsx->fields[$field_table[$table]]."'");
  		$item	= $rsit->fields['inventory_service_type'];
  	}else{
  		$item	.= $rsx->fields[$field_table[$table]];
  	}  
  	return $item;
}

function get_product_by_id($id){
GLOBAL $conn;
	$rs	= $conn->Execute("SELECT name FROM products WHERE id='$id'");
	return $rs->fields['name'];
}

function get_menu_by_id($id){
GLOBAL $conn;
	$rs	= $conn->Execute("SELECT menu FROM menus WHERE id='$id'");
	return $rs->fields['menu'];
}

function get_submenu_by_id($id){
GLOBAL $conn;
	$rs	= $conn->Execute("SELECT submenu FROM submenus WHERE id='$id'");
	return $rs->fields['submenu'];
}


function get_pradefined_form_by_id($id){
GLOBAL $conn;
	$rs	= $conn->Execute("SELECT pradefined_form FROM pradefined_forms WHERE id='$id'");
	return $rs->fields['pradefined_form'];
}

function get_network_by_id($id){
GLOBAL $conn;
	$rs	= $conn->Execute("SELECT network FROM networks WHERE id='$id'");
	return $rs->fields['network'];
}

function get_program_by_id($id){
GLOBAL $conn;
	$rs	= $conn->Execute("SELECT program FROM programs WHERE id='$id'");
	return $rs->fields['program'];
}

/*spesific for API*/
function genTrxID($rsu){
	return $rsu->fields['area_id'].$rsu->fields['sales_area_id'].$rsu->fields['cluster_id'].mktime();
}

function get_dompetku_outlet_by_id($userid){
GLOBAL $conn;
	$rs	= $conn->Execute("SELECT msisdn FROM user_dompetkus WHERE user_id='$userid' AND fg_status='1' AND fg_default='1'");	
	return $rs->fields['msisdn'];
}

function get_msisdn_transfer_dompetku($type,$outletuserid){
	#echo "$type,$outletuserid | ";
	switch($type){
		case 'outlet':
			return get_dompetku_outlet_by_id($outletuserid);
			break;
		case 'disti':
			return get_dompetku_disti_by_outletuserid($outletuserid);
			break;	
		case 'customer':case 'indosat':
			return '';
			break;
	}
	
}

function get_dompetku_disti_by_outletuserid($outletuserid){
GLOBAL $conn;
	$sql="SELECT a.msisdn FROM user_dompetkus a LEFT JOIN users b ON a.user_id=b.parent_user_id WHERE b.id='$outletuserid' AND a.fg_status='1' AND a.fg_default='1'";
	#echo $sql;
	$rs	= $conn->Execute($sql);	
	return $rs->fields['msisdn'];
}

function savetoInternalLog($data){
GLOBAL $conn, $_POST, $_SERVER;
	$host		= $_SERVER['REQUEST_URI'];
	$parameter	= safeSQL(json_encode($_POST));
	$response	= safeSQL(json_encode($data));
	$ip			= $_SERVER['REMOTE_ADDR'];
	$api_status	= $data['status'];
	$sql	= "INSERT INTO log_internals VALUES ('','".$_POST['user_id']."',NOW(),'$host','$parameter','$response','$ip','$api_status')";
	#echo $sql."<hr>";
	$conn->Execute($sql);
}

function get_biaya_adm_by_program_id($id){
GLOBAL $conn;
	$sql	= "SELECT SUM(`value`) AS jml FROM program_transfers WHERE program_id='".$id."' AND `from`='outlet' AND `to`='indosat'";
	$rs	= $conn->Execute($sql);
	$total	= $rs->fields['jml'];
	$rs	= $conn->Execute("SELECT SUM(`value`) AS jml FROM program_transfers WHERE program_id='".$id."' AND `from`='indosat' AND `to`='indosat'");
	$bagibagi	= $rs->fields['jml'];
	$adm = $total-$bagibagi>0 ? $total-$bagibagi : 0;
	return $adm;
}

function generate_kode_konfirmasi_sms(){
	//$characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
	$characters = '0123456789';
	$string = '';
	for ($i = 0; $i < 8; $i++) {
		$string .= $characters[rand(0, strlen($characters) - 1)];
	}
	return $string;
}

function get_program_detail($id){
GLOBAL $conn;
	$rs	= $conn->Execute("SELECT * FROM programs WHERE fg_status='1' AND id='".$id."'");
	if($rs->RecordCount()>0){
		$data['program']	= $rs->fields;
		$data['field']		= array();
		$data['metadata']	= array();
		$rsc	= $conn->Execute("SELECT * FROM program_customfields WHERE program_id='".$id."' ORDER BY urutan ASC");
		if($rsc->RecordCount()>0){
			$i=0;
			while (!$rsc->EOF){
				#$rsc->fields['option']	= str_replace(' ','',$rsc->fields['option']);
				$data['field']['step'.$rsc->fields['show_on_step']][]	= $rsc->fields;
				$rsc->MoveNext();$i++;
			}
		}
		$rsm	= $conn->Execute("SELECT * FROM program_metadatas WHERE program_id='".$id."'");
		if($rsm->RecordCount()>0){
			$i=0;
			while (!$rsm->EOF){
				$data['metadata'][$i]	= $rsm->fields;
				$rsm->MoveNext();$i++;
			}
		}
		/*
		$rst	= $conn->Execute("SELECT * FROM program_transfers WHERE program_id='".$id."'");
		if($rst->RecordCount()>0){
			$i=0;
			while (!$rst->EOF){
				$data['transfer'][$i]	= $rst->fields;
				$rst->MoveNext();$i++;
			}
		}*/
		return $data;
	}else{
		return false;
	}
}

function jsoncallback($data){
GLOBAL $_GET;
	if($_GET['jsoncallback']){	
		header('Access-Control-Allow-Origin: *');
    	header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
    	header('Access-Control-Max-Age: 1000');
		header('Access-Control-Allow-Headers: Content-Type');
  		header('Content-type: application/json');
		return $_GET['jsoncallback'] . '(' . json_encode($data) . ');';
	}else{
		return json_encode($data);
	}
}



function url_shortener($url){
	return @file_get_contents("http://wq.lt/api?url=$url");
}

function microtime_float(){
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

function tembak($url){
	#echo $url;
	$cache_file	= 'cache/'.urlencode($url);
	if (file_exists($cache_file)) {
	   // Cache file is less than five minutes old. 
	   // Don't bother refreshing, just use the file as-is.
	   $file = file_get_contents($cache_file);
	} else {
	   // Our cache is out-of-date, so load the data from our remote server,
	   // and also save it over our cache for next time.
	   $file = file_get_contents($url);
	   #file_put_contents($cache_file, $file);
	    $fh = fopen($cache_file, 'w') or die("can't open file");
		if(fwrite($fh, $file)){
		  #echo "sukses write";
		}else{
		  #echo "gagal write";
		}
		fclose($fh);
	}
	return $file;
}
/*custom function*/


$time_start = microtime_float();
session_start();
?>
