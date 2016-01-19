<?php 
require 'SOAP/Client.php';
require_once(t3lib_extMgm::extPath('he_tools').'lib/XML.inc.php');
require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_util.php');
define(SEM_STRING,'Fachsemester');

class tx_he_tools_lsf	{

	var $prefixId = EXT_KEY;
	var $extKey = 'dmc_browse_soap';
	var $types = array();
	var $listTypes = '';
	var $hostname = '';
	var $requestPath = '';
	var $rpcMode = 'rpc';
	var $xmlRequest = null;
	var	$semBeg2 = 3; // semesterbeginn für den 2. Studienabschnitt
	var	$tableWidth = 740;
	var $cObj;
	var $lsfDebug = FALSE;
	var $moduleTitleClass = 'moduleTitle';
	var $moduleValueClass = 'moduleValue';
	var $modulFeldnamen = array(
			'de' => array(
						'ModulText' =>  						array(10,'Titel'),
						'ModulCode' =>  						array(20,'Modulcode'),
						'ZielDesModuls' =>  				array(30,'Gesamtziel'),
						'Inhalt' =>  								array(40,'Inhalt'),
						'Keywords' => 							array(50,'Schlüsselworte'),
						'NutzbarkeitDesModuls' => 	array(60,'Nutzbar für andere Studiengänge'),
						'Voraussetzungen' => 				array(61,'Voraussetzungen'),
						'Verantw' => 								array(65,'Modulverantwortlicher'),		
						'Lehrform' => 							array(100,'Lehr-, Lernform'),
						'Lernziel' => 							array(120,'Lernziele'),
						'PruefungsDetail' => 				array(130,'Prüfungsleistung insgesamt'),
						'lehrsprache' => 						array(150,'Unterrichtsprache'),
						'Credits' => 								array(160,'ECTS-Credits'),
						'selbstzeit' => 						array(170,'Zeit für Selbststudium'),
						'kontaktzeit' => 						array(180,'Zeit für Kontaktstudium'),
						'PruefungsVorbereitung' => 	array(190,'Zeit für Prüfungsvorbereitung'),
						'Literaturhinweise' => 			array(200,'Literaturhinweise'),
										
//						'pversion' => 							array(180,'Prüfungsversion'),
					
// Teilleistungen:					
						'deTxt' => 									array(500,'Titel'),
						'VerantwTl' => 							array(510,'Modulverantwortlicher'),		
						'Ziel' =>  									array(520,'Ziel der Teilleistung'),
						'bonus' => 									array(530,'ECTS-Credits'),
						'pord.selbstzeit' => 				array(540,'Zeit für Selbststudium'),
						'pord.praesenzzeit' => 			array(550,'Zeit für Kontaktstudium'),
						'PruefungsformUndDauer' => 	array(560,'Prüfungsform und Dauer'),
						'xxx' => 										array(999,'Unterrichtsprache'),
						'xxx' => 										array(999,'Unterrichtsprache'),
						'xxx' => 										array(999,'Unterrichtsprache'),
						),
			'en' => array(
						'ModulText' =>  						array(10,'Title'),
						'ModulCode' =>  						array(20,'Modulecode'),
						'Target (of the Modul/Exam)' =>  array(30,'Target'),
						'Content' =>  								array(40,'Content'),
						'Keywords' => 							array(50,'Keywords'),
						'NutzbarkeitDesModuls' => 	array(60,'Relevance for other study programs'),		
						'ConditionsForParticipation' => 	array(70,'Conditions for participation'),
						'Verantw' => 								array(65,'Moduleowner'),	
						
						'Lehrform' => 							array(100,'Type of instruction'),
						'Lernziel' => 							array(120,'Aims, learning outcomes'),
						'TestCondition' => 					array(130,'A test condition'),
						'lehrsprache' => 						array(150,'Language of instruction'),
						'Credits' => 								array(160,'ECTS points'),
						'selbstzeit' => 						array(170,'Time for self-study'),
						'kontaktzeit' => 						array(180,'Contact time for studying'),
						'ExamPreparation' => 				array(190,'Time for Exam Preparation'),
						'Literature' => 						array(200,'Literature'),
					
					
	// Teilleistungen
						'Lehrform' => 							array(100,'Type of instruction'),
						'Ziel' => 									array(120,'Aims, learning outcomes'),
						'ExaminationFormAndDuration' => 	array(130,'Examination form and duration'),
						'FormOfInstruction' => 			array(135,'Form of instruction'),
						'Target(of the Modul/Exam)' =>  array(136,'Target'),
						'enTxt' => 									array(140,'Title'),
						'lehrsprache' => 						array(150,'Language of instruction'),
						'VerantwTl' => 							array(165,'Moduleowner'),		
						'pord.selbstzeit' => 				array(200,'Time for self-study'),
						'pord.praesenzzeit' => 			array(210,'Contact time for studying'),
						'bonus' => 									array(220,'Workload'),
						'xxx' => 										array(199,'Unterrichtsprache'),
						'xxx' => 										array(199,'Unterrichtsprache'),
						),
	);
	
	static $einrichtungen = array(
		'AAA' => 'Akademisches Auslandsamt',
		'AN' => 'Fakultät Angewandte Naturwissenschaften',
		'DZ' => 'Didaktikzentrum',
		'BW' => 'Fakultät Betriebswirtschaft',
		'FZ' => 'Fakultät Fahrzeugtechnik',
		'GL' => 'Fakultät Grundlagen',
		'GS' => 'Fakultät Graduate School',
		'GU' => 'Fakultät Gebäude Energie Umwelt',
		'IT' => 'Fakultät Informationstechnik',
		'MB' => 'Fakultät Maschinenbau',
		'ME' => 'Fakultät Mechatronik und Elektrotechnik',
		'SP' => 'Fakultät Soziale Arbeit Gesundheit und Pflege',
		'WI' => 'Fakultät Wirtschaftsingenieurwesen',
		'IFS' => 'Institut für Fremdsprachen',
	);
	
	static $einrichtungenEn = array(
		'AAA' => 'International Office',
		'AN' => 'Faculty Natural Sciences',
		'BW' => 'Faculty Management',
		'FZ' => 'Faculty Automotive Engineering',
		'GL' => 'Faculty Basic Sciences',
		'GS' => 'Faculty Graduate School',
		'GU' => 'Faculty Building Services, Energy, Environment',
		'IT' => 'Faculty Information Technology',
		'MB' => 'Faculty Mechanical Engineering',
		'ME' => 'Faculty Mechatronics and Electrical Engineering',
		'SP' => 'Faculty Social Work, Health Care and Nursing Sciences',
		'WI' => 'Faculty Engineering Management',
		'IFS' => 'Institute of Foreign Languages',
	);
	
	static $standorteEn = array(
			'SM' => 'Campus City Centre',
			'HZE' => 'Hilltop Campus',
			'GP' => 'Göppingen Campus',
	);
	
	static $einrichtungenStandorte = array(
		'AAA' => 'SM',
		'AN' => 'SM',
		'BW' => 'HZE',
		'FZ' => 'SM',
		'GL' => 'SM',
		'GS' => 'HZE',
		'GU' => 'SM',
		'IT' => 'HZE',
		'MB' => 'SM',
		'ME' => 'GP',
		'SP' => 'HZE',
		'WI' => 'GP',
		'IFS' => 'HZE',
	);
	
	function __construct($cObj='') {
		
		global $TYPO3_CONF_VARS;
		$this->hostname = 'http://www3.hs-esslingen.de';
		$this->requestPath = '/qislsf/services/dbinterface';
		$this->rpcMethod = 'getDataXML';
		$this->cObj = $cObj;
	}

	function erzeugeModulListe($studiengang,$abschlussLsf,$vertiefungLsf,$abschluss,$version,$semVertiefung,
														 $studiengangBezeichung,$fakultaetsBezeichung,$kuerzelFakultaet,$linksDeaktivieren=FALSE) {
		$get = t3lib_div::_GET();#
		if (isset($get['L'])) {
			if ($get['L']==1) {
				$lang = 'en';
			} else {
				$lang = 'de';
			}
		} else {
			$lang = 'de';
		}
		if (isset($get['pordId'])) {
			$pordId = $get['pordId'];
			$this->modulEinzeln($pordId,$modulDatenEinzeln,$modulDatenTlEinzeln,$lang);
			$modulEinzeln = $this->gibModulaDatenAus($modulDatenEinzeln,$lang);
			if (!empty($modulDatenTlEinzeln)) {
				if ($lang=='de') {
					$teilleistungenEinzeln = '<h1>Teilleistungen</h1>';
				} else {
					$teilleistungenEinzeln = '<h1>Submodules</h1>';
				}
				foreach ($modulDatenTlEinzeln as $daten) {
					$teilleistungenEinzeln .= $this->gibModulaDatenAus($daten,$lang);
				}
			}
      $pdfLink = '';
/*
PDF funktioniert momentan nicht
      $pdfLink = $this->gibLinkModulPdf($kuerzelFakultaet,$abschlussLsf, $studiengang, $version, $pordId, $lang);
*/
if ($GLOBALS['TSFE']->fe_user->user['username']=='mmirsch') {
  $pdfLink = $this->gibLinkModulPdf($kuerzelFakultaet,$abschlussLsf, $studiengang, $version, $pordId, $lang);
}
			return $modulEinzeln . $teilleistungenEinzeln . $pdfLink;	
		} else {
//			$module = $this->modulListe($studiengang,$version,$abschlussLsf);
//			$aktuelleVersion = $this->gibMaxVerson($module);
//			$aktuelleModule = $this->gibNeusteModule($module);
			$aktuelleModule = $this->modulListe($studiengang,$version,$abschlussLsf,$lang);
			if (empty($aktuelleModule)) {
				$out = '<h2 class="error">Für den Studiengang "' . $studiengangBezeichung .
							 '" gibt es aktuell keine Module mit der PO-Version ' . 
							 $version . '!</h2>';
			} else {
				$inksModulHandbuch = array();
				if (!empty($vertiefungLsf)) {
					foreach ($aktuelleModule as $uid=>$modulDaten) {
						if (!empty($modulDaten['ktxtvert']) && $modulDaten['ktxtvert']!=$vertiefungLsf) {
							unset($aktuelleModule[$uid]);
						}
					}
					if ($lang=='en') {
						$title = 'Modulehandbook';
					} else {
						$title = 'Modulhandbuch';
					}
					$linksModulHandbuch[] = $this->gibLinkModulHandbuch($title, $kuerzelFakultaet, $abschlussLsf, $studiengang, $version, $lang, '', 'allgemein' , FALSE);
					$title .= ' - Vertiefung ' . $this->gibVertiefungsBezeichnung($vertiefungLsf, $lang);
					$linksModulHandbuch[] = $this->gibLinkModulHandbuch($title, $kuerzelFakultaet, $abschlussLsf, $studiengang, $version, $lang, $vertiefungLsf, '' , FALSE);
				} else {
					$vertiefungen = array();
					foreach ($aktuelleModule as $uid=>$modulDaten) {
						if (!empty($modulDaten['ktxtvert']) && !in_array($modulDaten['ktxtvert'],$vertiefungen)) {
							$vertiefungen[] = $modulDaten['ktxtvert'];
						}
					}
					if ($lang=='en') {
						$title = 'Modulehandbook';
					} else {
						$title = 'Modulhandbuch für alle Vertiefungen';
					}
					$linksModulHandbuch[] = $this->gibLinkModulHandbuch($title, $kuerzelFakultaet, $abschlussLsf, $studiengang, $version, $lang, '', 'allgemein' , FALSE);
					$i = 1;
					foreach ($vertiefungen as $vertiefung) {
						$cssVertiefung = ' vertiefung_' . $i;
						$i++;
						$titleVertiefung = 'Modulhandbuch - Vertiefung ' . $this->gibVertiefungsBezeichnung($vertiefung, $lang);
						$linksModulHandbuch[] = $this->gibLinkModulHandbuch($titleVertiefung, $kuerzelFakultaet, $abschlussLsf, $studiengang, $version, $lang, $vertiefung, $cssVertiefung , FALSE);
					}
					
				}

				$out = $this->erstelleModulListe($aktuelleModule,$abschluss,$semVertiefung,$studiengangBezeichung,$kuerzelFakultaet,$lang,$linksDeaktivieren);
/*
PDF funktioniert momentan nicht

        $out .= '<div class="module_handbooks vertiefungen">
								';
				foreach ($linksModulHandbuch as $button) {
					$out .= $button;
				}
				$out .= '</div>';
*/
			}
				
			return $out;		
		}
	}
	
	function erzeugeModulHandbuch($studiengang,$abschlussLsf,$vertiefungLsf,$abschluss,$version,$semVertiefung,$studiengangBezeichung,$fakultaetsBezeichung) {
		$get = t3lib_div::_GET();#
		if (isset($get['L'])) {
			if ($get['L']==1) {
				$lang = 'en';
			} else {
				$lang = 'de';
			}
		} else {
			$lang = 'de';
		}
		$aktuelleModule = $this->modulListe($studiengang,$version,$abschlussLsf,$lang);
		if (empty($aktuelleModule)) {
			$out = '<h2 class="error">Für den Studiengang "' . $studiengangBezeichung .
						 '" gibt es aktuell keine Module mit der PO-Version ' . 
						 $version . '!</h2>';
		} else {
			if (!empty($vertiefungLsf)) {
				foreach ($aktuelleModule as $uid=>$modulDaten) {
					if (!empty($modulDaten['ktxtvert']) && $modulDaten['ktxtvert']!=$vertiefungLsf) {
						unset($aktuelleModule[$uid]);
					}
				}
			}
			if ($lang=='en') {
				$title = 'Modulehandbook';
			} else {
				$title = 'Modulhandbuch';
			}
			$out = $this->gibLinkModulHandbuch($title, $fakultaetsBezeichung, $abschlussLsf, $studiengang, $version, $lang);
		}
		return $out;		
	}
	
