<?php
date_default_timezone_set('Africa/Johannesburg');
setlocale(LC_ALL, 'en_ZA.UTF8');
$errorPath = dirname(ini_get('error_log'));
ini_set("error_log", $errorPath.DIRECTORY_SEPARATOR."LIN-php-error.log");
ini_set("always_populate_raw_post_data",-1);
//ini_set('memory_limit', '256M');
if (session_id() == "") {
	$SID = @session_start();
} else $SID = session_id();
if (!$SID) {
	session_start();
	$SID = session_id();
}

$GLOBALS["models"] = array();
$GLOBALS["css"] = array();
$GLOBALS["js"] = array();
$GLOBALS["output"] = array();
$GLOBALS["render"] = "";

$f3 = require('lib/f3/base.php');
require_once('inc/general.php');

require_once('/inc/class.timer.php');
require_once('/inc/functions.php');


require_once('lib/Twig/Autoloader.php');
Twig_Autoloader::register();

require_once('/inc/class.template.php');
require_once('/inc/class.email.php');
//require_once('inc/class.store.php');
require_once('/inc/class.pagination.php');
require_once('lib/Opauth/Opauth.php');

$cfg = array();
require_once('config.default.inc.php');
if (file_exists("config.inc.php")) {
	require_once('config.inc.php');
}

$general = new general();
$general->pageStart();

function pageEnd() {
	$general = new general();
	$general->pageEnd();

}

$debug = isset($_GET['debug']) ? true : $cfg['showDebug'];

$GLOBALS['cfg'] = $cfg;

$f3->set('AUTOLOAD', './|lib/|controllers/|inc/|/modules/|/modules/*/');
$f3->set('PLUGINS', 'lib/f3/');
$f3->set('CACHE', true);
//$f3->set('TEMP', '/tmp');
if ($debug || isLocal()){
	$f3->set('DEBUG', 2);
} else {
	$f3->set('DEBUG', 0);
}




