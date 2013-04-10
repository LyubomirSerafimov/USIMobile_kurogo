<?php
error_reporting(E_ERROR);
Kurogo::includePackage('db');

class BibcatalogAPIModule extends APIModule {

	protected $id='bibcatalog';
	protected $vmin = 1;
	protected $vmax = 1;

	// special factory method for core
	public static function factory($id='bibcatalog', $command='', $args=array()) {
		$module = new BibcatalogAPIModule();
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
			case 'search':
				$bibcatalog = new BibCatalog();
				$result = $bibcatalog->search($this->args);
				// check for errors
				$this->setResponse($result);
				$this->setResponseVersion(1);
				break;
			default:
				$this->invalidCommand();
				break;
		}
	}
}
