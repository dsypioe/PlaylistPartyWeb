package com.example.playlistparty

import org.json.*
import java.net.HttpURLConnection
import java.net.URL
import javax.net.ssl.HttpsURLConnection
import kotlin.*

class GenerateRoom {
    fun generateRoomID() : String {
        // First create the join code for the room
        var characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890"
        var joinstring = (1..5).map{characters.random()}.joinToString("")

        // Get the room id from our database
        val roomid = URL("http://playlistparty.live/api/createRoom.php").readText()

        println(roomid)

        return roomid
    }

    fun createPlaylist() : String {
        var playlistID = ""

        return playlistID
    }
}