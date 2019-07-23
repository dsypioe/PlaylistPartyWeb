<?php
	//this script gets the information of the room playlist, can be used instead of direct calls to spotify api
	
	$inData = getRequestInfo();
	
	// connects to database and gets playlist info
	$conn = new mysqli("localhost", "v3ksrrem0t05", "#Ijsda914", "PlaylistParty");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		$sql = "SELECT songname, songid, artist, artlink FROM Playlist WHERE roomid= '" . $inData["roomid"] . "'"; 
		$result = $conn->query($sql);
		
		if ($result->num_rows > 0)
		{
            // Add contact to array.
            while($row = $result->fetch_assoc())
            {
                $songname = $row["songname"]; 
                $songid = $row["songid"]; 
                $artist = $row["artist"]; 
                $artlink = $row["artlink"];
                
                $playlistArray = array('songname'=>$songname, 'songid'=>$songid, 'artist'=>$artist, 'artlink'=>$artlink);
            }

			returnWithInfo($playlistArray);
		}
		
		//there are no songs in playlist
		else
		{
			returnWithError( $errorArray );
		}
		$conn->close();
	}
	
	// get json data
	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}
	
	// return json with error message
	function returnWithError( $err )
	{
		$retValue = json_encode($err);
		sendResultInfoAsJson( $retValue, JSON_FORCE_OBJECT);
	}

    // return json with data
	function returnWithInfo( $dataArray )
	{
		$retValue = json_encode(array('item' => $dataArray), JSON_FORCE_OBJECT);
		sendResultInfoAsJson( $retValue );
	}
	
	// send json, which will be a json object called item, with subitems songname, songid, artist, and artlink 
	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}
?>