$return = clean_url("http" . (($_SERVER['SERVER_PORT'] == 443) ? "s://" : "://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
$urlsky = $return;


$return = return_url($return);

$f3->set("return_here", $return);


$f3->set('DB', new DB\SQL('mysql:host=' . $cfg['DB']['host'] . ';dbname=' . $cfg['DB']['database'] . '', $cfg['DB']['username'], $cfg['DB']['password']));
$f3->set('cfg', $cfg);

$f3->set('UNLOAD', 'pageEnd');

$f3->set('UI', 'ui/|media/');
$f3->set('MEDIA', './media/');
$f3->set('TZ', 'Africa/Johannesburg');


$f3->set('show_settings', $debug);

//test_array($f3->get('show_settings')); 

$version = date("YmdH");
if (file_exists("./.git/refs/heads/" . $cfg['git']['branch'])) {
	$version = file_get_contents("./.git/refs/heads/" . $cfg['git']['branch']);
	$version = substr(base_convert(md5($version), 16, 10), -10);
}



$minVersion = preg_replace("/[^0-9]/", "", $version);
$f3->set('version', $version);
$f3->set('v', $minVersion);





$domainO = new models\domains();
$domain = $domainO->getDomainUrl();

//test_array(array($cfg['login'],$urlsky)); 

;
if ($domain['ID'] == "") {
	if (strpos($urlsky, $cfg['login']) === false) {
		$f3->error(404);
	} else {
		
	}
} else {
	$f3->set('ONERROR', 'Error::handler');
}

//test_array(array($cfg['login'],$urlsky,strpos($urlsky, $cfg['login']))); 

$domain = $domainO->get();





//test_array($urlsky); 

$f3->set('domain', $domain);



$uID = "";
if (isset($_GET['loginuser']) && $_GET['loginuser']) {
	$uID = models\users_auth::auth($_GET['loginuser']);
	$f3->reroute(clean_url($urlsky));
}
if (isset($_GET['userLoggedin']) && $_GET['userLoggedin']) {
	$uID = models\users_auth::auth(base64_decode($_GET['userLoggedin']));
	$f3->reroute(clean_url($urlsky));
}




if (isset($_SESSION['uID'])) {
	$uID = base64_decode($_SESSION['uID']);
}



$userO = new \models\users();

$user = $userO->get($uID);
if ($user['ID']) $userO->lastActivity($user['ID']);
$f3->set('user', $user);
//test_array($user);


/*
$general->checkCache();
if (isset($_GET['cache'])){
	$general->clearCache();
}
*/
$pages = models\pages::getAll("domainID='" . $domain['ID'] . "'");


//test_array($pages);
$a = array();
foreach ($pages as $page) {
	if ($page['url']) {

		$url = $page['url'];
		$last = explode("/", $url);



		$url = preg_replace_callback("(\*\/)", function ($a) {
				$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
				$randomString = '';
				for ($i = 0; $i < 5; $i++) {
					$randomString .= $characters[rand(0, strlen($characters) - 1)];
				}

				return "@_" . $randomString . "/";
			}, $url
		);
		$a[] = $page;
		//	test_array($a);
		$f3->route('GET|POST ' . $url, function ($f3, $params) use ($page) {

				$p = new controllers\front\pages($page);
				$p->page();

				//$f3->chain('controllers\\front\\pages->page2');
			}
		);
	}

}
//test_array($a);

$f3->route('GET /thumbnail/files/@ID/@width/@height/*', function ($f3, $params) {

		$ID = $params['ID'];
		$width = $params['width'];
		$height = $params['height'];


		$gen = new general();
		$gen->thumbnail_file($ID, $width, $height, true);


	}
);
$f3->route('GET /photo/files/@ID/@width/@height/*', function ($f3, $params) {

		$ID = $params['ID'];
		$width = $params['width'];
		$height = $params['height'];


		$gen = new general();
		$gen->thumbnail_file($ID, $width, $height, false, false);


	}
);

$f3->route('GET /thumbnail/@width/@height', function ($f3, $params) {

		$file = (isset($_GET['file'])) ? $_GET['file'] : "";
		
		$crop = (isset($_GET['crop'])) ? $_GET['crop'] : "";
		$enlarge = (isset($_GET['enlarge'])) ? $_GET['enlarge'] : "";
		
		if ($crop=="true"){
			$crop = true;
		} else {
			$crop=false;
		}
		if ($enlarge=="true"){
			$enlarge = true;
		} else {
			$enlarge = false;
		}
		
		
		$width = $params['width'];
		$height = $params['height'];

	//test_array($file); 
	
	$folder = $f3->get("MEDIA");
	$mime = mime_content_type($folder . "/".$file);
	
	if ($mime=="application/pdf"){
		$thumb = DIRECTORY_SEPARATOR . str_replace(".pdf", "_thumb.png", $file);
		
		//test_array($thumb); 


		if (!file_exists($folder . $thumb) && file_exists($folder . $file)) {
			$exportPath = $folder . $thumb;
			$res = "96";
			$pdf = $folder . $file;

			$str = "gs -dCOLORSCREEN -dNOPAUSE -box -sDEVICE=png16m -dUseCIEColor -dTextAlphaBits=4 -dFirstPage=1 -dLastPage=1 -dGraphicsAlphaBits=4 -o$exportPath -r$res  $pdf";

			exec($str);
			
			general::remove_white($folder . $thumb);
		}

		$file = DIRECTORY_SEPARATOR . str_replace(".pdf", ".png", $file);
	}
		
//test_array($file); 

		if ($file) {
			$gen = new general();
			$gen->thumbnail($file, $width, $height, $crop, $enlarge);
		}


	}
);
$f3->route('GET /image/@width/@height', function ($f3, $params) {

		$file = (isset($_GET['file'])) ? $_GET['file'] : "";
		$width = $params['width'];
		$height = $params['height'];

	$folder = $f3->get("MEDIA");
	
	
		if ($file && file_exists($folder . "" . $file)) {
			
			
			$gen = new general();
			$gen->image($file, $width, $height);
		} else {
			$f3->error(404);
		}


	}
);


$f3->route('GET /min/javascript*', 'general->_javascript');

//$f3->route('GET|POST /@domainID/system/auth/@strategy', 'controllers\\front\\page_user->auth');


$f3->route('GET|POST /user', 'controllers\\front\\page_user->page');
$f3->route('GET|POST /user/edit', 'controllers\\front\\page_user_edit->page');
$f3->route('GET|POST /user/auth/local', 'controllers\\front\\page_user->auth_local');
$f3->route('GET|POST /user/auth/@strategy', 'controllers\\front\\page_user->auth');
$f3->route('GET|POST /user/auth/@strategy/*', 'controllers\\front\\page_user->auth');
$f3->route('GET|POST /user/auth/@strategy/done', 'controllers\\front\\page_user->auth_return');


$f3->route('GET|POST /@domainID/user/auth/@strategy', 'controllers\\front\\page_user->auth');
$f3->route('GET|POST /@domainID/user/auth/@strategy/*', 'controllers\\front\\page_user->auth');
$f3->route('GET|POST /@domainID/user/auth/@strategy/done', 'controllers\\front\\page_user->auth_return');




$f3->route('GET|POST /user/login', 'controllers\\front\\page_user->login');
$f3->route('GET|POST /user/logout', function ($f3) {
		if (isset($_SESSION['uID'])) {
			unset($_SESSION['uID']);
		}

		$returnurl = "/";
		if (isset($_GET['return']) && $_GET['return']) {
			$returnurl = $_GET['return'];
			$returnurl = @urldecode($returnurl);
			$returnurl = @base64_decode($returnurl);


		}
		$f3->reroute($returnurl);


	}
);
$f3->route('GET|POST /subscribe', 'controllers\\front\\page_subscribe->page');


$f3->route('GET|POST /user/register', 'controllers\\front\\page_user->register');
$f3->route('GET|POST /user/verify', 'controllers\\front\\page_user->verify');
$f3->route('GET|POST /user/password', 'controllers\\front\\page_user->forgot_password');
$f3->route('GET|POST /user/password/reset', 'controllers\\front\\page_user->reset_password');


$f3->route('GET|POST /save/modules/@module/@identifier', 'controllers\\front\\save->save');
$f3->route('GET|POST /save/modules/@module/@identifier/@ID', 'controllers\\front\\save->save');




$f3->route('GET /holder.js/*', function ($f3, $params) {
		header("Content-Type: application/javascript");
		echo file_get_contents("holder.js");
	}
);

$f3->route('GET /details/*/*/@articleID', function ($f3, $params) {

		$article = new models\articles();
		$article = $article->get($params['articleID']);

		if ($article['ID']) {

		} else {
			$f3->error("404");
		}

		//test_array($article); 
		$returnurl = "/articles/" . $article['category']['url'] . "/" . $article['ID'] . "/" . $article['publishDate']['date'] . "/" . $article['url'];


		//test_array($returnurl); 

		$f3->reroute($returnurl);
	}
);



function isAdmin() {
	$f3 = \Base::instance();
	$user = $f3->get("user");
	if ($user['administrator'] == "0") {
		$f3->error("404");
	}
}

$f3->route('GET /ui/*', function ($f3, $params) {
		if (!file_exists($_SERVER['REQUEST_URI'])) {
			$f3->error(404);
		}


	}
);

$f3->route('GET /admin/*', function ($f3, $params) {
		if (!file_exists($_SERVER['REQUEST_URI'])) {
			$f3->error(404);
		}

	}
);

$f3->route('GET|POST /admin', 'controllers\admin\home->page');
$f3->route('GET|POST /admin/articles', 'controllers\admin\articles->page');
$f3->route('GET|POST /admin/articles/form', 'controllers\admin\articles->form');
$f3->route('GET|POST /admin/articles/form/@ID', 'controllers\admin\articles->form');
$f3->route('GET|POST /admin/articles/form/@ID/remove', 'controllers\admin\articles->remove');
$f3->route('GET|POST /admin/articles/form/upload', 'controllers\admin\articles->upload');
$f3->route('GET|POST /admin/articles/form/upload/replace', 'controllers\admin\articles->upload_replace');
$f3->route('GET /admin/pages', 'controllers\admin\pages->page');
$f3->route('GET /admin/pages/nav', 'controllers\admin\pages_nav->page');
$f3->route('GET /admin/pages/masterhead', 'controllers\admin\pages_masterhead->page');
$f3->route('GET /admin/pages/footer', 'controllers\admin\pages_footer->page');
$f3->route('GET /admin/pages/css', 'controllers\admin\pages_css->page');
$f3->route('GET /admin/pages/js', 'controllers\admin\pages_js->page');

$f3->route('GET /admin/lists', 'controllers\admin\lists->page');




$f3->route('GET|POST /admin/comments', 'controllers\admin\comments->page');
$f3->route('GET|POST /admin/comments/reported', 'controllers\admin\comments->page_reported');
$f3->route('GET|POST /admin/comments/recent', 'controllers\admin\comments->page_recent');
$f3->route('GET|POST /admin/comments/search', 'controllers\admin\comments->page_search');



$f3->route('GET|POST /admin/events', 'controllers\admin\events->page');
$f3->route('GET|POST /admin/events/@ID', 'controllers\admin\events->page');

$f3->route('GET|POST /admin/feedback', 'controllers\admin\feedback->page');
$f3->route('GET|POST /admin/feedback/@ID', 'controllers\admin\feedback->page');

$f3->route('GET|POST /admin/directory', 'controllers\admin\directory->page');
$f3->route('GET|POST /admin/directory/categories', 'controllers\admin\directory_categories->page');

$f3->route('GET|POST /admin/classifieds', 'controllers\admin\classifieds->page');
$f3->route('GET|POST /admin/classifieds/categories', 'controllers\admin\classifieds_categories->page');

$f3->route('GET|POST /admin/adserver', 'controllers\admin\adserver_active->page');
$f3->route('GET|POST /admin/adserver/history', 'controllers\admin\adserver_history->page');
$f3->route('GET|POST /admin/adserver/capture', 'controllers\admin\adserver_capture->page');
$f3->route('GET|POST /admin/adserver/capture/@ID', 'controllers\admin\adserver_capture->page');
$f3->route('GET|POST /admin/adserver/settings', 'controllers\admin\adserver_settings->page');

$f3->route('GET|POST /admin/polls', 'controllers\admin\polls->page');
$f3->route('GET|POST /admin/polls/@ID', 'controllers\admin\polls->page');


$f3->route('GET|POST /admin/users', 'controllers\admin\users->page');
$f3->route('GET|POST /admin/users/form', 'controllers\admin\users->form');
$f3->route('GET|POST /admin/users/form/@ID', 'controllers\admin\users->form');


$f3->route('GET|POST /admin/settings/authors', 'controllers\admin\authors->page');
$f3->route('POST /admin/settings/authors/upload', 'controllers\admin\authors->upload');
$f3->route('GET|POST /admin/settings/categories', 'controllers\admin\article_categories->page');
$f3->route('GET|POST /admin/settings/flags', 'controllers\admin\article_flags->page');
$f3->route('GET|POST /admin/settings/permissions', 'controllers\admin\permissions->page');
$f3->route('GET|POST /admin/settings/types', 'controllers\admin\feedback_types->page');
$f3->route('GET|POST /admin/settings/social', 'controllers\admin\social->page');
$f3->route('GET|POST /admin/settings/publications', 'controllers\admin\publications->page');
$f3->route('GET|POST /admin/settings/urls', 'controllers\admin\urls->page');
$f3->route('GET|POST /admin/settings/fields', 'controllers\admin\fields->page');
$f3->route('GET|POST /admin/settings', 'controllers\admin\domain->page');


$f3->route("GET|POST /admin/save/@function", function ($app, $params) {
		$app->call("controllers\\admin\\save\\save->" . $params['function']);
	}
);
$f3->route("GET|POST /admin/save/@class/@function", function ($app, $params) {
		$app->call("controllers\\admin\\save\\" . $params['class'] . "->" . $params['function']);
	}
);
$f3->route("GET|POST /admin/save/@folder/@class/@function", function ($app, $params) {
		$app->call("controllers\\admin\\save\\" . $params['folder'] . "\\" . $params['class'] . "->" . $params['function']);
	}
);
$f3->route("GET|POST /admin/data/@function", function ($app, $params) {
		$app->call("controllers\\admin\\data\\data->" . $params['function']);
	}
);
$f3->route("GET|POST /admin/data/@class/@function", function ($app, $params) {
		$app->call("controllers\\admin\\data\\" . $params['class'] . "->" . $params['function']);
	}
);
$f3->route("GET|POST /admin/data/@folder/@class/@function", function ($app, $params) {
		$app->call("controllers\\admin\\data\\" . $params['folder'] . "\\" . $params['class'] . "->" . $params['function']);
	}
);

$f3->route('GET|POST /admin/save/module/@module', 'controllers\\admin\\save\\_save->module');
$f3->route('GET|POST /admin/remove/module', 'controllers\\admin\\save\\_save->module_remove');



$f3->route("GET|POST /admin/redirect", function ($app, $params) {
		$string = isset($_GET['return']) ? $_GET['return'] : "";

		if ($string) {
			$string = urldecode($string);
			$string = base64_decode($string);
		} else {
			$string = "/admin";
		}

		$app->reroute($string);

		//$app->call("controllers\\admin\\data\\" . $params['folder'] . "\\" . $params['class'] . "->" . $params['function']);
	}
);



$f3->route("GET|POST /api/@folder/@class/@function", function ($app, $params) {
	$app->call("controllers\\api\\" . $params['folder'] . "\\" . $params['class'] . "->" . $params['function']);
}
);
$f3->route("GET|POST /api/@class/@function", function ($app, $params) {
	$app->call("controllers\\api\\" . $params['class'] . "->" . $params['function']);
}
);






$f3->route('GET /mailtest', function ($f3) {
	$to      = 'awstam@gmail.com';
	$subject = 'the subject';
	$message = 'hello';
	$headers = 'From: webmaster@linmedia.co.za' . "\r\n" .
		'Reply-To: webmaster@example.com';

	
	if(mail($to,$subject, $message, $headers)){
		echo('ok');
	}
	else{
		echo('not ok');
	}
	}
);
$f3->route('GET /php', function () {
		phpinfo();
		exit();
	}
);
$f3->route('GET /ip', function () {

		$IP = $_SERVER['REMOTE_ADDR'];

		$proxyIP = "";
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$proxyIP = $_SERVER['HTTP_CLIENT_IP'];
		} else {
			if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
				$proxyIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
			}
		}



		echo "your IP is: " . $IP;
		echo "<br>Internal IP: " . $proxyIP;

		$g = new \Web\Geo();


		$geo = $g->location($IP);


		echo "<br>Geo lookup: <br>" . print_r($geo);


		exit();
	}
);

