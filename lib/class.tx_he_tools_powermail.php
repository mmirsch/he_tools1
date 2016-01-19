<?php

require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_util.php');
//require_once(t3lib_div::getIndpEnv('TYPO3_DOCUMENT_ROOT') . '/execdir/phpexcel/PHPExcel.php');
//require_once(t3lib_div::getIndpEnv('TYPO3_DOCUMENT_ROOT') . '/execdir/phpexcel/PHPExcel/IOFactory.php');

define('PID_PRAXISSEMESTER_FORMULAR',40);
define('PID_PRAXISSEMESTER_DATEN',40);
define('PID_PRAXISSEMESTER_SP',92098);
define('PID_MENTOREN',131710);
define('PID_MENTEES',131712);
define('UID_STUDIENGANG','STUDIENGANG');
define('UID_FAKULTAET',6);


class tx_he_tools_powermail	{
	static public $sagpSql;
	static public $sagpDb;

	protected $fakultaetsIds = array(
		'AN' => 875,
		'BW' => 877,
		'FZ' => 878,
		'GS' => 879,
		'GL' => 880,
		'GU' => 884,
		'IT' => 874,
		'MB' => 881,
		'ME' => 882,
		'SP' => 883,
		'WI' => 876,
	);

	protected $studiengangFeldIds = array(
		'BW' => 24,
		'AN' => 27,
		'FZ' => 28,
		'GS' => 29,
		'GL' => 30,
		'IT' => 31,
		'MB' => 32,
		'ME' => 33,
		'SP' => 34,
		'VU' => 35,
		'WI' => 36,
	);
	
	protected $anzeigeFelder = array(
		'default' => array(
											'ANMELDEDATUM' => 'Anmeldedatum',
											1 => 'Vorname',
											2 => 'Nachname',
											UID_STUDIENGANG => 'Studiengang',
											),
		'SP' => array(
											'ANMELDEDATUM' => 'Anmeldedatum',
											891 => 'Vorname',
											892 => 'Nachname',
											UID_STUDIENGANG => 'Studiengang',
											),
	);
	
	protected $exportFelder = array(
		'default' => array(
			1 => ' Vorname: ',
			2 => ' Nachname: ',
			3 => ' E-Mail: ',
			4 => ' Telefon: ',
			5 => ' Matrikel-Nr: ',
			6 => ' Fakultät: ',
			UID_STUDIENGANG => ' Studiengang: ',
			7 => ' Praxissemester: ',
			8 => ' Firma: ',
			9 => ' Strasse (Firma): ',
			10 => ' PLZ (Firma): ',
			11 => ' Ort (Firma): ',
			12 => ' Land (Firma): ',
			13 => ' Abteilung: ',
			14 => ' Betreuer/in in der Firma:  ',
			15 => ' Telefon des Betreuers: ',
			16 => ' Fax des Betreuers: ',
			17 => ' E-Mail-Adresse des Betreuers: ',
			18 => ' Vorgesehene Arbeitsgebiete: ',
			23 => ' Adresse während des Praktikum: ',
			25 => ' Praxissemester wird ausgeübt im: ',
			19 => ' erster Arbeitstag: ',
			20 => ' letzter Arbeitstag: ',
			),
		'IT' => array(
			2 => 'Nachname: ',
			1 => 'Vorname: ',
			3 => 'E-Mail: ',
			4 => 'Telefon: ',
			5 => 'Matrikel-Nr: ',
			23 => 'Adresse während des Praktikums: ',
			'DUMMY1' => 'Praxissemester',
			25 => 'Praxissemester wird ausgeübt im: ',
			8 => 'Firma: ',
			9 => 'Strasse (Firma): ',
			10 => 'PLZ (Firma): ',
			11 => 'Ort (Firma): ',
			13 => 'Abteilung: ',
			14 => 'Betreuer/in in der Firma:  ',
			15 => 'Telefon des Betreuers: ',
			16 => 'Fax des Betreuers: ',
			17 => 'E-Mail-Adresse des Betreuers: ',
			18 => 'Vorgesehene Arbeitsgebiete: ',
			19 => 'erster Arbeitstag: ',
			20 => 'letzter Arbeitstag: ',			
			6 => 'Fakultät: ',
			UID_STUDIENGANG => 'Studiengang: ',
			'CRDATE2' => 'pr_date',
			'DUMMY3' => 'HE-Betreuer',
			'DUMMY4' => 'Besuchsvermerk',
			'DUMMY5' => 'Anmerkung',
			'DUMMY6' => 'Vertragskopie',
			),
		'ME' => array(
			'DUMMY1' => 'Titel',
			'CRDATE1' => 'online_date',
			'DUMMY2' => 'offline_date',
			2 => 'Nachname: ',
			1 => 'Vorname: ',
			3 => 'E-Mail: ',
			4 => 'Telefon: ',
			5 => 'Matrikel-Nr: ',
			23 => 'Adresse während des Praktikums: ',
			'DUMMY8' => 'Praxissemester',
			25 => 'Praxissemester wird ausgeübt im: ',
			8 => 'Firma: ',
			9 => 'Strasse (Firma): ',
			10 => 'PLZ (Firma): ',
			11 => 'Ort (Firma): ',
			13 => 'Abteilung: ',
			14 => 'Betreuer/in in der Firma:  ',
			15 => 'Telefon des Betreuers: ',
			16 => 'Fax des Betreuers: ',
			17 => 'E-Mail-Adresse des Betreuers: ',
			18 => 'Vorgesehene Arbeitsgebiete: ',
			19 => 'erster Arbeitstag: ',
			20 => 'letzter Arbeitstag: ',			
			6 => 'Fakultät: ',
			UID_STUDIENGANG => 'Studiengang: ',
			'CRDATE2' => 'pr_date',
			'DUMMY3' => 'pr_checked',
			12 => 'Land (Firma): ',
			'DUMMY4' => 'id',
			'DUMMY5' => 'user',
			),
		'SP' => array(
			892 => 'Nachname: ',
			891 => 'Vorname: ',
			890 => 'Geschlecht: ',
			897 => 'Matrikelnr: ',
			896 => 'Adresse: ',
			893 => 'E-Mail: ',
			894 => 'Telefon: ',
			895 => 'Mobiltel: ',
			898 => 'PS: ',
			910 => 'Studiengang: ',
			913 => 'Vor-Bachelor bestanden? ',
			914 => 'Welche Module fehlen noch? ',
			916 => 'TPS Anmeldung ',
			915 => 'Supervision? ',
			917 => 'Träger der Einrichtung: ',
			918 => 'Name der Einrichtung: ',
			928 => 'Abteilung: ',
			919 => 'Strasse (Firma): ',
			920 => 'PLZ (Firma): ',
			921 => 'Ort (Firma): ',
			922 => 'Land: ',
			923 => 'Internetseite: ',
			923 => 'Anleitung:  ',
			924 => 'E-Mail-Adresse des Betreuers: ',
			925 => 'Telefon des Betreuers: ',	
			929 => 'Vorgesehene Arbeitsgebiete: ',
			930 => 'erster Arbeitstag: ',
			931 => 'letzter Arbeitstag: ',				
			
			'CRDATE2' => 'pr_date',
			),
		'AAA' => array(
			12 => ' Land: ',
			11 => ' Ort: ',
			25 => ' HS-Semester: ',
			8 => ' Firma: ',
			UID_FAKULTAET => ' Fakultät: ',
			),
		'MENTOREN' => array(
			2440 => ' Name ',
			2441 => ' Vorname ',
			2442 => ' Straße/Hausnummer ',
			2443 => ' PLZ/Ort ',
			2444 => ' Telefonnummer ',
			2445 => ' Mobil-Nummer ',
			2446 => ' E-Mailadresse ',
			2447 => ' Geburtsdatum ',
			2448 => ' Kinder? ',
			2449 => ' Interessen/Hobbys ',
			2450 => ' Studiengang ',
			2451 => ' Semester ',
			2453 => ' Besondere Kenntnisse ',
			2452 => ' Studienart ',
			2454 => ' Mitgliedschaft ',
			2455 => ' Erfahrungen ',
			2456 => ' Erwartungen ',
			),
		'MENTEES' => array(
			2461 => ' Name ',
			2462 => ' Vorname ',
			2463 => ' Straße/Hausnummer ',
			2464 => ' PLZ/Ort ',
			2465 => ' Telefonnummer ',
			2466 => ' Mobil-Nummer ',
			2467 => ' E-Mailadresse ',
			2468 => ' Geburtsdatum ',
			2469 => ' Kinder? ',
			2470 => ' Interessen/Hobbys ',
			2471 => ' Studiengang ',
			2472 => ' Semester ',
			2473 => ' Studienart ',
			2474 => ' Besondere Kenntnisse ',
			2481 => ' andere Mentoringprogramme ',
			2475 => ' Mitgliedschaften ',
			2476 => ' Erfahrungen ',
			2477 => ' bevorzugte Patenschaft ',
			),
	);
	
