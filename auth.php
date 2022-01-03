<?php 

function login_globo() {
	header('Content-type: application/json');

	$email = "leomuniz_muniz@globo.com"; // email da sua conta no cartola fc
	$password = "5525165leo"; // senha da sua conta no cartola fc
	$serviceId = 4728;

	$url = 'https://login.globo.com/api/authentication';

	$jsonAuth = array(
		'captcha' => '',
		'payload' => array(
			'email' => $email,
			'password' => $password,
			'serviceId' => $serviceId
		)
	);

	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json') );
	curl_setopt( $ch, CURLOPT_POST, true );
	curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $jsonAuth ) );
	$result = curl_exec( $ch );

	if ($result === FALSE) {
		die( curl_error( $ch ) );
	}
	curl_close( $ch );

	$parseJson = json_decode( $result, TRUE );

	print_r( $parseJson );

	// if ( $parseJson['id'] == "Authenticated" ){
	// 	$session = array( 'glbId' => $parseJson['glbId'] );
	// 	if( $this->session->set_userdata( $session ) ) {
	// 		return true;
	// 	}
	// } else {
	// 	//redirect( base_url('fail') );
	// }
}

function auth_time($glbId){
	$curl = curl_init('https://api.cartolafc.globo.com/auth/time');
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36');
	curl_setopt($curl, CURLOPT_HTTPHEADER, array('X-GLB-Token:'.$glbId));
	$jsonAuthTime = curl_exec($curl);
	$ArrayAuthTime = json_decode($jsonAuthTime);
	return $ArrayAuthTime;
}


login_globo();
