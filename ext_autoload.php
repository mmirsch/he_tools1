<?php
/*
 * Register necessary class names with autoloader
 *
 * $Id: ext_autoload.php 6536 2009-11-25 14:07:18Z stucki $
 */
// TODO: document necessity of providing autoloader information


return array(
	'tx_hetools_solr_update'	=> t3lib_extMgm::extPath('he_tools', 'cron/class.tx_hetools_solr_update.php'),
	'tx_hetools_datenimport_cron'	=> t3lib_extMgm::extPath('he_tools', 'cron/class.tx_hetools_datenimport_cron.php'),
	'tx_hetools_mensaimport_cron'	=> t3lib_extMgm::extPath('he_tools', 'cron/class.tx_hetools_mensaimport_cron.php'),
	'tx_hetools_mm_forum_indexing' =>t3lib_extMgm::extPath('he_tools', 'cron/class.tx_hetools_mm_forum_indexing.php'),
	'tx_hetools_loesche_modul_pdfs' =>t3lib_extMgm::extPath('he_tools', 'cron/class.tx_hetools_loesche_modul_pdfs.php'),
	'tx_hetools_cron_realurl' => t3lib_extMgm::extPath('he_tools', 'cron/class.tx_hetools_cron_realurl.php'),
	'tx_hetools_cron_realurl_AdditionalFieldProvider' => t3lib_extMgm::extPath('he_tools', 'cron/class.tx_hetools_cron_realurl_AdditionalFieldProvider.php'),
);
?>
