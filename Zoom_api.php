<?php
require __DIR__ . '/vendor/autoload.php';
use \Firebase\JWT\JWT;

class Zoom_api{
    ## credenciales api_key y api_secret obtenidas en marketplace de zoom en developers
    private $zoom_api_key = "<your api key>";
    private $zoom_api_secret = "<your api secret>";
    private $email = "<your email>";

    //funcion que genera jwt
    public function generateJWT(){
        $key = $this->zoom_api_key;
        $secret = $this->zoom_api_secret;
        $token = array(
            "iss"=>$key,
            //generation date
            'iat'=>time(),
            //expiration
            'exp'=>time()+604800
        );
        return JWT::encode($token,$secret);
    }
    //function create meeting donde se definen las propiedades del meeting
    public function createMeeting($data = array()){
        $post_time = $data['start_date'];
        $start_time = gmdate("Y-m-d\TH:i:s",strtotime($post_time));
        $create_meeting_array =  array();
        $create_meeting_array['topic']      = $data['topic'];
        $create_meeting_array['agenda']     = !empty($data['agenda'])?$data['agenda'] : "";
        $create_meeting_array['type']       = !empty($data['type'])?$data['type'] : "";
        $create_meeting_array['start_time'] = $start_time;
        $create_meeting_array['time_zone']  = 'America/Argentina/Buenos_Aires';
        $create_meeting_array['password']   = !empty($data['password'])?$data['password'] : "";
        $create_meeting_array['duration']   = !empty($data['duration'])?$data['duration'] : "";
        $create_meeting_array['settings']   = array(
            'join_before_host'  =>  !empty($data['join_before_host']) ? true : false,
            'host_video'        =>  !empty($data['option_host_video']) ? true : false,
            'participant_video' =>  !empty($data['option_participants_video']) ? true : false,
            'mute_upon_entry'   =>  !empty($data['option_mute_participants']) ? true : false,
            'enforce_login'     =>  !empty($data['option_enforce_login']) ? true : false,
            'auto_recording'    =>  !empty($data['option_auto_recording']) ? $data['option_auto_recording'] : "none",
            'alternative_host'  =>  isset($alternative_host_ids) ? $alternative_host_ids : "",
            'approval_type' => 1,
            # esta propiedad define que tiene que ser aprobado
            'registration_type' => 2,
            # esta propiedad define que refistrarse debe ser obligatorio
        
        );

        return $this->sendRequest($create_meeting_array);
    }
    public function sendRequest($data){
        //email para personalizar
        $request_url = "https://api.zoom.us/v2/users/".$this->email."/meetings";
		echo "<pre>";
        
        echo "<br>";
        # muestra el token generado
        print_r($this->generateJWT());
        echo "<pre>";

		$headers = array(
			"authorization: Bearer ".$this->generateJWT(),
			"content-type: application/json",
			"Accept: application/json",
		);
		# se convierte parametros a json para pasar en body
		$postFields = json_encode($data);
		
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $request_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if (!$response) {
                return $err;
		}
        
        return json_decode($response);
    }
    ## funcion que trae datos de participantes y recibe el id del meet como parametro
    public function infoMeet($meetId){
        $url = 'https://api.zoom.us/v2/past_meetings/'.$meetId.'/participants';
        $headers = array(
            "authorization: Bearer ".$this->generateJWT(),
			"content-type: application/json",
			"Accept: application/json",
        );
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if (!$response) {
                return $err;
		}
        
        return json_decode($response);
        
    }
    # esta funcion trae los participantes de una reunion en progreso
    # solo disponible para licencias busines
    public function infoLiveMeeting($id){
        $url = 'https://api.zoom.us/v2/metrics/meetings/'.$id.'/participants/qos';
        $headers = array(
            "authorization: Bearer ".$this->generateJWT(),
			"content-type: application/json",
			"Accept: application/json",
        );
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if (!$response) {
                return $err;
		}
        
        return json_decode($response);
    }
    ## funcion que devuelve el listado de reuniones de dicha cuenta
    public function meetingList(){
        $url = "https://api.zoom.us/v2/users/".$this->email."/meetings";
        $headers = array(
            "authorization: Bearer ".$this->generateJWT(),
			"content-type: application/json",
			"Accept: application/json",
        );
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if (!$response) {
                return $err;
		}
        
        return json_decode($response);
        
    }
    public function listRegistrants($id){
        $request_url = "https://api.zoom.us/v2/meetings/".$id."/registrants";
		$headers = array(
			"authorization: Bearer ".$this->generateJWT(),
			"content-type: application/json",
			"Accept: application/json",
		);
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $request_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if (!$response) {
                return $err;
		}
        
        return json_decode($response);
    }
    ## funcion que devuelve el listado de registrados para una reunion
    ## esta funion recibe el id de la reunion,
    ## un method que puede ser put o post
    ## en caso de post registrantArray debe ser un objeto con los atributos 
    ## nombre e email obligatorios
    ## en el caso de que el motodo sea put los parametros deben un objeto con el 
    ## action que puede ser cancel, deny, o approve para cancelar, denegar y aprobar respectivamente
    ## una de las propiedades del objeto es registrants que es un array de objetos que tienen como
    ## parametros obligatorios email e ID
    ## en el caso de post devuelve los datos del registrante junto un link unico para la reunion
    ## en el caso de put no devuelve respuesta   
    public function actionRegistrant($meetId,$method,$registrantArray){
        $request_url = "https://api.zoom.us/v2/meetings/".$meetId."/registrants";
		$headers = array(
			"authorization: Bearer ".$this->generateJWT(),
			"content-type: application/json",
			"Accept: application/json",
		);
        $data = json_encode($registrantArray);
        echo $data;
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $request_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if (!$response) {
                return $err;
		}
        
        return json_decode($response);
    }
}