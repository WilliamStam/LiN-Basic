<?php
namespace controllers;
use \timer as timer;
class _ {

	function __construct() {
		$this->f3 = \base::instance();
		$this->user = $this->f3->get("user");
		$this->cfg = $this->f3->get("cfg");

		
		
	}

	function api($part,$ttl=0){
		$url = $this->cfg['api'] . $part;
		$key = md5($url);
		$cache = new \Cache($key);

	
		
		
		if ( $cache->exists($key)){
			$data = json_decode($cache->get($key),true);
		} else {
			
			$web = new \Web();
			$data = $web->request($url);
			$data = json_decode($data['body'],true);
			
			
			$ddata = json_encode($data);
			$cache->set($key,$ddata,2000);
			
			
		}
		
		


		return (array) $data;
	}

	
}
