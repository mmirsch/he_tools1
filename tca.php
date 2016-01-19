<?php

/*
 * ################### Bestellwesen #################### 
 */

$TCA['tx_hebest_hauptkategorie'] = array (
		'ctrl' => $TCA['tx_hebest_hauptkategorie']['ctrl'],
		'interface' => array (
				'showRecordFieldList' => 'title'
		),
		'feInterface' => $TCA['tx_hebest_hauptkategorie']['feInterface'],
		'columns' => array (
				'title' => array (
						'exclude' => 0,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_hauptkategorie.title',
						'config' => array (
								'type' => 'input',
								'size' => '30',
						)
				),
		),
		'types' => array (
				'0' => array('showitem' => 'hidden;;1;;1-1-1, title;;;;2-2-2')
		),
		'palettes' => array (
				'1' => array('showitem' => '')
		)
);



$TCA['tx_hebest_unterkategorie'] = array (
		'ctrl' => $TCA['tx_hebest_unterkategorie']['ctrl'],
		'interface' => array (
				'showRecordFieldList' => 'title'
		),
		'feInterface' => $TCA['tx_hebest_unterkategorie']['feInterface'],
		'columns' => array (
				'title' => array (
						'exclude' => 0,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_unterkategorie.title',
						'config' => array (
								'type' => 'input',
								'size' => '30',
						)
				),
		),
		'types' => array (
				'0' => array('showitem' => 'title;;;;1-1-1')
		),
		'palettes' => array (
				'1' => array('showitem' => '')
		)
);



$TCA['tx_hebest_eigenschaft1'] = array (
		'ctrl' => $TCA['tx_hebest_eigenschaft1']['ctrl'],
		'interface' => array (
				'showRecordFieldList' => 'title'
		),
		'feInterface' => $TCA['tx_hebest_eigenschaft1']['feInterface'],
		'columns' => array (
				'title' => array (
						'exclude' => 0,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_eigenschaft1.title',
						'config' => array (
								'type' => 'input',
								'size' => '30',
						)
				),
		),
		'types' => array (
				'0' => array('showitem' => 'title;;;;1-1-1')
		),
		'palettes' => array (
				'1' => array('showitem' => '')
		)
);

$TCA['tx_hebest_eigenschaft2'] = array (
		'ctrl' => $TCA['tx_hebest_eigenschaft1']['ctrl'],
		'interface' => array (
				'showRecordFieldList' => 'title'
		),
		'feInterface' => $TCA['tx_hebest_eigenschaft2']['feInterface'],
		'columns' => array (
				'title' => array (
						'exclude' => 0,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_eigenschaft2.title',
						'config' => array (
								'type' => 'input',
								'size' => '30',
						)
				),
		),
		'types' => array (
				'0' => array('showitem' => 'title;;;;1-1-1')
		),
		'palettes' => array (
				'1' => array('showitem' => '')
		)
);

$TCA['tx_hebest_lieferanten'] = array (
		'ctrl' => $TCA['tx_hebest_lieferanten']['ctrl'],
		'interface' => array (
				'showRecordFieldList' => 'keyword1,keyword2,title,anschrift,plz,stadt,tel,fax,www,email,' .
				'bemerkung,interne_bemerkung,bild,intranet',
		),
		'feInterface' => $TCA['tx_hebest_lieferanten']['feInterface'],
		'columns' => array (
				'title' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_lieferanten.title',
						'config' => array (
								'type' => 'input',
								'size' => '30',
						)
				),
				'keyword1' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_lieferanten.keyword1',
						'config' => array (
								'type' => 'select',
								'foreign_table' => 'tx_hebest_keyword1',
								'foreign_table_where' => 'AND tx_hebest_keyword1.pid=###CURRENT_PID### ORDER BY tx_hebest_keyword1.title',
								'size' => 10,
								'minitems' => 0,
								'maxitems' => 10,
								'wizards' => array(
										'_PADDING'  => 2,
										'_VERTICAL' => 1,
										'add' => array(
												'type'   => 'script',
												'title'  => 'Neues Stichwort erzeugen',
												'icon'   => 'add.gif',
												'params' => array(
														'table'    => 'tx_hebest_keyword1',
														'pid'      => '###CURRENT_PID###',
														'setValue' => 'prepend'
												),
												'script' => 'wizard_add.php',
										),
								),
						)
				),
				'keyword2' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_lieferanten.keyword2',
						'config' => array (
								'type' => 'select',
								'foreign_table' => 'tx_hebest_keyword2',
								'foreign_table_where' => 'AND tx_hebest_keyword2.pid=###CURRENT_PID### ORDER BY tx_hebest_keyword2.title',
								'size' => 10,
								'minitems' => 0,
								'maxitems' => 10,
								'wizards' => array(
										'_PADDING'  => 2,
										'_VERTICAL' => 1,
										'add' => array(
												'type'   => 'script',
												'title'  => 'Neues Stichwort erzeugen',
												'icon'   => 'add.gif',
												'params' => array(
														'table'    => 'tx_hebest_keyword2',
														'pid'      => '###CURRENT_PID###',
														'setValue' => 'prepend'
												),
												'script' => 'wizard_add.php',
										),
								),
						)
				),
				'anschrift' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_lieferanten.anschrift',
						'config' => array (
								'type' => 'input',
								'size' => '30',
						)
				),
				'plz' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_lieferanten.plz',
						'config' => array (
								'type' => 'input',
								'size' => '30',
						)
				),
				'stadt' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_lieferanten.stadt',
						'config' => array (
								'type' => 'select',
								'foreign_table' => 'tx_hebest_stadt',
								'foreign_table_where' => 'AND tx_hebest_stadt.pid=###CURRENT_PID### ORDER BY tx_hebest_stadt.title',
								'size' => 10,
								'minitems' => 0,
								'maxitems' => 10,
								'wizards' => array(
										'_PADDING'  => 2,
										'_VERTICAL' => 1,
										'add' => array(
												'type'   => 'script',
												'title'  => 'Neue Stadt anlegen',
												'icon'   => 'add.gif',
												'params' => array(
														'table'    => 'tx_hebest_stadt',
														'pid'      => '###CURRENT_PID###',
														'setValue' => 'prepend'
												),
												'script' => 'wizard_add.php',
										),
								),
						)
				),
				'tel' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_lieferanten.tel',
						'config' => array (
								'type' => 'input',
								'size' => '30',
						)
				),
				'fax' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_lieferanten.fax',
						'config' => array (
								'type' => 'input',
								'size' => '30',
						)
				),
				'www' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_lieferanten.www',
						'config' => array (
								'type' => 'input',
								'size' => '30',
						)
				),
				'email' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_lieferanten.email',
						'config' => array (
								'type' => 'input',
								'size' => '30',
						)
				),
				'bemerkung' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_lieferanten.bemerkung',
						'config' => array (
								'type' => 'text',
								'cols' => '80',
								'rows' => '5',
						)
				),
				'interne_bemerkung' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_lieferanten.interne_bemerkung',
						'config' => array (
								'type' => 'text',
								'cols' => '80',
								'rows' => '5',
						)
				),
				'bild' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_lieferanten.bild',
						'config' => array (
								'type' => 'group',
								'internal_type' => 'file',
								'allowed' => 'gif,png,jpeg,jpg',
								'max_size' => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],
								'uploadfolder' => 'uploads/tx_hebest',
								'size' => 1,
								'minitems' => 0,
								'maxitems' => 1,
						)
				),
				'intranet' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_lieferanten.intranet',
						'config' => array (
								'type' => 'check',
						)
				),
		),
		'types' => array (
				'0' => array('showitem' => 'title, keyword1, keyword2, anschrift, plz, stadt, tel, fax, www, email, ' .
						'bemerkung;;;richtext[],interne_bemerkung;;;richtext[],bild,intranet')
		),
		'palettes' => array (
				'1' => array('showitem' => '')
		)
);

