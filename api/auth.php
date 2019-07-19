<?php
//this retains Oauth for sites spotify profile where playlist will be published

//access token expires after set ammount of time, this stores the refresh token that allows us to get new access tokens as they
//are needed
	$info = array(
		'refresh_token' => 'AQDav4JRFtK3mepZWNizXJcwINeMh0QroYAjq0ythB1kbArnA0sTeqq_AUqTbKZ75U27_6J7RNe4mdnq2NymCDwmTP0AHNUKixo6RoIbtc2a7xcdPzJ9JIJWQQIn0rknST2ABA',
		'grant_type' => 'refresh_token'
	);
	
//initializes connection with spotify api and retreives new access token
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://accounts.spotify.com/api/token");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLINFO_HEADER_OUT, true);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($info));
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/x-www-form-urlencoded',
				'Authorization: Basic Mjc4ZTVkMDFjMmNlNDRhMzg1OGU0ZTM4ODM2YmU0NmI6NzI0M2NkNzNhNWNkNDRlMTk0NDE0Njk3ZGQ3MzUxZTE=')
	);
	
	$data = curl_exec($ch);
	$err = curl_error($ch);
	curl_close($ch);
	
	if ($err) {
		returnWithError($err);
	} else {
		returnWithInfo($data);
	}
	
	function returnWithError( $err )
	{
		sendResultInfoAsJson( $err);
	}
	
	function returnWithInfo( $data )
	{
		sendResultInfoAsJson( $data );
	}
	
	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		$grab = json_decode($obj);
		echo json_encode($grab->access_token);
	}
?>