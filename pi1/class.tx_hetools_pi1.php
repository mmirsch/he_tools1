<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Manfred Mirsch <Manfred.Mirsch@hs-esslingen.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(t3lib_extMgm::extPath('he_tools').'lib/globals.php');
require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_util.php');
require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_rz_skripte.php');
require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_suchergebnisse.php');
require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_zeitschriftenlisten.php');
require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_powermail.php');
require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_module.php');
require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_kms_formular.php');
require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_einfuehrung_anmeldungen.php');
require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_gast_kennungen.php');
require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_lsf.php');
require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_lib_db_suche.php');
require_once(t3lib_extMgm::extPath('he_personen').'lib/class.tx_he_personen_util.php');
require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_calexport.php');
require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_infoscreen.php');
require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_veranstaltungs_buchung.php');
require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_mensa.php');
require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_spezialfunktionen.php');
require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_spezial_elemente.php');
require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_hochschulexpress.php');
require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_online_sb.php');
require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_echug.php');
require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_jqplot.php');
require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_technolino.php');
require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_wetterstation.php');
require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_gu_qr_admin.php');

/*
 * Plugin 'Plugins' for the 'he_tools' extension.
 *
 * @author	Manfred Mirsch <Manfred.Mirsch@hs-esslingen.de>
 * @package	TYPO3
 * @subpackage	tx_hetools
 */
