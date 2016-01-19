<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_hebest_artikel'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_artikel',		
		'label'     => 'produktname',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY produktname',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
			'fe_group' => 'fe_group',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/icon_tx_hebest_artikel.gif',
	),
);

$TCA['tx_hebest_eigenschaft1'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_eigenschaft1',		
		'label'     => 'title',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY title',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden'
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/icon_tx_hebest_eigenschaft1.gif',
	),
);

$TCA['tx_hebest_eigenschaft2'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_eigenschaft2',		
		'label'     => 'title',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY title',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden'
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/icon_tx_hebest_eigenschaft2.gif',
	),
);

$TCA['tx_hebest_hauptkategorie'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_hauptkategorie',		
		'label'     => 'title',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY title',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden'
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/icon_tx_hebest_hauptkategorie.gif',
	),
);

$TCA['tx_hebest_hersteller'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_hersteller',		
		'label'     => 'title',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY title',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden'
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/icon_tx_hebest_hersteller.gif',
	),
);

$TCA['tx_hebest_lieferanten'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_lieferanten',		
		'label'     => 'title',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY title',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden'
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/icon_tx_hebest_lieferanten.gif',
	),
);

$TCA['tx_hebest_keyword1'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_keyword1',		
		'label'     => 'title',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY title',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden'
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/icon_tx_hebest_keyword1.gif',
	),
);

$TCA['tx_hebest_keyword2'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_keyword2',		
		'label'     => 'title',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY title',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden'
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/icon_tx_hebest_keyword2.gif',
	),
);

$TCA['tx_hebest_stadt'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_stadt',		
		'label'     => 'title',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY title',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden'
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/icon_tx_hebest_stadt.gif',
	),
);

$TCA['tx_hebest_unterkategorie'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_unterkategorie',		
		'label'     => 'title',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY title',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden'
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/icon_tx_hebest_unterkategorie.gif',
	),
);

$TCA['tx_hetools_wmw_abteilungen'] = array (
	'ctrl' => array (
		'title'     => 'Abteilungen (Wer macht was)',		
		'label'     => 'title',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY title',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden'
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/book.gif',
	),
);

$TCA['tx_hetools_wer_macht_was'] = array (
	'ctrl' => array (
		'title'     => 'Wer macht was?',		
		'label'     => 'title',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY title',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden'
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/book.gif',
	),
);

t3lib_div::loadTCA('tx_he_zeitschriftenliste');
$TCA['tx_he_zeitschriftenliste'] = array (
	'ctrl' => array (
		'title'     => 'Zeitschriftenliste',		
		'label'     => 'titel',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY sortiertitel',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden'
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/book.gif',
	),
);

t3lib_div::loadTCA('tx_hetools_kategorie1');
$TCA['tx_hetools_kategorie1'] = array (
	'ctrl' => array (
		'title'     => 'Kategorie 1',		
		'label'     => 'title',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY title',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden'
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/book.gif',
	),
);

t3lib_div::loadTCA('tx_hetools_kategorie2');
$TCA['tx_hetools_kategorie2'] = array (
	'ctrl' => array (
		'title'     => 'Kategorie 2',		
		'label'     => 'title',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY title',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden'
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/book.gif',
	),
);

t3lib_div::loadTCA('tx_hetools_bereich');
$TCA['tx_hetools_bereich'] = array (
	'ctrl' => array (
		'title'     => 'Bereich',		
		'label'     => 'title',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY title',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden'
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/book.gif',
	),
);

t3lib_div::loadTCA('tx_hetools_module_studiengaenge');
$TCA['tx_hetools_module_studiengaenge'] = array (
	'ctrl' => array (
		'title'     => 'Studiengänge für Modulbeschreibungen',		
		'label'     => 'title',	
		'label_alt' => 'schwerpunkt',
		'label_alt_force' => 1,
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY title',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden'
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/book.gif',
	),
);

