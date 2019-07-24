package com.example.playlistparty

import android.util.Log
import android.widget.Toast
import khttp.responses.Response
import khttp.delete
import khttp.get
import khttp.post
import kotlinx.android.synthetic.main.activity_join.*
import org.json.JSONObject
import java.io.Serializable

class OurAPI : Serializable {
    var token : String = ""
    var playlistid : String = ""
    var playlistname : String = ""
    var joincode : String = ""
    var roomid : String = ""
    var songid : String = ""
    var songname : String = ""
    var artistname : String = ""
    var albumart : String = ""
    val siteplaylist_url = "https://api.spotify.com/v1/users/314tn8lrwdckwt5nj3i214u2b/playlists"

    // the following are for communicating with our API
    fun auth() {
        val response : Response = get("http://www.playlistparty.live/api/auth.php")
        val obj : JSONObject = response.jsonObject
        token = obj.optString("token")

        Log.i("Responses: ", response.text)
    }

    fun createRoom() {
        val response = post(
            url = "http://www.playlistparty.live/api/createRoom.php",
            json = mapOf("playlistid" to playlistid, "joincode" to joincode,
                "playlistname" to playlistname
            ),
            headers = mapOf("Content-type" to "application/json")
        )

        roomid = response.text

        Log.i("Responses: ", response.text)
    }

    fun joinRoom() : Int {
        val response = post(
            url = "http://www.playlistparty.live/api/joinRoom.php",
            json = mapOf("joincode" to joincode),
            headers = mapOf("Content-type" to "application/json")
        )

        val obj : JSONObject = response.jsonObject
        roomid = obj.optString("id")
        playlistid = obj.optString("playlistid")
        playlistname = obj.optString("playlistname")

        Log.i("Responses: ", response.text)

        return response.statusCode
    }

    fun addtoBlacklist() {
        val response = post(
            url = "http://www.playlistparty.live/api/addtoBlacklist.php",
            json = mapOf("roomid" to roomid, "songid" to songid),
            headers = mapOf("Content-type" to "application/json")
        )
    }

    fun checkBlacklist() {
        val response = post(
            url = "http://www.playlistparty.live/api/checkBlacklist.php",
            json = mapOf("roomid" to roomid, "songid" to songid),
            headers = mapOf("Content-type" to "application/json")
        )
    }

    fun addtoPlaylist() {
        val response = post(
            url = "http://www.playlistparty.live/api/addtoPlaylist.php",
            json = mapOf(
                "roomid" to roomid,
                "songid" to songid,
                "songname" to songname,
                "artist" to artistname,
                "artlink" to albumart
            ),
            headers = mapOf("Content-type" to "application/json")
        )
    }

    fun checkDuplicatePlaylist() {
        val response = post(
            url = "http://www.playlistparty.live/api/checkduplicatePlaylist.php",
            json = mapOf("roomid" to roomid, "songid" to songid),
            headers = mapOf("Content-type" to "application/json")
        )
    }

    fun getPlaylistInfo() {
        val response = post(
            url = "http://www.playlistparty.live/api/getplaylistInfo.php",
            json = mapOf("roomid" to roomid),
            headers = mapOf("Content-type" to "application/json")
        )
    }

    fun removeFromPlaylist() {
        val response = post(
            url = "http://www.playlistparty.live/api/removefromPlaylist.php",
            json = mapOf("roomid" to roomid, "songid" to songid),
            headers = mapOf("Content-type" to "application/json")
        )

        val obj : JSONObject = response.jsonObject
    }

    fun checkDuplicateJoincode() {
        val response = post(
            url = "http://www.playlistparty.live/api/checkduplicateJoincode.php",
            json = mapOf("joincode" to joincode),
            headers = mapOf("Content-type" to "application/json")
        )
    }

    // the following are for calls to spotify API

    // create a playlist on our apps spotify account
    fun createPlaylist() {
        val response = post(
            url = siteplaylist_url,
            json = mapOf("name" to playlistname),
            headers = mapOf("Content-type" to "application/json", "Authorization" to "Bearer $token")
        )

        val obj = response.jsonObject
        playlistid = obj.optString("id")

        Log.i("Responses: ", response.text)
    }

    // add a song to spotify playlist
    fun addSong(playlistid : String, songid : String, token : String) {
        val response = post(
            url = "https://api.spotify.com/v1/playlists/$playlistid/tracks",
            json = mapOf("uris" to "spotify:track:$songid"),
            headers = mapOf("Content-type" to "application/json", "Authorization" to "Bearer $token")
        )
    }

    // remove a song to spotify playlist
    fun deleteSong(playlistid: String, songid: String, token: String) {
        val payload = mapOf("uri" to "spotify:track:$songid")
        val response = delete(
            url = "https://api.spotify.com/v1/playlists/$playlistid/tracks",
            json = mapOf("tracks" to payload),
            headers = mapOf("Content-type" to "application/json", "Authorization" to "Bearer $token")
        )
    }

}