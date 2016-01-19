<?php

require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_solr.php');

class tx_hetools_solr_update extends tx_scheduler_Task {
	function execute() {
		$solr = new tx_he_tools_solr();
		$solr->updateDisabledSolrPages();
		return $solr->submitDeletedPages();
	}
	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_tools/cron/class.tx_hetools_solr_update.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_tools/cron/class.tx_hetools_solr_update.php']);
}
?>