t3lib_div::loadTCA('tx_hetools_module_vertiefungen');
$TCA['tx_hetools_module_vertiefungen'] = array (
	'ctrl' => array (
		'title'     => 'Vertiefung mit Version für Modulbeschreibungen',		
		'label'     => 'vertiefung',	
		'label_alt' => 'version',
		'label_alt_force' => 1,
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY vertiefung,version',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden'
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/book.gif',
	),
);


t3lib_div::loadTCA('tx_hetools_veranstaltungen');
$TCA['tx_hetools_veranstaltungen'] = array (
	'ctrl' => array (
		'title'     => 'Veranstaltungen',
		'label'     => 'title',
		'default_sortby' => 'ORDER BY title',
		'sortby' => 'sorting',
		'delete' => 'deleted',
		'enablecolumns' => array (		
			'disabled' => 'hidden'
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/book.gif',
	),
);

t3lib_div::loadTCA('tx_hetools_veranstaltungen_termine');
$TCA['tx_hetools_veranstaltungen_termine'] = array (
	'ctrl' => array (
		'title'     => 'Termine der Veranstaltungen',
		'label'     => 'veranstaltung',
		'label_alt' => 'von,bis',
		'label_alt_force' => 1,
		'default_sortby' => 'ORDER BY tx_hetools_veranstaltungen_termine.veranstaltung, tx_hetools_veranstaltungen_termine.von',
		'sortby' => 'veranstaltung, von',
		'delete' => 'deleted',
		'enablecolumns' => array (		
			'disabled' => 'hidden'
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/book.gif',
	),
);

t3lib_div::loadTCA('tx_hetools_veranstaltungen_belegung');
$TCA['tx_hetools_veranstaltungen_belegung'] = array (
	'ctrl' => array (
		'title'     => 'Belegung der Veranstaltungen',
		'label'     => 'username',
		'default_sortby' => 'ORDER BY username, termin',
		'sortby' => 'sorting',
		'delete' => 'deleted',
		'enablecolumns' => array (		
			'disabled' => 'hidden'
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/book.gif',
	),
);

t3lib_div::loadTCA('tx_hetools_veranstaltungen_abhaengigkeiten');
$TCA['tx_hetools_veranstaltungen_abhaengigkeiten'] = array (
	'ctrl' => array (
		'title'     => 'Abhängigkeiten Veranstaltungen',
		'label'     => 'title',
		'default_sortby' => 'ORDER BY uid',
		'sortby' => 'sorting',
		'delete' => 'deleted',
		'enablecolumns' => array (
			'disabled' => 'hidden'
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/book.gif',
	),
);


//

t3lib_div::loadTCA('tx_hetools_einfuehrungsveranstaltungen');
$TCA['tx_hetools_einfuehrungsveranstaltungen'] = array (
	'ctrl' => array (
		'title'     => 'Termine für die Einführungsveranstaltungen',
		'label'     => 'raum',
		'label_alt' => 'start, ende',
		'label_alt_force' => 1,
		'default_sortby' => 'ORDER BY raum, start',
		'delete' => 'deleted',
		'enablecolumns' => array (		
			'disabled' => 'hidden'
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/book.gif',
	),
);

t3lib_div::loadTCA('tx_hetools_infoscreen_elemente');
$TCA['tx_hetools_infoscreen_elemente'] = array (
	'ctrl' => array (
		'title'     => 'Anzeige-Elemente für Infoscreens',
		'label'     => 'title',
		'label_alt' => 'raum',
		'default_sortby' => 'ORDER BY title',
		'delete' => 'deleted',
		'enablecolumns' => array (		
			'disabled' => 'hidden'
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/book.gif',
	),
);

t3lib_div::loadTCA('tx_hetools_infoscreen_anzeige_zeitraeume');
$TCA['tx_hetools_infoscreen_anzeige_zeitraeume'] = array (
		'ctrl' => array (
			'title'     => 'Zeiträume für Infoscreen-Seiten',
			'label'     => 'title',
			'label_alt' => 'von,bis',
			'default_sortby' => 'ORDER BY title',
			'delete' => 'deleted',
			'enablecolumns' => array (		
				'disabled' => 'hidden'
			),
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
			'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/book.gif',
		),
);

t3lib_div::loadTCA('tx_hetools_infoscreen_redirects');
$TCA['tx_hetools_infoscreen_redirects'] = array (
	'ctrl' => array (
		'title'     => 'Redirects für Infoscreen-Raspberries',
		'label'     => 'title',
		'label_alt' => 'ip',
		'label_alt_force' => 1,
		'default_sortby' => 'ORDER BY title',
		'delete' => 'deleted',
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/book.gif',
	),
);

t3lib_div::loadTCA('tx_he_standorte');
$TCA['tx_he_standorte'] = array (
	'ctrl' => array (
		'title'     => 'Standorte',
		'label'     => 'title',
		'default_sortby' => 'ORDER BY title',
		'delete' => 'deleted',
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/book.gif',
	),
);

t3lib_div::loadTCA('tx_he_fakultaeten');
$TCA['tx_he_fakultaeten'] = array (
		'ctrl' => array (
				'title'     => 'Fakultäten',
				'label'     => 'title',
				'default_sortby' => 'ORDER BY kuerzel',
				'delete' => 'deleted',
				'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
				'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/book.gif',
		),
);

t3lib_div::loadTCA('tx_he_studiengaenge');
$TCA['tx_he_studiengaenge'] = array (
		'ctrl' => array (
				'title'     => 'Studiengänge',
				'label'     => 'title',
				'default_sortby' => 'ORDER BY kuerzel',
				'delete' => 'deleted',
				'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
				'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/book.gif',
		),
);

t3lib_div::loadTCA('tx_he_modules_en');
$TCA['tx_he_modules_en'] = array (
		'ctrl' => array (
				'title'     => 'Englischsprachige Module',
				'label'     => 'title',
				'default_sortby' => 'ORDER BY kuerzel',
				'delete' => 'deleted',
				'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
				'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/book.gif',
		),
);

$tempColumnsIrFaqs = array(
	'tx_hetools_max_words' => array(
			'exclude' => 1,
			'label'   => 'Weiter-Link nach Anzahl von Worten',
			'config'  => array(
					'type' => 'input',
					'size' => '5'
			) 
	),
);

t3lib_div::loadTCA('tx_irfaq_q');
t3lib_extMgm::addTCAcolumns('tx_irfaq_q', $tempColumnsIrFaqs, 1);
t3lib_extMgm::addToAllTCAtypes('tx_irfaq_q', 'tx_hetools_max_words');

$tempColumns = array(
	'tx_hetools_titel_startseite' => array(
        'exclude' => 1,
        'label'   => 'Titel für die Startseite',
        'config'  => array(
						        	'type' => 'input',
											'size' => '72') ),
	'tx_hetools_kandidat_arbeit' => array(
        'exclude' => 1,
        'label'   => 'Kandidat(en)',
        'config'  => array( 
						        	'type' => 'input',
											'size' => '80') ),
	'tx_hetools_sortierfeld' => array(
        'exclude' => 1,
        'label'   => 'Sortiernummer (bitte mit führenden Nullen eingeben)',
        'config'  => array( 
						        	'type' => 'input',
											'size' => '04') ),
);

t3lib_div::loadTCA('tt_news');
t3lib_extMgm::addTCAcolumns    ('tt_news', $tempColumns, 1);
t3lib_extMgm::addToAllTCAtypes ('tt_news', 'tx_hetools_kandidat_arbeit');
t3lib_extMgm::addToAllTCAtypes ('tt_news', 'tx_hetools_sortierfeld;;;;1-1-1','', 'before:datetime');
t3lib_extMgm::addToAllTCAtypes ('tt_news', 'tx_hetools_titel_startseite;;;;1-1-1','', 'before:short');

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key';


t3lib_extMgm::addPlugin(array(
	'LLL:EXT:he_tools/locallang_db.xml:tt_content.list_type_pi1',	$_EXTKEY . '_pi1',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'icon_tools.gif'
),'list_type');

t3lib_extMgm::addPlugin(array(
	'LLL:EXT:he_tools/locallang_db.xml:tt_content.list_type_pi2',	$_EXTKEY . '_pi2',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'icon_tools.gif'
),'list_type');

$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']= 'pi_flexform';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:'.$_EXTKEY.'/flexform_ds.xml');

$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi2']= 'pi_flexform';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi2', 'FILE:EXT:'.$_EXTKEY.'/flexform_pi2_ds.xml');

$tempColumns = array(
'tx_hetools_filelist_dateitypen' => array(
		'exclude' => 1,
		'label'   => 'Dateityp(en) (z.B. pdf, um nur Dateien vom Typ PDF anzuzeigen)',
		'config'  => array( 
			'type' => 'input',
			'size' => '10',
		), 
	),
	'tx_hetools_filelist_sortierfeld' => array(
		'exclude' => 1,
		'label'   => 'Sortierfeld',
    'config' => array (
			'type' => 'select',
			'eval' => 'trim,lower,alphanum_x',
			'items' => array (
				array (
					'Titel (standard)', 
					'title'
				),
				array (
					'Sortiernummer', 
					'tx_hetools_dam_sortiernummer'
				),
				array (
					'Dateiname',
					'name'
				),
				array (
					'Dateigröße', 
					'size'
				),
				array (
					'Dateityp', 
					'ext'
				),
				array (
					'Datum', 
					'date'
				),
				array (
					'Ident-Nr.', 
					'ident'
				),
				array (
					'Überschrift', 
					'caption'
				),
			),
		), 
	),
	'tx_hetools_filelist_sortierung' => array(
		'exclude' => 1,
		'label'   => 'Sortierung',
		'config'  => array( 
			'type' => 'select',
			'items' => array (
				array (
					'aufsteigend (standard)', 
					'a'
				),
				array (
					'absteigend', 
					'r'
				),
			),
		), 
	),
	'tx_hetools_filelist_layout' => array(
		'exclude' => 1,
		'label'   => 'Ausgabeformat',
		'config'  => array( 
			'type' => 'select',
			'items' => array (
				array (
					'Dokumenttitel - Dateigröße (standard)', 
					0
				),
				array (
					'Dokumenttitel - Datum', 
					1
				),
				array (
					'Dokumenttitel - Dateigröße - Datum', 
					2
				),
				array (
					'nur Dokumenttitel', 
					3
				),
				array (
					'Beschreibung - Dokumenttitel', 
					4
				),
			),
		), 
	),
	);

t3lib_extMgm::addTCAcolumns('tt_content', $tempColumns, 1);

$pageColumns = array (
		'he_pagetype' => array (
				'exclude' => 1,
				'label' => 'Seitentyp',
				'config' => array (
					'type' => 'select',
					'items' => array (
						array ('Kein eigener Seitentyp (Vererbung)',''),
						array ('Normale Seite','Sonstige Seiten'),
						array ('Aktuelles','Aktuelles'),
						array ('Alumni','Alumni'),
						array ('Ausländische Studieninteressierte','Ausländische Studieninteressierte'),
						array ('Fakultätsseiten','Fakultätsseiten'),
						array ('Forschung','Forschung'),
						array ('International','International'),
						array ('Intranet','Intranet'),
						array ('Jubilaeumsseite','Jubiläumsseite'),
						array ('Kalendertermin','Kalendertermin'),
						array ('Personenseite','Personenseiten'),
						array ('Presse','Presse'),
						array ('Pressemitteilungen','Pressemitteilungen'),
						array ('Schulen','Schulen'),
						array ('Serviceeinrichtungen','Serviceeinrichtungen'),
						array ('Studierende','Studierende'),
						array ('Studieninteressierte','Studieninteressierte'),
						array ('Unternehmen','Unternehmen'),
						array ('Verwaltung','Verwaltung'),
						array ('Verwaltungsleitung','Verwaltungsleitung'),
					),
				)
		),
		'he_suchbegriffe' => array (
				'exclude' => 1,
				'label' => 'Suchbegriffe',
				'config' => array (
						'type' => 'input',
						'size' => '80',
				)
		),
);

t3lib_div::loadTCA('pages');
t3lib_extMgm::addTCAcolumns('pages',$pageColumns,1);
t3lib_extMgm::addToAllTCAtypes('pages','he_pagetype,he_suchbegriffe');

// lange Pfade für Dateiverzeichnisse erlauben
$TCA['tt_content']['columns']['select_key']['label'] = 'Verzeichnis (Sie können über das Stifsymbol rechts den Assistenten anklicken)';
$TCA['tt_content']['columns']['select_key']['config']['type'] = 'text';
$TCA['tt_content']['columns']['select_key']['config']['cols'] = 80;
$TCA['tt_content']['columns']['select_key']['config']['rows'] = 2;
$TCA['tt_content']['columns']['select_key']['config']['type'] = 'text';

// Wizard zum Auswählen des Verzeichnisses einfügen
$TCA['tt_content']['columns']['select_key']['config']['wizards']['link']['type'] = 'popup';
$TCA['tt_content']['columns']['select_key']['config']['wizards']['link']['title'] = 'Verzeichnis wählen';
$TCA['tt_content']['columns']['select_key']['config']['wizards']['link']['icon'] = 'link_popup.gif';
$TCA['tt_content']['columns']['select_key']['config']['wizards']['link']['script'] = 'browse_links.php?mode=wizard&act=folder';
$TCA['tt_content']['columns']['select_key']['config']['wizards']['link']['JSopenParams'] = 'height=400,width=600,status=0,menubar=0,scrollbars=1';
$TCA['tt_content']['columns']['select_key']['config']['eval'] = 'trim';

// t3lib_extMgm::addToAllTCAtypes('tt_content','tx_hetools_filelist_layout', 'uploads', 'after:select_key');

// zusätzliche Felder als Palette einfügen
// Neue Palette erzeugen

$palettenNr = max(array_keys($TCA['tt_content']['palettes'])) + 1;

$TCA['tt_content']['palettes'][$palettenNr] = array();
$TCA['tt_content']['palettes'][$palettenNr]['showitem'] = 'tx_hetools_filelist_dateitypen, ' . 
																													'tx_hetools_filelist_sortierfeld, ' . 
																													'tx_hetools_filelist_sortierung, ' . 
																													'--linebreak--, ' . 
																													'tx_hetools_filelist_layout';
$TCA['tt_content']['palettes'][$palettenNr]['canNotCollapse']='1';
t3lib_extMgm::addToAllTCAtypes('tt_content','--palette--;Optionen zu den Dateiverweisen;' . $palettenNr, 'uploads', 'after:select_key');

// Feld 'layout' im Bereich upload ausblenden
$uploadItems = explode(',',$TCA['tt_content']['types']['uploads']['showitem']);
foreach($uploadItems as $index=>$text) {
	$eintrag = trim($text); 
	if (strpos($eintrag,'layout')!==FALSE && strpos($eintrag,'layout')==0) {
		unset($uploadItems[$index]);
	}
}

$TCA['tt_content']['types']['uploads']['showitem'] = implode(',',$uploadItems);

// Ende Dateiverweise

$damColumns = array(
	'tx_hetools_dam_sortiernummer' => array(
		'exclude' => 1,
		'label'   => 'Sortiernummer',
		'config'  => array( 
			'type' => 'input', 
			'size' => '3',
		)
	)
);

t3lib_div::loadTCA('tx_dam');
t3lib_extMgm::addTCAcolumns('tx_dam',$damColumns,1);
t3lib_extMgm::addToAllTCAtypes('tx_dam', 'tx_hetools_dam_sortiernummer');

$TCA['tx_dam']['txdamInterface']['index_fieldList'] .= ',tx_hetools_dam_sortiernummer';

// Kontext-sensitive Hilfetexte ändern
t3lib_extMgm::addLLrefForTCAdescr('tt_content','EXT:he_tools/lang/locallang_csh_tt_content.php');

if (TYPO3_MODE == 'BE') {	
	t3lib_extMgm::addModulePath('web_txhetoolsM1', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');
	t3lib_extMgm::addModule('web', 'txhetoolsM1', '', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');
}

?>