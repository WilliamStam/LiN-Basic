<?php
namespace controllers;
use \timer as timer;
class home extends _ {
	function __construct(){
		parent::__construct();
		
	}
	function page(){
		$user = $this->f3->get("user");
		$domain = $this->f3->get("domain");
		$cfg = $this->cfg;
		$page = isset($_GET['page'])?$_GET['page']:'1';

		$articles = $this->api("article/_list?limit=0,6&thumb_width=64&thumb_height=64&pagination=9&page=$page");
		$categories = $this->api("category/_list");
		
		//test_array($articles); 
		
		$tmpl = new \template("template.twig");
		$tmpl->page = array(
			"section"    => "home",
			"sub_section"=> "home",
			"template"   => "home",
			"meta"       => array(
				"title"=> "Welcome to ".$domain['domainname'],
			),
			"css"=>"",
			"js"=>"",
		);
		$tmpl->_page = "/";
		$tmpl->articles = $articles['data'];
		$tmpl->categories = $categories['data'];
		$tmpl->pagination = isset($articles['data']['pagination'])?$articles['data']['pagination']:false;
		$tmpl->output();
		
	}
}
