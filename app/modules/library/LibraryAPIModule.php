<?php
error_reporting(E_ERROR);
Kurogo::includePackage('db');

class LibraryAPIModule extends APIModule {

	protected $id='library';
	protected $vmin = 1;
	protected $vmax = 1;

	// special factory method for core
	public static function factory($id='library', $command='', $args=array()) {
		$module = new LibraryAPIModule();
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
			case 'search_books':
				$bibcatalog = new LibraryCatalog();
				$result = $bibcatalog->searchBooks($this->args);
				$this->setResponse($result);
				$this->setResponseVersion(1);
				break;
			case 'search_journals':
				$bibcatalog = new LibraryCatalog();
				$result = $bibcatalog->searchJournals($this->args);
				// check for errors
				if(KurogoError::isError($result)) {
					$this->throwError($result);
				}
				$this->setResponse($result);
				$this->setResponseVersion(1);
				break;
			default:
				$this->invalidCommand();
				break;
		}
	}
}
