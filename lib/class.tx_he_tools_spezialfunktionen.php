<?php 
require_once(t3lib_extMgm::extPath('he_tools').'hooks/class.tx_he_tools_powermail_hooks.php');

class tx_he_tools_spezialfunktionen {
	
	function main($spezialFunktion) {
		$GLOBALS["TSFE"]->set_no_cache();			
		$out = '';
		switch ($spezialFunktion) {
			case 'AnmeldungenTYPO3':
				if ($GLOBALS['TSFE']->fe_user->user['username']=='mmirsch' ||
					$GLOBALS['TSFE']->fe_user->user['username']=='sapfeiff') {
					$out = $this->zeigeAnmeldestandTypo3Schulungen();
				}
				break;
			case 'AnmeldungenGirlsDayGp':
				if ($GLOBALS['TSFE']->fe_user->user['username']=='aeble' ||
						$GLOBALS['TSFE']->fe_user->user['username']=='cfetzer' ||
						$GLOBALS['TSFE']->fe_user->user['username']=='mmirsch') {
					$out = $this->zeigeAnmeldestandGirlsDayGp();
				}
				break;
		}
		return $out;
	}
	
	public function anzahlAnmeldungenGirlsDayGp($conditions,$pid) {
		$summe = 0;
		foreach($conditions['tests'] as $condition)  {
/*
			$where = $conditions['db_field'] . ' LIKE "%' . $condition['check_string'] . '%" AND ' .
							 'deleted=0 AND hidden=0 AND pid=' . $pid;
			$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid','tx_powermail_mails',$where);
			$anzahl = $GLOBALS['TYPO3_DB']->sql_num_rows($abfrage);
			$summe += $anzahl*$condition['count'];
*/
			$where = 'deleted=0 AND hidden=0 AND pid=' . $pid;
			$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid','tx_powermail_mails',$where);
			$anzahl = $GLOBALS['TYPO3_DB']->sql_num_rows($abfrage);
			$summe += $anzahl;
			
			$GLOBALS['TYPO3_DB']->sql_free_result($abfrage);
		}
		return $summe;
	}
	
	function zeigeAnmeldestandGirlsDayGp() {
		$get = t3lib_div::_GET();
		if ($get['export']=='csv') {
			return $this->eintraegeExportierenGirlsDayGpCsv();
		}
		$conf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_hetools_pi1.']['powermail.'];
		$hinweisTexte = array();
		if (is_array($conf['block_elements.'])) {
			$blockElementConfig = $conf['block_elements.'];
			foreach ($blockElementConfig as $cssClass=>$data) {
				$hinweisTexte[] = array('titel'=>$data['infoText'],
																'hinweis'=>$data['hinweis']);
				$cssClass = substr($cssClass,0,strlen($cssClass)-1);
				if (!empty($data['conditions.']) && count($data['conditions.'])>0) {
					$conditions = array('db_field'=>$data['db_field']);
					foreach ($data['conditions.'] as $id=>$conditionData) {
						$conditions['tests'][] = array(
																					 'check_string'=>$conditionData['check_string'],
																					 'count'=>$conditionData['count']);
					}
				} else if (!empty($data['count'])) {
					$conditions['maxCount'] = $data['count'];
				}
				$anzAnmeldungen = tx_he_tools_powermail_hooks::anzahlEintraege($conditions,$conf['pid']);
				$meldungen .= $data['infoText'] . ': ' . $anzAnmeldungen . ' von ' . $data['count'] . ' möglichen Anmeldungen<br>';
			}
		}
		$meldungen .= '<h3><a target="_blank" href="index.php?id=' .  $GLOBALS['TSFE']->id . '&export=csv">Anmeldedaten exportieren</a></h3>';
		$hinweise = '';
		if (!empty($hinweisTexte)) {
			$hinweise .= '<hr/><h3>Hinweistexte bei voller Belegung</h3>';
			foreach ($hinweisTexte as $hinweis) {
				$hinweise .= '<table class="grid tab100"><tbody class="t25_75">
											<tr><td class="td25"><b>Bereich</b></td><td class="td75"><b>' . $hinweis['titel'] . '</b></td></tr>' . 
											'<tr><td class="td25"><b>Hinweistext</b></td><td class="td75">' . $hinweis['hinweis'] . '</td></tr>
											 </tbody></table>';
			}
		}
		$out = '<div style="border: 1px solid #004666; padding: 10px;">
						<h3>Die folgenden Informationen sind nur für Frau Eble und Herrn Fetzer sichtbar (sofern eingeloggt)</h3>
						<span style="color: red; font-weight: normal;">' . 
						$meldungen . '</span>
						' . $hinweise . '</div>';
		return $out;
	}
	
