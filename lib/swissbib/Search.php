<?php

function search($query, $network=false, $library=false, $offset=1) {
	$sruQuery = new SruQuery();	
	
	$pxml = $sruQuery->getResultsFromSearch($query, $network, $library, $offset);
		
	$response = array();	

	if ($pxml->numberOfRecords == 0) { //no results
		$response['noresults'] = true;
		$spell_suggestion = $sruQuery->getSpellSuggestion($query);
		
		if ($spell_suggestion != false) {
			$response['didyoumean'] = $spell_suggestion;
		} else {
			$response['revisesearch'] = true;
		}
	} else {
		$response['numberofresults'] = intval($pxml->numberOfRecords);
		$response['results'] = array();

		foreach ($pxml->records->record as $record) {
			$marc = $record->recordData->children('srw_marc', true);

			$entry = array();	
			$entry['id'] = (string) getControlField($marc, '001');
			$entry['title'] = (string) getMarcField($marc, '245', 'a');
			$entry['author'] = (string) getMarcField($marc, '245', 'c');
			$entry['publisher'] = (string) getMarcField($marc, '260', 'b');
			$entry['publishdate'] = (string) getMarcField($marc, '260', 'c');

			array_push($response['results'], $entry);
		}

		$response['nextoffset'] = intval($pxml->nextRecordPosition);
	}
	
	/*
	print_r('<pre>');
	print_r($response);
	print_r('</pre>');
	*/

	return $response;
}
?>
