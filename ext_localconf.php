<?php
if (!defined ('TYPO3_MODE')) {
 	die ('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['tx_hetools_solr_update'] = array(
		'extension'        => $_EXTKEY,
		'title'            => "SOLR Update",
		'description'      => "SOLR / Update der gelöschten/verborgenen Seiten ",
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['tx_hetools_datenimport_cron'] = array(
		'extension'        => $_EXTKEY,
		'title'            => "Datenimport RZ",
		'description'      => "Import von Software- und EDV-Material-Listen",
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['tx_hetools_mensaimport_cron'] = array(
		'extension'        => $_EXTKEY,
		'title'            => "Mensaimport",
		'description'      => "Import des Mensa-Speisaplans des Studentenwerks Stuttgart",
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['tx_hetools_mm_forum_indexing'] = array(
		'extension'        => $_EXTKEY,
		'title'            => "MM-Forum Indexieren",
		'description'      => "Indexierung des MM-Forums",
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['tx_hetools_loesche_modul_pdfs'] = array(
		'extension'        => $_EXTKEY,
		'title'            => "Lösche Modul-PDFs",
		'description'      => "PDFs der LSF-Modulneschreibungen löschen",
);


if (t3lib_extMgm::isLoaded('realurl')) {
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['tx_hetools_cron_realurl'] = array(
			'extension'        => $_EXTKEY,
			'title'            => 'RealURL Cleaner',
			'description'      => 'Removes expired records from tx_realurl_urlencodecache',
			'additionalFields' => 'tx_hetools_cron_realurl_AdditionalFieldProvider'
	);
}

t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_hetools_pi1.php', '_pi1', 'list_type', 1);
t3lib_extMgm::addPItoST43($_EXTKEY, 'pi2/class.tx_hetools_pi2.php', '_pi2', 'list_type', 1);
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][$_EXTKEY] = 'EXT:he_tools/hooks/class.tx_he_tools_clearcache_hook.php:&tx_he_tools_clearcache_hook->clearCachePostProc';
require_once(t3lib_extMgm::extPath($_EXTKEY).'hooks/class.tx_he_tools_damhooks.php');
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam_filelinks']['pi1_hooks']['getDamFromDatabase'] = 'tx_he_tools_damhooks';

// Hook für das Speichern im Backend
require_once(t3lib_extMgm::extPath($_EXTKEY).'hooks/class.tx_he_tools_tcemainhooks.php');
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:he_tools/hooks/class.tx_he_tools_tcemainhooks.php:tx_he_tools_tcemainhooks';

// hook for tt_news
if (TYPO3_MODE == 'FE')    {
    require_once(t3lib_extMgm::extPath($_EXTKEY).'hooks/class.tx_he_tools_news_hooks.php');
}
 
$TYPO3_CONF_VARS['EXTCONF']['tt_news']['extraItemMarkerHook'][]   = 'tx_he_tools_news_hooks';
$TYPO3_CONF_VARS['EXTCONF']['tt_news']['extraGlobalMarkerHook'][]   = 'tx_he_tools_news_hooks';
$TYPO3_CONF_VARS['EXTCONF']['tt_news']['getSingleViewLinkHook'][]   = 'tx_he_tools_news_hooks';

// Hooks für Powermail
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_SubmitBeforeMarkerHook'][] = 'EXT:he_tools/hooks/class.tx_he_tools_powermail_hooks.php:tx_he_tools_powermail_hooks';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_SubmitEmailHook'][] = 'EXT:he_tools/hooks/class.tx_he_tools_powermail_hooks.php:tx_he_tools_powermail_hooks';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_MainContentHookAfter'][] = 'EXT:he_tools/hooks/class.tx_he_tools_powermail_hooks.php:tx_he_tools_powermail_hooks';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_MandatoryHook'][] = 'EXT:he_tools/hooks/class.tx_he_tools_powermail_hooks.php:tx_he_tools_powermail_hooks';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_ConfirmationHook'][] = 'EXT:he_tools/hooks/class.tx_he_tools_powermail_hooks.php:tx_he_tools_powermail_hooks';

// Hooks für wt_gallery
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_gallery']['list_inner'][] = 'EXT:he_tools/hooks/class.tx_he_tools_wt_gallery_hooks.php:&tx_he_tools_wt_gallery_hooks->wt_gallery_list_inner';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_gallery']['list_outer'][] = 'EXT:he_tools/hooks/class.tx_he_tools_wt_gallery_hooks.php:&tx_he_tools_wt_gallery_hooks->wt_gallery_list_outer';


// Hooks für Templavoila
if (TYPO3_MODE == 'FE')    {
require_once(t3lib_extMgm::extPath($_EXTKEY).'hooks/class.tx_he_tools_templavoila_hooks.php');
}
$TYPO3_CONF_VARS['EXTCONF']['templavoila']['pi1']['renderElementClass'][] = 'tx_he_tools_templavoila_hooks';

// Hooks für browser
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['browser']['BR_TemplateElementsTransformedHook'][] = 'EXT:he_tools/hooks/class.tx_browserhooks.php:tx_browserhooks';

// Hook für RTE Link-Wizard
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/rtehtmlarea/mod3/class.tx_rtehtmlarea_browse_links.php']['addAttributeFields'][]='EXT:he_tools/hooks/class.tx_he_tools_browselinkshooks.php:tx_he_tools_browselinkshooks';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/rtehtmlarea/mod3/class.tx_rtehtmlarea_browse_links.php']['extendJScode'][]='EXT:he_tools/hooks/class.tx_he_tools_browselinkshooks.php:tx_he_tools_browselinkshooks';
// eID
$TYPO3_CONF_VARS['FE']['eID_include']['he_tools'] = 'EXT:he_tools/eid/class.tx_he_tools_eid.php';

// dynaflex
$GLOBALS['T3_VAR']['ext']['dynaflex']['tt_content'][] = 'EXT:he_tools/dcafiles/class.tx_hetools_dfconfig.php:tx_hetools_dfconfig';


?>
