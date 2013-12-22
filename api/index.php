<?php
require_once 'vendor/flight/Flight.php';
require_once 'lib/entity_ns.php';
require_once 'lib/action_ns.php';
require_once 'lib/user_ns.php';
require_once 'lib/KLogger.php';
//require 'lib/webdraclib.php';


//Flight::register('webdraclib', 'WebdracLib', array('localhost','root','','webdrac'));
Flight::register('Entity',  'WebDrac\Entity');
Flight::register('Action',  'WebDrac\Action');
Flight::register('User',    'WebDrac\User');

// 2 type of environments
// - DEV
// - PROD

Flight::set('env','DEV');

// Allow from any origin
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}
/*
    need : Authenticate the users :
    url : /authenticate/$username/$password
    return value : HTTP CODE 200 or 401
*/
    
Flight::route('/user/authenticate/@username/@password', function($username,$password){
    //Initializing
    $user = Flight::User();

    //Checks and response
    if($user->authenticate($username,$password))
    {
        echo "flightok";
        Flight::halt(200, 'You rocks !');
    }else{

        if(Flight::get('env') != 'DEV')
        {
            usleep(rand(100,1000)*1000);        
        }
        echo "flightko";
        Flight::halt(403, 'authentication failed');
    }
 
});

Flight::route('GET /user/find/@partial_name', function($partial_name){
    //Initializing
    $o_user = Flight::User();
    

    //Checks and response
    Flight::json($o_user->find($partial_name));
});

/*
    need : Create or update data
    url : /data/createorupdate/
    body : attributes js array
    return value : 200/401 http response
*/
Flight::route('/data/createorupdate/', function(){
    $log = KLogger::instance('logs/', KLogger::DEBUG);

    //Initializing
    $entity = Flight::Entity();

    if(Flight::request()->body == "")
    {
        Flight::halt(401, 'Nothing to parse !');
    }
    $data = json_decode(Flight::request()->body);
    $entity->ConstructwithContext($data->application_name,$data->object); 

    //Checks and response
    $rtn = $entity->createOrUpdate($data);
    //$log->logDebug('Rtn ! : '.$rtn);

    if($rtn['status'] = 'ok')
    { 
        echo json_encode($rtn);
       // Flight::halt(200, $rtn['value']);
    }else
    {
        Flight::halt(401, 'Data update failed');
    }

});

/*
    need : Get datas
    url : /data/@application/@entity/
    body : filters
    return value : datas to display
*/
Flight::route('/data/', function(){
    //Initializing
    if(Flight::request()->body == "")
    {
        Flight::halt(401, 'Nothing to parse !');
    }
    $data = json_decode(Flight::request()->body);
    var_dump($data);
    $element = Flight::Entity();
    $element->ConstructwithContext($data->application,$data->object); 

    //Checks and response
    Flight::json($element->get($data->id));
});
/*
Flight::route('GET /data/@application/@entity', function($application,$entity){
    //Initializing
    $element = Flight::Entity();
    $element->ConstructwithContext($application,$entity); 

    //Checks and response
    Flight::json($element->get_all(Flight::request()->body));
});
*/

/*
    need : Get datas
    url : /data/
    body : filters
    return value : datas to display

*/
Flight::route('POST /data/@application/@object/@list', function($application,$object,$list){
    //Initializing
    $element = Flight::Entity();
    $element->ConstructwithContext($application,$object); 

    if(Flight::request()->body == "")
    {
        Flight::halt(401, 'Nothing to parse !');
    }
    $body = json_decode(Flight::request()->body);
    Flight::json($element->getListWithFilters($list,$body));
});

Flight::route('GET /action/@application/@entity/@object_id/@action', function($application,$entity,$object_id,$action){
    //Initializing
    $o_action = Flight::Action();
    $o_action->ConstructwithContext($application,$entity); 

    //Checks and response
    Flight::json($o_action->perform($object_id,$action));
});

/*
Flight::route('GET /parameters',function ()
{
    $json = file_get_contents('');
$data = json_decode($json, TRUE);
})
*/

Flight::start();
?>
