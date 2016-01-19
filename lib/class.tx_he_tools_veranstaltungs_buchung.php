<?php
require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(PATH_t3lib.'class.t3lib_parsehtml_proc.php');
require_once(t3lib_extMgm::extPath('phpexcel_service') . 'class.tx_phpexcel_service.php');

require_once(t3lib_extMgm::extPath('he_tools') . 'lib/class.tx_he_tools_util.php');

class tx_he_tools_veranstaltungs_buchung {
protected $conf;
	
	public function main(&$conf) {
		$this->conf = &$conf;

		$get = t3lib_div::_GET();
		if (isset($get['export'])) {
			if ($get['export']=='ics') {
				return $this->exportUserIcal();
			}
		}

		$GLOBALS['TSFE']->additionalHeaderData['he_tools_jquery'] = '<script src="' . t3lib_extMgm::siteRelPath('he_portal') . 'res/jquery/js/jquery-1.7.1b.min.js" type="text/javascript"></script>';
		$GLOBALS['TSFE']->additionalHeaderData['he_tools_css'] = '<link rel="stylesheet" href="' . t3lib_extMgm::siteRelPath('he_tools') . 'res/veranstaltungen.css" type="text/css" />';
		$username = $GLOBALS['TSFE']->fe_user->user['username'];
		$out = '';
		$out .= '<script type="text/javascript">
						function popupwindow(url,titel) {
							var fenster = window.open(url, titel, "top=50,left=50,width=400,height=600,scrollbars=yes");
							fenster.focus;
							return false;
						}
						</script>
						';
		$pid = $this->conf['veranstaltungen.']['pid'];
		$anmeldeanzahl = $this->conf['veranstaltungen.']['anmeldeanzahl'];
		$post = t3lib_div::_POST();
		if (isset($post['submit_cancel'])) {
			if (!empty($post['termine'])) {
				foreach ($post['termine'] as $veranstaltung=>$termin){
					$erfolg = $this->terminLoeschen($pid,$termin,$username);
				}
				if ($erfolg) {
					$out .= '<h3 class="error">Der von Ihnen gewählte Termin wurde storniert und eine E-Mail mit Ihrer Buchungsübersicht versendet.</h3>';
				} else {
					$out .= '<h3>Beim Stornieren ist ein Fehler aufgetreten! Bitte wenden Sie sich an den <a href="mailto:t3admin@hs-esslingen.de">Webmaster</a></h3>';
				}
			}
		} else if (isset($post['submit_booking'])) {
			if (!empty($post['termine'])) {
				$terminBelegt = false;
				foreach ($post['termine'] as $veranstaltung=>$termin){
					$erfolg = $this->terminEintragen($pid,$termin,$username);
					if (!$erfolg) {
						$terminBelegt = true;
					}
				}
				if ($terminBelegt) {
					$out .= '<h3 class="error">Leider konnte der von Ihnen gewählte Termin nicht gebucht werden, da er mittlerweile belegt ist.</h3>';
				} else {
					$out .= '<h3>Ihre Buchung wurde eingetragen und eine E-Mail mit Ihrer Buchungsübersicht versendet.</h3>';
				}
			}
		}

		$veranstaltungen = $this->gibVeranstaltungsDaten($pid);

		$userTermine = $this->userTerminZeitraeume($username);
		if (is_array($userTermine) && count($userTermine)>0) {
			$url = 'http://www.hs-esslingen.de/index.php?id=' . $GLOBALS['TSFE']->id . '&export=ics';
			$out .= '<h3><a target="_blank" href="' . $url . '">Termine als Kalenderdatei speichern (ics-Export für Outlook)</a></h3>';
		}
		$veranstaltungsIds = array();
		foreach ($veranstaltungen as $veranstaltung) {
			$out .= $this->printVeranstaltungsUeberschrift($veranstaltung['title'],$veranstaltung['raum'],$veranstaltung['link']);
			$userTermin = $this->userbelegung($veranstaltung['uid'],$username);
			if ($userTermin=='booked') {
				$out .= '<p>Sie haben einen Termin für diese Veranstaltung gebucht.<br>Falls Sie diesen wieder stornieren möchten, klicken Sie den Termin bitte an und bestätigen die Stornierung.</p>';
			} else if ($userTermin=='dependend') {
				$out .= '<p>Sie haben bereits einen Termin einer anderen Veranstaltung gebucht.</p>';
			}
			$termine = $this->gibTermine($veranstaltung['uid'],$userTermine,$userTermin);
			$out .= $this->printVeranstaltungsTermine($veranstaltung['uid'],$termine);
			$veranstaltungsIds[] = $veranstaltung['uid'];

		}
		$out .= $this->printJqueryCode($veranstaltungsIds);
		return $out;
	}
	