	protected $fakultaetsKuerzel;
	
	public function admin($conf, $cobj) {
		$out = '<div class="praxis_anmeldungen_ueberschrift">Verwaltung der Anmeldungen zum praktischen Studiensemester</div>' . "\n";
		$post = t3lib_div::_POST();
		if (count($post)>0) {
			$out .= $this->behandleAktion($post);
		}
		$user = $GLOBALS['TSFE']->fe_user->user[username];
		$userGroups = $GLOBALS['TSFE']->fe_user->user[usergroup];
		$benutzergruppen = explode(',',$userGroups);
		$paLeitung = tx_he_tools_util::gibBenutzergruppe('PRAKTIKANTENAMTSLEITUNG');
		$paLeitung2 = tx_he_tools_util::gibBenutzergruppe('PRAXISVERWALTER');

		if (!in_array($paLeitung,$benutzergruppen) && !in_array($paLeitung2,$benutzergruppen)) {
			return 'Sie haben keine Berechtigung, diese Praxissemesterdaten zu verwalten!<br/>' .
						 'Wenden Sie sich bitte an die <a href="mailto:t3admin@hs-esslingen.de">Webmaster</a> der Hochschule Esslingen, falls Sie einen Zugang benötigen.';
		}
		$fakultaetsGruppen = tx_he_tools_util::gibFakultaetsBenutzergruppen();
		$fakultaetsBenutzerGruppen = array();
		foreach ($fakultaetsGruppen as $gruppe) {
			if (in_array($gruppe,$benutzergruppen)) {
				$fakultaetsBenutzerGruppen[] = $gruppe;
			}
		}
		if (count($fakultaetsBenutzerGruppen)==0) {
			return 'Sie sind keiner Fakultät in der Funktion "Praxisamtsleitung" zugeordnet!<br/>' .
						 'Wenden Sie sich bitte an die <a href="mailto:t3admin@hs-esslingen.de">Webmaster</a> der Hochschule Esslingen.';
		}
		foreach ($fakultaetsBenutzerGruppen as $fakultaetsBenutzerGruppe) {
			$out .= $this->zeigeAnmeldungen($fakultaetsBenutzerGruppe,$post);
		}
		return $out;
	}
	
