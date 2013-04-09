<?php

Kurogo::includePackage('DataParser');

class BibCatalog extends XMLDataParser { 

	public function search($params){ 
		//$sru = new SruQuery();
		//$results = $sru->getResultsFromSearch($params['query'], 'SBT', 'LUBUL');
		//print_r($results); return;
		$sru_url = 'http://sru.swissbib.ch/SRW/search/?query=dc.language+%3D+%22it%22+and+dc.possessingInstitution+%3D+%22LUBUL%22+and+dc.title+%3D+%22financial+management%22+and+dc.xNetwork+%3D+%22SBT%22&version=1.1&operation=searchRetrieve&recordSchema=info%3Asrw%2Fschema%2F1%2Fdc-v1.1&maximumRecords=100&startRecord=1&resultSetTTL=300&recordPacking=xml&recordXPath=&sortKeys=';
		/*
		$sru_url = 'http://sru.swissbib.ch/SRW/search/?query=';
		$sru_url.= 'dc.language+%3D+%22'.$params['lang'].'%22+and+dc.possessingInstitution+%3D+%22LUBUL%22+and+';
		$sru_url.= 'dc.title+%3D+%22'.$params['query'].'%22+and+dc.xNetwork+%3D+%22SBT%22&version%3D1.1';
		$sru_url.= '&operation=searchRetrieve&recordSchema=info:srw/schema/1/dc-v1.1';
		$sru_url.= '&maximumRecords=100&startRecord=1&resultSetTTL=300&recordPacking=xml&recordXPath=&sortKeys=';
		*/
		//print_r($sru_url); return;
		$httpRequest = new HttpRequest($sru_url, HttpRequest::METH_GET);
		$httpRequest->send();
		/*
		print_r($httpRequest->getResponseCode());
		print_r($httpRequest->getResponseHeader());
		print_r($httpRequest->getResponseBody());
		return;
		*/

		if( $httpRequest->getResponseCode() == 200) { // OK
			$header = $httpRequest->getResponseHeader();
			if($header['Content-Type'] == 'text/xml'){
				$response = $this->parseData($httpRequest->getResponseBody());
				//print_r($response);
				//print_r($httpRequest->getResponseBody()); 
				return $response;
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

	public function error($code) {
		$error = new KurogoError();
		$error->code = $code;

		switch ($code) {
			case 0:
				$error->title = 'Library Catalog';
				$error->message = 'Query failed. Service not disponible.';
				break;
			case 1:
				$error->title = 'Library Catalog';
				$error->message = 'Wrong data format received.';
				break;
			case 2:
				$error->title = 'Library Catalog';
				$error->message = 'Internal server error.';
				break;
			default:
				$error->title = 'Unknown error';
				$error->message = 'Unknown error occured';
		}
		return $error;
	}

	protected function shouldStripTags($element) {
		return false;
	}
	
    protected function shouldHandleStartElement($name) {
		return true;
		//print_r("\n\n start element : ");
		//print_r($name);
	}

    protected function handleStartElement($name, $attribs) {
		return true;
		//return false;
		//print_r($name);
		//print_r($attribs);
	}

    protected function shouldHandleEndElement($name) {
		return true;
		//print_r("\n\n end element : ");
		//print_r($name);
	}

    protected function handleEndElement($name, $element, $parent) {
		return true;
	
	}

	protected function shouldHTMLDecodeCDATA($element) {
        return true;
    }
}
