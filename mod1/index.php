<?php

/***************************************************************
*  Copyright notice
*
*  (c) 2009 Josefiak/Mirsch <t3admin@hs-esslingen.de>
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


$LANG->includeLLFile('EXT:he_tools/mod1/locallang.xml');
require_once(PATH_t3lib . 'class.t3lib_scbase.php');
require_once(t3lib_extMgm::extPath('he_tools').'mod1/class.he_backend_util.php');
require_once(t3lib_extMgm::extPath('he_tools').'mod1/class.tx_he_tools_pers_verwaltung.php');
require_once(t3lib_extMgm::extPath('he_tools').'mod1/class.tx_he_tools_alias.php');
require_once(t3lib_extMgm::extPath('he_tools').'mod1/class.tx_he_tools_qr_codes.php');

require_once(t3lib_extMgm::extPath('he_tools').'mod1/class.sys_logs.php');
require_once(t3lib_extMgm::extPath('he_tools').'mod1/class.tx_he_tools_portal.php');

require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_util.php');
require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_be_mountpoints.php');
require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_solr.php');
require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_lsf.php');
require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_zeitschriftenlisten.php');
require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_powermail.php');
require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_veranstaltungs_buchung.php');

require_once(t3lib_extMgm::extPath('he_surveys').'lib/class.tx_he_surveys_lib_import.php');
require_once(t3lib_extMgm::extPath('phpexcel_service') . 'class.tx_phpexcel_service.php');


$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]


/**
 * Module 'HE-Benutzer' for the 'he_tools' extension.
 *
 * @author	Josefiak/Mirsch <t3admin@hs-esslingen.de>
 * @package	TYPO3
 * @subpackage	tx_hepersonen
 */
class  tx_hetools_module1 extends t3lib_SCbase {
				var $pageinfo;

				/**
				 * Initializes the Module
				 * @return	void
				 */
				function init()	{
					global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

					parent::init();

					/*
					if (t3lib_div::_GP('clear_all_cache'))	{
						$this->include_once[] = PATH_t3lib.'class.t3lib_tcemain.php';
					}
					*/
				}

				/**
				 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
				 *
				 * @return	void
				 */
				function menuConfig()	{
					global $LANG,$BE_USER;
					$beGroups = explode(',',$BE_USER->user['usergroup']);
					if ($BE_USER->user['admin']) {
						$this->MOD_MENU = Array (
							'function' => Array (
								'1a' => 'Personendaten Backend',
								'1b' => 'Personendaten Frontend',
								'1c' => 'Backendbenutzer anlegen (Studi)',
								'2' => 'Seitenbaum-Aktionen',
								'3' => 'Bearbeiter anzeigen',
								'4' => 'Lesezeichen',
//								'5' => 'Shopartikel-Exporte',
								'7' => 'LSF-Test',
								'8' => 'Logfile-Suche',
//								'9' => 'SOLR-Admin',
								'10' => 'Alias-Verwaltung',
								'11' => 'Portal-Funktionen',
								'12' => 'SOLR-Funktionen',
								'101' => 'Importiere QSM-Daten',
								'102' => 'Veranstaltungen (Buchungen)',
								'103' => 'Export Zetschriftenliste',
								'104' => 'Importiere Umfrage',
								'105' => 'Exportiere Redakteure',
								'106' => 'Exportiere SeitenRedakteure (nach Seiten sortiert)',
								'107' => 'QR-Codes',
								'999' => 'Testfunktion',
						)
						);
					}
					if (strpos($BE_USER->groupData['tables_modify'],'tx_hetools_veranstaltungen')!==FALSE) {
						$this->MOD_MENU['function']['102'] = 'Veranstaltungen (Buchungen)';
					}
					if (in_array('174',$beGroups)) {
						$this->MOD_MENU['function']['106'] = 'QR-Codes';
					}
					parent::menuConfig();
				}

				/**
				 * Main function of the module. Write the content to $this->content
				 * If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
				 *
				 * @return	[type]		...
				 */
				function main()	{

					global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

					// Access check!
					// The page will show only if there is a valid page and if this page may be viewed by the user
					$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
					$access = is_array($this->pageinfo) ? 1 : 0;
					// spezielle Benutzerabfrage
//					$beuserErlaubt = $this->beuserErlaubt($BE_USER->user[username]);
					$beuserErlaubt = count($this->MOD_MENU['function'])>0;
					if ($beuserErlaubt || ($this->id && $access) || ($BE_USER->user['admin'] && !$this->id))	{

							// Draw the header.
						$this->doc = t3lib_div::makeInstance('template');
//						$this->doc->styleSheetFile2 = "/fileadmin/css/backend/he_backend.css";
						$this->doc->addStyleSheet('backend_styles', 
																			t3lib_div::resolveBackPath($this->doc->backPath) . 
																			t3lib_extMgm::extRelPath('he_tools') . '/res/he_tools.css');
						$this->doc->form='<form action="" method="post" enctype="multipart/form-data">';

						$this->pers_verwaltung = new tx_he_tools_pers_verwaltung();
							// JavaScript
						$this->doc->JScode .= '
							<script language="javascript" type="text/javascript">
								script_ended = 0;
								function jumpToUrl(URL)	{
									document.location = URL;
								}
							</script>
						';
						
						$this->doc->postCode = '
							<script language="javascript" type="text/javascript">
								script_ended = 1;
								if (top.fsMod) top.fsMod.recentIds["web"] = 0;
							</script>
						';

						$headerSection = $this->doc->getHeader('pages',$this->pageinfo,$this->pageinfo['_thePath']).'<br />'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.path').': '.t3lib_div::fixed_lgd_cs($this->pageinfo['_thePath'],50);

						$this->content.=$this->doc->startPage($LANG->getLL('title'));
						$this->content.=$this->doc->header($LANG->getLL('title'));
						$this->content.=$this->doc->spacer(5);
						$this->content.=$this->doc->section('',$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,'SET[function]',$this->MOD_SETTINGS['function'],$this->MOD_MENU['function'])));
						$this->content.=$this->doc->divider(5);