$TCA['tx_hebest_hersteller'] = array (
		'ctrl' => $TCA['tx_hebest_hersteller']['ctrl'],
		'interface' => array (
				'showRecordFieldList' => 'keyword1,keyword2,title,anschrift,plz,stadt,tel,fax,www,email,' .
				'bemerkung,interne_bemerkung,bild,intranet',
		),
		'feInterface' => $TCA['tx_hebest_hersteller']['feInterface'],
		'columns' => array (
				'title' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_hersteller.title',
						'config' => array (
								'type' => 'input',
								'size' => '30',
						)
				),
				'keyword1' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_hersteller.keyword1',
						'config' => array (
								'type' => 'select',
								'foreign_table' => 'tx_hebest_keyword1',
								'foreign_table_where' => 'AND tx_hebest_keyword1.pid=###CURRENT_PID### ORDER BY tx_hebest_keyword1.title',
								'size' => 10,
								'minitems' => 0,
								'maxitems' => 10,
								'wizards' => array(
										'_PADDING'  => 2,
										'_VERTICAL' => 1,
										'add' => array(
												'type'   => 'script',
												'title'  => 'Create new record',
												'icon'   => 'add.gif',
												'params' => array(
														'table'    => 'tx_hebest_keyword1',
														'pid'      => '###CURRENT_PID###',
														'setValue' => 'prepend'
												),
												'script' => 'wizard_add.php',
										),
								),
						)
				),
				'keyword2' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_hersteller.keyword2',
						'config' => array (
								'type' => 'select',
								'foreign_table' => 'tx_hebest_keyword2',
								'foreign_table_where' => 'AND tx_hebest_keyword2.pid=###CURRENT_PID### ORDER BY tx_hebest_keyword2.title',
								'size' => 10,
								'minitems' => 0,
								'maxitems' => 10,
								'wizards' => array(
										'_PADDING'  => 2,
										'_VERTICAL' => 1,
										'add' => array(
												'type'   => 'script',
												'title'  => 'Create new record',
												'icon'   => 'add.gif',
												'params' => array(
														'table'    => 'tx_hebest_keyword2',
														'pid'      => '###CURRENT_PID###',
														'setValue' => 'prepend'
												),
												'script' => 'wizard_add.php',
										),
								),
						)
				),
				'anschrift' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_hersteller.anschrift',
						'config' => array (
								'type' => 'input',
								'size' => '30',
						)
				),
				'plz' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_hersteller.plz',
						'config' => array (
								'type' => 'input',
								'size' => '30',
						)
				),
				'stadt' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_hersteller.stadt',
						'config' => array (
								'type' => 'select',
								'foreign_table' => 'tx_hebest_stadt',
								'foreign_table_where' => 'AND tx_hebest_stadt.pid=###CURRENT_PID### ORDER BY tx_hebest_stadt.title',
								'size' => 10,
								'minitems' => 0,
								'maxitems' => 10,
								'wizards' => array(
										'_PADDING'  => 2,
										'_VERTICAL' => 1,
										'add' => array(
												'type'   => 'script',
												'title'  => 'Create new record',
												'icon'   => 'add.gif',
												'params' => array(
														'table'    => 'tx_hebest_stadt',
														'pid'      => '###CURRENT_PID###',
														'setValue' => 'prepend'
												),
												'script' => 'wizard_add.php',
										),
								),
						)
				),
				'tel' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_hersteller.tel',
						'config' => array (
								'type' => 'input',
								'size' => '30',
						)
				),
				'fax' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_hersteller.fax',
						'config' => array (
								'type' => 'input',
								'size' => '30',
						)
				),
				'www' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_hersteller.www',
						'config' => array (
								'type' => 'input',
								'size' => '30',
						)
				),
				'email' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_hersteller.email',
						'config' => array (
								'type' => 'input',
								'size' => '30',
						)
				),
				'bemerkung' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_hersteller.bemerkung',
						'config' => array (
								'type' => 'text',
								'cols' => '80',
								'rows' => '5',
						)
				),
				'interne_bemerkung' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_hersteller.interne_bemerkung',
						'config' => array (
								'type' => 'text',
								'cols' => '80',
								'rows' => '5',
						)
				),
				'bild' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_hersteller.bild',
						'config' => array (
								'type' => 'group',
								'internal_type' => 'file',
								'allowed' => 'gif,png,jpeg,jpg',
								'max_size' => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],
								'uploadfolder' => 'uploads/tx_hebest',
								'size' => 1,
								'minitems' => 0,
								'maxitems' => 1,
						)
				),
				'intranet' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_hersteller.intranet',
						'config' => array (
								'type' => 'check',
						)
				),
		),
		'types' => array (
				'0' => array('showitem' => 'title, keyword1, keyword2, anschrift, plz, stadt, tel, fax, www, email, ' .
						'bemerkung;;;richtext[],interne_bemerkung;;;richtext[],bild,intranet')
		),
		'palettes' => array (
				'1' => array('showitem' => '')
		)
);


$TCA['tx_hebest_stadt'] = array (
		'ctrl' => $TCA['tx_hebest_stadt']['ctrl'],
		'interface' => array (
				'showRecordFieldList' => 'title'
		),
		'feInterface' => $TCA['tx_hebest_stadt']['feInterface'],
		'columns' => array (
				'title' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_stadt.title',
						'config' => array (
								'type' => 'input',
								'size' => '30',
						)
				),
		),
		'types' => array (
				'0' => array('showitem' => 'title;;;;1-1-1')
		),
		'palettes' => array (
				'1' => array('showitem' => '')
		)
);

$TCA['tx_hebest_produktname'] = array (
		'ctrl' => $TCA['tx_hebest_produktname']['ctrl'],
		'interface' => array (
				'showRecordFieldList' => 'title'
		),
		'feInterface' => $TCA['tx_hebest_produktname']['feInterface'],
		'columns' => array (
				'title' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_produktname.title',
						'config' => array (
								'type' => 'input',
								'size' => '30',
						)
				),
		),
		'types' => array (
				'0' => array('showitem' => 'title;;;;1-1-1')
		),
		'palettes' => array (
				'1' => array('showitem' => '')
		)
);


$TCA['tx_hebest_keyword1'] = array (
		'ctrl' => $TCA['tx_hebest_keyword1']['ctrl'],
		'interface' => array (
				'showRecordFieldList' => 'title'
		),
		'feInterface' => $TCA['tx_hebest_keyword1']['feInterface'],
		'columns' => array (
				'title' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_keyword1.title',
						'config' => array (
								'type' => 'input',
								'size' => '30',
						)
				),
		),
		'types' => array (
				'0' => array('showitem' => 'title;;;;1-1-1')
		),
		'palettes' => array (
				'1' => array('showitem' => '')
		)
);


$TCA['tx_hebest_keyword2'] = array (
		'ctrl' => $TCA['tx_hebest_keyword2']['ctrl'],
		'interface' => array (
				'showRecordFieldList' => 'title'
		),
		'feInterface' => $TCA['tx_hebest_keyword2']['feInterface'],
		'columns' => array (
				'title' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_keyword2.title',
						'config' => array (
								'type' => 'input',
								'size' => '30',
						)
				),
		),
		'types' => array (
				'0' => array('showitem' => 'title;;;;1-1-1')
		),
		'palettes' => array (
				'1' => array('showitem' => '')
		)
);

