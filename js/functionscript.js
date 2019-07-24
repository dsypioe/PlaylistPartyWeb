var token = "";//holds the access token for the apps spotify account
var usertoken = "";//if user want to add from there own playlist, holds the user access token
var playlistname = "";//when creating a room, this is the playlistname entered by the host
var hostplaylist = "";//this holds the spotify playlist id for the rooms playlist
var mergesong;//when adding a song to the playlist, this will hold the entire track object for the song to be added
var delsong;//this just holds the spotify id for the song to be deleted from the playlist
var roomid;//this holds the room id for the room, is used in a lot of our API calls
var joincode = "";//holds the joincode to display to user to share with others to join room
var siteplaylist_url = "https://api.spotify.com/v1/users/314tn8lrwdckwt5nj3i214u2b/playlists";//the url used to publish playlist on apps spotify account

//calls needed functions on landing page load
function landingstart()
{
	checksiteAccess();
	readimplicitAccess();
}

//calls needed funtions on home page load
function homestart()
{
	checksiteAccess();
	readimplicitAccess();
	readsessionPHP();
}

/*
//checks sites authorization token for site spotify account, called at beginning of every function
function checksiteAccess()
{
    var retreive = new XMLHttpRequest();
	retreive.open("GET", siteplaylist_url, false);
	retreive.setRequestHeader("Authorization", "Bearer "+token);
	retreive.send();
    var obj = JSON.parse(retreive.status);
    
    if (obj !== 200)
    {
        getnewToken();
    }
}
*/

//retrieves new token as needed for sites spotify account
function checksiteAccess()
{
	var retreive = new XMLHttpRequest();
	retreive.open("GET", "http://www.playlistparty.live/api/auth.php", false);
	retreive.setRequestHeader("Content-type", "application/json");
	retreive.send();
	var obj = JSON.parse(retreive.responseText);
	token = obj.token;
}

//this grabs the entered playlist name from the host when creating a room
function namePlaylist()
{
	playlistname = document.getElementById('playlistname').value;
}

//this creates the room 
function createRoom()
{
	checksiteAccess();
	
	//this creates a random 5 character string used as the code to join the room
	//before being used, we check our database to see if the joincode already exist, or if it can be used
	var valid = 1;
	while (valid !== 200)
	{
		var joinstring = '';
		var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz123456789';
		var charactersLength = characters.length;
		for ( var i = 0; i < 5; i++ ) {
			joinstring += characters.charAt(Math.floor(Math.random() * charactersLength));
		}
		valid = checkduplicatejoincodePHP(joinstring);
	}
	
	var params = {
		"name":playlistname,
	};
	
	//sends post request to create playlist with entered name on spotify
	var retreive = new XMLHttpRequest();
	retreive.open("POST", siteplaylist_url, false);
	retreive.setRequestHeader("Content-Type", "application/json");
	retreive.setRequestHeader("Authorization", "Bearer "+token);
	retreive.send(JSON.stringify(params));
	var obj = JSON.parse(retreive.responseText);
	
	//retreive the playlist id to store for room
	hostplaylist = obj.id;
	
	//creates the room on our databse and returns the roomid
	createroomPHP(joinstring);
	
	//writes session variables
	writesessionPHP();
}

//this is what allows a user to join a room
function joinRoom()
{
	//grabs the join code entered by the user
	var joinstring = document.getElementById('joinstring').value;
	
	//checks if the join code is valid, if it is 200 will be returned to error. otherwise invalid join code
	var error = joinRoomPHP(joinstring);
	
	if(error !== 200)
	{
		//error here
	}
	else{
		//join worked fine
		writesessionPHP();
	}
}

//this adds a song to the playlist
function addSong()
{
	checksiteAccess();
	
	//first check if song has been blacklisted or if it is a duplicate
	var blacklist = checkblacklistPHP(mergesong.id);
	var dupeplaylist = checkduplicateplaylistPHP(mergesong.id);
	if (blacklist !== 200){
		//song is blacklisted, do stuff
		return;
	}
	if (dupeplaylist !== 200){
		//song is a duplicate, do stuff
		return;
	}
	
	var params = {
		"uris":["spotify:track:"+mergesong.id],
	};
	
	//sends post request to create playlist with entered name on spotify
	var retreive = new XMLHttpRequest();
	retreive.open("POST", "https://api.spotify.com/v1/playlists/"+hostplaylist+"/tracks", false);
	retreive.setRequestHeader("Content-Type", "application/json");
	retreive.setRequestHeader("Authorization", "Bearer "+token);
	retreive.send(JSON.stringify(params));
	var obj = JSON.parse(retreive.responseText);
	var stat = JSON.parse(retreive.status);
	
	if (stat !== 201)
    {
        //apparently spotify is down
    }
	else
	{
		//upon success adding song to spotify playlist, adds to our playlist table for site use
		addtoplaylistPHP(mergesong.id, mergesong.name, mergesong.artists, mergesong.album.images[2].url);
	}
}

