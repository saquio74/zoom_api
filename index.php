<?php
include_once 'Zoom_api.php';

$zoom_meeting = new Zoom_api();

$data = array();
$data['topic']      = "example meeting";
$data['start_date'] = date("Y-m-d h:i:s", strtotime('today'));
$data['duration']   = 30;
$data['type']       = 2;
$data['password']   = '123456';
try {
	//$response = $zoom_meeting->createMeeting($data);
	$response = $zoom_meeting->createRoom();
	echo "<pre>";
	print_r($response);
	echo "<pre>";
	$meetInfo = $zoom_meeting->infoMeet(81221617407);

	echo "<pre>";
	print_r($meetInfo);
	echo "<pre>";
	
	// echo "Meeting ID: ". $response->id;
	// echo "<br>";
	// echo "Topic: "	. $response->topic;
	// echo "<br>";
	// echo "Join URL: ". $response->join_url ."<a href='". $response->join_url ."'>Open URL</a>";
	// echo "<br>";
	// echo "Meeting Password: ". $response->password;
    
	
} catch (Exception $ex) {
    echo $ex;
}

?>