$TCA['tx_hebest_artikel'] = array (
		'ctrl' => $TCA['tx_hebest_artikel']['ctrl'],
		'interface' => array (
				'showRecordFieldList' => 'produktname, hidden, artikelnummer, preis, hersteller_bezeichnung, hauptkategorie, unterkategorie, eigenschaft1, eigenschaft2, hersteller, ' .
				'lieferant, anzeigen_bis, ansprechpartner, ' .
				'bemerkung,interne_bemerkung,link,linktext,bild,intranet,oeffentlich_verbergen',
		),
		'feInterface' => $TCA['tx_hebest_artikel']['feInterface'],
		'columns' => array (
				'deleted' => array(
					'exclude' => 1,
					'label' => 'gelöscht',
					'config' => array(
						'type' => 'check',
						'default' => '0',
					)
				),
				'hidden' => array(
					'exclude' => 1,
					'label' => 'verborgen',
					'config' => array(
						'type' => 'check',
						'default' => '0',
					)
				),

				'produktname' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_artikel.produktname',
						'config' => array (
								'type' => 'input',
								'size' => '30',
								'eval' => 'required',
						)
				),
				'artikelnummer' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_artikel.artikelnummer',
						'config' => array (
								'type' => 'input',
								'size' => '30',
								'eval' => 'required, evaluniqueInPid',
						)
				),
				'preis' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_artikel.preis',
						'config' => array (
								'type' => 'input',
								'size' => '20',
								'eval' => 'required',
						)
				),
				'hersteller_bezeichnung' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_artikel.hersteller_bezeichnung',
						'config' => array (
								'type' => 'input',
								'size' => '30',
						)
				),

				'hauptkategorie' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_artikel.hauptkategorie',
						'config' => array (

								'internal_type' => 'db',
								'allowed' => 'tx_hebest_hauptkategorie',

								'type' => 'select',
								'foreign_table' => 'tx_hebest_hauptkategorie',
								'foreign_table_where' => 'AND tx_hebest_hauptkategorie.pid=###CURRENT_PID### ORDER BY tx_hebest_hauptkategorie.title',
								'size' => 10,
								'minitems' => 0,
								'maxitems' => 10,
								'wizards' => array(
										'_PADDING'  => 2,
										'_VERTICAL' => 1,
										'add' => array(
												'type'   => 'script',
												'title'  => 'Neue Kategorie anlegen',
												'icon'   => 'add.gif',
												'params' => array(
														'table'    => 'tx_hebest_hauptkategorie',
														'pid'      => '###CURRENT_PID###',
														'setValue' => 'prepend'
												),
												'script' => 'wizard_add.php',
										),
								),
						)
				),
				'unterkategorie' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_artikel.unterkategorie',
						'config' => array (
								'type' => 'select',
								'foreign_table' => 'tx_hebest_unterkategorie',
								'foreign_table_where' => 'AND tx_hebest_unterkategorie.pid=###CURRENT_PID### ORDER BY tx_hebest_unterkategorie.title',
								'size' => 10,
								'minitems' => 0,
								'maxitems' => 10,
								'wizards' => array(
										'_PADDING'  => 2,
										'_VERTICAL' => 1,
										'add' => array(
												'type'   => 'script',
												'title'  => 'Neue Unterkategorie anlegen',
												'icon'   => 'add.gif',
												'params' => array(
														'table'    => 'tx_hebest_unterkategorie',
														'pid'      => '###CURRENT_PID###',
														'setValue' => 'prepend'
												),
												'script' => 'wizard_add.php',
										),
								),
						)
				),
				'eigenschaft1' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_artikel.eigenschaft1',
						'config' => array (
								'type' => 'select',
								'foreign_table' => 'tx_hebest_eigenschaft1',
								'foreign_table_where' => 'AND tx_hebest_eigenschaft1.pid=###CURRENT_PID### ORDER BY tx_hebest_eigenschaft1.title',
								'size' => 10,
								'minitems' => 0,
								'maxitems' => 10,
								'wizards' => array(
										'_PADDING'  => 2,
										'_VERTICAL' => 1,
										'add' => array(
												'type'   => 'script',
												'title'  => 'Neue Eigenschaft-1 anlegen',
												'icon'   => 'add.gif',
												'params' => array(
														'table'    => 'tx_hebest_eigenschaft1',
														'pid'      => '###CURRENT_PID###',
														'setValue' => 'prepend'
												),
												'script' => 'wizard_add.php',
										),
								),
						)
				),
				'eigenschaft2' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_artikel.eigenschaft2',
						'config' => array (
								'type' => 'select',
								'foreign_table' => 'tx_hebest_eigenschaft2',
								'foreign_table_where' => 'AND tx_hebest_eigenschaft2.pid=###CURRENT_PID### ORDER BY tx_hebest_eigenschaft2.title',
								'size' => 10,
								'minitems' => 0,
								'maxitems' => 10,
								'wizards' => array(
										'_PADDING'  => 2,
										'_VERTICAL' => 1,
										'add' => array(
												'type'   => 'script',
												'title'  => 'Neue Eigenschaft-2 anlegen',
												'icon'   => 'add.gif',
												'params' => array(
														'table'    => 'tx_hebest_eigenschaft2',
														'pid'      => '###CURRENT_PID###',
														'setValue' => 'prepend'
												),
												'script' => 'wizard_add.php',
										),
								),
						)
				),
				'hersteller' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_artikel.hersteller',
						'config' => array (
								'type' => 'select',
								'foreign_table' => 'tx_hebest_hersteller',
								'foreign_table_where' => 'AND tx_hebest_hersteller.pid=###CURRENT_PID### ORDER BY tx_hebest_hersteller.title',
								'size' => 10,
								'minitems' => 0,
								'maxitems' => 10,
								'wizards' => array(
										'_PADDING'  => 2,
										'_VERTICAL' => 1,
										'add' => array(
												'type'   => 'script',
												'title'  => 'Neuen Hersteller anlegen',
												'icon'   => 'add.gif',
												'params' => array(
														'table'    => 'tx_hebest_hersteller',
														'pid'      => '###CURRENT_PID###',
														'setValue' => 'prepend'
												),
												'script' => 'wizard_add.php',
										),
								),
						)
				),
				'lieferant' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_artikel.lieferant',
						'config' => array (
								'type' => 'select',
								'foreign_table' => 'tx_hebest_lieferanten',
								'foreign_table_where' => 'AND tx_hebest_lieferanten.pid=###CURRENT_PID### ORDER BY tx_hebest_lieferanten.title',
								'size' => 10,
								'minitems' => 0,
								'maxitems' => 10,
								'wizards' => array(
										'_PADDING'  => 2,
										'_VERTICAL' => 1,
										'add' => array(
												'type'   => 'script',
												'title'  => 'Neuen Lieferant anlegen',
												'icon'   => 'add.gif',
												'params' => array(
														'table'    => 'tx_hebest_lieferanten',
														'pid'      => '###CURRENT_PID###',
														'setValue' => 'prepend'
												),
												'script' => 'wizard_add.php',
										),
								),
						)
				),
				'anzeigen_bis' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_artikel.anzeigen_bis',
						'config' => array (
								'type'     => 'input',
								'size'     => '8',
								'max'      => '20',
								'eval'     => 'date',
								'checkbox' => '0',
								'default'  => '0'
						)
				),
				'ansprechpartner' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_artikel.ansprechpartner',
						'config' => array (
								'type' => 'input',
								'size' => '30',
						)
				),
				'bemerkung' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_artikel.bemerkung',
						'config' => array (
								'type' => 'text',
								'cols' => '80',
								'rows' => '5',
						)
				),
				'interne_bemerkung' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_artikel.interne_bemerkung',
						'config' => array (
								'type' => 'text',
								'cols' => '80',
								'rows' => '5',
						)
				),
				'link' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_artikel.link',
						'config' => array (
								'type'     => 'input',
								'size'     => '80',
								'max'      => '255',
								'checkbox' => '',
								'eval'     => 'trim',
								'wizards'  => array(
										'_PADDING' => 2,
										'link'     => array(
												'type'         => 'popup',
												'title'        => 'Seite auswählen',
												'icon'         => 'link_popup.gif',
												'script'       => 'browse_links.php?mode=wizard',
												'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1'
										)
								)
						)
				),
				'linktext' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_artikel.linktext',
						'config' => array (
								'type' => 'input',
								'size' => '30',
						)
				),
				'hersteller_bezeichnung' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_artikel.hersteller_bezeichnung',
						'config' => array (
								'type' => 'input',
								'size' => '30',
						)
				),
				'bild' => array (
						'exclude' => 1,
						'l10n_mode' => $l10n_mode_image,
						'label' => 'LLL:EXT:lang/locallang_general.php:LGL.images',
						'config' => Array (
								'type' => 'group',
								'internal_type' => 'file',
								'allowed' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
								'max_size' => '10000',
								'uploadfolder' => 'uploads/tx_hebest',
								'show_thumbs' => '1',
								'size' => 3,
								'autoSizeMax' => 15,
								'maxitems' => '99',
								'minitems' => '0'
						)
				),
				'intranet' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_artikel.intranet',
						'config' => array (
								'type' => 'check',
						)
				),
				'oeffentlich_verbergen' => array (
						'exclude' => 1,
						'label' => 'LLL:EXT:he_tools/locallang_db.xml:tx_hebest_artikel.oeffentlich_verbergen',
						'config' => array (
								'type' => 'check',
						)
				),
		),
		'types' => array (
				'0' => array('showitem' => 'produktname, hidden, artikelnummer, preis, hersteller_bezeichnung, hauptkategorie, unterkategorie, eigenschaft1, eigenschaft2, ' .
						'hersteller, lieferant, anzeigen_bis, ansprechpartner, ' .
						'bemerkung;;;richtext[],interne_bemerkung;;;richtext[],link,linktext,bild,intranet,oeffentlich_verbergen')
		),
		'palettes' => array (
				'1' => array('showitem' => '')
		)
);