class tx_hetools_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_hetools_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.hetools_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'he_tools';	// The extension key.
	var $pi_checkCHash = true;
	var $ap_admin;
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf) {
		$id = $GLOBALS['TSFE']->id;

		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_initPIflexForm();
		$modus = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'mode');
		$host = t3lib_div::getIndpEnv('HTTP_HOST');
		$this->pageLink = $this->pi_getPageLink($id);
		$get = t3lib_div::_GET();

		switch ($modus) {
		case "PRAXIS":
			$pmAdmin = new tx_he_tools_powermail();
			$content = $pmAdmin->admin($conf, $this->cObj);
			break; 
		case "PRAXIS_AAA":
			$pmAdmin = new tx_he_tools_powermail();
			$content = $pmAdmin->exportFormularAAA();
			break; 
		case "POWERMAIL_EXPORT_STUDIENBERATUNG":
			$pmAdmin = new tx_he_tools_powermail();
			$content = $pmAdmin->exportFormularStudienberatung();
			break; 
		case "RZ_VERFUEGBARKEIT":
			$content = tx_he_tools_rz_skripte::rz_verfuegbarkeit();
			break;
		case "RZ_ACCESS_POINTS":
			$content = tx_he_tools_rz_skripte::rz_access_points();
			break;
		case "RZ_WETTER_AKTUELL_KURZ":
			$GLOBALS["TSFE"]->set_no_cache();
			/**@var $wetterStation tx_he_tools_wetterstation */
			$wetterStation = t3lib_div::makeInstance('tx_he_tools_wetterstation');
			$content = $wetterStation->aktuellesWetter();
			break;
		case "RZ_WETTER_AKTUELL_DETAILS":
			$GLOBALS["TSFE"]->set_no_cache();
			/**@var $wetterStation tx_he_tools_wetterstation */
			$wetterStation = t3lib_div::makeInstance('tx_he_tools_wetterstation');
			$content = $wetterStation->aktuellesWetter(true);
			break;
		case "RZ_WETTER_FORMULAR":
			$GLOBALS["TSFE"]->set_no_cache();
			/**@var $wetterStation tx_he_tools_wetterstation */
			$wetterStation = t3lib_div::makeInstance('tx_he_tools_wetterstation');
			$content = $wetterStation->wetterFormular();
			break;
		case "ZEITSCHRIFTENLISTE":
			$zeitschriften = new tx_he_tools_zeitschriftenlisten($this->extKey);
			$content = $zeitschriften->renderZeitschriftenListe();
			break; 
		case "SUCHERGEBNISSE":
			if ($get['mode'] && $get['eingabe']) {
				$obj = new tx_he_tools_suchergebnisse($this->extKey);			
				$content = $obj->suchergebnisse($get['mode'],$get['eingabe']);
			} 
			break; 
		case "VERWENDUNG_STUDIENGEBUEHREN":
			$content = tx_he_tools_rz_skripte::studiengebuehrenListe($this->pageLink);
			break; 
		case "MOTD":
			$content = tx_he_tools_rz_skripte::motd();
			break;
		case "MODUL_TABELLE":
			$studiengang = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'studiengang');
			$schwerpunkt = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'schwerpunkt');
			$spoVersion = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'spo_version');
			$module = new tx_he_tools_module();
			$content = $module->modulUebersicht($conf, $this, $studiengang, $schwerpunkt, $spoVersion);
			break;
		case "MODUL_HANDBUCH":
			$studiengang = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'studiengang');
			$schwerpunkt = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'schwerpunkt');
			$spoVersion = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'spo_version');
			$module = new tx_he_tools_module();
			$content = $module->modulHandbuch($conf, $this, $studiengang, $schwerpunkt, $spoVersion);
			break;
		case "MODUL_TABELLE_LSF":
			$GLOBALS["TSFE"]->set_no_cache();			
			$studiengang = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'studiengang');
			$schwerpunkt = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'schwerpunkt');
			$spoVersion = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'spo_version');
			$linksDeaktivieren = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'links_deaktivieren');
			$module = new tx_he_tools_module();
			$content = $module->modulUebersicht_lsf($this, $studiengang, $schwerpunkt, $spoVersion);
			break;
		case "MODUL_HANDBUCH_LSF":
			$GLOBALS["TSFE"]->set_no_cache();			
			$studiengang = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'studiengang');
			$schwerpunkt = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'schwerpunkt');
			$spoVersion = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'spo_version');
			$module = new tx_he_tools_module();
			$content = $module->modulHandbuch_lsf($this, $studiengang, $schwerpunkt, $spoVersion);
			break;
		case "MODULUEBERSICHT_LSF":
			$GLOBALS["TSFE"]->set_no_cache();			
			$studiengang = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'studiengang_lsf');
			$vertiefung = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'vertiefung_lsf');
			$darstellung = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'darstellungs_art');
			$spoVersion = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'spo_version');
			$maxCol = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'max_col');
			$linksDeaktivieren = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'links_deaktivieren');
			$module = new tx_he_tools_module();
			$content = $module->modulUebersicht_lsfNeu($this, $studiengang, $vertiefung, $spoVersion,$darstellung,$maxCol,$linksDeaktivieren);
			break;
		case "MODULUEBERSICHT_LSF_VERT":
			$GLOBALS["TSFE"]->set_no_cache();			
			$studiengang = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'studiengang_lsf');
			$vertiefung = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'vertiefung_lsf');
			$darstellung = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'darstellungs_art');
			$spoVersion = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'spo_version');
			$maxCol = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'max_col');
			$module = new tx_he_tools_module();
			$content = $module->modulUebersicht_lsf_vertiefungen($this, $studiengang, $vertiefung, $spoVersion,$darstellung,$maxCol);
			break;
		case "MODULHANDBUCH_LSF":
			$GLOBALS["TSFE"]->set_no_cache();			
			$studiengang = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'studiengang_lsf');
			$vertiefung = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'vertiefung_lsf');
			$spoVersion = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'spo_version');
			$module = new tx_he_tools_module();
			$content = $module->modulHandbuch_lsfNeu($this, $studiengang, $vertiefung, $spoVersion);
			break;
		case "MODULE_ENGLISCH_LSF_VERANSTALTUNGEN":
			//$GLOBALS["TSFE"]->set_no_cache();			
			$module = new tx_he_tools_lsf($this->cObj);
			$content = $module->veranstaltungenEnglisch();
			break;
		case "MODULE_ENGLISCH_LSF_MODULBESCHREIBUNGEN":
			//$GLOBALS["TSFE"]->set_no_cache();			
			$module = new tx_he_tools_lsf($this->cObj);
			$content = $module->moduleEnglisch();
			break;
		case "MODULE_ENGLISCH_LSF_TEILLEISTUNGEN":
			//$GLOBALS["TSFE"]->set_no_cache();			
			$module = new tx_he_tools_lsf($this->cObj);
			$content = $module->teilleistungenEnglisch();
			break;
		case "MODULE_ENGLISCH_LSF_TEILLEISTUNGEN":
			//$GLOBALS["TSFE"]->set_no_cache();			
			$module = new tx_he_tools_lsf($this->cObj);
			$content = $module->teilleistungenEnglisch();
			break;
		case "MODULE_ENGLISCH_TYPO3":
			$content = tx_he_tools_rz_skripte::modulbeschreibungenEnglisch($this);
			break;
		case "SOAP_TEST_LSF":
			if ($GLOBALS['TSFE']->fe_user->user['username']!='hemp' && $GLOBALS['TSFE']->fe_user->user['username']!='mmirsch') {
				$content = 'Kein Zugriff';
			} else {
				$GLOBALS["TSFE"]->set_no_cache();
				$module = new tx_he_tools_lsf($this->cObj);
				$content = $module->soapTest();
			}
			break;
		case "GADGETS":
			$gadget = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'gadgetkuerzel');
			$content = $this->eigeneGadgets($gadget);
			break;
		case "TOOLS":
			$kuerzel = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'toolskuerzel','settings');
			$content = $this->tools($kuerzel,$get,$conf);
			break;
		case "DATA_VIEW":
			$elem = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'dataview');
			$content = $this->dataView($elem,$get);
			break;
		case "HOCHSCHULEXPRESS":
			$bild = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'hex_image');
			$bereich = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'hex_bereichstitel');
			$ueberschrift = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'hex_ueberschrift');
			$spalteLinks = $this->pi_RTEcssText($this->pi_getFFvalue($this->cObj->data['pi_flexform'],'hex_spalte_links'));
			$spalteRechts = $this->pi_RTEcssText($this->pi_getFFvalue($this->cObj->data['pi_flexform'],'hex_spalte_rechts'));
			$hochschulexpress = t3lib_div::makeInstance('tx_he_tools_hochschulexpress');
			$content = $hochschulexpress->bereichAnzeigen($this->cObj,$bild,$bereich,$ueberschrift,$spalteLinks,$spalteRechts);
			break;
		case "HOCHSCHULTICKER":
			$bild = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'hex_image');
			$bereich = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'hex_bereichstitel');
			$spalteRechts = $this->pi_RTEcssText($this->pi_getFFvalue($this->cObj->data['pi_flexform'],'hex_spalte_rechts'));
			$hochschulexpress = t3lib_div::makeInstance('tx_he_tools_hochschulexpress');
			$content = $hochschulexpress->bereichAnzeigenZweispaltig($this->cObj,$bild,$bereich,$spalteRechts);
			break;
		case "FAQ_EINTRAEGE":
			$eintraege = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'faq_eintraege');
			$layout = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'faq_layout');
			$faqs = t3lib_div::makeInstance('tx_he_tools_spezial_elemente');
			$pidList = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_hetools_pi1.']['pid_wussten_sie_schon'];
			$content = $faqs->faqs($this,$eintraege,$layout,$pidList);
			break;
		case "ECHUG":
			$funktion = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'echug_funktionen');
			$echug = t3lib_div::makeInstance('tx_he_tools_echug');
			if ($funktion=='UMFRAGE') {
				$content = $echug->umfrage();
			} elseif ($funktion=='AUSWERTUNG') {
				$content = $echug->auswertung();
			}
			break;
		case 'ONLINE_SB':
			$GLOBALS["TSFE"]->set_no_cache();
			$funktion = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'sb_funktion');
			$onlineSb = t3lib_div::makeInstance('tx_he_tools_online_sb',$this);
			
			switch ($funktion) {
				case 'LOGIN_WARNUNG':
					$content = $onlineSb->pruefeAnmeldeZustand();
					break;
				case 'CREATE_FE_USER':
					$content = $onlineSb->benutzerAnlegen();
					break;
				case 'LOGIN_FORMULAR':
					$content = $onlineSb->loginFormular();
					break;
				case 'SHOW_REG_DATA':
					$content = $onlineSb->zeigeRegistrierungsdaten();
					break;
				case 'ANFRAGEN_BEARBEITEN':
					$content = $onlineSb->anfrageBearbeiten();
					break;
				case 'ANFRAGEN_ANZEIGEN':
					$content = $onlineSb->anfragenAnzeigen();
					break;
				case 'LOGOUT_FORMULAR':
					$content = $onlineSb->logoutFormular();
					break;
				case 'ANFRAGENSTATISTIK':
					$content = $onlineSb->anfragenstatistikAnzeigen();
					break;
					
				default:
					$content = '<h3>noch nicht implementiert : ' . $funktion . '</h3>';
			}
			break;
    case "TEST":
			$test = t3lib_div::makeInstance('tx_he_tools_jqplot');
			$content = $test->main();
			break;
			
		}
		return $this->pi_wrapInBaseClass($content);
	}

	function eigeneGadgets($kuerzel) {
		$dbSuche = t3lib_div::makeInstance('tx_he_tools_lib_db_suche');
		switch ($kuerzel) {
		case 'PERS':
			$out = $dbSuche->personenSuche($this);
			break;
		case "HOCHSCHULE_A_BIS_Z":
			$out = $dbSuche->hochschuleABisZSucheGadget($this);
			break; 
		case 'RAUM':
			$out = $dbSuche->raumSuche($this);
			break;
		case 'VVS':
			$spezial = t3lib_div::makeInstance('tx_he_tools_spezial_elemente');
			$out = $spezial->showVvsIframe($this);
			break;
		default:
			$out = '<h3>noch nicht implementiert: ' . $kuerzel . '</h3>';	
		}
		return $out;
	}
	
	function dataView($kuerzel, $get) {
		$dbSuche = t3lib_div::makeInstance('tx_he_tools_lib_db_suche');
		switch ($kuerzel) {
		case 'ABFALL_A_BIS_Z':
			$eingabe = $get['eingabe'];
			$buchstabe = $get['buchstabe'];
			$out = $dbSuche->ajaxContentForm('abfallABisZ',$this,$eingabe,$buchstabe);
			break;
		case "HOCHSCHULE_A_BIS_Z":
			$eingabe = $get['eingabe'];
			$buchstabe = $get['buchstabe'];
			$out = $dbSuche->ajaxContentForm('hochschuleABisZ',$this,$eingabe,$buchstabe);
			break; 
		case 'EDV_VORZUGSLISTE':
			$data['eingabe'] = $get['eingabe'];
			$data['buchstabe'] = $get['buchstabe'];
			$data['pid'] = $this->conf['pid'];
			$data['seiten'] = $this->conf['seiten.'];
			$out = $dbSuche->ajaxContentForm('edvVorzugsListe',$data);
			break;
		case 'SHOP_GUP':
			$data['eingabe'] = $get['eingabe'];
			$data['buchstabe'] = $get['buchstabe'];
			$data['pid'] = $this->conf['pid'];
			$out = $dbSuche->ajaxContentForm('shopBueromaterialGup',$data);
			break;
		default:
			$out = '<h3>noch nicht implementiert: ' . $kuerzel . '</h3>';	
		}
		return $out;
	}
	
	function tools($kuerzel,$get,&$conf='') {

		$dbSuche = t3lib_div::makeInstance('tx_he_tools_lib_db_suche');
		switch ($kuerzel) {
		case 'CONTENTELEMENT':
			$uid = $get['uid'];
			$out = tx_he_tools_util::renderContentElem($this->cObj,$uid);
			break;
		case 'HOCHSCHULE_A_BIS_Z':
			$eingabe = $get['eingabe'];
			$buchstabe = $get['buchstabe'];
			$out = $dbSuche->hochschuleABisZSucheContent($this,$eingabe,$buchstabe);
			break; 
		case 'ABFALL_A_BIS_Z':
			$eingabe = $get['eingabe'];
			$buchstabe = $get['buchstabe'];
			$out = $dbSuche->abfallABisZSucheContent($this,$eingabe,$buchstabe);
			break; 
		case "CAL_EXPORT":
			$calexport = new tx_he_tools_calexport($this->extKey);
			$out = $calexport->main();
			break;	
		case "FLINC":
			$out = tx_he_tools_rz_skripte::flinc();
			break;
		case 'WER_MACHT_WAS_A_BIS_Z':
			$eingabe = $get['eingabe'];
			$buchstabe = $get['buchstabe'];
			$out = $dbSuche->werMachtWasABisZSucheContent($this,$eingabe,$buchstabe);
			break; 
		case 'GOOGLE_MAPS':
			$url = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'iframe_url','settings');
			$hoehe = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'iframe_hoehe','settings');
			$out = tx_he_tools_util::renderIframe($url,$hoehe);
			break;
		case 'INFOSCREEN':
			$GLOBALS["TSFE"]->set_no_cache();			
			$app = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'infoscreen_app','settings');
			$infoscreenDelay = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'infoscreen_delay','settings');
			$delay = $infoscreenDelay*1000;
			$infoscreenForceReload = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'infoscreen_force_reload','settings');
			$reload = $infoscreenForceReload*60;
			$app = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'infoscreen_app','settings');
			$infoScreen = t3lib_div::makeInstance('tx_he_tools_infoscreen',$this->cObj,$delay,$reload);
			if ($app=='MAIN') {
				return $infoScreen->redirectMain();
			} else if ($app=='VIDEO') {
				$videoDatei = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'infoscreen_video','settings');
				return $infoScreen->zeigeVideo($videoDatei);
			} else if ($app=='UEBERSICHT') {
				$GLOBALS["TSFE"]->set_no_cache();
				return $infoScreen->zeigeUebersichtsSeite();
			} else if ($app=='SEITENINHALT') {
				$dauerAnzeige = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'infoscreen_cal_daueranzeige','settings');
				$dauerUebergang = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'infoscreen_cal_daueruebergang','settings');
				$sponsoren = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'infoscreen_sponsoren','settings');
				$gebaeude = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'infoscreen_gebaeude','settings');
				$ttContentId = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'infoscreen_seiteninhalt','settings');
				return $infoScreen->zeigeSeitenInhalt($ttContentId,$dauerAnzeige*1000,$dauerUebergang*1000,$sponsoren,$gebaeude);
			} else if ($app=='SEITENINHALT_LISTE') {
				$dauerAnzeige = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'infoscreen_cal_daueranzeige','settings');
				$dauerUebergang = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'infoscreen_cal_daueruebergang','settings');
				$sponsoren = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'infoscreen_sponsoren','settings');
        $gebaeude = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'infoscreen_gebaeude','settings');
        $standardText = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'infoscreen_standard_anzeigetext','settings');
				$ttContentPid = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'infoscreen_seiteninhalt_seite','settings');
				return $infoScreen->zeigeSeitenInhaltListe($ttContentPid,$dauerAnzeige*1000,$dauerUebergang*1000,$sponsoren,$gebaeude,$standardText);
			} else if ($app=='FLEXIBEL') {
				$standardmeldung = 'Herzlich willkommen an der Hochschule Esslingen';
				$dauerAnzeige = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'infoscreen_cal_daueranzeige','settings');
				$dauerUebergang = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'infoscreen_cal_daueruebergang','settings');
				$sponsoren = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'infoscreen_sponsoren','settings');
				$gebaeude = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'infoscreen_gebaeude','settings');
				$standort = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'infoscreen_standort','settings');
				$elemente = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'infoscreen_elemente','settings');
				return $infoScreen->zeigeFlexiblenInhalt($standardmeldung,$elemente,$dauerAnzeige*1000,$dauerUebergang*1000,$sponsoren,$gebaeude,$standort);
			} else {
				$standardmeldung = 'Herzlich willkommen an der Hochschule Esslingen';
				$category = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'infoscreen_cal_cat','settings');
				$dauerAnzeige = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'infoscreen_cal_daueranzeige','settings');
				$dauerUebergang = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'infoscreen_cal_daueruebergang','settings');
				$sponsoren = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'infoscreen_sponsoren','settings');
				$gebaeude = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'infoscreen_gebaeude','settings');
				$standort = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'infoscreen_standort','settings');
				return $infoScreen->zeigeKalendertermine($category,$standardmeldung,$dauerAnzeige*1000,$dauerUebergang*1000,$sponsoren,$gebaeude,$standort);
			}
			break;
		case 'CAMPUS_LEBEN':

			$imgLink = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'campus_leben_imglink','settings');
			$link = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'campus_leben_link','settings');
			$email = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'campus_leben_email','settings');
			return tx_he_tools_rz_skripte::campusLeben($link,$imgLink,$email);
			break;
		case 'VERANSTALTUNGEN':
      $funktion = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'veranstaltungen','settings');
      $out = $this->veranstaltungen($funktion,$conf,$get);
      break;
		case 'KMS_ANTRAG':
			$GLOBALS["TSFE"]->set_no_cache();			
			$kmsFormular = t3lib_div::makeInstance('tx_he_tools_kms_formular');
			$out = $kmsFormular->formularAnzeigen();
			break;						
		case 'GASTKENNUNG_BEANTRAGEN':
			$GLOBALS["TSFE"]->set_no_cache();
      /** @var tx_he_tools_gast_kennungen $gastFormular */
			$gastFormular = t3lib_div::makeInstance('tx_he_tools_gast_kennungen');
			$out = $gastFormular->formularAnzeigen();
			break;						
		case 'GASTKENNUNG_VERWALTEN':
			$GLOBALS["TSFE"]->set_no_cache();
      /** @var tx_he_tools_gast_kennungen $gastFormular */
			$gastFormular = t3lib_div::makeInstance('tx_he_tools_gast_kennungen');
			$out = $gastFormular->kennungenVerwalten();
			break;						
		case 'RZ_EINFUEHRUNG_ANMELDUNG':
			$einfuehrungAnmeldungen = t3lib_div::makeInstance('tx_he_tools_einfuehrung_anmeldungen');
			$out = $einfuehrungAnmeldungen->main();
			break;						
		case 'HTACCESS_HILFE':
			$out = tx_he_tools_util::htaccessErzeugen();
			break;
		case 'MENSA_SPEISEPLAN':
			$mensa = t3lib_div::makeInstance('tx_he_tools_mensa');
			$out = $mensa->zeigeMensaDaten($this);
			break;
		case 'SPEZIAL':
			$spezialFunktion = t3lib_div::makeInstance('tx_he_tools_spezialfunktionen');
			$funktion = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'spezialfunktionen','settings');
			return $spezialFunktion->main($funktion);
			break;
		case 'SHIB_LOGIN_REDIRECT':
			$anzeigeText = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'login_anzeigetext','settings');
			return tx_he_tools_rz_skripte::erzeugeShibLoginMitRedirect($anzeigeText);
			break;
		case 'SHOW_BROWSERINFO':
			return tx_he_tools_rz_skripte::showBrowserInfo();
		case 'SHOP_ROEM_GESCHENKE':
			return tx_he_tools_rz_skripte::roemGeschenke($this->cObj);
		case 'GU_QR_ADMIN':
      $GLOBALS["TSFE"]->set_no_cache();
      $guQrAdmin = t3lib_div::makeInstance('tx_he_tools_gu_qr_admin');
      return $guQrAdmin->main($this->cObj,$get);
		default:
			$out = '<h3>noch nicht implementiert : ' . $kuerzel . '</h3>';	
		}
		return $out;
	}

  function veranstaltungen($funktion,&$conf,$get) {
    $GLOBALS["TSFE"]->set_no_cache();
    switch ($funktion) {
      case 'VERANSTALTUNGS_ANMELDUNG':
        /** @var  $veranstaltungen tx_he_tools_veranstaltungs_buchung */
        $veranstaltungen = t3lib_div::makeInstance('tx_he_tools_veranstaltungs_buchung');
        $out = $veranstaltungen->main($conf);
      break;
      case 'TECHNOLINO_TERMINANZEIGE':
        /** @var  $veranstaltungen tx_he_tools_technolino */
        $veranstaltungen = t3lib_div::makeInstance('tx_he_tools_technolino');
        if (isset($get['bookEvent']) &&
            isset($get['eventId']) &&
            isset($get['eventDateId'])
            ) {
          $out = $veranstaltungen->veranstaltungBuchen($this,137186,$get['eventId'],$get['eventDateId']);
        } else {
          $out = $veranstaltungen->zeige_termine($this,137186);
        }


        break;
      case 'TECHNOLINO_DATENEXPORT':
        /** @var  $veranstaltungen tx_he_tools_technolino */
        $veranstaltungen = t3lib_div::makeInstance('tx_he_tools_technolino');
        $out = $veranstaltungen->datenexport($this,137186);
        break;
    }
    return $out;
  }

	function intranetCheck() {
		tx_he_tools_util::pruefeIntranetZugriff();
	}
	
	function intranetZugriff() {
		return	tx_he_tools_util::intranetZugriff();
	}
	
	function create_breadcrumb() {
		$get = t3lib_div::_GET();
		$pageId = $get['id'];
		if (empty($pageId)) {
			$pageId = $GLOBALS['TSFE']->id;
		}
		if ( tx_he_personen_util::istPersonenSeite()) {
			return	tx_he_personen_util::erzeugePersonenRootline($this,$pageId);
		} else {
			return	tx_he_tools_util::erzeugeRootline($this,$pageId);
		}
	}

  function createMainNav() {
    $this->pi_USER_INT_obj = 0;
    return	tx_he_tools_util::createMainNavHe($GLOBALS['TSFE']->id,$this);
  }

  function createNavQuicklinks() {
    $this->pi_USER_INT_obj = 0;
    return	tx_he_tools_util::createMainNavHe(38289,$this);
  }

  function getPortalBanner() {
		$image = 'hochschulexpress_portal.jpg';
		$seite = $GLOBALS['TSFE']->id;
		$get = t3lib_div::_GET();
		
		$mp = $get['MP'];
		$sysPageObj = t3lib_div::makeInstance('t3lib_pageSelect');
		$rootLineArray = $sysPageObj->getRootLine($seite,$mp);
		foreach ($rootLineArray as $daten) {
			if (!empty($daten['_MOUNT_PAGE']['uid'])) {
				$mountSeite = $daten['_MOUNT_PAGE']['uid'];
				$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('media','pages',
						'deleted=0 AND hidden=0 AND uid=' . $mountSeite);
				if ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
					$image = $daten['media'];
					break;
				}
			} else if (!empty($daten['media'])) {
				$image = $daten['media'];
				break;
			}
		}
		return '<div class="bannerfoto" style="background: url(/uploads/media/' . $image . ') 220px top no-repeat transparent;"></div>';
	}
	
	function geschuetzteSeitenAusblenden($menuArr ,$conf) {
		$new_menuArr = array();
		foreach ($menuArr as $eintrag) {
			if ($this->seiteAnzeigen($eintrag)) {
				$new_menuArr[] = $eintrag;
			}
		}
//t3lib_div::devLog(print_r($del_menuArr,true), 'geschuetzteSeitenAusblenden', 0);
		return $new_menuArr;
	}

	function seiteAnzeigen($seite) {
		if (tx_he_tools_util::intranetZugriff() || $seite[tx_six2t3_intranet]!=1) {
			return true;
		} else {
			return false;
		}
	}
	
	function keineRobots() {
  	if (preg_match('/robot|spider|crawler|curl|^$/i', $_SERVER['HTTP_USER_AGENT'])) {
			header("HTTP/1.0 404 Not Found");
			header('Location: http://www.hs-esslingen.de/de/fehler.html');
  		exit();
  	}
	}
	
	function rechte_box_suchen($uid,$startSeite) {
		$rechteBox = FALSE;
		$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid','tt_content',
																			'deleted=0 AND hidden=0 ' . 
																			' AND tx_templavoila_to=' . TEMPLAVOILA_TO_RECHTE_BOX .
																			' AND CType="templavoila_pi1" AND pid=' . $uid);
		if ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
			$GLOBALS['TYPO3_DB']->sql_free_result($abfrage);
			$abfragePages = $GLOBALS['TYPO3_DB']->exec_SELECTquery('pid','pages',
																				'deleted=0 ' .
																				' AND tx_templavoila_flex LIKE "%' . $daten[uid] . '%"' . 
																				' AND uid=' . $uid);
			if ($datenPages = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfragePages)) {
				$GLOBALS['TYPO3_DB']->sql_free_result($abfragePages);
				if ($startSeite) {
					return FALSE;
				} else {
					$rechteBox = $daten[uid];				
				}
			}
		} 
		if (!$rechteBox) {
			$abfragePages = $GLOBALS['TYPO3_DB']->exec_SELECTquery('pid','pages',
																				'deleted=0 AND uid=' . $uid);
			if ($datenPages = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfragePages)) {
				$GLOBALS['TYPO3_DB']->sql_free_result($abfragePages);
				$rechteBox = $this->rechte_box_suchen($datenPages[pid],FALSE);
			}
		}
		return $rechteBox;
	}

	function rechte_box_suchen_neu($uid,$startSeite) {
		$rechteBox = FALSE;
		$boxUid = 0;
		$abfrageTtContent = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid','tt_content',
																			'deleted=0 AND hidden=0 ' . 
																			' AND tx_templavoila_to=' . TEMPLAVOILA_TO_RECHTE_BOX .
																			' AND CType="templavoila_pi1" AND pid='.$uid);
		while ($boxUid==0 && 
					 $daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageTtContent)) {
			$abfragePages = $GLOBALS['TYPO3_DB']->exec_SELECTquery('pid','pages',
																				'deleted=0 AND hidden=0 ' .
																				' AND tx_templavoila_flex LIKE "%' . $daten[uid] . '%"' . 
																				' AND uid=' . $uid);
			if ($datenPages = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfragePages)) {
				$boxUid = $daten[uid];
			}
		}
		$GLOBALS['TYPO3_DB']->sql_free_result($abfrageTtContent);
		if ($boxUid==0) {
			$abfragePages = $GLOBALS['TYPO3_DB']->exec_SELECTquery('pid','pages',
																				'deleted=0 AND uid=' . $uid);
			if ($datenPages = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfragePages)) {
				$GLOBALS['TYPO3_DB']->sql_free_result($abfragePages);
				$rechteBox = $this->rechte_box_suchen($datenPages[pid],FALSE);
			}
		} else {
			if ($startSeite) {
				return FALSE;
			} else {
				$rechteBox = $boxUid;				
			}
		}
		return $rechteBox;
	}

	function rechte_box($dummy,$conf) {
		
		$content = '';
		$id = $GLOBALS["TSFE"]->id;
		$rechteBox = $this->rechte_box_suchen($id,TRUE);
		if ($rechteBox) {
			$config = array('tables' => 'tt_content','source' => $rechteBox,'dontCheckPid' => 1);
			$content = $this->cObj->RECORDS($config);
		}
		return $content;
	}
	
	function includeJquery() {
		if (t3lib_extMgm::isLoaded('t3jquery')) {
			require_once(t3lib_extMgm::extPath('t3jquery').'class.tx_t3jquery.php');
		}		
		// checks if t3jquery is loaded
		if (T3JQUERY === true) {
			tx_t3jquery::addJqJS();
			$out = "\n<!-- jquery: t3jquery -->";
		} else {
			$out = '<script src="fileadmin/res/jquery/js/jquery-1.9.1.min.js" type="text/javascript"></script>';
		}
		return $out;
	}

	function getHeaderType($header,$conf,$temp) {
		return print_r($temp,true);
	}
	
	function bearbeiterAnzeigen() {
		$seite = $GLOBALS["TSFE"]->id;
		$ausnahmen = array(1,35971,33120);
		if (!in_array($seite,$ausnahmen)) {
			$queryBearbeiter = '
			SELECT fe_users.name,fe_users.email,fe_users.tx_hepersonen_profilseite as page
			FROM fe_users
			INNER JOIN be_users ON be_users.username=fe_users.username
			INNER JOIN pages ON pages.cruser_id=be_users.uid
			WHERE pages.uid = ' . $seite . '
			AND be_users.deleted=0 AND be_users.disable=0
			AND fe_users.deleted=0 AND fe_users.disable=0
			AND fe_users.username<>"mmirsch"
			';
			$abfrageBearbeiter = $GLOBALS['TYPO3_DB']->sql_query($queryBearbeiter);
			if ($datenBearbeiter = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageBearbeiter)) {
				$user = $GLOBALS['TSFE']->fe_user->user['username'];
				$bearbeiterAnzeigen = $user=='mmirsch' || $user=='chrath';
				if ($bearbeiterAnzeigen) {
					if (!empty($datenBearbeiter['page'])) {
						$bearbeiterLink = '<a class="internalLink" target="_blank" href="index.php?id=' . $datenBearbeiter['page'] . '">' . $datenBearbeiter['name'] . '</a>';
					} else {
						$bearbeiterLink = '<a class="mail" href="mailto:' . $datenBearbeiter['name'] . ' <' . $datenBearbeiter['email'] . '>">' . $datenBearbeiter['name'] . '</a>';
					}
					$bearbeiterAnzeige = ', zuletzt bearbeitet von: ' . $bearbeiterLink;
				} else {
					$bearbeiterAnzeige = '';
				}
				
			}
			$textSeitenId = 'Seiten-ID: ' . $seite;
			$out = '
			<span class="pageEditor">
			' . $textSeitenId . $bearbeiterAnzeige . '
			</span>
			';			
		}
		return $out;		
	}
	
/*	
	function seiteMitBenutzergruppeNeuLaden() {
		$feGroupTItle = array('23','24','25','26','27','28','29','30','31','32','33');
		$studiGruppe = '71';
		$conf = array('parameter' => $GLOBALS['TSFE']->id);
		$url =  $cobj->cObj->typolink($title,$conf);
		
		$redirectHeader = t3lib_div::getURL($url, 1, true, $report);
		
	}
*/ 
		
/*	
	function geschuetzteSeitenAusblenden($menuArr ,$conf) {
		return tx_he_tools_util::geschuetzteSeitenAusblenden($menuArr ,$conf);
	}
*/
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_tools/pi1/class.tx_hetools_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_tools/pi1/class.tx_hetools_pi1.php']);
}

?>