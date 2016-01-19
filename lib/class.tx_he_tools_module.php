<?php
require_once(t3lib_extMgm::extPath('he_tools') . 'lib/class.tx_he_tools_db.php');
require_once(t3lib_extMgm::extPath('he_tools') . 'lib/sixcms/class.curl.php');
require_once(t3lib_extMgm::extPath('he_tools') . 'lib/sixcms/class.myIconv.php');
require_once(t3lib_extMgm::extPath('he_tools') . 'lib/sixcms/class.MyHandler.php');
require_once(t3lib_extMgm::extPath('he_tools') . 'lib/sixcms/class.HTMLSax3.php');
require_once(t3lib_extMgm::extPath('he_tools') . 'lib/class.tx_he_tools_util.php');
require_once(t3lib_extMgm::extPath('he_tools') . 'lib/class.tx_he_tools_lsf.php');
// 


class tx_he_tools_module {
	public function modulUebersicht($conf, $piObj, $studiengang, $schwerpunkt, $version) {		
		$get = t3lib_div::_GET();
		if (isset($get[sixId])) {
			$sixID = $get[sixId];
		} else {
			$where = 'deleted=0 AND title="' . $studiengang . '"';
			if (!empty($schwerpunkt)) {
				$where .= ' AND schwerpunkt="' . $schwerpunkt . '"';
			}
			$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('six_id','tx_hetools_module_studiengaenge',$where);
			if ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
				$sixID = $daten['six_id'];
			}
		}
		
