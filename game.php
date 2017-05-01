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
    set_to_session('user_data', $user_data);
    load_user_world_staff_to_session();
    return true;
}

/**
 * logout current user
 */
function logout(){
    if(check_user_in_session()){
        backup_user_world_staff();
        backup_user_data();

        unset_from_session('user_data');
        unset_from_session('world_staff');
        unset_from_session('world');
    }
}

/**
 * process the 'go' command
 * @param $room_id
 */
function process_go_command($room_id){
    $usr = get_current_usr();
    $usr['room_id']=$room_id;
    set_to_session('user_data',$usr);
    backup_user_data();
}

/**
 * process remove command
 * @param $item_id
 */
function process_remove_item_from_inventory($item_id){
    $usr = get_current_usr();
    //$usr['inventory_staff_ids'] = array_diff($usr['inventory_staff_ids'],array($item_id));
    if (false !== $key = array_search($item_id, $usr['inventory_staff_ids'])) {
        unset($usr['inventory_staff_ids'][$key]);
    }
    set_to_session('user_data',$usr);
    add_item_to_world_staff($usr['room_id'],$item_id);
    backup_user_world_staff();
    backup_user_data();
}

/**
 * process take command
 * @param $item_id
 */
function process_add_item_to_inventory($item_id){
    $usr = get_current_usr();
    array_push($usr['inventory_staff_ids'],$item_id);
    set_to_session('user_data',$usr);
    remove_item_from_world_staff($usr['room_id'],$item_id);
    backup_user_world_staff();
    backup_user_data();
}

/**
 * process the world display data request
 */
function process_map_request(){
    header("Content-type : application/json");
    echo get_world_display_params();
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
                exit();
                break;
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