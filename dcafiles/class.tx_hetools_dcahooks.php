<?php

require_once(t3lib_extMgm::extPath('he_tools').'pi2/class.tx_hetools_pi2.php');

class tx_hetools_dcahooks	{
	/**
	 * This method expects the current DCA and the table we are workin on.
	 * It simple adds another modification.
	 * 
	 * @param	array	$currentDCA: The current DCA passed by reference (maybe this was already altered by another hook!!!)
	 * @param	string	$table: The table we are working on
	 */
	
	
	function alterDCA_onLoad(&$currentDCA, $table)	{
		global $BE_USER,$TCA;		
		$maxTabs = 10;
		$tabConfig = tx_hetools_pi2::getTabConfig();
		
//t3lib_utility_Debug::debugInPopUpWindow($TCA);
//t3lib_div::devlog('Zeit: ' . time(),'alterDCA_onLoad',0);
		
		$seitenTypen = array (
			'STARTSEITE_FAKULTAET' => 'Startseite Fakultät',
			'STARTSEITE_STUDIENGANG' => 'Startseite Studiengang',
			'FREIE_CONTAINER_EINSTIEG' => 'Freie Container - Einstiegsseite (Maximal ' . $maxTabs . ')',
			'FREIE_CONTAINER_UNTERSEITE' => 'Freie Container - Unterseite (Maximal ' . $maxTabs . ')',
			'FREIE_CONTAINER_UNTERSEITE_TEXT' => 'Freie Container - Unterseite mit Einleitungstext (Maximal ' . $maxTabs . ')',
			'STARTSEITE_JUBILAEUM' => 'Startseite Jubiläum - (Maximal ' . $maxTabs . ')',
			'UNTERSEITE_JUBILAEUM' => 'Unterseite Jubiläum - (Maximal ' . $maxTabs . ')',
		);
		
		$currentDCA[0]['modifications'][] = array (
				'method' => 'add',
				'type' => 'sheet',
				'name' => 'allgemein',
				'label' => 'Allgemein',
		);
		
		
		if ($BE_USER->user['admin']) {
			
			$menu = array (
							'method' => 'add',
							'path' => 'sheets/allgemein/ROOT/el',
							'type' => 'field',
							'field_config' => array (
									'name' => 'auswahl',
									'label' => 'Auswahl',
									'exclude' => '1',
									'config' => array (
											'type' => 'select',
											'items' => array (),
									),
							),
					);
			foreach ($seitenTypen as $key=>$label) {
				$menu['field_config']['config']['items'][] =	array (
						'0' => $label,
						'1'  => $key,
				);
			}
			$currentDCA[0]['modifications'][] = $menu;
		}

/*
 * Conditions festlegen
 * (Tabs je nach Menüauswahl konfigurieren)
 */
		foreach ($seitenTypen as $key=>$label) {
			$conditions[$key] =	array (
					'source' => 'cce',
					'if' => 'isEqual',
					'isXML' => TRUE,
					'cefield' => 'pi_flexform',
					'path' => 'data/allgemein/lDEF/auswahl/vDEF',
					'compareTo' => $key,
			);
		}
		
		$modifications['STARTSEITE_FAKULTAET'][] = array (
				'method' => 'add',
				'path' => 'sheets/allgemein/ROOT/el',
				'type' => 'field',
				'field_config' => array (
						'name' => 'title',
						'label' => 'Titel (erscheint oberhalb der Banner-Grafiken)',
						'config' => array(
								'type' => 'input',
								'size' => '80',
						),
				),
		);
		
		$modifications['STARTSEITE_FAKULTAET'][] = array (
				'method' => 'add',
				'path' => 'sheets/allgemein/ROOT/el',
				'type' => 'field',
				'field_config' => array (
						'name' => 'title_en',
						'label' => 'Titel - englische Version (erscheint oberhalb der Banner-Grafiken)',
						'config' => array(
								'type' => 'input',
								'size' => '80',
						),
				),
		);
		
		$modifications['STARTSEITE_FAKULTAET'][] = array (
				'method' => 'add',
				'path' => 'sheets/allgemein/ROOT/el',
				'type' => 'field',
				'field_config' => array (
						'name' => 'carousel_images',
						'label' => 'Dateien für die Banner-Grafiken',
						'config' => array(
								'type' => 'group',
								'internal_type' => 'file',
								'allowed' => 'gif,jpg,jpeg,png',
								'show_thumbs' => true,
								'minitems' => 0,
								'maxitems' => 20,
								'size' => '4',
						),
				),
		);
		
		$modifications['STARTSEITE_FAKULTAET'][] = array (
				'method' => 'add',
				'path' => 'sheets/allgemein/ROOT/el',
				'type' => 'field',
				'field_config' => array (
						'name' => 'carousel_images_en',
						'label' => 'Dateien für die Banner-Grafiken (englisch)',
						'config' => array(
								'type' => 'group',
								'internal_type' => 'file',
								'allowed' => 'gif,jpg,jpeg,png',
								'show_thumbs' => true,
								'minitems' => 0,
								'maxitems' => 20,
								'size' => '4',
						),
				),
		);
		
		$modifications['STARTSEITE_FAKULTAET'][] = array (
				'method' => 'add',
				'path' => 'sheets/allgemein/ROOT/el',
				'type' => 'field',
				'field_config' => array (
						'name' => 'carousel_tooltips',
						'label' => 'Tooltips für die Banner-Grafiken (für jede Grafik eine TYPO3-Id oder URL in einer Zeile eintragen)',
						'config' => array(
								'type' => 'text',
								'cols' => '80',
								'rows' => '10',
						),
				),
		);
		
		$modifications['STARTSEITE_FAKULTAET'][] = array (
				'method' => 'add',
				'path' => 'sheets/allgemein/ROOT/el',
				'type' => 'field',
				'field_config' => array (
						'name' => 'carousel_links',
						'label' => 'Links für die Banner-Grafiken (für jede Grafik eine TYPO3-Id oder URL in einer Zeile eintragen)',
						'config' => array(
								'type' => 'text',
								'cols' => '80',
								'rows' => '10',
						),
				),
		);
		
		$modifications['STARTSEITE_FAKULTAET'][] = array (
				'method' => 'add',
				'path' => 'sheets/allgemein/ROOT/el',
				'type' => 'field',
				'field_config' => array (
						'name' => 'carousel_tooltips_en',
						'label' => 'Banner-Tooltips - englisch (für jede Grafik eine TYPO3-Id oder URL in einer Zeile eintragen)',
						'config' => array(
								'type' => 'text',
								'cols' => '80',
								'rows' => '10',
						),
				),
		);
		
		$modifications['STARTSEITE_FAKULTAET'][] = array (
				'method' => 'add',
				'path' => 'sheets/allgemein/ROOT/el',
				'type' => 'field',
				'field_config' => array (
						'name' => 'carousel_links_en',
						'label' => 'Banner-Links - englisch (für jede Grafik eine TYPO3-Id oder URL in einer Zeile eintragen)',
						'config' => array(
								'type' => 'text',
								'cols' => '80',
								'rows' => '10',
						),
				),
		);
		
		foreach ($tabConfig['STARTSEITE_FAKULTAET'] as $tabData) {
			$modifications['STARTSEITE_FAKULTAET'][] = array (
										'method' => 'add',
										'type' => 'sheet',
										'name' => $tabData['name'],
										'label' => $tabData['label'],
									);
			if ($BE_USER->user['admin']) {
/*
 * Grafik hinzufügen
*/
				$modifications['STARTSEITE_FAKULTAET'][] = array (
						'method' => 'add',
						'path' => 'sheets/' . $tabData['name'] . '/ROOT/el',
						'type' => 'field',
						'field_config' => array (
								'name' => 'grafik',
								'label' => 'Grafik für den Bereich "' . $tabData['label'] . '" auswählen',
								'config' => array(
										'type' => 'group',
										'internal_type' => 'file',
										'allowed' => 'gif,jpg,jpeg,png',
										'show_thumbs' => true,
										'minitems' => 0,
										'maxitems' => 1,
										'size' => 1,
								),
						),
				);
			}
				
			foreach ($tabData['links'] as $name => $titel) {
				
/*
 * Links hinzufügen
 */			
				$modifications['STARTSEITE_FAKULTAET'][] = array (
											'method' => 'add',
											'path' => 'sheets/' . $tabData['name'] . '/ROOT/el',
											'type' => 'field',
											'field_config' => array (
												'name' => $name,
												'label' => 'Link zur Seite "' . $titel . '" auswählen:',
												'config' => array(
													'type' => 'group',
													'internal_type' => 'db',
													'allowed' => 'pages',
													'minitems' => 0,
													'maxitems' => 1,
													'size' => 1,
												),
											),
									);

			}
		}
		
		$modifications['STARTSEITE_STUDIENGANG'][] = array (
				'method' => 'add',
				'path' => 'sheets/allgemein/ROOT/el',
				'type' => 'field',
				'field_config' => array (
						'name' => 'title',
						'label' => 'Titel',
						'config' => array(
								'type' => 'input',
								'size' => '80',
						),
				),
		);
		
		$modifications['STARTSEITE_STUDIENGANG'][] = array (
				'method' => 'add',
				'path' => 'sheets/allgemein/ROOT/el',
				'type' => 'field',
				'field_config' => array (
						'name' => 'title_en',
						'label' => 'Titel - englische Version',
						'config' => array(
								'type' => 'input',
								'size' => '80',
						),
				),
		);
		
		foreach ($tabConfig['STARTSEITE_STUDIENGANG'] as $tabData) {
			$modifications['STARTSEITE_STUDIENGANG'][] = array (
										'method' => 'add',
										'type' => 'sheet',
										'name' => $tabData['name'],
										'label' => $tabData['label'],
									);

			foreach ($tabData['links'] as $name => $titel) {
				if ($BE_USER->user['admin']) {
					/*
					 * Grafik hinzufügen
					*/
					$modifications['STARTSEITE_STUDIENGANG'][] = array (
							'method' => 'add',
							'path' => 'sheets/' . $tabData['name'] . '/ROOT/el',
							'type' => 'field',
							'field_config' => array (
									'name' => 'grafik',
									'label' => 'Grafik für den Bereich "' . $tabData['label'] . '" auswählen',
									'config' => array(
											'type' => 'group',
											'internal_type' => 'file',
											'allowed' => 'gif,jpg,jpeg,png',
											'show_thumbs' => true,
											'minitems' => 0,
											'maxitems' => 1,
											'size' => 1,
									),
							),
					);
				}
				
				
/*
 * Links hinzufügen
 */			
				$modifications['STARTSEITE_STUDIENGANG'][] = array (
											'method' => 'add',
											'path' => 'sheets/' . $tabData['name'] . '/ROOT/el',
											'type' => 'field',
											'field_config' => array (
												'name' => $name,
												'label' => 'Link zur Seite "' . $titel . '" auswählen:',
												'config' => array(
													'type' => 'group',
													'internal_type' => 'db',
													'allowed' => 'pages',
													'minitems' => 0,
													'maxitems' => 1,
													'size' => 1,
												),
											),
									);

			}
			if ($BE_USER->user['admin'] && isset($tabData['checks'])) {
				foreach ($tabData['checks'] as $name => $titel) {
					$modifications['STARTSEITE_STUDIENGANG'][] = array (
							'method' => 'add',
							'path' => 'sheets/' . $tabData['name'] . '/ROOT/el',
							'type' => 'field',
							'field_config' => array (
									'name' => $name,
									'label' => $titel,
									'config' => array(
											'type' => 'check',
									),
							),
					);
				}
			}
		}
		
		$modifications['STARTSEITE_STUDIENGANG'][] = array (
				'method' => 'add',
				'type' => 'sheet',
				'name' => $tabConfig['STUDIENGANG_DATEN_UND_FAKTEN']['name'],
				'label' => $tabConfig['STUDIENGANG_DATEN_UND_FAKTEN']['label'],
		);
		
		foreach ($tabConfig['STUDIENGANG_DATEN_UND_FAKTEN']['zeilen'] as $titel) {
			$modifications['STARTSEITE_STUDIENGANG'][] = array (
					'method' => 'add',
					'path' => 'sheets/' . $tabConfig['STUDIENGANG_DATEN_UND_FAKTEN']['name'] . '/ROOT/el',
					'type' => 'field',
					'field_config' => array (
							'name' => $titel,
							'label' => $titel . ':',
							'config' => array(
									'type' => 'input',
									'size' => '80',
							),
					),
			);
		}
		$modifications['STARTSEITE_STUDIENGANG'][] = array (
				'method' => 'add',
				'type' => 'sheet',
				'name' => $tabConfig['STUDIENGANG_DATEN_UND_FAKTEN_EN']['name'],
				'label' => $tabConfig['STUDIENGANG_DATEN_UND_FAKTEN_EN']['label'],
		);
		
		foreach ($tabConfig['STUDIENGANG_DATEN_UND_FAKTEN_EN']['zeilen'] as $titel) {
			$modifications['STARTSEITE_STUDIENGANG'][] = array (
					'method' => 'add',
					'path' => 'sheets/' . $tabConfig['STUDIENGANG_DATEN_UND_FAKTEN_EN']['name'] . '/ROOT/el',
					'type' => 'field',
					'field_config' => array (
							'name' => $titel,
							'label' => $titel . ':',
							'config' => array(
									'type' => 'input',
									'size' => '80',
							),
					),
			);
		}
		
		$modifications['FREIE_CONTAINER_EINSTIEG'][] = array (
				'method' => 'add',
				'path' => 'sheets/allgemein/ROOT/el',
				'type' => 'field',
				'field_config' => array (
						'name' => 'carousel_images',
						'label' => 'Dateien für die Banner-Grafiken',
						'config' => array(
								'type' => 'group',
								'internal_type' => 'file',
								'allowed' => 'gif,jpg,jpeg,png',
								'show_thumbs' => true,
								'minitems' => 0,
								'maxitems' => 20,
								'rows' => '10',
								'size' => 8,
						),
				),
		);
		
		$modifications['FREIE_CONTAINER_EINSTIEG'][] = array (
				'method' => 'add',
				'path' => 'sheets/allgemein/ROOT/el',
				'type' => 'field',
				'field_config' => array (
						'name' => 'carousel_tooltips',
						'label' => 'Tooltips für die Banner-Grafiken (für jede Grafik eine TYPO3-Id oder URL in einer Zeile eintragen)',
						'config' => array(
								'type' => 'text',
								'cols' => '80',
								'rows' => '10',
						),
				),
		);
		
		$modifications['FREIE_CONTAINER_EINSTIEG'][] = array (
				'method' => 'add',
				'path' => 'sheets/allgemein/ROOT/el',
				'type' => 'field',
				'field_config' => array (
						'name' => 'carousel_links',
						'label' => 'Links für die Banner-Grafiken (für jede Grafik eine TYPO3-Id oder URL in einer Zeile eintragen)',
						'config' => array(
								'type' => 'text',
								'cols' => '80',
								'rows' => '10',
						),
				),
		);
		
		for ($i=0;$i<$maxTabs;$i++) {
			$modifications['FREIE_CONTAINER_EINSTIEG'][] = array (
					'method' => 'add',
					'type' => 'sheet',
					'name' => 'container_' . $i,
					'label' => 'Container ' . ($i+1),
			);
			$modifications['FREIE_CONTAINER_EINSTIEG'][] = array (
					'method' => 'add',
					'path' => 'sheets/container_' . $i . '/ROOT/el',
					'type' => 'field',
					'field_config' => array (
							'name' => 'title_' . $i,
							'label' => 'Überschrift',
							'config' => array (
									'type' => 'input',
									'size' => '80',
							),
						),
					);
			$modifications['FREIE_CONTAINER_EINSTIEG'][] = array (
					'method' => 'add',
					'path' => 'sheets/container_' . $i . '/ROOT/el',
					'type' => 'field',
					'field_config' => array (
							'name' => 'grafik_' . $i,
							'label' => 'Grafik',
							'config' => array(
									'type' => 'group',
									'internal_type' => 'file',
									'allowed' => 'gif,jpg,jpeg,png',
									'show_thumbs' => true,
									'minitems' => 0,
									'maxitems' => 1,
									'size' => 1,
							),
						),
				);
			$modifications['FREIE_CONTAINER_EINSTIEG'][] = array (
					'method' => 'add',
					'path' => 'sheets/container_' . $i . '/ROOT/el',
					'type' => 'field',
					'field_config' => array (
							'name' => 'grafik_link_' . $i,
							'label' => 'Link für die Grafik auswählen:',
							'config' => array(
													'type' => 'group',
													'internal_type' => 'db',
													'allowed' => 'pages',
													'minitems' => 0,
													'maxitems' => 1,
													'size' => 1,
												),
						),
				);
			$modifications['FREIE_CONTAINER_EINSTIEG'][] = array (
					'method' => 'add',
					'path' => 'sheets/container_' . $i . '/ROOT/el',
					'type' => 'field',
					'field_config' => array (
							'label' => 'Unterpunkte',
							'name' => 'unterpunkte_' . $i,
							'config' => array (
									'type' => 'text',
									'cols' => '80',
									'rows' => '20',
							),
							'defaultExtras' => 'richtext[]:rte_transform[mode=css]',
					),
			);
		}
		
		for ($i=0;$i<$maxTabs;$i++) {
			$modifications['FREIE_CONTAINER_UNTERSEITE'][] = array (
					'method' => 'add',
					'type' => 'sheet',
					'name' => 'container_' . $i,
					'label' => 'Container ' . ($i+1),
			);
			$modifications['FREIE_CONTAINER_UNTERSEITE'][] = array (
					'method' => 'add',
					'path' => 'sheets/container_' . $i . '/ROOT/el',
					'type' => 'field',
					'field_config' => array (
							'name' => 'title_' . $i,
							'label' => 'Überschrift',
							'config' => array (
									'type' => 'input',
									'size' => '80',
							),
						),
					);
			$modifications['FREIE_CONTAINER_UNTERSEITE'][] = array (
					'method' => 'add',
					'path' => 'sheets/container_' . $i . '/ROOT/el',
					'type' => 'field',
					'field_config' => array (
							'name' => 'add_title_link_' . $i,
							'label' => 'Überschrift verlinken?',
							'config' => array (
									'type' => 'check',
							),
					),
			);			
			
			$modifications['FREIE_CONTAINER_UNTERSEITE'][] = array (
					'method' => 'add',
					'path' => 'sheets/container_' . $i . '/ROOT/el',
					'type' => 'field',
					'field_config' => array (
							'name' => 'grafik_' . $i,
							'label' => 'Grafik',
							'config' => array(
									'type' => 'group',
									'internal_type' => 'file',
									'allowed' => 'gif,jpg,jpeg,png',
									'show_thumbs' => true,
									'minitems' => 0,
									'maxitems' => 1,
									'size' => 1,
							),
						),
				);
			$modifications['FREIE_CONTAINER_UNTERSEITE'][] = array (
					'method' => 'add',
					'path' => 'sheets/container_' . $i . '/ROOT/el',
					'type' => 'field',
					'field_config' => array (
							'name' => 'grafik_link_' . $i,
							'label' => 'Link für die Grafik auswählen:',
							'config' => array(
													'type' => 'group',
													'internal_type' => 'db',
													'allowed' => 'pages',
													'minitems' => 0,
													'maxitems' => 1,
													'size' => 1,
												),
						),
				);
			$modifications['FREIE_CONTAINER_UNTERSEITE'][] = array (
					'method' => 'add',
					'path' => 'sheets/container_' . $i . '/ROOT/el',
					'type' => 'field',
					'field_config' => array (
							'label' => 'Unterpunkte',
							'name' => 'unterpunkte_' . $i,
							'config' => array (
									'type' => 'text',
									'cols' => '80',
									'rows' => '20',
							),
							'defaultExtras' => 'richtext[]:rte_transform[mode=css]',
					),
			);
		}
		
		for ($i=0;$i<$maxTabs;$i++) {
			$modifications['FREIE_CONTAINER_UNTERSEITE_TEXT'][] = array (
					'method' => 'add',
					'type' => 'sheet',
					'name' => 'container_' . $i,
					'label' => 'Container ' . ($i+1),
			);
			$modifications['FREIE_CONTAINER_UNTERSEITE_TEXT'][] = array (
					'method' => 'add',
					'path' => 'sheets/container_' . $i . '/ROOT/el',
					'type' => 'field',
					'field_config' => array (
							'name' => 'title_' . $i,
							'label' => 'Überschrift',
							'config' => array (
									'type' => 'input',
									'size' => '80',
							),
						),
					);
			$modifications['FREIE_CONTAINER_UNTERSEITE_TEXT'][] = array (
					'method' => 'add',
					'path' => 'sheets/container_' . $i . '/ROOT/el',
					'type' => 'field',
					'field_config' => array (
							'label' => 'Einleitungstext',
							'name' => 'einleitung_' . $i,
							'config' => array (
									'type' => 'text',
									'cols' => '80',
									'rows' => '3',
							),
					),
			);
			$modifications['FREIE_CONTAINER_UNTERSEITE_TEXT'][] = array (
					'method' => 'add',
					'path' => 'sheets/container_' . $i . '/ROOT/el',
					'type' => 'field',
					'field_config' => array (
							'name' => 'grafik_' . $i,
							'label' => 'Grafik',
							'config' => array(
									'type' => 'group',
									'internal_type' => 'file',
									'allowed' => 'gif,jpg,jpeg,png',
									'show_thumbs' => true,
									'minitems' => 0,
									'maxitems' => 1,
									'size' => 1,
							),
						),
				);
			$modifications['FREIE_CONTAINER_UNTERSEITE_TEXT'][] = array (
					'method' => 'add',
					'path' => 'sheets/container_' . $i . '/ROOT/el',
					'type' => 'field',
					'field_config' => array (
							'name' => 'grafik_link_' . $i,
							'label' => 'Link für die Grafik auswählen:',
							'config' => array(
													'type' => 'group',
													'internal_type' => 'db',
													'allowed' => 'pages',
													'minitems' => 0,
													'maxitems' => 1,
													'size' => 1,
												),
						),
				);
			$modifications['FREIE_CONTAINER_UNTERSEITE_TEXT'][] = array (
					'method' => 'add',
					'path' => 'sheets/container_' . $i . '/ROOT/el',
					'type' => 'field',
					'field_config' => array (
							'label' => 'Unterpunkte',
							'name' => 'unterpunkte_' . $i,
							'config' => array (
									'type' => 'text',
									'cols' => '80',
									'rows' => '20',
							),
							'defaultExtras' => 'richtext[]:rte_transform[mode=css]',
					),
			);
		}
		
/*
			'STARTSEITE_JUBILAEUM' => 'Startseite Jubiläum - (Maximal ' . $maxTabs . ')',
			'UNTERSEITE_JUBILAEUM' => 'Unterseite Jubiläum - (Maximal ' . $maxTabs . ')',

 */		
		
		
		
		$modifications['STARTSEITE_JUBILAEUM'][] = array (
				'method' => 'add',
				'path' => 'sheets/allgemein/ROOT/el',
				'type' => 'field',
				'field_config' => array (
						'name' => 'banner',
						'label' => 'Banner-Grafik',
						'config' => array(
								'type' => 'group',
								'internal_type' => 'file',
								'allowed' => 'gif,jpg,jpeg,png',
								'show_thumbs' => true,
								'minitems' => 0,
								'maxitems' => 1,
								'size' => 1,
						),
				),								
		);
		
		$modifications['STARTSEITE_JUBILAEUM'][] = array (
				'method' => 'add',
				'path' => 'sheets/allgemein/ROOT/el',
				'type' => 'field',
				'field_config' => array (
						'label' => 'Einleitung links',
						'name' => 'einleitung_links',
						'config' => array (
								'type' => 'text',
								'cols' => '80',
								'rows' => '20',
						),
						'defaultExtras' => 'richtext[]:rte_transform[mode=css]',
				),			
		);
		
		$modifications['STARTSEITE_JUBILAEUM'][] = array (
				'method' => 'add',
				'path' => 'sheets/allgemein/ROOT/el',
				'type' => 'field',
				'field_config' => array (
						'label' => 'Einleitung rechts',
						'name' => 'einleitung_rechts',
						'config' => array (
								'type' => 'text',
								'cols' => '80',
								'rows' => '20',
						),
						'defaultExtras' => 'richtext[]:rte_transform[mode=css]',
				),			
		);
		
		for ($i=0;$i<$maxTabs;$i++) {
			$modifications['STARTSEITE_JUBILAEUM'][] = array (
					'method' => 'add',
					'type' => 'sheet',
					'name' => 'container_' . $i,
					'label' => 'Container ' . ($i+1),
			);
			$modifications['STARTSEITE_JUBILAEUM'][] = array (
					'method' => 'add',
					'path' => 'sheets/container_' . $i . '/ROOT/el',
					'type' => 'field',
					'field_config' => array (
							'name' => 'title_' . $i,
							'label' => 'Überschrift',
							'config' => array (
									'type' => 'input',
									'size' => '80',
							),
					),
			);
			$modifications['STARTSEITE_JUBILAEUM'][] = array (
					'method' => 'add',
					'path' => 'sheets/container_' . $i . '/ROOT/el',
					'type' => 'field',
					'field_config' => array (
							'name' => 'grafik_link_' . $i,
							'label' => 'Link für Titel/Grafik auswählen:',
							'config' => array(
									'type' => 'group',
									'internal_type' => 'db',
									'allowed' => 'pages',
									'minitems' => 0,
									'maxitems' => 1,
									'size' => 1,
							),
					),
			);
			$modifications['STARTSEITE_JUBILAEUM'][] = array (
					'method' => 'add',
					'path' => 'sheets/container_' . $i . '/ROOT/el',
					'type' => 'field',
					'field_config' => array (
							'name' => 'grafik_' . $i,
							'label' => 'Grafik',
							'config' => array(
									'type' => 'group',
									'internal_type' => 'file',
									'allowed' => 'gif,jpg,jpeg,png',
									'show_thumbs' => true,
									'minitems' => 0,
									'maxitems' => 1,
									'size' => 1,
							),
					),
			);
			$modifications['STARTSEITE_JUBILAEUM'][] = array (
					'method' => 'add',
					'path' => 'sheets/container_' . $i . '/ROOT/el',
					'type' => 'field',
					'field_config' => array (
							'label' => 'Text',
							'name' => 'unterpunkte_' . $i,
							'config' => array (
									'type' => 'text',
									'cols' => '80',
									'rows' => '20',
							),
							'defaultExtras' => 'richtext[]:rte_transform[mode=css]',
					),
			);
		}
		
		
/* Unterseiten für die Jubiläumshomepage */
				
		for ($i=0;$i<$maxTabs;$i++) {
			$modifications['UNTERSEITE_JUBILAEUM'][] = array (
					'method' => 'add',
					'type' => 'sheet',
					'name' => 'container_' . $i,
					'label' => 'Container ' . ($i+1),
			);
			$modifications['UNTERSEITE_JUBILAEUM'][] = array (
					'method' => 'add',
					'path' => 'sheets/container_' . $i . '/ROOT/el',
					'type' => 'field',
					'field_config' => array (
							'name' => 'title_' . $i,
							'label' => 'Überschrift',
							'config' => array (
									'type' => 'input',
									'size' => '80',
							),
					),
			);
			$modifications['UNTERSEITE_JUBILAEUM'][] = array (
					'method' => 'add',
					'path' => 'sheets/container_' . $i . '/ROOT/el',
					'type' => 'field',
					'field_config' => array (
							'name' => 'grafik_' . $i,
							'label' => 'Grafik',
							'config' => array(
									'type' => 'group',
									'internal_type' => 'file',
									'allowed' => 'gif,jpg,jpeg,png',
									'show_thumbs' => true,
									'minitems' => 0,
									'maxitems' => 1,
									'size' => 1,
							),
					),
			);
			$modifications['UNTERSEITE_JUBILAEUM'][] = array (
					'method' => 'add',
					'path' => 'sheets/container_' . $i . '/ROOT/el',
					'type' => 'field',
					'field_config' => array (
							'name' => 'grafik_link_' . $i,
							'label' => 'Link für die Grafik auswählen:',
							'config' => array(
									'type' => 'group',
									'internal_type' => 'db',
									'allowed' => 'pages',
									'minitems' => 0,
									'maxitems' => 1,
									'size' => 1,
							),
					),
			);
			$modifications['UNTERSEITE_JUBILAEUM'][] = array (
					'method' => 'add',
					'path' => 'sheets/container_' . $i . '/ROOT/el',
					'type' => 'field',
					'field_config' => array (
							'label' => 'Text',
							'name' => 'unterpunkte_' . $i,
							'config' => array (
									'type' => 'text',
									'cols' => '80',
									'rows' => '20',
							),
							'defaultExtras' => 'richtext[]:rte_transform[mode=css]',
					),
			);
		}
		
		
		foreach ($modifications as $bereich=>$modificationArray) {
			foreach ($modificationArray as $modificationData) {
				$modificationData['condition'] =	$conditions[$bereich];
				$currentDCA[0]['modifications'][] = $modificationData;
			}
		}
		
/*		
		$currentDCA[0]['modifications'][] =  array (
				'method' => 'add',
				'path' => 'sheets/allgemein/ROOT/el',
				'type' => 'field',
				'condition' => array (
					'source' => 'cce',
					'if' => 'isEqual',
					'isXML' => TRUE,
					'cefield' => 'pi_flexform',
					'path' => 'data/allgemein/lDEF/auswahl/vDEF',
					'compareTo' => 'SONSTIGES',
				),
				'field_config' => array (
						'name' => 'anz_tabs',
						'label' => 'Anzahl der Container',
						'config' => array(
								'type' => 'input',
						),
				),
		);
		
		$currentDCA[0]['modifications'][] = 
		array (
				'method' => 'add',
				'type' => 'sheets',
				'source' => 'field',					
				'source_config' => array (
						'path' => 'allgemein/lDEF',	
						'xml_field' => 'anz_tabs',	
						'db_field' => 'pi_flexform',
				),
				'condition' => array (
					'source' => 'cce',
					'if' => 'isEqual',
					'isXML' => TRUE,
					'cefield' => 'pi_flexform',
					'path' => 'data/allgemein/lDEF/auswahl/vDEF',
					'compareTo' => 'SONSTIGES',
				),
				// configure the sheets
				'sheet_config' => array (
						'label' => 'Container ###SINDEX###',	// ###SINDEX### is replaced with the index of the cycle
						'name' => 'container',					// the name is appended by the index of the cycle
						// place some fields on each sheet, replacing and tailing is like for the sheet itself
						'fields' => array (
								array (
										'label' => 'Überschrift',
										'name' => 'title',
										'config' => array (
												'type' => 'input',
												'size' => '80',
										)
								),
								array (
										'label' => 'Grafik',
										'name' => 'grafik',
										'config' => array(
												'type' => 'group',
												'internal_type' => 'file',
												'allowed' => 'gif,jpg,jpeg,png',
												'show_thumbs' => true,
												'minitems' => 0,
												'maxitems' => 1,
										),
								),
								array (
										'label' => 'Unterpunkte',
										'name' => 'unterpunkte',
										'config' => array (
												'type' => 'text',
												'cols' => '80',
												'rows' => '20',
										),
										'defaultExtras' => 'richtext[]:rte_transform[mode=css]',
								),
						)
				)
		);	
*/		
	}
}
?>