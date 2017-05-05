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
 * read the world staff from file
 */
function get_world_staff(){
    $user_data = get_current_usr();
    return json_decode(file_get_contents("data/users/".$user_data['name']."/world_staff.json"),true);
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
    $_SESSION['mobs'] = json_decode(file_get_contents("data/mobs.json"),true);
}

/**
 * load the user data to session
 * @param $user_data
 */
function load_user_data_to_session($user_data){
    $_SESSION['user_data'] = $user_data;
}

/**
 * load the user data to session
 * @param $world_staff
 */
function load_user_world_staff_to_session($world_staff){
    $_SESSION['world_staff'] = $world_staff;
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
 * check if event is the event of the end of the game
 * @param $event - @see world.json ("special_events")
 * @return boolean is the event the end of the game or not
 */
function is_end_event($event){
    return $event['name'] == 'win'? 1: ($event['name'] == 'lose')?0:-1;
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

/**
 * get the list of mobs in the selected room
 * @param $roomId - id of the room
 */
function get_mobs_by_room_id($roomId){
    return get_world_staff_by_room_id($roomId)['mobs'];
}

/**
 * get the mob information from
 * @param $mob_id
 * @return object mob information @see mobs.json
 */
function get_mob_by_id($mob_id){
    for($i=0;$i<count($_SESSION['mobs']);$i++){
        if($_SESSION['mobs'][$i]['id']==$mob_id){
            return $_SESSION['mobs'][$i];
        }
    }
}

/**
 * remove the item from the room of the world
 * @param $room_id
 * @param $mob_id
 */
function remove_mob_from_world_staff($room_id,$mob_id){
    for($i=0;$i<count($_SESSION['world_staff']);$i++){
        if($_SESSION['world_staff'][$i]['room_id']==$room_id){
            $_SESSION['world_staff'][$i]['mobs'] = array_diff($_SESSION['world_staff'][$i]['mobs'],array($mob_id));
            if (false !== $key = array_search($mob_id, $_SESSION['world_staff'][$i]['mobs'])) {
                unset($_SESSION['world_staff'][$i]['mobs'][$key]);
            }
        }
    }
}

/**
 * drop item from user inventory to the current room
 * @param $item_id
 */
function drop_item($item_id){
    $usr = get_current_usr();
    //$usr['inventory_staff_ids'] = array_diff($usr['inventory_staff_ids'],array($item_id));
    if (false !== $key = array_search($item_id, $usr['inventory_staff_ids'])) {
        unset($usr['inventory_staff_ids'][$key]);
    }
    add_item_to_world_staff($usr['room_id'],$item_id);
    load_user_data_to_session($usr);
    backup_user_world_staff();
    backup_user_data();
}

/**
 * pick item from the current room and put to the inventory
 * @param $item_id
 */
function pick_item($item_id){
    $usr = get_current_usr();
    array_push($usr['inventory_staff_ids'],$item_id);
    load_user_data_to_session($usr);
    remove_item_from_world_staff($usr['room_id'],$item_id);
    backup_user_world_staff();
    backup_user_data();
}

/**
 * unset the data from session
 */
function unset_session_variables(){
    unset_from_session('user_data');
    unset_from_session('world_staff');
    unset_from_session('world');
    unset_from_session('staff');
    unset_from_session('mobs');
}

/**
 * put user to the selected room
 * @param $room_id
 */
function put_user_to_room($room_id){
    $usr = get_current_usr();
    $usr['room_id']=$room_id;
    load_user_data_to_session($usr);
    backup_user_data();
}

/**
 * set user settings to default
 */
function set_user_settings_to_default(){
    $user_data = add_new_user(get_current_usr()['name']);

    load_data_to_session();
    load_user_data_to_session($user_data);
    load_user_world_staff_to_session(get_world_staff());
}

/**
 * simulate the fight between user and the mob
 * @param $mob_id
 */
function simulate_fight($mob_id){
    $user = get_current_usr();
    $mob = get_mob_by_id($mob_id);
    while ($user['stats']['health']>0&&$mob['stats']['health']>0){
        $m_dmg = $mob['stats']['attack'] - $user['stats']['defence'];
        if($m_dmg>0) $user['stats']['health']-= $m_dmg;

        $u_dmg = $user['stats']['attack'] - $mob['stats']['defence'];
        if($u_dmg>0) $mob['stats']['health']-= $u_dmg;
    }
    if($mob['stats']['health']<=0){
        remove_mob_from_world_staff($user['room_id'],$mob_id);
    }
    $_SESSION['user_data'] = $user;
    backup_user_data();
}

/**
 * get the inventory items of the user
 * $return array of staff items @see staff.json
 */
function get_user_inventory(){
    $inventory = array();
    $inv_ids = get_current_usr()['inventory_staff_ids'];
    for($i = 0;$i<count($inv_ids);$i++){
        array_push($inventory,get_staff_by_id($inv_ids[$i]));
    }
    return $inventory;
}

/**
 * calculate the bonuses from the inventory items of the user
 * $return array contains ("attack"->value,"defence"->value,"health"->value)
 */
function calculate_inventory_bonuses(){
    $bonuses = array("health"=>0,"defence"=>0,"attack"=>0);
    $inventory = get_user_inventory();
    for($i = 0;$i<count($inventory);$i++){
        if($inventory[$i]['usage']=='static')
        for($j = 0;$j< count($inventory[$i]['effects']);$j++){
            $effect = $inventory[$i]['effects'][$j];
            switch ($effect['property']){
                case 'attack':
                    $bonuses['attack']+=$effect['value'];
                    break;
                case 'defence':
                    $bonuses['defence']+=$effect['value'];
                    break;
                case 'health':
                    $bonuses['health']+=$effect['value'];
                    break;
            }
        }
    }
    return $bonuses;
}
