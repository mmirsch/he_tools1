<?php

require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_lsf.php');

class tx_hetools_loesche_modul_pdfs extends tx_scheduler_Task {
	
	function createPdf($abschlussLsf,$spoVersion,$fakultaet,$studiengangLsf,$vertiefungLsf='') {
		$error = FALSE;
		$url = 'http://www3.hs-esslingen.de/qislsf/rds?state=modulBeschrGast&createPDF=Y&create=blobs&modulversion.semester=&modulversion.versionsid=' .
				'&nodeID=auswahlBaum|abschluss:abschl=' . $abschlussLsf. '|studiengang:stg=' . $studiengangLsf. '|stgSpecials:vert=' . $vertiefungLsf . ',schwp=,kzfa=H,pversion=' . $spoVersion. '&expand=1&asi=#';
		$dateiName = $studiengangLsf;
		if (!empty($vertiefungLsf)) {
			$dateiName .= '_' . $vertiefungLsf;
		}
		$dateiName .= '_' . $spoVersion;
		$dateiNameDe = $dateiName . '_de.pdf';
		$pfad = 'fileadmin/medien/fakultaeten/allgemein/modulbeschreibungen/' . $fakultaet;
		$documentRoot = t3lib_div::getIndpEnv(TYPO3_DOCUMENT_ROOT);
		$pfadKomplett = $documentRoot . '/' . $pfad;
		if (!is_dir($pfadKomplett)) {
			mkdir($pfadKomplett,0755);
		}
		$dateiPfad = $pfadKomplett . '/' . $dateiNameDe;
		$redirectHeader = t3lib_div::getURL($url, 1, true, $report);
		preg_match('#^(.*)(http://www3.hs-esslingen.de/qislsf/.*&asi=)(.*)#Uis',$redirectHeader,$matches);
		$urlNew = $matches[2];
		$content = t3lib_div::getURL($urlNew, 1, true, $report);
		if ($report['error']) {
			$error = 'Fehler beim Einlesen des PDFs: "' . $dateiPfad . '"';
		} else {
			file_put_contents($dateiPfad, $content);
			/*
			$urlEnStart = 'http://www3.hs-esslingen.de/qislsf/rds?state=user&type=5&language=en';
			preg_match('#^(.*)(http://www3.hs-esslingen.de/qislsf/.*&asi=)(.*)#Uis',$redirectHeader,$matches);
			
			$content = t3lib_div::getURL($urlNew, 1, true, $report);
			$dateiNameEn = $dateiName . '_en.pdf';
			$dateiPfad = $pfadKomplett . '/' . $dateiNameEn;
			$redirectHeader = t3lib_div::getURL($url, 1, true, $report);
			preg_match('#^(.*)(http://www3.hs-esslingen.de/qislsf/.*&asi=)(.*)#Uis',$redirectHeader,$matches);
			$urlNew = $matches[2];
			$content = t3lib_div::getURL($urlNew, 1, true, $report);
			if ($report['error']) {
				$error = 'Fehler beim Einlesen des PDFs: "' . $dateiPfad . '"';
			} else {
				file_put_contents($dateiPfad, $content);
			}
			*/
		}		
		return $error;
	}
	
	function recursive_rm($path) {
		return;
		$handle = opendir($path);
		while (($file = readdir($handle)) !== false) {
			if ($file != '.' && $file != '..') {
				$filepath = $path . '/' . $file;
				if (is_dir($filepath)) {
					$this->recursive_rm($filepath);
				} else {
					unlink($filepath);
				}
			}
		}
		closedir($handle);
	}
	
	function execute() {
		return 1;
		$pfad = 'fileadmin/medien/fakultaeten/allgemein/modulbeschreibungen';
		$documentRoot = t3lib_div::getIndpEnv(TYPO3_DOCUMENT_ROOT);
		$pfadKomplett = $documentRoot . '/' . $pfad;
		$this->recursive_rm($pfadKomplett);
		return 1;
		
		$start = time();
		$lsfStudiengangDaten = array();
		$where = 'deleted=0 AND hidden=0 AND cType="list" AND list_type="he_tools_pi1"';
		$contentElems = $GLOBALS['TYPO3_DB']->exec_SELECTquery('pid,pi_flexform','tt_content',$where);
		while ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($contentElems)) {
			$flexFormSettings = t3lib_div::xml2array($daten['pi_flexform']);
			$flexData = $flexFormSettings['data']['sDEF']['lDEF'];
			if (is_array($flexData)) {
				if ($flexData['mode']['vDEF']=='MODULUEBERSICHT_LSF') {
					$spoVersion = $flexData['spo_version']['vDEF'];
					$studiengang = $flexData['studiengang_lsf']['vDEF'];
					$vertiefung = $flexData['vertiefung_lsf']['vDEF'];

					$whereStudiengang = 'uid=' . $studiengang;
					$abfrageStudiengang = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_hetools_module_studiengaenge',$whereStudiengang);
					if ($datenStudiengang = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageStudiengang)) {
						$abschlussLsf = $datenStudiengang['lsf_abs'];
						$studiengangLsf = $datenStudiengang['lsf_stdg'];
						$fakultaet = $datenStudiengang['fakultaet'];
						$vertiefungenLsf = array();
						$whereVertiefung = ' modstud_id=' . $studiengang;
						$abfrageVertiefung = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_hetools_module_vertiefungen',$whereVertiefung);
						while ($datenVertiefung = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageVertiefung)) {
							if (!empty($datenVertiefung['version'])) {
								if ($datenVertiefung['version']==$spoVersion) {
									$vertiefungenLsf[] = $datenVertiefung['kuerzel'];
								}
							} else {
								$vertiefungenLsf[] = $datenVertiefung['kuerzel'];
							}
						}
						if (empty($lsfStudiengangDaten[$studiengang . $spoVersion])) {
							$lsfStudiengangDaten[$studiengang . $spoVersion] = array($abschlussLsf,$spoVersion,$fakultaet,$studiengangLsf,$vertiefungenLsf);
						}
					}
				}
			}
		}
		foreach ($lsfStudiengangDaten as $lsfStudiengang) {
			// PDF für den Studiengang speichern
			$this->createPdf($lsfStudiengang[0],$lsfStudiengang[1],$lsfStudiengang[2],$lsfStudiengang[3],'');
			if (!empty($lsfStudiengang[4]) && count($lsfStudiengang[4])>0) {
				foreach ($lsfStudiengang[4] as $vertiefungLsf) {
					$this->createPdf($lsfStudiengang[0],$lsfStudiengang[1],$lsfStudiengang[2],$lsfStudiengang[3],$vertiefungLsf);
				}
			}
		}
		$ende = time();
		$dauer = $ende-$start;
		t3lib_div::devLog('dauer: ' . $dauer . ' Sekunden', 'lsf_pdfs', 0);
		return 1;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_tools/cron/class.tx_hetools_loesche_modul_pdfs.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_tools/cron/class.tx_hetools_loesche_modul_pdfs.php']);
}
?>