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
    //$user_data = array("name"=>$user_name,"room_id"=>0,"inventory_staff_ids"=>[]);
    $user_data = json_decode(file_get_contents("data/users/default/user_data.json"),true);
    $user_data['name'] = $user_name;
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

/**
 * check the condition
 * @param $condition - the condition object @see world.json ("condition" object)
 * @return boolean condition is respected or not
 */
function check_condition($condition){
    switch ($condition['value']){
        case 'key':
            $objects_needed = $condition['objects'];
            if(user_has_objects($objects_needed)){
                return true;
            }
            break;
        default:
            //if some unsupported condition
            return false;
    }
}

/**
 * check the set of conditions
 * @see check_condition();
 * @param $conditions - array of conditions
 * @return bool - respected or not
 */
function check_conditions($conditions){
    if($conditions==null) return true;

    for ($i = 0;$i<count($conditions);$i++){
        if(!check_condition($conditions[$i])){
            return false;
        }
    }
    return true;
}

/**
 * check if user has the objects in inventory
 * @param $objects_needed - array of staff items
 * @return boolean user has the objects of not
 */
function user_has_objects($objects_needed){
    $staff = get_current_usr()['inventory_staff_ids'];
    for($i = 0;$i<count($objects_needed);$i++){
        if(!in_array($objects_needed[$i],$staff)) return false;
    }
    return true;
}

/**
 * get the conditions for the specified  room and specified out
 * @param $room_id
 * @param $out_number
 * @return array of conditions
 */
function get_conditions_for_room_id_and_out($room_id,$out_number){
    $room = get_room_by_id($room_id);
    if(array_key_exists('conditions',$room['outs'][$out_number])){
        return $room['outs'][$out_number]['conditions'];
    } else return null;
}

/**
 * check the special events in selected room
 * if exist, execute them
 * @param $room_id - id of the room
 * @return - array i.e. {"name":"event name","message":"event message"}
 */
function execute_special_events($room_id){
    $room = get_room_by_id($room_id);
    $events = array();
    if(array_key_exists('special_events',$room)){
        for ($i=0;$i<count($room['special_events']);$i++){
            array_push($events,execute_event($room['special_events'][$i]));
        }
    }
    return $events;
}

/**
 *  execute the event
 * @param $event - object @see world.json ("special_events")
 * @return array i.e. {"name":"event name","message":"event message"}
 */
function execute_event($event){
    if(!empty($event['effect'])){
        //not supported yet
    }
    return array("name"=>$event['value'],"message"=>$event['message']);
}

/**
 * get the current coordinates of the user
 * @return string in json format i.e {"x":"position x","y":"position y"}
 */
function get_user_coordinates(){
    $coords = get_room_by_id(get_current_usr()['room_id'])['display_params']['points'];
    $x = $coords[0][0]+ ($coords[2][0] - $coords[0][0])/2;
    $y = $coords[0][1]+ ($coords[2][1] - $coords[0][1])/2;
    return json_encode(array("x"=>$x,"y"=>$y));
}