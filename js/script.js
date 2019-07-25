var canvas, ctx, center_x, center_y, radius, bars,
    x_end, y_end, bar_height, bar_width,
    frequency_array, counter;

bars = 250;
bar_width = 2;
counter = 0;

function animationLooper(){

    // set to the size of device
    canvas = document.getElementById("renderer");
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
    ctx = canvas.getContext("2d");

    // find the center of the window
    center_x = canvas.width / 2;
    center_y = canvas.height / 2;
    radius = 200;

    // style the background
    var gradient = ctx.createLinearGradient(0,0,0,canvas.height);
    gradient.addColorStop(0,"#4CAF50");
    gradient.addColorStop(1,"#181818");
    ctx.fillStyle = gradient;
    ctx.fillRect(0,0,canvas.width,canvas.height);

    //draw a circle
    ctx.beginPath();
    ctx.arc(center_x,center_y,radius,0,2*Math.PI);
    ctx.stroke();


      for(var i = 0; i < bars; i++){

        //divide a circle into equal parts
        rads = Math.PI * 2 / bars;

        bar_height = 25 + Math.cos(i*0.075) * 10;

        // set coordinates
        x = center_x + Math.cos(rads * (i + counter)) * (radius);
        y = center_y + Math.sin(rads * (i + counter)) * (radius);
        x_end = center_x + Math.cos(rads * (i + counter))*(radius + bar_height);
        y_end = center_y + Math.sin(rads * (i + counter))*(radius + bar_height);



        //draw a bar
        drawBar(x, y, x_end, y_end, bar_width);
      }

    counter = counter + 1;
    if (counter > 250) {
      counter = 0;
    }
    window.requestAnimationFrame(animationLooper);
}

// for drawing a bar
function drawBar(x1, y1, x2, y2, width){

    var lineColor = "black";

    ctx.strokeStyle = lineColor;
    ctx.lineWidth = width;
    ctx.beginPath();
    ctx.moveTo(x1,y1);
    ctx.lineTo(x2,y2);
    ctx.stroke();
}

function displayBlock(divID) {
  var item = document.getElementById(divID);
  if(item.style.display == 'none')
          item.style.display = 'block';
       else
          item.style.display = 'none';
  }

function displayNone(divID) {
  var item = document.getElementById(divID);
  if(item.style.display == 'block')
          item.style.display = 'none';
       else
          item.style.display = 'block';
}
