<?php
	//this script checks if a song has been blacklisted
	
	$inData = getRequestInfo();
	
	// connects to database and checks if the song has been blacklisted
	$conn = new mysqli("localhost", "v3ksrrem0t05", "#Ijsda914", "PlaylistParty");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		$sql = "SELECT songid FROM Blacklist where roomid='" . $inData["roomid"] . "' and  songid='" . $inData["songid"] . "'";
		if( $result = $conn->query($sql) != TRUE )
		{
			songNotBlacklisted();
		}
		else{
			songIsBlacklisted();
		}
		$conn->close();
	}
	
	// get json data
	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}

    // song is not blacklisted, returns status = 200 signifying okay to add
	function songNotBlacklisted()
	{
		header('Content-type: application/json');
		$retValue = '{"status":"200"}';
		sendResultInfoAsJson( $retValue );
	}
	
	 // song is blacklisted, returns status = 400 signifying not okay to add
	function songIsBlacklisted()
	{
		header('Content-type: application/json');
		$retValue = '{"status":"400"}';
		sendResultInfoAsJson( $retValue );
	}
	
	// return json with error message
	function returnWithError( $err )
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