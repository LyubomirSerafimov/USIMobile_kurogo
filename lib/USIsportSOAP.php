<?php

class USIsportSOAP { 
	
	public function checkMembership($username, $password) { 
		// check function parameters
		if(empty($username)) {
			return $this->error(0, 'Missing username');
		}

		if(empty($password)) {
			return $this->error(1, 'Missing password');
		}

		$params = array('netidlogin' => $username, 'password' => $password);
		return $this->soapRequest(Kurogo::getSiteVar('CHECK_MEMBERSHIP_METHOD', 'usi_sport'), $params);
	}

	public function checkSubscription($username, $password, $activity) { 
		// check function parameters
		if(empty($username)) {
			return $this->error(0, 'Missing username');
		}

		if(empty($password)) {
			return $this->error(1, 'Missing password');
		}

		if(empty($activity)) {
			return $this->error(2, 'Missing activity');
		}

		$params = array('netidlogin' => $username, 'password' => $password, 'idAttivita' => $activity);
		return $this->soapRequest(Kurogo::getSiteVar('CHECK_SUBSCRIPTION_METHOD', 'usi_sport'), $params);
	}

	public function subscribe($username, $password, $activity) { 
		// check function parameters
		if(empty($username)) {
			return $this->error(0, 'Missing username');
		}

		if(empty($password)) {
			return $this->error(1, 'Missing password');
		}

		if(empty($activity)) {
			return $this->error(2, 'Missing activity');
		}

		$params = array('netidlogin' => $username, 'password' => $password, 'idAttivita' => $activity);
		return $this->soapRequest(Kurogo::getSiteVar('SUBSCRIBE_METHOD', 'usi_sport'), $params);
	}

	public function unsubscribe($username, $password, $activity) { 
		// check function parameters
		if(empty($username)) {
			return $this->error(0, 'Missing username');
		}

		if(empty($password)) {
			return $this->error(1, 'Missing password');
		}

		if(empty($activity)) {
			return $this->error(2, 'Missing activity');
		}

		$params = array('netidlogin' => $username, 'password' => $password, 'idAttivita' => $activity);
		return $this->soapRequest(Kurogo::getSiteVar('UNSUBSCRIBE_METHOD', 'usi_sport'), $params);
	}

	public function soapRequest($method, $params) {
		$soapClient = new SoapClient(Kurogo::getSiteVar('WDSL', 'usi_sport'), array('cache_wsdl' => WSDL_CACHE_NONE));
                   
        try {
            $response = $soapClient->__call($method, array($params));
        } catch (SoapFault $fault) {
			//$message = 'CODE: ' . $fault->faultcode . ' ' . $fault->faultstring;
			return $this->error($fault->faultcode, $fault->faultstring);
        }

		$result = $response->{$method.'Result'};
		if(!empty($result->flagErrore)) {
			return $this->error($result->CodiceErrore, $result->MessaggioErrore);
		} else if(isset($result->Oggetto)) {
			if($result->Oggetto == 'True') {
				return true;
			} else if($result->Oggetto == 'False') {
				return false;
			}
		} else {
			return true;
		}
    }

	public function error($code, $message) {
		$error = new KurogoError();
		$error->code = $code;
		$error->title = 'Sport Activities SOAP';
		$error->message = $message;
		return $error;
	}
}