						// Render content:
						$this->moduleContent();


						// ShortCut
						if ($BE_USER->mayMakeShortcut())	{
							$this->content.=$this->doc->spacer(20).$this->doc->section('',$this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']));
						}

						$this->content.=$this->doc->spacer(10);
					} else {
							// If no access or if ID == zero

						$this->doc = t3lib_div::makeInstance('mediumDoc');
						$this->doc->backPath = $BACK_PATH;

						$this->content.=$this->doc->startPage($LANG->getLL('title'));
						$this->content.=$this->doc->header($LANG->getLL('title'));
						$this->content.=$this->doc->spacer(5);
						$this->content.=$this->doc->spacer(10);
					}
//$this->content.= print_r($BE_USER->groupData['tables_modify'],true);				
				}

				function beuserErlaubt($username)	{
				$zugelasseneRedakteure = array(
					'akamin',
					'jsteck',
				);
					if (in_array($username,$zugelasseneRedakteure)) {
						return TRUE;		
					} else {
						return FALSE;
					}
				}
				
				function zugelasseneModule($username)	{
					$standardModule = array (
							'function' => array (
									'4' => 'Lesezeichen',
									'5' => 'Shopartikel-Exporte',
							)
					);
				}
				
				/**
				 * Prints out the module HTML
				 *
				 * @return	void
				 */
				function printContent()	{

					$this->content.=$this->doc->endPage();
					echo $this->content;
				}

				/**
				 * Generates the module content
				 *
				 * @return	void
				 */
				function moduleContent()	{
					$util = new he_backend_util();
						switch((string)$this->MOD_SETTINGS['function'])	{
							case '1a':
							  $this->content .= $this->pers_verwaltung->backend();
								break;
							case '1b':
							  $this->content .= $this->pers_verwaltung->frontend();
								break;
							case '1c':
							  $this->content .= $this->pers_verwaltung->addBackendUser();
								break;
							case '2':
							  $this->content .= $this->seitenbaumAktionen($this->id);
							  break;
							case 3:
								$this->content .= $this->bearbeiterAnzeigen($this->id);
								break;
							case 4:
								$app = new tx_he_tools_be_mountpoints();
							  $this->content .= $app->temporaraerenMountPointAuswaehlen($this->id);
								break;
							case 5:
							  $this->content .= $util->artikelExporte();
								break;
							case 7:
//								$lsfTest = new tx_he_tools_lsf();
//							  $this->content .= $lsfTest->test($this->MCONF['_']);
								break;
							case 8:
								$app = new he_tools_sys_logs();
							  $this->content .= $app->main($this->id);
								break;
							case 10:
								$app = new tx_he_tools_alias();
							  $this->content .= $app->main($this,$this->id);
								break;
							case 11:
								$app = new tx_he_tools_portal();
							  $this->content .= $app->main($this,$this->id);
								break;
							case 12:
								$app = new tx_he_tools_solr();
							  $this->content .= $app->main($this,$this->id);
								break;							
							case 101:
								$this->content .= $this->qsmDatenImportieren();
								break;
							case 102:
							  $buchungen = new tx_he_tools_veranstaltungs_buchung();
								$this->content .= $buchungen->backend_util($this->id);
								break;
							case 103:
							  $zeitschriften = new tx_he_tools_zeitschriftenlisten('tx_he_tools');
							  $zeitschriften->gibZeitschriftenListe($zeitschriftenListe);
								$this->exportiereZeitschriftenListe($zeitschriftenListe);
								break;
							case 104:
								$app = new tx_he_surveys_lib_import();
							  $this->content .= $app->main($this->id,$this->file);	
							  break;
							case 105:
								$this->content .= $this->bearbeiterExportieren($this->id);
								break;
							case 106:
								$this->content .= $this->seitenRedakteureExportieren($this->id);
								break;
							case 107:
								$app = new tx_he_tools_qr_codes();
								$this->content .= $app->main($this,$this->id);
								break;
							case 999:
								$this->content .= $this->testfunktion();
								break;
/*
								case 99:
							  $this->content .= $util->firmendaten_exportieren($this->id);
								break;
							case 101:
								$werMachtWas = new he_werMachtWas();
								$this->content .= $werMachtWas->importWerMachtWas();
								break;
*/
							default:
								$this->content .= (string)$this->MOD_SETTINGS['function'];
								break;
						}
				}
				
				function gibGremienBereich($kuerzel) {
					if ($kuerzel=='VU') {
						$kuerzel = 'GU';
					}
					$where = 'kuerzel="' . $kuerzel . '"';
					$query = $GLOBALS['TYPO3_DB']->exec_SELECTquery('kuerzel','tx_qsm_gremien',$where);
					if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)) {
						return $row['kuerzel'];
					}
					return '';
				}
				
				function gibEinrichtung($kuerzel) {
					$where = 'kuerzel="' . $kuerzel . '"';
					$query = $GLOBALS['TYPO3_DB']->exec_SELECTquery('kuerzel','tx_qsm_einrichtungen',$where);
					if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)) {
						return $row['kuerzel'];
					}
					return '';
				}
				
				function gibFinaBereich1($schluessel) {
					$where = 'schluessel=' . $schluessel;
					$query = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid','tx_qsm_fina_bereiche1',$where);
					if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)) {
						return $row['uid'];
					}
					return 0;
				}
				
				function gibFinaBereich2($schluessel) {
					$where = 'schluessel=' . $schluessel;
					$query = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid','tx_qsm_fina_bereiche2',$where);
					if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)) {
						return $row['uid'];
					}
					return 0;
				}
				
				function gibBezugsSemester($semester) {
					$jahr = substr($semester,0,4);
					if (substr($semester,4,1)=='1') {
						$semesterTitel = 'SS ' . $jahr;
					} else {
						$semesterTitel = 'WS ' . $jahr . '/' . ($jahr+1);
					}
					$where = 'title="' . $semesterTitel . '"';
					$query = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid','tx_qsm_zeitraeume',$where);
					if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)) {
						return $row['uid'];
					}
					return 0;
				}
				
				function behandleSonderfaelle($feld,&$speicherDaten,&$daten) {
					switch ($feld) {
						case 'antragsteller_name':
							$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','fe_users','deleted=0 AND disable=0 AND username=' . $daten['ag_username']);
							if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
								if (!empty($row['tx_hepersonen_akad_grad'])) {
									$name = $row['tx_hepersonen_akad_grad'] . ' ' . $row['first_name'] . ' ' . $row['last_name'] . ' (' . $row['username'] . ')';
								} else {
									$name = $row['first_name'] . ' ' . $row['last_name'] . ' (' . $row['username'] . ')';
								}
								$speicherDaten['antragsteller_name'] = $name;
							} else {
								$speicherDaten['antragsteller_name'] = $daten['ag_vorname'] . ' ' . $daten['ag_name'];
							}
							break;
						case 'bereich':
							$speicherDaten['bereich'] = $this->gibGremienBereich($daten['bereich']);
							break;
						case 'einrichtung':
							$speicherDaten['einrichtung'] = $this->gibEinrichtung($daten['bereich_einrichtung']);
							break;
						case 'fina_bereich1':
							$speicherDaten['fina_bereich1'] = $this->gibFinaBereich1($daten['fina_bereich']);
							break;
						case 'fina_bereich2':
							$speicherDaten['fina_bereich2'] = $this->gibFinaBereich2($daten['fina_bereich2_index']);
							break;
						case 'semester':
							$speicherDaten['bezugssemester'] = $this->gibBezugsSemester($daten['semester']);
							break;
					}
				}
				
				function behandleVerantwortliche($uidNew,$verantw) {
					if (!empty($verantw)) {
						$speicherDaten['pid'] = 131801;
						$speicherDaten['crdate'] = time();
						$speicherDaten['tstamp'] = time();
						$speicherDaten['sorting'] = 1;
						$speicherDaten['antrag'] = $uidNew;
						$speicherDaten['username'] = $verantw;
						$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','fe_users','deleted=0 AND disable=0 AND username="' . $verantw . '"');
						if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
							if (!empty($row['tx_hepersonen_akad_grad'])) {
								$name = $row['tx_hepersonen_akad_grad'] . ' ' . $row['first_name'] . ' ' . $row['last_name'];
							} else {
								$name = $row['first_name'] . ' ' . $row['last_name'];
							}
							$speicherDaten['name'] = $name;
							$abfrage = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_qsm_antraege_verantwortliche',$speicherDaten);
						}
					}
				}
				
				function behandleMittel($uidNew,&$mittel) {
					$sorting = 1;
					foreach ($mittel as $eintrag) {
						$speicherDaten['pid'] = 131801;
						$speicherDaten['crdate'] = time();
						$speicherDaten['sorting'] = $sorting++;
						$speicherDaten['tstamp'] = time();
						$speicherDaten['title'] = $eintrag['titel'];
						$speicherDaten['betrag'] = $eintrag['betrag'];
						$speicherDaten['kostenstelle'] = $eintrag['kostenstelle'];
						$speicherDaten['antrag'] = $uidNew;
						$abfrage = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_qsm_mittel',$speicherDaten);
					}
				}
				
				function behandleBudgets($uidNew,$semester,$beanbudget,$bewbudget) {
					$speicherDaten['pid'] = 131801;
					$speicherDaten['crdate'] = time();
					$speicherDaten['tstamp'] = time();
					$speicherDaten['sorting'] = 1;
					$speicherDaten['budget'] = $beanbudget;
					$speicherDaten['zeitraum'] = $semester;
					$speicherDaten['antrag'] = $uidNew;
					$speicherDaten['version'] = 0;
					$speicherDaten['mode'] = 'beanbudget';
					$abfrage = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_qsm_budgets',$speicherDaten);
					$speicherDaten['budget'] = $bewbudget;
					$speicherDaten['mode'] = 'bewbudget';
					$abfrage = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_qsm_budgets',$speicherDaten);
				}
				
				function qsmEintragImportieren(&$daten,&$mittel) {
					$speichernFelder = array ('masnanr','crdate','tstamp','status',
							'antragsteller','antragsteller_name','verantw',
							'bereich','einrichtung','semester',
							'short_title','title','ziel','begruendung','anlage',
							'start','ende','entscheidung',
							'beanbudget','bewbudget',
							'mittelpersonal','persstellen','sachmittel',
							'zwbericht','asbericht',
							'fina_bereich1','fina_bereich2',
							'anmerkungen','persstellen','email_pers'
					);					
					$importZuordnung = array(
							'tstamp' => 'tstamp',
							'crdate' => 'crdate',
							'sorting' => 'sorting',
							'deleted' => 'deleted',
							'hidden' => 'hidden',
							'masnanr' => 'masnanr',
							'antragsteller' => 'ag_username',
//							'antragsteller_name' => 'ag_username',
							'status' => 'status',
//							'bereich' => 'bereich',
//							'einrichtung' => 'bereich_einrichtung',
//							'verantw' => 'verantw',
//							'bezugssemester' => 'semester',
							'short_title' => 'shtitel',
							'title' => 'titel',
							'ziel' => 'ziel',
							'begruendung' => 'grund',
							'anlage' => 'anlage',
							'start' => 'start',
							'ende' => 'ende',
							'entscheidung' => 'ed_genehm_date',
//							'beanbudget' => 'beanbudget',
//							'bewbudget' => 'bewbudget',
//							'budget_begruendung' => 'budget_begruendung',
							'persstellen' => 'persstellen',
//							'sachmittel' => 'sachmittel',
//							'mittelpersonal' => 'mittelpersonal',
							'anmerkungen' => 'ed_comment',
							
//							'fina_bereich1' => 'fina_bereich',
//							'fina_bereich2' => 'fina_bereich2_index',
							
							'zwbericht' => 'zwbericht',
							'asbericht' => 'asbericht',
							'email_pers' => 'email_pers',
//							'kombinr' => 'kombinr',
//							'ed_budget' => 'ed_budget',
//							'mittelsachmittel' => 'mittelsachmittel',
//							'verantw_mail' => 'verantw_mail',
//							'verantw_name' => 'verantw_name',
//							'verantw_vorname' => 'verantw_vorname',
//							'ag_email' => 'ag_email',
							'originalId' => 'uid',
							
							);
					$speicherDaten = array();
					$speicherDaten['pid'] = 131801;
					foreach ($speichernFelder as $feld) {
						if (!empty($importZuordnung[$feld])) {
							$speicherDaten[$feld] = $daten[$importZuordnung[$feld]];
						} else {
							$this->behandleSonderfaelle($feld,$speicherDaten,$daten);
						}
					}
					$abfrage = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_qsm_antraege',$speicherDaten);
					$uidNew = $GLOBALS['TYPO3_DB']->sql_insert_id();
					$this->behandleMittel($uidNew,$mittel[$daten['uid']]);
					$this->behandleBudgets($uidNew,$speicherDaten['bezugssemester'],$daten['beanbudget'],$daten['bewbudget']);
					$this->behandleVerantwortliche($uidNew,$daten['verantw']);
					
					if ($abfrage) {
						return TRUE;
					} else {
						return FALSE;
					}
				}
				
				function qsmDatenAnlegen(&$massnahmen,&$mittel) {
					$anz = 0;
					foreach ($massnahmen as $eintrag) {
						if ($this->qsmEintragImportieren($eintrag,$mittel)) {
							$anz++;
						}
					}
					$out = $anz . ' Massnahmen wurden importiert!';
/*
					$out .= '<br>Mittel: ' . print_r($mittel,true);
					$out .= '<br>Stuktur: <br/>';
					foreach ($massnahmen[0] as $key=>$val) {
						$out .= "'" . $key . "' => '" . $key . "',<br/>";
					}
*/					
					return $out;
				}
				
				function qsmDatenImportAusfuehren() {
					$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_qsm_antraege','TRUE');
					$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_qsm_mittel','TRUE');
					$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_qsm_budgets','TRUE');
					$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_qsm_antraege_verantwortliche','TRUE');
					$sgebHost = t3lib_div::makeInstance('t3lib_db');
					$sgebDb = $sgebHost->sql_pconnect('rzlx0301.hs-esslingen.de','typo3user','r4N9RL2uWdHzf2S3') or die('Could not connect to TYPO3-tuitionfees server.' );
					$sgebHost->sql_select_db("studytax",$sgebDb) or die('Could not select database.');
					$where = 'deleted=0 AND hidden=0 AND (masnanr LIKE "Q%" OR masnanr LIKE "S-Q%")';
					$abfrage = $sgebHost->exec_SELECTquery('*','tx_tuitionfees_antrag',$where);
					$qsmDatenImport = array();
					$mittel = array();
					while ($daten = $sgebHost->sql_fetch_assoc($abfrage)) {
						$qsmDatenImport[] = $daten;
						$mittel[$daten['uid']] = array();
						$sqlMittel = 'SELECT * FROM tx_tuitionfees_means INNER JOIN 
												 tx_tuitionfees_antrag_sachmittel_mm on tx_tuitionfees_means.uid=tx_tuitionfees_antrag_sachmittel_mm.uid_foreign
												 WHERE tx_tuitionfees_means.deleted=0 and tx_tuitionfees_means.hidden=0 AND tx_tuitionfees_antrag_sachmittel_mm.uid_local= ' . $daten['uid'] . '
												 ORDER BY tx_tuitionfees_means.uid';
						$resultMittel = $sgebHost->sql_query($sqlMittel);
						while ($datenMittel = $sgebHost->sql_fetch_assoc($resultMittel)) {
							$mittel[$daten['uid']][] = $datenMittel;
						}
					}
					$GLOBALS['TYPO3_DB']->connectDB();
					return $this->qsmDatenAnlegen($qsmDatenImport,$mittel);
				}
				
				function qsmDatenImportieren() {
					$erg .= '<h2>Import der QSM-Daten</h2>';
					$erg .= '<div class="qsmDatenImportieren">';
					$erg .= '<form name="qsmDatenImport" method="post" action="'.$this->file.'">';
					$erg .= '<input type="submit" name="qsmDatenImportieren" value="QSM-Daten importieren"/><br/><br/>';
					$erg .= '</form>';
					$erg .= '</div>';
					$this->post = t3lib_div::_POST();
					$qsmDatenImportieren = !empty($this->post[qsmDatenImportieren]);
					
					if ($qsmDatenImportieren) {
						$erg .= $this->qsmDatenImportAusfuehren();
					}
					return $erg;
				}
				
				function testfunktion() {
					$erg .= '<h2>Testfunktion</h2>';
					$erg .= '<div class="seitenaktionen">';
					$erg .= '<form name="seitenaktionen" method="post" action="'.$this->file.'">';
					$erg .= '<input type="submit" name="testFunktion" value="Verwaiste Elemente anzeigen"/><br/><br/>';
					$erg .= '</form>';				
					$erg .= '</div>';
					$this->post = t3lib_div::_POST();
					$testFunktionGedrueckt = !empty($this->post[testFunktion]);
					
					if ($testFunktionGedrueckt) {
						$out = '<h1>Seiten mit Inhalten aus Sixcms</h1>';
						$pageUids = array();
						$queryPages = 'SELECT uid FROM pages where deleted=0';
						$abfragePages = $GLOBALS['TYPO3_DB']->sql_query($queryPages);
						$delContent = array();
						while ($datenPages = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfragePages)) {
							$pageUids[] = $datenPages['uid'];
						}
						$queryContent = 'SELECT uid,pid FROM tt_content where deleted=0 ORDER BY pid';
																		
						$abfrageContent = $GLOBALS['TYPO3_DB']->sql_query($queryContent);
						$count = 0;
						$max = 2000;
						while ($count<$max && $datenContent = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageContent)) {
							if (!in_array($datenContent['pid'],$pageUids)) {								
//								$delContent[$datenContent['uid']] = $datenContent['pid'];
								if (!in_array($datenContent['pid'],$delContent)) {				
									$delContent[] = $datenContent['pid'];
									$count++;
								}
							}
						}
						
