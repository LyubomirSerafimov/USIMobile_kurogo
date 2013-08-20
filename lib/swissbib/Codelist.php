<?php
class Codelist {
	private $xmlurl="http://www.swissbib.ch/libraries/tpgreen-libraries.xml";
	private $dom = null;
	private $list = null;

	function __construct() {
		$this->dom = new DOMDocument();
		$file = file_get_contents($this->xmlurl);
		$this->dom->loadXML($file);
		$this->extractList();
	}

	private function extractList() {
		$this->list = array();
		$libraries = $this->dom->getElementsByTagName('library');
		foreach($libraries as $library) {
			$lib_id = $library->getElementsByTagName('libraryIdentifier')->item(0)->nodeValue;
			$translations = $library->getElementsByTagName('entry');
			$this->list[$lib_id] = array();
			foreach($translations as $translation) {
				//$this->list[$lib_id] = array();
				$lang = $translation->getElementsByTagName('key')->item(0)->nodeValue;
				$name = $translation->getElementsByTagName('value')->item(0)->nodeValue;
				$this->list[$lib_id][$lang] = $name;
			}
		}
	}

	public function getName($identifier, $lang) {
		return $this->list[$identifier][$lang];
	}
}
?>
