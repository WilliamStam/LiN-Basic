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
		
		$data = api_fetch($part,$ttl=0);

		

		return (array) $data;
	}

	
}
