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
    if($user_name=='default') {
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
 * process the fight request
 * @param $mob_id
 */
function process_fight_request($mob_id){
    simulate_fight($mob_id);
}

/**
 * handle and process the request params to control
 * the content of the page, this method in fact handles
 * all the requests of this project
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
            case 'fight':
                process_fight_request($_GET['mob']);
                break;
            case 'use':
                use_single_time_item($_GET['item']);
                break;
            case 'map':
                process_map_request();
                return;
            case 'where':
                process_where_request();
                return;
            case 'end':
                set_user_settings_to_default();
                logout();
                header("Location:view/login.php?status=".($_GET['status']));
                return;
            default:
                echo file_get_contents("view/error.html");
        }
    }

    if(!check_user_in_session()){
        header("Location:view/login.php?status=login");
    } else {
        header("Location:view/main.php");
    }
    exit();
}