package com.example.playlistparty

import androidx.appcompat.app.AppCompatActivity
import android.os.Bundle

class HostRoomActivity : AppCompatActivity() {

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_host_room)

        val call = intent.extras?.getSerializable("EXTRA_OurAPI") as OurAPI
    }
}
