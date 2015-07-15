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
		$page = isset($_GET['page'])?$_GET['page']:'1';

		$articles = $this->api("article/_list?limit=0,5&thumb_width=64&thumb_height=64&pagination=9&page=$page");
		$categories = $this->api("category/_list");
		
		
		
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
		$tmpl->articles = $articles['data'];
		$tmpl->categories = $categories['data'];
		$tmpl->pagination = $articles['data']['pagination'];
		$tmpl->output();
		
	}
}
