<?php
	// used to save login and id from index page to be retrieved by home page
	session_start();
	$inData = getRequestInfo();
	// save login and id in session variables
	$_SESSION["roomid"] = $inData["roomid"];
	$_SESSION["joincode"] = $inData["joincode"];
	$_SESSION["playlistid"] = $inData["playlistid"];
	$_SESSION["playlistname"] = $inData["playlistname"];

	// get json data
	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}
?>