		if (!empty($sixID)) {
			$out = $this->renderSix($sixID,$piObj);
		} else {
			$out = '<h3>Es wurde keine SixCMS-Seite für den Studiengang "' . $studiengang . '" ';
			if (!empty($schwerpunkt)) {
				$out .= 'und den Schwerpunkt "' . $schwerpunkt . '" ';
			}
			$out .= 'gefunden!<h3>';
		}
		return $out;
	}
	
	public function modulHandbuch($conf, $piObj, $studiengang, $schwerpunkt, $version) {		
		$where = 'deleted=0 AND title="' . $studiengang . '"';
		$where .= ' AND schwerpunkt="' . $schwerpunkt . '"';
		$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('six_id_handbuch','tx_hetools_module_studiengaenge',$where);
		if ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
			$sixID = $daten['six_id_handbuch'];
		}
		
		if (!empty($sixID)) {
			$out = $this->renderSix($sixID,$piObj);
		} else {
			$out = '<h3>Es wurde keine SixCMS-Seite für den Studiengang "' . $studiengang . '" ';
			if (!empty($schwerpunkt)) {
				$out .= 'und den Schwerpunkt "' . $schwerpunkt . '" ';
			}
			$out .= 'gefunden!</h3>';
			$out .= 'where: ' . $where;
		}
		return $out;
	}
	
	public function gibStudiengaenge(&$config) {		
		$where = 'deleted=0 AND hidden=0';
		$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,title,fakultaet','tx_hetools_module_studiengaenge',$where,'','title');
		$optionList = array();
		while ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
			$text = $daten['title'] . ' (' . $daten['fakultaet'] . ')';
			$optionList[] = array(0=>$text, 1=>$daten['uid']);
		}
		$config['items'] = $optionList;
		return $config;
	}
	
	public function gibVertiefungen(&$config) {		
		$where = 'deleted=0 AND hidden=0';
		$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,vertiefung,version,kuerzel','tx_hetools_module_vertiefungen',$where,'','vertiefung');
		while ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
			$text = $daten['vertiefung'] . ' (' . $daten['kuerzel'] . ') - Version: ' . $daten['version'];
			$optionList[] = array(0=>$text, 1=>$daten['uid']);
		}
		$config['items'] = $optionList;
		return $config;
	}
	
	public function modulUebersicht_lsfNeu(&$piObj, $studiengang, $vertiefung, $version, $darstellungsArt='', $maxCol='', $linksDeaktivieren=FALSE) {	
		$get = t3lib_div::_GET();#
		if (isset($get['modulhandbuch'])) {
			$module = new tx_he_tools_lsf($piObj->cObj);
			$args = unserialize(base64_decode($get['modulhandbuch']));
			return $module->gibModulHandbuch($args);
		} else if (isset($get['modulpdf'])) {
			$module = new tx_he_tools_lsf($piObj->cObj);
			$args = unserialize(base64_decode($get['modulpdf']));
			return $module->gibModulPdf($args);
		}
		$whereStudiengang = 'deleted=0 AND uid=' . $studiengang;
		$abfrageStudiengang = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_hetools_module_studiengaenge',$whereStudiengang);
		if ($datenStudiengang = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageStudiengang)) {
			$studiengangBezeichnung = $datenStudiengang['title'];
			$studiengangLsf = $datenStudiengang['lsf_stdg'];
			$abschlussLsf = $datenStudiengang['lsf_abs'];
			$abschluss = $datenStudiengang['abschluss'];
			$semVertiefung = $datenStudiengang['sem_schwp'];
			$kuerzelFakultaet = $datenStudiengang['fakultaet'];
			$fakultaeten = tx_he_tools_util::gibfakultaetenListe();
			$fakultaetsBezeichung = $fakultaeten[$kuerzelFakultaet];
			$module = new tx_he_tools_lsf($piObj->cObj);
			$vertiefungLsf = '';

			if (!empty($vertiefung)) {
				$whereVertiefung = 'deleted=0 AND uid=' . $vertiefung;
				$abfrageVertiefung = $GLOBALS['TYPO3_DB']->exec_SELECTquery('vertiefung,kuerzel','tx_hetools_module_vertiefungen',$whereVertiefung);
				if ($datenVertiefung = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageVertiefung)) {
					$studiengangBezeichnung .= ' - ' . $datenVertiefung['vertiefung'];
					$vertiefungLsf = $datenVertiefung['kuerzel'];
				}
			}
			if ($darstellungsArt=='TABELLE') {
				return $module->erzeugeModulTabelle($studiengangLsf,$abschlussLsf,$vertiefungLsf,$abschluss,
																						$version,$semVertiefung,$studiengangBezeichnung,$fakultaetsBezeichung,$kuerzelFakultaet,$maxCol,$linksDeaktivieren);
			} else {
				return $module->erzeugeModulListe($studiengangLsf,$abschlussLsf,$vertiefungLsf,$abschluss,$version,$semVertiefung,$studiengangBezeichnung,$fakultaetsBezeichung,$kuerzelFakultaet,$linksDeaktivieren);
				
			}
		} else {
			$out = '<h3>Es wurde kein Eintrag für den Studiengang "' . $studiengang . '" ';
			if (!empty($vertiefung)) {
				$out .= 'und die Vertiefung "' . $vertiefung . '" ';
			}
			$out .= 'gefunden!</h3>';
			$out .= 'where:' . $where;
			return $out;
		}
	}
	
	public function modulUebersicht_lsf_vertiefungen(&$piObj, $studiengang, $vertiefung, $version, $darstellungsArt='', $maxCol='') {	
		$get = t3lib_div::_GET();#
		if (isset($get['modulhandbuch'])) {
			$module = new tx_he_tools_lsf($piObj->cObj);
			$args = unserialize(base64_decode($get['modulhandbuch']));
			return $module->gibModulHandbuch($args);
		} else if (isset($get['modulpdf'])) {
			$module = new tx_he_tools_lsf($piObj->cObj);
			$args = unserialize(base64_decode($get['modulpdf']));
			return $module->gibModulPdf($args);
		}
		$whereStudiengang = 'deleted=0 AND uid=' . $studiengang;
		$abfrageStudiengang = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_hetools_module_studiengaenge',$whereStudiengang);
		if ($datenStudiengang = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageStudiengang)) {
			$studiengangBezeichnung = $datenStudiengang['title'];
			$studiengangLsf = $datenStudiengang['lsf_stdg'];
			$abschlussLsf = $datenStudiengang['lsf_abs'];
			$abschluss = $datenStudiengang['abschluss'];
			$semVertiefung = $datenStudiengang['sem_schwp'];
			$kuerzelFakultaet = $datenStudiengang['fakultaet'];
			$fakultaeten = tx_he_tools_util::gibfakultaetenListe();
			$fakultaetsBezeichung = $fakultaeten[$kuerzelFakultaet];
			$module = new tx_he_tools_lsf($piObj->cObj);
			$vertiefungLsf = '';
			if (!empty($vertiefung)) {
				$whereVertiefung = 'deleted=0 AND uid=' . $vertiefung;
				$abfrageVertiefung = $GLOBALS['TYPO3_DB']->exec_SELECTquery('vertiefung,kuerzel','tx_hetools_module_vertiefungen',$whereVertiefung);
				if ($datenVertiefung = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageVertiefung)) {
					$studiengangBezeichnung .= ' - ' . $datenVertiefung['vertiefung'];
					$vertiefungLsf = $datenVertiefung['kuerzel'];
				}
			}
			if ($darstellungsArt=='TABELLE') {
				return $module->erzeugeModulTabelleMitVertiefungen($studiengangLsf,$abschlussLsf,$vertiefungLsf,$abschluss,
																						$version,$semVertiefung,$studiengangBezeichnung,$fakultaetsBezeichung,$kuerzelFakultaet,$maxCol);
			} else {
				return $module->erzeugeModulListe($studiengangLsf,$abschlussLsf,$vertiefungLsf,$abschluss,$version,$semVertiefung,$studiengangBezeichnung,$fakultaetsBezeichung,$kuerzelFakultaet);
				
			}
		} else {
			$out = '<h3>Es wurde kein Eintrag für den Studiengang "' . $studiengang . '" ';
			if (!empty($vertiefung)) {
				$out .= 'und die Vertiefung "' . $vertiefung . '" ';
			}
			$out .= 'gefunden!</h3>';
			$out .= 'where:' . $where;
			return $out;
		}
	}
	
	public function modulUebersicht_lsf(&$piObj, $studiengang, $schwerpunkt, $version) {		
		$where = 'deleted=0 AND title="' . $studiengang . '"';
		if (!empty($schwerpunkt)) {
//			$where .= ' AND schwerpunkt="' . $schwerpunkt . '"';
			$studiengangBezeichnung = $studiengang . ' - ' . $schwerpunkt;
		} else {
			$studiengangBezeichnung = $studiengang;
		}
		$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,lsf_stdg,lsf_abs,fakultaet,abschluss,sem_schwp','tx_hetools_module_studiengaenge',$where);
		if ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
			$studiengang = $daten['lsf_stdg'];
			$abschlussLsf = $daten['lsf_abs'];
			$abschluss = $daten['abschluss'];
			$semVertiefung = $daten['sem_schwp'];
			$kuerzelFakultaet = $daten['fakultaet'];
			$fakultaeten = tx_he_tools_util::gibfakultaetenListe();
			$fakultaetsBezeichung = $fakultaeten[$kuerzelFakultaet];
			$module = new tx_he_tools_lsf($piObj->cObj);
			$vertiefungLsf = '';
			$whereVertiefung = 'vertiefung="' . $schwerpunkt . '" AND modstud_id=' . $daten['uid'];
			$abfrageVertiefung = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_hetools_module_vertiefungen',$whereVertiefung);
			while ($datenVertiefung = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageVertiefung)) {
				if (!empty($datenVertiefung['version'])) {
					if ($datenVertiefung['version']==$version) {
						$vertiefungLsf = $datenVertiefung['kuerzel'];
					}
				} else {
					$vertiefungLsf = $datenVertiefung['kuerzel'];
				}
			}
			return $module->erzeugeModulListe($studiengang,$abschlussLsf,$vertiefungLsf,$abschluss,$version,$semVertiefung,$studiengangBezeichnung,$fakultaetsBezeichung,$kuerzelFakultaet);
		} else {
			$out = '<h3>Es wurde kein Eintrag für den Studiengang "' . $studiengang . '" ';
			if (!empty($schwerpunkt)) {
				$out .= 'und den Schwerpunkt "' . $schwerpunkt . '" ';
			}
			$out .= 'gefunden!</h3>';
			$out .= 'where:' . $where;
			return $out;
		}
	}
	
	public function modulHandbuch_lsf(&$piObj, $studiengang, $schwerpunkt, $version) {		
		$where = 'deleted=0 AND title="' . $studiengang . '"';
		if (!empty($schwerpunkt)) {
			$where .= ' AND schwerpunkt="' . $schwerpunkt . '"';
			$studiengangBezeichnung = $studiengang . ' - ' . $schwerpunkt;
		} else {
			$studiengangBezeichnung = $studiengang;
		}
		$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,lsf_stdg,lsf_abs,fakultaet,abschluss,sem_schwp','tx_hetools_module_studiengaenge',$where);
		if ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
			$studiengang = $daten['lsf_stdg'];
			$abschlussLsf = $daten['lsf_abs'];
			$abschluss = $daten['abschluss'];
			$semVertiefung = $daten['sem_schwp'];
			$kuerzelFakultaet = $daten['fakultaet'];
			$fakultaeten = tx_he_tools_util::gibfakultaetenListe();
			$fakultaetsBezeichung = $fakultaeten[$kuerzelFakultaet];
			$module = new tx_he_tools_lsf($piObj->cObj);
			$vertiefungLsf = '';
			$whereVertiefung = 'modstud_id=' . $daten['uid'];
			$abfrageVertiefung = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_hetools_module_vertiefungen',$whereVertiefung);
			while ($datenVertiefung = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageVertiefung)) {
				if (!empty($datenVertiefung['version'])) {
					if ($datenVertiefung['version']==$poVersionSelected) {
						$vertiefungLsf = $datenVertiefung['kuerzel'];
					}
				} else {
					$vertiefungLsf = $datenVertiefung['kuerzel'];
				}
			}
			
			return $module->erzeugeModulHandbuch($studiengang,$abschlussLsf,$vertiefungLsf,$abschluss,$version,$semVertiefung,$studiengangBezeichnung,$fakultaetsBezeichung);
		} else {
			$out = '<h3>Es wurde kein Eintrag für den Studiengang "' . $studiengang . '" ';
			if (!empty($schwerpunkt)) {
				$out .= 'und den Schwerpunkt "' . $schwerpunkt . '" ';
			}
			$out .= 'gefunden!</h3>';
			$out .= 'where:' . $where;
			return $out;
		}
	}
	
	function gibStudiengangModuleSix($studiengang, $schwerpunkt) {
		he_tools_db::sixCmsConnect();
		$abfrageStudiengang = 'SELECT id FROM sixcms_article
													 WHERE published=1 AND area_id=35 AND
																 title LIKE "' . $studiengang . '"';
		$resStudiengang = he_tools_db::six_sql_query($abfrageStudiengang);
		if ($rowStudiengang = he_tools_db::six_sql_fetch_assoc($resStudiengang)) {			
			$studiengangId = $rowStudiengang['id'];
			$abfrageSemester = 'SELECT sixcms_article_article.article_id, sixcms_article_data.value as semester  
													FROM sixcms_article_article
													INNER JOIN sixcms_article_data ON sixcms_article_data.article_id = sixcms_article_article.article_id
													WHERE  sixcms_article_article.rel_id=' . $studiengangId . ' AND sixcms_article_data.fieldname = "semester_nr"';			
			if (!empty($schwerpunkt)) {
				$abfrageSchwerpunkt = 'SELECT id FROM sixcms_article
															 WHERE published=1 AND area_id=386 AND
																		 title LIKE "' . $schwerpunkt . '"';
				$resSchwerpunkt = he_tools_db::six_sql_query($abfrageSchwerpunkt);
				if ($rowSchwerpunkt = he_tools_db::six_sql_fetch_assoc($resSchwerpunkt)) {			
					$schwerpunktId = $rowSchwerpunkt['id'];
					$abfrageSemester .= ' AND sixcms_article_article.article_id IN 
																(SELECT article_id FROM sixcms_article_article 
																 WHERE rel_id=22169)';
													
					
				}
			}
			$abfrageSemester .= ' ORDER BY sixcms_article_data.value DESC';
			$resSemester = he_tools_db::six_sql_query($abfrageSemester);
			$semesterListe = array();
			while ($rowSemester = he_tools_db::six_sql_fetch_assoc($resSemester)) {
				$semesterListe[$rowSemester['semester']] = $rowSemester['article_id'];
			}		
			foreach ($semesterListe as $semester=>$modulUebersichtId) {
				$module[$semester] = $this->gibModule($modulUebersichtId);
			}
			return $module;
		}
	}
	
	function gibModule($modulUebersichtId) {
		he_tools_db::sixCmsConnect();
		$abfrageModule = 'SELECT rel_id FROM sixcms_article_article
													 WHERE article_id=' . $modulUebersichtId . 
													 ' AND fieldname="rel_module"
													 ORDER BY sort';
		$resModule = he_tools_db::six_sql_query($abfrageModule);
		$module = array();
		while ($rowModule = he_tools_db::six_sql_fetch_assoc($resModule)) {			
			$module[] = $rowModule['rel_id'];
		}
		return $module;
	}
	
	function inhaltEntfernen(&$inhalt, $startMarker, $endeMarker) {
		$contentStart = strpos($inhalt,$startMarker);
		$contentEnde = strpos($inhalt,$endeMarker)+strlen($endeMarker);
		if($contentStart>0 && $contentEnde>0) {
			$teil1 = substr($inhalt,0,$contentStart);
			$teil2 = substr($inhalt,$contentEnde);
			$inhalt = $teil1.$teil2;
		}
	}

	function my_preg_replace($suche, $ersetzen, &$content) {
		$gefunden = preg_match($suche,$content);
		if ($gefunden) {
			$content = preg_replace($suche, $ersetzen, $content);
		}
	}

}
?>