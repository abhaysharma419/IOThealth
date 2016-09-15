<?php
require 'Slim/Slim/Slim.php';
require 'NotORM/NotORM.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

$dbhost = 'localhost';
$dbuser = 'root';
$dbpassword = 'password1234';
$dbname = 'iot';
$dbmethod = 'mysql:dbname=';

$dsn = $dbmethod.$dbname;

$pdo = new PDO($dsn,$dbuser,$dbpassword);
$db = new NotORM($pdo);

$app->get('/',  function (){
   echo 'Home - My Slim Application'; 
});

$app->get('/data_pat',  function() use ($app,$db)
{
    $patient = array();
    foreach ($db->details() as $info)
    {
        $detail[] = array(
            'id' => $info['ID'],
            'patientName' => $info['PATIENTNAME'],
            'patientAge' => $info['PATIENTAGE'],
            'relativeContact' => $info['RELATIVECONTACT'],
            'doctorName' => $info['DOCTORNAME'],
	    'address' => $info['ADDRESS'],
            'heartRate' => $info['HEARTRATE']);
    }
    
    $app->response()->header("Content-Type","application/json");
    echo json_encode($detail);
});

$app->put('/up_data/:id', function($id) use($app, $db){
    $app->response()->header("Content-Type", "application/x-www-form-urlencoded");
    $res = $db->details()->where("id", $id);
    if ($res->fetch()) {
        $post = $app->request()->put();
        $result = $res->update($post);
        echo json_encode(array(
            "status" => (bool)$result,
            "message" => "HeartBeat updated successfully"
            ));
    }
    else{
        echo json_encode(array(
            "status" => false,
            "message" => "id $id does not exist"
        ));
    }
});

$app->run();
