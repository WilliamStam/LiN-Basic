<?php
namespace controllers;
use \timer as timer;
class event_list extends _ {
	function __construct(){
		parent::__construct();
		
	}
	function page(){
		$user = $this->f3->get("user");
		$cfg = $this->cfg;
		$page = isset($_GET['page'])?$_GET['page']:'1';
		
		
		
		$data = $this->api("event/_list?limit=0,100&daterange=monthfuture&orderby=dateStart+ASC");
		$data = $data['data']['list'];


		
		
		
		//test_array($data); 
		
		$tmpl = new \template("template.twig");
		$tmpl->page = array(
			"section"    => "events",
			"sub_section"=> "list",
			"template"   => "event_list",
			"meta"       => array(
				"title"=> "Zoutnet | Events",
			),
			"css"=>"",
			"js"=>"",
		);
		$tmpl->_page = "events/list";
		$tmpl->data = $data;
		$tmpl->data_orig = $data;
		$tmpl->output();
		
	}
}
