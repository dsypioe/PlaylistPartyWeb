<?php
	session_start();
	$roomid = 0;
	$joincode = "ERROR";
	$playlistid = "ERROR";
	$playlistname = "ERROR";
	if(isset($_SESSION["roomid"]))
	{
		$roomid = $_SESSION["roomid"];
	}
	if(isset($_SESSION["joincode"]))
	{
		$joincode = $_SESSION["joincode"];
	}
	if(isset($_SESSION["playlistid"]))
	{
		$playlistid = $_SESSION["playlistid"];
	}
	if(isset($_SESSION["playlistname"]))
	{
		$playlistname = $_SESSION["playlistname"];
	}

    returnWithInfo($roomid, $joincode, $playlistid, $playlistname);
	
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
		$retValue = '{"id":0,"login":"false","error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
    // return json with blank error field
	function returnWithInfo( $idValue, $joinValue, $playlistValue, $playlistnameValue )
	{
		$retValue =   '{"roomid":' . $idValue . ',"joincode":"' . $joinValue . '","playlistid":"' . $playlistValue .'","playlistname":"' . $playlistnameValue .'"}';
		sendResultInfoAsJson( $retValue );
	}
?>