//$erg .= '<br>' . $count . ' Elemente sind verwaist<br>';
//return $erg;						
						$delete['deleted'] = 1;
						$seitenListe = implode(',',$delContent);
						$wherePages = 'pid IN (' . $seitenListe . ')';
						
//						$contentListe = implode(',',array_keys($delContent));
//						$whereContent = 'pid IN (' . $contentListe . ')';
//						$gespeichert = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tt_content',$whereContent,$delete);
						
						$erg .= 'UPDATE tt_content SET deleted=1 WHERE ' . $wherePages;
//						$out = 'SELECT * FROM pages WHERE ' . $wherePages;
						return $erg;
						
						$sgebHost = t3lib_div::makeInstance('t3lib_db');
						$sgebDb = $sgebHost->sql_pconnect('rzlx0301.hs-esslingen.de','typo3user','r4N9RL2uWdHzf2S3') or die('Could not connect to TYPO3-tuitionfees server.' );
						$sgebHost->sql_select_db("studytax",$sgebDb) or die('Could not select database.');
						$where = 'deleted=0 AND hidden=0 AND masnanr LIKE "Q%"';
						$abfrage = $sgebHost->exec_SELECTquery('*','tx_tuitionfees_antrag',$where);
						t3lib_utility_Debug::debugInPopUpWindow($abfrage);
						while ($daten = $sgebHost->sql_fetch_assoc($abfrage)) {
							$erg .= print_r($daten,true);
							$GLOBALS['TYPO3_DB']->connectDB();
							return $erg;
						}
						$GLOBALS['TYPO3_DB']->connectDB();
					}
					return $erg;
				}

				public function seitenbaumAktionen($seite) {
					$this->post = t3lib_div::_POST();
					$seitenId = trim($this->post[seitenId]);
					$seitenCacheLoeschen = trim($this->post['seitenCacheLoeschen']);
					$seitenBaumWiederherstellen = trim($this->post['seitenBaumWiederherstellen']);
          $gibAnzahlUnterseiten = trim($this->post['gibAnzahlUnterseiten']);
          $erg = '';
					if ($seitenId!='' && $seitenCacheLoeschen!='') {
						$erg .= $this->seitenCacheLoeschen($seitenId);
          }	else if ($seitenId!='' && $seitenBaumWiederherstellen!='') {
            $erg .= $this->seitenBaumWiederherstellen($seitenId);
          }	else if ($seitenId!='' && $gibAnzahlUnterseiten!='') {
            $erg .= $this->gibAnzahlUnterseiten($seitenId);
          }	else {
						$seitenId = $seite;
					}
					$erg .= '<h2>Seitencache löschen</h2>';
					$erg .= '<div class="seitenaktionen">';
					$erg .= '<form name="seitenaktionen" method="post" action="'.$this->file.'">';
					$erg .= '<input type="text" name="seitenId" value="' . $seitenId . '"/><br/><br/>';
					$erg .= '<input type="submit" name="seitenCacheLoeschen" value="Cache für Seitenbaum löschen"/><br/><br/>';
          $erg .= '<input type="submit" name="seitenBaumWiederherstellen" value="Seitenbaum wiederherstellen (undelete)"/><br/><br/>';
          $erg .= '<input type="submit" name="gibAnzahlUnterseiten" value="Anzahl der Unterseiten bestimmen"/><br/><br/>';
          $erg .= '</form>';
					$erg .= '</div>';
					return $erg;
				}
				
				function bearbeiterExportieren($seite) {
					$erg = '<h2>TYPO3-Redakteure</h2>';
					$erg .= '<div class="bearbeiterExportieren">';
					$erg .= '<form name="bearbeiterExportieren" method="post" action="'.$this->file.'">';
					$erg .= '<input type="submit" name="bearbeiterExportieren" value="Bearbeiter exportieren"/><br/><br/>';
					$erg .= '</form>';
					$erg .= '</div>';

					$seitenListe = array();
					tx_he_tools_util::getChildrenPages($seite,$seitenListe);
					$benutzerGruppen = tx_he_tools_util::getBeGroupsWithPageAccess($seitenListe);
					$beUsers = tx_he_tools_util::getBeUsers($benutzerGruppen,'"mamiitoo"');
					
					$ergWeb = '';
					$ergWeb .= '<table class="grid" id="ergebnisliste">' . "\n";
					$ergWeb .= '<tr>
									<th>Name</th><th>E-Mail</th><th>Seitenbereiche</th>	<th>letzter Login</th>
									</tr>
					';
					foreach ($beUsers as $username) {
						$userData = tx_he_tools_util::getFeUserData($username);
						$dbMounts = tx_he_tools_util::getDbMounts($username);
						$lastLogin = date('d.m.Y',tx_he_tools_util::getLastLogin($username));
						$pageList = '';
						$pages = '';
						if (!empty($dbMounts)) {
							foreach ($dbMounts as $pageData) {
								$pageList[] = '<a target="_blank" href="/index.php?id=' . $pageData['uid'] . '">' . $pageData['title'] . '</a>';
							}
							$pages = implode('<br />', $pageList);
						}
						$name = $userData['first_name'] . ' ' . $userData['last_name'];
						$email = '<a href="mailto:' . $userData['email'] . '">' . $userData['email'] . '</a>';
						$name = '<a target="_blank" href="/index.php?id=' . $userData['tx_hepersonen_profilseite'] . '">' . $name . '</a>';
						$ergWeb .= '<tr>
						<td>' . $name . '</td><td>' . $email . '</td><td>' . $pages . '</td><td>' . $lastLogin . '</td>
						</tr>
						';
						$ergExport[] = array('name'=>$name,'email'=>$email,'seiten'=> $pages,'lastLogin'=>$lastLogin);
					}
					$ergWeb .= '</table>';

					$this->post = t3lib_div::_POST();
					if (!empty($this->post[bearbeiterExportieren])) {
						return $this->bearbeiterExportierenExcel($ergExport);
					} else {
						$erg .= $ergWeb;
					}
					
					return $erg;
				}

				function seitenRedakteureExportieren($seite) {
					$erg = '<h2>TYPO3-Seitenredakteure</h2>';
					$erg .= '<div class="bearbeiterExportieren">';
					$erg .= '<form name="bearbeiterExportieren" method="post" action="'.$this->file.'">';
					$erg .= '<input type="submit" name="bearbeiterExportieren" value="Bearbeiter exportieren"/><br/><br/>';
					$erg .= '</form>';
					$erg .= '</div>';

					$seitenListe = tx_he_tools_util::getChildPages($seite,'de');
					$ergWeb = '';
					$ergWeb .= '<table class="grid" id="ergebnisliste">' . "\n";
					$ergWeb .= '<tr>
												<th>Name</th><th>E-Mail</th><th>Seitenbereiche</th>	<th>letzter Login</th>
												</tr>
								';
					foreach ($seitenListe as $seite=>$titel) {
						$benutzerGruppen = tx_he_tools_util::getBeGroupsWithPageAccess(array($seite));
						$beUsers = tx_he_tools_util::getBeUsers($benutzerGruppen,'"mamiitoo"');
						foreach ($beUsers as $username) {
							$userData = tx_he_tools_util::getFeUserData($username);
							$lastLogin = date('d.m.Y',tx_he_tools_util::getLastLogin($username));
							$name = $userData['first_name'] . ' ' . $userData['last_name'];
							$email = '<a href="mailto:' . $userData['email'] . '">' . $userData['email'] . '</a>';
							$name = '<a target="_blank" href="/index.php?id=' . $userData['tx_hepersonen_profilseite'] . '">' . $name . '</a>';
							$ergWeb .= '<tr>
									<td>' . $name . '</td><td>' . $email . '</td><td>' . $titel  . '</td><td>' . $lastLogin . '</td>
									</tr>
									';
							$ergExport[] = array('name'=>$name,'email'=>$email,'seiten'=> $titel,'lastLogin'=>$lastLogin);
						}
					}

					$ergWeb .= '</table>';

					$this->post = t3lib_div::_POST();
					if (!empty($this->post[bearbeiterExportieren])) {
						return $this->bearbeiterExportierenExcel($ergExport);
					} else {
						$erg .= $ergWeb;
					}

					return $erg;
				}

		function bearbeiterExportierenExcel(&$bearbeiterDaten) {
					$jahr = date('Y');
					$monat = date('m');
					$tag = date('d');
					$sheetname = 'TYPO3_Redakteure' . '_'  . $jahr. '_' . $monat. '_' . $tag;
					$dateiname = $sheetname . '.xlsx';
					
					$spaltenTitel = array('Name', 'E-Mail', 'Seitenbereiche','letzter Login');
					
					$phpExcelService =  t3lib_div::makeInstance('tx_phpexcel_service');
					$phpExcel = $phpExcelService->getPHPExcel();				
					$phpExcel ->getProperties()->setTitle('TYPO3_Redakteure')->setSubject('TYPO3 Redakteure');
					$phpExcel->getActiveSheet()->setTitle('TYPO3 Redakteure');
					$sheet = $phpExcel->getActiveSheet();
					PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
					
					$spalte = 0;
					foreach($spaltenTitel as $titelText) {
						$objRichText = $phpExcelService->getInstanceOf('PHPExcel_RichText');
						$titel = $objRichText->createTextRun($titelText);
						$titel->getFont()->setSize('12');
						$titel->getFont()->setName('Arial');
						$titel->getFont()->setBold(true);
						$zelle = chr(ord('A') + $spalte) . '1';
						$sheet->setCellValue($zelle, $objRichText);
						$sheet->getStyle($zelle)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$spalte++;
					}
					$zeile = 2;
					foreach ($bearbeiterDaten as $daten) {
							$sheet->setCellValueByColumnAndRow(0, $zeile, $this->cleanStringForExport($daten['name']));
							$sheet->setCellValueByColumnAndRow(1, $zeile, $this->cleanStringForExport($daten['email']));
							$sheet->setCellValueByColumnAndRow(2, $zeile, $this->cleanStringForExport($daten['seiten']));
							$sheet->setCellValueByColumnAndRow(3, $zeile, $this->cleanStringForExport($daten['lastLogin']));
							$sheet->getStyle('C' . $zeile)->getAlignment()->setWrapText(true);
							$zeile++;
					}
					
					$sheet->getDefaultRowDimension()->setRowHeight(-1);
					$sheet->getColumnDimension('A')->setWidth(30);
					$sheet->getColumnDimension('B')->setWidth(30);
					$sheet->getColumnDimension('C')->setWidth(80);										
										
					header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
					header('Content-Type: application/vnd.ms-excel');
					header('Content-Disposition: attachment;filename="' . $dateiname . '"');
					/*
					header('Content-Disposition: attachment;filename="' . $dateiname . '"');
					header('Cache-Control: max-age=0');
					*/
					$excelWriter = $phpExcelService->getInstanceOf('PHPExcel_Writer_Excel5', $phpExcel);
					$excelWriter->save('php://output');
					exit();
				}
					
				function bearbeiterAnzeigen($seite) {
					$erg = '<h2>Es wurden keine Bearbeiter für diese Seite gefunden</h2>';
					$seitenListe = array();
					tx_he_tools_util::getParentPages($seite,$seitenListe);
					if (!empty($seitenListe)) {
//						$benutzerGruppen = tx_he_tools_util::getBeGroupsWithPageAccess($seitenListe);
						$benutzerGruppen = tx_he_tools_util::getBeGroupsWithPageAccess(array($seite));

						if (!empty($benutzerGruppen)) {
							$beUsers = tx_he_tools_util::getBeUsers($benutzerGruppen);
							if (!empty($benutzerGruppen)) {
								$erg = '<h2>Folgende Benutzer haben Bearbeitungsrechte für diese Seite:</h2>';
								$erg .= '<table class="grid" id="ergebnisliste">' . "\n";
								$erg .= '<tr>
												<th>Name</th><th>E-Mail</th>	
												</tr>
								';
								foreach ($beUsers as $username) {
									$userData = tx_he_tools_util::getFeUserData($username);
									$name = $userData['first_name'] . ' ' . $userData['last_name'];
									$email = '<a href="mailto:' . $userData['email'] . '">' . $userData['email'] . '</a>';
									$name = '<a target="_blank" href="/index.php?id=' . $userData['tx_hepersonen_profilseite'] . '">' . $name . '</a>';
									$erg .= '<tr>
									<th>' . $name . '</th><th>' . $email . '</th>
									</tr>
									';
								}
								$erg .= '</table>';
							}
						}
					}
					
					return $erg;
				}
				
				function seitenCacheLoeschen($seitenId) {
					$seitenListe = array();
					tx_he_tools_util::getPageTree($seitenId,&$seitenListe);
					
					$erg = tx_he_tools_util::loescheTypo3Seitencache($seitenListe);
					if ($erg) {
						$out = 'Der Cache von ' .  count($seitenListe) . ' Seiten wurde gelöscht';
					} else {
						$out = 'Fehler beim Löschen des Seitencache';
					}
					return $out;
				}
								
				function seitenBaumWiederherstellen($seitenId) {
					$seitenArray = array();
					tx_he_tools_util::getPageTree($seitenId,&$seitenArray,'all');
					
					$seitenListe = '(' . implode(',',$seitenArray) . ')';

					$undelete['deleted'] = 0;
					$wherePages = 'uid IN ' . $seitenListe;
					$gespeichert = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('pages',$wherePages,$undelete);
					if (!$gespeichert) {
						$out = 'Fehler beim Wiederhertellen der Seiten';
					} else {
						$whereTtContent = 'pid IN ' . $seitenListe;
						$gespeichert = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tt_content',$whereTtContent,$undelete);
						if (!$gespeichert) {
							$out = 'Fehler beim Wiederherstellen der Seiten';
						} else {
							$out = count($seitenArray) . ' Seiten wurden wiederhergestellt';
						}
					}
					
					return $out;
				
				}
								
				function exportiereZeitschriftenListe($zeitschriftenListe) {
					$jahr = date('Y');
					$monat = date('m');
					$tag = date('d');
					$sheetname = 'Zeitschriftenliste' . '_'  . $jahr. '_' . $monat. '_' . $tag;
					$dateiname = $sheetname . '.xls';
										
					$spaltenTitel = array('Titel', 'Signatur', 'Bestandsnachweis');
					/** @var $phpExcelService tx_phpexcel_service */
					$phpExcelService = t3lib_div::makeInstanceService('phpexcel');
				
					$phpExcel = $phpExcelService->getPHPExcel();
					$phpExcel->getActiveSheet()->setTitle($sheetname);
					$sheet = $phpExcel->getActiveSheet();
					
					$spalte = 0;
					foreach($spaltenTitel as $titelText) {
						$objRichText = $phpExcelService->getInstanceOf('PHPExcel_RichText');
						$titel = $objRichText->createTextRun($titelText);
						$titel->getFont()->setSize('12');
						$titel->getFont()->setName('Arial');
						$titel->getFont()->setBold(true);
						$zelle = chr(ord('A') + $spalte) . '1';
						$sheet->setCellValue($zelle, $objRichText);
						$sheet->getStyle($zelle)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$spalte++;
					}
					$zeile = 2;
					foreach ($zeitschriftenListe as $buchstabe=>$daten) {
						foreach ($daten as $eintrag) {
							$sheet->setCellValueByColumnAndRow(0, $zeile, $this->cleanStringForExport($eintrag[titel]));
							$sheet->setCellValueByColumnAndRow(1, $zeile, $this->cleanStringForExport($eintrag[signatur]));
							$sheet->setCellValueByColumnAndRow(2, $zeile, $this->cleanStringForExport($eintrag[bestandsnachweis]));
							$zeile++;
						}
						
					}
					
					$sheet->getColumnDimension('A')->setWidth(80);
					$sheet->getColumnDimension('B')->setWidth(10);
					$sheet->getColumnDimension('C')->setWidth(50);
					
					header('Content-Type: application/vnd.ms-excel');
					header('Content-Disposition: attachment;filename="' . $dateiname . '"');
					header('Cache-Control: max-age=0');
					$excelWriter = $phpExcelService->getInstanceOf('PHPExcel_Writer_Excel5', $phpExcel);
					$excelWriter->save('php://output');
				}
				
				function cleanStringForExport($text) {
 					$text = str_replace("<br>","\n",$text);
 					$text = str_replace("<br/>","\n",$text);
 					$text = str_replace("<br />","\n",$text);
 					$text = strip_tags($text);
 					return $text;
				}

        function gibSeitenzahlenBaumAus($seitenListe, $ebene) {
          $out = '<tr>';
          for ($i=0;$i<$ebene;$i++) {
            $out .= '<td></td>';
          }
          $out .= '<td>' . $seitenListe['title'] . '</td>';
          for ($i=$ebene;$i<3;$i++) {
            $out .= '<td></td>';
          }
          for ($i=0;$i<$ebene;$i++) {
            $out .= '</td><td>';
          }
          $out .= '<td>' .$seitenListe['childrenCount'] . '</td>';
          for ($i=$ebene;$i<3;$i++) {
            $out .= '<td></td>';
          }
          $out .= '</tr>';
          if (!empty($seitenListe['childrenList'])) {
            foreach($seitenListe['childrenList'] as $seitenDaten) {
              $out .= self::gibSeitenzahlenBaumAus($seitenDaten,$ebene+1);
            }
          }
          return $out;
        }

        function gibAnzahlUnterseiten($seitenId) {
          $seitenListe = array();
          //tx_he_tools_util::getPageTree($seitenId,&$seitenListe,0,0,1);
          tx_he_tools_util::getPageSubTreeWithTitle($seitenId,&$seitenListe,2);
          $out = '<table>';
          $out .=  self::gibSeitenzahlenBaumAus($seitenListe[$seitenId],0);
          $out .= '</table>';
          return $out;
        }

	}
		

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_tools/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_tools/mod1/index.php']);
}

// Make instance:
$SOBE = t3lib_div::makeInstance('tx_hetools_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>