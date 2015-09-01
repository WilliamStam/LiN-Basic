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
		$categories = $this->api("category/_list");



		$data_orig = $data;

		//test_array($data);



		$d = array();

		foreach ($data as $item){
			if (isset($item['dateStart']['raw'])) $d[date("Y-m-01",strtotime($item['dateStart']['raw']))] = array();
			if (isset($item['dateEnd']['raw'])) $d[date("Y-m-01",strtotime($item['dateEnd']['raw']))] = array();
		}


		krsort($d);

		$records = $data;
		$rec = array();
		foreach ($records as $item){

			if (isset($item['dateEnd']['raw'])){
				$begin = new \DateTime( $item['dateStart']['raw'] );
				$end = new \DateTime( $item['dateEnd']['raw'] );
				$end = $end->modify( '+1 day' );

				$interval = \DateInterval::createFromDateString('1 day');
				$period = new \DatePeriod($begin, $interval, $end);

				$dend = date("Y-m-d",strtotime($item['dateEnd']['raw']));
				foreach ( $period as $dt ){
					if ($dt->format( "Y-m-d" ) <= $dend){
						$rec[$dt->format( "Y-m-d" )][] = $item ;
					}
				}
			} else {
				$rec[date( "Y-m-d", strtotime($item['dateStart']['raw']) )][] = $item;
			}
		}

		foreach ($rec as $key=>$item){
			
				if (date("Y-m",strtotime($key)) >= date("Y-m") ){
					$d[date("Y-m-01",strtotime($key))][$key][] = $item;
				}
			
		}

		$n = array();
		foreach ($d as $k=>$v){
			if (count($v)){
				$n[$k]=$v;
			}
		}


		$data = $n;
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
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
		$tmpl->data_orig = $data_orig;
		$tmpl->categories = $categories['data'];
		$tmpl->output();
		
	}
}
