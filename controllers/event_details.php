<?php
namespace controllers;
use \timer as timer;
class event_details extends _ {
	function __construct(){
		parent::__construct();
		
	}
	function page(){
		$user = $this->f3->get("user");
		$cfg = $this->cfg;
		$page = isset($_GET['page'])?$_GET['page']:'1';

		$data = $this->api("event/_details?ID=".$this->f3->get("PARAMS['ID']"));
		$data = $data['data'];
		if ($data['ID']=='')$this->f3->error("404");
		
		$categories = $this->api("category/_list");


		$list = $this->api("event/_list?limit=0,100&daterange=monthfuture&orderby=dateStart+ASC");
		$list = $list['data']['list'];
		
		//test_array($articles); 
		
		$tmpl = new \template("template.twig");
		$tmpl->page = array(
			"section"    => "articles",
			"sub_section"=> "events",
			"template"   => "event_details",
			"meta"       => array(
				"title"=> "Zoutnet | ".$data['heading'],
			),
			"css"=>"",
			"js"=>"",
		);
		$tmpl->data = $data;
		$tmpl->list = $list;
		$tmpl->_page = "events/list";
		$tmpl->categories = $categories['data'];
		$tmpl->output();
		
	}
}
