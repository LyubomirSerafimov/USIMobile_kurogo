<?php
error_reporting(E_ERROR);

class AaiAPIModule extends APIModule {

	protected $id='aai';
	protected $vmin = 1;
	protected $vmax = 1;

	private $domain = '';
	private $location = '';
	private $cookies = array();
	private $request_number = 0;
	private $request_limit = 20;

	// special factory method for core
	public static function factory($id='aai', $command='', $args=array()) {
		$module = new AaiAPIModule();
		$module->init($command, $args);
		return $module;
	}

	public function initializeForCommand() {  
	
		switch ($this->command) {
			case 'hello':
				$response = array(
					'salute'=>'hi there'
				);
				$this->setResponse($response);
				$this->setResponseVersion(1);
				break;
			case 'get_idps':
				$wayf = $this->getModuleVar('wayf','urls');
				$httpRequest = new HttpRequest($wayf, HttpRequest::METH_GET);
				$httpRequest->send();
			
				if( $httpRequest->getResponseCode() == 200) { // OK
					// extracting the form data
					$DOMDocument = new DOMDocument();
					$DOMDocument->loadHtml($httpRequest->getResponseBody());
					$optgroup = $DOMDocument->getElementsByTagName('optgroup');
					//$organisation = array();
					//var_dump($optgroup->item(0)->attributes->getNamedItem('title')->value);
					$idp = array();
					foreach($optgroup as $group) {
						$options = $group->getElementsByTagName('option');
						foreach($options as $option) {
							$idp[count($idp)] = array('name' => $option->getAttribute('title'), 'url' => $option->getAttribute('value') );
						}
					}

					$this->setResponse($idp);
					$this->setResponseVersion(1);
				} else {
					$this->raiseError(0);
				}
				break;

			case 'authenticate':
				// check idp attribute
				if( !isset($this->args['idp']) ){
					$this->raiseError(1);	
				}

				// check username
				if( !isset($this->args['username']) ){
					$this->raiseError(2);	
				}

				// check password
				if( !isset($this->args['password']) ){
					$this->raiseError(3);	
				}

				// check target
				if( !isset($this->args['target']) ){
					$this->raiseError(4);	
				}

				// request
				$result = $this->httpGet($this->args['target']);

				$this->setResponse($this->get_session());
				$this->setResponseVersion(1);

				break;

			default:
				$this->invalidCommand();
				break;
		}
	}

	private function isNumberOfRequestsExceeded(){
		$this->request_number += 1;
		if($this->request_number > $this->request_limit) {
			if(isset($this->args['verbose'])) {
				print_r("\n\n!!!!!!!!!!!!!!!!!limit reached!!!!!!!!!!!!!!!!!!!!!!!!\n\n");
			}
			return true;
		} else {
			if(isset($this->args['verbose'])) {
				print_r("\n\n>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> request number: $this->request_number <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<\n\n");
			}
			return false;
		}
	}

	private function httpGet($location) {
		
		if($this->isNumberOfRequestsExceeded()){ return; }
		
		// get cookies to send
		$cookies = $this->getCookies($location);
		if(isset($this->args['verbose'])) {
			header('Content-type: text/plain');
			print_r("======================================================\n");
			print_r("GET: $location\n");
			print_r("======================================================\n");
			print_r("Cookies:\n");
			print_r($cookies);
			print_r("\n");
			print_r("======================================================\n");
			print_r("\n");
		}
		$httpRequest = new HttpRequest($location, HttpRequest::METH_GET);
		$httpRequest->setCookies($cookies);
		$httpRequest->send();
		// process
		return $this->processHttpResponse(
			$httpRequest->getResponseCode(),	
			$httpRequest->getResponseHeader(),	
			$httpRequest->getResponseBody(),
			$httpRequest->getResponseCookies()
		);
	}

