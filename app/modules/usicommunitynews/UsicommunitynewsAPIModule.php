<?php
error_reporting(E_ERROR);
Kurogo::includePackage('db');

class UsicommunitynewsAPIModule extends APIModule {

	protected $id='usicommunitynews';
	protected $vmin = 1;
	protected $vmax = 1;

	// special factory method for core
	public static function factory($id='usicommunitynews', $command='', $args=array()) {
		$module = new UsicommunitynewsAPIModule();
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
			case 'get_list':
				$usicommunitynews = new USIcommunityNews();
				$result = $usicommunitynews->getList();
				// check for errors
				if(KurogoError::isError($result)) {
					$this->throwError($result);
				}
				$this->setResponse($result);
				$this->setResponseVersion(1);
				break;
			case 'get_item':
				$usicommunitynews = new USIcommunityNews();
				$result = $usicommunitynews->getDetails($this->args['id']);
				// check for errors
				if(KurogoError::isError($result)) { $this->throwError($result); }
				$this->setResponse($result);
				$this->setResponseVersion(1);
				break;
			default:
				$this->invalidCommand();
				break;
		}
	}
}
