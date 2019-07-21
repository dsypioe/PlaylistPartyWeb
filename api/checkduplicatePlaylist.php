<?php
	//this script checks the playlist for the room to see if a song has already been added to the playlist
	
	$inData = getRequestInfo();
	
	// connects to database and checks if the song in question is already in the playlist
	$conn = new mysqli("localhost", "v3ksrrem0t05", "#Ijsda914", "PlaylistParty");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		$sql = "SELECT songid FROM Playlist (roomid, songid) VALUES ('" . $roomid . "','" . $songid . "')";
		if( $result = $conn->query($sql) != TRUE )
		{
			notDuplicate();
		}
		else{
			isDuplicate();
		}
		$conn->close();
	}
	
	//if song does not exist in playlist, return status 200, meaning it is okay to add to the playlist
	function notDuplicate()
	{
		$retValue = '{"status":"200"}';
		sendResultInfoAsJson( $retValue );
	}
	
	//if song does exist in playlist, return status 400, meaning it is not okay to add to the playlist
	function isDuplicate()
	{
		$retValue = '{"status":"400"}';
		sendResultInfoAsJson( $retValue );
	}
	
	// get json data
	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}
	
	// send json
	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}
?>