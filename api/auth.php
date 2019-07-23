<?php
//this retains Oauth for sites spotify profile where playlist will be published
//access token expires after set ammount of time, this stores the refresh token that allows us to get new access tokens as they
//are needed

	//connects with our database and grabs most recent  access token
	$conn = new mysqli("localhost", "v3ksrrem0t05", "#Ijsda914", "PlaylistParty");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		$sql = "SELECT token FROM Auth WHERE id= '1'";
		$result = $conn->query($sql);
		if ($result->num_rows > 0)
		{
            $row = $result->fetch_assoc();
            $token = $row["token"]; 
		}
		$conn->close();
		checkToken($token);
	}
	
	//this checks if the most recent access token is valid
	function checkToken($key)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://api.spotify.com/v1/users/314tn8lrwdckwt5nj3i214u2b/playlists");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER,  array(
					'Authorization: Bearer '.$key
		));
		
		$data = curl_exec($ch);
		$err = curl_error($ch);
		$stat = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		//if the token is not valid, the refresh will be used to grab a new valid access token
		if($stat !== 200)
		{
			getToken();
		}
		//otherwise the access token is valid and returned to the session
		else{
			sendOtherJson($key);
		}
	}
	
	function getToken()
	{
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
	}

	function storekey($newtoken)
	{
		$conn2 = new mysqli("localhost", "v3ksrrem0t05", "#Ijsda914", "PlaylistParty");
		if ($conn2->connect_error) 
		{
			returnWithError( $conn2->connect_error );
		} 
		else
		{
			$sql = "UPDATE Auth SET token = $newtoken WHERE id = 1";
			$result = $conn2->query($sql);
		}
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
		$send = $grab->access_token;
		storekey(json_encode($send));
		echo json_encode($grab->access_token);
	}
	
	function sendOtherJson($obj)
	{
		echo json_encode($obj);
	}
?>