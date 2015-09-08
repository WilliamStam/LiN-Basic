<?php
namespace controllers;
use \timer as timer;
class article_search extends _ {
	function __construct(){
		parent::__construct();
		
	}
	function page(){
		$user = $this->f3->get("user");
		$cfg = $this->cfg;
		$page = isset($_GET['page'])?$_GET['page']:'1';
		$catID = isset($_GET['catID'])?$_GET['catID']:'';
		$search = isset($_GET['search'])?$_GET['search']:'';
		
		
		
		//test_array($category); 
		$search_url = urlencode($search);
		$articles = $this->api("article/_list?limit=0,10&thumb_width=64&thumb_height=64&pagination=9&page={$page}&category={$catID}&search={$search_url}");
		
		//test_array($articles); 
		
		$tmpl = new \template("template.twig");
		$tmpl->page = array(
			"section"    => "search",
			"sub_section"=> 'search',
			"template"   => "article_search",
			"meta"       => array(
				"title"=> "Zoutnet | Search | ".$search,
			),
			"css"=>"",
			"js"=>"",
		);
		$tmpl->_page = "/search";
		$tmpl->search =$search;
		$tmpl->catID =$catID;
		$tmpl->articles = $articles['data'];
		$tmpl->pagination = isset($articles['data']['pagination'])?$articles['data']['pagination']:false;
		$tmpl->output();
		
	}
}