	protected function behandleAktion($post) {
		$markierteEintraege = array_keys($post[check]);
    $out = '';
		if (isset($post[markierte_loeschen])) {
			if (count($markierteEintraege)>0) {
				if ($this->eintraegeLoeschen($markierteEintraege)) {
					$out .= '<h2>' . count($markierteEintraege) . ' Einträge gelöscht</h2>';
				}
			} else {
				$out .= $this->meldung('Bitte markieren Sie zuerst die Datensätze, die Sie löschen möchten.');
			}
		} else if (isset($post[eintrag_loeschen])) {
			$eintrag = array_keys($post[eintrag_loeschen]);
			if (count($eintrag)>0) {
				if ($this->eintraegeLoeschen($eintrag)) {
					$out .= '<h3>Eintrag gelöscht</h3>';
				}
			}
		} else if (isset($post[exportieren])) {
			if (count($markierteEintraege) > 0) {
				$exportListe = $this->exportFelder['default'];
				if (isset($post[fakultaets_kuerzel])) {
					$kuerzel = $post[fakultaets_kuerzel];
					if (isset($this->exportFelder[$kuerzel])) {
						$exportListe = $this->exportFelder[$kuerzel];
					}
				}
				if ($this->eintraegeExportierenCsv($markierteEintraege, $exportListe, $post[fakultaets_kuerzel])) {
					$out .= '<h3>Daten Exportiert</h3>';
				}
			}	else {
				$out .= $this->meldung('Bitte markieren Sie zuerst die Datensätze, die Sie exportieren möchten.');
			}
		}	else if (isset($post[exportierenDB])) {
			if (count($markierteEintraege)>0) {
				$exportListe = $this->exportFelder['default'];
				if (isset($post[fakultaets_kuerzel])) {
					$kuerzel = $post[fakultaets_kuerzel];
					if (isset($this->exportFelder[$kuerzel])) {
						$exportListe = $this->exportFelder[$kuerzel];
					}
				}
				$out = $this->eintraegeExportierenSagpDB($markierteEintraege,$exportListe);
			} else {
				$out .= $this->meldung('Bitte markieren Sie zuerst die Datensätze, die Sie exportieren möchten.');
			}
		} 
		return $out;
	}

	protected function zeigeAnmeldungen($benutzergruppe,$post) {
		$fakultaetsKuerzel = tx_he_tools_util::gibBenutzergruppenName($benutzergruppe);
		$titel = tx_he_tools_util::gibFakultaetsName($benutzergruppe);			
		$fakultaetsName = tx_he_tools_util::gibFakultaetsName($benutzergruppe);
		$out = '<h2>' . $titel . '</h2>' . "\n";
		$out .= $this->gibAnmeldeDatenAus($fakultaetsName,$fakultaetsKuerzel,$post);
		return $out;
	}
	
	protected function gibAnmeldeDatenAus($fakultaetsName,$fakultaetsKuerzel,$post) {
		$fakultaetsId = $this->fakultaetsIds[$fakultaetsKuerzel];
		$anmeldungen = $this->gibAnmeldeDaten($fakultaetsName,$fakultaetsId,$fakultaetsKuerzel);
		$uidListe = array();
		foreach ($anmeldungen as $uid) {
			$uidListe[] = $uid;
		}
		$uids = join(',',$uidListe);
		$out = $this->gibMarkierungsJavascript($fakultaetsKuerzel,$uids);
		$out .= '<form class="anmeldedaten" method="post" action="">' . "\n";
		$out .= '<input type="hidden" name="fakultaets_kuerzel" value="' . $fakultaetsKuerzel . '"/>' . "\n";
		$out .= '<table class="anmeldedaten">' . "\n";
		$out .= '<tr class="ueberschrift">';
		$out .= '<th><input type="button" value="Alle" onclick="javascript:alleMarkieren_' . $fakultaetsKuerzel . '()"/></th>' . "\n";
		if ($fakultaetsKuerzel=='SP') {
			$idVorname = 891;
			$idNachname = 892;	
			$anzeigeFelder = $this->anzeigeFelder['SP'];
		} else {
			$idVorname = 1;
			$idNachname = 2;	
			$anzeigeFelder = $this->anzeigeFelder['default'];
		}
		foreach($anzeigeFelder as $feldId=>$feldTitel) {
			$out .= '<th><input type="submit" title="nach ' . $feldTitel . ' sortieren" ' .
										'name="sortieren[' . $feldTitel . ']" value="' . $feldTitel . '" /></th>' . "\n";
		}
		$out .= '<th>Eintrag löschen</th>' . "\n";
		$out .= '</tr>';
		$reihenModus = 'odd';
		
		if (isset($post[sortieren])) {
			$key = array_pop($post[sortieren]);
			switch ($key) {
				case 'Anmeldedatum':
				foreach ($uidListe as $uid) {
					$liste[$uid] = $this->gibFormularFeldwert($uid,'ANMELDUNG_SORT',$fakultaetsKuerzel);
				}
				asort($liste,SORT_NUMERIC);
			break;
			case 'Vorname':
				foreach ($uidListe as $uid) {
					$liste[$uid] = $this->gibFormularFeldwert($uid,$idVorname,$fakultaetsKuerzel);
				}
				asort($liste);
				break;
			case 'Nachname':
				foreach ($uidListe as $uid) {
					$liste[$uid] = $this->gibFormularFeldwert($uid,$idNachname,$fakultaetsKuerzel);
				}
				asort($liste);
				break;
			case 'Studiengang':
				foreach ($uidListe as $uid) {
					$liste[$uid] = $this->gibFormularFeldwert($uid,UID_STUDIENGANG,$fakultaetsKuerzel);
				}
				asort($liste);
				break;
			}
		} else  {
			foreach ($uidListe as $uid) {
				$liste[$uid] = $this->gibFormularFeldwert($uid,UID_STUDIENGANG,$fakultaetsKuerzel);
			}
			asort($liste);
		}
		foreach ($liste as $uid=>$dummy) {
			$out .= '<tr class="' . $reihenModus . '">';
			if ($reihenModus == 'odd') {
				$reihenModus = 'even';
			} else {
				$reihenModus = 'odd';
			}
			$out .= '<td class="checkbox"><input type="checkbox" id="check_' . $uid . '" name="check[' . $uid . ']"/></td>' . "\n";
			foreach($anzeigeFelder as $feldId=>$feldTitel) {
				$wert = $this->gibFormularFeldwert($uid,$feldId,$fakultaetsKuerzel);
				$out .= '<td class="daten">' . $wert . '</td>' . "\n";
			}
			$out .= '<td class="loeschen"><input type="image" src="/typo3/sysext/t3skin/icons/gfx/garbage.gif" name="eintrag_loeschen[' . $uid . ']"/></td>' . "\n";
			$out .= '</tr>' . "\n";
		}
		$out .= '</table>' . "\n";
		if ($fakultaetsKuerzel=='SP') {
			$out .= '<input type="submit" name="exportieren[markierte]" value="Markierte exportieren"/>' . "\n";
//			$out .= '<input type="submit" name="exportierenDB[markierte]" value="Markierte in DB exportieren (aktuell noch im Test)"/>' . "\n";
		} else {
			$out .= '<input type="submit" name="exportieren[markierte]" value="Markierte exportieren"/>' . "\n";
		}
		$out .= '<input type="submit" name="markierte_loeschen" value="Markierte löschen"/>' . "\n";
		$out .= '</form>' . "\n";
		return $out;
	}
	
