<?php
require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_db.php');
//require_once(t3lib_extMgm::extPath('six2t3').'lib/class.siximport.php');

define("PERSDB_MA_SEITEN",90604);
define("PERSDB_LB_SEITEN",90603);
define("PERSDB_PROF_SEITEN",90601);

class  he_backend_util {
protected $post;
protected $get;

public function main() {
		$this->post = t3lib_div::_POST();
		$erg .= '<div class="Tools">';
		$erg .= '<form name="seiten-import" method="post" action="">';
		$erg .= '<input type="submit" name="test" value="Testfunktion"/><br/><br/>';
		$erg .= '<input type="submit" name="aendereSeitenId" value="SixCMS-Seiten neu zuweisen"/><br/><br/>';
		$erg .= '</form>';				
		$erg .= '</div>';
		$aendereSeitenId = $this->post[aendereSeitenId];
		if ($aendereSeitenId!='') {
			$erg .= $this->aendereSeitenId();
		}				
		return $erg;
	}
					
	public function aendereSeitenId() {
		$this->post = t3lib_div::_POST();
		$sixid = $this->post[sixid];
		if ($this->post[hole_typo3id]) {
			$typo3id = 'nicht angelegt';
			$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery ('typo3_id','tx_six2t3_pages','id="' . $sixid . '"');
			if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
				$typo3id = $row[typo3_id];
				$out .= '<h4><a style="color: #f00!important;" href="/de/' . $sixid . '" target="_blank">Vorschau?</a></h4>';
			}
		} else {
			$typo3id = $this->post[typo3id];				
		}
		if ($this->post[aendereSeitenId]) {
			if ($sixid>0 && $typo3id>0) {
				$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery ('uid','tx_six2t3_pages','id="' . $sixid . '"');
				if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
					$six2t3pagesDaten[typo3_id] = $typo3id;
					$uid = $row[uid];
					$ergebnis = $GLOBALS['TYPO3_DB']->exec_UPDATEquery ('tx_six2t3_pages','uid=' . $uid,$six2t3pagesDaten);
					if ($ergebnis) {
						$out .= '<h3>Die Seitenzuordnung wurde geändert</h3>';
						$out .= '<h4><a style="color: #f00!important;" href="/de/' . $sixid . '" target="_blank">Vorschau?</a></h4>';
					} else {
						$out .= '<h3>Die Seitenzuordnung ist fehlgeschlagen</h3>';
					}
				}
				$out .= '<br/>' . "\n";
			}
		}
		$out .= '<script type="text/javascript" language="javascript">' . "\n";
		$out .= '	function absenden() {
								document.aendereSeitenId.submit();
							}
						';
		$out .= '</script>'."\n";
		$out .= '<form name="aendereSeitenId" id="aendereSeitenId" method="post" action="">';
		$out .= '<label for="sixid">Six-ID:</label>';
		$out .= '<input type="text" name="sixid" id="sixid" value="' . $sixid . '"' .
						' onblur="javascript:absenden()" />';
		$out .= '<label for="typo3id"> TYPO3-ID:</label>';
		$out .= '<input type="text" id="typo3id"  name="typo3id" value="' . $typo3id . '"/>';
		$out .= '<input type="submit" id="hole_typo3id"  name="hole_typo3id" value="aktuelle TYPO3-ID bestimmen"/>';
		$out .= '<br/>';
		$out .= '<input type="submit" name="aendereSeitenId" value="SixCMS-Seiten neu zuweisen"/>';
		$out .= '<input type="submit" name="zeigeSeitenInfos" value="Seiten-Infos anzeigen"/><br/><br/>';
		$out .= '</form>';			
		return $out;	
	}
		
	public function neueTypo3Seite($sixId) {
		$sixImport = new siximport();
		return $sixImport->importiereSixSeite($sixId);
	}
		
	public function logfileAktionenAusfuehren() {
		$this->post = t3lib_div::_POST();
		$logfileAktion = $this->post[logfileAction];
		if ($logfileAktion!='') {
			$daten = explode('-',$logfileAktion);
			if ($daten[1]=='onChange') {
				$this->updateTypo3Seite($daten[0]);
			} else if ($daten[1]=='onDelete') {
				$this->loescheTypo3Seite($daten[0]);
			} else if ($daten[1]=='onNew') {
				$erg .= $this->neueTypo3Seite($daten[3]);
			}
			$this->loescheProtokollEintrag($daten[2]);
		}				
		$erg .= '<div class="Tools">';
		$erg .= '<form name="seiten-import" method="post" action="">';
		$erg .= $this->gibLogfileAktionen();
		$erg .= '</form>';				
		$erg .= '</div>';
		return $erg;
		
	}
		
	public function gibLogfileAktionen() {
		$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,sixID,mode','tx_six2t3_log','','','mode');
		while ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
			$uid = $daten[uid];
			$sixId = $daten[sixID];
			$mode = $daten[mode];
			$result2 = $GLOBALS['TYPO3_DB']->exec_SELECTquery('typo3_id','tx_six2t3_pages',"id='$sixId'");
			if ($daten2 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result2)) {
				$typo3Id = $daten2[typo3_id];
			}
			if (!empty($typo3Id) && $mode=='onNew') {
				$out .= '<input type="submit" name="logfileAction" value="' . $typo3Id . '-' . $mode . '-' . $uid. '-' . $sixId . '"/>';
			}
		} 
		return $out;
	}
	
	public function loescheProtokollEintrag($uid) {
		$result = $GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_six2t3_log','uid='.$uid);
	}
	
	public function loescheTypo3Seite($typo3Id) {
		$deleted[deleted] = 1;
		$ergebnis = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('pages','uid='.$typo3Id,$deleted);
	}
	
	public function updateTypo3Seite($typo3Id) {
		$typo3PageCache = $GLOBALS['TYPO3_DB']->exec_DELETEquery('cache_pages',
																																		'page_id='.$typo3Id);
		$typo3PageSectionCache = $GLOBALS['TYPO3_DB']->exec_DELETEquery('cache_pagesection',
																																		'page_id='.$typo3Id);
		$urlDe = 'http://www.hs-esslingen.de?id=' . $typo3_id . '&L=0';
		exec("wget -O /dev/null " . $urlDe);
		$urlEn = 'http://www.hs-esslingen.de?id=' . $typo3_id . '&L=1';
		exec("wget -O /dev/null " . $urlEn);
	}
	
	public function sqlAssistent($id) {
		$sqlBefehl = trim($post[sqlBefehl]);
		if ($sqlBefehl!='') {
			$erg .= $this->sqlBefehlAusfuehren();
		}				
		$erg .= '<div class="sqlAssistent">';
		$erg .= '<form name="seiten-import" method="post" action="'.$this->file.'">';
		$erg .= '<textarea" name="sqlBefehl">' . $sqlBefehl . '<textarea/><br/>';
		$erg .= '<input type="submit" name="sqlBefehlAusfuehren" value="SQL ausführen"/><br/>';
		$erg .= '</form>';				
		$erg .= '</div>';
	}
