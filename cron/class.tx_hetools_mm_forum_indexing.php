<?php

require_once(t3lib_extMgm::extPath('mm_forum') . 'cron/classes/class.tx_mmforum_cron_indexing.php');

class tx_hetools_mm_forum_indexing extends tx_scheduler_Task {
	function execute() {
		$forumIndexer = new tx_mmforum_cron_indexing();
		$res = $forumIndexer->main();
		return TRUE;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_tools/cron/class.tx_hetools_mm_forum_indexing.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_tools/cron/class.tx_hetools_mm_forum_indexing.php']);
}
?>