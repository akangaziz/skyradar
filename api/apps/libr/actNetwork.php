<?
function get_tagihan_ppob($type,$ppobid, $userid){
GLOBAL $conn,$url_api, $parameter,$logExtID, $_POST;
	$rs	= $conn->Execute("SELECT * FROM user_dompetkus WHERE user_id='$userid' AND fg_status='1' AND fg_default='1'");	
	if($rs->RecordCount()>0){
		$return		= shoot_check_tagihan_ppob($type,$ppobid, $userid);
		$status		= $return['status'];
		$trxidext	= $return['trxID'];
		/*save API response to log_externals*/
		$conn->Execute("INSERT INTO log_externals VALUES('',NULL, NULL, '2',NOW(),'$url_api','".json_encode($parameter)."','".json_encode($return)."','status','trxidext','-')");
		$logExtID[]	= $conn->Insert_ID();
		if($return['status']==1){
			$data['status']	= 1;
			$data['jmltagihan']	= $return['jmltagihan'];
			$data['info']		= $return['info'];
		}else{
			$data['status']		= 3;
			$data['message']	= "Gagal mendapatkan data tagihan PPOB.";
		}		
	}else{
		$data['status']		= 4;
		$data['message']	= "Anda belum memilikik akun aktif dompetku yang terpairing ke IDDP.";
	}
	return $data;
}
	

function bayar_tagihan_ppob($type,$ppobid, $userid, $pin){
GLOBAL $conn,$url_api, $parameter,$logExtID;
	$rs	= $conn->Execute("SELECT * FROM user_dompetkus WHERE user_id='$userid' AND fg_status='1' AND fg_default='1'");	
	if($rs->RecordCount()>0){
		$return		= shoot_bayar_tagihan_ppob($type,$ppobid, $userid,$pin);
		$status		= $return['status'];
		$trxidext	= $return['trxID'];
		/*save API response to log_externals*/
		$conn->Execute("INSERT INTO log_externals VALUES('','#trxID#', NULL, '2',NOW(),'$url_api','".json_encode($parameter)."','".json_encode($return)."','status','trxidext','-')");
		$logExtID[]	= $conn->Insert_ID();
		if($return['status']==1){
			$data['status']	= 1;
			$data['jmltagihan']	= $return['jmltagihan'];
			$data['info']		= $return['info'];
		}else{
			$data['status']		= 2;
			$data['message']	= "Gagal membayar tagihan PPOB.";
		}		
	}else{
		$data['status']		= 0;
		$data['message']	= "Anda belum memilikik akun aktif dompetku yang terpairing ke IDDP.";
	}
	return $data;
}

function dompetku_transfer($msisdnfrom,$pinfrom,$msisdnto,$jml){
GLOBAL $conn,$url_api, $parameter,$logExtID;
	/*save API response to log_externals*/
	$return		= shoot_dompetku_transfer($msisdnfrom,$pinfrom,$msisdnto,$jml);
	$status		= $return['status'];
	$trxidext	= $return['trxID'];
	/*save API response to log_externals*/
	$conn->Execute("INSERT INTO log_externals VALUES('',NULL, NULL, '1',NOW(),'$url_api','".json_encode($parameter)."','".json_encode($return)."','status','trxidext','-')");
	$logExtID[]	= $conn->Insert_ID();
	#debug($logExtID);
	
}

function registrasi_dompetku($data){
GLOBAL $conn,$url_api, $parameter,$logExtID;
	/*save API response to log_externals*/
	$return		= shoot_registrasi_dompetku($data);
	$status		= $return['status'];
	$trxidext	= $return['trxID'];
	/*save API response to log_externals*/
	$conn->Execute("INSERT INTO log_externals VALUES('',NULL, NULL, '1',NOW(),'$url_api','".json_encode($parameter)."','".json_encode($return)."','status','trxidext','-')");
	$logExtID[]	= $conn->Insert_ID();
	#debug($logExtID);
	
}

function get_token_pln(){
GLOBAL $conn, $_POST;
	return rand(111111,999999);
}

function shoot_dompetku_transfer($msisdnfrom,$pinfrom,$msisdnto,$jml){
GLOBAL $url_api, $parameter;
	/*simulasi tembak API dompetku transfer*/
	$url_api	= "http://blablabla.indosat.com/dompetku/transfer";
	$parameter	= array('from' => $msisdnfrom, 'pinfrom' => $pinfrom, 'to' => $msisdnto, 'jml' => $jml);
	/*start tembak*/
	
	/*end tembak*/
	$return		= array('trxID' => mktime(),'status' => '1', 'jml' => $jml);
	return $return;
}

function shoot_registrasi_dompetku($data){
GLOBAL $url_api, $parameter;
	/*simulasi tembak API dompetku transfer*/
	$url_api	= "http://blablabla.indosat.com/dompetku/registrasi";
	$parameter	= $data;
	/*start tembak*/
	
	/*end tembak*/
	$return		= array('trxID' => mktime(),'status' => '1', 'jml' => $jml);
	return $return;
}

function shoot_check_tagihan_ppob($type,$ppobid, $userid){
GLOBAL $url_api, $parameter;
	/*simulasi tembak API PPOB cek tagihan*/
	$url_api	= "http://blablabla.indosat.com/ppob/cektagihan";
	$parameter	= array('type' => $type, 'msisdn' => $rs->fields['msisdn']);
	/*start tembak*/
	
	/*end tembak*/
	if($type=='plntoken'){
		$info	= "Nama: Enang Kurniawan <br> Kelas Daya: 1300watt.";
	}else{
		$info	= 'Nama: Richard Baihaki<br>Alamat: Jl Pangestu 5 No 3 Jakarta Selatan';
	}
	$return		= array('trxID' => mktime(),'status' => '1', 'jmltagihan' => rand(200000,500000), 'info' => $info);
	return $return;
}

function shoot_bayar_tagihan_ppob($type,$ppobid, $userid){
GLOBAL $url_api, $parameter;
	/*simulasi tembak API PPOB bayar tagihan*/
	$url_api	= "http://blablabla.indosat.com/ppob/bayartagihan";
	$parameter	= array('type' => $type, 'msisdn' => $rs->fields['msisdn'], 'pin' => $pindompetku);
	/*start tembak*/
	
	/*end tembak*/
	$return		= array('trxID' => mktime(),'status' => '1', 'jmltagihan' => rand(200000,500000), 'message' => 'berhasil');
	return $return;
}


?>