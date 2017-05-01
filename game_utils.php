<?php
/**
 * Created by PhpStorm.
 * User: trident
 * Date: 30/04/2017
 * Time: 16:20
 */
session_start();

/**
 * @return bool true, if there is a user data in session
 */
function check_user_in_session(){
    return (!empty($_SESSION['user_data']));
}

/**
 * get the current user data from session
 */
function get_current_usr(){
    return $_SESSION['user_data'];
}

/**
 * get the world data from session
 */
function get_world(){
    return $_SESSION['world'];
}

/**
 * get the world staff for the user
 */
function get_current_world_staff(){
    return $_SESSION['world_staff'];
}

/**
 * check whether user already exists or not
 * @return boolean true if user exists, false if doesn't
 * @param $user_name - name of the user
 */
function check_user_exists($user_name){
    return get_user_data($user_name)!=null;
}


/**
 * get the user data
 * @param $user_name
 * @return null if user does not exist, and the user object if it exists
 */
function get_user_data($user_name){
    if(!file_exists("data/users/".$user_name."/user_data.json"))
        return null;
    return json_decode(file_get_contents("data/users/".$user_name."/user_data.json"),true);

}

/**
 * save new user
 * @param $user_name - the name of the user
 * @return the variable contains the user data, @see users.json
 */
function add_new_user($user_name){
    $user_data = array("name"=>$user_name,"room_id"=>0,"inventory_staff_ids"=>[]);
    mkdir("data/users/".$user_name);
    file_put_contents("data/users/".$user_name."/user_data.json",json_encode($user_data));
    create_user_staff_data($user_name);
    return $user_data;
}

/**
 * create the file with the staff distribution in the world map for the new user
 * @param $user_name - user name
 */
function create_user_staff_data($user_name){
    mkdir("data/users/".$user_name);
    file_put_contents("data/users/".$user_name."/world_staff.json",
        file_get_contents("data/users/default/world_staff.json"));
}

/**
 * get the world staff information for the current user
 * @param $user_name
 * @return object contains user world staff information
 */
function get_user_staff_data($user_name){
    if(!file_exists("data/users/".$user_name."/world_staff.json"))
        return null;
    return json_decode(file_get_contents("data/users/".$user_name."/world_staff.json"),true);

}

/**
 * save the object to the session
 * @param $label - the key of this value
 * @param $object - the value
 */
function set_to_session($label,$object){
    $_SESSION[$label] = $object;
}

/**
 * remove the value from session
 * @param $label - the value key
 */
function unset_from_session($label){
    unset($_SESSION[$label]);
}

/**
 * load the world staff data to session
 */
function load_user_world_staff_to_session(){
    $_SESSION['world_staff'] = json_decode(file_get_contents("data/users/".get_current_usr()['name']."/world_staff.json"),true);
}

/**
 * save the user data to file
 */
function backup_user_data(){
    $user_data = get_current_usr();
    file_put_contents("data/users/".$user_data['name']."/user_data.json",json_encode($user_data));
}

/**
 * save the user world staff to file
 */
function backup_user_world_staff(){
    $user_staff = get_current_world_staff();
    $user_data = get_current_usr();
    file_put_contents("data/users/".$user_data['name']."/world_staff.json",json_encode($user_staff));

}

/**
 * load the data to session
 */
function load_data_to_session(){
    $_SESSION['world'] = json_decode(file_get_contents("data/world.json"),true);
    $_SESSION['staff'] = json_decode(file_get_contents("data/staff.json"),true);
}

/**
 * get the room of the world by id
 * @param $room_id
 * @return object room info, if exists. null if doesn't
 */
function get_room_by_id($room_id){
    for($i=0;$i<count($_SESSION['world']);$i++){
        if($_SESSION['world'][$i]['id']==$room_id){
            return $_SESSION['world'][$i];
        }
    }
    return null;
}

/**
 * get the staff item by id
 * @param $staff_id - staff item id
 * @return object staff item
 */
function get_staff_by_id($staff_id){
    for($i=0;$i<count($_SESSION['staff']);$i++){
        if($_SESSION['staff'][$i]['id']==$staff_id){
            return $_SESSION['staff'][$i];
        }
    }
}


/**
 * get the staff of the room
 * @param $room_id
 * @return object staff of the room
 */
function get_world_staff_by_room_id($room_id){
    for($i=0;$i<count($_SESSION['world_staff']);$i++){
        if($_SESSION['world_staff'][$i]['room_id']==$room_id){
            return $_SESSION['world_staff'][$i];
        }
    }
}

/**
 * remove the item from the room of the world
 * @param $room_id
 * @param $staff_id
 */
function remove_item_from_world_staff($room_id,$staff_id){
    for($i=0;$i<count($_SESSION['world_staff']);$i++){
        if($_SESSION['world_staff'][$i]['room_id']==$room_id){
            $_SESSION['world_staff'][$i]['staff'] = array_diff($_SESSION['world_staff'][$i]['staff'],array($staff_id));
            if (false !== $key = array_search($staff_id, $_SESSION['world_staff'][$i]['staff'])) {
                unset($_SESSION['world_staff'][$i]['staff'][$key]);
            }
        }
    }
}

/**
 * add the item to the staff of the room
 * @param $room_id - id of the room
 * @param $staff_id - if of staff item
 */
function add_item_to_world_staff($room_id,$staff_id){
    for($i=0;$i<count($_SESSION['world_staff']);$i++){
        if($_SESSION['world_staff'][$i]['room_id']==$room_id){
            array_push($_SESSION['world_staff'][$i]['staff'],$staff_id);
        }
    }
}

/**
 * get the wold map display data in json format
 * @return string contains display data JSON. @see world.json field "display_params"
 */
function get_world_display_params(){
    $display_data = array();
    $world = get_world();
    for($i=0;$i<count($world);$i++){
        array_push($display_data,$world[$i]["display_params"]);
    }
    return json_encode($display_data);
}