$f3->route('GET /t', function () {
	$r = array(
		"ID"=>"109",
		"name"=>"The Namibian Friday",
		"contact_person"=>"Morina",
		"print_order"=>"65000",
	    "rates"=>array(
			"ads"=>array("..."),
			"inserts"=>array("...")
		)
	);
	


	test_array($r);
	exit();
}
);


$f3->route('GET /ser', function () {

		test_array($_SERVER);
		exit();
	}
);
$f3->route('GET|POST /proxy/json', function ($f3) {
		$url = isset($_REQUEST['url']) ? $_REQUEST['url'] : "";
		//$url = "http://forecast.io/forecast?q=-23.6665,27.7448&units=ca";

		//test_array($url); 
		$response = array(
			"url" => $url
		);

		if ($url) {




			$response = \Web::instance()->request($url);
			$response['url'] = $url;

			foreach ($response['headers'] as $item) {
				//header($item);
			}

			test_array(json_decode($response['body']));

		}
		//test_array($response);
		//exit();
		die();
	}, 10800
);

$f3->route('GET|POST /proxy', function ($f3, $params) {
		$url = isset($_REQUEST['url']) ? $_REQUEST['url'] : "";
		$ttl = isset($_REQUEST['cache']) ? $_REQUEST['cache'] : "";
		$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : "string";
		$cache = \Cache::instance();

		$return = "";
		if ($ttl) {
			
			if ($cache->exists(md5($url))) {
				$return = $cache->get(md5($url));
			} else {
				$response = \Web::instance()->request($url);
				$response['url'] = $url;
				$return = $response['body'];
				$cache->set(md5($url), $return, $ttl);
			}

		} else {
			$response = \Web::instance()->request($url);
			$response['url'] = $url;
			$return = $response['body'];
		}


		switch($type){
			case "json":
				test_array(json_decode($return));
				break;
			default:
				test_string($return);
				break;
		}
		
		die();
	}
);

$f3->run();