	function erzeugeModulTabelle($studiengang,$abschlussLsf,$vertiefungLsf,$abschluss,$version,$semVertiefung,$studiengangBezeichung,$fakultaetsBezeichung,$kuerzelFakultaet,$maxCol=4,$linksDeaktivieren=FALSE) {
		$get = t3lib_div::_GET();#
		if (isset($get['L'])) {
			if ($get['L']==1) {
				$lang = 'en';
			} else {
				$lang = 'de';
			}
		} else {
			$lang = 'de';
		}
		if (isset($get['pordId'])) {
			$pordId = $get['pordId'];
			$this->modulEinzeln($pordId,$modulDatenEinzeln,$modulDatenTlEinzeln,$lang);
			$modulEinzeln = $this->gibModulaDatenAus($modulDatenEinzeln,$lang);
			if (!empty($modulDatenTlEinzeln)) {
				if ($lang=='de') {
					$teilleistungenEinzeln = '<h1>Teilleistungen</h1>';
				} else {
					$teilleistungenEinzeln = '<h1>Submodules</h1>';
				}
				foreach ($modulDatenTlEinzeln as $daten) {
					$teilleistungenEinzeln .= $this->gibModulaDatenAus($daten,$lang);
				}
			}
      $pdfLink = '';
/*
PDF funktioniert momentan nicht
      $pdfLink = $this->gibLinkModulPdf($kuerzelFakultaet,$abschlussLsf, $studiengang, $version, $pordId, $lang);
*/

			return $modulEinzeln . $teilleistungenEinzeln . $pdfLink;
		} else {
			$aktuelleModule = $this->modulListe($studiengang,$version,$abschlussLsf,$lang);
			if (empty($aktuelleModule)) {
				$out = '<h2 class="error">Für den Studiengang "' . $studiengangBezeichung .
							 '" gibt es aktuell keine Module mit der PO-Version ' . 
							 $version . '!</h2>';
			} else {
				$vertiefungen = array();
				if (!empty($vertiefungLsf)) {
					$vertiefungen[] = $vertiefungLsf;
					foreach ($aktuelleModule as $uid=>$modulDaten) {
						if (!empty($modulDaten['ktxtvert']) && $modulDaten['ktxtvert']!=$vertiefungLsf) {
							unset($aktuelleModule[$uid]);
						}
					}
					if ($lang=='en') {
						$title = 'Modulehandbook';
					} else {
						$title = 'Modulhandbuch für alle Vertiefungen';
					}
					$linksModulHandbuch[] = $this->gibLinkModulHandbuch($title, $kuerzelFakultaet, $abschlussLsf, $studiengang, $version, $lang);
				} else {
					foreach ($aktuelleModule as $uid=>$modulDaten) {
						if (!empty($modulDaten['ktxtvert']) && !in_array($modulDaten['ktxtvert'],$vertiefungen)) {
							$vertiefungen[] = $modulDaten['ktxtvert'];
						}
					}
					if (empty($vertiefungen)) {
						if ($lang=='en') {
							$title = 'Modulehandbook';
						} else {
							$title = 'Modulhandbuch';
						}
					} else {
						if ($lang=='en') {
							$title = 'Modulehandbook';
						} else {
							$title = 'Modulhandbuch<br>für alle Vertiefungen';
						}
					}
					$linksModulHandbuch[] = $this->gibLinkModulHandbuch($title, $kuerzelFakultaet, $abschlussLsf, $studiengang, $version, $lang);
				}
				$out = $this->erstelleModulTabelle($aktuelleModule,$abschluss,$semVertiefung,$studiengangBezeichung,$kuerzelFakultaet,$lang,$maxCol,$linksDeaktivieren);
				
				if (!empty($vertiefungen)) {
					$i = 1;
					foreach ($vertiefungen as $vertiefung) {
						if ($lang=='en') {
							$title = 'Modulehandbook<br>';
						} else {
							$title = 'Modulhandbuch<br> ';
						}
						$cssVertiefung = ' vertiefung_' . $i;
						$i++;
						$titleVertiefung = $title . $this->gibVertiefungsBezeichnung($vertiefung, $lang);
						$linksModulHandbuch[] = $this->gibLinkModulHandbuch($titleVertiefung, $kuerzelFakultaet, $abschlussLsf, $studiengang, $version, $lang, $vertiefung, $cssVertiefung);
					}
				}
/*
 * PDFs funktionieren momentan nicht
 */  $linksModulHandbuch = array();

				if (is_array($linksModulHandbuch) && count($linksModulHandbuch)>0) {
					if (count($linksModulHandbuch)==1) {
						$out .= '<table class="module_handbooks tab50 zentriert"><tbody>';
						$out .= '<tr class="z1"><td class="td100">';
						$out .= $linksModulHandbuch[0];
						$out .= '</td></tr>';
					}	else if (count($linksModulHandbuch)==2) {
						$out .= '<table class="module_handbooks tab75 zentriert"><tbody>';
						$out .= '<tr class="z1">';
						$out .= '<td class="td50">';
						$out .= $linksModulHandbuch[0];
						$out .= '</td><td class="td50">';
						$out .= $linksModulHandbuch[1];
						$out .= '</td></tr>';
					}	else if (count($linksModulHandbuch)==3) {
						$out .= '<table class="module_handbooks tab100 zentriert"><tbody>';
						$out .= '<tr class="z1">';
						$out .= '<td class="td33">';
						$out .= $linksModulHandbuch[0];
						$out .= '</td><td class="td33">';
						$out .= $linksModulHandbuch[1];
						$out .= '</td><td class="td33">';
						$out .= $linksModulHandbuch[2];
						$out .= '</td></tr>';
					}	else if (count($linksModulHandbuch)==4) {
						$out .= '<table class="module_handbooks tab75 zentriert"><tbody>';
						$out .= '<tr class="z1">';
						$out .= '<td class="td50">';
						$out .= $linksModulHandbuch[0];
						$out .= '</td><td class="td50">';
						$out .= $linksModulHandbuch[1];
						$out .= '</td></tr>';
						$out .= '<tr class="z1">';
						$out .= '<td class="td50">';
						$out .= $linksModulHandbuch[2];
						$out .= '</td><td class="td50">';
						$out .= $linksModulHandbuch[3];
						$out .= '</td></tr>';
					}
					$out .= '</tbody></table>';
				}
			}
			return $out;
		}
	}

	function erzeugeModulTabelleMitVertiefungen($studiengang,$abschlussLsf,$vertiefungLsf,$abschluss,$version,$semVertiefung,$studiengangBezeichung,$fakultaetsBezeichung,$kuerzelFakultaet,$maxCol=4) {
		$get = t3lib_div::_GET();#
		if (isset($get['L'])) {
			if ($get['L']==1) {
				$lang = 'en';
			} else {
				$lang = 'de';
			}
		} else {
			$lang = 'de';
		}
		if (isset($get['pordId'])) {
			$pordId = $get['pordId'];
			$this->modulEinzeln($pordId,$modulDatenEinzeln,$modulDatenTlEinzeln,$lang);
			$modulEinzeln = $this->gibModulaDatenAus($modulDatenEinzeln,$lang);
			if (!empty($modulDatenTlEinzeln)) {
				if ($lang=='de') {
					$teilleistungenEinzeln = '<h1>Teilleistungen</h1>';
				} else {
					$teilleistungenEinzeln = '<h1>Submodules</h1>';
				}
				foreach ($modulDatenTlEinzeln as $daten) {
					$teilleistungenEinzeln .= $this->gibModulaDatenAus($daten,$lang);
				}
			}
      $pdfLink = '';
      /*
      PDF funktioniert momentan nicht
            $pdfLink = $this->gibLinkModulPdf($kuerzelFakultaet,$abschlussLsf, $studiengang, $version, $pordId, $lang);
      */

			return $modulEinzeln . $teilleistungenEinzeln . $pdfLink;
		} else {
			$aktuelleModule = $this->modulListe($studiengang,$version,$abschlussLsf,$lang);
			if (empty($aktuelleModule)) {
				$out = '<h2 class="error">Für den Studiengang "' . $studiengangBezeichung .
							 '" gibt es aktuell keine Module mit der PO-Version ' . 
							 $version . '!</h2>';
			} else {
				$vertiefungen = array();
				if (!empty($vertiefungLsf)) {
					$vertiefungen[] = $vertiefungLsf;
					foreach ($aktuelleModule as $uid=>$modulDaten) {
						if (!empty($modulDaten['ktxtvert']) && $modulDaten['ktxtvert']!=$vertiefungLsf) {
							unset($aktuelleModule[$uid]);
						}
					}
					if ($lang=='en') {
						$title = 'Modulehandbook';
					} else {
						$title = 'Modulhandbuch für alle Vertiefungen';
					}
					$linksModulHandbuch[] = $this->gibLinkModulHandbuch($title, $kuerzelFakultaet, $abschlussLsf, $studiengang, $version, $lang);
				} else {
					foreach ($aktuelleModule as $uid=>$modulDaten) {
						if (!empty($modulDaten['ktxtvert']) && !in_array($modulDaten['ktxtvert'],$vertiefungen)) {
							$vertiefungen[] = $modulDaten['ktxtvert'];
						}
					}
					if (empty($vertiefungen)) {
						if ($lang=='en') {
							$title = 'Modulehandbook';
						} else {
							$title = 'Modulhandbuch';
						}
					} else {
						if ($lang=='en') {
							$title = 'Modulehandbook';
						} else {
							$title = 'Modulhandbuch<br>für alle Vertiefungen';
						}
					}
					$linksModulHandbuch[] = $this->gibLinkModulHandbuch($title, $kuerzelFakultaet, $abschlussLsf, $studiengang, $version, $lang);
				}
				$out = $this->erstelleModulTabelleMitVertiefungen($aktuelleModule,$abschluss,$semVertiefung,$studiengangBezeichung,$fakultaetsBezeichung,$lang,$maxCol);
				
				if (!empty($vertiefungen)) {
					$i = 1;
					foreach ($vertiefungen as $vertiefung) {
						if ($lang=='en') {
							$title = 'Modulehandbook<br>';
						} else {
							$title = 'Modulhandbuch<br> ';
						}
						$cssVertiefung = ' vertiefung_' . $i;
						$i++;
						$titleVertiefung = $title . $this->gibVertiefungsBezeichnung($vertiefung, $lang);
						$linksModulHandbuch[] = $this->gibLinkModulHandbuch($titleVertiefung, $kuerzelFakultaet, $abschlussLsf, $studiengang, $version, $lang, $vertiefung, $cssVertiefung);
					}
				}
				if (is_array($linksModulHandbuch) && count($linksModulHandbuch)>0) {
					if (count($linksModulHandbuch)==1) {
						$out .= '<table class="module_handbooks vertiefungen tab50 zentriert"><tbody>';
						$out .= '<tr class="z1"><td class="td100">';
						$out .= $linksModulHandbuch[0];
						$out .= '</td></tr>';
					}	else if (count($linksModulHandbuch)==2) {
						$out .= '<table class="module_handbooks vertiefungen tab75 zentriert"><tbody>';
						$out .= '<tr class="z1">';
						$out .= '<td class="td50">';
						$out .= $linksModulHandbuch[0];
						$out .= '</td><td class="td50">';
						$out .= $linksModulHandbuch[1];
						$out .= '</td></tr>';
					}	else if (count($linksModulHandbuch)==3) {
						$out .= '<table class="module_handbooks vertiefungen tab100 zentriert"><tbody>';
						$out .= '<tr class="z1">';
						$out .= '<td class="td33">';
						$out .= $linksModulHandbuch[0];
						$out .= '</td><td class="td33">';
						$out .= $linksModulHandbuch[1];
						$out .= '</td><td class="td33">';
						$out .= $linksModulHandbuch[2];
						$out .= '</td></tr>';
					}	else if (count($linksModulHandbuch)==4) {
						$out .= '<table class="module_handbooks vertiefungen tab75 zentriert"><tbody>';
						$out .= '<tr class="z1">';
						$out .= '<td class="td50">';
						$out .= $linksModulHandbuch[0];
						$out .= '</td><td class="td50">';
						$out .= $linksModulHandbuch[1];
						$out .= '</td></tr>';
						$out .= '<tr class="z1">';
						$out .= '<td class="td50">';
						$out .= $linksModulHandbuch[2];
						$out .= '</td><td class="td50">';
						$out .= $linksModulHandbuch[3];
						$out .= '</td></tr>';
					}		
					$out .= '</tbody></table>';
				}
			} 
			return $out;		
		}
	}
	
	function veranstaltungenEnglisch() {
		$getDataRequest = '<SOAPDataService>
		<general>
		<object>enVeranstaltung</object>
		</general>
		</SOAPDataService>';
		$erg = $this->soapRequest($getDataRequest);
		if (isset($erg['Modul']['enVeranstaltung'])) {
			return $this->giblisteEnglischeVeranstaltungenAus($erg['Modul']['enVeranstaltung']);
		} else {
			return '';
		}
	}
	
