package com.example.playlistparty

import android.content.Intent
import android.os.Bundle
import android.view.KeyEvent
import android.view.View
import android.widget.EditText
import androidx.appcompat.app.AppCompatActivity
import android.widget.TextView
import kotlinx.android.synthetic.main.activity_create.*
import org.jetbrains.anko.doAsync

class CreateActivity : AppCompatActivity() {

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_create)

        val call = OurAPI()

        var characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890"
        var joinstring = (1..5).map{characters.random()}.joinToString("")
        // TODO: call.checkDuplicateJoincode() conditional

        playlist_name_field.setOnKeyListener(View.OnKeyListener { v, keyCode, event ->
            if (keyCode == KeyEvent.KEYCODE_ENTER && event.action == KeyEvent.ACTION_UP) {
                val intent = Intent(this, HostRoomActivity::class.java)

                var playlistName = playlist_name_field.text.toString()
                call.playlistname = playlistName
                call.joincode = joinstring

                doAsync {
                    call.auth()
                    call.createPlaylist()
                    call.createRoom()
                }

                intent.putExtra("EXTRA_OurAPI", call)
                startActivity(intent)

                return@OnKeyListener true
            }
            false
        })




        /*
        var characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890"
        var joinstring = (1..5).map{characters.random()}.joinToString("")

        // Get the room id from our database
        val roomid = URL("http://playlistparty.live/api/createRoom.php").readText()
         */
    }
}
