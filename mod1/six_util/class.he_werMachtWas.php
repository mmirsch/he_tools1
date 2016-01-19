<?php
require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_db.php');
require_once(t3lib_extMgm::extPath('six2t3').'lib/class.siximport.php');

class he_werMachtWas {

	function werMachtWas() {
		$abteilungen = array();
		$sqlAbteilungen = 'SELECT DISTINCT id,title FROM sixcms_article 
										 WHERE area_id = 510';
		$abfrageAbteilungen = he_tools_db::six_sql_query($sqlAbteilungen);
		while ($datenAbteilungen = he_tools_db::six_sql_fetch_assoc($abfrageAbteilungen)) {
			$abteilungen[$datenAbteilungen[id]] = $datenAbteilungen[title];
		}
		$werMachtWas = array();
		$sqlWerMachtWas = 'SELECT id,title FROM sixcms_article
										 	WHERE area_id = 511 AND published=1
										 	ORDER BY title';
		$abfrageWerMachtWas = he_tools_db::six_sql_query($sqlWerMachtWas);
		while ($datenWerMachtWas = he_tools_db::six_sql_fetch_assoc($abfrageWerMachtWas)) {
			$werMachtWas[$datenWerMachtWas[id]] = $datenWerMachtWas[title];
		}
		foreach ($werMachtWas as $id=>$title) {
			$werMachtWasDaten[$id][titel] =  $title;
			$sqlWerMachtWas = 'SELECT fieldname,value FROM sixcms_article_data
											 	WHERE article_id = ' . $id;
			$abfrageWerMachtWas = he_tools_db::six_sql_query($sqlWerMachtWas);
			while ($datenWerMachtWas = he_tools_db::six_sql_fetch_assoc($abfrageWerMachtWas)) {
				if (!empty($datenWerMachtWas[value])) {
					$werMachtWasDaten[$id][$datenWerMachtWas[fieldname]] = $datenWerMachtWas[value];
				}
			}
			$sqlWerMachtWasRelAbteilung = 'SELECT rel_id FROM sixcms_article_article
											 						 WHERE fieldname="rel_abteilung" AND article_id = ' . $id;
			$abfrageWerMachtWasRelAbteilung = he_tools_db::six_sql_query($sqlWerMachtWasRelAbteilung);
			while ($datenWerMachtWasRelAbteilung = he_tools_db::six_sql_fetch_assoc($abfrageWerMachtWasRelAbteilung)) {
				$werMachtWasDaten[$id][abteilungen][] = $datenWerMachtWasRelAbteilung[rel_id];
			}
			$sqlWerMachtWasRelPersonen = 'SELECT username FROM sixcms_article
																		 INNER JOIN sixcms_article_article ON sixcms_article.id=sixcms_article_article.rel_id
											 						   WHERE sixcms_article_article.fieldname="rel_personen" AND sixcms_article_article.article_id = ' . $id;
			$abfrageWerMachtWasRelPersonen = he_tools_db::six_sql_query($sqlWerMachtWasRelPersonen);
			while ($datenWerMachtWasRelPersonen = he_tools_db::six_sql_fetch_assoc($abfrageWerMachtWasRelPersonen)) {
				$werMachtWasDaten[$id][personen][] = $datenWerMachtWasRelPersonen[username];
			}
		}
		return '<pre>' . print_r($werMachtWasDaten,true) . '</pre>';
	}

