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
		$domain = $this->f3->get("domain");
		
		
		//test_array($articles); 
		
		$tmpl = new \template("template.twig");
		$tmpl->page = array(
			"section"    => "contact",
			"sub_section"=> "contact",
			"template"   => "contact",
			"meta"       => array(
				"title"=> $domain['domainname'] . " | Contact Us",
			),
			"css"=>"",
			"js"=>"",
		);
		$tmpl->_page = "contact";
		$tmpl->output();
		
	}
}
