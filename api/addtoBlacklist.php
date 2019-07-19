<?php
	$inData = getRequestInfo();
	
	$roomid = $inData["roomid"];
	$songid = $inData["songid"];
	
	// connects to database and inserts data into room table for created room
	$conn = new mysqli("localhost", "v3ksrrem0t05", "#Ijsda914", "PlaylistParty");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		$sql = "INSERT INTO Blacklist (roomid, songid) VALUES ('" . $roomid . "','" . $songid . "')";
		if( $result = $conn->query($sql) != TRUE )
		{
			returnWithError( $conn->error );
		}
		$conn->close();
	}
	
	returnWithError("");
	
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
	
	// return json with error message
	function returnWithError( $err )
	{
		$retValue = '{"error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
?>