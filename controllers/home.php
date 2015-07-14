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
		
		echo $cfg['api'];
		
		/*
		
		$tmpl = new \template("template.twig");
		$tmpl->page = array(
			"section"    => "home",
			"sub_section"=> "home",
			"template"   => "home",
			"meta"       => array(
				"title"=> "MeetPad",
			),
			"css"=>"",
			"js"=>"",
		);
		$tmpl->output();
		*/
	}
}
