<?php
namespace controllers;
use \timer as timer;
class home extends _ {
	function __construct(){
		parent::__construct();
		
	}
	function page(){
		$user = $this->f3->get("user");
		$cfg = $this->cfg;
		
		//test_array($cfg['api'] . "articles/_list?"); 

		$data = $this->api("articles/_list?limit=0,5");
		
		//test_array($data); 
		
		$tmpl = new \template("template.twig");
		$tmpl->page = array(
			"section"    => "home",
			"sub_section"=> "home",
			"template"   => "home",
			"meta"       => array(
				"title"=> "Welcome to Zoutnet",
			),
			"css"=>"",
			"js"=>"",
		);
		$tmpl->data = $data['data'];
		$tmpl->output();
		
	}
}