	private function httpPost($location, $data) {

		if($this->isNumberOfRequestsExceeded()){ return; }
		
		// get cookies to send
		$cookies = $this->getCookies($location);
		if(isset($this->args['verbose'])) {
			print_r("======================================================\n");
			print_r("POST: $location\n");
			print_r("======================================================\n");
			print_r("Data:\n");
			print_r($data);
			print_r("Cookies:\n");
			print_r($cookies);
			print_r("======================================================\n");
			print_r("\n");
		}
		$httpRequest = new HttpRequest($location, HttpRequest::METH_POST);
		$httpRequest->setPostFields($data);
		$httpRequest->setCookies($cookies);
		$httpRequest->send();
		// process
		return $this->processHttpResponse(
			$httpRequest->getResponseCode(),	
			$httpRequest->getResponseHeader(),	
			$httpRequest->getResponseBody(),
			$httpRequest->getResponseCookies()
		);
	}

	private function isUrlRelative($location){
		if( preg_match('/^http/', $location)) {
			return false;
		} else {
			return true;	
		}
	}
	
	// stores the last request domain in form of scheme://host:port
	// this is useful for post requests where the action is given as
	// relative path
	private function storeLocation($location) {
		if( !$this->isUrlRelative($location)) {
			$this->domain = $this->extractDomain($location);	
			$this->location = $location;
		}

		if(isset($this->args['verbose'])) {
			print_r(".......................................................\n");
			print_r("Last domain stored: ");
			print_r($this->domain);
			print_r("\nLast location stored: ");
			print_r($this->location);
			print_r("\n.......................................................\n");
		}
	}
	
	// extracts the domain
	private function extractDomain($location) {
		$url = parse_url($location);
		$domain = $url['scheme'].'://';
		$domain.=$url['host'];
		//set the port if available
		if( isset($url['port']) ) {
			$domain.=':'.$url['port'];
		}
		return $domain;
	}


	// stores cookies and catalogs cookies based on the last domain
	private function storeCookies($raw_cookies) {
		if(!empty($raw_cookies)) {
			foreach($raw_cookies as $number => $raw_cookie){
				 //array_push($this->cookies[$this->domain], $raw_cookie);
				 array_push($this->cookies, $raw_cookie);
			}
		}
	}	

	private function getCookies($location) {
		$domain = $this->extractDomain($location);
		$url = parse_url($location);
		$response_cookies = array();
		foreach($this->cookies as $cookie) {
			// check the domain and the path of the cookie
			if( preg_match('|'.$cookies->domain.'$|', $domain) and preg_match('|^'.$cookie->path.'|', $url['path'])) { 
				$response_cookies = array_merge($response_cookies, $cookie->cookies);	
			}
		}
		return $response_cookies;
	}

	private function processHttpResponse($code, $header, $body, $cookies) {
		if(isset($this->args['verbose'])) {
			print_r("+++++++++++++++++++++++++++++++++++++++++++++++++++++++\n");
			print_r("Response code: $code\n");
			print_r("+++++++++++++++++++++++++++++++++++++++++++++++++++++++\n");
			print_r("Response header:\n");
			print_r("-------------------------------------------------------\n");
			print_r($header);
			print_r("\n");
			print_r("-------------------------------------------------------\n");
			print_r("Response Body:\n");
			print_r("-------------------------------------------------------\n");
			print_r($body);
			print_r("\n");
			print_r("-------------------------------------------------------\n");
			print_r("Response Cookies:\n");
			print_r("-------------------------------------------------------\n");
			print_r($cookies);
			print_r("\n");
			print_r("+++++++++++++++++++++++++++++++++++++++++++++++++++++++\n");
		}
		
		$this->storeCookies($cookies);
		$this->storeLocation($header['Location']);
		switch($code) {
			case 200:
				if($this->isAuthenticated()) {
					return true;
				} else {
					$form = $this->extractForm($body); 
					if( is_array($form) and !empty($form['action']) and !empty($form['data']) ){
						return $this->httpPost($form['action'], $form['data'], $response_cookies);
					} else {
						// not authenticated and no forms in the page
						$this->raiseError(5);	
					}
				} 
				break;	
			case 301: // permanent redirect
				return $this->httpGet($header['Location']);
				break;
			case 302: // redirect
				return $this->httpGet($header['Location']);
				break;
			case 303: // redirect
				if($this->isAuthenticated()) {
					return true;	
				} else {
					return $this->httpGet($header['Location']);
				}
				break;
		}
	}

