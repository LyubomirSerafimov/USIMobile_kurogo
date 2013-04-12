<?php
include('swissbib/Header.php');

class LibraryCatalog { 

	public function searchBooks($params) {
		$network = '';
		$library = '';
		$offset = 1;

		if(!empty($params['network'])) { 
			$network = $params['network'];
		}

		if(!empty($params['library'])) {
			$library = $params['library'];
		}

		if(!empty($params['offset'])) {
			$offset = $params['offset'];
		}

		return search($params['query'], $network, $library, $offset);
	}

	public function searchJournals($params) {
		$config = Kurogo::siteConfig();
		$config->getVar('JOURNAL_BY_LETTER', 'usi_urls');
		// request
		if(empty($params['query'])) {
			$httpRequest = new HttpRequest($config->getVar('JOURNAL_BY_LETTER', 'usi_urls').$params['letter'], HttpRequest::METH_GET);
		} else {
			$httpRequest = new HttpRequest($config->getVar('JOURNAL_BY_PATTERN', 'usi_urls').$params['query'], HttpRequest::METH_GET);
		}

		$httpRequest->send();
		if( $httpRequest->getResponseCode() == 200) { // OK
			$header = $httpRequest->getResponseHeader();
			if($header['Content-Type'] == 'text/html'){
				$response = $httpRequest->getResponseBody();
				return $this->htmlJournalListToArray($response, $params['offset']);
			} else {
				return $this->error(1);
			}
		} else {
			return $this->error(0);
		}
	}


	// The html page here parsed is a list of paragraph nodes
	// that can contain text or anchors.
	// Every journal entry is separated by an hr tag.
	// This means that every journal entry in the html page is a list
	// of paragraphs terminated by an hr occourence.
	// This function is an ad-hoc parses for such a structure.
	public function htmlJournalListToArray($content, $offset=0) {
		$doc = new DOMDocument();
		if(!$doc->loadHTML($content)) {
			return $this->error(2);
		}
		$body = $doc->getElementsByTagName('body');
		$journals = array();
		$entry = array();
		$page_lenght = 10;
		$numentry = 0;
		$skip_entry_number = $offset * $page_lenght;
		foreach ($body->item(0)->childNodes as $node) {
			// offset skip 
			if($numentry <= $skip_entry_number){
				if($node->nodeName == 'hr') {
					$numentry += 1;	
				}
				continue;
			}
			// process node
			if($node->nodeName == 'p') {
				if(count($entry) == 0){ // first element so this is the title
					$entry['title'] = $node->textContent;
				} else if(is_numeric(strpos($node->textContent, 'ISSN: '))){
					$entry['issn'] = preg_replace('/^ *ISSN: */', '', $node->textContent);
				} else if(is_numeric(strpos($node->textContent, 'Collezione: '))){
					$entry['collection'] = preg_replace('/^ *Collezione: */', '', $node->textContent);
				} else if(is_numeric(strpos($node->textContent, 'Argomento: '))){
					$entry['topic'] = preg_replace('/^ *Argomento: */', '', $node->textContent);
				} else if(is_numeric(strpos($node->textContent, 'Segnatura: '))){
					$entry['shelfmark'] = preg_replace('/^ *Segnatura: */', '', $node->textContent);
				} else if(is_numeric(strpos($node->textContent, 'Ultimo fascicolo ricevuto: '))){
					$entry['lastissue'] = preg_replace('/^ *Ultimo fascicolo ricevuto: */', '', $node->textContent);
				} else { // link to the online journal issue
					foreach ($node->childNodes as $subnode) {
						if($subnode->nodeName == 'a'){
							$entry['linktext'] = $subnode->textContent;
							$entry['url'] = $subnode->attributes->item(0)->textContent;
						}
					}
				}
			} else if($node->nodeName == 'hr') { // store this node and proceed with the next one
				if(count($journals) == $page_lenght) {
					break;
				}
				array_push($journals, $entry);
				$entry = array();
			}
		}
		/*
		print_r('skipped: '.$numentry);
		print_r('<pre>');
		print_r($journals);
		print_r('</pre>');
		*/
		return $journals;
	}

	public function error($code) {
		$error = new KurogoError();
		$error->code = $code;
		$error->title = 'USI Library Journals';

		switch ($code) {
			case 0:
				$error->message = 'Fetching the USI Library Journals list failed.';
				break;
			case 1:
				$error->message = 'Wrong data format received.';
				break;
			case 2:
				$error->message = 'Internal server error. Could not load the html. DOMDocument missing perhaps.';
				break;
			default:
				$error->message = 'Unknown error occured';
		}
		return $error;
	}
}
