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
require_once(t3lib_extMgm::extPath('he_tools').'dcafiles/class.tx_hetools_dcahooks.php');
require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_util.php');


/*
 * Plugin 'Plugins' for the 'he_tools' extension.
 *
 * @author	Manfred Mirsch <Manfred.Mirsch@hs-esslingen.de>
 * @package	TYPO3
 * @subpackage	tx_hetools
 */
class tx_hetools_pi2 extends tslib_pibase {
	var $prefixId      = 'tx_hetools_pi2';		// Same as class name
	var $scriptRelPath = 'pi2/class.hetools_pi2.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'he_tools';	// The extension key.
	var $pi_checkCHash = true;
	var $ap_admin;
	var $language;
  var $imgWidthStartContainer = 259;
  var $imgWidthUnterContainer = 150;
  static $tabConfig = array (
			'STARTSEITE_FAKULTAET' => array (
					array (
							'name' => 'fakultaet',
							'label' => 'Fakultät',
							'links' => array(
									'standort'=>'Infos zur Fakultät',
									'personen'=>'Personen',
									'labore_und_institute'=>'Labore und Institute',
							),
							'lang' => 'de',
					),
					array (
							'name' => 'studium',
							'label' => 'Studium',
							'links' => array(
									'studiengaenge'=>'Studiengänge',
									'studienberatung'=>'Studienberatung',
									'infos'=>'Infos für Studierende',
	
							),
							'lang' => 'de',
					),
					array (
							'name' => 'forschung_und_kooperationen',
							'label' => 'Forschung und Kooperationen',
							'links' => array(
									'forschung_und_transfer'=>'Forschung und Transfer',
									'partner'=>'Partner und Kooperationen',
									'projekte'=>'Projekte',
							),
							'lang' => 'de',
					),
					array (
							'name' => 'aktuelles',
							'label' => 'Aktuelles',
							'links' => array(
									'fakultaets_news'=>'Fakultäts-News',
									'hochschul_News'=>'Hochschul-News',
									'hochschulkalender'=>'Hochschulkalender',
							),
							'lang' => 'de',
					),
					array (
							'name' => 'international',
							'label' => 'International',
							'links' => array(
									'studierende_der_hochschule'=>'Studierende der Hochschule',
									'internationale_studierende'=>'Internationale Studierende',
									'akademisches_auslandsamt'=>'Akademisches Auslandsamt',
							),
							'lang' => 'de',
					),
					array (
							'name' => 'karriere_und_weiterbildung',
							'label' => 'Karriere und Weiterbildung',
							'links' => array(
									'karriereservice'=>'Karriereservice',
									'job_portal'=>'Job-Portal',
									'weiterbildung'=>'Weiterbildung',
							),
							'lang' => 'de',
					),
					array (
							'name' => 'study',
							'label' => 'Studies',
							'links' => array(
									'campus'=>'Faculty Information',
									'study_programmes'=>'Degree Programmes',
									'student_advice_centre'=>'Student advice centre',
							),
							'lang' => 'en',
					),
					array (
							'name' => 'international_en',
							'label' => 'International',
							'links' => array(
									'international_students'=>'International students',
									'information_for_applicants'=>'Information for applicants',
									'international_office'=>'International office',
							),
							'lang' => 'en',
					),
			),
			'STARTSEITE_STUDIENGANG' => array (
					array (
							'name' => 'inhalt',
							'label' => 'Inhalt und Aufbau',
							'links' => array(
									'allgemeines'=>'Allgemeine Informationen',
									'studienverlauf'=>'Studienverlauf, Studieninhalte',
									'berufsperspektiven'=>'Berufsperspektiven',
							),
							'lang' => 'de',
					),
					array (
							'name' => 'bewerbung',
							'label' => 'Rund um die Bewerbung',
							'links' => array(
									'bewerbung'=>'Bewerbung',
									'studienberatung'=>'Studienberatung',
									'internationale_bewerber'=>'Internationale Bewerber',
							),
							'checks' => array(
									'online_bewerbung_deaktivieren'=>'Online Bewerbung deaktivieren?',
							),
							'lang' => 'de',
					),
					array (
							'name' => 'content_and_structure',
							'label' => 'Content and structure',
							'links' => array(
									'general_information'=>'General Information',
									'progress_of_programme'=>'Structure of degree programme',
									'career_prospects'=>'Career prospects',
							),
							'lang' => 'en',
					),
					array (
							'name' => 'application',
							'label' => 'Applications',
							'links' => array(
									'international_applicants'=>'International applicants',
									'international_office'=>'International Office',
									'student_advice_centre'=>'Student advice centre',
							),
							'lang' => 'en',
					),
			),
			'STUDIENGANG_DATEN_UND_FAKTEN' => array (
					'name' => 'datenUndFakten',
					'label' => 'Daten und Fakten',
					'zeilen' => array(
							'Abschluss',
							'Fakultät',
							'Campus',
							'Studienart',
							'Studieninhalte',
							'Studienziel',
							'Studienschwerpunkte/ Vertiefung',
							'Berufsperspektiven',
							'Regelstudienzeit',
							'Praktische Zugangsvoraussetzung',
							'Praxissemester',
							'Unterrichtssprache',
							'Zulassungsbeschränkung',
							'Studienbeginn',
							'Bewerbungsfristen',
							'Sprachkenntnisse',
					),
			),
	
			'STUDIENGANG_DATEN_UND_FAKTEN_EN' => array (
					'name' => 'datenUndFaktenEn',
					'label' => 'Data and Facts',
					'zeilen' => array(
							'Degree',
							'Faculty',
							'Campus',
							'Mode of study',
							'Contents',
							'Objectives of the degree programme',
							'Specialisations',
							'Job perspectives',
							'Period of study',
							'Entry requirements (practical experience)',
							'Internship',
							'Language of instruction',
							'Admission restriction',
							'Start of studies',
							'Application deadlines',
							'Language skills',
					),
			),
	
	);
	
