<?php
error_reporting(E_ERROR);

class DemoAPIModule extends APIModule {

	protected $id='aai';
	protected $vmin = 1;
	protected $vmax = 1;
	protected $target = 'https://aai-demo.switch.ch/secure/';
	

	// special factory method for core
	public static function factory($id='demo', $command='', $args=array()) {
		$module = new DemoAPIModule();
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

			case 'get':
				// check if args is an array 
				if( !is_array($this->args) ){
					$this->raiseError(1);
				}

				// check if args is empty 
				if( empty($this->args) ){
					$this->raiseError(2);
				}

				// check if the request is well formed
				// only one argument is needed which is the shibsession cookie
				if( count($this->args) > 1 ){
					$this->raiseError(3);
				}

				if( preg_match('/^_shibsession/',key($this->args)) != 1 ){
					$this->raiseError(4);
				}

				// check shibboleth cookie existence and validity
				$httpRequest = new HttpRequest($this->target, HttpRequest::METH_GET);
				$httpRequest->setCookies($this->args);
				$httpRequest->send();

				if( $httpRequest->getResponseCode() == 200) { // OK
					$this->args['data'] = $httpRequest->getResponseBody();
					$this->command = 'process';
					$this->initializeForCommand();
				} else {
					$this->raiseError(5);
				}
				break;

			case 'process':
				print_r($this->args['data']);	
				$response = array(
					'args'=>'data processed'
				);
				$this->setResponse($response);
				$this->setResponseVersion(1);
				break;

			default:
				$this->invalidCommand();
				break;
		}
	}
	
	public function printResponse($hr) {
		print_r('<pre>');
		//print_r('url:');
		//print_r($hr->getUrl());
		print_r('response code:');
		print_r($hr->getResponseCode());
		print_r('response header:');
		print_r($hr->getResponseHeader());
		print_r("\n\n");
		$body = $hr->getResponseBody();
		//print_r("<script type='text/javascript'> <!-- $body // --> </script>");
		print_r($body);
		print_r("\n\n");
		print_r($hr->getCookies());
		print_r('sp cookies:');
		print_r($this->args['sp_cookies']);
		print_r("\n\n");
		print_r('idp cookies:');
		print_r($this->args['idp_cookies']);
		print_r("\n\n");
		print_r('</pre>');
	}

	
	public function raiseError($code) {

		$error = new KurogoError();
		$error->code = $code;

		switch ($code) {
			case 1:
				$error->title = 'Malformed request';
				$error->message = 'Cannot perform. Malformed request.';
				break;
			case 2:
				$error->title = 'Missing the shibsession cookie';
				$error->message = 'The shibsession cookie has not been passed in the request.';
				break;
			case 3:
				$error->title = 'Too many arguments';
				$error->message = 'Too many arguments have been passed.';
				break;
			case 4:
				$error->title = 'Shibsession cookie not valid';
				$error->message = 'The shibsession cookie is not valid. Cannot perform the request.';
				break;
			case 5:
				$error->title = 'Shibsession cookie expired';
				$error->message = 'The shibsession cookie has expired. Please authenticate again.';
				break;
		}
		$this->throwError($error);
	}

}