	protected function gibAnmeldeDatenSingle($uid,$felder,$eintrag,$anzeigefelder,$fakultaet='') {
		$feldliste = $this->gibFormulardaten($felder,$eintrag);
		foreach($anzeigefelder as $feldname) {
			$uid = $this->gibFeldUid($feldname,$fakultaet);
			$ausgabe[] = $feldliste[$uid];
		}
		return $ausgabe;
	}
	
	protected function gibFormulardaten($felder,$eintrag) {
		$feldliste = array();
		foreach($felder as $uid=>$titel) {
/*
 * Alle Felder mit der uid>37 sind Spezialfelder, die nicht exportiert werden
 */
			if ($uid<37) {
				$pattern = '/^(.*<uid' . $uid . '>)(.*)<\/uid' . $uid . '>/Uis';
				preg_match($pattern,$eintrag,$ergebnis);
				$wert = $ergebnis[2];
				if (strpos('Studiengang',$titel)===FALSE || !empty($wert)) {
					$feldliste[$uid] = $wert;
				}
			}
		}
		return $feldliste;
	}
	
	public function gibFormularFelder($fakultaet='') {
		$felder = array();
		$seite = $this->gibFormularSeite($fakultaet);
		$where = 'pid=' . $seite . ' AND deleted=0';
		$abfrageFelder = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,title','tx_powermail_fields',$where,'','uid');
		while ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageFelder)) {
			$felder[$daten[uid]] = $daten[title];
		}
		return $felder;
	}
	
	protected function gibAnmeldeDaten($fakultaetsName,$fakultaetsId,$fakultaet) {
		$anmeldungen = array();
		$seite = $this->gibDatenSeite($fakultaet);
		if ($fakultaet=='SP') {
			$whereFakultaet = 'deleted=0 AND pid=' . $seite;
		} else {
			$whereFakultaet = '(piVars LIKE "%<uid6>'. $fakultaetsName . '</uid6>%" OR ' . 
												' piVars LIKE "%<uid6>'. $fakultaetsId . '</uid6>%") ' .
												' AND deleted=0 AND pid=' . $seite;
		}

		$abfrageDaten = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid','tx_powermail_mails',$whereFakultaet,'','uid');
		while ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageDaten)) {
			$anmeldungen[] = $daten[uid];
		}
		return $anmeldungen;
	}
	
	protected function gibFeldUid($titel,$fakultaet='') {
		$uid = '';
		$seite = $this->gibFormularSeite($fakultaet);
		$where = 'pid=' . $seite . ' AND title="' . $titel . '" AND deleted=0';
		$abfrageFelder = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid','tx_powermail_fields',$where);
		if ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageFelder)) {
			$uid = $daten[uid];
		}
		return $uid;
	}
	
	public function gibFormularFeldwert($uid,$feldId,$fakultaet='',$rmLf=true) {
		$seite = $this->gibFormularSeite($fakultaet);
		if (strpos($feldId,'DUMMY')!==FALSE) {
			$feldWert = '';
		} else if (strpos($feldId,'ANMELDEDATUM')!==FALSE) {
			$where = 'uid=' . $uid;
			$abfrageWert = $GLOBALS['TYPO3_DB']->exec_SELECTquery('crdate','tx_powermail_mails',$where);
			$daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageWert);
			$feldWert = strftime('%d.%m.%Y',$daten[crdate]);
		} else if (strpos($feldId,'ANMELDUNG_SORT')!==FALSE) {
			$where = 'uid=' . $uid;
			$abfrageWert = $GLOBALS['TYPO3_DB']->exec_SELECTquery('crdate','tx_powermail_mails',$where);
			$daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageWert);
			$feldWert = strftime('%Y%m%d',$daten[crdate]);
		} else if (strpos($feldId,'CRDATE')!==FALSE) {
			$where = 'uid=' . $uid;
			$abfrageWert = $GLOBALS['TYPO3_DB']->exec_SELECTquery('crdate','tx_powermail_mails',$where);
			$daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageWert);
			$feldWert = strftime('%d.%m.%Y %H:%M',$daten[crdate]);
		} else {
			$feldWert = '';
			$where = 'uid=' . $uid;
			$abfrageWert = $GLOBALS['TYPO3_DB']->exec_SELECTquery('piVars','tx_powermail_mails',$where);
			$daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageWert);
			$formularEingabe = $daten[piVars];
			if ($feldId==UID_STUDIENGANG) {
				$where = 'pid=' . $seite . ' AND title LIKE "%Studiengang%" AND deleted=0';
				$abfrageFelder = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid','tx_powermail_fields',$where);
				while (empty($feldWert) && $daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageFelder)) {
					$pattern = '/^(.*<uid' . $daten[uid] . '>)(.*)<\/uid' . $daten[uid] . '>/Uis';
					preg_match($pattern,$formularEingabe,$ergebnis);
					$wert = $ergebnis[2];
					if (!empty($wert)) {
						$feldWert = $wert;
					}
				}
			} else if ($feldId==UID_FAKULTAET) {
				$pattern = '/^(.*<uid' . $feldId . '>)(.*)<\/uid' . $feldId . '>/Uis';
				preg_match($pattern,$formularEingabe,$ergebnis);
				$fakultaet = $ergebnis[2];
				$where = 'uid=' . UID_FAKULTAET;
				$abfrageWert = $GLOBALS['TYPO3_DB']->exec_SELECTquery('flexform','tx_powermail_fields',$where);
				$daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageWert);
				$pattern = '/^(.*index="vDEF">)(.*)<\/value>/Uis';
				preg_match($pattern,$daten['flexform'],$ergebnis);
				$zeilen = explode("\n",$ergebnis[2]);
				foreach ($zeilen as $zeile) {
					$eintraege = explode('|',$zeile);
					if (strcmp(trim($eintraege[1]),$fakultaet)==0) {
						$feldWert = trim($eintraege[0]);
						return $feldWert;
					}
				}
				
			} else {
				$pattern = '/^(.*<uid' . $feldId . '>)(.*)<\/uid' . $feldId . '>/Uis';
				preg_match($pattern,$formularEingabe,$ergebnis);
				$feldWert = $ergebnis[2];
				if ($feldId==19 || $feldId==20 || $feldId==930 || $feldId==931) {
					if (strpos($feldWert,'.')===FALSE) {
						$feldWert = strftime('%d.%m.%Y',$feldWert);
					}
				} else if ($feldId==4 || $feldId==15 || $feldId==16 || 
									 $feldId==894 || $feldId==925) {
					$feldWert = $this->formatiereTelefonnummer($feldWert);
				}
			}
		}
    if ($rmLf) {
      $feldWert = str_replace("\n"," ",$feldWert);
    }
		return $feldWert;
	}
	
	protected function gibMarkierungsJavascript($fakultaetsKuerzel,$uids) {
		$out = '<script type="text/javascript" language="JavaScript">
							function alleMarkieren_' . $fakultaetsKuerzel . '() {
								var checkboxen = Array(' . $uids . ');
								var checkMode = "init";
								var i;
								var checkbox;
								
								for (i=0;i<checkboxen.length;i++) {
									checkbox = document.getElementById("check_" + checkboxen[i]);
									if (checkMode=="init") {
										if(checkbox.checked) {
											checkMode = "";
										} else {
											checkMode = "checked";
										}
									}
									checkbox.checked = checkMode;
								}
							}
						</script>
						';
		return $out;
	}
	
	protected function eintraegeLoeschen($eintraege) {
		$loeschen[deleted] = TRUE;
		foreach($eintraege as $uid) {
			$where = 'uid=' . $uid;
			$ergebnis = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_powermail_mails',$where,$loeschen);
			if (!$ergebnis) {
				die ('Fehler beim Löschen eines Eintrags!<br/>' .
						 'Wenden Sie sich bitte an die <a href="mailto:t3admin@hs-esslingen.de">Webmaster</a> der Hochschule Esslingen.');
			}
		}
		return TRUE;
	}
 
	function htmlStart() {
	   $out = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="ltr" xmlns="http://www.w3.org/1999/xhtml" xml:lang="de-DE" lang="de-DE">
<head>
<title>Excel-Export</title>
</head>
<body">
';
	   return $out;
	}
	
	function htmlEnde() {
	   $out = '</body>
</html>
';
	   return $out;
	}
	
	protected function eintraegeExportierenExcel($eintraege,$exportListe,$fakulaet='') {
		$dateiname = "praxissemesterdaten_export.xls";
		header("Content-type: application/vnd-ms-excel");
		header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Content-Disposition: attachment; filename="'.$dateiname.'"');
		header('Pragma: no-cache');
		$out = $this->htmlStart();
		$out .= '<table>';
		$out .= '<tr>';
		foreach($exportListe as $feldId=>$feldTitel) {
			$titel = iconv('UTF-8','CP1252',str_replace(':','',$feldTitel));
			$out .= '<th>' . $titel . '</th>' . "\n";
		}
		$out .= '</tr>';
		foreach($eintraege as $uid) {
			$out .= '<tr>';
			foreach($exportListe as $feldId=>$feldTitel) {
				$wert = $this->gibFormularFeldwert($uid,$feldId,$fakulaet);
				$out .= '<td>' . iconv('UTF-8','CP1252',$wert) . '</td>' . "\n";
			}
			$out .= '</tr>';
		}
		$out .= '</table>';
		$out .= $this->htmlEnde();
		print $out;
		exit();
	}

	protected function eintraegeExportierenCsv($eintraege,$exportListe,$fakultaet='') {
		$fak = strtolower($fakultaet);
		$dateiname = 'praxissemesterdaten_export_' . $fak . '.csv';
		header("Content-type: text/csv");
		header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Content-Disposition: inline; filename="'.$dateiname.'"');
		header('Pragma: no-cache');
		$nl = chr(13) . chr(10);
		foreach($exportListe as $feldId=>$feldTitel) {
			$titelZeile[] = '"' . iconv('UTF-8','CP1252',str_replace(':','',$feldTitel)) . '"';
		}
		$out =  join(';', $titelZeile) . $nl;
		foreach($eintraege as $uid) {
			$zeile = array();
			foreach($exportListe as $feldId=>$feldTitel) {
				$wert = $this->gibFormularFeldwert($uid,$feldId,$fakultaet);
				$zeile[] = '"' . iconv('UTF-8','CP1252',$wert) . '"';
			}
			$out .=  join(';', $zeile) . $nl;
		}
		print $out;
		exit();
	}