	public static function getTabConfig() {
		return self::$tabConfig;
	}
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
		$modus = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'auswahl','allgemein');
		$host = t3lib_div::getIndpEnv('HTTP_HOST');
		$this->pageLink = $this->pi_getPageLink($id);
		$get = t3lib_div::_GET();
		if ($get['L']==1) {
			$this->language = 'en';
		} else {
			$this->language = 'de';
		}
		$GLOBALS['TSFE']->additionalHeaderData['he_tools_pi2'] .= '
			<link rel="stylesheet" type="text/css" href="/typo3conf/ext/he_tools/pi2/he_img_links.css" />
			';
		switch ($modus) {
		case "STARTSEITE_FAKULTAET":
			$pfad = '/fileadmin/medien/fakultaeten/allgemein/startseite_fakultaeten/';
			$konfiguration = self::$tabConfig['STARTSEITE_FAKULTAET'];
			$content = $this->renderFakultaetsSeite($konfiguration,$this->cObj->data['pi_flexform']['data'],$pfad);	
			break; 
		case "STARTSEITE_STUDIENGANG":
			$pfad = '/fileadmin/medien/fakultaeten/allgemein/startseite_studiengaenge/';
			$konfiguration = self::$tabConfig['STARTSEITE_STUDIENGANG'];
			$content = $this->renderStudiengangSeite($konfiguration,$this->cObj->data['pi_flexform']['data'],$pfad);	
			break;
		case "FREIE_CONTAINER_EINSTIEG":
			$content = $this->renderFreieContainerEinstieg($this->cObj->data['pi_flexform']['data']);
			break; 
		case "FREIE_CONTAINER_UNTERSEITE":
			$content = $this->renderFreieContainerUnterseite($this->cObj->data['pi_flexform']['data']);	
			break; 
		case "FREIE_CONTAINER_UNTERSEITE_TEXT":
			$content = $this->renderFreieContainerUnterseite($this->cObj->data['pi_flexform']['data'],TRUE);	
			break; 
		case "STARTSEITE_JUBILAEUM":
			$content = $this->renderStartseiteJubilaeum($this->cObj->data['pi_flexform']['data']);	
			break; 
		case "UNTERSEITE_JUBILAEUM":
			$content = $this->jubilaeumUnterseite($this->cObj->data['pi_flexform']['data']);	
			break; 
		default:
			$content = '<h3>noch nicht implementiert: ' . $kuerzel . '</h3>';	
			break;
		}
		$content = '<div id="he_container">' . $content . '</div>';
		return $this->pi_wrapInBaseClass($content);
	}

	function renderFreieContainerEinstiegAlt($flexFormData) {
		$maxTabs = 20;
		$out = '';
		$carousel = $this->erzeugeCarouselConfig($flexFormData);
		$out = '';
		if (count($carousel['bilder'])>1) {
			$out .= $this->createImageCarousel($carousel);
		} else {
			if (!empty($carousel['bilder'][0])) {
				$out .= '<div id="singleHeader">
								<img src="' . $carousel['bilder'][0] . '" />
								</div>';				
			}
		}

		$finished = FALSE;
		$elemType = 'left';
		for ($index=0;$index<$maxTabs && !$finished;$index++) {

			if (empty($flexFormData['container_' . $index])) {
				$finished = TRUE;
			} else {
				$data = &$flexFormData['container_' . $index];
				if (!empty($data['lDEF']['title_' . $index]['vDEF'])) {
					if (!empty($data['lDEF']['grafik_link_' . $index]['vDEF'])) {
						$typolink_conf = array(
								'returnLast' => "url",
								'parameter' => $data['lDEF']['grafik_link_' . $index]['vDEF'],
						);
						$linkUrl = $this->cObj->typolink("", $typolink_conf);
					} else {
						$linkUrl = '';
					}
					$img = '<img src="' . $data['lDEF']['grafik_' . $index]['vDEF'] . '" />';
					if (!empty($linkUrl)) {
						$img = '<a href="' . $linkUrl . '">' . $img . '</a>';
					}
					$titel = '<div class="title">' . $data['lDEF']['title_' . $index]['vDEF'] . '</div>' ;
					$links = '<div class="container_img">' . $img . '<div class="filter"></div></div>';
					$rechts = '<div class="content">' .
							$this->pi_RTEcssText($data['lDEF']['unterpunkte_' . $index]['vDEF']) .
							'</div>';
					
					$out .= '<div id="elem_' . $index . '" class="he_img_links_block einstieg ' . $elemType . '">' .
							$titel . $links . $rechts .
							'</div>';
				}
			}
			if ($elemType=='left') {
				$elemType = 'right';
			} else {
				$elemType = 'left';
				$out .= '<br class="clear" />';
			}
		}
		$out .= '
			<script type="text/javascript">
			$(document).ready(function () {
				var maxHeight = 0;
				var blockHeight;
				var blockTop;
				var content;
				var spalte=0; 
				var ids = Array();
				$(".he_img_links_block").each(function(){
					ids[spalte] = $(this).attr("id");
					content = $(this).find(".content");
					position = content.position();
					blockTop = position.top - $(this).position().top;
					blockHeight = content.outerHeight(true)+blockTop;
					if (maxHeight<blockHeight) {
						maxHeight = blockHeight;
					}
					spalte++;
					if (spalte==2) {
						$("#" + ids[0]).height(maxHeight);
						$("#" + ids[1]).height(maxHeight);
						maxHeight = 0;
						spalte=0;
					}
				});
				if (spalte==1) {
					$("#" + ids[0]).height(maxHeight);
				}
				
			});
			</script>
		';
		return $out;
	}
	
	function renderFreieContainerEinstieg($flexFormData) {
		$maxTabs = 20;
		$out = '';
		$carousel = $this->erzeugeCarouselConfig($flexFormData);
		$out = '';
		if (count($carousel['bilder'])>1) {
			$out .= $this->createImageCarousel($carousel);
		} else {
			if (!empty($carousel['bilder'][0])) {
				$out .= '<div id="singleHeader">
								<img src="' . $carousel['bilder'][0] . '" />
								</div>';				
			}
		}
		
		$finished = FALSE;
		$maxIndex = $maxTabs-1;
		for ($index=0;$index<$maxTabs && !$finished;$index++) {
			if (empty($flexFormData['container_' . $index]['lDEF']['title_' . $index]['vDEF'])) {
				$maxIndex = $index;
				$finished = TRUE;
			}
		}
		
		$elemType = 'col1';
		$colLeft = '';
		$colRight = '';
		$colSep = '<div class="colSep"></div>';
		for ($index=0;$index<=$maxIndex;$index++) {
			$data = &$flexFormData['container_' . $index];
			if (!empty($data['lDEF']['title_' . $index]['vDEF']) || !empty($data['lDEF']['grafik_link_' . $index]['vDEF'])) {
				if (!empty($data['lDEF']['title_' . $index]['vDEF'])) {
					$titel = '<div class="title">' . $data['lDEF']['title_' . $index]['vDEF'] . '</div>' ;
				} else {
					$titel = '<div class="title"></div>' ;
				}
				if ($data['lDEF']['grafik_' . $index]['vDEF']) {
					$imgThumbnail = $this->getThumbnail($this->imgWidthStartContainer,$data['lDEF']['grafik_' . $index]['vDEF']);
					if (!empty($data['lDEF']['grafik_link_' . $index]['vDEF'])) {
						$typolink_conf = array(
								'returnLast' => "url",
								'parameter' => $data['lDEF']['grafik_link_' . $index]['vDEF'],
						);
						$linkUrl = $this->cObj->typolink("", $typolink_conf);
					} else {
						$linkUrl = '';
					}
					$img = '<img src="' . $imgThumbnail . '" />';
					if (!empty($linkUrl)) {
						$img = '<a href="' . $linkUrl . '">' . $img . '</a>';
					}
					$links = '<div class="container_img">' . $img . '<div class="filter"></div></div>';
				} else {
					$links = '';
				}
				
				$rechts = '<div class="content">' .
						$this->pi_RTEcssText($data['lDEF']['unterpunkte_' . $index]['vDEF']) .
						'</div>';
				$content = '<div id="elem_' . $index . '" class="he_img_links_block einstieg ' . $elemType . '">' .
						$titel . $links . $rechts .
						'</div>';
			}
			if ($elemType=='col1') {
				$colLeft = $content;
				$elemType = 'col2';
			} else {
				$elemType = 'col1';
				$colRight = $content;
				$out .= '<div class="container1">
								<div class="containerSep">
								<div class="container2">';
				$out .= $colLeft . $colSep . $colRight;
				$out .= '</div></div></div>';
			}
		}
		return $out;
	}

	function getThumbnail($width,$path) {
		$imgConfig = array();
		$imgConfig['file'] = $path;
		$imgConfig['file.']['maxW'] = $width;
		$bildUrl = $this->cObj->IMG_RESOURCE($imgConfig);
		return $bildUrl;
	}
	
	function renderStartseiteJubilaeum($flexFormData) {
		$maxTabs = 20;
		$out = '';
		$finished = FALSE;
		$maxIndex = $maxTabs-1;
		for ($index=0;$index<$maxTabs && !$finished;$index++) {
			if (empty($flexFormData['container_' . $index]['lDEF']['title_' . $index]['vDEF'])) {
				$maxIndex = $index;
				$finished = TRUE;
			}
		}
	
		$elemType = 'col1';
		$colLeft = '';
		$colRight = '';
		$colSep = '<div class="colSep"></div>';

		$banner = $flexFormData['allgemein']['lDEF']['banner']['vDEF'];
		if (!empty($banner)) {
			$out .= '<div class="banner"><img src="' . $banner . '" /></div>';
		}
		$out .= '<div class="einleitung">' .
							'<div class="links">' .	$this->pi_RTEcssText($flexFormData['allgemein']['lDEF']['einleitung_links']['vDEF']) . '</div>' .
							'<div class="rechts">' .	$this->pi_RTEcssText($flexFormData['allgemein']['lDEF']['einleitung_rechts']['vDEF']) . '</div>' .
							'</div>';
//		$out .= $this->renderCarousel($flexFormData);
				
		for ($index=0;$index<=$maxIndex;$index++) {
			$data = &$flexFormData['container_' . $index];
			if (!empty($data['lDEF']['title_' . $index]['vDEF']) || !empty($data['lDEF']['grafik_link_' . $index]['vDEF'])) {
				if (!empty($data['lDEF']['grafik_link_' . $index]['vDEF'])) {
					$typolink_conf = array(
							'returnLast' => "url",
							'parameter' => $data['lDEF']['grafik_link_' . $index]['vDEF'],
					);
					$linkUrl = $this->cObj->typolink("", $typolink_conf);
				} else {
					$linkUrl = '';
				}
				if (!empty($data['lDEF']['title_' . $index]['vDEF'])) {
					$titleText = '&raquo;&nbsp;' . $data['lDEF']['title_' . $index]['vDEF'];
				} else {
					$titleText = '' ;
				}
				if (!empty($linkUrl)) {
					$titleText = '<a href="' . $linkUrl . '">' . $titleText . '</a>';
				}
				$titel = '<div class="title">' . $titleText . '</div>' ;
				if ($data['lDEF']['grafik_' . $index]['vDEF']) {
					$img = '<img src="' . $data['lDEF']['grafik_' . $index]['vDEF'] . '" />';
					if (!empty($linkUrl)) {
						$img = '<a href="' . $linkUrl . '">' . $img . '</a>';
					}
					$grafik = '<div class="container_img">' . $img . '</div>';
				} else {
					$grafik = '';
				}
	
				$text = '<div class="content">' .
						$this->pi_RTEcssText($data['lDEF']['unterpunkte_' . $index]['vDEF']) .
						'</div>';
				$content = '<div id="elem_' . $index . '" class="he_img_links_block inhalt ' . $elemType . '">' .
						$titel . $grafik  .
						'</div>';
			}
			if ($elemType=='col1') {
				$colLeft = $content;
				$elemType = 'col2';
			} else {
				$elemType = 'col1';
				$colRight = $content;
				$out .= '<div class="container1"><div class="containerSep"><div class="container2">' . 
								$colLeft . $colSep . $colRight . 
								'</div></div></div>';
			}
		}
		return '<div class="startseite_jubilaeum">' . $out . '</div>';
	}
	
	function renderFreieContainerUnterseite($flexFormData,$einleitungsText=FALSE) {
		$maxTabs = 20;
		$out = '';
		$finished = FALSE;
		for ($index=0;$index<$maxTabs && !$finished;$index++) {
			if (empty($flexFormData['container_' . $index])) {
				$finished = TRUE;
			} else {
				$einleitung = '';
				$data = &$flexFormData['container_' . $index];
				
				if (!empty($data['lDEF']['grafik_' . $index]['vDEF'])) {
					$imgThumbnail = $this->getThumbnail($this->imgWidthUnterContainer,$data['lDEF']['grafik_' . $index]['vDEF']);
					if (!empty($data['lDEF']['grafik_link_' . $index]['vDEF'])) {
						$typolink_conf = array(
								'returnLast' => "url",
								'parameter' => $data['lDEF']['grafik_link_' . $index]['vDEF'],
						);
						$linkUrl = $this->cObj->typolink("", $typolink_conf);
					} else {
						$linkUrl = '';
					}
					$img = '<img src="' . $imgThumbnail . '" />';
					if (!empty($linkUrl)) {
						$img = '<a href="' . $linkUrl . '">' . $img . '</a>';
					}
					$links = '<div class="left">'	. $img . '</div>';
				} else {
					$links = '';
				}
								
				$titelVerlinken = $data['lDEF']['add_title_link_' . $index]['vDEF'];
				if (!empty($data['lDEF']['title_' . $index]['vDEF']) || !empty($data['lDEF']['grafik_link_' . $index]['vDEF'])) {
					if (!empty($data['lDEF']['title_' . $index]['vDEF'])) {
						if ($titelVerlinken && !empty($linkUrl)) {
							$titel = '<div class="title"><span><a href="' . $linkUrl . '">' . $data['lDEF']['title_' . $index]['vDEF'] . '</a></span></div>';
						} else {
							$titel = '<div class="title"><span>' . $data['lDEF']['title_' . $index]['vDEF'] . '</span></div>' ;
						}
					} else {
						$titel = '<div class="title"></div>' ;
					}
					if ($einleitungsText && !empty($data['lDEF']['einleitung_' . $index]['vDEF'])) {
						$einleitung = '<div class="einleitung">' . $data['lDEF']['einleitung_' . $index]['vDEF'] . '</div>';
					}
					
					$rechts = '<div class="content">' . 
										$this->pi_RTEcssText($data['lDEF']['unterpunkte_' . $index]['vDEF']) . 
										'</div>';
					$out .= '<div id="elem_' . $index . '" class="he_img_container">' .
										$titel . $einleitung . $links . $rechts . 
									'</div>';
				}
			}
		}
		return $out;
	}
	
	function jubilaeumUnterseite($flexFormData,$einleitungsText=FALSE) {
		$maxTabs = 20;
		$out = '';
		$finished = FALSE;
		for ($index=0;$index<$maxTabs && !$finished;$index++) {
			if (empty($flexFormData['container_' . $index])) {
				$finished = TRUE;
			} else {
				$data = &$flexFormData['container_' . $index];
				if (!empty($data['lDEF']['grafik_link_' . $index]['vDEF'])) {
					$typolink_conf = array(
							'returnLast' => "url",
							'parameter' => $data['lDEF']['grafik_link_' . $index]['vDEF'],
					);
					$linkUrl = $this->cObj->typolink("", $typolink_conf);
				} else {
					$linkUrl = '';
				}

				if (!empty($data['lDEF']['title_' . $index]['vDEF']) || !empty($data['lDEF']['grafik_link_' . $index]['vDEF'])) {
					if (!empty($data['lDEF']['title_' . $index]['vDEF'])) {
						$titel = '<span>' . $data['lDEF']['title_' . $index]['vDEF'] . '</span>' ;
						if (!empty($linkUrl)) {
							$titel = '<a href="' . $linkUrl . '">' . $titel . '</a>';
						} 
					}
					$titel = '<div class="title">' . $titel . '</div>' ;
					if (!empty($data['lDEF']['grafik_' . $index]['vDEF'])) {
						$img = '<img src="' . $data['lDEF']['grafik_' . $index]['vDEF'] . '" />';
						if (!empty($linkUrl)) {
							$img = '<a href="' . $linkUrl . '">' . $img . '</a>';
						}
						$links = '<div class="left">'	. $titel . $img . '</div>';
					} else {
						$links = '<div class="left">'	. $titel . '</div>';
					}
					$weiter = '<span class="weiter"> ... <a class="internal-link" href="' . $linkUrl . '">weiter</a></span>';
					$rechts = '<div class="content">' . 
										$this->pi_RTEcssText($data['lDEF']['unterpunkte_' . $index]['vDEF'] . $weiter) . 
										'</div>';
					$out .= '<div id="elem_' . $index . '" class="he_jub_container">' .
										$links . $rechts . 
									'</div>';
				}
			}
		}
		return $out;
	}
	
	function renderTitle(&$flexFormData,$seitenart) {
		$out = '';
		if ($this->language=='de') {
			$titel = $flexFormData['allgemein']['lDEF']['title']['vDEF'];
		} else {
			$titel = $flexFormData['allgemein']['lDEF']['title_en']['vDEF'];
		}
		if (!empty($titel)) {
			$out = '<h1 class="' . $seitenart  . '">' . $titel . '</h1>';
		}
		return $out;
	}
	
	function erzeugeCarouselConfig(&$flexFormData) {
		$GLOBALS['TSFE']->additionalHeaderData['he_tools_pi2'] .= '
			<script type="text/javascript" src="/typo3conf/ext/he_tools/res/jquery.easing.1.3.js"></script>
			<script type="text/javascript" src="/typo3conf/ext/he_tools/res/jquery.moodular.js"></script>
			<script type="text/javascript" src="/typo3conf/ext/he_tools/res/jquery.moodular.controls.js"></script>
			<script type="text/javascript" src="/typo3conf/ext/he_tools/res/jquery.moodular.effects.js"></script>
			<script type="text/javascript">
				jQuery(document).ready(function () {
					jQuery(function($){
						$.extend($.fn.moodular.controls, {
		        	myButtons: function (m) {
								m.opts.bt_prev.bind("click", function () {
									m.prev();
									return false;
								});
								m.opts.bt_next.bind("click", function () {
									m.next();
									return false;
								});
							},
		        });
		      });  
			        
					jQuery("#slideshow #images").moodular({
						effects: "left",
						controls: "stopOver buttons",
						bt_prev : jQuery("#prev"),
						bt_next : jQuery("#next"),
						auto: true,
						api: true,
						easing: "",
						speed: 2000,
						dispTimeout: 5000
					});
		});	
			</script>
		';
		
		if ($this->language=='de') {
			$carousel['bilder'] = explode(',',$flexFormData['allgemein']['lDEF']['carousel_images']['vDEF']);
			$carousel['links'] = explode("\n",$flexFormData['allgemein']['lDEF']['carousel_links']['vDEF']);
			$carousel['tooltips'] = explode("\n",$flexFormData['allgemein']['lDEF']['carousel_tooltips']['vDEF']);
		} else {
			$carousel['bilder'] = explode(',',$flexFormData['allgemein']['lDEF']['carousel_images_en']['vDEF']);
			$carousel['links'] = explode("\n",$flexFormData['allgemein']['lDEF']['carousel_links_en']['vDEF']);
			$carousel['tooltips'] = explode("\n",$flexFormData['allgemein']['lDEF']['carousel_tooltips_en']['vDEF']);
		}
		return $carousel;
	}

	function renderCarousel($flexFormData) {
		$carousel = $this->erzeugeCarouselConfig($flexFormData);
		$out = '';
		if (count($carousel['bilder'])>1) {
			$out .= $this->createImageCarousel($carousel);
		} else {
			if (!empty($carousel['bilder'][0])) {
				$tooltip = $carousel['tooltips'][0];
				if (!empty($tooltip)) {
					$tooltip = ' title="' . $tooltip . '" alt="' . $tooltip . '" ';
				}
				
				$out .= '<div id="singleHeader">
								<img src="' . $carousel['bilder'][0] . '" ' . $tooltip . ' />
								</div>';				
			}
		}
		return $out;
	}

	function behandleSonderfaelleFakultaeten(&$konfiguration,&$flexFormData) {
		$fakultaet = '';
		if (strpos($flexFormData['allgemein']['lDEF']['title']['vDEF'],'Energie')>0 &&
				strpos($flexFormData['allgemein']['lDEF']['title']['vDEF'],'Umwelt')>0) {
			$fakultaet = 'GU';
		}
		switch ($fakultaet) {
			case 'GU':
				foreach ($konfiguration as $index=>$eintrag) {
					if ($eintrag['name']=='aktuelles') {
						$konfiguration[$index]['links']['fakultaets_news'] = 'Schwarzes Brett';
						$konfiguration[$index]['links']['hochschulkalender'] = 'Fakultätskalender';
					} else if ($eintrag['name']=='karriere_und_weiterbildung') {
						$konfiguration[$index]['links']['karriereservice'] = 'Stellenangebote';
					}
				}
				break;
		}
	}
	
	function renderFakultaetsSeite(&$konfiguration,&$flexFormData,$pfad) {
		$content = $this->renderTitle($flexFormData,'fakultaet');
		$content .= $this->renderCarousel($flexFormData);
		$this->behandleSonderfaelleFakultaeten($konfiguration,$flexFormData);
		$content .= $this->renderBildMitLinks($konfiguration,$flexFormData,$pfad);
		return $content;
	}
	
	function renderStudiengangSeite(&$konfiguration,&$flexFormData,$pfad) {
		$content = $this->renderTitle($flexFormData,'studiengang');
		$content .= $this->renderBildMitLinks($konfiguration,$flexFormData,$pfad);
		if ($this->language=='de') {
			$textLaufband = '&rsaquo;&rsaquo;&nbsp;Jetzt online bewerben!';
			$seiteBewerbung = $flexFormData['bewerbung']['lDEF']['bewerbung']['vDEF'];
			$datenUndFakten = $flexFormData['datenUndFakten'];
			$title = 'Daten und Fakten – auf einen Blick';
			$configField = 'STUDIENGANG_DATEN_UND_FAKTEN';
		} else {
			$textLaufband = '&rsaquo;&rsaquo;&nbsp;Apply online now!';
			$seiteBewerbung = $flexFormData['application']['lDEF']['international_applicants']['vDEF'];
			$datenUndFakten = $flexFormData['datenUndFaktenEn'];
			$title = 'Data and Facts – at a glance';
			$configField = 'STUDIENGANG_DATEN_UND_FAKTEN_EN';
		}
		$content .= $this->renderDatenUndFakten($title,$datenUndFakten,$configField);
		if ($this->onlineBewerbungAnzeigen($flexFormData)) {
			$content .= $this->renderLaufband($textLaufband,$seiteBewerbung);
		}
		return $content;
	}
	
	function onlineBewerbungAnzeigen($flexFormData) {
		return tx_he_tools_util::bewerbungOnlineMoeglich() &&
					 !$flexFormData['bewerbung']['lDEF']['online_bewerbung_deaktivieren']['vDEF'];
	}
	
	function renderLaufband($text,$seiteBewerbung) {
		$typolink_conf = array(
							'returnLast' => "url",
							'parameter' => $seiteBewerbung,
					);
		$url = $this->cObj->typolink("", $typolink_conf);
		$out = '<div class="laufband studiengaenge">
						<a target="_blank" href="' . $url . '">
						<marquee>' . $text . '</marquee>
						</a>
						</div>';
		return $out;
	}
	
	function renderDatenUndFakten($title,&$datenUndFakten,$configField) {
		$content = '<table class="tab100 daten_und_fakten">' .
							 '<thead><tr class="hg_dunkelblau">
							 	<th colspan="2">' . $title . '</th>
							 	</tr></thead>
							 	<tbody>
							 	';
		$mode = 'hg_hellblau';
		foreach (self::$tabConfig[$configField]['zeilen'] as $feld) {
			$data = $datenUndFakten['lDEF'][$feld];
			$wert = $data['vDEF'];
			if (!empty($wert)) {
				$content .= '<tr class="' . $mode . '">';
				$content .= '<td class="td50 label"> &raquo; ' . $feld . '</td>';
				$content .= '<td class="td50 content">' . $wert . '</td>';
				$content .= '</tr>';
				if ($mode=='hg_hellblau') {
					$mode = 'weiss';
				} else {
					$mode = 'hg_hellblau';
				}
			}
		}
		$content .= '</tbody></table>';
		return $content;
	}
	
	function renderBildMitLinks(&$konfiguration,$flexFormData,$pfad) {
		$elemType = 'left';
		foreach ($konfiguration as $elemData) {
			if ($elemData['lang']==$this->language) {
				$data = $flexFormData[$elemData['name']]['lDEF'];
				$out .= '<div class="he_img_links_block ' . $elemType . '">';
				$out .= '<div class="title">' . $elemData['label'] . '</div>';
				if (!empty($data['grafik']['vDEF'])) {
					$img = '<img src="' . $data['grafik']['vDEF'] . '" />';
					if (!empty($data['image_link']['vDEF'])) {
						$typolink_conf = array(
								'returnLast' => "url",
								'parameter' => $data['image_link']['vDEF'],
						);
						$linkUrl = $this->cObj->typolink("", $typolink_conf);
						$img = '<a href="' . $linkUrl . '">' . $img . '</a>';
					}
					$out .= '<div class="container_img">' . $img . '<div class="filter"></div></div>';
				}
			
				$linkListe = '';
				$linkConfig = $elemData['links'];
				foreach ($linkConfig as $name=>$label) {
					if (strpos($flexFormData['allgemein']['lDEF']['title']['vDEF'],'Soziale Arbeit')>0) {
						if ($label=='Labore und Institute') {
							$label = 'Institute';
						} elseif ($label=='Laboratories and Institutes') {
							$label = 'Institutes';
						}
					}				
// erst mal alle Links anzeigen
					if (empty($data[$name]['vDEF'])) {
						$data[$name]['vDEF'] = "#"; 
//						$linkListe .= '<li><a class="error">' . $label . '</a></li>';
						$linkListe .= '<li><a href="#">' . $label . '</a></li>';
					} else  {
						$typolink_conf = array(
								'returnLast' => "url",
								'parameter' => $data[$name]['vDEF'],
						);
						$link = $this->cObj->typolink("", $typolink_conf);
						$linkListe .= '<li><a href="' . $link . '">' . $label . '</a></li>';
					}
				}
				if (!empty($linkListe)) {
					$out .= '<ul class="link_list">' . $linkListe . '</ul>';
				}
				$out .= '</div>';
				if ($elemType=='left') {
					$elemType = 'right';
				} else {
					$elemType = 'left';
					$out .= '<br class="clear" />';
				}
			}
		}
		return $out;
	}

	function createImageCarousel($carouselData) {
		$out = '<div id="slideshow">';
		$out .=  '<a id="prev" href="#"></a>';
		$out .=  '<a id="next" href="#"></a>';
		$out .= '<ul id="images">';
		for ($i=0;$i<count($carouselData['bilder']);$i++) {
			$url = $carouselData['links'][$i];
			if (!empty($url)) {
				if (is_numeric($url)) {
					$typolink_conf = array(
							'returnLast' => "url",
							'parameter' => $url,
					);
					$url = $this->cObj->typolink("", $typolink_conf);
				} 
			}
			$tooltip = $carouselData['tooltips'][$i];
			if (!empty($tooltip)) {
				$tooltip = ' title="' . $tooltip . '" alt="' . $tooltip . '" ';
			}
			$elem = '<img src="' . $carouselData['bilder'][$i] . '"' . $tooltip . ' />';
			if (!empty($url)) {
				$elem = '<a href="' . $url . '">' . $elem . '</a>';
			}
			$out .= '<li>' . $elem . '</li>';
		}		
		$out .= '</ul>';
		$out .= '</div>';
		
		return $out;
	}

	function test() {
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
				$bearbeiterAnzeigen = $user=='mmirsch' || $user=='sapfeiff' || $user=='cmack';
				if ($bearbeiterAnzeigen) {
					if (!empty($datenBearbeiter['page'])) {
						$bearbeiterAnzeige = '<a class="internalLink" target="_blank" href="index.php?id=' . $datenBearbeiter['page'] . '">' . $datenBearbeiter['name'] . '</a>';
					} else {
						$bearbeiterAnzeige = '<a class="mail" href="mailto:' . $datenBearbeiter['name'] . ' <' . $datenBearbeiter['email'] . '>">' . $datenBearbeiter['name'] . '</a>';
					}
					$out = '
					<span class="pageEditor">
					Zuletzt bearbeitet von: ' . $bearbeiterAnzeige . '
					</span>
					';
				}
			}
			
		}
		return $out;
	
		
	}
	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_tools/pi2/class.tx_hetools_pi2.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_tools/pi2/class.tx_hetools_pi2.php']);
}

?>