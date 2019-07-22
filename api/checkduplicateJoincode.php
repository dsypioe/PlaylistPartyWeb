<?php
	//this script checks if a joincode has already been generated, ensures that joincodes are unique to room
	
	$inData = getRequestInfo();
	
	// connects to database and checks if the joincode already exist
	$conn = new mysqli("localhost", "v3ksrrem0t05", "#Ijsda914", "PlaylistParty");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		$sql = $conn->query("SELECT joincode FROM Room WHERE joincode='" . $inData["joincode"] . "'");
		if($sql->num_rows == 0) {
			joincodeAvailable();
		} 
		else {
			joincodeUsed();
		}
		$conn->close();
	}
	
	// get json data
	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}

    // joincode has not been used, returns status = 200 signifying okay to use
	function joincodeAvailable()
	{
		header('Content-type: application/json');
		$retValue = '{"status":"200"}';
		sendResultInfoAsJson( $retValue );
	}
	
	 // joincode has been used, returns status = 400 signifying not okay to use
	function joincodeUsed()
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