	public function gibTermine($veranstaltung,$userTermine,$userTermin) {
		$termine = array();
		$where = 'deleted=0 AND hidden=0 and veranstaltung=' . $veranstaltung;
		$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_hetools_veranstaltungen_termine',$where,'','von');
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
			$mode = 'normal';
			$gebucht = $this->zeitraumBereitsGebucht($row['von'],$row['bis'],$veranstaltung,$userTermine);
			if (!empty($gebucht)) {
				if ($gebucht=='exakt') {
					$mode = 'user_booked';
				} else {
					$mode = 'user_blocked';
				}
			} else if ($this->terminBelegt($row['uid'])) {
				if ($userTermin=='booked') {
					$mode = 'disabled booked';
				} else if ($userTermin=='dependend') {
					$mode = 'disabled dependend';
				} else {
					$mode = 'booked';
				}
			} else {
				if ($userTermin=='booked') {
					$mode = 'disabled booked';
				} else if ($userTermin=='dependend') {
					$mode = 'disabled dependend';
				}
			}
			$termine[$row['uid']] = array('von'=>$row['von'],'bis'=>$row['bis'],'mode'=>$mode);
		}
		return $termine;
	}
	
	public function terminEintragen($pid,$termin,$username){
		if ($this->terminBelegt($termin)) {
			return false;
		}
		$daten['pid'] = $pid;
		$daten['username'] = $username;
		$daten['termin'] = $termin;

		$result = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_hetools_veranstaltungen_belegung',$daten);
		if ($result) {
			$email = $GLOBALS['TSFE']->fe_user->user['email'];
			$name = $GLOBALS['TSFE']->fe_user->user['name'];
			$titel = 'Sehr geehrte(r) ' . $name . ',<br>vielen Dank für Ihre Anmeldung!';
			$return = $this->sendeBuchungsUeberblick($titel,$email);
		}			
		return $result;
	}

	public function terminLoeschen($pid,$termin,$username){
		$where = 'deleted=0 AND pid=' . $pid .' AND username="' . $username .'" AND termin=' . $termin;
		$result = $GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_hetools_veranstaltungen_belegung',$where);
		if ($result) {
			$email = $GLOBALS['TSFE']->fe_user->user['email'];
			$name = $GLOBALS['TSFE']->fe_user->user['name'];
			$titel = 'Sehr geehrte(r) ' . $name . ',<br>die von Ihnen ausgewählte Anmeldung wurde storniert!';
			$return = $this->sendeBuchungsUeberblick($titel,$email);
		}
		return $result;
	}

	public function printVeranstaltungsUeberschrift($titel,$raum,$link) {
		$linkUrl = 'index.php?id=' . $link . '&popup=1';
		$out = '<h2><a title="zur Veranstaltungsbeschreibung" onclick="popupwindow(\'' . $linkUrl . '\',\'' . $titel . '\');return false;" href="' . $linkUrl . '">' . $titel . '</a></h2>';
		$out .= '<h3>Raum: ' . $raum . '</h3>';
		return $out;
	}
	
	public function printVeranstaltungsBelegtHinweis() {
		$out = '<p>Sie haben bereits einen Termin für diese Veranstaltung gebucht.</p>';
		return $out;
	}

	public function printVeranstaltungsTermine($id,$termine) {
		$titles = array(
			'normal'=>'Bitte wählen Sie diesen Termin durch Anklicken aus.',
			'booked'=>'Dieser Termin ist bereits belegt.',
			'disabled booked'=>'Dieser Termin ist bereits belegt.',
			'disabled dependend'=>'Dieser Termin ist bereits belegt.',
			'user_blocked'=>'Sie haben in diesem Zeitraum bereits einen anderen Termin gebucht.',
			'user_booked'=>'Sie können diesen bereits gebuchten Termin durch Anklicken wieder stornieren.',
			'disabled'=>'Sie haben bereits einen anderen Termin für diese Veranstaltung gebucht.',
		);

		$out = '<form method="POST" id="form_' . $id . '" >';
		foreach ($termine as $uid=>$daten) {
			$readonly = '';
			$cssClass = ' class="uhrzeit ' . $daten['mode'] . '" ';
			if ($daten['mode']!='normal' && $daten['mode']!='user_booked') {
				$readonly = ' disabled="disabled" ';
			}
			$terminId = $id . $uid;
			$labelTitle = '';
			/*
			if (isset($titles[$daten['mode']])) {
				$labelTitle = 'title="' . $titles[$daten['mode']] . '"';
			}
			*/
			$out .= '<span ' . $cssClass . '>' .
							'<input class="veranstaltungs_termin" type="radio" id="' . $terminId . '" name="termine[' . $id . ']" 
							 ' . $readonly . ' value="' . $uid . '" />' .
							'<label ' . $labelTitle . ' for="' . $terminId . '">' . $daten['von'] . '-' . $daten['bis'] . '</label>' .
							'</span>';
		}
		$out .= '<input type="submit" name="submit_booking" id="submit_booking_' . $id . '" value="Termin verbindlich buchen" class="button hidden" />';
		$out .= '<input type="submit" name="submit_cancel" id="submit_cancel_' . $id . '" value="Gebuchten Termin stornieren" class="button hidden" />';
		$out .= '</form>';
		
		return $out;
	}

	public function gibVeranstaltungsAbhaengigkeiten() {
		// Alle gebuchtenTermine des eingeloggte Benutzer zurückgeben
		$select = 'SELECT veranstaltungen FROM tx_hetools_veranstaltungen_abhaengigkeiten WHERE deleted=0';
		$res = $GLOBALS['TYPO3_DB']->sql_query($select);
		$abhaengigkeiten = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$abhaengigkeiten[] =  explode(',',$row['veranstaltungen']);
		}
		return $abhaengigkeiten;
	}

	public function userTermine($username) {
// Alle gebuchtenTermine des eingeloggte Benutzer zurückgeben
		$selectUserTermine = 'SELECT tx_hetools_veranstaltungen_belegung.termin FROM tx_hetools_veranstaltungen_belegung
													INNER JOIN tx_hetools_veranstaltungen_termine ON tx_hetools_veranstaltungen_termine.uid = tx_hetools_veranstaltungen_belegung.termin
													WHERE tx_hetools_veranstaltungen_belegung.deleted=0 AND tx_hetools_veranstaltungen_belegung.username="' . $username . '" ORDER BY tx_hetools_veranstaltungen_termine.von';
		$res = $GLOBALS['TYPO3_DB']->sql_query($selectUserTermine);
		$termine = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$termine[] =  $row['termin'];
		}
		return $termine;
	}

	public function userTerminZeitraeume($username) {
// Termine des eingeloggte Benutzer zurückgeben
		$selectUserTermine = 'SELECT termin FROM tx_hetools_veranstaltungen_belegung WHERE deleted=0 AND username="' . $username . '"';
		$sqlQuery = 'SELECT veranstaltung,von,bis FROM tx_hetools_veranstaltungen_termine WHERE ' .
								'  deleted=0 AND uid IN (' . $selectUserTermine . ')';

		$res = $GLOBALS['TYPO3_DB']->sql_query($sqlQuery);
		$termine = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$termine[$row['veranstaltung']] =  array('von'=>$row['von'],'bis'=>$row['bis']);
		}
		return $termine;
	}

	public function userbelegung($veranstaltung, $username) {
// Prüfen ob der eingeloggte Benutzer bereits einen Termin der Veranstaltung belegt hat
		$selectUserTermine = 'SELECT termin FROM tx_hetools_veranstaltungen_belegung WHERE deleted=0 AND username="' . $username . '"';
		$veranstaltungsAbhaengigkeiten = $this->gibVeranstaltungsAbhaengigkeiten();
		$whereAnd = ' AND veranstaltung=' . $veranstaltung;
		if (is_array($veranstaltungsAbhaengigkeiten) && count($veranstaltungsAbhaengigkeiten)>0) {
			foreach($veranstaltungsAbhaengigkeiten as $veranstaltungsAbhaengigkeit) {
				if (in_array($veranstaltung,$veranstaltungsAbhaengigkeit)) {
					$veranstaltungen = implode(',',$veranstaltungsAbhaengigkeit);
					$whereAnd = ' AND veranstaltung IN (' . $veranstaltungen . ')';
				}
			}
		}

		$sqlQuery = 'SELECT von,bis,veranstaltung FROM tx_hetools_veranstaltungen_termine WHERE ' .
			'  deleted=0 AND uid IN (' . $selectUserTermine . ')' . $whereAnd;
		$res = $GLOBALS['TYPO3_DB']->sql_query($sqlQuery);
		if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			if ($row['veranstaltung']==$veranstaltung) {
				return 'booked';
			} else {
				return 'dependend';
			}
		}
		return '';
	}
	
	public function terminBelegt($termin) {
// Prüfen ob der konkrete Termin bereits belegt ist
		$resultBelegung = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_hetools_veranstaltungen_belegung','deleted=0 AND hidden=0 and termin=' . $termin);
		$anmeldungen = $GLOBALS['TYPO3_DB']->sql_num_rows($resultBelegung);

		$sqlQueryMaxTeilnehmer = 'SELECT max_teilnehmer FROM tx_hetools_veranstaltungen INNER JOIN tx_hetools_veranstaltungen_termine ' .
								'ON tx_hetools_veranstaltungen.uid=tx_hetools_veranstaltungen_termine.veranstaltung ' .
 								'WHERE tx_hetools_veranstaltungen_termine.uid=' . $termin;
		$res = $GLOBALS['TYPO3_DB']->sql_query($sqlQueryMaxTeilnehmer);
		if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$maxTeilnehmer = $row['max_teilnehmer'];
			if ($anmeldungen<$maxTeilnehmer) {
				return FALSE;
			}
		}
		return TRUE;
	}

	public function zeitraumBereitsGebucht($von,$bis,$veranstaltung,$userTermine) {
		$von1 = strtotime($von);
		$bis1 = strtotime($bis);

		foreach($userTermine as $gebuchteVeranstaltung=>$termin) {
			$von2 = strtotime($termin['von']);
			$bis2 = strtotime($termin['bis']);
			if (($von1>=$von2 && $von1<$bis2) ||
					($bis1>$von2 && $bis1<=$bis2)) {
				if ($veranstaltung==$gebuchteVeranstaltung) {
					return 'exakt';
				} else {
					return 'zeitraum';
				}
			}
		}
		return false;
	}

	public function printJqueryCode($veranstaltungsIds) {
		$out = '
			<script type="text/javascript">
			$(".veranstaltungs_termin").click(function(elem) {
				var parentElem = $(this).parent();
				var name = this.name;
				var left = name.indexOf("[")+1;
				var length = name.indexOf("]")-left;
				var veranstaltungsId = name.substr(left,length);

				$("form .uhrzeit").removeClass("active");
				$(this).parent().addClass("active");

			if ($(parentElem).hasClass("normal")) {
				';
			foreach ($veranstaltungsIds as $id)	{
				$out .= '
				if (!$("#submit_booking_' . $id . '").hasClass("hidden")) {
					$("#submit_booking_' . $id . '").addClass("hidden");
				}
				';
			}
			$out .= '$("#submit_booking_" + veranstaltungsId).removeClass("hidden");
				} else if ($(parentElem).hasClass("user_booked")) {
				';
			foreach ($veranstaltungsIds as $id)	{
				$out .= '
					if (!$("#submit_cancel_' . $id . '").hasClass("hidden")) {
						$("#submit_cancel_' . $id . '").addClass("hidden");
					}
					';
			}
			$out .= '$("#submit_cancel_" + veranstaltungsId).removeClass("hidden");
				}
			';

		$out .= '
				});
				</script>';
		return $out;
	}
	
	public function time2timestamp($zeit) {
		$zeitDaten = explode(':',$zeit);
		if (count($zeitDaten)==2) {
			return ($zeitDaten[0] * 3600 + $zeitDaten[1] * 60);
		}
		return 0;
	}
	
	public static function loescheAlleVeranstaltungsTermine($pid) {
//		$result = $GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_hetools_veranstaltungen_termine','TRUE');
		$result = $GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_hetools_veranstaltungen_termine','pid =' . $pid);
		if ($result) {
			return '<h2>Alle Belegungen dieser PID wurden gelöscht</h2>';
		}	else {
			return '<h2>Beim Löschen der Belegungen gab es einen Fehler</h2>';
		}
	}
	
	public function loescheAlleVeranstaltungsBelegungen() {
		$result = $GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_hetools_veranstaltungen_belegung','TRUE');
	}
	
	public function generiereAlleVeranstaltungsTermine($pid) {
		$this->loescheAlleVeranstaltungsTermine($pid);
		$veranstaltungen = $this->gibVeranstaltungsDaten($pid);
		foreach ($veranstaltungen as $veranstaltung) {
			$start = $this->time2timestamp($veranstaltung['startzeit']);
			$ende = $this->time2timestamp($veranstaltung['endzeit']);
			$intervall = $veranstaltung['intervall']*60;
			$pause = $veranstaltung['pause']*60;
			for ($zeit=$start; $zeit<$ende; $zeit += ($intervall+$pause)) {
				$this->generiereVeranstaltungsTermin($veranstaltung['uid'],$zeit,$zeit+$intervall,$pid);
			}
		}
		return '<h2>Alle Veranstaltungstermine wurden erzeugt</h2>';
	}
	
	public function generiereVeranstaltungsTermin($veranstaltungsId,$von,$bis,$pid) {
		$daten['pid'] = $pid;
		$stundenVon = $von / 3600;
		$minutenVon = ($von % 3600) / 60;
		$daten['von'] = sprintf("%02d:%02d",$stundenVon,$minutenVon);
		$stundenBis = $bis / 3600;
		$minutenBis = ($bis % 3600) / 60;
		$daten['bis'] = sprintf("%02d:%02d",$stundenBis,$minutenBis);
		$daten['veranstaltung'] = $veranstaltungsId;
		$result = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_hetools_veranstaltungen_termine',$daten);			
	}
	
	public function gibBenutzerDaten($username) {
		$benutzerDaten = array();
		$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery('name,email','fe_users','deleted=0 AND disable=0 AND username="' . $username . '"');			
		if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
			$benutzerDaten = $row['name'] . ' (' . $row['email'] . ')';
		} else {
			$benutzerDaten = 'keine Anmeldung';
		}
		return $benutzerDaten;
	}
	
	public function exportTerminPlan(&$terminKalender) {
		$dateiname = 'termine_gesundheitstag.xls';

		$phpExcelService =  t3lib_div::makeInstance('tx_phpexcel_service');
		$phpExcel = $phpExcelService->getPHPExcel();
		$title = 'Anmeldungen - Gesundheitstag 2015';
		$phpExcel ->getProperties()->setTitle($title)->setSubject($title);
		PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);

		// das erste worksheet anwaehlen
		$sheet = $phpExcel ->getActiveSheet();
		$sheet->setTitle('Anmeldungen2015');

		$zeile = 1;
		foreach($terminKalender as $veranstaltungsDaten) {
			$titelEintrag =  $veranstaltungsDaten['title'] . ' - (Raum ' . $veranstaltungsDaten['raum'] . ')';

			$objRichText = new PHPExcel_RichText();
			$cellTitle = $objRichText->createTextRun($titelEintrag);
			$cellTitle->getFont()->setSize('14');
			$cellTitle->getFont()->setName('Arial');
			$sheet->setCellValueByColumnAndRow(0, $zeile, $objRichText);
			$zelle = 'A' . $zeile;
			$zellRaum = 'A' . $zeile . ':B' . $zeile;
			$sheet->mergeCells($zellRaum);
			$sheet->getStyle($zelle)->getAlignment()->setWrapText(true);
			$sheet->getRowDimension($zeile)->setRowHeight(40);
			$zeile++;

			$objRichText = new PHPExcel_RichText();
			$cellTitle = $objRichText->createTextRun('Uhrzeit');
			$cellTitle->getFont()->setSize('12');
			$cellTitle->getFont()->setName('Arial');
			$sheet->setCellValueByColumnAndRow(0, $zeile, $objRichText);
			$zelle = 'A' . $zeile;
			$sheet->getStyle($zelle)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle($zelle)->getAlignment()->setWrapText(true);

			$objRichText = new PHPExcel_RichText();
			$cellTitle = $objRichText->createTextRun('Person');
			$cellTitle->getFont()->setSize('12');
			$cellTitle->getFont()->setName('Arial');
			$sheet->setCellValueByColumnAndRow(1, $zeile, $objRichText);
			$zelle = 'B' . $zeile;
			$sheet->getStyle($zelle)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$zelle = 'A' . $zeile;
			$sheet->getStyle($zelle)->getAlignment()->setWrapText(true);
			$sheet->getRowDimension($zeile)->setRowHeight(40);
			$zeile++;

			foreach ($veranstaltungsDaten['belegung'] as $terminDaten) {
				$sheet->setCellValueByColumnAndRow(0, $zeile, $terminDaten['zeit']);
				$zelle = 'A' . $zeile;
				$sheet->getStyle($zelle)->getAlignment()->setWrapText(true);
				$zelle = 'B' . $zeile;
				$sheet->setCellValueByColumnAndRow(1, $zeile, $terminDaten['user']);
				$sheet->getStyle($zelle)->getAlignment()->setWrapText(true);
				$zeile++;
			}

		}
		$sheet->getColumnDimension('A')->setWidth(15);
		$sheet->getColumnDimension('B')->setWidth(80);

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $dateiname . '"');
		header('Cache-Control: max-age=0');
		$excelWriter = $phpExcelService->getInstanceOf('PHPExcel_Writer_Excel2007', $phpExcel);
		$excelWriter->save('php://output');

		exit();

	}

	public function exportiereVeranstaltungsBelegung($pid) {
		$terminKalender = array();
		$resVeranstaltung = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_hetools_veranstaltungen','deleted=0 AND hidden=0 and pid=' . $pid,'','sorting');
		while ($rowVeranstaltung = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resVeranstaltung)) {
			$index = 0;
			$terminKalender[$rowVeranstaltung['uid']] = array('title'=>$rowVeranstaltung['title'],
																		 'raum'=>$rowVeranstaltung['raum']);
			$terminKalender[$rowVeranstaltung['uid']]['belegung'] = array();
			$whereTermine = 'deleted=0 AND hidden=0 and veranstaltung=' . $rowVeranstaltung['uid'];
			$resTermine = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_hetools_veranstaltungen_termine',$whereTermine,'','von');			
			while ($rowTermine = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resTermine)) {
				// $terminKalender[$rowVeranstaltung['uid']]['belegung'][$rowTermine['uid']]['zeit'] = $rowTermine['von'] . '-' . $rowTermine['bis'];
				$zeit = $rowTermine['von'] . '-' . $rowTermine['bis'];
				$whereBelegung = 'deleted=0 AND hidden=0 and termin=' . $rowTermine['uid'];
				$resBelegung = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_hetools_veranstaltungen_belegung',$whereBelegung);	
				$anzahl = $GLOBALS['TYPO3_DB']->sql_num_rows($resBelegung);	
				if ($anzahl==0) {
					$terminKalender[$rowVeranstaltung['uid']]['belegung'][$index]['zeit'] = $zeit;
					$terminKalender[$rowVeranstaltung['uid']]['belegung'][$index]['user'] = '';
					$index++;
				} else {
					while ($rowBelegung = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resBelegung)) {
						$terminKalender[$rowVeranstaltung['uid']]['belegung'][$index]['zeit'] = $zeit;
						$terminKalender[$rowVeranstaltung['uid']]['belegung'][$index]['user'] = $this->gibBenutzerDaten($rowBelegung['username']);
						$index++;
					}
				}
				
			}
		}
