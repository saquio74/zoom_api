<?php
include_once 'Zoom_api.php';
include_once 'Persona.php';

$zoom_meeting = new Zoom_api();
#se define informacion del meeting para luego ser pasada a funcion createMetting
$data = array();
$data['topic']      = "example meeting";
$data['start_date'] = date("Y-m-d h:i:s", strtotime('today'));
$data['duration']   = 30;
$data['type']       = 2;
$data['password']   = '123456';
try {
	#aqui se llama a la funcion con los datos
	// $response = $zoom_meeting->createMeeting($data);
	// echo "<pre>";
	// print_r($response);
	// echo "<pre>";
	# Se crea un array para modificar participantes, con cancel se borra del listado
	# Con deny se banea de la lista
	$registrants = array();
	$registrants['action']= "cancel";
	#se crea el objeto persona para pasarlo por parametro a la funcion actionRestrant
	$user = new Persona();
	$user->id = 'qwj3-UX2SmGT1im5eO3UJw';
	$user->first_name = 'Pedro Guananja';
	$user->email = 'pedroguananja@gmail.com';
	$user->auto_approve = true;
	$meetID = 81267023709;
	// $registrants['registrants'] = array();
	// array_push($registrants['registrants'],$user);
	//print_r( json_encode($registrants));

	# los parametros que recibe son el ID del meeting, en caso de post para autorizar usuario
	# se debe pasar un objeto que con auto_approve en true para que se agregue al listado de
	# restrantes, con parametros obligatorios email y nombre
	###
	# en caso de put se pasa un objeto que tiene un atributo action que donde se pasa cancel o deny 
	# y otro atributo que es un array de objetos 
	# usuarios que tienen como parametro obligatorio el id y un email
	$cancelRegistrants = $zoom_meeting->actionRegistrant($meetID,"POST",$user);

	# en esta linea se agrega el id de una reunion ya terminada, la cual devuelve los participantes
	# de dicha reunion
	$meetInfo = $zoom_meeting->listRegistrants($meetID);

	echo "<pre>";
	print_r($cancelRegistrants);
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