//this removes a song from the playlist
function removeSong()
{
	checksiteAccess();
	
	var params = {
		"tracks":[{"uri": "spotify:track:"+delsong}],
	};
	
	//sends post request to create playlist with entered name on spotify
	var retreive = new XMLHttpRequest();
	retreive.open("DELETE", "https://api.spotify.com/v1/playlists/"+hostplaylist+"/tracks", false);
	retreive.setRequestHeader("Content-Type", "application/json");
	retreive.setRequestHeader("Authorization", "Bearer "+token);
	retreive.send(JSON.stringify(params));
	var obj = JSON.parse(retreive.responseText);
	var stat = JSON.parse(retreive.status);
	
	if (stat !== 200)
    {
        //spotify is down again
    }
	else
	{
		//song has been removed from spotify playlist, is added to blacklist and removed from playlist table
		addtoblacklistPHP(delsong);
		removefromplaylistPHP(delsong);
	}
}

//reads the implicit token from the redirect and hides it from the url
function readimplicitAccess()
{
	// Get the hash of the url
	const hash = window.location.hash
	.substring(1)
	.split('&')
	.reduce(function (initial, item) {
		if (item) {
			var parts = item.split('=');
			initial[parts[0]] = decodeURIComponent(parts[1]);
		}
	return initial;
	}, {});
	window.location.hash = '';

	// Set token
	let _token = hash.access_token;
	usertoken = _token;
	
	//sends request to spotify to grab users playlist, which they will be able to scan through or add as whole to host
	var retreive = new XMLHttpRequest();
	retreive.open("GET", "https://api.spotify.com/v1/me/playlists", false);
	retreive.setRequestHeader("Content-type", "application/json");
	retreive.setRequestHeader("Authorization", "Bearer "+usertoken);
	retreive.send();
	var obj = JSON.parse(retreive.responseText);
	var userplaylist = obj;
}

//allows implicit access from users spotify account
function getimplicitAccess()
{
	const authEndpoint = 'https://accounts.spotify.com/authorize';
	const clientId = '278e5d01c2ce44a3858e4e38836be46b';
	const redirectUri = 'http://www.playlistparty.live';

	window.location = `${authEndpoint}?client_id=${clientId}&redirect_uri=${redirectUri}&scope=playlist-read-collaborative%20playlist-read-private%20user-library-read%20user-read-recently-played%20user-top-read&response_type=token&show_dialog=true`;
}

//functions for calls to our API

//this is the server script to create a room
function createroomPHP(joinstring)
{
	var params = {
		"joincode":joinstring,
		"playlistid":hostplaylist,
		"playlistname":playlistname,
	};
	
	var retreive = new XMLHttpRequest();
	retreive.open("POST", "http://www.playlistparty.live/api/createRoom.php", false);
	retreive.setRequestHeader("Content-Type", "application/json");
	retreive.send(JSON.stringify(params));
	var obj = JSON.parse(retreive.responseText);
	
	//when room is created, the roomid is returned and used for many future calls to our API
	roomid = obj.id;
}

//this is the server script for joining a room
function joinRoomPHP(joinstring)
{
	var params = {
		"joincode":joinstring,
	};
	
	var retreive = new XMLHttpRequest();
	retreive.open("POST", "http://www.playlistparty.live/api/joinRoom.php", false);
	retreive.setRequestHeader("Content-Type", "application/json");
	retreive.send(JSON.stringify(params));
	var obj = JSON.parse(retreive.responseText);
	
	roomid = obj.id;
	hostplaylist = obj.playlistid;
	playlistname = obj.playlistname;
	
	//returns 200 if successful join, return 400 if not
	return obj.status;
}

//this is the server script for adding a song to the blacklist
function addtoblacklistPHP(songid)
{
	var params = {
		"roomid":roomid,
		"songid":songid,
	};
	
	var retreive = new XMLHttpRequest();
	retreive.open("POST", "http://www.playlistparty.live/api/addtoBlacklist.php", false);
	retreive.setRequestHeader("Content-Type", "application/json");
	retreive.send(JSON.stringify(params));
	var obj = JSON.parse(retreive.responseText);
}

//this is the server script for checking if a song is in the blacklist
function checkblacklistPHP(songid)
{
	var params = {
		"roomid":roomid,
		"songid":songid,
	};
	
	var retreive = new XMLHttpRequest();
	retreive.open("POST", "http://www.playlistparty.live/api/checkBlacklist.php", false);
	retreive.setRequestHeader("Content-Type", "application/json");
	retreive.send(JSON.stringify(params));
	var obj = JSON.parse(retreive.responseText);
	
	//if 200 is returned song is not blacklisted, if 400 is returned song is blacklisted
	return obj.status;
}

