<?php
//this script creates a new room in the database and returns the room id

	$inData = getRequestInfo();
	
	$joincode = $inData["joincode"];
	$playlistid = $inData["playlistid"];
	
	// connects to database and inserts data into room table for created room
	$conn = new mysqli("localhost", "v3ksrrem0t05", "#Ijsda914", "PlaylistParty");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		$sql = "INSERT INTO Room (playlistid, joincode) VALUES ('" . $playlistid . "','" . $joincode . "')";
		if( $result = $conn->query($sql) != TRUE )
		{
			returnWithError( $conn->error );
		}
		// Get data if contact data belongs to user.
		$getroomid = "SELECT id FROM Room WHERE joincode= '" . $inData["joincode"] . "' and playlistid= '" . $inData["playlistid"] . "'"; 
		$result2 = $conn->query($getroomid);
		
		if ($result2->num_rows > 0)
		{
            $row = $result->fetch_assoc();
            $id = $row["id"]; 
            
			returnWithInfo($id);
		}
		
		// no matching user row in database
		else
		{
			returnWithError( "No Records Found" );
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
		$retValue = '{"error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
	// return json with blank error field
	function returnWithInfo( $id )
	{
		$retValue =   '{"id":' . $id . ',"error":""}';
		sendResultInfoAsJson( $retValue );
	}
?>