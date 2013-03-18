<?php
class USIcommunity {
	
	private $short_community_list_url = "http://swisscast.ticinoricerca.ch/webexport/export_json_documents_list.php?prv_id=27&amp;prv_id=12&amp;prv_id=2&amp;prv_id=10&amp;prv_id=23&amp;prv_id=39&amp;prv_id=38&amp;prv_id=40&amp;prv_id=57&amp;prv_id=61&amp;prv_id=63&amp;channel=1&amp;nmax=3&amp;type=2&amp;order_by=doc_evt_start_date&amp;order_by_criteria=ASC&amp;evtdatefrom=";
	private $detailed_community_list_url = "http://swisscast.ticinoricerca.ch/webexport/export_json_document_details.php";

	public function getList(){ 
		$httpRequest = new HttpRequest($this->short_community_list_url.time(), HttpRequest::METH_GET);
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
		$httpRequest = new HttpRequest($this->detailed_community_list_url.$parameters, HttpRequest::METH_GET);
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
				$error->title = 'Getting the USI community list';
				$error->message = 'Fetching the community list failed.';
				break;
			case 1:
				$error->title = 'Getting the USI community list';
				$error->message = 'Wrong data format received.';
				break;
			case 2:
				$error->title = 'Getting the USI community list';
				$error->message = 'Internal server error.';
				break;
			case 3:
				$error->title = 'Getting the USI community details';
				$error->message = 'Malformed request. Missing the id parameter.';
				break;
			case 4:
				$error->title = 'Getting the USI community details';
				$error->message = 'Fetching the detailed new failed.';
				break;
			case 5:
				$error->title = 'Getting the USI community details';
				$error->message = 'Wrong data format received.';
				break;
			case 6:
				$error->title = 'Getting the USI community details';
				$error->message = 'Internal server error.';
				break;
			default:
				$error->title = 'Unknown error';
				$error->message = 'Unknown error occured';
		}
		return $error;
	}

}
