package com.example.playlistparty

import android.os.Bundle
import com.google.android.material.bottomnavigation.BottomNavigationView
import androidx.appcompat.app.AppCompatActivity
import android.widget.TextView

class RoomActivity : AppCompatActivity() {



    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_room)


        val call = intent.extras?.getSerializable("EXTRA_OurAPI") as OurAPI
    }

    private fun openSpotify() {

    }

    private fun openPlaylist(call: OurAPI) {

    }
}
