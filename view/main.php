<!DOCTYPE html>
<?php
include_once '../game_utils.php';
if(!check_user_in_session()){
    header("Location:../game.php");
    exit();
}

if(!check_user_alive()){
    header("Location:../game.php?command=end&status=dead");
    exit();
}

?>
<html lang="en" xmlns:background-size="http://www.w3.org/1999/xhtml" xmlns:background="http://www.w3.org/1999/xhtml">
    <head>
        <meta charset="UTF-8">
        <title>Main page</title>
        <link rel="stylesheet" type="text/css" href="style.css">

    </head>
    <body>
        <script src="mapscript.js"></script>
        <audio src="sound.mp3" autoplay>
        </audio>
        <div class="container">
            <header>
                <h1>Bienvenue, <?php echo get_current_usr()['name'];?></h1>
                <form action="../game.php" method="get">
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
                                    $conditions = get_conditions_for_room_id_and_out($room['id'],$i);
                                    if($conditions!=null&&!check_conditions($conditions)){
                                        echo "<p>".$room_name." par ".$name." (fermé)"."</p>";
                                    } else{
                                        echo "<p><a href=\"../game.php?command=go&room=".$id."\">".$room_name." par ".$name."</a></p>";
                                    }
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
                                    echo empty($_SESSION['world_staff']);
                                    for ($i=0;$i<count($room_staff);$i++){
                                        echo "<div class='link'>".
                                            "<h4 class='link_title'>".get_staff_by_id($room_staff[$i])['name']."  ".
                                            "<a class = 'link_ref' href=\"../game.php?command=take&item=".$room_staff[$i]."\" >"."Prendre"."</a>".
                                            "</h4>".
                                            "</div>";
                                    }

                                ?>
                            </div>
                            <table class="main">
                                <tr>
                                    <th>Bêtes dans ce chambre</th>
                                </tr>
                            </table>
                            <div>
                                <?php
                                    $mobs = get_mobs_by_room_id(get_current_usr()['room_id']);
                                    for($i=0;$i<count($mobs);$i++){
                                        echo "<div class='link'>".
                                            "<h4 class='link_title'>".get_mob_by_id($mobs[$i])['name']."  ".
                                            "<a class = 'link_ref' href=\"../game.php?command=fight&mob=".$mobs[$i]."\" >"."Battre"."</a>".
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
                                        $s = "<div class='link'>".
                                            "<h4 class='link_title'>".get_staff_by_id($staff_id)['name']."  ".
                                            "<a class = 'link_ref' href=\"../game.php?command=remove&item=".$staff_id."\" >"."  Jeter"."</a>";
                                        if(is_single_use_item($staff_id))
                                            $s.="<a class = 'link_ref' href=\"../game.php?command=use&item=".$staff_id."\" >"."  Utiliser"."</a>";
                                        $s.="</h4>"."</div>";
                                        echo $s;
                                    }
                                }
                            ?>
                            <table class="main">
                                <tr>
                                    <th>Information specifique</th>
                                    <p id='info'></p>
                                </tr>
                            </table>
                            <div>
                                <?php
                                    $events = execute_special_events(get_current_usr()['room_id']);
                                    if(!empty($events)){
                                        for ($i=0;$i<count($events);$i++){
                                            echo $events[$i]['message'];
                                            $status = is_end_event($events[$i]);
                                            if($status==1){
                                                header("Location:../game.php?command=end&status=win");
                                                exit();
                                            }
                                            if($status==0){
                                                header("Location:../game.php?command=end&status=lose");
                                                exit();
                                            }
                                        }
                                    }
                                ?>
                            </div>
                            <table class="main">
                                <tr>
                                    <th>Vos Stats:</th>
                                </tr>
                            </table>
                            <div>
                                <?php
                                    $stats = get_current_usr()['stats'];
                                    $bonuses = calculate_inventory_bonuses();
                                    echo "<p> Santé: ".$stats['health']."/".$stats['default_health'].($bonuses['health']>0?"+".$bonuses['health']:" ")."</p>";
                                    echo "<p> Ataque: ".$stats['attack'].($bonuses['attack']>0?"+".$bonuses['attack']:" ")."</p>";
                                    echo "<p> Défence: ".$stats['defence'].($bonuses['defence']>0?"+".$bonuses['defence']:" ")."</p>";
                                ?>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>