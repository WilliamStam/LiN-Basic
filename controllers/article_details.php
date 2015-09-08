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
		$domain = $this->f3->get("domain");
		$article = $this->api("article/_details?ID=".$this->f3->get("PARAMS['ID']"));
		$article = $article['data'];
		if ($article['ID']=='')$this->f3->error("404");
		

		
		//test_array($articles); 
		
		$tmpl = new \template("template.twig");
		$tmpl->page = array(
			"section"    => "articles",
			"sub_section"=> $article['category']['url'],
			"template"   => "article_details",
			"meta"       => array(
				"title"=> $domain['domainname'] . " | ".$article['heading'],
			),
			"css"=>"",
			"js"=>"",
		);
		$tmpl->article = $article;
		$tmpl->_page = "articles/".$article['category']['url'];
		$tmpl->output();
		
	}
}
