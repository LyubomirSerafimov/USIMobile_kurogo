<?php
class USISearch {
	public function getPeopleList() {
		$url = "http://search.usi.ch/people/search.json";
		$json_list = file_get_contents($url);
		if($json_list == false) {
			return $this->error(0);
		} else {
			return json_decode($json_list);
		}
	}

	public function getPeopleHash() {
		$list = $this->getPeopleList();
		return md5(serialize($list->data));
	}

	public function error($code) {

		$error = new KurogoError();
		$error->code = $code;

		switch ($code) {
			case 0:
				$error->title = 'USISearch: Getting People';
				$error->message = 'Getting People data failed: ' . mssql_get_last_message();
				break;
			case 1:
				$error->title = 'USISearch: Getting People Checksum';
				$error->message = 'Getting People checksum failed: ' . mssql_get_last_message();
				break;
			default:
				$error->title = 'USISearch: Unknown error';
				$error->message = 'Unknown error. Last db message:' . mssql_get_last_message();
		}
		return $error;
	}
}
?>
