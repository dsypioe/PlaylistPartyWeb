var token = "";
var usertoken = "";
var hostplaylist = "";
var mergesong = "";
var mergeplaylist;
var siteplaylist_url = "https://api.spotify.com/v1/users/314tn8lrwdckwt5nj3i214u2b/playlists";

//calls needed functions on page load
function start()
{
	checksiteAccess();
	readimplicitAccess();
}

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

//retrieves new token as needed for sites spotify account
function getnewToken()
{
	var retreive = new XMLHttpRequest();
	retreive.open("GET", "http://www.playlistparty.live/api/auth.php", false);
	retreive.setRequestHeader("Content-type", "application/json");
	retreive.send();
	var obj = JSON.parse(retreive.responseText);
	token = obj;
}

//creates a new playlist on sites spotify account (this will be host playlist for a room)
function createplaylist()
{
	var name = document.getElementById('playlistid').value;
	checksiteAccess();
	
	var params = {
		"name":name,
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
	//document.getElementById("hostplaylist_info").innerHTML = "created playlist "+name+" with id "+hostplaylist;
}

function addSong()
{
	checksiteAccess();
	
	var params = {
		"uris":"spotify:track:"+mergesong,
	};
	
	//sends post request to create playlist with entered name on spotify
	var retreive = new XMLHttpRequest();
	retreive.open("POST", "https://api.spotify.com/v1/playlists/"+hostplaylist+"/tracks", false);
	retreive.setRequestHeader("Content-Type", "application/json");
	retreive.setRequestHeader("Authorization", "Bearer "+token);
	retreive.send(JSON.stringify(params));
	var obj = JSON.parse(retreive.responseText);
	
	if (obj !== 201)
    {
        //place error return here
    }
	else
	{
		//otherwise song has been added to playlist
	}
}

function addPlaylist()
{
	checksiteAccess();
}

function removeSong()
{
	checksiteAccess();
	
	var params = {
		"tracks":[{"uri": "spotify:"+mergesong}],
	};
	
	//sends post request to create playlist with entered name on spotify
	var retreive = new XMLHttpRequest();
	retreive.open("DELETE", "https://api.spotify.com/v1/playlists/"+hostplaylist+"/tracks", false);
	retreive.setRequestHeader("Content-Type", "application/json");
	retreive.setRequestHeader("Authorization", "Bearer "+token);
	retreive.send(JSON.stringify(params));
	var obj = JSON.parse(retreive.responseText);
	
	if (obj !== 200)
    {
        //place error return here
    }
	else
	{
		//otherwise song has been removed from playlist
	}
}

//search for tracks to add to host playlist
function search()
{
	var searchval = document.getElementById('searchid').value;
	checksiteAccess();
	
	var searchurl = "https://api.spotify.com/v1/search?q="+searchval+"&type=track,artist,album&market=from_token";
	
	//sends a get request to spotify for the search value entered
	var retreive = new XMLHttpRequest();
	retreive.open("GET", searchurl, false);
	retreive.setRequestHeader("Content-type", "application/json");
	retreive.setRequestHeader("Authorization", "Bearer "+token);
	retreive.send();
	var obj = JSON.parse(retreive.responseText);
	
	//spotify holds data for tracks, artists, and albums. These are just the whole JSON objects.
	//To access specific parts of each must use .items then call the specific data
	//an example being obj.tracks.items[0].name , which would return the name of the first track to come up when searching
	var tracks = JSON.stringify(obj.tracks);
	var artists = JSON.stringify(obj.artists);
	var albums = JSON.stringify(obj.albums);
	
	//this just prints out the entire JSON object of what tracks come up from the search query. for testing
	//document.getElementById("searchinfo").innerHTML = tracks;
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
	
	//document.getElementById("userplaylist").innerHTML = JSON.stringify(userplaylist);
}

//allows implicit access from users spotify account to pull up their playlist/liked songs
function getimplicitAccess()
{
	const authEndpoint = 'https://accounts.spotify.com/authorize';

	// Replace with your app's client ID, redirect URI and desired scopes
	const clientId = '278e5d01c2ce44a3858e4e38836be46b';
	const redirectUri = 'http://www.playlistparty.live';

	window.location = `${authEndpoint}?client_id=${clientId}&redirect_uri=${redirectUri}&scope=playlist-read-collaborative%20playlist-read-private%20user-library-read%20user-read-recently-played%20user-top-read&response_type=token&show_dialog=true`;
}