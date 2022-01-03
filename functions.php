<?php 

function callURL( $url ) {

	$userAgent = 'Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
	$options   = [
		CURLOPT_CUSTOMREQUEST  => 'GET',       //set request type post or get
		CURLOPT_POST           => false,       //set to GET
		CURLOPT_USERAGENT      => $userAgent,  //set user agent
		CURLOPT_RETURNTRANSFER => true,        // return web page
		CURLOPT_HEADER         => false,       // don't return headers
		CURLOPT_FOLLOWLOCATION => false,       // follow redirects
		CURLOPT_ENCODING       => '',          // handle all encodings
		CURLOPT_AUTOREFERER    => true,        // set referer on redirect
		CURLOPT_CONNECTTIMEOUT => 120,         // timeout on connect
		CURLOPT_TIMEOUT        => 120,         // timeout on response
		CURLOPT_MAXREDIRS      => 10,          // stop after 10 redirects
		CURLOPT_SSL_VERIFYHOST => false,
		CURLOPT_SSL_VERIFYPEER => false,
	];

	$ch = curl_init( $url );
	curl_setopt_array( $ch, $options );
	$response = curl_exec( $ch );
	$err      = curl_errno( $ch );
	$errmsg   = curl_error( $ch );
	curl_close( $ch );

	return json_decode( $response );
}

function load_info_time( $time_id, $rodada ) {

	if ( file_exists( 'rodadas/' . $rodada . '/' . $time_id . '.json' ) ) {
		$rodada_time = file_get_contents( 'rodadas/' . $rodada . '/' . $time_id . '.json' );
		return json_decode( $rodada_time );
	}

	$rodada_time = callURL( 'https://api.cartolafc.globo.com/time/id/' . $time_id . '/' . $rodada  );

	if ( !is_dir('rodadas/' . $rodada ) ) {
		mkdir( 'rodadas/' . $rodada, 0777, true );
	}

	file_put_contents( 'rodadas/' . $rodada . '/' . $time_id . '.json', json_encode( $rodada_time ) );
	sleep(1);

	return $rodada_time;
}


// Calcula o Desvio Padrão de um Array
function stddv( $arr ) {
	$num_of_elements = count($arr);

	$variance = 0.0;
	$average  = array_sum( $arr ) / $num_of_elements;

	foreach ( $arr as $i ) {
		$variance += pow(( $i - $average ), 2);
	}

	return (float) sqrt( $variance / $num_of_elements );
}
