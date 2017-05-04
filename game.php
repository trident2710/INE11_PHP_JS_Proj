<?php
include_once 'game_utils.php';
handle_request();

/**
 * login the user with the selected name
 * @param $user_name
 * @return boolean if login succeeded
 */
function login($user_name){
    if(empty($user_name)) {
        return false;
    }
    $user_data = get_user_data($user_name);
    if($user_data==null){
       $user_data = add_new_user($user_name);
    }
    load_data_to_session();
    load_user_data_to_session($user_data);
    load_user_world_staff_to_session(get_world_staff());
    return true;
}

/**
 * logout current user
 */
function logout(){
    if(check_user_in_session()){
        backup_user_world_staff();
        backup_user_data();
        unset_session_variables();
    }
}

/**
 * process the 'go' command
 * @param $room_id
 */
function process_go_command($room_id){
    put_user_to_room($room_id);
}

/**
 * process remove command
 * @param $item_id
 */
function process_remove_item_from_inventory($item_id){
    drop_item($item_id);
}

/**
 * process take command
 * @param $item_id
 */
function process_add_item_to_inventory($item_id){
    pick_item($item_id);
}

/**
 * process the world display data request
 */
function process_map_request(){
    header("Content-type: application/json");
    header("HTTP/1.1 200 OK");
    echo get_world_display_params();
}

/**
 * process the where (the player on map) request
 */
function process_where_request(){
    header("Content-type: application/json");
    header("HTTP/1.1 200 OK");
    echo get_user_coordinates();
}

/**
 * process the end of the game request
 * @param $status_param - the request param indicates with which status the
 * game has ended
 */
function process_end_of_the_game($status_param){
    header("Content-type: application/json");

    if(empty($status_param)){
        header("HTTP/1.1 400 Bad Request");
        echo json_encode("{\"status\":\"error\"}");
        return;
    }
    if($status_param=='win'||$status_param=='lose'){
        set_user_settings_to_default();
        header("HTTP/1.1 200 OK");
        echo json_encode("{\"status\":\"success\"}");
    } else{
        header("HTTP/1.1 400 Bad Request");
        echo json_encode("{\"status\":\"error\"}");
    }

}
/**
 * handle and process the request params to control
 * the content of the page
 */
function handle_request(){
    if(!empty($_GET["command"])){
        switch ($_GET["command"]){
            case 'login':
                if(!login($_GET['name']))
                    echo file_get_contents("view/error.html");
                break;
            case 'logout':
                logout();
                break;
            case 'go':
                process_go_command($_GET['room']);
                break;
            case 'remove':
                process_remove_item_from_inventory($_GET['item']);
                break;
            case 'take':
                process_add_item_to_inventory($_GET['item']);
                break;
            case 'map':
                process_map_request();
                return;
            case 'where':
                process_where_request();
                return;
            case 'end':
                process_end_of_the_game($_GET['status']);
                return;
            default:
                echo file_get_contents("view/error.html");
        }
    }

    if(!check_user_in_session()){
        echo file_get_contents("view/login.html");
    } else {
        header("Location:view/main.php");
        exit();
    }
}