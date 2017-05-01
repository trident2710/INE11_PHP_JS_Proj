<!DOCTYPE html>
<?php
include_once '../game_utils.php';
if(!check_user_in_session()){
    header("Location:../game.php");
    exit();
}
?>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Main page</title>
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body>
        <script src="mapscript.js"></script>
        <div class="container">
            <header>
                <h1>Bienvenue, <?php echo get_current_usr()['name'];?></h1>
                <form action="/INE11_PHP_JS_Proj/game.php" method="get">
                    <input type="hidden" name="command" value="logout" />
                    <input type="submit" value="Logout" style="font-size: 12px"/>
                </form>
            </header>
            <table class="main">
                <tr>
                    <th class="map">Carte</th>
                    <th class="navigation">Navigation</th>
                    <th class="inventory">Inventaire</th>
                </tr>
                <tr>
                    <td >
                        <div class="content">
                            <canvas id="map" width="300" height="300">

                            </canvas>
                        </div>
                    </td>
                    <td >
                        <div class="content">
                            <?php
                                $room = get_room_by_id(get_current_usr()['room_id']);
                                echo "<h4>Maintenant, vous êtes dans le chambre:</h4>"."<p class='imp_data'>".$room['name']."</p>";
                                echo"<h4>Vous pouvez aller dans les chambres:</h4>";
                                for($i=0;$i<count($room['outs']);$i++){
                                    $id = $room['outs'][$i]['id'];
                                    $name = $room['outs'][$i]['name'];
                                    $room_name = get_room_by_id($id)['name'];
                                    echo "<p><a href=\"/INE11_PHP_JS_Proj/game.php?command=go&room=".$id."\">".$room_name." par ".$name."</a></p>";
                                }
                            ?>
                            <table class="main">
                                <tr>
                                    <th>Inventaire dans ce chambre</th>
                                </tr>
                            </table>
                            <div>
                                <?php
                                    $usr = get_current_usr();
                                    $room_staff = get_world_staff_by_room_id($usr['room_id'])['staff'];
                                    for ($i=0;$i<count($room_staff);$i++){
                                        echo "<div class='link'>".
                                            "<h4 class='link_title'>".get_staff_by_id($room_staff[$i])['name']."  ".
                                            "<a class = 'link_ref' href=\"/INE11_PHP_JS_Proj/game.php?command=take&item=".$room_staff[$i]."\" >"."Prendre"."</a>".
                                            "</h4>".
                                            "</div>";
                                    }

                                ?>
                            </div>
                        </div>
                    </td>
                    <td >
                        <div class="content">
                            <?php
                                if(empty(get_current_usr()['inventory_staff_ids'])){
                                    echo "<p>Vous n'avez aucun inventaire</p>";
                                } else{
                                    echo "<p>Votre inventaire:</p>";
                                    for ($i=0;$i<count(get_current_usr()['inventory_staff_ids']);$i++){
                                        $staff_id = get_current_usr()['inventory_staff_ids'][$i];
                                        echo "<div class='link'>".
                                            "<h4 class='link_title'>".get_staff_by_id($staff_id)['name']."  ".
                                            "<a class = 'link_ref' href=\"/INE11_PHP_JS_Proj/game.php?command=remove&item=".$staff_id."\" >"."  Supprimer"."</a>".
                                            "</h4>".
                                            "</div>";
                                    }
                                }
                            ?>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>