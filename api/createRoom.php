<?php
//this script creates a new room in the database and returns the room id

	$inData = getRequestInfo();
	
	$joincode = $inData["joincode"];
	$playlistid = $inData["playlistid"];
	$playlistname = $inData["playlistname"];
	
	// connects to database and inserts data into room table for created room
	$conn = new mysqli("localhost", "v3ksrrem0t05", "#Ijsda914", "PlaylistParty");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		$sql = "INSERT INTO Room (playlistid, joincode, playlistname) VALUES ('" . $playlistid . "','" . $joincode . "','" . $playlistname . "')";
		if( $result = $conn->query($sql) != TRUE )
		{
			returnWithError( $conn->error );
		}
		else{
			$last_id = $conn->insert_id;
			returnWithInfo($last_id);
		}
		$conn->close();
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
	
	// return json with error message
	function returnWithError( $err )
	{
		$retValue = '{"error":"' . $err . '","status":"400"}';
		sendResultInfoAsJson( $retValue );
	}
	
	// upon successful creation of room, returns the room id 
	function returnWithInfo( $id )
	{
		$retValue =   '{"id":' . $id . ',"status":"200"}';
		sendResultInfoAsJson( $retValue );
	}
?>