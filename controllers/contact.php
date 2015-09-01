<?php
namespace controllers;
use \timer as timer;
class contact extends _ {
	function __construct(){
		parent::__construct();
		
	}
	function page(){
		$user = $this->f3->get("user");
		$cfg = $this->cfg;
		$page = isset($_GET['page'])?$_GET['page']:'1';

		
		$categories = $this->api("category/_list");
		
		//test_array($articles); 
		
		$tmpl = new \template("template.twig");
		$tmpl->page = array(
			"section"    => "contact",
			"sub_section"=> "contact",
			"template"   => "contact",
			"meta"       => array(
				"title"=> "Zoutnet | Contact Us",
			),
			"css"=>"",
			"js"=>"",
		);
		$tmpl->_page = "about";
		$tmpl->categories = $categories['data'];
		$tmpl->output();
		
	}
}