//$out .= '<a onclick="top.loadEditId(33117)" href="#">Testlink</a>';
	
	public function setzeZugriffsrechte($uid,$username,$beUserId,$root=FALSE) {
		if ($root) {
			$rechte = 9;
		}	else {
			$rechte = 31;
		}
		$mitarbeiterSeite[perms_userid] = $beUserId;
		$mitarbeiterSeite[perms_user] = $rechte;
		$mitarbeiterSeite[perms_groupid] = 0;
		$mitarbeiterSeite[perms_group] = 0;
		$mitarbeiterSeite[perms_everybody] = 0;
		$GLOBALS['TYPO3_DB']->exec_UPDATEquery('pages','uid=' . $uid,$mitarbeiterSeite);
		$ergebnis = $username . ':' . $beUserId . '<br>';
		$whereKindseiten = 'pid=' . $uid . ' AND deleted=0';
		$abfrageKindseiten = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','pages',$whereKindseiten);
		while ($datenKindseiten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageKindseiten)) {
			$this->setzeZugriffsrechte($datenKindseiten[uid],$username,$beUserId);
		}
		return $ergebnis;
	}
					
	public function tests($id) {
		return $this->exportModulbeschreibungen();
		$uid = 8964;
		$params = array('cacheCmd' => 'update_dam_pages',
										'uid' => $uid);
		if ($params[cacheCmd]=='update_dam_pages') {
			$where = 'tx_dam.uid=' . $params[uid];		
			$abfrageFelder = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query('tt_content.uid','tx_dam','tx_dam_mm_ref','tt_content',$where);
			
			$abfrage = 'SELECT tt_content.pid FROM tt_content ' . 
								 'INNER JOIN tx_dam_mm_ref ON ' . 
								 'tx_dam_mm_ref.uid_foreign = tt_content.uid ' . 
								 'INNER JOIN tx_dam ON ' . 
								 'tx_dam.uid = tx_dam_mm_ref.uid_local ' . 
								 'where tx_dam.uid=' . $params[uid];		
			$abfrageFelder = $GLOBALS['TYPO3_DB']->sql_query($abfrage);
			
			while ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageFelder)) {
				if (!in_array($daten[pid],$tt_content)) {
					$pages[] = $daten[pid];
				}
			}
			$where = 'bodytext LIKE "%<media ' . $params[uid] . '%"';
			$abfrageFelder = $GLOBALS['TYPO3_DB']->exec_SELECTquery('pid','tt_content',$where);
			while ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageFelder)) {
				if (!in_array($daten[pid],$pages)) {
					$pages[] = $daten[pid];
				}
			}
		}