	function eintraegeExportierenGirlsDayGpCsv() {
		$exportFelder = array(
						98 => 'Vorname',
						99 => 'Nachname',
						100 => 'E-Mail',
						103 => 'Name der Schule',
						104 => 'Ort ',
						105 => 'Alter ',
						2133 => 'Wunschworkshop',
						2134 => 'Alt. Wunschworkshop',
/*				
 * Felder für die erweiterte Seite
						2137 => 'Teilnahme vorm.',
						2133 => 'Wunschworkshop vorm.',
						2134 => 'Alt. Wunschworkshop vorm.',
						2131 => 'Teilnahme nachm.',
						2138 => 'Name Mutter',
						2135 => 'Wunschworkshop nachm.',
						2136 => 'Alt. Wunschworkshop nachm.',
*/						
				);
		$seite = 52;
		$dateiname = 'anmeldedatendaten_export_girlsday.csv';
		header("Content-type: text/csv");
		header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Content-Disposition: inline; filename="' . $dateiname . '"');
		header('Pragma: no-cache');
		$nl = chr(13) . chr(10);
		foreach($exportFelder as $feldId=>$feldTitel) {
			$titelZeile[] = '"' . iconv('UTF-8','CP1252',$feldTitel) . '"';
		}
		$out .=  implode(';', $titelZeile) . $nl;
		$where = 'pid=' . $seite . ' AND deleted=0';
		$abfrageFelder = $GLOBALS['TYPO3_DB']->exec_SELECTquery('piVars','tx_powermail_mails',$where,'','uid');
		while ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageFelder)) {
			$zeile = array();
			foreach($exportFelder as $feldId=>$feldTitel) {
				$pattern = '/^(.*<uid' . $feldId . '>)(.*)<\/uid' . $feldId . '>/Uis';
				preg_match($pattern,$daten['piVars'],$ergebnis);
				if (count($ergebnis)==3) {
					$wert = $ergebnis[2];
				} else {
					$wert = '';
				}
				$zeile[] = '"' . iconv('UTF-8','CP1252',$wert) . '"';
			}
			$out .=  implode(';', $zeile) . $nl;
		}
		print $out;
		exit();
	}
		
	protected function gibFormularFelder($pid) {
		$felder = array();
		$where = 'pid=' . $pid . ' AND deleted=0';
		$abfrageFelder = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,title','tx_powermail_fields',$where,'','uid');
		while ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageFelder)) {
			$felder[$daten[uid]] = $daten[title];
		}
		return $felder;
	}
	
	function zeigeAnmeldestandTypo3Schulungen() {
		$out = '';
		$out .= '<table>';
		$anmeldeZahlen = array(0,0,0,0);
		$where = 'piVars LIKE \'%uid2161 type="array"%\' AND deleted=0 AND hidden=0';
		$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('piVars','tx_powermail_mails',$where);
		$kurse = array('Bilder','Formulare','FAQ- und News','Suchmaschine');
		$feldListe = array(
				'uid2155'=>'E-Mail',
		);
		$personenListe = array();
		while ($data = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
			for ($i=0;$i<4;$i++) {
				if (strpos($data['piVars'],$kurse[$i])>0) {
					$anmeldeZahlen[$i]++;
					$suchmuster = '#<uid2155[^>]*>(.*?)</uid2155>#isu'; 
					preg_match($suchmuster,$data['piVars'],$treffer);
					$personenListe[$i][] = $treffer[1];
				}
			}
		}
		$GLOBALS['TYPO3_DB']->sql_free_result($abfrage);
		
		for ($i=0;$i<4;$i++) {
			$out .= '<tr><td>' . $kurse[$i] . ':</td><td>' . $anmeldeZahlen[$i] . ' Anmeldungen</td></tr>';
		}
		$out .= '</table>';
		$out .= '<h2>Personenliste</h2>';
		for ($i=0;$i<4;$i++) {
			$out .= '<h3>' . $kurse[$i] . '</h3>';
			$out .= implode(',',$personenListe[$i]);
		}
		
		return '<h3 style="border: 1px solid #004666; padding: 10px;">
						Die folgenden Informationen sind nur für Sabine und Manfred sichtbar:<br>
						'. 
						$out . 
						'</h3>';
		
	}
}
?>