/*
 * return t3lib_DB
 */
	protected function initSagpDb() {
		self::$sagpSql = t3lib_div::makeInstance('t3lib_db');
//		self::$sagpDb = self::$sagpSql->sql_pconnect("splx8001.hs-esslingen.de","sagp_practdb_rw","FC3AyFZh6RfCE4Xq") or die('Could not connect to SAGP-Mysql server.' );
		self::$sagpDb = self::$sagpSql->sql_pconnect("splx8001.hs-esslingen.de","wwwtypo3he","KFBWUzLFA75CESbF") or die('Could not connect to SAGP-Mysql server.' );

		self::$sagpSql->sql_select_db("sagp_practdb",self::$sagpDb) or die('Could not select database.');
		return self::$sagpSql;
	}

	protected function eintraegeExportierenSagpDB($eintraege,$exportListe) {
		$out = '';
		/** @var t3lib_DB $db */
		$db = $this->initSagpDb();
		foreach($eintraege as $uid) {
			$eintrag = array();
			foreach($exportListe as $feldId=>$feldTitel) {
				$wert = $this->gibFormularFeldwert($uid,$feldId,'SP');
				$eintrag[$feldId] = iconv('UTF-8','CP1252',$wert);
			}
			$out .= $this->exportiereEintragSagpDb($db,$eintrag);
		}
		$db->connectDB();
		return $out;
	}

	/**
	 * @param $db t3lib_DB
	 * @param $eintrag array
	 */
	protected function exportiereEintragSagpDb(&$db, $eintrag) {
		if (!$this->sagpStudentVorhanden($db, $eintrag['891'], $eintrag['892'], $eintrag['897'], $eintrag['910'])) {
			$email = $eintrag['893'];
			$telPrivate = $eintrag['894'];
			$telMobile = $eintrag['895'];
			$contact = $this->addContactData($db, $email, $telPrivate, $telMobile);
			$address = $this->addAddress($db, $eintrag['891'], $eintrag['891'], $eintrag['891'], $eintrag['891'], $eintrag['891']);
			return '<h3>Noch nicht vorhanden</h3>: ' . $eintrag['891'] . ' ' . $eintrag['892'] . '(' . $eintrag['897'] . ')';
		} else {
			return '<h3>vorhanden</h3>: ' . $eintrag['891'] . ' ' . $eintrag['892'] . '(' . $eintrag['897'] . ')';
		}
		$felder = array(
			891 => 'Vorname: ',
			892 => 'Nachname: ',
			890 => 'Geschlecht: ',
			897 => 'Matrikelnr: ',
			896 => 'Adresse: ',
			893 => 'E-Mail: ',
			894 => 'Telefon: ',
			895 => 'Mobiltel: ',
			898 => 'PS: ',
			910 => 'Studiengang: ',
			913 => 'Vor-Bachelor bestanden? ',
			914 => 'Welche Module fehlen noch? ',
			916 => 'TPS Anmeldung ',
			915 => 'Supervision? ',
			917 => 'Träger der Einrichtung: ',
			918 => 'Name der Einrichtung: ',
			928 => 'Abteilung: ',
			919 => 'Strasse (Firma): ',
			920 => 'PLZ (Firma): ',
			921 => 'Ort (Firma): ',
			922 => 'Land: ',
			923 => 'Internetseite: ',
			923 => 'Anleitung:  ',
			924 => 'E-Mail-Adresse des Betreuers: ',
			925 => 'Telefon des Betreuers: ',
			929 => 'Vorgesehene Arbeitsgebiete: ',
			930 => 'erster Arbeitstag: ',
			931 => 'letzter Arbeitstag: ',
			'CRDATE2' => 'pr_date',
		);

	}

	/**
	 * @param $db t3lib_DB
	 * @param $vorname string
	 * @param $nachname string
	 * @param $matNr string
	 */
	protected function sagpStudentVorhanden(&$db, $vorname, $nachname, $matNr) {
		$where = 'forename="' . $vorname . '" AND name="' . $nachname . '" AND matnr="' . $matNr . '"';
		return $db->exec_SELECTgetSingleRow('matnr', 'student', $where);
	}

	/**
	 * @param $db t3lib_DB
	 * @param $email string
	 * @param $telPrivate string
	 * @param $telMobile string
	 */
	protected function addContactData($db, $email, $telPrivate, $telMobile) {
		$new = array(
			'email_addr'=>$email,
			'tel_private'=>$telPrivate,
			'tel_mobile'=>$telMobile,
			);
		$id = $db->exec_INSERTquery('contact_data', $new);
		return $id;
	}

	/**
	 * @param $db t3lib_DB
	 * @param $postalCode string
	 * @param $city string
	 * @param $street string
	 * @param $house string
	 * @param $country int
	 */
	protected function addAddress($db, $postalCode, $city, $street, $house, $country) {
		$new = array(
			'postal_code' => $postalCode,
			'city' => $city,
			'street' => $street,
			'house' => $house,
			'country' => $country,
		);
		$id = $db->exec_INSERTquery(' address', $new);
		return $id;
	}

	protected function formatiereTelefonnummer($tel) {
		if (strpos($tel,' ')!==FALSE) {
			return $tel;
		}	else {
			$stellen = 4;
			$laenge = strlen($tel);
			$telNeu = '';
			$start = $laenge-$stellen;
/*
			for ($i=$start;$i>$stellen;$i-=$stellen) {
				$telNeu = substr($tel,$i,$stellen) . ' ' . $telNeu;
			}
*/
			for ($i=0;$i<$laenge-$stellen;$i+=$stellen) {
				$telNeu .= substr($tel,$i,$stellen) . ' ';
			}
			$telNeu .= substr($tel,$i);
			return $telNeu;
		}
	}
	
	protected function gibFormularSeite($fakultaet) {
		if ($fakultaet=='SP') {
			return PID_PRAXISSEMESTER_SP;
		} else {
			return PID_PRAXISSEMESTER_FORMULAR;
		}
	}

	protected function gibDatenSeite($fakultaet) {
		if ($fakultaet=='SP') {
			return PID_PRAXISSEMESTER_SP;
		} else {
			return PID_PRAXISSEMESTER_DATEN;
		}
	}

	protected function meldung($text) {
		return '<div class="fehler">' . $text . '</div' . "\n";
	}
	