	function moduleEnglisch() {
		$get = t3lib_div::_GET();
		if (isset($get['modulpdf'])) {
			$args = unserialize(base64_decode($get['modulpdf']));
			return $this->gibModulPdf($args);
		} elseif (isset($get['pordId'])) {
			$pordId = $get['pordId'];
							
			$lang = 'en';
			$this->modulEinzeln($pordId,$modulDatenEinzeln,$modulDatenTlEinzeln,$lang);
			$modulEinzeln = $this->gibModulaDatenAus($modulDatenEinzeln,$lang);
			if (!empty($modulDatenTlEinzeln)) {
				if ($lang=='de') {
					$teilleistungenEinzeln = '<h1>Teilleistungen</h1>';
				} else {
					$teilleistungenEinzeln = '<h1>Submodules</h1>';
				}
				foreach ($modulDatenTlEinzeln as $daten) {
					$teilleistungenEinzeln .= $this->gibModulaDatenAus($daten,$lang);
				}
			}
			//$pdfLink = $this->gibLinkModulPdf($kuerzelFakultaet,$abschlussLsf, $studiengang, $version, $pordId, $lang, 'enModulDescription');
			$pdfLink = '';
			return $modulEinzeln . $teilleistungenEinzeln . $pdfLink;
		}
		
		$englischeModulbeschreibungen = array();
		$queryFakuStdg = '
			SELECT * FROM tx_hetools_module_studiengaenge
			where deleted=0 AND hidden=0
			ORDER BY fakultaet,lsf_stdg 
		';
		$standortListe = array();
		$abfrageFakuStdg = $GLOBALS['TYPO3_DB']->sql_query($queryFakuStdg);
		while ($datenFakuStdg = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageFakuStdg)) {
			$fakultaet = self::$einrichtungenEn[$datenFakuStdg['fakultaet']];
			$standort = self::$standorteEn[self::$einrichtungenStandorte[$datenFakuStdg['fakultaet']]];
			if (!isset($standortListe[$standort])) {
					$standortListe[$standort] = array();
				}
			if (!isset($standortListe[$standort][$fakultaet])) {
				$standortListe[$standort][$fakultaet] = array();
			}
			$standortListe[$standort][$fakultaet][] = $datenFakuStdg;
		}
		$standortListe[self::$standorteEn['SM']][self::$einrichtungenEn['AAA']] = array('AAA' => array('lsf_abs'=>97,'lsf_stdg'=>000));
		foreach ($standortListe as $standort=>$fakultaetsListe) {
			foreach ($fakultaetsListe as $fakultaet=>$studiengangListe) {
				foreach ($studiengangListe as $studiengangDaten) {
					if ($fakultaet==self::$einrichtungenEn['AAA']) {
						$lsfObject = 'enModulListAAA';
						$studiengang = '';
					} else {
						$lsfObject = 'enModulList';
						$studiengang = $studiengangDaten['title_en'];
					}
					$getDataRequest = '<SOAPDataService>
					<general>
					<object>' . $lsfObject . '</object>
					</general>
					<condition>
					<abschluss>' . $studiengangDaten['lsf_abs'] . '</abschluss>
					<studiengang>' . $studiengangDaten['lsf_stdg'] . '</studiengang>
					<version></version>
					</condition>
					</SOAPDataService>';
					$erg = $this->soapRequest($getDataRequest);
					if (isset($erg['Modul'][$lsfObject])) {
						if (isset($erg['Modul'][$lsfObject][0])) {
							$stdgModuleList = $erg['Modul'][$lsfObject];
						} else {
							$stdgModuleList = array('0'=>$erg['Modul'][$lsfObject]);
						}
						foreach ($stdgModuleList as $modulDaten) {
							if ($modulDaten['lehrsprache']=='E' || $modulDaten['lehrsprache']=='D+E') {
								if (!isset($englischeModulbeschreibungen[$standort])) {
									$englischeModulbeschreibungen[$standort] = array();
								}
								if (!isset($englischeModulbeschreibungen[$standort][$fakultaet])) {
									$englischeModulbeschreibungen[$standort][$fakultaet] = array();
								}
								$hash = md5($modulDaten['ModulText'] . $studiengang);
								if (!isset($englischeModulbeschreibungen[$standort][$fakultaet][$hash])) {
									$englischeModulbeschreibungen[$standort][$fakultaet][$hash] = $this->gibLsfModulDatenFuerModulTabelle($modulDaten,$studiengang);
								}
							}
						}
					}
				}
			}
		}
		return $this->gibEnglischeModultabelleAus($englischeModulbeschreibungen);
	}
	
	function teilleistungenEnglisch() {
		$get = t3lib_div::_GET();
		if (isset($get['modulpdf'])) {
			$args = unserialize(base64_decode($get['modulpdf']));
			return $this->gibModulPdf($args);
		} elseif (isset($get['pordIdTl'])) {
			$pordId = $get['pordId'];
			$pordIdTl = $get['pordIdTl'];
			$lang = 'en';
			$teilleistungEinzeln = array();
			$this->teilleistungEinzelnEn($pordId,$pordIdTl,$teilleistungEinzeln);
			$modulEinzeln = $this->gibModulaDatenAus($teilleistungEinzeln,$lang);
			$pdfLink = '';
			return $modulEinzeln . $pdfLink;
		} elseif (isset($get['pordId'])) {
			$pordId = $get['pordId'];
			$lang = 'en';
			$this->modulEinzeln($pordId,$modulDatenEinzeln,$modulDatenTlEinzeln,$lang);
			$modulEinzeln = $this->gibModulaDatenAus($modulDatenEinzeln,$lang);
			if (!empty($modulDatenTlEinzeln)) {
				if ($lang=='de') {
					$teilleistungenEinzeln = '<h1>Teilleistungen</h1>';
				} else {
					$teilleistungenEinzeln = '<h1>Submodules</h1>';
				}
				foreach ($modulDatenTlEinzeln as $daten) {
					$teilleistungenEinzeln .= $this->gibModulaDatenAus($daten,$lang);
				}
			}
			//$pdfLink = $this->gibLinkModulPdf($kuerzelFakultaet,$abschlussLsf, $studiengang, $version, $pordId, $lang, 'enModulDescription');
			$pdfLink = '';
			return $modulEinzeln . $teilleistungenEinzeln . $pdfLink;
		}
		
		$englischeModulbeschreibungen = array();
		$queryFakuStdg = '
			SELECT * FROM tx_hetools_module_studiengaenge
			where deleted=0 AND hidden=0
			ORDER BY fakultaet,lsf_stdg 
		';
		$standortListe = array();
		$abfrageFakuStdg = $GLOBALS['TYPO3_DB']->sql_query($queryFakuStdg);
		while ($datenFakuStdg = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageFakuStdg)) {
			$fakultaet = self::$einrichtungenEn[$datenFakuStdg['fakultaet']];
			$standort = self::$standorteEn[self::$einrichtungenStandorte[$datenFakuStdg['fakultaet']]];
			if (!isset($standortListe[$standort])) {
					$standortListe[$standort] = array();
				}
			if (!isset($standortListe[$standort][$fakultaet])) {
				$standortListe[$standort][$fakultaet] = array();
			}
			$standortListe[$standort][$fakultaet][] = $datenFakuStdg;
		}
		$standortListe[self::$standorteEn['SM']][self::$einrichtungenEn['AAA']] = array('AAA' => array('lsf_abs'=>97,'lsf_stdg'=>000));
		foreach ($standortListe as $standort=>$fakultaetsListe) {
			foreach ($fakultaetsListe as $fakultaet=>$studiengangListe) {
				foreach ($studiengangListe as $studiengangDaten) {
					if ($fakultaet==self::$einrichtungenEn['AAA']) {
						$lsfObject = 'enModulListAAA';
						$studiengang = '';
					} else {
						$lsfObject = 'ModulList';
						$studiengang = $studiengangDaten['title_en'];
					}
					$getDataRequest = '<SOAPDataService>
					<general>
					<object>' . $lsfObject . '</object>
					</general>
					<condition>
					<abschluss>' . $studiengangDaten['lsf_abs'] . '</abschluss>
					<studiengang>' . $studiengangDaten['lsf_stdg'] . '</studiengang>
					<version></version>
					</condition>
					</SOAPDataService>';
					$erg = $this->soapRequest($getDataRequest);
					if (isset($erg['Modul'][$lsfObject])) {
						if (isset($erg['Modul'][$lsfObject][0])) {
							$stdgModuleList = $erg['Modul'][$lsfObject];
						} else {
							$stdgModuleList = array('0'=>$erg['Modul'][$lsfObject]);
						}
						foreach ($stdgModuleList as $modulDaten) {
							$lsfObject = 'enModulTeilLeist';
							$getDataRequestTl = '<SOAPDataService>
							<general><object>' . $lsfObject . '</object></general>
							<condition><pordID>' . $modulDaten['pordID'] . '</pordID></condition>
							</SOAPDataService>';
							$erg = $this->soapRequest($getDataRequestTl);
							if (isset($erg['Modul'][$lsfObject])) {
								if (isset($erg['Modul'][$lsfObject][0])) {
									$tlList = $erg['Modul'][$lsfObject];
								} else {
									$tlList = array('0'=>$erg['Modul'][$lsfObject]);
								}
								foreach ($tlList as $tlDaten) {
									if ($tlDaten['lehrsprache']=='E' || $tlDaten['lehrsprache']=='D+E') {
										$tlDaten['pordID'] = $modulDaten['pordID'];
										if (!isset($englischeModulbeschreibungen[$standort])) {
											$englischeModulbeschreibungen[$standort] = array();
										}
										if (!isset($englischeModulbeschreibungen[$standort][$fakultaet])) {
											$englischeModulbeschreibungen[$standort][$fakultaet] = array();
										}
										$pordTeilLeist = $tlDaten['pordTeilLeist'];
										if (!isset($englischeModulbeschreibungen[$standort][$fakultaet][$pordTeilLeist])) {
											$englischeModulbeschreibungen[$standort][$fakultaet][$pordTeilLeist] = $this->gibLsfTlDatenFuerModulTabelle($tlDaten,$studiengang);
										}
//t3lib_utility_Debug::debug($englischeModulbeschreibungen);return;										
									}
								}
							} else {
								if ($modulDaten['lehrsprache']=='E' || $modulDaten['lehrsprache']=='D+E') {
									if (!isset($englischeModulbeschreibungen[$standort])) {
										$englischeModulbeschreibungen[$standort] = array();
									}
									if (!isset($englischeModulbeschreibungen[$standort][$fakultaet])) {
										$englischeModulbeschreibungen[$standort][$fakultaet] = array();
									}
									$hash = md5($modulDaten['ModulText'] . $studiengang);
									if (!isset($englischeModulbeschreibungen[$standort][$fakultaet][$hash])) {
										$englischeModulbeschreibungen[$standort][$fakultaet][$hash] = $this->gibLsfModulDatenFuerModulTabelle($modulDaten,$studiengang);
									}
								}
							}
						}						
					}
				}
			}
		}
		return $this->gibEnglischeModultabelleAus($englischeModulbeschreibungen);
	}
	
	function soapTest() {
		$post = t3lib_div::_POST();
		if (!empty($post['soapRequestData'])) {
			$getDataRequest = $post['soapRequestData'];
			$erg = $this->soapRequest($getDataRequest);
			$out .= '<h1>Ergebnis des Aufrufs:</h1><pre>' . print_r($erg,true) . '</pre>';
		} else {
			$getDataRequest = '<SOAPDataService>
<general><object>modulList</object></general>
<condition>
<abschluss>84</abschluss>
<studiengang>TIB</studiengang>
<version>2</version>
</condition>
</SOAPDataService>
';
		}
		$id = $GLOBALS['TSFE']->id;
		$out .= '<h1>SOAP-Schnittstelle testen (nur hemp und mmirsch)</h1>
		<form class="soaptest" action="index.php?id=' .  $id . '" method="POST">
		<div class="row">
		<label for="soapRequestData">SoapRequestData:</label>
		</div>
		<div class="row">
		<textarea rows="20" cols="80" name="soapRequestData">' . $getDataRequest . '</textarea>
		</div>
		<div class="row">
		<input type="submit" name="absenden" value="Befehl absenden">
		</div>
		</form>';
		return $out;
	}
	
	function gibVertiefungenSelect($studiengang) {
		$vertiefungen = $this->gibVertiefungen($studiengang);
		if (count($vertiefungen)==0) {
			return '<span style="width: 200px;" id="vertiefung_lsf">Keine Vertiefung</span>';
		}
		if (count($vertiefungen)==1) {
			$vertiefungenTabelle =	'<select style="width: 200px;" id="vertiefung_lsf" name="vertiefung_lsf">';
			$vertiefung = $vertiefungen[0];
			$titel = $vertiefung['vertiefung'];
			if (!empty($vertiefung['kuerzel'])) {
				$titel .= ' (' . $vertiefung['kuerzel'] . ')';
			}
			if (!empty($vertiefung['version'])) {
				$titel .= ' PO' . $vertiefung['version'];
			}
			$vertiefungenTabelle .= '<option ' . $selected . ' value="' . $vertiefung['uid'] . '">' . $titel . '</option>';
			$vertiefungenTabelle .= '</select>';
			return $vertiefungenTabelle;
		}
		$vertiefungenTabelle =	'<select style="width: 150px;" id="vertiefung_lsf" name="vertiefung_lsf">
														<option value="">Bitte eine Vertiefung auswählen</option>
			';
		foreach ($vertiefungen as $vertiefung) {
			if ($vertiefung['uid']==$vertiefungSelected) {
				$selected = ' selected="selected" ';
			} else {
				$selected = '';
			}
			$titel = $vertiefung['vertiefung'];
			if (!empty($vertiefung['kuerzel'])) {
				$titel .= ' (' . $vertiefung['kuerzel'] . ')';
			}
			if (!empty($vertiefung['version'])) {
				$titel .= ' PO' . $vertiefung['version'];
			}
			$vertiefungenTabelle .= '<option ' . $selected . ' value="' . $vertiefung['uid'] . '">' . $titel . '</option>';
		}
		$vertiefungenTabelle .= '</select>';
		return $vertiefungenTabelle;
	}
	
	function gibVersionenSelect($vertiefung='',$versionSelected='') {
		$versionen = $this->gibVersionen($vertiefung);
		if (count($versionen)==1) {
			$versionenTabelle =	'<select id="po_version" name="po_version">';
			$version = $versionen[0];
			$versionenTabelle .= '<option ' . $selected . ' value="' . $version . '">Version ' . $version . '</option>';
			$versionenTabelle .= '</select>';
			return $versionenTabelle;
		} else {
			$versionenTabelle =	'<select id="po_version" name="po_version">';
			foreach($versionen as $version) {
				if ($version==$versionSelected) {
					$selected = ' selected="selected" ';
				} else {
					$selected = '';
				}
				$versionenTabelle .= '<option ' . $selected . ' value="' . $version . '">Version ' . $version . '</option>';
			}
			$versionenTabelle .= '</select>';
		}
		return $versionenTabelle;
	}
	
	function gibVersionen($vertiefung='') {
		if (empty($vertiefung)) {
			$versionen = array(1,2,3);
		} else {
			$whereVertiefung = 'uid=' . $vertiefung;
			$abfrageVertiefung = $GLOBALS['TYPO3_DB']->exec_SELECTquery('version','tx_hetools_module_vertiefungen',$whereVertiefung);
			$versionenDaten = array();
			while ($datenVertiefung = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageVertiefung)) {
				$versionenDaten[] = $datenVertiefung['version'];
			}
			if (count($versionenDaten)==1 && empty($versionenDaten[0]['version'])) {
				$versionen = array(1,2,3);
			} else {
				foreach($versionenDaten as $version) {
					$versionen[] = $version;
				}
			}
		}
		return $versionen;
	}
	
	function gibVertiefungen($studiengang) {
		$vertiefungen = array();
		$whereVertiefung = 'modstud_id=' . $studiengang;
		$abfrageVertiefung = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,version,kuerzel,vertiefung','tx_hetools_module_vertiefungen',$whereVertiefung);
		while ($datenVertiefung = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageVertiefung)) {
			$vertiefungen[] = $datenVertiefung;
		}
		return $vertiefungen;
	}
	
	function modulTest_lsf() {
		$master = FALSE;
		$post = t3lib_div::_POST();
		$get = t3lib_div::_GET();
		$studiengangSelected = 0;
		$abschlussSelected = 0;
		if (!empty($get['po_version'])) {
			$poVersionSelected = $get['po_version'];
		} else {
			$poVersionSelected = 2;
		}
		if (!empty($get['darstellungs_art'])) {
			$darstellungsArtSelected = $get['darstellungs_art'];
		} else {
			$darstellungsArtSelected = 'studienabschnitte';
		}
		if (!empty($get['anzahl_spalten'])) {
			$spaltenZahlSelected = $get['anzahl_spalten'];
		} else {
			$spaltenZahlSelected = '4';
		}
		
		if (!empty($get['studiengang_lsf'])) {
			$studiengang = $get['studiengang_lsf'];
			$whereStudiengang = 'uid=' . $studiengang;
			$abfrageStudiengang = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_hetools_module_studiengaenge',$whereStudiengang);
			if ($datenStudiengang = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageStudiengang)) {			
				$studiengangSelected = $datenStudiengang['uid'];
				$studiengangBezeichung = $datenStudiengang['title'];
				$abschlussLsf = $datenStudiengang['lsf_abs'];
				$fakultaet = $datenStudiengang['fakultaet'];
				$semVertiefung = $datenStudiengang['sem_schwp'];
				$abschluss = $datenStudiengang['abschluss'];
				$lsfStudiengang = $datenStudiengang['lsf_stdg'];
			}
		}
		if (!empty($get['vertiefung_lsf'])) {
			$vertiefungSelected = $get['vertiefung_lsf'];
		} else {
			$vertiefungSelected = '';
		}
		$where = 'deleted=0 AND hidden=0 AND title NOT LIKE "%SPO3%"';
		$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_hetools_module_studiengaenge',$where,'','lsf_abs,title');
		$studiengangTabelle = '<h3>Studiengang</h3>
													<select style="width: 150px;" id="studiengang_lsf" name="studiengang_lsf">
													<option value="">Bitte einen Studiengang auswählen</option>
													<option value="" disabled="disabled">--------- Bachelor-Studiengänge ---------</option>
		';
		while ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
			if ($daten['uid']==$studiengangSelected) {
				$selected = ' selected="selected" ';
			} else {
				$selected = '';
			}
			if (!$master && $daten['lsf_abs']==90) {
				$studiengangTabelle .= '<option value="" disabled="disabled">--------- Master-Studiengänge ---------</option>
				';
				$master = TRUE;
			}
			$titel = $daten['title'];
			if (!empty($daten['schwerpunkt'])) {
				$titel .= ' (' . $daten['schwerpunkt'] . ')';
			}
			$studiengangTabelle .= '<option ' . $selected . ' value="' . 
														 	$daten['uid'] . '">' . $titel . '</option>';
		}
		$studiengangTabelle .= '</select>';
		
		if (!empty($studiengangSelected)) {
			$vertiefungsSelect .= $this->gibVertiefungenSelect($studiengangSelected);
		} else {
			$vertiefungsSelect = '';
		}
		$vertiefungenTabelle = '<h3>Vertiefung</h3>
														<div id="vertiefungen">
														' . $vertiefungsSelect . '</div>';
		$poVersion = '<h3>PO-Vers.</h3>
									<div id="versionen">';
		$poVersion .= $this->gibVersionenSelect($vertiefungSelected,$poVersionSelected);
		$poVersion .= '</div>';
		$darstellungsArt = '<h3>Darstellung</h3><select style="width: 150px;" id="darstellungs_art" name="darstellungs_art">
		';
		if ($darstellungsArtSelected=='studienabschnitte') {
			$darstellungsArt .= '<option selected="selected" value="studienabschnitte">nach Studienabschnitten (Liste)</option>';
			$darstellungsArt .= '<option value="tabelle">nach Semestern (Tabelle)</option>';
		} else {
			$darstellungsArt .= '<option value="liste">nach Studienabschnitten (Liste)</option>';
			$darstellungsArt .= '<option selected="selected" value="tabelle">nach Semestern (Tabelle)</option>';
		}
		$darstellungsArt .= '</select>';
		$spaltenZahl = '<h3>Anz. Spalten</h3><select id="anzahl_spalten" name="anzahl_spalten">
		';
		for ($i=2;$i<=6;$i++) {
			if ($i==$spaltenZahlSelected) {
				$selected = ' selected="selected" ';
			} else {
				$selected = '';
			}
			$spaltenZahl .= '<option ' . $selected . ' value="' . $i . '">' . $i . ' Spalten</option>';
		}
		$spaltenZahl .= '</select>';
		$absenden = '<h3>Modulübersicht</h3><input id="moduleAnzeigen" style="margin: 0 0 0 20px; padding: 8px 12px;" 
									name="absenden" type="submit" value="anzeigen" />';
		$url = 'http://www.hs-esslingen.de/index.php?';
		
		$out = '<div style="overflow: hidden; margin-bottom: 10px;">
						<form method="get" action="' . $url . '">' .
					 '<input type="hidden" name="id" value="' . $GLOBALS['TSFE']->id . '" />' .
					 '<div id="wrap_studiengaenge" style="float: left; margin-right: 4px;">' . $studiengangTabelle . '</div>' .
					 '<div id="wrap_vertiefungen" style="float: left; margin-right: 4px;">' . $vertiefungenTabelle . '</div>' .
					 '<div id="wrap_version" style="float: left; margin-right: 4px;">' . $poVersion . '</div>' .
					 '<div id="wrap_darstellungsArt" style="float: left; margin-right: 4px;">' . $darstellungsArt . '</div>' .
					 '<div id="wrap_spaltenZahl" style="float: left; margin-right: 4px;">' . $spaltenZahl . '</div>' .
					 '<div id="wrap_absenden" style="float: left;">' . $absenden . '</div>' .
					 '</form></div><hr style="clear: both;margin-bottom: 20px;"/>
					 <script type="text/javascript">
					 	var studiengang = $("#studiengang_lsf").val();
				 		if (studiengang!=0) {
				 			$("#wrap_absenden").css("display","inherit");
						} else {
							$("#wrap_absenden").css("display","none");
						}
					 	var darstellungsArt = $("#darstellungs_art").val();
					 		if (darstellungsArt=="tabelle") {
					 			$("#wrap_spaltenZahl").css("display","inherit");
							} else {
								$("#wrap_spaltenZahl").css("display","none");
							}
					 $("#studiengang_lsf").change(function(){
					 	var studiengang = $("#studiengang_lsf").val();
					 	if (studiengang!=0) {
					 		$("#vertiefungen_lsf").detach();
							$("#vertiefungen").load("index.php?eID=he_tools&action=gib_lsf_modb_vertiefungen&modId=" + studiengang);
					 		$("#wrap_vertiefungen").css("display","inherit");
					 		$("#wrap_version").css("display","inherit");
					 		var darstellungsArt = $("#darstellungs_art").val();
					 		if (darstellungsArt=="tabelle") {
					 			$("#wrap_spaltenZahl").css("display","inherit");
							} else {
								$("#wrap_spaltenZahl").css("display","none");
							}
					 		$("#wrap_absenden").css("display","inherit");
						} else {
					 		$("#wrap_vertiefungen").css("display","none");
					 		$("#wrap_version").css("display","none");
					 		$("#wrap_spaltenZahl").css("display","none");
							$("#wrap_absenden").css("display","none");
					 	}
					 });
					 $("#wrap_vertiefungen").delegate($("#vertiefung_lsf"),"change",function(){
					 	var vertiefung = $("#vertiefung_lsf").val();
					 	if (vertiefung!=0) {
					 		$("#wrap_spaltenZahl").css("display","none");
							$("#wrap_absenden").css("display","none");
					 		$("#po_version").detach();
							$("#versionen").load("index.php?eID=he_tools&action=gib_lsf_modb_versionen&vertiefung=" + vertiefung);
							$("#wrap_version").css("display","inherit");
							var darstellungsArt = $("#darstellungs_art").val();
					 		if (darstellungsArt=="tabelle") {
					 			$("#wrap_spaltenZahl").css("display","inherit");
							} else {
								$("#wrap_spaltenZahl").css("display","none");
							}
							$("#wrap_absenden").css("display","inherit");
					} else {
					 		$("#wrap_vertiefungen").css("display","none");
					 		$("#wrap_version").css("display","none");
					 		$("#wrap_spaltenZahl").css("display","none");
						}
					 });
					 
					 $("#wrap_darstellungsArt").delegate($("#darstellungs_art"),"change",function(){
					  var darstellungsArt = $("#darstellungs_art").val();
					 		if (darstellungsArt=="tabelle") {
					 			$("#wrap_spaltenZahl").css("display","inherit");
							} else {
								$("#wrap_spaltenZahl").css("display","none");
							}
					 });
						var studiengang = $("#studiengang_lsf").val();
					 	if (studiengang==0) { 
					 		$("#wrap_vertiefungen").css("display","none");
					 		$("#wrap_version").css("display","none");
					 		$("#wrap_spaltenZahl").css("display","none");
						}
					 </script>'
					 	;
		if (isset($get['pordId'])) {
			if (isset($get['L'])) {
				if ($get['L']==1) {
					$lang = 'en';
				} else {
					$lang = 'de';
				}
			} else {
				$lang = 'de';
			}
			
	 		$pordId = $get['pordId'];
	 		$this->modulEinzeln($pordId,$modulDatenEinzeln,$modulDatenTlEinzeln);
	 		$modulEinzeln = $this->gibModulaDatenAus($modulDatenEinzeln,$lang);
	 		$teilleistungenEinzeln = '<h1>Teilleistungen</h1>';
	 		foreach ($modulDatenTlEinzeln as $daten) {
	 			$teilleistungenEinzeln .= $this->gibModulaDatenAus($daten,$lang);
	 		}
	 		$out .=  $modulEinzeln . $teilleistungenEinzeln;
	 	} else if (!empty($get['absenden'])) {
	 		if ($darstellungsArtSelected=='studienabschnitte') {
				$out .= $this->erzeugeModulListe($lsfStudiengang,$abschlussLsf,$vertiefungLsf,$abschluss,
																				 $poVersionSelected,$semVertiefung,
																				 $studiengangBezeichung,$fakultaet);
	 		} else {
	 			$maxCol = $spaltenZahlSelected;
	 			$out .= $this->erzeugeModulTabelle($lsfStudiengang,$abschlussLsf,$vertiefungLsf,$abschluss,
	 																				 $poVersionSelected,$semVertiefung,
	 																				 $studiengangBezeichung,$fakultaet,$maxCol);
	 		}
		}
		return $out;					 	
	}
	
	function modulEinzeln($pordNummer,&$modulDatenEinzeln,&$modulDatenTlEinzeln,$lang,$lsfObject=''){
		if (empty($lsfObject)) {
			if ($lang=='en') {
				$lsfObject = 'enModulDescription';
			} else {
				$lsfObject = 'ModulDescription';
			}
		}
		$getDataRequest = '<SOAPDataService>
				<general><object>' . $lsfObject . '</object></general>
				<condition><pordID>' . $pordNummer . '</pordID></condition>
			</SOAPDataService>';
		$erg = $this->soapRequest($getDataRequest);	
		if (isset($erg['Modul'][$lsfObject])) {
			if ($lang=='en') {
				$modulDatenEinzeln = $erg['Modul'][$lsfObject][0];
				foreach ($erg['Modul'][$lsfObject] as $modulEintrag) {
					$modulDatenEinzeln[$modulEintrag['Beschreibung']] = $modulEintrag['txt'];
				}
			} else {
				$modulDatenEinzeln = $erg['Modul'][$lsfObject];
			}
			$modulDatenEinzeln['Verantw'] = $this->gibVerantwortliche($pordNummer);
		} else {
			$modulDatenEinzeln = array();
		}

		if ($lang=='en') {
			$lsfObject = 'enModulTeilLeist';
		} else {
			$lsfObject = 'ModulTeilLeist';
		}
		$getDataRequest = '<SOAPDataService>
		<general><object>' . $lsfObject . '</object></general>
		<condition><pordID>' . $pordNummer . '</pordID></condition>
		</SOAPDataService>';
		$erg = $this->soapRequest($getDataRequest);
		if (isset($erg['Modul'][$lsfObject])) {
			
			if ($lang=='en') {
				if (isset($erg['Modul'][$lsfObject][0])) {
					$pordId= 0;
					foreach ($erg['Modul'][$lsfObject] as $modulEintrag) {
						if ($modulEintrag['pordTeilLeist']!=$pordId) {
							$pordId = $modulEintrag['pordTeilLeist'];
							$modulDatenTlEinzeln[$pordId] = $modulEintrag;
						}
						$modulDatenTlEinzeln[$pordId][$modulEintrag['Beschreibung']] = $modulEintrag['txt'];
					}
				} else {
					$modulDatenTlEinzeln = array('0'=>$erg['Modul'][$lsfObject]);
				}
			} else {
				// Teilleistungen ggf. in ein Array packen 
				// falls es nur eine einzelne Teilleistung gibt
				if (isset($erg['Modul'][$lsfObject][0])) {
					$modulDatenTlEinzeln = $erg['Modul'][$lsfObject];
				} else {
					$modulDatenTlEinzeln = array('0'=>$erg['Modul'][$lsfObject]);
				}
			}
			
			foreach($modulDatenTlEinzeln as $index=>$tlDaten) {
				if (!empty($tlDaten['pordTeilLeist'])) {
					$modulDatenTlEinzeln[$index]['VerantwTl'] = $this->gibVerantwortliche($tlDaten['pordTeilLeist']);
				}
			}
		} else {
			$modulDatenTlEinzeln = array();
		}
	}
		
	function teilleistungEinzelnEn($pordId,$pordIdTl,&$modulDatenTlEinzeln){
		$lsfObject = 'enModulTeilLeist';
		$getDataRequest = '<SOAPDataService>
		<general><object>' . $lsfObject . '</object></general>
		<condition><pordID>' . $pordId . '</pordID></condition>
		</SOAPDataService>';
		$erg = $this->soapRequest($getDataRequest);
		if (isset($erg['Modul'][$lsfObject])) {
			if (isset($erg['Modul'][$lsfObject][0])) {
				foreach ($erg['Modul'][$lsfObject] as $modulEintrag) {
					if ($modulEintrag['pordTeilLeist']==$pordIdTl) {
						if (empty($modulDatenTlEinzeln)) {
							$modulDatenTlEinzeln['ModulText'] = $modulEintrag['ModulText'];
						}
						$modulDatenTlEinzeln[$modulEintrag['Beschreibung']] = $modulEintrag['txt'];
					}
				}
			}
		}
		if (!empty($modulDatenTlEinzeln)) {
			$modulDatenTlEinzeln['VerantwTl'] = $this->gibVerantwortliche($pordIdTl);
		}
	}
		
	function gibTeilleistungen($pordId){
		$getDataRequest = '<SOAPDataService>
				<general><object>modulTeilLeist</object></general>
				<condition><pordid>' . $pordId . '</pordid></condition>
				</SOAPDataService>';
		$erg = $this->soapRequest($getDataRequest);
		return $erg;
	}
		
	function gibVerantwortliche($pordId){
		$getDataRequest = '<SOAPDataService>
				<general><object>Verantwortlicher</object></general>
				<condition><pordid>' . $pordId . '</pordid></condition>
				</SOAPDataService>';
		$erg = $this->soapRequest($getDataRequest);
		if (isset($erg['Modul']['Verantwortlicher'][0])) {
			return $erg['Modul']['Verantwortlicher'];
		} else {
			return array('0'=>$erg['Modul']['Verantwortlicher']);
		}
	}

	function raeumeListe($eingabe){
		$getDataRequest = '<SOAPDataService>
				<general><object>modulList</object></general>
			</SOAPDataService>';
		$erg = $this->soapRequest($getDataRequest);		
		if (isset($erg['Modul']['ModulDescription'])) {
			return $erg['Modul']['ModulDescription'];
		} else {
			return $erg;
		}
	}
		
	function giblisteEnglischeVeranstaltungenAus(&$veranstaltungsListeLsf){
		$englischeVeranstaltungen = array();
		$out = '';
		foreach($veranstaltungsListeLsf as $veranstaltung) {
			$veranstalter = $veranstaltung['Einrichtung'];
			if (!isset($englischeVeranstaltungen[$veranstalter])) {
				$englischeVeranstaltungen[$veranstalter] = array();
			}
			if (!isset($englischeVeranstaltungen[$veranstalter][$veranstaltung['VeranstDtxt']])) {
				$englischeVeranstaltungen[$veranstalter][$veranstaltung['VeranstDtxt']] = $this->gibLsfVeranstaltungsDatenFuerModulTabelle($veranstaltung);
			}
		}
		ksort($englischeVeranstaltungen);
		return $this->gibEnglischeModultabelleAus($englischeVeranstaltungen);
	}
	
	function gibLsfVeranstaltungsDatenFuerModulTabelle(&$veranstaltung){
		$studiengang = '';
		$title = $veranstaltung['VeranstDtxt'];
		if (!empty($veranstaltung['AkadGrad'])) {
			$akadGrad = $veranstaltung['AkadGrad'] . ' ';
		} else {
			$akadGrad = '';
		}
		$email = '<a class="email" href="mailto:' . $veranstaltung['email'] . '">' .
				$akadGrad . $veranstaltung['Anrede'] . ' ' . $veranstaltung['Vorname'] . ' ' . $veranstaltung['Nachname'] .
				'</a>';
		$lecturer = $email;
		$credits = '';
		$level1 = '';
		$level2 = '';
		$sem = intval($veranstaltung['Sem']);
		if (empty($sem)) {
			$semester = $veranstaltung['Sem'];
		} else {
			$jahr = intval(substr($veranstaltung['Sem'],0,4));
			$sem = substr($veranstaltung['Sem'],4,1);
			if ($sem == 1) {
				$semester = 'SS ' . $jahr;
				$semester = 'SS';
			} else {
				$semester = 'WS ' . $jahr . '/' . ($jahr+1);
				$semester = 'WS';
			}
		}
		return array(
				'studiengang'=>$studiengang,
				'title'=>$title,
				'lecturer'=>$lecturer,
				'credits'=>$credits,
				'level1'=>$level1,
				'level2'=>$level2,
				'semester'=>$semester,
		);
	}
	
	function gibLsfModulDatenFuerModulTabelle(&$modulbeschreibung,$lsfStudiengang) {
		$modulDatenTlEinzeln = array();
		$this->modulEinzeln($modulbeschreibung['pordID'],$modulDatenEinzeln,$modulDatenTlEinzeln,'en');
		$verantwortliche = '';
		foreach ($modulDatenEinzeln['Verantw'] as $verantw) {
			$verantwortliche .= $this->gibLinkVerantwortlichen($verantw);
		}
		$lecturer = $verantwortliche;
		$studiengang = $lsfStudiengang;
		$title = $modulbeschreibung['ModulText'];
		$credits = $modulbeschreibung['Credits'];
		$pordId = $modulbeschreibung['pordID'];
		if ( $modulbeschreibung['Fachsemester']>2) {
			$level1 = '';
			$level2 = 'X';
		} else {
			$level1 = 'X';
			$level2 = '';
		}
		$semester = '';
		return array(
				'studiengang'=>$studiengang,
				'title'=>$title,
				'lecturer'=>$lecturer,
				'credits'=>$credits,
				'level1'=>$level1,
				'level2'=>$level2,
				'semester'=>$semester,
				'pordId'=>$pordId,
		);
	}
	
	function gibLsfTlDatenFuerModulTabelle(&$tlDaten,$lsfStudiengang) {
		$modulDatenTlEinzeln = $tlDaten;
		if (!empty($tlDaten['pordTeilLeist'])) {
			$modulDatenTlEinzeln['VerantwTl'] = $this->gibVerantwortliche($tlDaten['pordTeilLeist']);
		}
		$verantwortliche = '';
		foreach ($modulDatenTlEinzeln['VerantwTl'] as $verantw) {
			$verantwortliche .= $this->gibLinkVerantwortlichen($verantw);
		}
		$lecturer = $verantwortliche;
		$studiengang = $lsfStudiengang;
		$title = $modulDatenTlEinzeln['ModulText'];
		$credits = $modulDatenTlEinzeln['Credits'];
		$pordId = $modulDatenTlEinzeln['pordID'];
		$pordIdTl = $modulDatenTlEinzeln['pordTeilLeist'];
		if ( $modulDatenTlEinzeln['Fachsemester']>2) {
			$level1 = '';
			$level2 = 'X';
		} else {
			$level1 = 'X';
			$level2 = '';
		}
		$semester = '';
	
		return array(
				'studiengang'=>$studiengang,
				'title'=>$title,
				'lecturer'=>$lecturer,
				'credits'=>$credits,
				'level1'=>$level1,
				'level2'=>$level2,
				'semester'=>$semester,
				'pordId'=>$pordId,
				'pordIdTl'=>$pordIdTl,
		);
		
	}
	
	function gibEnglischeModultabelleAus(&$englischeModule){
		$out = '<table class="tab_gitternetz tab100"><tbody>';

		foreach($englischeModule as $standort=>$einrichtungsListe) {
			$out .= '
			<tr class="hg_rot"> <td colspan="7"><h3>' . $standort . '</h3></td></tr>
			<tr> <td colspan="7">&nbsp; </td></tr>
			<tr class="hg_dunkelblau">
			<td><strong>Departments/ <br>Study programs </strong></td>
			<td><strong>Title </strong></td>
			<td><strong>Lecturer </strong></td>
			<td><strong>ECTS <br>credits </strong></td>
			<td><strong>Bachelor Level A <br>(1.-2. Sem.) </strong></td>
			<td><strong>Bachelor Level B <br>(3.-7. Sem.) </strong></td>
			<td><strong><span title="offered in which semester?" class="abbr">Semester </span></strong></td>
			</tr>
			';
		
			foreach($einrichtungsListe as $einrichtung=>$modulListe) {
				$out .= '<tr> <td colspan="7"><strong>' . $einrichtung . '</strong></td></tr>';
				$bg = 'hg_dunkel';
				foreach($modulListe as $modul) {
					if ($bg=='hg_dunkel') {
						$bg = '';
						$cssClass = '';
					} else {
						$bg = 'hg_dunkel';
						$cssClass = ' class="hg_dunkel" ';
					}
					if (!empty($modul['pordIdTl'])) {
						$url = $this->cObj->typoLink_URL(array(
								'parameter' => $GLOBALS['TSFE']->id,
								'additionalParams' => '&pordId=' . $modul['pordId'] . '&pordIdTl=' . $modul['pordIdTl'],
								'useCacheHash' => 1,
								));
					} else if (!empty($modul['pordId'])) {
						$url = $this->cObj->typoLink_URL(array(
								'parameter' => $GLOBALS['TSFE']->id,
								'additionalParams' => '&pordId=' . $modul['pordId'],
								'useCacheHash' => 1,
								));
					}
					$title =  '<a target="_blank" href="' . $url . '">' . $modul['title'] . '</a>';
					$out .= '<tr' . $cssClass . '>';
					$out .= '<td>' . $modul['studiengang'] . '</td>';
					$out .= '<td>' . $title . '</td>';
					$out .= '<td>' . $modul['lecturer'] . '</td>';
					$out .= '<td>' . $modul['credits'] . '</td>';
					$out .= '<td>' . $modul['level1'] . '</td>';
					$out .= '<td>' . $modul['level2'] . '</td>';
					$out .= '<td>' . $modul['semester'] . '</td>';
					$out .= '</tr>';
				}
				$out .= '<tr> <td colspan="7">&nbsp; </td></tr>';
			}
		}
		$out .= '</tbody></table>';
		return $out;
	}

	function modulListe($studiengang,$version=2,$abschluss=84,$lang='de'){
		if ($lang=='en') {
			$lsfObject = 'enModulList';
		} else {
			$lsfObject = 'ModulList';
		}
		$getDataRequest = '<SOAPDataService>
					<general>
						<object>' . $lsfObject . '</object>
					</general>
					<condition>
					<abschluss>' . $abschluss . '</abschluss>
					<studiengang>' . $studiengang . '</studiengang>
					<version>' . $version . '</version>
					</condition>
			 </SOAPDataService>';
		$erg = $this->soapRequest($getDataRequest);	
		if (isset($erg['Modul'][$lsfObject])) {
/*
			$liste = $erg['Modul'][$lsfObject];
			$modulListe = array();
			foreach ($liste as $elem) {
				$getDataRequestSingle = '<SOAPDataService>
				<general><object>modulDescription</object></general>
				<condition><pordid>' . $elem['pordID'] . '</pordid></condition>
				</SOAPDataService>';
				$ergSingle = $this->soapRequest($getDataRequestSingle);
				if (!empty($ergSingle['Modul']['ModulDescription']['Inhalt'])) {
					$modulListe[] = $elem;
				}
			}
			return $modulListe;
*/			
			return $erg['Modul'][$lsfObject];
		} else {
			return '';
		}
	}

	function soapRequest($getDataRequest){
		$returnValue = '';
		$url = $this->hostname . $this->requestPath;
		$soapclient = new SOAP_Client($url);
		$soapclient->setOpt('curl', CURLOPT_SSL_VERIFYPEER, 0);
		$soapclient->setOpt('timeout', $this->soapTimeout);
		$params = array('arg0' => $getDataRequest);		
		$returnValue = $soapclient->call($this->rpcMethod, $params);
		$xmlArr = $this->XML_unserialize($returnValue);

$debug = FALSE;
if ($this->lsfDebug) {
	$debug = TRUE;
} else {
	$get = t3lib_div::_GET();
	if (isset($get['debug'])) {
		$debug = TRUE;
	}	
}
if ($debug) {
	t3lib_utility_Debug::debugInPopUpWindow($params,'Soap-Anfrage');
	t3lib_utility_Debug::debugInPopUpWindow($xmlArr,'Soap-Response');
	
}
		return $xmlArr;

	}
		
	function gibNeusteModule($modulListe){ 
		foreach($modulListe as $modulDaten) {
			if ($modulDaten['Version']>$maxVersion) {
				$maxVersion = $modulDaten['Version'];
			}
		}
		foreach($modulListe as $modulDaten) {
			$sem = $modulDaten['FruehestesSemester'];
			if (preg_match('@([0-9]).*@',$modulDaten['ModulCode'],$matches)) {
				$sem = $matches[1];
			}
			$modulTabelle[$sem][] = array(
																	'pordID'=>$modulDaten['pordID'],
																	'titel'=>$modulDaten['ModulText'],
																	'ModulCode'=>$modulDaten['ModulCode'],
																	);
		}
		krsort($modulTabelle);
	}
		
	function gibFeldNamen($feld,$lang) {
		if (isset($this->modulFeldnamen[$lang][$feld])) {
			return $this->modulFeldnamen[$lang][$feld];
		}
//return $feld;	
		return '';
	}
	
	function gibModulaDatenAus($modulDaten,$lang='de') {
		if (!is_array($modulDaten) || count($modulDaten)<1) {
			return '';
		}
		$title = '';
		if (!empty($modulDaten['ModulText']))	 {
			if (isset($modulDaten['pordTeilLeist'])) {
				$title = '<h2>' . $modulDaten['ModulText'] . '</h2>';
			} else {
				$title = '<h1>' . $modulDaten['ModulText'] . '</h1>';
			}
			unset($modulDaten['ModulText']);
		}
/*
	if ($lang=='de') {
	} else {
			if (!empty($modulDaten['ModulText']))	 {
				$title = '<h1>' . $modulDaten['ModulText'] . '</h1>';
				unset($modulDaten['ModulText']);
			} elseif (!empty($modulDaten['enTxt']))	 {
				$title = '<h2>' . $modulDaten['enTxt'] . '</h2>';
				unset($modulDaten['enTxt']);
			}
		}
*/		
		foreach ($modulDaten as $feld=>$wert) {
			$feldName = $this->gibFeldNamen($feld,$lang);
			
			if (!empty($feldName)) {
				if (count($feldName)==2 && $feld!='ModulText') {
					if ($feld=='Verantw' || $feld=='VerantwTl') {
						$verantwortliche = '';
						foreach ($wert as $key=>$verantw) {
							$verantwortliche .= $this->gibLinkVerantwortlichen($verantw);
						}
						$wert = $verantwortliche;
					} else if ($feld=='lehrsprache') {
						if ($wert=='D' || $wert=='Deutsch' || empty($wert)) {
							$wert = 'deutsch';
						} elseif ($wert=='D+E') {
							$wert = 'deutsch und englisch';
						}else {
							$wert = 'englisch';
						}
					} else if (!empty($wert) && (
											$feld=='selbstzeit' || 
										 	$feld=='kontaktzeit' ||
										 	$feld=='pord.selbstzeit' ||
										 	$feld=='pord.praesenzzeit')) {
						if ($lang=='de') {
							$wert .= ' Stunden';
						} else {
							$wert .= ' hours';
						}
					}
					if (!empty($wert)) {
						$ergListe[$feldName[0]] = array($feldName[1],$wert);
					}
				}
			}
		}
		$out = $title;
		if (is_array($ergListe) && count($ergListe)>0) {
			ksort($ergListe);
			$out .= '<dl class="modules">';
			foreach ($ergListe as $index=>$daten) {
				$out .= '<dt>' .
						$daten[0] .
						'</dt>
						<dd class="' . $this->moduleValueClass . '">' .
						$daten[1] .
						'</dd>' . "\n";
			}
			$out .= '</dl>';
			
		}
		return $out;
	}
	
	function gibLinkVerantwortlichen($lsfDaten) {
		$verantwortlicher = '';
		if (!empty($lsfDaten) && is_array($lsfDaten)) {
			$username = array_pop($lsfDaten);

			$queryPerson = '
			SELECT name,email,tx_hepersonen_profilseite as page, 
						 tx_hepersonen_akad_grad as akad_grad FROM fe_users
			WHERE fe_users.username = "' . $username . '"
			AND fe_users.deleted=0 AND fe_users.disable=0
			';
			$abfragePerson = $GLOBALS['TYPO3_DB']->sql_query($queryPerson);
			if ($datenPerson = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfragePerson)) {
				if (!empty($datenPerson['akad_grad'])) {
					$name = $datenPerson['akad_grad'] . ' ' . $datenPerson['name'];
				} else {
					$name = $datenPerson['name'];
				}
				
				if (!empty($datenPerson['page'])) {
					$typolink_conf = array(
							'returnLast' => 'url',
							'parameter' => $datenPerson['page']);
					
					$linkUrl = $this->cObj->typolink("", $typolink_conf);
					$personenLink = '<a class="internalLink" target="_blank" href="' . $linkUrl . '">' . $name . '</a>';
				} else {
					$personenLink = '<a class="mail" href="mailto:' . $name . ' <' . $datenPerson['email'] . '>">' . $name . '</a>';
				}
			}
			$verantwortlicher = '<div class="verantw">' . $personenLink . '</div>';
		}
		return $verantwortlicher;
	}

	function gibVertiefungsBezeichnung($kuerzel,$lang) {
		$vertiefung = $kuerzel;
		$whereVertiefung = 'kuerzel="' . $kuerzel . '"';
		$abfrageVertiefung = $GLOBALS['TYPO3_DB']->exec_SELECTquery('vertiefung','tx_hetools_module_vertiefungen',$whereVertiefung);
		if ($datenVertiefung = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageVertiefung)) {
			$vertiefung = $datenVertiefung['vertiefung'];
		}
		return $vertiefung;
	}
	
	function erstelleModulListe($modulListe,$abschluss,$semVertiefung,$studiengang,$fakultaet,$lang='de',$linksDeaktivieren=FALSE) {
		$modulTabelle = array();
		if (empty($abschluss)) {
			$abschluss = 'Bachelor of Engineering';
		}
		$vertiefungen = array();
		foreach($modulListe as $modulDaten) {
			if (!empty($modulDaten['ktxtvert'])) {
				if (empty($vertiefungen[$modulDaten['ktxtvert']])) {
					$vertiefungen[$modulDaten['ktxtvert']] = $this->gibVertiefungsBezeichnung($modulDaten['ktxtvert'], $lang);
				}
			}
		}
		
		foreach($modulListe as $modulDaten) {
			if (!empty($modulDaten['ktxtvert'])) {
				$abschnitt = $vertiefungen[$modulDaten['ktxtvert']];
			} else if ($modulDaten[SEM_STRING]<$this->semBeg2) {
				$abschnitt = 1;
			} else {
				$abschnitt = 2;
			}
			$modulTabelle[$abschnitt][] = array(
					'pordID'=>$modulDaten['pordID'],
					'titel'=>trim(preg_replace('#^MD #', '', $modulDaten['ModulText'])),
					'ModulCode'=>$modulDaten['ModulCode'],
			);
		}
		$out .= '<h1>Studiengang ' . $studiengang . '</h1>';
		$out .= '<h2>' . $abschluss . '</h2>';
		foreach ($modulTabelle as $abschnitt=>$semesterDaten) {
			switch ($abschnitt) {
				case 1:
					$titel['de'] = 'Erster Studienabschnitt';
					$titel['en'] = 'First Study Section';
					break;
				case 2:
					$titel['de'] = 'Zweiter Studienabschnitt';
					$titel['en'] = 'Second Study Section';
					break;
				default:
					$titel['de'] = 'Vertiefung: ' . $abschnitt;
					$titel['en'] = 'Specialization: ' . $abschnitt;
					break;
			}
			$out .= $this->gibSemesterModuleAus($titel[$lang], $semesterDaten, $linksDeaktivieren);
		}
		if ($lang=='en') {
      $out .= '<h1>' . self::$einrichtungenEn[$fakultaet] . '</h1>';
    } else {
      $out .= '<h1>' . self::$einrichtungen[$fakultaet] . '</h1>';
    }

		return $out;
	}
	
	function erstelleModulTabelle($modulListe,$abschluss,$semVertiefung,$studiengang,$fakultaet,$lang='de',$maxCol=4,$linksDeaktivieren=FALSE) {
		$modulTabelle = array();
		if (empty($abschluss)) {
			$abschluss = 'Bachelor of Engineering';
		}
		$vertiefungen = array();
		foreach($modulListe as $modulDaten) {
			if (!empty($modulDaten['ktxtvert'])) {
				if (empty($vertiefungen[$modulDaten['ktxtvert']])) {
					$vertiefungen[$modulDaten['ktxtvert']] = $this->gibVertiefungsBezeichnung($modulDaten['ktxtvert'], $lang);
				}
			}
		}
		$i = 1;
		$legende = '';
		if (count($vertiefungen)>0) {
			$legende = '<h2>Legende für die verschiedenen Farben</h2>';
			$legende .= '<h3 class="legende allgemein"><span></span>Module für alle Vertiefungen</h3>';
			foreach ($vertiefungen as $key=>$vertiefung) {
				$cssVertiefung[$key] = ' vertiefung_' . $i;
				$legende .= '<h3 class="legende vertiefung_' . $i . '"><span></span>Vertiefung: ' . $vertiefung . '</h3>';
				$i++;
			}
		}

		foreach($modulListe as $modulDaten) {
			$sem = $modulDaten[SEM_STRING];
			$eintrag = array(
					'pordID'=>$modulDaten['pordID'],
					'titel'=>$this->gibmodulTitel($modulDaten['ModulText']),
					'ModulCode'=>$modulDaten['ModulCode'],
			);
			if (!empty($modulDaten['ktxtvert'])) {
				$eintrag['sortVert'] = $modulDaten['ktxtvert'];
				$eintrag['vert'] = $vertiefungen[$modulDaten['ktxtvert']];
				$eintrag['css'] = $cssVertiefung[$modulDaten['ktxtvert']];
			} else {
				$eintrag['sortVert'] = 'AA';
				
			}
			$modulTabelle[$sem][] = $eintrag;
		}
		krsort($modulTabelle);
		$out .= $legende;
		$out .= '<table class="modul_table">';
		$out .= '<tr class="titel">';
		$out .= '<th colspan="2">' . $abschluss . '</th>';
		$out .= '</tr>';
		foreach ($modulTabelle as $semester=>$semesterDaten) {
			if ($semester==$semVertiefung) {
				$out .= '<tr class="titel">';
				$out .= '<th colspan="2">' . $studiengang . '</th>';
				$out .= '</tr>';
			}
			$out .= $this->gibSemesterModulZeileAus($semester, $semesterDaten, $maxCol, $linksDeaktivieren);
		}
		$out .= '<tr class="titel">';
    if ($lang=='en') {
      $out .= '<th colspan="2">' . self::$einrichtungenEn[$fakultaet] . '</th>';
    } else {
      $out .= '<th colspan="2">' . self::$einrichtungen[$fakultaet] . '</th>';
    }

		$out .= '</tr>';
		$out .= '</table>';
		return $out;
	}
	
	function erstelleModulTabelleMitVertiefungen($modulListe,$abschluss,$semVertiefung,$studiengang,$fakultaet,$lang='de',$maxCol=4) {
		$modulTabelle = array();
		if (empty($abschluss)) {
			$abschluss = 'B. Eng.';
		}
		$vertiefungen = array();
		foreach($modulListe as $modulDaten) {
			if (!empty($modulDaten['ktxtvert'])) {
				if (empty($vertiefungen[$modulDaten['ktxtvert']])) {
					$vertiefungen[$modulDaten['ktxtvert']] = $this->gibVertiefungsBezeichnung($modulDaten['ktxtvert'], $lang);
				}
			}
		}
		$i = 1;
	
		foreach($modulListe as $modulDaten) {
			$sem = $modulDaten[SEM_STRING];
			$eintrag = array(
					'pordID'=>$modulDaten['pordID'],
					'titel'=>$this->gibmodulTitel($modulDaten['ModulText']),
					'ModulCode'=>$modulDaten['ModulCode'],
			);
			if (!empty($modulDaten['ktxtvert'])) {
				$eintrag['sortVert'] = $modulDaten['ktxtvert'];
				$eintrag['vert'] = $vertiefungen[$modulDaten['ktxtvert']];
				$eintrag['css'] = $cssVertiefung[$modulDaten['ktxtvert']];
			} else {
				$eintrag['sortVert'] = 'AA';
	
			}
			$modulTabelle[$sem][] = $eintrag;
		}
		krsort($modulTabelle);
		$anzVertiefungen = count($vertiefungen);
		$anzSpalten =  $anzVertiefungen + 2;
		$out .= '<table class="modul_title">';
		$out .= '<tr class="titel">';
		$out .= '<th>' . $studiengang . ' (' . $abschluss . ')' . '</th>';
		$out .= '</tr>';
		$out .= '</table>';
		
		$out .= '<table class="modul_table">';
		$out .= '<tr class="titel">';
		$out .= '<th colspan="2">Gemeinsame Module</th>';
		foreach ($vertiefungen as $kuerzel=>$titelVertiefung) {
			$out .= '<th>Studienschwerpunkt<br/>' . $titelVertiefung . '</th>';
		}
		$out .= '</tr>';
		
		foreach ($modulTabelle as $semester=>$semesterDaten) {
			if ($semester==$semVertiefung) {
				$out .= '<tr class="titel">';
				$out .= '<th colspan="' . $anzSpalten . '">Hauptstudium</th>';
				$out .= '</tr>';
			}
			$out .= $this->gibSemesterModulZeileAusMitVertiefungen($semester, $semesterDaten, $vertiefungen, $maxCol);
		}
		$out .= '<tr class="titel">';
		$out .= '<th colspan="' . $anzSpalten . '">Grundstudium</th>';
		$out .= '</tr>';
		$out .= '</table>';
		return $out;
	}
	
	function gibModulHandbuch($args){
		$dateiName = strtolower($args['stug']);
		if (!empty($args['vert'])) {
			$dateiName .= '_' . strtolower($args['vert']);
		}
		$dateiName .= '_' . $args['spo'] . '_' . $args['lang'] . '.pdf';		
		$pfadKomplett = 'fileadmin/medien/fakultaeten/allgemein/modulbeschreibungen/' . strtolower($args['faku']) ;
		if (!is_dir($pfadKomplett)) {
			mkdir($pfadKomplett,0755);
		}
		$pfadKomplett = 'fileadmin/medien/fakultaeten/allgemein/modulbeschreibungen/' . strtolower($args['faku']) . '/' .  strtolower($args['stug']);
		if (!is_dir($pfadKomplett)) {
			mkdir($pfadKomplett,0755);
		}
		$dateiPfad = $pfadKomplett . '/' . $dateiName;
		$documentRoot = t3lib_div::getIndpEnv(TYPO3_DOCUMENT_ROOT);
		$systemPfad = $documentRoot . '/' . $dateiPfad;
		$dateiVorhanden = file_exists($systemPfad);
		if ($dateiVorhanden) {
			$heute = date("Ymd",time());
			$dateiDatum = date("Ymd",filectime($systemPfad));
			if ($heute!=$dateiDatum) {
				unlink($systemPfad);
				$dateiVorhanden = FALSE;
			}
		}
		if (!$dateiVorhanden) {
			$url = 'http://www3.hs-esslingen.de/qislsf/rds?state=modulBeschrGast&struct=auswahlBaum&language=' . $args['lang'] . '&createPDF=Y&create=blobs&modulversion.semester=&modulversion.versionsid=' .
					'&nodeID=auswahlBaum|abschluss:abschl=' . $args['abs']. '|studiengang:stg=' . $args['stug']. '|stgSpecials:vert=' . $args['vert'] . ',schwp=,kzfa=H,pversion=' . $args['spo']. '&expand=1&asi=#';
			$redirectHeader = t3lib_div::getURL($url, 1, true, $report);
			preg_match('#^(.*)(http://www3.hs-esslingen.de/qislsf/.*&asi=)(.*)#Uis',$redirectHeader,$matches);
			$urlNew = $matches[2];
			$content = t3lib_div::getURL($urlNew, 1, true, $report);
			if ($report['error']) {
				$error = 'Fehler beim Einlesen des PDFs: "' . $dateiPfad . '"';
			} else {
				file_put_contents($dateiPfad, $content);
			}
		}
		$datei = fopen($dateiPfad,'rb');
		
		header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: no-cache');
		header("Content-Type : application/pdf");
		header("Content-Disposition: attachment; filename=". $dateiName);
		while (!feof($datei)) {
			set_time_limit (60);
			echo fread($datei, 8192);
		}
		fclose($datei);
		exit();
	}
	
	function gibModulPdf($args){
		$dateiName = strtolower($args['stug']) . '_' . $args['pord']  . '_' . $args['spo'] . '_' . $args['lang'] . '.pdf'; ;
		$pfadKomplett = 'fileadmin/medien/fakultaeten/allgemein/modulbeschreibungen/' . strtolower($args['faku']) ;
		if (!is_dir($pfadKomplett)) {
			mkdir($pfadKomplett,0755);
		}
		$pfadKomplett = 'fileadmin/medien/fakultaeten/allgemein/modulbeschreibungen/' . strtolower($args['faku']) . '/' .  strtolower($args['stug']);
				if (!is_dir($pfadKomplett)) {
			mkdir($pfadKomplett,0755);
		}
		$dateiPfad = $pfadKomplett . '/' . $dateiName;
		$documentRoot = t3lib_div::getIndpEnv(TYPO3_DOCUMENT_ROOT);
		$systemPfad = $documentRoot . '/' . $dateiPfad;		
		$dateiVorhanden = file_exists($systemPfad);
		if ($dateiVorhanden) {
			$heute = date("Ymd",time());
			$dateiDatum = date("Ymd",filectime($systemPfad));
			if ($heute!=$dateiDatum) {
				unlink($systemPfad);
				$dateiVorhanden = FALSE;
			}
		}
		if (!$dateiVorhanden) {
     $report = '';

      $url1 = 'http://www3.hs-esslingen.de/qislsf/rds?state=modulBeschrGast&moduleParameter=modDescr&struct=auswahlBaum&language=' . $args['lang'] . '&next=wait.vm&lastState=modulBeschrGast' .
        '&nodeID=auswahlBaum|abschluss:abschl=' . $args['abs'] . '|studiengang:stg=' . $args['stug']. '|stgSpecials:vert=,schwp=,kzfa=H,pversion=' .  $args['spo']. '|kontoOnTop:pordnr=' . $args['pord']. '&asi=#' .
        'auswahlBaum|abschluss:abschl=' . $args['abs'] . '|studiengang:stg=' . $args['stug']. '|stgSpecials:vert=,schwp=,kzfa=H,pversion=' .  $args['spo']. '|kontoOnTop:pordnr=' . $args['pord'];

      $url1 = 'http://www3.hs-esslingen.de/qislsf/rds?state=modulBeschrGast&moduleParameter=modDescr';

 if ($GLOBALS['TSFE']->fe_user->user['username']=='mmirsch') {
 t3lib_utility_Debug::debugInPopUpWindow($url1);
 }
			$redirectHeader = tx_he_tools_util::getURL($url1, 1, 'GET');

      preg_match('#^.*JSESSIONID=(.*);.*#Uis',$redirectHeader,$matches);

      $sessionCookie = '&jsessionid==' . $matches[1];

      $url2 = 'http://www3.hs-esslingen.de/qislsf/rds' .
               '?state=modulBeschrGast&createPDF=Y&create=blobs&moduleParameter=modDescr&struct=auswahlBaum&language=' . $args['lang'] . '&next=wait.vm&lastState=modulBeschrGast' .
              '&nodeID=auswahlBaum|abschluss:abschl=' . $args['abs'] . '|studiengang:stg=' . $args['stug']. '|stgSpecials:vert=,schwp=,kzfa=H,pversion=' .  $args['spo']. '|kontoOnTop:pordnr=' . $args['pord']. '&asi=#' .
              'auswahlBaum|abschluss:abschl=' . $args['abs'] . '|studiengang:stg=' . $args['stug']. '|stgSpecials:vert=,schwp=,kzfa=H,pversion=' .  $args['spo']. '|kontoOnTop:pordnr=' . $args['pord']
      . $sessionCookie;

      $content = tx_he_tools_util::getURL($url2, 0, 'SET');

if ($GLOBALS['TSFE']->fe_user->user['username']=='mmirsch') {
  t3lib_utility_Debug::debugInPopUpWindow($matches);
  t3lib_utility_Debug::debugInPopUpWindow($url2);
  t3lib_utility_Debug::debugInPopUpWindow($content);
  exit();
}

/*
      $url2 = 'http://www3.hs-esslingen.de/qislsf/rds?state=modulBeschrGast&createPDF=Y&create=blobs&moduleParameter=modDescr&struct=auswahlBaum&language=' . $args['lang'] . '&next=wait.vm&lastState=modulBeschrGast' .
        '&nodeID=auswahlBaum|abschluss:abschl=' . $args['abs'] . '|studiengang:stg=' . $args['stug']. '|stgSpecials:vert=,schwp=,kzfa=H,pversion=' .  $args['spo']. '|kontoOnTop:pordnr=' . $args['pord']. '&asi=#' .
        'auswahlBaum|abschluss:abschl=' . $args['abs'] . '|studiengang:stg=' . $args['stug']. '|stgSpecials:vert=,schwp=,kzfa=H,pversion=' .  $args['spo']. '|kontoOnTop:pordnr=' . $args['pord'];




      preg_match('#^(.*)(http://www3.hs-esslingen.de/qislsf/.*&asi=)(.*)#Uis',$redirectHeader,$matches);
			$urlNew = $matches[2];
			$content = t3lib_div::getURL($urlNew, 1, true, $report);
*/
      if ($report['error']) {
				$error = 'Fehler beim Einlesen des PDFs: "' . $dateiPfad . '"';
			} else {
				file_put_contents($dateiPfad, $content);
			}
		}
		$datei = fopen($dateiPfad,'rb');
		
		header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: no-cache');
		header("Content-Type : application/pdf");
		header("Content-Disposition: attachment; filename=". $dateiName);
		while (!feof($datei)) {
			set_time_limit (60);
			echo fread($datei, 8192);
		}
		fclose($datei);
		exit();
	}
	
	function gibLinkModulHandbuch($title, $fakultaet, $abschluss, $studiengangLsf, $spoVersion, $lang, $vertiefungLsf='',$cssVertiefung=' allgemein',$button=TRUE){
		$url = t3lib_div::getIndpEnv('REQUEST_URI');
		$args = array('stug'=>$studiengangLsf,
									'vert'=>$vertiefungLsf,
									'spo'=>$spoVersion,
									'faku'=>$fakultaet,
									'abs'=>$abschluss,
									'lang'=>$lang);
		if (strpos($url,'?')>0) {
			$seperator = '&';
		} else {
			$seperator = '?';
		}
		$url .= $seperator . 'modulhandbuch=' . base64_encode(serialize($args));
		if ($button) {
			$out = '<span class="shadow_button' . $cssVertiefung . '"><a href="' . $url . '" target="_blank">' . $title . '</a></span>';
		} else {
			$out = '<a class="button ' . $cssVertiefung . '" href="' . $url . '" target="_blank">' . $title . '</a>';
		}
		
		return $out;
	}
	
	function gibLinkModulPdf($fakultaet, $abschluss, $studiengangLsf, $spoVersion, $pordNr, $lang) {
		$url = t3lib_div::getIndpEnv('REQUEST_URI');
		$args = array('stug'=>$studiengangLsf,
									'spo'=>$spoVersion,
									'faku'=>$fakultaet,
									'abs'=>$abschluss,
									'pord'=>$pordNr,
									'lang'=>$lang);
		$url .= '&modulpdf=' . base64_encode(serialize($args));
/*		
$url2 = 'http://www3.hs-esslingen.de/qislsf/rds?state=modulBeschrGast&createPDF=Y&create=blobs&moduleParameter=modDescr&struct=auswahlBaum&language=' . $lang . '&next=wait.vm&lastState=modulBeschrGast' . 
						'&nodeID=auswahlBaum|abschluss:abschl=' . $abschluss. '|studiengang:stg=' . $studiengang. '|stgSpecials:vert=,schwp=,kzfa=H,pversion=' . $spoVersion. '|kontoOnTop:pordnr=' . $pordNr. '&asi=#' .
						'auswahlBaum|abschluss:abschl=' . $abschluss. '|studiengang:stg=' . $studiengang. '|stgSpecials:vert=,schwp=,kzfa=H,pversion=' . $spoVersion. '|kontoOnTop:pordnr=' . $pordNr;
*/		
		if ($lang=='en') {
			$url .= '&language=en';
			$title = 'Download Module as PDF';
		} else {
			$title = 'Modul als PDF herunterladen';
		}
		$out = '<a style="float: left; padding: 8px 12px;" class="button" href="' . $url . '" target="_blank">' . $title . '</a>';
		return $out;
	}
	
	function gibSemesterZeilenAnzahl($semesterDaten, $maxCol){
		$anzSpalten = count($semesterDaten);
		return ceil($anzSpalten/$maxCol);
	}
	
	function gibmodulTitel($titel){
		$titelNeu = trim(preg_replace('#^MD #', '', $titel));
		return $titelNeu;
	}
	
	function sortiereModule($module, $felder, $asc = TRUE){
    $result = array();
    $werte = array();
    foreach ($module as $id => $wert) {
    	$werte[$id] = '';
      $fieldList = explode(',',$felder);
      foreach($fieldList as $field) {
      	$werte[$id] .= isset($wert[$field]) ? $wert[$field] : '';
      }
    }
    if ($asc) {
      asort($werte);
    } else {
      arsort($werte);
    }
    
    foreach ($werte as $feld => $wert) {
       $result[] = $module[$feld];
    }   
    return $result;	
	}
	
	function gibmodulLink($pordId){
		$typolink_conf = array(
            'no_cache' => false, 
            'returnLast' => "url", 
            'parameter' => $GLOBALS['TSFE']->id, 
            'additionalParams' => '&pordId=' . $pordId); 
		
		$get = t3lib_div::_GET();
		foreach ($get as $key=>$val) {
			$typolink_conf['additionalParams'] .= '&' . $key . '=' . $val;
		}
		return $this->cObj->typolink("", $typolink_conf);
	}
	
	function gibSemesterModuleAus($titel, $semesterDaten, $linksDeaktivieren=FALSE) {
		$semesterDatenSortiert = $this->sortiereModule($semesterDaten,'titel');
		$out = '<h2>' . $titel . '</h2>';
		$mode = 'hg_hellblau';
		foreach ($semesterDatenSortiert as $modulDaten) {
			$link = $this->gibmodulLink($modulDaten['pordID']);
			$modulTitel = $modulDaten['titel'];
			if ($mode == 'hg_hellblau') {
				$mode = 'weiss';
			} else {
				$mode = 'hg_hellblau';
			}
			if ($linksDeaktivieren) {
				$out .= '<div class="' . $mode . '"><span>' . $modulTitel . '</span></div>';
			} else {
				$out .= '<div class="' . $mode . '"><a href="' . $link . '">' . $modulTitel . '</a></div>';
			}
			
		}
		return $out;
	}

	function gibSemesterModulZeileAus($semester, $semesterDaten, $maxCol=4, $linksDeaktivieren=FALSE) {
		$anzModule = count($semesterDaten);
		$anzZeilen = ceil($anzModule/$maxCol);
		if ($anzZeilen>1) {
			$maxCol = ceil($anzModule/$anzZeilen);
		}
		$anzSpalten = count($semesterDaten);
		$semesterDatenSortiert = $this->sortiereModule($semesterDaten,'sortVert,titel');
		$startSpalte = 0;
		$spalte = 0;
		$out .= '<tr>';
		$out .= '<td class="semester">' . $semester . '</td>';
		$out .= '<td class="modulListe">';
		if ($anzSpalten>$maxCol) {
			$restSpalten = $anzSpalten;
			while ($restSpalten>$maxCol) {
				$out .= '<table class="modulLinks">';
				$out .= '<tr>';
				for ($spalte=$startSpalte;$spalte<$startSpalte+$maxCol;$spalte++) {
					$modulTitel = $semesterDatenSortiert[$spalte]['titel'];
					if ($spalte<$startSpalte+$maxCol-1) {
						$tdclass = 'modulLink border_bottom border_right col' . $maxCol;
					} else {
						$tdclass = 'modulLink border_bottom col' . $maxCol;
					}
//			$modulTitel .= 	'</br>(' . $modulDaten['ModulCode'] . ')';
					if (!empty($semesterDatenSortiert[$spalte]['vert'])) {
						$title = ' title="Vertiefung - ' . $semesterDatenSortiert[$spalte]['vert'] . '" ';
						$cssClass = ' class="' . $semesterDatenSortiert[$spalte]['css'] . '" ';
						$tdclass .= ' ' . $semesterDatenSortiert[$spalte]['css'];
					} else {
						$title = '';
						$cssClass = ' class="allgemein"';
						$tdclass .= ' allgemein';
					}
					$out .= '<td class="' . $tdclass . '">';
					if ($linksDeaktivieren) {
						$out .= '<span ' . $cssClass . $title . '>' . $modulTitel . '</span>';
					} else {
						$link = $this->gibmodulLink($semesterDatenSortiert[$spalte]['pordID']);
						$out .= '<a ' . $cssClass . $title . 'href="' . $link . '">' . $modulTitel . '</a>';
					}
					$out .= '</td>';
				}
				$restSpalten -= $maxCol;
				$startSpalte += $maxCol;
				$out .= '</tr>';
				$out .= '</table>';
			}
		}
		if ($spalte<$anzSpalten) {
			$restSpalten = $anzSpalten-$spalte;
			$out .= '<table class="modulLinks">';
			$out .= '<tr>';
			while ($spalte<$anzSpalten) {
				$modulTitel = $this->gibmodulTitel($semesterDatenSortiert[$spalte]['titel']);
				$faktor = ceil($restSpalten/($anzSpalten-$spalte));
				if ($spalte<$anzSpalten-1) {
					$tdclass = 'modulLink border_right col' . $restSpalten;
				} else {
					$tdclass = 'modulLink col' . $restSpalten;
				}
				if (!empty($semesterDatenSortiert[$spalte]['vert'])) {
					$title = ' title="Vertiefung - ' . $semesterDatenSortiert[$spalte]['vert'] . '" ';
					$cssClass = ' class="' . $semesterDatenSortiert[$spalte]['css'] . '" ';
					$tdclass .= ' ' . $semesterDatenSortiert[$spalte]['css'];
				} else {
					$title = '';
					$cssClass = ' class="allgemein"';
					$tdclass .= ' allgemein';
				}
				$out .= '<td class="' . $tdclass . '">';
				if ($linksDeaktivieren) {
					$out .= '<span ' . $cssClass . $title . '>' . $modulTitel . '</span>';
				} else {
					$link = $this->gibmodulLink($semesterDatenSortiert[$spalte]['pordID']);
					$out .= '<a ' . $cssClass . $title . 'href="' . $link . '">' . $modulTitel . '</a>';
				}
				$out .= '</td>';
				$spalte++;
			}
			$out .= '</tr>';
			$out .= '</table>';
		}
		$out .= '</td>';
		$out .= '</tr>';
		return $out;
	}

	function gibSemesterModulZeileAusMitVertiefungen($semester, &$semesterDaten, &$vertiefungen, $maxCol=4) {
		$anzModule = count($semesterDaten);
		$anzZeilen = ceil($anzModule/$maxCol);
		if ($anzZeilen>1) {
			$maxCol = ceil($anzModule/$anzZeilen);
		}
		$semesterDatenSortiert = $this->sortiereModule($semesterDaten,'sortVert,titel');
		$semesterDatenAllgemein = array();
		$semesterDatenVertiefungen = array();
		foreach ($vertiefungen as $kuerzel=>$titel) {
			$semesterDatenVertiefungen[$kuerzel] = array();
		}
		foreach($semesterDatenSortiert as $daten) {
			if (array_key_exists($daten['sortVert'],$vertiefungen)) {
				$semesterDatenVertiefungen[$daten['sortVert']][] = $daten;
			} else {
				$semesterDatenAllgemein[] = $daten;
			}
		}
		$anzSpalten = count($semesterDatenAllgemein);
		
		$startSpalte = 0;
		$spalte = 0;
		
/*
		$zeile = 0;
		$modulTabelleKomplett = array();
		if ($anzSpalten>$maxCol) {
			$restSpalten = $anzSpalten;
			while ($restSpalten>$maxCol) {
				$modulTabelleKomplett[$zeile] = array();
				for ($spalte=$startSpalte;$spalte<$startSpalte+$maxCol;$spalte++) {
					$modulTabelleKomplett[$zeile]['allgemein'][] = $semesterDatenAllgemein[$spalte];
				}
				foreach ($vertiefungen as $kuerzel=>$titel) {
					if (!empty($semesterDatenVertiefungen[$kuerzel][$zeile])) {
						$modulTabelleKomplett[$zeile]['vertiefungen'][$kuerzel] = $semesterDatenVertiefungen[$kuerzel][$zeile];
					} 
				}
				$restSpalten -= $maxCol;
				$startSpalte += $maxCol;
				$zeile++;
			}
		}
		if ($spalte<$anzSpalten) {
			$modulTabelleKomplett[$zeile] = array();
			while ($spalte<$anzSpalten) {
				$modulTabelleKomplett[$zeile]['allgemein'][] = $semesterDatenAllgemein[$spalte];				
				$spalte++;
			}
			foreach ($vertiefungen as $kuerzel=>$titel) {
				if (!empty($semesterDatenVertiefungen[$kuerzel][$zeile])) {
					$modulTabelleKomplett[$zeile]['vertiefungen'][$kuerzel] = $semesterDatenVertiefungen[$kuerzel][$zeile];
				} 
			}
		}
if ($GLOBALS['TSFE']->fe_user->user['username']=='mmirsch') {
	t3lib_utility_Debug::debugInPopUpWindow($modulTabelleKomplett);
}		
		$out .= '<tr>';
		$out .= '<td class="semester">' . $semester . '</td>';
		$out .= '<td class="modulListe allgemein">';
		foreach ($modulTabelleKomplett as $zeile=>$daten) {
			$out .= '<table class="modulLinks">';
			$out .= '<tr>';
			$spalte = 0;
			foreach ($daten['allgemein'] as $semesterDaten) {
				$linkUrl = $this->gibmodulLink($semesterDaten['pordID']);
				$modulTitel = $semesterDaten['titel'];
				if ($spalte<$maxCol-1) {
					$tdclass = 'modulLink border_bottom border_right col' . $maxCol;
				} else {
					$tdclass = 'modulLink border_bottom col' . $maxCol;
				}
				$title = '';
				$tdclass .= ' allgemein';
				$out .= '<td class="' . $tdclass . '">';
				$out .= '<a href="' . $linkUrl . '">' . $modulTitel . '</a>';
				$out .= '</td>';
				$spalte++;
			}
			$vertiefungsIndex = 0;
			foreach ($vertiefungen as $kuerzel=>$titel) {
				if ($spalte<$maxCol-1) {
					$tdclass = 'modulLink vertiefung border_bottom border_right col' . $maxCol;
				} else {
					$tdclass = 'modulLink vertiefung border_bottom col' . $maxCol;
				}
				$tdclass .= ' vert_' . $vertiefungsIndex;
				$cssClass = ' class=" vert_' . $vertiefungsIndex . '" ';
				if (!empty($daten['vertiefungen'][$kuerzel])) {
					$linkUrl = $this->gibmodulLink($daten['vertiefungen'][$kuerzel]['pordID']);
					$modulTitel = $daten['vertiefungen'][$kuerzel]['titel'];
					$link = '<a href="' . $linkUrl . '">' . $modulTitel . '</a>';
				}
				$out .= '<td class="' . $tdclass . '">';
				$out .= $link;
				$out .= '</td>';
				$vertiefungsIndex++;
			}
			$out .= '</tr>';
			$out .= '</table>';
		}
		$out .= '</td>';
		$out .= '</tr>';
		return $out;
*/		
		
		$out .= '<tr>';
		$out .= '<td class="semester">' . $semester . '</td>';
		$out .= '<td class="modulListe allgemein">';
		$anzZeilen = 0;
		if ($anzSpalten>$maxCol) {
			$restSpalten = $anzSpalten;
			while ($restSpalten>$maxCol) {
				$anzZeilen++;
				$out .= '<table class="modulLinks">';
				$out .= '<tr>';
				for ($spalte=$startSpalte;$spalte<$startSpalte+$maxCol;$spalte++) {
					$link = $this->gibmodulLink($semesterDatenAllgemein[$spalte]['pordID']);
					$modulTitel = $semesterDatenAllgemein[$spalte]['titel'];
					if ($spalte<$startSpalte+$maxCol-1) {
						$tdclass = 'modulLink border_bottom border_right col' . $maxCol;
					} else {
						$tdclass = 'modulLink border_bottom col' . $maxCol;
					}
					//			$modulTitel .= 	'</br>(' . $modulDaten['ModulCode'] . ')';
					if (!empty($semesterDatenAllgemein[$spalte]['vert'])) {
						$title = ' title="Vertiefung - ' . $semesterDatenAllgemein[$spalte]['vert'] . '" ';
						$cssClass = ' class="' . $semesterDatenAllgemein[$spalte]['css'] . '" ';
						$tdclass .= ' ' . $semesterDatenAllgemein[$spalte]['css'];
					} else {
						$title = '';
						$cssClass = ' class="allgemein"';
						$tdclass .= ' allgemein';
					}
					$out .= '<td class="' . $tdclass . '">';
					$out .= '<a ' . $cssClass . $title . 'href="' . $link . '">' . $modulTitel . '</a>';
					$out .= '</td>';
				}
				$restSpalten -= $maxCol;
				$startSpalte += $maxCol;
				$out .= '</tr>';
				$out .= '</table>';
			}
		}
		if ($spalte<$anzSpalten) {
			$restSpalten = $anzSpalten-$spalte;
			$anzZeilen++;
			$out .= '<table class="modulLinks">';
			$out .= '<tr>';
			while ($spalte<$anzSpalten) {
				$link = $this->gibmodulLink($semesterDatenAllgemein[$spalte]['pordID']);
				$modulTitel = $this->gibmodulTitel($semesterDatenAllgemein[$spalte]['titel']);
				if ($spalte<$anzSpalten-1) {
					$tdclass = 'modulLink border_right col' . $restSpalten;
				} else {
					$tdclass = 'modulLink col' . $restSpalten;
				}
				if (!empty($semesterDatenAllgemein[$spalte]['vert'])) {
					$title = ' title="Vertiefung - ' . $semesterDatenAllgemein[$spalte]['vert'] . '" ';
					$cssClass = ' class="' . $semesterDatenAllgemein[$spalte]['css'] . '" ';
					$tdclass .= ' ' . $semesterDatenAllgemein[$spalte]['css'];
				} else {
					$title = '';
					$cssClass = ' class="allgemein"';
					$tdclass .= ' allgemein';
				}
				$out .= '<td class="' . $tdclass . '">';
				$out .= '<a ' . $cssClass . $title . 'href="' . $link . '">' . $modulTitel . '</a>';
				$out .= '</td>';
				$spalte++;
			}
			$out .= '</tr>';
			$out .= '</table>';
		}
		$out .= '</td>';
		$i = 1;
		foreach ($vertiefungen as $kuerzel=>$titel) {
			$out .= '<td class="modulListe vertiefung vert_' . $i . '">';
			foreach ($semesterDatenVertiefungen[$kuerzel] as $modulDaten) {
				$link = $this->gibmodulLink($modulDaten['pordID']);
				$modulTitel = $this->gibmodulTitel($modulDaten['titel']);
				if (count($semesterDatenVertiefungen[$kuerzel])>1 && $i==1) {
					$cssClass = 'modulLinks first';
				} else {
					$cssClass = 'modulLinks';
				}
				$out .= '<table class="' . $cssClass . '">';
				$out .= '<tr>';
				$out .= '<td class="modulLink col1">';
				$out .= '<a href="' . $link . '">' . $modulTitel . '</a>';
				$out .= '</td>';
				$out .= '</tr>';
				$out .= '</table>';
			}
			$i++;
			$out .= '</td>';
		}
		
		$out .= '</tr>';
		return $out;
	}
	
	function &XML_unserialize(&$xml){
		$xml_parser = new XML();
		$data = $xml_parser->parse($xml);
		$xml_parser->destruct();
		return $data;
	}
	
	function gibModulCss() {
		$out .= '<style type="text/css">';
		$out .= 'table.modul_table {
							 width: ' . $this->tableWidth . 'px;
							 background: #004666; 
							 color: #fff;
							 border-collapse: collapse;
						 }
						 table.modul_table tr {
							border-left: 1px solid #fff;
							border-top: 1px solid #fff;
						 }
						 table.modul_table td,
						 table.modul_table th {
							border: 1px solid #fff;
							padding: 4px;
							margin: 0;
							verticle-align: middle;
							text-align: center;
						 } 
						 table.modul_table a {
							color: #fff;
							text-decoration: none;
						 } 
						';
		$out .= '</style>';
		return $out;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_tools/lib/class.tx_he_tools_lsf.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_tools/lib/class.tx_he_tools_lsf.php']);
}
?>