	function importWerMachtWas() {
/* 
		$abteilungen = array();
		$sqlAbteilungen = 'SELECT DISTINCT id,title FROM sixcms_article 
										 WHERE area_id = 510';
		$abfrageAbteilungen = db::six_sql_query($sqlAbteilungen);
		while ($datenAbteilungen = db::six_sql_fetch_assoc($abfrageAbteilungen)) {
			$abteilungen[$datenAbteilungen[id]] = $datenAbteilungen[title];
		}
		db::six_disconnect();
		foreach($abteilungen as $id=>$abteilung) {
			$daten[pid] = 91367;
			$daten[title] = $abteilung;
			$abfrage = $GLOBALS['TYPO3_DB']->exec_INSERTquery ('tx_hetools_wmw_abteilungen',
																											 $daten);
		}
		return "OK";
*/		
		$werMachtWas = array();
		$sqlWerMachtWas = 'SELECT id,title FROM sixcms_article
										 	WHERE area_id = 511 AND published=1
										 	ORDER BY title';
		$abfrageWerMachtWas = db::six_sql_query($sqlWerMachtWas);
		while ($datenWerMachtWas = db::six_sql_fetch_assoc($abfrageWerMachtWas)) {
			$werMachtWas[$datenWerMachtWas[id]] = $datenWerMachtWas[title];
		}
		foreach ($werMachtWas as $id=>$title) {
			$werMachtWasDaten[$id][title] =  $title;
			$detailDaten = array();
			$sqlWerMachtWas = 'SELECT fieldname,value FROM sixcms_article_data
											 	WHERE article_id = ' . $id;
			$abfrageWerMachtWas = db::six_sql_query($sqlWerMachtWas);
			while ($datenWerMachtWas = db::six_sql_fetch_assoc($abfrageWerMachtWas)) {
				if (!empty($datenWerMachtWas[value])) {
					switch (trim($datenWerMachtWas[fieldname])) {
						case 'beschreibung':
							$detailDaten[0] = '<p>' . $datenWerMachtWas[value] . '</p>';
							break;
						case 'linkadresse':
							$adresse = str_replace('http://www.hs-esslingen.de/de/','',$datenWerMachtWas[value]);
							$adresse = str_replace('http://www.hs-esslingen.de/','',$adresse);
							$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery ('typo3_id',
																										'tx_six2t3_pages',
																										'deleted=0 AND hidden=0 AND id=' . $adresse);
							if ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
								$link = '<a class="internalLink" href="https://www.hs-esslingen.de/?id=' . 
												$daten[typo3_id] . '">';
							} else {
								$link = '<a href="' . $datenWerMachtWas[value] . '">';
							}
							$detailDaten[1] = $link;
							$detailDaten[3] = '</a>';
							break;		
						case 'linkbezeichnung':
							$detailDaten[2] = $datenWerMachtWas[value];
							break;
					}
				}
				
				$details = $detailDaten[0] . $detailDaten[1] . $detailDaten[2] . $detailDaten[3];
				$werMachtWasDaten[$id][beschreibung] = $details;
			}
			
			$abteilungen = array();
			$sqlWerMachtWasRelAbteilung = 'SELECT title FROM sixcms_article
																		 INNER JOIN sixcms_article_article ON sixcms_article.id=sixcms_article_article.rel_id
																		 WHERE sixcms_article_article.fieldname="rel_abteilung" AND sixcms_article_article.article_id = ' . $id;
			$abfrageWerMachtWasRelAbteilung = db::six_sql_query($sqlWerMachtWasRelAbteilung);
			while ($datenWerMachtWasRelAbteilung = db::six_sql_fetch_assoc($abfrageWerMachtWasRelAbteilung)) {
				$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery ('uid',
																							'tx_hetools_wmw_abteilungen',
																							'title="' . $datenWerMachtWasRelAbteilung[title] . '"');
				
				if ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
					$abteilungen[] = $daten['uid'];
				}
			}
			$werMachtWasDaten[$id][abteilungen] = implode(',',$abteilungen);
			$personen = array();
			$sqlWerMachtWasRelPersonen = 'SELECT username FROM sixcms_article
																		 INNER JOIN sixcms_article_article ON sixcms_article.id=sixcms_article_article.rel_id
											 						   WHERE sixcms_article_article.fieldname="rel_personen" AND sixcms_article_article.article_id = ' . $id;
			$abfrageWerMachtWasRelPersonen = db::six_sql_query($sqlWerMachtWasRelPersonen);
			while ($datenWerMachtWasRelPersonen = db::six_sql_fetch_assoc($abfrageWerMachtWasRelPersonen)) {
				$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery ('uid',
																							'fe_users',
																							'deleted=0 AND disable=0 AND username="' . $datenWerMachtWasRelPersonen[username] . '"');
				
				if ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
					$personen[] = $daten[uid];
				} else {
//					$personen[] = $datenWerMachtWasRelPersonen[username];
				}
			}
			$werMachtWasDaten[$id][personen] = implode(',',$personen);
		}
		db::six_disconnect();
		foreach($werMachtWasDaten as $id=>$daten) {
			$daten[pid] = 91367;
			$abfrage = $GLOBALS['TYPO3_DB']->exec_INSERTquery ('tx_hetools_wer_macht_was',
																											 $daten);
		}
		return 'OK';		
		return '<pre>' . print_r($werMachtWasDaten,true) . '</pre>';
	}
	
}
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_tools/mod1/class.he_backend_util.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_tools/mod1/class.he_backend_util.php']);
}
?>