<?php
class tx_he_tools_tcemainhooks {

	function processDatamap_postProcessFieldArray($status, $table, $id, &$fieldArray) {
// Letzter Benutzer wird in der Seite eingetragen
		if ($table == 'pages') {
			$fieldArray['cruser_id'] = $GLOBALS['BE_USER']->user['uid'];
		}		
	}
}
?>
