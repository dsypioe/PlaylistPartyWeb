package com.example.playlistparty

import android.content.Intent
import androidx.appcompat.app.AppCompatActivity
import android.os.Bundle
import android.view.KeyEvent
import android.view.View
import android.widget.Toast
import kotlinx.android.synthetic.main.activity_join.*
import org.jetbrains.anko.doAsync
import org.jetbrains.anko.onComplete

class JoinActivity : AppCompatActivity() {

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_join)

        val call = OurAPI()

        room_key_field.setOnKeyListener(View.OnKeyListener { v, keyCode, event ->
            // On key listener, listening for the enter key
            if (keyCode == KeyEvent.KEYCODE_ENTER && event.action == KeyEvent.ACTION_UP) {
                val intent = Intent(this, RoomActivity::class.java)

                var joinCode = room_key_field.text.toString()
                call.joincode = joinCode


                doAsync {
                    val status = call.joinRoom()
                    /* TODO: error for nonexistent room
                    onComplete {
                        if (status == 200) {
                            intent.putExtra("EXTRA_OurAPI", call)

                            startActivity(intent)
                        } else {
                            Toast.makeText(this@JoinActivity, "Room does not exist.", Toast.LENGTH_SHORT).show()
                            room_key_field.text.clear()
                        }
                    }
                    */
                }

                intent.putExtra("EXTRA_OurAPI", call)

                startActivity(intent)

                return@OnKeyListener true
            }
            false
        })
    }
}