//this is the server script for adding song info to the playlist table
function addtoplaylistPHP(songid)
{
	var params = {
		"roomid":roomid, 
		"songid":songid, 
	};
	
	var retreive = new XMLHttpRequest();
	retreive.open("POST", "http://www.playlistparty.live/api/addtoPlaylist.php", false);
	retreive.setRequestHeader("Content-Type", "application/json");
	retreive.send(JSON.stringify(params));
	var obj = JSON.parse(retreive.responseText);
}

//this is the server script for checking if a song is already in the playlist
function checkduplicateplaylistPHP(songid)
{
	var params = {
		"roomid":roomid,
		"songid":songid,
	};
	
	var retreive = new XMLHttpRequest();
	retreive.open("POST", "http://www.playlistparty.live/api/checkduplicatePlaylist.php", false);
	retreive.setRequestHeader("Content-Type", "application/json");
	retreive.send(JSON.stringify(params));
	var obj = JSON.parse(retreive.responseText);
	
	//if 200 is returned song is not in playlist, if 400 is returned song is already in playlist
	return obj.status;
}

//this is the server script for retreiving the current playlist info
function getplaylistinfoPHP()
{
	var params = {
		"playlistid":hostplaylist,
		"token":token,
	};
	
	var retreive = new XMLHttpRequest();
	retreive.open("POST", "http://www.playlistparty.live/api/getplaylistInfo.php", false);
	retreive.setRequestHeader("Content-Type", "application/json");
	retreive.send(JSON.stringify(params));
	var obj = JSON.parse(retreive.responseText);
	
	//returns a mess of info, will work on parsing it correctly
	var playlistlength = obj.items.length;
	for(var i = 0; i < playlistlength; i++)
	{
		var songname = obj.items[i].track.name;
		var songartist = obj.items[i].track.artists[0].name;
		var songalbum = obj.items[i].track.album.name;
		var albumimage = obj.items[i].track.album.images[2];
		
		//this should grab everything that is needed, just needs to be displayed on table
	}
}

//this is the server script for removing a song from the playlist table
function removefromplaylistPHP(songid)
{
	var params = {
		"roomid":roomid,
		"songid":songid,
	};
	
	var retreive = new XMLHttpRequest();
	retreive.open("POST", "http://www.playlistparty.live/api/removefromPlaylist.php", false);
	retreive.setRequestHeader("Content-Type", "application/json");
	retreive.send(JSON.stringify(params));
	var obj = JSON.parse(retreive.responseText);
}

//this is the server script for checking if a generated joincode is already being used
function checkduplicatejoincodePHP(joinstring)
{
	var params = {
		"joincode":joinstring,
	};
	
	var retreive = new XMLHttpRequest();
	retreive.open("POST", "http://www.playlistparty.live/api/checkduplicateJoincode.php", false);
	retreive.setRequestHeader("Content-Type", "application/json");
	retreive.send(JSON.stringify(params));
	var obj = JSON.parse(retreive.responseText);
	
	//if 200 is returned join code is okay to use, if 400 is returned joincode can not be used
	return obj.status;
}

//this function calls our close rom API, deleteing all data associated with the current room id
function closeroomPHP()
{
	var params = {
		"roomid":roomid,
	};
	
	var retreive = new XMLHttpRequest();
	retreive.open("POST", "http://www.playlistparty.live/api/closeRoom.php", false);
	retreive.setRequestHeader("Content-Type", "application/json");
	retreive.send(JSON.stringify(params));
	var obj = JSON.parse(retreive.responseText);
}

//this writes the needed session variables from the landing page
function writesessionPHP()
{
	var payload = '{"roomid" : "' + roomid + '", "joincode" : "' + joincode + '", "playlistid" : "' + hostplaylist + '", "playlistname" : "' + playlistname + '"}';
	var retreive = new XMLHttpRequest();
	retreive.open("POST", "http://www.playlistparty.live/api/writeSession.php", false);
	retreive.setRequestHeader("Content-Type", "application/json");
	retreive.send(payload);
}

//this retreives the needed seesion variables for the homepage
function readsessionPHP()
{
	var payload = '{"roomid" : "' + roomid + '", "joincode" : "' + joincode + '", "playlistid" : "' + hostplaylist + '"}';
	var retreive = new XMLHttpRequest();
	retreive.open("POST", "http://www.playlistparty.live/api/readSession.php", false);
	retreive.setRequestHeader("Content-Type", "application/json");
	retreive.send(payload);
	var obj = JSON.parse(retreive.responseText);
	
	roomid = obj.roomid;
	joincode = obj.joincode;
	hostplaylist = obj.playlistid;
	playlistname = obj.playlistname;
}