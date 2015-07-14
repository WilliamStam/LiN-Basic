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
		$web = new \Web();
		$data = $web->request($this->cfg['api'] . $part);
		$data = json_decode($data['body']);
		$data = json_decode(json_encode($data), true);
		
		
		return (array) $data;
	}

	
}