return print_r($pages,true);			
		
	}
					
	public function firmendaten_exportieren($id) {
		$eintraege = array();
		$where = 'pid=45 AND deleted=0';
		$abfrageFelder = $GLOBALS['TYPO3_DB']->exec_SELECTquery('piVars','tx_powermail_mails',$where,'','uid');
		while ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageFelder)) {
			$eintraege[] = $daten[piVars];
		}
		$felder = array(
										'Träger der Einrichtung' => '77',
										'Firma/Einrichtung' => '8',
										'Strasse' => '9',
										'PLZ' => '10',
										'Ort' => '11',
										'Land' => '12',
										'Ansprechpartner' => '14',
										'E-Mail-Adresse' => '17',
										'Telefon' => '15',
										'Webseite' => '776',
										'Abteilung' => '13',
										);
		$zeilen = '';
		$werte = array();
		foreach ($felder as $titel=>$id) {
			$werte[] = $titel;
		}
		$zeilen .= '"' . implode('";"',$werte) . '"<br />';
		foreach ($eintraege as $eintrag) {
			$werte = array();
			foreach ($felder as $titel=>$id) {
				$werte[] = $this->gibFormularFeldwert($eintrag,$id);
			}
			$zeilen .= '"' . implode('";"',$werte) . '"<br />';
		}
		return $zeilen;
	}

	protected function gibFormularFeldwert($daten,$feldId) {
		$pattern = '/^(.*<uid' . $feldId . '>)(.*)<\/uid' . $feldId . '>/Uis';
		preg_match($pattern,$daten,$ergebnis);
		return $ergebnis[2];
	}
	
	public function faqGenerator($id) {					
		$FAQ_PID = 90817;
		$this->post = t3lib_div::_POST();
		$kategorie = $this->post[kategorien];
		if ($this->post[legeFaqsAn]) {
			$out .= $this->legeFaqsAn($id,$FAQ_PID,$this->post[kategorien],$this->post[sprache]);
		}
					
		$abfrageKategorien = $GLOBALS['TYPO3_DB']->exec_SELECTquery ('*','tx_irfaq_cat','');
		while ($datenKategorien = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageKategorien)) {
			$optionen[$datenKategorien[uid]] = $datenKategorien[title];
		}
		$out .= '<form name="tests" id="testId" method="post" action="">';
		$out .= '<label for="kategorien">FAQ-Kategorie wählen:</label>';
		$out .= '<select name="kategorien">';
		foreach ($optionen as $id=>$label) {
			$out .= '<option value="' . $id . '">' . $label . '</option>';
		}
		$out .= '</select>';
		$out .= '<select name="sprache">';
		$out .= '<option value="0">deutsch</option>';
		$out .= '<option value="1">englisch</option>';
		$out .= '</select>';
		$out .= '<br/>';
		$out .= '<input type="submit" name="legeFaqsAn" value="FAQ-Datensätze anlegen"/>';
		$out .= '</form>';
		return $out;				
	}
	
	public function gibTypo3SeitenAbsaetze($seitenId) {
		$absaetze = array();
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('tx_templavoila_flex','pages','uid = ' . $seitenId);
		if ($data = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$flexform = $data[tx_templavoila_flex];
			if (!empty($flexform)) {
				$flexArray = t3lib_div::xml2array($flexform);
				if (is_array($flexArray)) {
					$absaetze = explode(',',$flexArray['data']['sDEF']['lDEF']['field_maincontent']['vDEF']);
				}
			}
		}
		return $absaetze;
	}
	
	public function legeFaqsAn($seitenId,$pid,$kategorieId,$sprache) {	
		$absaetze = $this->gibTypo3SeitenAbsaetze($seitenId);	
		$anzahl = 1;
		foreach ($absaetze as $absatzId) {
			if ($sprache==0) {
				$where = 'uid=' . $absatzId . ' AND CType="textpic" AND sys_language_uid=' . $sprache;
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tt_content',$where);
			} else {
				// englischer Absatz
				$where = 'l18n_parent=' . $absatzId . ' AND CType="textpic" AND sys_language_uid=' . $sprache;
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tt_content',$where);
			}
			if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$datenFaq[tstamp] = time();
				$datenFaq[crdate] = time();
				$datenFaq[pid] = $pid;
				$datenFaq[q] =  $row[header];
				$datenFaq[a] =  $row[bodytext];
				$datenFaq[cat] =  $kategorieId;
				$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_irfaq_q',$datenFaq);
				$lastID = $GLOBALS['TYPO3_DB']->sql_insert_id();
				$datenFaqSorting[sorting] =  $lastID;
				$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_irfaq_q','uid=' . $lastID,$datenFaqSorting);
				$datenMm[uid_local] = $lastID;
				$datenMm[uid_foreign] = $kategorieId;
				$datenMm[sorting] = $lastID;
				$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_irfaq_q_cat_mm',$datenMm);
				$anzahl++;
			}
			$GLOBALS['TYPO3_DB']->sql_free_result($res);					
		}
		return $anzahl . ' FAQs angelegt';
	}
	
	public function artikelExporte() {
		$this->post = t3lib_div::_POST();
		$bueromaterial = trim($this->post[bueromaterial]);
		if ($bueromaterial!='') {
			$erg .= $this->exportBueromaterialien();
		}				
		$erg .= '<h2>Bitte wählen Sie aus, welche Shopartikel Sie exportieren möchten</h2>';
		$erg .= '<div class="artikelexporte">';
		$erg .= '<form name="artikel-export" method="post" action="'.$this->file.'">';
		$erg .= '<input type="submit" name="bueromaterial" value="Büromaterial exportieren"/><br/><br/>';
		$erg .= '<input type="submit" name="edvVerbrauchsmaterial" value="EDV-Verbrauchsmaterial exportieren"/><br/><br/>';
		$erg .= '<input type="submit" name="mobiliar" value="Mobiliar exportieren"/><br/><br/>';
		$erg .= '</form>';				
		$erg .= '</div>';
		return $erg;
	}
	
	public function exportBueromaterialien() {
		$dateiname = "bueromaterial_export.csv";
		header("Content-type: text/csv");
		header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Content-Disposition: inline; filename="'.$dateiname.'"');
		header('Pragma: no-cache');
		$nl = chr(13) . chr(10);
		$pid = 90126;
		$felder = 'produktname,preis';
		$where = 'deleted=0 AND pid=' . $pid;
		$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery($felder,'tx_hebest_artikel',$where,'','produktname');
		while ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
			$ergebnis .= 	'"' . 
										iconv('UTF-8','CP1252',$daten[produktname]) . 
										'";"' . 
										iconv('UTF-8','CP1252',$daten[preis]) . 
										'"' .
										$nl;
		} 
		print $ergebnis;
		exit();
	}
	
}
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_tools/mod1/class.he_backend_util.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_tools/mod1/class.he_backend_util.php']);
}
?>