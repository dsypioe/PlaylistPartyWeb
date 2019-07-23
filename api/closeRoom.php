<?php
//this script will delete all information associated with the room for our database

	$inData = getRequestInfo();
	$roomid = $inData["roomid"];
	
	$conn = new mysqli("localhost", "v3ksrrem0t05", "#Ijsda914", "PlaylistParty");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		$sql = "DELETE FROM Playlist WHERE roomid= '" . $inData["roomid"] . "'"; 
		if( $result = $conn->query($sql) != TRUE )
		{
			returnWithError( $conn->error );
		}
		else{
			$sql2 = "DELETE FROM Blacklist WHERE roomid= '" . $inData["roomid"] . "'";
			if( $result = $conn->query($sql2) != TRUE )
			{
				returnWithError( $conn->error );
			}
			else{
				$sql3 = "DELETE FROM Room WHERE id= '" . $inData["roomid"] . "'";
				if( $result = $conn->query($sql3) != TRUE )
				{
					returnWithError( $conn->error );
				}
				else{
					returnSuccess();
				}
			}
		}
		$conn->close();
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
	
	// get json data
	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}
?>