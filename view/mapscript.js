
var xhttp = new XMLHttpRequest();
xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        fill_map(JSON.parse(xhttp.responseText));
    }
};
xhttp.open("GET", "../game.php?command=map",true);
xhttp.send();


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