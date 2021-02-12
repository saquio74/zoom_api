<?php
require __DIR__ . '/vendor/autoload.php';
use \Firebase\JWT\JWT;

class Zoom_api{
    private $zoom_api_key = 'OHIWzvjzTBicHJwLlJ7pKw';
    private $zoom_api_secret = 'a9hnX4e5fNHxeWNkeP8MEsoOtii3zNRwseAI';

    //generate jwt
    public function generateJWT(){
        $key = $this->zoom_api_key;
        $secret = $this->zoom_api_secret;
        $token = array(
            "iss"=>$key,
            'iat'=>time(),
            'exp'=>time()+604800
        );
        return JWT::encode($token,$secret);
    }
    //function create meeting
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
            'approval_type' => 2,
            'registration_type' => 2,
        
        );

        return $this->sendRequest($create_meeting_array);
    }
    public function sendRequest($data){
        //email para personalizar
        $request_url = "https://api.zoom.us/v2/users/pedroguananja@gmail.com/meetings";
		echo "<pre>";
        $timestamp = time();
        $expiration = time()+604800;
        print_r($timestamp);
        echo "<br>";
        print_r($this->generateJWT());
        echo "<pre>";
		$headers = array(
			"authorization: Bearer ".$this->generateJWT(),
			"content-type: application/json",
			"Accept: application/json",
		);
		
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
    
    public function infoMeet($id){
        $url = 'https://api.zoom.us/v2/meetings/'.$id.'/registrants';
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
    public function createRoom(){
        
    }
}