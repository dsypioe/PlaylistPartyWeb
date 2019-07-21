<?php
	//this script removes the song in question form the playlist table
	
	$inData = getRequestInfo();
	$roomid = $inData["roomid"];
	$songid = $inData["songid"];
	
	// connects to database and gets playlist info
	$conn = new mysqli("localhost", "v3ksrrem0t05", "#Ijsda914", "PlaylistParty");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
        $sql = "DELETE FROM Playlist WHERE roomid= '" . $inData["roomid"] . "' and songid= '" . $inData["songid"] . "'"; 
		if( $result = $conn->query($sql) != TRUE )
		{
			returnWithError( $conn->error );
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
	
	//if song is removed, returns status of 200
	function returnSuccess()
	{
		$retValue = '{"status":"200"}';
		sendResultInfoAsJson( $retValue );
	}
	
	// return json with error message
	function returnWithError( $err )
	{
		$retValue = json_encode($err);
		sendResultInfoAsJson( $retValue, JSON_FORCE_OBJECT);
	}
	
	// send json 
	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}
?>