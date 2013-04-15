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
				$libraryCatalog = new LibraryCatalog();
				$result = $libraryCatalog->searchBooks($this->args);
				$this->setResponse($result);
				$this->setResponseVersion(1);
				break;
			case 'search_journals':
				$libraryCatalog = new LibraryCatalog();
				$result = $libraryCatalog->searchJournals($this->args);
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

	// The over-writing of this function is an
	// ugly hack is to fix the broken json output that occours
	// from time to time when setting the output with the parent::setResponse function.
	// At the moment I don't have a clue why this happens. Will fix as soon
	// as I understand how. =(
	public function setResponse($result) {
		echo json_encode( array('response' => $result) );
		exit;
	}
}
