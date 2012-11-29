<?php
error_reporting(E_ERROR);
Kurogo::includePackage('db');

class UsinewsAPIModule extends APIModule {

	protected $id='usinews';
	protected $vmin = 1;
	protected $vmax = 1;

	// special factory method for core
	public static function factory($id='usinews', $command='', $args=array()) {
		$module = new UsinewsAPIModule();
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
			case 'get_list':
				$list = $this->getNewsList();
				$this->setResponse($list);
				$this->setResponseVersion(1);
				break;
			case 'get_item':
				$details = $this->getDetails();
				$this->setResponse($details);
				$this->setResponseVersion(1);
				break;
			default:
				$this->invalidCommand();
				break;
		}
	}

	private function getNewsList(){ 
		$url = $this->getModuleVar('news_list_json','urls');
		$httpRequest = new HttpRequest($url, HttpRequest::METH_GET);
		$httpRequest->send();
		if( $httpRequest->getResponseCode() == 200) { // OK
			$header = $httpRequest->getResponseHeader();
			if($header['Content-Type'] == 'application/json'){
				$response = json_decode($httpRequest->getResponseBody());
				if($response->status == 'ok') {
					return $response->content->documents;
				} else {
					$this->raiseError(2);
				}
			} else {
				$this->raiseError(1);
			}
		} else {
			$this->raiseError(0);
		}
	}

	private function getDetails(){ 
		$url = $this->getModuleVar('details_json','urls');
		$parameters = '?doc_id=';
		if(empty($this->args['id']) or !is_numeric($this->args['id'])){
			$this->raiseError(3);
		} else {
			$parameters .= $this->args['id'];
		}
		$httpRequest = new HttpRequest($url.$parameters, HttpRequest::METH_GET);
		$httpRequest->send();
		if( $httpRequest->getResponseCode() == 200) { // OK
			$header = $httpRequest->getResponseHeader();
			if($header['Content-Type'] == 'application/json'){
				$response = json_decode($httpRequest->getResponseBody());
				if($response->status == 'ok') {
					return $response->content->document;
				} else {
					$this->raiseError(6);
				}
			} else {
				$this->raiseError(5);
			}
		} else {
			$this->raiseError(4);
		}
	}


	public function raiseError($code) {

		$error = new KurogoError();
		$error->code = $code;

		switch ($code) {
			case 0:
				$error->title = 'Getting the news list';
				$error->message = 'Fetching the news list failed.';
				break;
			case 1:
				$error->title = 'Getting the news list';
				$error->message = 'Wrong data format received.';
				break;
			case 2:
				$error->title = 'Getting the news list';
				$error->message = 'Internal server error.';
				break;
			case 3:
				$error->title = 'Getting the details';
				$error->message = 'Malformed request. Missing the id parameter.';
				break;
			case 4:
				$error->title = 'Getting the details';
				$error->message = 'Fetching the detailed new failed.';
				break;
			case 5:
				$error->title = 'Getting the details';
				$error->message = 'Wrong data format received.';
				break;
			case 6:
				$error->title = 'Getting the details';
				$error->message = 'Internal server error.';
				break;
			default:
				$error->title = 'Unknown error';
				$error->message = 'Unknown error occured';
		}
		$this->throwError($error);
	}

}