/*
 * Daten-Export für Studienberatung (Mentoren/Mentees)
 */	
	public function exportFormularStudienberatung() {
		$post = t3lib_div::_POST();
		$out = '<h1>Anmeldedaten exportieren</h1>' . "\n";
		$out .= '<form class="datenExport" method="post" action="">' . "\n";
		$out .= '<input type="submit" name="exportierenMentoren" value="Mentoren-Export"/>
						<input type="submit" name="exportierenMentees" value="Mentees-Export"/>				
						<br />' . "\n";
		$out .= '</form>' . "\n";
		if (isset($post['exportierenMentoren']) || isset($post['exportierenMentees'])) {
			if (isset($post['exportierenMentoren'])) {
				$out .= $this->datenExportierenMentoren();
			} else {
				$out .= $this->datenExportierenMentees();
			}
		}
		return $out;
	}

	protected function datenExportierenMentoren($monat=1,$jahr=2013) {
		$startTstamp = mktime(0,0,0,$monat,1,$jahr);
		$felder = array();
		$seite = PID_MENTOREN;
		$exportListe = $this->exportFelder['MENTOREN'];
		$where = 'deleted=0 AND hidden=0 AND pid=' . $seite . ' AND tstamp>=' . $startTstamp;
		$where = 'deleted=0 AND hidden=0 AND pid=' . $seite;
		$abfrageFelder = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid','tx_powermail_mails',$where,'','uid');
		$eintraege = array();
		while ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageFelder)) {
			$eintraege[] = $daten[uid];
		}
		if (count($eintraege)==0) {
			return '<br/><div style="padding-left: 20px;"><span class="error">Momentan sind keine Anmeldungen für Mentoren vorhanden!</span></div><br />';
		}
    $exportTitle = 'Anmeldungen Mentoren';
    $workbook  = t3lib_div::makeInstance('PHPExcel');
		$workbook ->getProperties()->setTitle($exportTitle)->setSubject($exportTitle);
		PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
		
		$sheet = $workbook ->getActiveSheet();
		$sheet->setTitle('Anmeldungen Mentoren');
		$spalte = 0;
		$anzSpalten = count($exportListe);
		foreach($exportListe as $feldId=>$feldTitel) {
			$feldTitel = trim(str_replace(':','',$feldTitel));
      $sheet->setCellValueByColumnAndRow($spalte, 1, $feldTitel);
			$spalte++;
		}
		for ($zeile=0;$zeile<count($eintraege);$zeile++) {
			$spalte=0;
			foreach($exportListe as $feldId=>$feldTitel) {
				$wert = $this->gibFormularFeldwert($eintraege[$zeile],$feldId);
      	$sheet->setCellValueByColumnAndRow($spalte, $zeile+2, $wert);
      	$spalte++;
			}
		}

		for ($spalte=0;$spalte<$anzSpalten;$spalte++) {
			$sheet->getColumnDimension(chr(ord('A')+ $spalte))->setAutoSize(true);
//			$sheet->getColumnDimension(chr(ord('A')+ $spalte))->setWidth(50);
		}
		
		$dateiname = 'anmeldedaten_export_mentoren.xls';
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $dateiname . '"');
		header('Cache-Control: max-age=0');
		
		$objWriter = PHPExcel_IOFactory::createWriter($workbook , 'Excel5');
		$objWriter->save('php://output');
		exit();
	}
	
	protected function datenExportierenMentees($monat=1,$jahr=2013) {
		$startTstamp = mktime(0,0,0,$monat,1,$jahr);
		$felder = array();
		$seite = PID_MENTEES;
		$exportListe = $this->exportFelder['MENTEES'];
		$where = 'deleted=0 AND hidden=0 AND pid=' . $seite . ' AND tstamp>=' . $startTstamp;
		$where = 'deleted=0 AND hidden=0 AND pid=' . $seite;
		$abfrageFelder = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid','tx_powermail_mails',$where,'','uid');
		$eintraege = array();
		while ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageFelder)) {
			$eintraege[] = $daten[uid];
			foreach($exportListe as $feldId=>$feldTitel) {
				$wert = $this->gibFormularFeldwert($daten[uid],$feldId,'AAA');
				$zeile[] = '"' . iconv('UTF-8','CP1252',$wert) . '"';
			}
		}
		if (count($eintraege)==0) {
			return '<br/><div style="padding-left: 20px;"><span class="error">Momentan sind keine Anmeldungen für Mentees vorhanden!</span></div><br />';
		}
    $exportTitle = 'Anmeldungen Mentees';
		$workbook  = t3lib_div::makeInstance('PHPExcel');
		$workbook ->getProperties()->setTitle($exportTitle)->setSubject($exportTitle);
		PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
		
		$sheet = $workbook ->getActiveSheet();
		$sheet->setTitle('Anmeldungen Mentees');
		$spalte = 0;
		$anzSpalten = count($exportListe);
		foreach($exportListe as $feldId=>$feldTitel) {
			$feldTitel = trim(str_replace(':','',$feldTitel));
      $sheet->setCellValueByColumnAndRow($spalte, 1, $feldTitel);
			$spalte++;
		}
		for ($zeile=0;$zeile<count($eintraege);$zeile++) {
			$spalte=0;
			foreach($exportListe as $feldId=>$feldTitel) {
				$wert = $this->gibFormularFeldwert($eintraege[$zeile],$feldId);
      	$sheet->setCellValueByColumnAndRow($spalte, $zeile+2, $wert);
      	$spalte++;
			}
		}

		for ($spalte=0;$spalte<$anzSpalten;$spalte++) {
			$sheet->getColumnDimension(chr(ord('A')+ $spalte))->setAutoSize(true);
//			$sheet->getColumnDimension(chr(ord('A')+ $spalte))->setWidth(50);
		}
		
		$dateiname = 'anmeldedaten_export_mentees.xls';
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $dateiname . '"');
		header('Cache-Control: max-age=0');
		
		$objWriter = PHPExcel_IOFactory::createWriter($workbook , 'Excel5');
		$objWriter->save('php://output');
		exit();
			}
	
