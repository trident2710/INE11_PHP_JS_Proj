<!DOCTYPE html>
<?php
    session_start();
    process_command();


    $world = array(
        array("name" => "chambre Jaune",
            "outs" => array ("porte" => 1,
                "fenêtre" => 2)),
        array("name" => "chambre Verte",
            "outs" => array ("porte" => 0,
                "fenêtre" => 2)),
        array("name" => "jardin",
            "outs" => array ("fenêtre Jaune" => 0,
                "fenêtre verte" => 1))
    );
    

    function default_unlogged_command(){
        echo "<form action=\"http://localhost:63342/INE11_PHP_JS_Proj/game.php\" method=\"GET\">
              <input type=\"hidden\" name=\"command\" value=\"login\" /> 
              <input type=\"text\" id = name title=\"Insert your name\" name=\"name\">
              <label for=\"name\">Insert your name</label>
              <input type=\"submit\"/> 
            </form>";
    }

    function get_current_room_name(){
        return $world[$_SESSION["room"]]["name"];
    }

    function default_logged_command(){
        echo "<h1>"."Bonjour ".get_login()."</h1>".
            "<h6>"."Currently you are in room ".get_current_room_name()."</h6>".
            "<a href=\"http://localhost:63342/INE11_PHP_JS_Proj/game.php?command=logout\">Logout</a>";
    }
    function get_login(){
        return $_SESSION["username"];
    }
    function is_logged_in(){
        return !empty($_SESSION["username"]);
    }

    function login(){
        if(!empty($_GET["name"])){
            $_SESSION["username"] = $_GET["name"];
            $_SESSION["room"] = 0;
            return true;
        }
        return false;
    }

    function logout(){
        unset($_SESSION["username"]);
        unset($_SESSION["room"]);
    }

    function show_message($message){
        echo $message;
    }

    function process_command(){
        if(!empty($_GET["command"])){
            switch ($_GET["command"]){
                case 'login':
                    if(login()) show_message("Logged in successfully");
                    break;
                case 'logout':
                    if(is_logged_in()){
                        logout();
                        show_message("Successfully logged out");
                    } else show_message("User is not logged in");
                    break;
                case 'go':
                    if(is_logged_in()){
                        show_message("Not supported yet");
                    } else show_message("User is not logged in");
                    break;
                case 'take':
                    if(is_logged_in()){
                        show_message("Not supported yet");
                    } else show_message("User is not logged in");
                    break;
                case 'put':
                    if(is_logged_in()){
                        show_message("Not supported yet");
                    } else show_message("User is not logged in");
                    break;
                default:
                    show_message("Unknown command");
            }
        }

        if(!is_logged_in()){
            default_unlogged_command();
        } else default_logged_command();

    }