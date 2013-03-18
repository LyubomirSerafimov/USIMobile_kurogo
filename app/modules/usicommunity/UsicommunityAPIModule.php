<?php
error_reporting(E_ERROR);
Kurogo::includePackage('db');

class UsicommunityAPIModule extends APIModule {

	protected $id='usicommunity';
	protected $vmin = 1;
	protected $vmax = 1;

	// special factory method for core
	public static function factory($id='usicommunity', $command='', $args=array()) {
		$module = new UsicommunityAPIModule();
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
				$usicommunity = new USIcommunity();
				$result = $usicommunity->getList();
				// check for errors
				if(KurogoError::isError($result)) {
					$this->throwError($result);
				}
				$this->setResponse($result);
				$this->setResponseVersion(1);
				break;
			case 'get_item':
				$usicommunity = new USIcommunity();
				$result = $usicommunity->getDetails($this->args['id']);
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
