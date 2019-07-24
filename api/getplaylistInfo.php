<?php
	
	$inData = getRequestInfo();
	$playlistid = $inData["playlistid"];
	$token = $inData["token"];
	$url = "https://api.spotify.com/v1/playlists/".$playlistid."/tracks";
	
	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => $url,
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "GET",
	  CURLOPT_HTTPHEADER => array(
		'Authorization: Bearer '.$token,
	  ),
	));

	$response = curl_exec($curl);
	$err = curl_error($curl);
	curl_close($curl);

	if ($err) {
	  echo "error:" . $err;
	} else {
	  returnWithInfo( $response );
	}
	
	// get json data
	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}
	
	function returnWithInfo( $data )
	{
		/*
		$info = json_decode($data, true);
		//$info2 = $info->items;
		foreach($info->items as $obj) 
		{
			$songname = $obj->track->name; 
            $artist = $obj->track->artist->0->name; 
            $artlink = $obj->track->album->images->2;
			$albumname = $obj->track->album->name;
			
			$playlistinfo[] = array($songname, $artist, $artlink, $albumname);
		}
		
		$playlist = json_encode($playlistinfo);
		$retValue = json_encode(array('item' => $playlist), JSON_FORCE_OBJECT);
		sendResultInfoAsJson( $retValue );
		//*/
		sendResultInfoAsJson( $data);
	}
	
	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}
?>