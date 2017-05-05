
var map_request = new XMLHttpRequest();
map_request.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        fill_map(JSON.parse(map_request.responseText));

        user_request.open("GET", "../game.php?command=where",true);
        user_request.send();
    }
};
map_request.open("GET", "../game.php?command=map",true);
map_request.send();


var user_request = new XMLHttpRequest();
user_request.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        display_user_position(JSON.parse(user_request.responseText));
    }
};



/**
 * fill the map using the coordinates of the rooms
 * @param map_data -json data @see world.json("display_params")
 */
var fill_map = function (map_data) {
    var map = document.getElementById('map');
    var ctx = map.getContext("2d");
    for(i = 0;i<map_data.length;i++){
        var h = map_data[i]['points'][2][0] - map_data[i]['points'][0][0];
        var l = map_data[i]['points'][2][1] - map_data[i]['points'][0][1];
        var color = map_data[i]['color'];
        ctx.fillStyle = color;
        ctx.fillRect(map_data[i]['points'][0][0],map_data[i]['points'][0][1],h,l);
    }
};

/**
 * display the user current position on the map
 * @param user_data - json object @see game_utils.php get_user_coordinates
 */
var display_user_position = function(user_data){
    var map = document.getElementById('map');
    var context = map.getContext("2d");
    context.beginPath();
    var img = new Image;
    img.src = "hero.png";
    img.onload = function(){
        context.drawImage(img,user_data['x']-25, user_data['y']-25); // Or at whatever offset you like
    };
    //context.drawImage(document.getElementById("hero"),10,10);
    // context.arc(user_data['x'], user_data['y'], 10, 0, 2 * Math.PI, false);
    // context.fillStyle = 'white';
    // context.fill();
    // context.lineWidth = 5;
    // context.strokeStyle = '#003300';
    // context.stroke();
};