	// extracts and prepares the form
	private function extractForm($page) {
		// check if there is a form in the body
		// if there is process and submit
		$DOMDocument = new DOMDocument();
		$DOMDocument->loadHtml($page);
		$form = $DOMDocument->getElementsByTagName('form');
		if ($form->length == 0) { 
			return false; // no form inside this page
		} else { // process form
			$action = $form->item(0)->attributes->getNamedItem('action')->value;
			// if the action url is relative or is not set then use the current location
			if(empty($action) or $this->isUrlRelative($action) ) {
				$action = $this->location;
			}

			$data = array();

			// check if the current form is the WAYF page form
			$wayf = $this->getModuleVar('wayf','urls');
			if( preg_match('|^'.$wayf.'|', $action)) {
				// this is the name of the select node
				// it is probably different for different WAYF pages so 
				// that's why is extracted from the html code
				$idp_key = $DOMDocument->getElementsByTagName('select')->item(0)->attributes->getNamedItem('name')->value;
				$data = array($idp_key => $this->args['idp']);
			} else { // not WAYF form
				$input = $DOMDocument->getElementsByTagName('input');
				for($i = 0; $i<$input->length; ++$i) {
					$key = $input->item($i)->attributes->getNamedItem('name')->value;
					$value = $input->item($i)->attributes->getNamedItem('value')->value;
					$data = array_merge($data, array($key => $value));
				}
				// if this is the authentication form
				if( array_key_exists('j_username', $data) and array_key_exists('j_password', $data) ) { // SWITCH standard
					$data['j_username'] = $this->args['username'];
					$data['j_password'] = $this->args['password'];
				} else if ( array_key_exists('username', $data) and array_key_exists('password', $data) ){ // USI setting
					$data['username'] = $this->args['username'];
					$data['password'] = $this->args['password'];
				}
			}
			return array('action'=>$action, 'data'=>$data);
		}
	}

	private function isAuthenticated() {
		$shib_session_cookie_name = $this->getModuleVar('shib_session_cookie_name','variables');
		// check for the shibsession cookie
		foreach($this->cookies as $num => $stored_cookie) {
			foreach($stored_cookie->cookies as $key => $value) {
				if(preg_match('/^'.$shib_session_cookie_name.'/', $key) and !empty($value)){
					if(isset($this->args['verbose'])) {
						print_r("|||||||||||||||||||||||||||||||||||||||||||||||||||\n");
						print_r("| Success! :: AAI cookie set. User authenticated. |\n");
						print_r("|||||||||||||||||||||||||||||||||||||||||||||||||||\n");
					}
					return true; // authenticated
				}
			}
		}
		return false;
	}


	function get_session() {
		$shib_session_cookie_name = $this->getModuleVar('shib_session_cookie_name','variables');
		foreach($this->cookies as $num => $stored_cookie) {
			foreach($stored_cookie->cookies as $key => $value) {
				if(preg_match('/^'.$shib_session_cookie_name.'/', $key) and !empty($value)){
					if(isset($this->args['verbose'])) {
						print_r("|||||||||||||||||||||||||||||||||||||||||||||||||||\n");
						print_r("| COOKIE :: \n");
						print_r(" $key ==> $value \n");
						print_r("|||||||||||||||||||||||||||||||||||||||||||||||||||\n");
					}
					return array($key => $value); // authenticated
				}
			}
		}
		return null;
	}


	
	public function raiseError($code) {

		$error = new KurogoError();
		$error->code = $code;

		switch ($code) {
			case 0:
				$error->title = 'WAYF list empty';
				$error->message = 'Where Are You From list is empty.';
				break;
			case 1:
				$error->title = 'IDP missing';
				$error->message = 'The Identity Provider url is missing.';
				break;
			case 2:
				$error->title = 'Username missing';
				$error->message = 'The username is missing.';
				break;
			case 3:
				$error->title = 'Password missing';
				$error->message = 'The password is missing.';
				break;
			case 4:
				$error->title = 'Target url missing';
				$error->message = 'The target url is missing. Please check your request.';
				break;
			case 5:
				$error->title = 'AAI session cookie';
				$error->message = 'The AAI session cookie has not been set.';
				break;
			default:
				$error->title = 'Unknown error';
				$error->message = 'Unknown error occured';
		}
		$this->throwError($error);
	}

}