/*
 * ################### Zeitschriften #################### 
 */

$TCA['tx_he_zeitschriftenliste'] = array(
	'ctrl' => $TCA['tx_he_zeitschriftenliste']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'uid,deleted,hidden,titel,sortiertitel,signatur,bestandsnachweis',
	),
	'feInterface' => $TCA['tx_he_zeitschriftenliste']['feInterface'],
	'columns' => array(
		'deleted' => array(
			'exclude' => 1,
			'label' => 'gelöscht',
			'config' => array(
				'type' => 'check',
				'default' => '0',
			)
		),
		'hidden' => array(
			'exclude' => 1,
			'label' => 'verborgen',
			'config' => array(
				'type' => 'check',
				'default' => '0',
			)
		),
		'titel' => array(
			'exclude' => 1,
			'label' => 'Titel',
			'config' => array(
				'type' => 'text',	
				'cols' => '80',
				'rows' => '5',
			)
		),
		'sortiertitel' => array(
			'exclude' => 1,
			'label' => 'Titel für Einsortierung',
			'config' => array(
				'type' => 'text',	
				'cols' => '80',
				'rows' => '5',
			)
		),
		'signatur' => array(
			'exclude' => 1,
			'label' => 'Signatur',
			'config' => array(
				'type' => 'text',	
				'cols' => '80',
				'rows' => '5',
			)
		),
		'bestandsnachweis' => array(
			'exclude' => 1,
			'label' => 'Bestandsnachweis',
			'config' => array(
				'type' => 'text',	
				'cols' => '80',
				'rows' => '5',
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'titel;;;richtext[],sortiertitel,signatur,bestandsnachweis;;;richtext[]')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);


$TCA['tx_hetools_wmw_abteilungen'] = array (
	'ctrl' => $TCA['tx_hetools_wmw_abteilungen']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'title'
	),
	'feInterface' => $TCA['tx_hetools_wmw_abteilungen']['feInterface'],
	'columns' => array (
		'title' => array (		
			'exclude' => 0,		
			'label' => 'Titel',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;1;;1-1-1, title;;;;2-2-2')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);

$TCA['tx_hetools_wer_macht_was'] = array (
	'ctrl' => $TCA['tx_hetools_wer_macht_was']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'title, beschreibung, abteilungen, personen, ' .
															 'linkadresse, Linkbezeichnung, datei',	
	),
	'feInterface' => $TCA['tx_hetools_wer_macht_was']['feInterface'],
	'columns' => array (
	 	'title' => array (		
			'exclude' => 0,		
			'label' => 'Aufgaben-Schlagwort',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
				'eval' => 'required',
			)
		),
	 	'beschreibung' => array (		
			'exclude' => 0,		
			'label' => 'Beschreibung',		
			'config' => array (
				'type' => 'text',	
				'cols' => '80',
				'rows' => '5',
			)
		),

		'abteilungen' => array (		
			'exclude' => 0,		
			'label' => 'Abteilungen',		
			'config' => array (

				'internal_type' => 'db',
				'allowed' => 'tx_hetools_wmw_abteilungen',
	
				'type' => 'select',	
				'foreign_table' => 'tx_hetools_wmw_abteilungen',	
				'foreign_table_where' => 'AND tx_hetools_wmw_abteilungen.pid=###CURRENT_PID### ORDER BY tx_hetools_wmw_abteilungen.title',	
				'size' => 10,	
				'minitems' => 0,
				'maxitems' => 10,	
				'wizards' => array(
					'_PADDING'  => 2,
					'_VERTICAL' => 1,
					'add' => array(
						'type'   => 'script',
						'title'  => 'Neue Abteilung anlegen',
						'icon'   => 'add.gif',
						'params' => array(
							'table'    => 'tx_hetools_wmw_abteilungen',
							'pid'      => '###CURRENT_PID###',
							'setValue' => 'prepend'
						),
						'script' => 'wizard_add.php',
					),
				),
			)
		),
		'personen' => array (		
			'exclude' => 0,		
			'label' => 'Personen',		
			'config' => array (
				'type' => 'group',	
		 		'internal_type' => 'db',
				'allowed' => 'fe_users',
				'foreign_table' => 'fe_users',
				'foreign_table_where' => ' AND NOT FIND_IN_SET("71",fe_users.usergroup) ORDER BY fe_users.last_name,fe_users.first_name',	
				'size' => '5',
				'maxitems' => '10',
		 		'show_thumbs' => '0'
			)
		),

		'datei' => array (		
			'exclude' => 0,		
			'l10n_mode' => $l10n_mode_image,
			'label' => 'Datei',
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
				'max_size' => '10000',
				'uploadfolder' => 'uploads/wer_macht_was',
				'show_thumbs' => '0',
				'size' => 3,
				'autoSizeMax' => 15,
				'maxitems' => '99',
				'minitems' => '0'
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'title, beschreibung;;;richtext[], abteilungen, personen, ' .
															 'linkadresse, Linkbezeichnung, datei')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);

$TCA['tx_hetools_bereich'] = array (
	'ctrl' => $TCA['tx_hetools_bereich']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'title, telefon, email, bemerkung, ' .
															 'kategorie1, kategorie1',	
	),
	'feInterface' => $TCA['tx_hetools_bereich']['feInterface'],
	'columns' => array (
	 	'title' => array (		
			'exclude' => 0,		
			'label' => 'Bereich',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
				'eval' => 'required',
			)
		),
	 	'telefon' => array (		
			'exclude' => 0,		
			'label' => 'Telefonnummer',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'email' => array (		
			'exclude' => 0,		
			'label' => 'E-Mail-Adresse',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		
		'bemerkung' => array (		
			'exclude' => 0,		
			'label' => 'Bemerkung',		
			'config' => array (
				'type' => 'text',	
				'cols' => '80',
				'rows' => '5',
			)
		),
		
		'kategorie1' => array (		
			'exclude' => 0,		
			'label' => 'Kategorie 1',		
			'config' => array (
				'internal_type' => 'db',
				'allowed' => 'tx_hetools_kategorie1',
	
				'type' => 'select',	
				'foreign_table' => 'tx_hetools_kategorie1',	
				'foreign_table_where' => 'AND tx_hetools_kategorie1.pid=###CURRENT_PID### ORDER BY tx_hetools_kategorie1.title',	
				'size' => 10,	
				'minitems' => 0,
				'maxitems' => 10,	
				'wizards' => array(
					'_PADDING'  => 2,
					'_VERTICAL' => 1,
					'add' => array(
						'type'   => 'script',
						'title'  => 'Neue Kategorie anlegen',
						'icon'   => 'add.gif',
						'params' => array(
							'table'    => 'tx_hetools_kategorie1',
							'pid'      => '###CURRENT_PID###',
							'setValue' => 'prepend'
						),
						'script' => 'wizard_add.php',
					),
				),
			)
		),
		
		'kategorie2' => array (		
			'exclude' => 0,		
			'label' => 'Kategorie 2',		
			'config' => array (
				'internal_type' => 'db',
				'allowed' => 'tx_hetools_kategorie2',
	
				'type' => 'select',	
				'foreign_table' => 'tx_hetools_kategorie2',	
				'foreign_table_where' => 'AND tx_hetools_kategorie2.pid=###CURRENT_PID### ORDER BY tx_hetools_kategorie2.title',	
				'size' => 10,	
				'minitems' => 0,
				'maxitems' => 10,	
				'wizards' => array(
					'_PADDING'  => 2,
					'_VERTICAL' => 1,
					'add' => array(
						'type'   => 'script',
						'title'  => 'Neue Kategorie anlegen',
						'icon'   => 'add.gif',
						'params' => array(
							'table'    => 'tx_hetools_kategorie2',
							'pid'      => '###CURRENT_PID###',
							'setValue' => 'prepend'
						),
						'script' => 'wizard_add.php',
					),
				),
			)
		),
		
	),
	'types' => array (
		'0' => array('showitem' => 'title, telefon, email, bemerkung;;;richtext[], ' .
															 'kategorie1, kategorie2')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);

$TCA['tx_hetools_kategorie1'] = array (
	'ctrl' => $TCA['tx_hetools_kategorie1']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'title'
	),
	'feInterface' => $TCA['tx_hetools_kategorie1']['feInterface'],
	'columns' => array (
		'title' => array (		
			'exclude' => 0,		
			'label' => 'Kategorie 1',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;1;;1-1-1, title;;;;2-2-2')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);

$TCA['tx_hetools_kategorie2'] = array (
	'ctrl' => $TCA['tx_hetools_kategorie2']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'title'
	),
	'feInterface' => $TCA['tx_hetools_kategorie2']['feInterface'],
	'columns' => array (
		'title' => array (		
			'exclude' => 0,		
			'label' => 'Kategorie 2',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;1;;1-1-1, title;;;;2-2-2')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);

$TCA['tx_hetools_module_studiengaenge'] = array (
	'ctrl' => $TCA['tx_hetools_module_studiengaenge']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden, title, fakultaet, schwerpunkt, lsf_id, abschluss, sem_schwp, six_id, six_id_handbuch'
	),
	'feInterface' => $TCA['tx_hetools_module_studiengaenge']['feInterface'],
	'columns' => array (
		'title' => array (		
			'exclude' => 0,		
			'label' => 'Studiengang',		
			'config' => array (
				'type' => 'select',
				'items' => array (
					array('Biotechnologie','Biotechnologie'),
					array('Chemieingenieurwesen / Farbe und Lack','Chemieingenieurwesen / Farbe und Lack'),
					array('Energiesysteme und Energiemanagment','Energiesysteme und Energiemanagment'),
					array('Gebäude- Energie- und Umwelttechnik','Gebäude- Energie- und Umwelttechnik'),
					array('Internationale Technische Betriebswirtschaft','Internationale Technische Betriebswirtschaft'),
					array('Internationale Technische Betriebswirtschaft (SPO3)','Internationale Technische Betriebswirtschaft (SPO3)'),
					array('Wirtschaftsingenieurwesen','Wirtschaftsingenieurwesen'),
					array('Wirtschaftsingenieurwesen (SPO3)','Wirtschaftsingenieurwesen (SPO3)'),
					array('Fahrzeugtechnik','Fahrzeugtechnik'),
					array('Ingenieurpädagogik Elektrotechnik-Informationstechnik (EIP)','Ingenieurpädagogik Elektrotechnik-Informationstechnik (EIP)'),
					array('Ingenieurpädagogik Fahrzeugtechnik-Maschinenbau (FMP)','Ingenieurpädagogik Fahrzeugtechnik-Maschinenbau (FMP)'),
					array('Ingenieurpädagogik Informationstechnik-Elektrotechnik (IEP)','Ingenieurpädagogik Informationstechnik-Elektrotechnik (IEP)'),
					array('Ingenieurpädagogik Maschinenbau-Automatisierungstechnik (MAP)','Ingenieurpädagogik Maschinenbau-Automatisierungstechnik (MAP)'),
					array('Ingenieurpädagogik Versorgungstechnik-Maschinenbau (VMP)','Ingenieurpädagogik Versorgungstechnik-Maschinenbau (VMP)'),
					array('Kommunikationstechnik','Kommunikationstechnik'),
					array('Softwaretechnik und Medieninformatik (Schwerpunkt Medientechnik)','Softwaretechnik und Medieninformatik (Schwerpunkt Medientechnik)'),
					array('Softwaretechnik und Medieninformatik (Schwerpunkt Softwaretechnik)','Softwaretechnik und Medieninformatik (Schwerpunkt Softwaretechnik)'),
					array('Technische Informatik','Technische Informatik'),
					array('Maschinenbau / Entwicklung und Konstruktion','Maschinenbau / Entwicklung und Konstruktion'),
					array('Maschinenbau / Entwicklung und Produktion','Maschinenbau / Entwicklung und Produktion'),
					array('Mechatronik / Automatisierungstechnik','Mechatronik / Automatisierungstechnik'),
					array('Mechatronik / Elektrotechnik','Mechatronik / Elektrotechnik'),
					array('Mechatronik / Feinwerktechnik','Mechatronik / Feinwerktechnik'),
					array('MechatronikPlus','MechatronikPlus'),
					array('Studienmodell: BMW SpeedUp (neu ab 2010)','Studienmodell: BMW SpeedUp (neu ab 2010)'),
					array('Studienmodell: PSM - Praxisintegriertes Studium','Studienmodell: PSM - Praxisintegriertes Studium'),
					array('Bildung und Erziehung in der Kindheit','Bildung und Erziehung in der Kindheit'),
					array('Pflege / Pflegemanagement','Pflege / Pflegemanagement'),
					array('Pflegepädagogik','Pflegepädagogik'),
					array('Soziale Arbeit','Soziale Arbeit'),
					array('Versorgungstechnik und Umwelttechnik','Versorgungstechnik und Umwelttechnik'),
					array('Internationales Wirtschaftsingenieurwesen','Internationales Wirtschaftsingenieurwesen'),
					array('Wirtschaftsinformatik','Wirtschaftsinformatik'),
					array('Angewandte Oberflächen- und Materialwissenschaften','Angewandte Oberflächen- und Materialwissenschaften'),
					array('Automotive Systems','Automotive Systems'),
					array('Design and Development in Automotive and Mechanical Engineering','Design and Development in Automotive and Mechanical Engineering'),
					array('Energie- und Gebäudetechnik','Energie- und Gebäudetechnik'),
					array('International Industrial Management (MBA)','International Industrial Management (MBA)'),
					array('Innovationsmanagement','Innovationsmanagement'),
					array('Pflegewissenschaften','Pflegewissenschaften'),
					array('Soziale Arbeit (Master)','Soziale Arbeit (Master)'),
					array('Sozialwirtschaft','Sozialwirtschaft'),
					array('Technische Betriebswirtschaft/Automobilindustrie','Technische Betriebswirtschaft/Automobilindustrie'),
					array('Umweltschutz','Umweltschutz'),				
				),
			)
		),
		'fakultaet' => array (		
			'exclude' => 0,		
			'label' => 'Fakultät',		
			'config' => array (
				'type' => 'select',
				'items' => array (
					array('AN', 'AN'),
					array('BW', 'BW'),
					array('FZ', 'FZ'),
					array('GS', 'GS'),
					array('GL', 'GL'),
					array('GU', 'GU'),
					array('IT', 'IT'),
					array('MB', 'MB'),
					array('ME', 'ME'),
					array('SP', 'SP'),
					array('WI', 'WI'),
				),
			)
		),
		'schwerpunkt' => array (		
			'exclude' => 0,		
			'label' => 'Schwerpunkt',		
			'config' => array (
				'type' => 'select',	
				'items' => array (
					array('kein Schwerpunkt',''),
					array('Antrieb','Antrieb'),
					array('Car Electronics','Car Electronics'),
					array('Design and Manufacturing','Design and Manufacturing'),
					array('Elektrische Anlagen','Elektrische Anlagen'),
					array('Elektrische Antriebe','Elektrische Antriebe'),
					array('Energie- und Gebäudetechnik (VUB / EGT)','Energie- und Gebäudetechnik (VUB / EGT)'),
					array('Energietechnik (ET)','Energietechnik (ET)'),
					array('Entwicklung und Konstruktion','Entwicklung und Konstruktion'),
					array('Entwicklung und Produktion','Entwicklung und Produktion'),
					array('Gebäudetechnik (GT)','Gebäudetechnik (GT)'),
					array('Umwelttechnik (UT)','Umwelttechnik (UT)'),
					array('Fahrwerk und Regelsysteme','Fahrwerk und Regelsysteme'),
					array('Feinwerktechnik','Feinwerktechnik'),
					array('Karosserie','Karosserie'),
					array('Kfz-Elektronik','Kfz-Elektronik'),
					array('Komponenten der AT','Komponenten der AT'),
					array('Mechatronics','Mechatronics'),
					array('Medientechnik','Medientechnik'),
					array('Mikrosystemtechnik (ETB)','Mikrosystemtechnik (ETB)'),
					array('Mikrosystemtechnik (FMB)','Mikrosystemtechnik (FMB)'),
					array('Service','Service'),
					array('Software Based Automotive Systems','Software Based Automotive Systems'),
					array('Software - Feldbusse und Netze','Software - Feldbusse und Netze'),
					array('Softwaretechnik','Softwaretechnik'),
					array('Umwelt - Wasser - Abwasser (VUB / UWA)','Umwelt - Wasser - Abwasser (VUB / UWA)'),
					array('Vehicle Dynamics','Vehicle Dynamics'),
				),
			)
		),
		'lsf_stdg' => array (		
			'exclude' => 0,		
			'label' => 'LSF-Studiengangkürzel',		
			'config' => array (
				'type' => 'input',	
				'size' => '4',
			)
		),
		'lsf_abs' => array (		
			'exclude' => 0,		
			'label' => 'LSF-Abschluss',		
			'config' => array (
				'type' => 'select',	
				'items' => array (
					array('Bachelor','84'),
					array('Master','90'),
				),
			)
		),
		'abschluss' => array (		
			'exclude' => 0,		
			'label' => 'Abschluss',		
			'config' => array (
				'type' => 'input',	
				'size' => '40',
			)
		),
		'sem_schwp' => array (		
			'exclude' => 0,		
			'label' => 'Vertiefung/Schwerpunkt ab Semester',		
			'config' => array (
				'type' => 'input',	
				'size' => '2',
			)
		),
		'six_id' => array (		
			'exclude' => 0,		
			'label' => 'SIX-Id',		
			'config' => array (
				'type' => 'input',	
				'size' => '10',
			)
		),
		'six_id_handbuch' => array (		
			'exclude' => 0,		
			'label' => 'SIX-Id des Modulhandbuchs',		
			'config' => array (
				'type' => 'input',	
				'size' => '10',
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'hidden, title, fakultaet, schwerpunkt, lsf_stdg, lsf_abs, abschluss, sem_schwp, six_id, six_id_handbuch')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);

$TCA['tx_hetools_module_vertiefungen'] = array (
		'ctrl' => $TCA['tx_hetools_module_vertiefungen']['ctrl'],
		'interface' => array (
				'showRecordFieldList' => 'hidden, modstud_id, vertiefung, kuerzel, version'
		),
		'feInterface' => $TCA['tx_hetools_module_vertiefungen']['feInterface'],
		'columns' => array (
				'modstud_id' => array (
						'exclude' => 0,
						'label' => 'Zugeordneter Studiengang',
						'config' => array (
								'internal_type' => 'db',
								'allowed' => 'tx_hetools_module_studiengaenge',
								'type' => 'select',
								'foreign_table' => 'tx_hetools_module_studiengaenge',
								'foreign_table_where' => 'AND tx_hetools_module_studiengaenge.pid=###CURRENT_PID### ORDER BY tx_hetools_module_studiengaenge.title',
								'size' => 40,
								'minitems' => 1,
								'maxitems' => 1,
						)
				),
				'kuerzel' => array (
						'exclude' => 0,
						'label' => 'LSF-Vertiefungskürzel',
						'config' => array (
								'type' => 'input',
								'size' => '4',
						)
				),
				'vertiefung' => array (
						'exclude' => 0,
						'label' => 'Vertiefung',
						'config' => array (
								'type' => 'input',
								'size' => '80',
						)
				),
				'version' => array (
						'exclude' => 0,
						'label' => 'POS-Version',
						'config' => array (
								'type' => 'input',
								'size' => '4',
						)
				),
		),
		'types' => array (
				'0' => array('showitem' => 'hidden, modstud_id, kuerzel, vertiefung, version')
		),
		'palettes' => array (
				'1' => array('showitem' => '')
		)
);


$TCA['tx_hetools_veranstaltungen'] = array (
	'ctrl' => $TCA['tx_hetools_veranstaltungen']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden, title, ort, raum, max_teilnehmer, link, datum, ' .
															 'startzeit, endzeit, intervall, pause',
	),	
	
	'feInterface' => $TCA['tx_hetools_bereich']['feInterface'],
	'columns' => array (
	 	'title' => array (		
			'exclude' => 1,		
			'label' => 'Titel der Veranstaltung',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
				'eval' => 'required',
			)
		),
	 	'ort' => array (		
			'exclude' => 1,		
			'label' => 'Veranstaltungsort',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
				'eval' => 'required'
			)
		),
	 	'raum' => array (		
			'exclude' => 1,		
			'label' => 'Raum',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'max_teilnehmer' => array (
			'exclude' => 1,
			'label' => 'Max. Anzahl der Teilnehmer',
			'config' => array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'required'
			)
		),
		'link' => array (		
			'exclude' => 1,		
			'label' => 'Webseite zur Veranstaltung',		
			'config' => array (
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'pages',
				'size' => '1',
				'autoSizeMax' => 1,
				'maxitems' => '1',
				'minitems' => '0',
				'show_thumbs' => '1',
				'wizards' => array(
					'suggest' => array(
						'type' => 'suggest'
					)
				)
			)
		),		
		'datum' => array (		
			'exclude' => 1,		
			'label' => 'Datum der Veranstaltung',		
			'config' => array (
				'type' => 'input',
				'size' => '10',
				'max' => '20',
				'eval' => 'date,required',
				'default' => '0'
			)
		),
	 	'startzeit' => array (		
			'exclude' => 1,		
			'label' => 'Beginn der Veranstaltung (hh:mm)',		
			'config' => array (
				'type' => 'input',	
				'size' => '6',
			)
		),
	 	'endzeit' => array (		
			'exclude' => 1,		
			'label' => 'Ende der Veranstaltung (hh:mm)',		
			'config' => array (
				'type' => 'input',	
				'size' => '6',
			)
		),
	 	'intervall' => array (		
			'exclude' => 1,		
			'label' => 'Dauer eines Termins in Minuten)',		
			'config' => array (
				'type' => 'select',	
				'items' => array (
					array('5','5'),
					array('10','10'),
					array('15','15'),
					array('20','20'),
					array('25','25'),
					array('30','30'),
					array('35','35'),
					array('40','40'),
					array('45','45'),
					array('50','50'),
					array('55','55'),
					array('60','60'),
					array('90','90'),
					array('120','120'),
					),
			)
		),
		'pause' => array (
			'exclude' => 1,
			'label' => 'Pause zwischen zwei Terminen (Minuten)',
			'config' => array (
				'type' => 'input',
				'size' => '3',
			)
		),

	),
	'types' => array (
		'0' => array('showitem' => 'hidden, title, ort, raum, max_teilnehmer, link, datum, ' .
															 'startzeit, endzeit, intervall, pause')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);

$TCA['tx_hetools_veranstaltungen_termine'] = array (
	'ctrl' => $TCA['tx_hetools_veranstaltungen_termine']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden, veranstaltung, von, bis',
	),	
	
	'feInterface' => $TCA['tx_hetools_veranstaltungen_termine']['feInterface'],
	'columns' => array (
	 	'veranstaltung' => array (		
			'exclude' => 1,		
			'label' => 'Veranstaltung',		
			'config' => array (
				'internal_type' => 'db',
				'allowed' => 'tx_hetools_veranstaltungen',
				'type' => 'select',	
				'foreign_table' => 'tx_hetools_veranstaltungen',	
				'foreign_table_where' => 'AND tx_hetools_veranstaltungen.pid=###CURRENT_PID### ORDER BY tx_hetools_veranstaltungen.title',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,	
			)
		),
	 	'von' => array (		
			'exclude' => 1,		
			'label' => 'Beginn (hh:mm)',		
			'config' => array (
				'type' => 'input',	
				'size' => '8',
			)
		),
		'bis' => array (
			'exclude' => 1,
			'label' => 'Ende (hh:mm)',
			'config' => array (
				'type' => 'input',
				'size' => '8',
			)
		),

	),
	'types' => array (
		'0' => array('showitem' => 'hidden, veranstaltung, von, bis')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);

$TCA['tx_hetools_veranstaltungen_belegung'] = array (
	'ctrl' => $TCA['tx_hetools_veranstaltungen_belegung']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden, termin, username',	
	),	
	
	'feInterface' => $TCA['tx_hetools_veranstaltungen_belegung']['feInterface'],
	'columns' => array (
	 	'termin' => array (		
			'exclude' => 0,		
			'label' => 'Termin',		
			'config' => array (
				'type' => 'select',
				'foreign_table' => 'tx_hetools_veranstaltungen_termine',	
				'foreign_table_where' => ' AND tx_hetools_veranstaltungen_termine.pid=###CURRENT_PID### ORDER BY tx_hetools_veranstaltungen_termine.veranstaltung, tx_hetools_veranstaltungen_termine.von ASC',
				'size' => 10,	
				'minitems' => 0,
				'maxitems' => 1,	
			)
		),
	 	'username' => array (		
			'exclude' => 0,		
			'label' => 'Benutzer',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		
	),
	'types' => array (
		'0' => array('showitem' => 'hidden, termin, username')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);


$TCA['tx_hetools_veranstaltungen_abhaengigkeiten'] = array (
	'ctrl' => $TCA['tx_hetools_veranstaltungen_abhaengigkeiten']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden, title, veranstaltungen',
	),

	'feInterface' => $TCA['tx_hetools_veranstaltungen_belegung']['feInterface'],
	'columns' => array (
		'title' => array (
			'exclude' => 0,
			'label' => 'Titel',
			'config' => array (
				'type' => 'input',
				'size' => '30',
			)
		),

		'veranstaltungen' => array (
			'exclude' => 0,
			'label' => 'Termin',
			'config' => array (
				'internal_type' => 'db',
				'allowed' => 'tx_hetools_veranstaltungen',
				'type' => 'group',
				'foreign_table' => 'tx_hetools_veranstaltungen',
				'foreign_table_where' => 'AND tx_hetools_veranstaltungen.pid=###CURRENT_PID### ORDER BY tx_hetools_veranstaltungen.title',
				'size' => 10,
				'minitems' => 2,
				'maxitems' => 2,
			)
		),


	),
	'types' => array (
		'0' => array('showitem' => 'hidden, title, veranstaltungen')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);

$TCA['tx_hetools_einfuehrungsveranstaltungen'] = array (
		'ctrl' => $TCA['tx_hetools_einfuehrungsveranstaltungen']['ctrl'],
		'interface' => array (
				'showRecordFieldList' => 'hidden, datum, beginn, ende, raum, dozent, standort',
		),
		'feInterface' => $TCA['tx_hetools_bereich']['feInterface'],
		'columns' => array (
				'datum' => array (
						'exclude' => 1,
						'label' => 'Datum der Veranstaltung',
						'config' => array (
								'type' => 'input',
								'size' => '10',
								'max' => '20',
								'eval' => 'date,required',
								'default' => '0'
						)
				),
				'beginn' => array (
						'exclude' => 1,
						'label' => 'Beginn der Veranstaltung (hh:mm)',
						'config' => array (
								'type' => 'input',
								'size' => '6',
						)
				),
				'ende' => array (
						'exclude' => 1,
						'label' => 'Ende der Veranstaltung (hh:mm)',
						'config' => array (
								'type' => 'input',
								'size' => '6',
						)
				),
				'raum' => array (
						'exclude' => 1,
						'label' => 'Raum',
						'config' => array (
								'type' => 'input',
								'size' => '30',
								'eval' => 'required',
						)
				),
				'dozent' => array (
						'exclude' => 1,
						'label' => 'Dozent',
						'config' => array (
								'type' => 'input',
								'size' => '30',
						)
				),
				'standort' => array (
						'exclude' => 1,
						'label' => 'Standort',
						'config' => array (
								'type' => 'input',
								'size' => '30',
						)
				),
		),
		'types' => array (
				'0' => array('showitem' => 'hidden, datum, beginn, ende, raum, dozent, standort')
		),
		'palettes' => array (
				'1' => array('showitem' => '')
		)
	);
	
	$TCA['tx_hetools_infoscreen_elemente'] = array (
		'ctrl' => $TCA['tx_hetools_infoscreen_elemente']['ctrl'],
		'interface' => array (
				'showRecordFieldList' => 'hidden, title, beschreibung, bild, raum',
		),
		'feInterface' => $TCA['tx_hetools_bereich']['feInterface'],
		'columns' => array (
				'title' => array (
						'exclude' => 1,
						'label' => 'Titel',
						'config' => array (
								'type' => 'input',
								'size' => '80',
								'eval' => 'required',
						)
				),
				
				'beschreibung' => array (
						'exclude' => 1,
						'label' => 'Beschreibung',
						'config' => array (
								'type' => 'text',
								'rows' => '5',
								'cols' => '80',
						)
				),
				'raum' => array (
						'exclude' => 1,
						'label' => 'Raum',
						'config' => array (
								'type' => 'input',
								'size' => '30',
						)
				),
				'bild' => array (
						'exclude' => 0,
						'label' => 'Grafik',
						'config' => Array (
								'type' => 'group',
								'internal_type' => 'file',
								'allowed' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
								'max_size' => '10000',
								'uploadfolder' => 'fileadmin/medien/Intranet/infoscreen',
								'show_thumbs' => '0',
								'size' => 3,
								'autoSizeMax' => 15,
								'maxitems' => '99',
								'minitems' => '0'
						)	
					),
		),
		'types' => array (
				'0' => array('showitem' => 'hidden, title, beschreibung;;;richtext[], bild, raum')
		),
		'palettes' => array (
				'1' => array('showitem' => '')
		)
	);

	$TCA['tx_hetools_infoscreen_anzeige_zeitraeume'] = array (
			'ctrl' => $TCA['tx_hetools_infoscreen_anzeige_zeitraeume']['ctrl'],
			'interface' => array (
					'showRecordFieldList' => 'hidden, title, von, bis , anzeigetyp, anzeigeObjekt, kalenderKategorie, inhaltsElement',
			),
			'feInterface' => $TCA['tx_hetools_bereich']['feInterface'],
			'columns' => array (
					'title' => array (
							'exclude' => 1,
							'label' => 'Bezeichnung der Anzeigeart',
							'config' => array (
								'type' => 'input',
								'size' => '80',
								'eval' => 'required',
							)
					),
					'von' => array (
							'exclude' => 1,
							'label' => 'Anzeige-Beginn',
							'config' => array (
									'type' => 'input',
									'size' => '10',
									'max' => '20',
									'eval' => 'datetime',
									'default' => '0'
							)
					),
					'bis' => array (
							'exclude' => 1,
							'label' => 'Anzeige-Ende',
							'config' => array (
									'type' => 'input',
									'size' => '10',
									'max' => '20',
									'eval' => 'datetime',
									'default' => '0'
							)
					),
					'anzeigetyp' => array (
							'exclude' => 1,
							'label' => 'Anzeige-Typ',
							'config' => array (
									'type' => 'select',
									'items' => array (
											array('Kalendertermine','KALENDERTERMINE'),
											array('Seitenelement anzeigen','SEITENINHALT'),
											array('Video anzeigen','VIDEO'),
									),
							)
					),					
					'anzeigeObjekt' => array (
							'exclude' => 1,
							'label' => 'Anzeigeobjekt',
							'config' => array (
									'internal_type' => 'db',
									'allowed' => 'pages',
									'type' => 'group',
									'foreign_table' => 'pages',
									'foreign_table_where' => 'AND pages.pid = 132598 ORDER BY title',									
									'size' => 1,
									'minitems' => 0,
									'maxitems' => 1,
							)
					),
					'kalenderKategorie' => array (
							'exclude' => 1,
							'label' => 'Kalender-Kategorie',
							'config' => array (
									'internal_type' => 'db',
									'allowed' => 'tx_cal_category',
									'type' => 'group',
									'foreign_table' => 'tx_cal_category',
									'foreign_table_where' => 'AND tx_cal_category.pid = 33142 ORDER BY tx_cal_category.title',
									'size' => 1,
									'minitems' => 0,
									'maxitems' => 1,
							)
					),
					'inhaltsElement' => array (
							'exclude' => 1,
							'label' => 'Inhalts-Element',
							'config' => array (
									'internal_type' => 'db',
									'allowed' => 'tt_content',
									'type' => 'group',
									'foreign_table' => 'tt_content',
									'foreign_table_where' => 'AND tt_content.pid = 136063 ORDER BY tt_content.title',
									'size' => 1,
									'minitems' => 0,
									'maxitems' => 1,
							)
					),
			),
			'types' => array (
					'0' => array('showitem' => 'hidden, title, von, bis , anzeigetyp, anzeigeObjekt, kalenderKategorie, inhaltsElement')
			),
			'palettes' => array (
					'1' => array('showitem' => '')
			)
	);
	
	$TCA['tx_hetools_infoscreen_redirects'] = array (
			'ctrl' => $TCA['tx_hetools_infoscreen_redirects']['ctrl'],
			'interface' => array (
					'showRecordFieldList' => 'hidden, title, ip, redirect_url',
			),
			'feInterface' => $TCA['tx_hetools_bereich']['feInterface'],
			'columns' => array (
					'title' => array (
							'exclude' => 1,
							'label' => 'Titel',
							'config' => array (
									'type' => 'input',
									'size' => '80',
									'eval' => 'required',
							)
					),
					'ip' => array (
							'exclude' => 1,
							'label' => 'IP-Adresse',
							'config' => array (
									'type' => 'input',
									'size' => '80',
									'eval' => 'required',
							)
					),
					'redirect_url' => array (
							'exclude' => 1,
							'label' => 'Redirect-URL (TYPO3-Seiten-ID oder komplette URL)',
							'config' => array (
									'type' => 'input',
									'size' => '80',
									'eval' => 'required',
							)
					),
			),
			'types' => array (
					'0' => array('showitem' => 'hidden, title, ip, redirect_url')
			),
			'palettes' => array (
					'1' => array('showitem' => '')
			)
	);
	
	
	$TCA['tx_he_standorte'] = array (
			'ctrl' => $TCA['tx_he_standorte']['ctrl'],
			'interface' => array (
					'showRecordFieldList' => 'title, title_en, kuerzel'
			),
			'feInterface' => $TCA['tx_he_standorte']['feInterface'],
			'columns' => array (
					'title' => array (
							'exclude' => 0,
							'label' => 'Bezeichnung',
							'config' => array (
									'type' => 'input',
									'size' => '80',
							)
					),
					'title_en' => array (
							'exclude' => 0,
							'label' => 'Bezeichnung englisch',
							'config' => array (
									'type' => 'input',
									'size' => '80',
							)
					),
					'kuerzel' => array (
							'exclude' => 0,
							'label' => 'Kürzel',
							'config' => array (
									'type' => 'input',
									'size' => '4',
							)
					),
			),
			'types' => array (
					'0' => array('showitem' => 'hidden, title, title_en, kuerzel')
			),
			'palettes' => array (
					'1' => array('showitem' => '')
			)
	);
	
	$TCA['tx_he_fakultaeten'] = array (
			'ctrl' => $TCA['tx_he_fakultaeten']['ctrl'],
			'interface' => array (
					'showRecordFieldList' => 'title, title_en, kuerzel, standort'
			),
			'feInterface' => $TCA['tx_he_fakultaeten']['feInterface'],
			'columns' => array (
					'title' => array (
							'exclude' => 0,
							'label' => 'Bezeichnung',
							'config' => array (
									'type' => 'input',
									'size' => '80',
							)
					),
					'title_en' => array (
							'exclude' => 0,
							'label' => 'Bezeichnung englisch',
							'config' => array (
									'type' => 'input',
									'size' => '80',
							)
					),
					'kuerzel' => array (
							'exclude' => 0,
							'label' => 'Kürzel',
							'config' => array (
									'type' => 'input',
									'size' => '4',
							)
					),
					'standort' => array (
							'exclude' => 0,
							'label' => 'Standort',
							'config' => array (
								'internal_type' => 'db',
								'allowed' => 'tx_he_standorte',
								'type' => 'select',	
								'foreign_table' => 'tx_he_standorte',	
								'foreign_table_where' => 'AND tx_he_standorte.pid=###CURRENT_PID### ORDER BY tx_he_standorte.title',	
								'size' => 1,	
								'minitems' => 0,
								'maxitems' => 1,	
							)
						),
			),
			'types' => array (
					'0' => array('showitem' => 'hidden, title, title_en, kuerzel, standort')
			),
			'palettes' => array (
					'1' => array('showitem' => '')
			)
	);
	
	$TCA['tx_he_studiengaenge'] = array (
			'ctrl' => $TCA['tx_he_studiengaenge']['ctrl'],
			'interface' => array (
					'showRecordFieldList' => 'title, title_en, kuerzel, fakultaet'
			),
			'feInterface' => $TCA['tx_he_studiengaenge']['feInterface'],
			'columns' => array (
					'title' => array (
							'exclude' => 0,
							'label' => 'Bezeichnung',
							'config' => array (
									'type' => 'input',
									'size' => '80',
							)
					),
					'title_en' => array (
							'exclude' => 0,
							'label' => 'Bezeichnung englisch',
							'config' => array (
									'type' => 'input',
									'size' => '80',
							)
					),
					'kuerzel' => array (
							'exclude' => 0,
							'label' => 'Kürzel',
							'config' => array (
									'type' => 'input',
									'size' => '4',
							)
					),
					'fakultaet' => array (
							'exclude' => 0,
							'label' => 'Fakultät',
							'config' => array (
								'internal_type' => 'db',
								'allowed' => 'tx_he_fakultaeten',
								'type' => 'select',	
								'foreign_table' => 'tx_he_fakultaeten',	
								'foreign_table_where' => 'AND tx_he_fakultaeten.pid=###CURRENT_PID### ORDER BY tx_he_fakultaeten.title',	
								'size' => 1,	
								'minitems' => 0,
								'maxitems' => 1,	
							)
						),
			),
			'types' => array (
					'0' => array('showitem' => 'hidden, title, title_en, kuerzel, fakultaet')
			),
			'palettes' => array (
					'1' => array('showitem' => '')
			)
	);
	
?>