<?php
/**
 * Custom error handling
 */
class Error {
	/**
	 * Handle error/s for specific error codes
	 *
	 * @param  object $f3 FatFree instance
	 * @return mixed      Custom error/Default FatFree error
	 */
	public static function handler($f3) {
		// F3 error: code, text, trace
		$error = $f3->get('ERROR');
		$domain = $f3->get("domain");
		
		error_log("Domain: ".$domain['domainname']);
		//test_array($_SERVER); 
		
		$urls = isset($_SERVER['HTTP_X_ORIGINAL_URL'])?$_SERVER['HTTP_X_ORIGINAL_URL']:$_SERVER['REQUEST_URI'];

		$url = clean_url("http" . (($_SERVER['SERVER_PORT'] == 443) ? "s://" : "://") . $_SERVER['HTTP_HOST'] . $urls);
		
		
		error_log("URL: ".$url);
		
		$botText = "";
		if (is_bot()){
			$botText = '---BOT--- ';
		}
		
		
		error_log("HTTP_USER_AGENT: ".$botText. $_SERVER['HTTP_USER_AGENT']);
		// custom error/s
		switch ($error['code']) {
			case 403:
				$f3->reroute("/user/login?return=" . $f3->get("return_here"));
				break;
			case 404:
				//test_array($error); 
				$f3->set("SOFTERROR",true);
			
				$page = models\default_pages::get("error404");
				//$page['js'] = $page['js'].";".$domain['js'];
				
				//test_array($page); 
				$p = new controllers\front\pages();
				$p->load($page, $error);
				

				break;
			
			
			default:
				$errorPath = dirname(ini_get('error_log'));
				ini_set("error_log", $errorPath.DIRECTORY_SEPARATOR."LIN-Errors-php-error.log");
				
				// restore F3 error handler (hackish)
				$f3->mset(array(
					          'ONERROR' => null,
					          'ERROR'   => null
				          )
				);
				$f3->error($error['code'], $error['text'], $error['trace']);
				
				exit();
				break;
			
		}
		// exit after custom action
		
	}
}