<?php
namespace controllers;
use \timer as timer;
class article_details extends _ {
	function __construct(){
		parent::__construct();
		
	}
	function page(){
		$user = $this->f3->get("user");
		$cfg = $this->cfg;
		$page = isset($_GET['page'])?$_GET['page']:'1';

		$article = $this->api("article/_details?ID=".$this->f3->get("PARAMS['ID']"));
		$article = $article['data'];
		if ($article['ID']=='')$this->f3->error("404");
		
		$categories = $this->api("category/_list");

		
		//test_array($articles); 
		
		$tmpl = new \template("template.twig");
		$tmpl->page = array(
			"section"    => "articles",
			"sub_section"=> $article['category']['url'],
			"template"   => "article_details",
			"meta"       => array(
				"title"=> "Zoutnet | ".$article['heading'],
			),
			"css"=>"",
			"js"=>"",
		);
		$tmpl->article = $article;
		$tmpl->_page = "articles/".$article['category']['url'];
		$tmpl->categories = $categories['data'];
		$tmpl->pagination = isset($articles['data']['pagination'])?$articles['data']['pagination']:false;
		$tmpl->output();
		
	}
}
