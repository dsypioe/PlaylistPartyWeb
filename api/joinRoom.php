<?php
//this script is what get calls when someone attempts to join a room, and upon success returns the room id and playlist id
	$inData = getRequestInfo();
	
	$conn = new mysqli("localhost", "v3ksrrem0t05", "#Ijsda914", "PlaylistParty");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		$sql = "SELECT id, joincode, playlistid FROM Room where joincode='" . $inData["joincode"] . "' ";
		$result = $conn->query($sql);
		
		if ($result->num_rows > 0)
		{
            $row = $result->fetch_assoc();
            $id = $row["id"]; 
            $playlistid = $row["playlistid"]; 
            
			returnWithInfo($id, $playlistid);
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
	function returnWithInfo( $id, $playlistid )
	{
		$retValue =   '{"id":' . $id . ',"playlistid":"' . $playlistid . '","error":""}';
		sendResultInfoAsJson( $retValue );
	}
?>