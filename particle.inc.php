<?php # clase de comunicaciones para particle - 09/01/2016

class particle {
	public
		$url	= 'https://api.particle.io',
		$ver	= 'v1',
		$token	= '';
	
	function __construct($token=false, $version=false){
		if($token	)	$this->token= $token;
		if($version	)	$this->var	= $version;
	}

	function call($path, $args=false, $method='POST'){
		$url	= $this->url.'/'.$this->ver.'/'.$path.'?access_token='.$this->token;
		$param	= array(
			CURLOPT_RETURNTRANSFER	=> 1,
			CURLOPT_URL				=> $url,
			CURLOPT_HEADER			=> false
		);
		
		if($method='PUT'){
			$param[CURLOPT_CUSTOMREQUEST]	= 'PUT';
			$param[CURLOPT_HTTPHEADER]		= array('Content-Type: application/json','OAuth-Token: '.$this->token);
		}
		
		if($args){
			$param[CURLOPT_POST]		= true;
			$param[CURLOPT_POSTFIELDS]	= http_build_query($args);
		}
				
		$curl = curl_init();
		curl_setopt_array($curl, $param);
		$return = json_decode(curl_exec($curl) ,1);
		curl_close($curl);
		
		return array('data'=>$return, 'url'=>$url);
	}
	
	// // // // // // // //
	
	function device($deviceID=false){
		if($deviceID	){	return $this->call('devices/'.$deviceID);	} //informacion de un dispositivo
		else			 {	return $this->call('devices');				} //lista de dispositivos
	}
	function device_rename($deviceID, $name){						return $this->call('devices/'.$deviceID, array('name'=>$name), 'PUT');	}
	function device_flash( $deviceID, $file, $file_type='binary'){	return $this->call('devices/'.$deviceID, array('file'=>'@'.$file, 'file_type'=>$file_type), 'PUT');	}
	
	
	function get($function, $args='', $deviceID=''){				return $this->call('devices/'.$deviceID.'/'.$function, ($args!=''?false:array('args'=>$args)) );	}
	
	function event($eventPrefix=false, $deviceID=false){
		$device = $deviceID ? 'devices/'.$deviceID.'/' : '';
		if($eventPrefix){	return $this->call($device.'events/'.$eventPrefix);	} //información de uno o varios eventos asociados a un dispositivo
		else			{	return $this->call($device.'events');				} //muestra todos los eventos asociados a todos los dispositivos
	}
	function set_event($name, $data, $private=true, $ttl=60){
		$post = array();
		$post['name'	] = $name;
		$post['data'	] = $data;
		$post['private'	] = $private?'true':'false';
		$post['ttl'		] = $ttl;
		return $this->call('devices/events', $post);
		
	}
}

?>