/*
 * Daten-Export für AAA
 */	
	public function exportFormularAAA() {
		$post = t3lib_div::_POST();
    $out = '';
		$datumAktuell = date('m.Y',time());
		if (isset($post['exportieren'])) {
			$datum = $post['startdatum'];
			$datumFelder = explode('.',$datum);
			if (count($datumFelder)==2 && 
					$datumFelder[0]>=1 && $datumFelder[0]<=12 &&
					$datumFelder[1]>=2000 && $datumFelder[1]<=2099
					) {
						$datumAktuell = sprintf('%02d.%4d',$datumFelder[0],$datumFelder[1]);
						$out .= $this->datenExportierenAAA($datumFelder[0],$datumFelder[1]);
					} else {
						$out .= '<h3 class="fehler">Bitte geben Sie das Startdatum im folgenden Format ein:<br/>
										 Monat (als Zahl), Punkt als Trennzeichen, Jahr, z.B. 9.2012.  
										 </div>';
					}
			
		}
		
		$out .= '<form class="datenExportAAA" method="post" action="">' . "\n";
		$out .= 'Daten exportieren ab (Bitte eingeben im Format MM.JJJJ):
						 <input type="text" name="startdatum" value="' . $datumAktuell . '">' . "\n";

		$out .= '<br /><input type="submit" name="exportieren" value="Daten exportieren"/>' . "\n";
		$out .= '</form>' . "\n";
		return $out;
	}
	
	protected function datenExportierenAAA($monat,$jahr) {
		$startTstamp = mktime(0,0,0,$monat,1,$jahr);
		$felder = array();
		$seite = $this->gibFormularSeite('AAA');
		$exportListe = $this->exportFelder['AAA'];
		$where = 'pid=' . $seite . ' AND tstamp>=' . $startTstamp;
		$abfrageFelder = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid','tx_powermail_mails',$where,'','uid');
		$eintraege = array();
		while ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageFelder)) {
			$eintraege[] = $daten[uid];
		}
		
		$dateiname = 'praxissemesterdaten_export_AAA.csv';
		header("Content-type: text/csv");
		header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Content-Disposition: inline; filename="' . $dateiname . '"');
		header('Pragma: no-cache');
		$nl = chr(13) . chr(10);
		foreach($exportListe as $feldTitel) {
			$titelZeile[] = '"' . iconv('UTF-8','CP1252',str_replace(':','',$feldTitel)) . '"';
		}
		$out =  join(';', $titelZeile) . $nl;
		
		foreach($eintraege as $uid) {
			$zeile = array();
			foreach($exportListe as $feldId=>$feldTitel) {
				$wert = $this->gibFormularFeldwert($uid,$feldId,'AAA');
				$zeile[] = '"' . iconv('UTF-8','CP1252',$wert) . '"';
			}
			$out .=  join(';', $zeile) . $nl;
		}
		print $out;
		exit();
	}
	
}
?>
