<?php
namespace controllers;
use \timer as timer;
class article_list extends _ {
	function __construct(){
		parent::__construct();
		
	}
	function page(){
		$user = $this->f3->get("user");
		$cfg = $this->cfg;
		$page = isset($_GET['page'])?$_GET['page']:'1';
		
		$category = $this->f3->get("PARAMS['category']");
		$category = $this->api("category/_details?ID=$category");
		$category = $category['data'];
		if ($category['ID']=='')$this->f3->error("404");
		//test_array($category); 
		
		$articles = $this->api("article/_list?limit=0,6&thumb_width=64&thumb_height=64&pagination=9&page=$page&category={$category['ID']}");
		$categories = $this->api("category/_list");
		
		//test_array($articles); 
		
		$tmpl = new \template("template.twig");
		$tmpl->page = array(
			"section"    => "articles",
			"sub_section"=> $category['url'],
			"template"   => "article_list",
			"meta"       => array(
				"title"=> "Zoutnet | ".$category['category'],
			),
			"css"=>"",
			"js"=>"",
		);
		$tmpl->_page = "articles/".$category['url'];
		$tmpl->articles = $articles['data'];
		$tmpl->categories = $categories['data'];
		$tmpl->pagination = isset($articles['data']['pagination'])?$articles['data']['pagination']:false;
		$tmpl->output();
		
	}
}
