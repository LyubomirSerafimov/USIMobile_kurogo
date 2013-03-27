<?php
class USInews {
	
	public function getList(){ 
        $config = Kurogo::siteConfig();
		$httpRequest = new HttpRequest($config->getVar('SHORT_NEWS', 'usi_urls'), HttpRequest::METH_GET);
		$httpRequest->send();
		if( $httpRequest->getResponseCode() == 200) { // OK
			$header = $httpRequest->getResponseHeader();
			if($header['Content-Type'] == 'application/json'){
				$response = json_decode($httpRequest->getResponseBody());
				if($response->status == 'ok') {
					return $response->content->documents;
				} else {
					return $this->error(2);
				}
			} else {
				return $this->error(1);
			}
		} else {
			return $this->error(0);
		}
	}

	public function getDetails($id){ 
		$parameters = '?doc_id=';
		if(empty($id) or !is_numeric($id)){
			return $this->error(3);
		} else {
			$parameters .= $id;
		}

        $config = Kurogo::siteConfig();
		$httpRequest = new HttpRequest($config->getVar('DETAILED_NEWS', 'usi_urls').$parameters, HttpRequest::METH_GET);
		$httpRequest->send();
		if( $httpRequest->getResponseCode() == 200) { // OK
			$header = $httpRequest->getResponseHeader();
			if($header['Content-Type'] == 'application/json'){
				$response = json_decode($httpRequest->getResponseBody());
				if($response->status == 'ok') {
					return $response->content->document;
				} else {
					return $this->error(6);
				}
			} else {
				return $this->error(5);
			}
		} else {
			return $this->error(4);
		}
	}


	public function error($code) {
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
		return $error;
	}

}
