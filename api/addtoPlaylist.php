<?php
	//this script adds song to playlist table to easily keep info on the tracks in a rooms playlist
	
	$inData = getRequestInfo();
	
	$roomid = $inData["roomid"];
	$songname = $inData["songname"];
	$songid = $inData["songid"];
	$artist = $inData["artist"];
	$artlink = $inData["artlink"];
	
	// connects to database and adds song and info to playlist table
	$conn = new mysqli("localhost", "v3ksrrem0t05", "#Ijsda914", "PlaylistParty");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		$sql = "INSERT INTO Playlist (roomid, songname, songid, artist, artlink) VALUES ('" . $roomid . "','" . $songname . "','" . $songid . "','" . $artist . "','" . $artlink . "')";
		if( $result = $conn->query($sql) != TRUE )
		{
			returnWithError($conn->error);
		}
		else{
			 returnSuccess();
		}
		$conn->close();
	}
	
	// get json data
	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}
	
	// upon success, return status of 200
	function returnSuccess()
	{
		$retValue = '{"status":"200"}';
		sendResultInfoAsJson( $retValue );
	}
	
	// return json with error message
	function returnWithError($err)
	{
		$retValue = '{"error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
	// send json
	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}
?>