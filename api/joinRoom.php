<?php
//this script is what get calls when someone attempts to join a room, and upon success returns the room id and playlist id
	$inData = getRequestInfo();
	
	//connects to the database and checks if the joincode exist
	$conn = new mysqli("localhost", "v3ksrrem0t05", "#Ijsda914", "PlaylistParty");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		$sql = "SELECT id, joincode, playlistid, playlistname FROM Room where joincode='" . $inData["joincode"] . "' ";
		$result = $conn->query($sql);
		
		if ($result->num_rows > 0)
		{
            $row = $result->fetch_assoc();
            $id = $row["id"]; 
            $playlistid = $row["playlistid"]; 
			$playlistname = $row["playlistname"];
            
			returnWithInfo($id, $playlistid, $playlistname);
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
		$retValue = '{"error":"' . $err . '","status":"400"}';
		sendResultInfoAsJson( $retValue );
	}
	
	// upon success, returns the roomid and the playlist id for the room
	function returnWithInfo( $id, $playlistid, $playlistname )
	{
		$retValue =   '{"id":' . $id . ',"playlistid":"' . $playlistid . '","playlistname":"' . $playlistname . '","error":"","status":"200"}';
		sendResultInfoAsJson( $retValue );
	}
?>