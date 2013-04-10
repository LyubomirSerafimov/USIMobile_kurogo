<?php
include('swissbib/Header.php');

class BibCatalog { 

	public function search($params) {
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
}
