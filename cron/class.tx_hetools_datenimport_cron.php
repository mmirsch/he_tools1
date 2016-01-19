<?php

require_once(t3lib_extMgm::extPath('he_tools') . 'lib/class.tx_he_tools_datenimport.php');
class tx_hetools_datenimport_cron extends tx_scheduler_Task {
	
	function execute() {
    /**@var $importeur tx_he_tools_datenimport */
		$importeur = t3lib_div::makeInstance('tx_he_tools_datenimport');
		$importOk = $importeur->importMaterial();
		return $importOk;
	}

	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_tools/cron/class.tx_hetools_datenimport_cron.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_tools/cron/class.tx_hetools_datenimport_cron.php']);
}
?>