//t3lib_div::debug($terminKalender,'$terminKalender');
//exit;			
		$this->exportTerminPlan($terminKalender);
	}
	
	public function gibVeranstaltungsDaten($pid) {
		$veranstaltungen = array();
		$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_hetools_veranstaltungen','deleted=0 AND hidden=0 and pid=' . $pid,'','sorting');
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
			$veranstaltungen[]= $row;
		}
		return $veranstaltungen;
	}
	
	public function gibTerminDetails($termin,&$titel,&$ort,&$raum,&$datum,&$uhrzeit) {
		$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_hetools_veranstaltungen_termine','uid=' . $termin);			
		if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
			$uhrzeit = $row['von'] . '-' . $row['bis'];
			$resultVeranstaltung = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_hetools_veranstaltungen','uid=' . $row['veranstaltung']);			
			if ($rowVeranstaltung = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resultVeranstaltung)) {
				$datum = date('d.m.Y',$rowVeranstaltung['datum']);
				$titel = $rowVeranstaltung['title'];
				$ort = $rowVeranstaltung['ort'];
				$raum = $rowVeranstaltung['raum'];
			}
		}
	}

	public function exportUserIcal() {
		$username = $GLOBALS['TSFE']->fe_user->user['username'];
		$userTermine = $this->userTermine($username);
		$eol = "\r\n";
		$titleVeranstaltung = $this->conf['veranstaltungen.']['veranstaltungs_titel'];

		$url = 'http://www.hs-esslingen.de/index.php?id=' . $GLOBALS['TSFE']->id;
		$content = 'BEGIN:VCALENDAR' .$eol .
								'VERSION:2.0' .$eol .
								'PRODID:-//hs-esslingen.de/gesundheitstag//NONSGML v1.0//DE' .$eol .
								'X-WR-CALNAME:Gesundheitstag-2015' . $eol .
								'X-WR-TIMEZONE:Europe/Berlin' . $eol .
								'BEGIN:VTIMEZONE' . $eol .
								'TZID:Europe/Berlin' . $eol .
								'X-LIC-LOCATION:Europe/Berlin' . $eol .
								'BEGIN:DAYLIGHT' . $eol .
								'DTSTART:19700329T020000' . $eol .
								'RRULE:BYMONTH=3;FREQ=YEARLY;BYDAY=-1SU' . $eol .
								'TZOFFSETFROM:+0100' . $eol .
								'TZOFFSETTO:+0200' . $eol .
								'END:DAYLIGHT' . $eol .
								'BEGIN:STANDARD' . $eol .
								'DTSTART:19701025T030000' . $eol .
								'RRULE:BYMONTH=10;FREQ=YEARLY;BYDAY=-1SU' . $eol .
								'TZOFFSETFROM:+0200' . $eol .
								'TZOFFSETTO:+0100' . $eol .
								'END:STANDARD' . $eol .
								'END:VTIMEZONE' . $eol;


			foreach($userTermine as $termin) {
				$this->gibTerminDetails($termin,$titel,$ort,$raum,$datum,$uhrzeit);
				$zeitraum = explode('-',$uhrzeit);
				$startTime = strtotime($datum . ', ' . $zeitraum[0]);
				$endTime = strtotime($datum . ', ' . $zeitraum[1]);

				$content .= 'BEGIN:VEVENT' . $eol .
										'DTEND;TZID="Europe/Berlin":' . date('Ymd\TGis', $endTime) . $eol .
										'DTSTAMP:' . date('Ymd\TGis', time()) . $eol .
										'DTSTART;TZID="Europe/Berlin":' . date('Ymd\TGis', $startTime) . $eol .
										'UID:' . md5($datum . $uhrzeit) . $eol .
										'LOCATION:' . htmlspecialchars($ort . ' - ' . $raum) . $eol .
										'URL;VALUE=URI:' . htmlspecialchars($url) . $eol .
										'SUMMARY:' . htmlspecialchars($titel) . $eol .
										'BEGIN:VALARM' . $eol .
										'TRIGGER:-PT30M' . $eol .
										'ACTION:DISPLAY' . $eol .
										'DESCRIPTION:Reminder' . $eol .
										'END:VALARM' . $eol .
										'END:VEVENT' . $eol;
			}

		$content .= "END:VCALENDAR";
		$filename = 'gesundheitstag-2015.ics';

		// Set the headers
		header('Content-type: text/calendar; charset=utf-8');
		header('Content-Disposition: attachment; filename=' . $filename);

		echo $content;
		exit();
	}

	public function sendeBuchungsUeberblick($anrede,$email) {
		$mail = t3lib_div::makeInstance('t3lib_mail_Message');
		$sender = $this->conf['veranstaltungen.']['email.']['sender'];
		$subject = $this->conf['veranstaltungen.']['email.']['subject'];
		$footer = $this->conf['veranstaltungen.']['email.']['footer.']['value'];
		$titleVeranstaltung = $this->conf['veranstaltungen.']['veranstaltungs_titel'];
		$username = $GLOBALS['TSFE']->fe_user->user['username'];
		$userTermine = $this->userTermine($username);

		$bodyHtml = '<p>' . $anrede . '</p>';
		if (count($userTermine)==0) {
			$bodyHtml .= '<p>Aktuell sind keine Veranstaltungen für Sie gebucht</p>';
		} else {
			$bodyHtml .= '<p>Aktuell sind folgende Veranstaltungen für Sie gebucht:</p>
										<table>
										';
			foreach($userTermine as $termin) {
				$this->gibTerminDetails($termin,$titel,$ort,$raum,$datum,$uhrzeit);
				$bodyHtml .= '<tr class="title"><th colspan="2">' . $titleVeranstaltung . ': ' . $titel . '</th></tr>
								<tr><td class="right">Datum:</td><td>' . $datum . '</td></tr>
								<tr><td class="right">Zeit:</td><td>' . $uhrzeit . '</td></tr>
								<tr><td class="right">Ort:</td><td>' . $ort . '</td></tr>
								<tr><td class="right">Raum:</td><td>' . $raum . '</td></tr>
				';
			}
			$bodyHtml .= '</table>
									';
		}
		$bodyHtml .= '<p>' . $footer . '</p>';
		$bodyPlain = strip_tags($bodyHtml);				
		$mail->setFrom($sender);
		$mail->setTo($email);
		$mail->setSubject($subject);
		$htmlComplete = $this->initHtml($subject) . 
										$bodyHtml . 
										$this->exitHtml();
		$mail->setBody($htmlComplete, 'text/html');
		$mail->addPart($bodyPlain, 'text/plain');
		$erg = $mail->send();
		if (!$erg) {
			$failedRecipients = $mail->getFailedRecipients();
			t3lib_div::devlog('E-Mail-Versand fehlgeschlagen!','fe_managment',0,$failedRecipients);
		}
		return $erg;
	}

	function initHtml($subject) {
		return '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>' . $subject . '</title>
	<style type="text/css">
	table {
		border-collapse: collapse;
	}
	th, td {
		border: 1px solid #004666;
		padding: 4px 8px;
	}
	th {
		text-align: left;
	}
	td.right {
		text-align: right;
	}
	tr.title {
		margin-top: 20px;
	}
	</style>
</head>
<body >
<div id="content" 
	style="font-family: verdana, arial, helvetica, sans-serif; padding: 0 20px; font-size:80%;">
		'; 
	}

	function exitHtml() {
		return '
</div>
</body>
</html>
		';
	}
	
	public function backend_util($pid) {
		$post = t3lib_div::_POST();
		$erg = '<div class="Tools">';
		$erg .= '<form name="veranstaltungsbuchung" method="post" action="">';
		$erg .= 'Systemordner für die Termine: <input type="text name="pid" value="' . $pid . '"/><br/><br/>';
		$erg .= '<input type="submit" value="Alle Belegungen löschen" name="belegungen_loeschen"/><br/><br/>';
		$erg .= '<input type="submit" value="Alle Veranstaltungstermine erzeugen" name="termine_generieren"/><br/><br/>';
		$erg .= '<input type="submit" value="Buchungen exportieren" name="belegung_exportieren"/><br/><br/>';
		$erg .= '</form>';				
		$erg .= '</div>';
		$belegungen_loeschen = $post['belegungen_loeschen'];
		$termine_generieren = $post['termine_generieren'];
		$belegung_exportieren = $post['belegung_exportieren'];
		if ($belegungen_loeschen!='') {
			$erg .= $this->loescheAlleVeranstaltungsBelegungen($pid);
		}	else if ($termine_generieren!='') {
			if (empty($pid)) {
				$erg .= '<p class="error">Bitte den Systemordner für die Termine eintragen bzw. auswählen</p>';
			} else {
				$erg .= $this->generiereAlleVeranstaltungsTermine($pid);
			}
		}	else if ($belegung_exportieren!='') {
			if (empty($pid)) {
				$erg .= '<p class="error">Bitte den Systemordner für die Termine eintragen bzw. auswählen</p>';
			} else {
				$erg .= $this->exportiereVeranstaltungsBelegung($pid);
			}
		}				
		return $erg;
	}
	
	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_portal/gadgets/class.tx_he_tools_lib_db_suche.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_portal/gadgets/class.tx_he_tools_lib_db_suche.php']);
}
?>