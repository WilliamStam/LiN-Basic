<?php
date_default_timezone_set('Africa/Johannesburg');
setlocale(LC_ALL, 'en_ZA.UTF8');
$errorPath = dirname(ini_get('error_log'));
$errorFile = $errorPath . DIRECTORY_SEPARATOR . basename(__DIR__) . "-errors.log";
ini_set("error_log", $errorFile);

if (session_id() == "") {
	$SID = @session_start();
} else $SID = session_id();
if (!$SID) {
	session_start();
	$SID = session_id();
}
$GLOBALS["output"] = array();
$GLOBALS["models"] = array();
require_once('vendor/autoload.php');
$f3 = \Base::instance();
require('inc/timer.php');
require('inc/template.php');
require('inc/functions.php');
require('inc/pagination.php');
$GLOBALS['page_execute_timer'] = new timer(true);
$cfg = array();
require_once('config.default.inc.php');
if (file_exists("config.inc.php")) {
	require_once('config.inc.php');
}
$f3->set("ttl",false);

$f3->set('AUTOLOAD', './|lib/|controllers/|inc/|/modules/');
$f3->set('PLUGINS', 'vendor/bcosca/fatfree/lib/');
$f3->set('CACHE', true);

$f3->set('cfg', $cfg);
$f3->set('DEBUG',3);

$f3->set('TZ', 'Africa/Johannesburg');
$f3->set("_api_hits",array());


$url = strtolower($_SERVER['HTTP_HOST']);
if (substr($url, 0, 4) == "www.") $url = str_replace("www.", "", $url);

if (substr($url,strpos($url,"."))=='.local'){
	$url = "lin.local";
} else {
	$url = substr(substr($url,strpos($url,".")),1);
}

if (!strpos($url, 'http://')) {
	$url= 'http://' . $url;
}



$f3->set("_api_url",$url);












$domain = api_fetch("domain/_micro",$f3->get("ttl"));
$domain = $domain['data'];

 
if ($domain['ID']==''){
	$f3->error("404");
}
$f3->set("domain",$domain);



//test_array($domain); 



$f3->set('ONERRORd',
	function($f3) {
		while (ob_get_level())
			ob_end_clean();
		echo $f3->get('ERROR.text');
		print_r($f3->get('ERROR.stack'));
	}
);

$version = date("YmdH");
if (file_exists("./.git/refs/heads/" . $cfg['git']['branch'])) {
	$version = file_get_contents("./.git/refs/heads/" . $cfg['git']['branch']);
	$version = substr(base_convert(md5($version), 16, 10), -10);
}

$minVersion = preg_replace("/[^0-9]/", "", $version);
$f3->set('_version', $version);
$f3->set('_v', $minVersion);




$f3->route('GET|POST /', 'controllers\home->page');
$f3->route('GET|POST /search', 'controllers\article_search->page');
$f3->route('GET|POST /articles/@category', 'controllers\article_list->page');
$f3->route('GET|POST /articles/@category/*/@ID/*', 'controllers\article_details->page');

$f3->route('GET|POST /events', 'controllers\event_list->page');
$f3->route('GET|POST /events/@ID/*', 'controllers\event_details->page');

$f3->route('GET|POST /contact', 'controllers\contact->page');





$f3->route('GET /thumbnail/files/@ID/@w/@h/@filename', function ($f3,$params) {
	$web = new \Web();
	$api = $f3->get("_api_url");
	$remoteImage = $api ."/thumbnail/files/".$params['ID']."/".$params['w']."/".$params['h']."/".$params['filename']."";
	
	//test_array($remoteImage); 
	$remoteImage = str_replace(" ", "%20", $remoteImage);
	$imginfo = getimagesize($remoteImage);
	header("Content-type: ".$imginfo['mime']);
	readfile($remoteImage);
	
});

$f3->route('GET /photo/files/@ID/@w/@h/@filename', function ($f3,$params) {
	$web = new \Web();
	$api = $f3->get("_api_url");
	$remoteImage = $api ."/photo/files/".$params['ID']."/".$params['w']."/".$params['h']."/".$params['filename']."";
	$remoteImage = str_replace(" ", "%20", $remoteImage);
	$imginfo = getimagesize($remoteImage);
	header("Content-type: ".$imginfo['mime']);
	readfile($remoteImage);
	
});
$f3->route('GET /ui/_images/events/@filename', function ($f3,$params) {
	$web = new \Web();
	$api = $f3->get("_api_url");
	$remoteImage = $api ."/ui/_images/events/".$params['filename']."";

	$imginfo = getimagesize($remoteImage);
	header("Content-type: ".$imginfo['mime']);
	readfile($remoteImage);
	
});




$f3->route('GET|POST /media/*', function ($f3,$params) {
	$request = parse_url($_SERVER['REQUEST_URI']);
	$path = $request["path"];
	$api = $f3->get("_api_url");
	$path = $api . $path . "?" . $_SERVER['QUERY_STRING'];
	
	$imginfo = getimagesize($path);
	header("Content-type: ".$imginfo['mime']);
	readfile($path);
});

$f3->route('GET /php', function () {
	phpinfo();
	exit();
});

$f3->run();



	

$models = $GLOBALS['models'];
$t = array();
foreach ($models as $model) {
	$c = array();
	foreach ($model['m'] as $method) {
		$c[] = $method;
	}
	$model['m'] = $c;
	$t[] = $model;
}



$models = $t;
$pageTime = $GLOBALS['page_execute_timer']->stop("Page Execute");

$GLOBALS["output"]['timer'] = $GLOBALS['timer'];
$GLOBALS["output"]['page'] = array(
	"page" => $_SERVER['REQUEST_URI'],
	"time" => $pageTime
);


if ($f3->get("ERROR")){
	exit();
}

if (($f3->get("AJAX") && ($f3->get("__runTemplate")==false) || $f3->get("__runJSON"))) {
	header("Content-Type: application/json");
	echo json_encode($GLOBALS["output"]);
} else {

	//if (strpos())
	if ($f3->get("NOTIMERS")){
		exit();
	}
	

	echo '
		</body>
		</html>
	';

}



?>
