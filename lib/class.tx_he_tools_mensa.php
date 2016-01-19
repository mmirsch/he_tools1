<?php 
require_once(t3lib_extMgm::extPath('he_portal') . 'lib/class.tx_he_portal_lib_gadgets.php');

class tx_he_tools_mensa {

  public function gibWochentag($tag,$kurzForm=TRUE) {
		if ($kurzForm) {
			switch ($tag) {
				case 1: return 'Mo';
				case 2: return 'Di';
				case 3: return 'Mi';
				case 4: return 'Do';
				case 5: return 'Fr';
				case 6: return 'Sa';
				case 0: return 'So';
			}
		} else {
			switch ($tag) {
				case 1: return 'Montag';
				case 2: return 'Dienstag';
				case 3: return 'Mittwoch';
				case 4: return 'Donnerstag';
				case 5: return 'Freitag';
				case 6: return 'Samstag';
				case 0: return 'Sonntag';
			}
			
		}
	}
	
	public function initGadget(&$cObj,$username,$gadgetId) {
		$out = tx_he_portal_lib_gadgets::renderGadgetHilfeText($gadgetId,$cObj,TRUE,$username);
		if (!empty($out)) {
			$out .= '<div class="hinweis">Anleitungstext ausblenden durch Anklicken des Icons 
							<a onclick="window.open(\'http://www.hs-esslingen.de/index.php?eID=he_portal&action=editGadgetSettings&gadgetId=32\',\'Einstellungen bearbeiten\',\'width=10,height=10,left=100,top=100\');">
							<img title="Einstellungen bearbeiten" style="vertical-align: -20%;" src="typo3conf/ext/he_portal/res/jquery/css/edit.gif" />
							</a>in der Titelleiste dieses Gadgets.</div>';
		}
		return $out;
	}
	
	public function pruefeFerien($startdatum,$standort) {
    $ferienConfig = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_hetools.']['mensa.'];
    $ferien['sommerferien'] = $ferienConfig['sommerferien.'][$standort . '.'];
    $ferien['weihnachtsferien'] = $ferienConfig['weihnachtsferien.'][$standort . '.'];

    $ferienText = '';
    if (count($ferien)==2) {
      foreach ($ferien as $ferienDaten) {
        if (count($ferienDaten)==2) {
          $datumsTeileStart = explode('.',$ferienDaten['start']);
          $datumsTeileEnde = explode('.',$ferienDaten['ende']);
          $ferienStart = mktime(0, 0, 0, $datumsTeileStart[1], $datumsTeileStart[0], $datumsTeileStart[2]);
          $ferienStart = floor($ferienStart/3600/24)*3600*24;
          $ferienEnde = mktime(0, 0, 0, $datumsTeileEnde[1], $datumsTeileEnde[0], $datumsTeileEnde[2]) + 86400;
          $ferienEnde = floor($ferienEnde/3600/24)*3600*24;

	        if ($ferienStart<=$startdatum && $startdatum<=$ferienEnde) {
            if ($standort=='fl') {
              $standortBezeichnung = 'Esslingen Flandernstrasse';
            } else {
              $standortBezeichnung = 'Esslingen Stadtmitte';
            }
            $ferienText = 'Die Mensa ' . $standortBezeichnung . ' ist<br />vom ' .
              $ferienDaten['start'] . ' bis zum ' . $ferienDaten['ende'] . ' geschlossen.';
          }
        }

      }
    }
		return $ferienText;
	}
	
	public function zeigeMensaDaten(&$parent) {
		$username = $GLOBALS['TSFE']->fe_user->user['username'];
		$gadgetId = tx_he_portal_lib_gadgets::gibGadgetId($GLOBALS['TSFE']->id);
		$out = $this->initGadget($parent->cObj,$username,$gadgetId);
		$gadgetEinstellungen = tx_he_portal_lib_gadgets::gadgetEinstellungenLaden($gadgetId, $username);
		$standort = tx_he_portal_lib_gadgets::gibGadgetEinstellungenWert($gadgetEinstellungen,'st');
		$preisanzeige = tx_he_portal_lib_gadgets::gibGadgetEinstellungenWert($gadgetEinstellungen,'pr');
		$speisenFilter = tx_he_portal_lib_gadgets::gibGadgetEinstellungenWert($gadgetEinstellungen,'sf');
		$filterListe = array();
		if (count($speisenFilter)>0) {
			foreach ($speisenFilter as $filter=>$val) {
				if ($val=='on') {
					$filterListe[] = $filter;
				}
			}
		}
		$GLOBALS['TSFE']->additionalHeaderData['he_tools'] .= '
				<link rel="stylesheet" type="text/css" href="' . t3lib_extMgm::siteRelPath('he_tools') . 'res/jquery-ui.css"/>
				<link rel="stylesheet" type="text/css" href="' . t3lib_extMgm::siteRelPath('he_tools') . 'res/he_mensa.css"/>
				<script>
				$(function() {
			    $( "#tabs" ).tabs();
			  });
			</script>';
		$feldZuordnung = $this->gibFeldZuordnung();
		$mensaDaten = $this->gibMensaDaten();
		
		$speisenListe = array('Tagesangebot',
													'Hauptgericht',
													'Bio',
													'Premium line',
													'Renner',
													'Tagessuppe'
													);

		$tagesDaten = array();
		
		$get = t3lib_div::_GET();		
		if ($get['week']==1) {
			$letzterTag = -1;
			foreach($mensaDaten as $datum=>$tagesSpeiseplan) {
				if ($letzterTag<$tagesSpeiseplan['w']) {
					$letzterTag = $tagesSpeiseplan['w'];
					unset($mensaDaten[$datum]);
				}
			}			
		}

		$letzterTag = -1;

		foreach($mensaDaten as $datum=>$tagesSpeiseplan) {
/*
      if (empty($startdatum)) {
        $startdatum = $datum;
      }
*/
			$ferien = $this->pruefeFerien($datum,$standort);
			$speiseDaten = array();
			foreach($speisenListe as $speise) {		
				$anzeigen = TRUE;		
				if (!empty($filterListe)) {
					$anzeigen = $this->pruefeSpeisenFilter($feldZuordnung,$tagesSpeiseplan,$filterListe,$speise);
				}
				if ($anzeigen) {
					$speiseDaten['datum'] = $tagesSpeiseplan['d'];
					$speiseDaten['Wochentag'] = $tagesSpeiseplan['w'];
					$speiseDaten[$speise] = $this->gibSpeisedaten($feldZuordnung,$tagesSpeiseplan,$speise);
				}
			}		
			if (empty($speiseDaten)) {
				$speiseDaten['datum'] = $tagesSpeiseplan['d'];
				$speiseDaten['Wochentag'] = $tagesSpeiseplan['w'];
			}
			$speiseDaten['ferien'] = $ferien;
			if ($letzterTag<$tagesSpeiseplan['w']) {
				$letzterTag = $tagesSpeiseplan['w'];
				$tagesDaten[] = $speiseDaten;
			} else {
				break;
			}
		}
		if ($standort=='fl') {
			$speisenListeAusgeben = array(
													'Tagesangebot',
													'Hauptgericht',
													'Bio',
													'Premium line',
													'Renner',
													'Tagessuppe'
													);
			$titel = 'Speiseplan<br />Esslingen Flandernstrasse';
		} else {
			$speisenListeAusgeben = array(
													'Tagesangebot',
													'Hauptgericht',
													'Bio',
													'Renner',
													'Tagessuppe'
													);
			$titel = 'Speiseplan<br />Esslingen Stadtmitte';
		}

		$out .= $this->gibWochenplanAus($parent->cObj,$titel,$tagesDaten,$speisenListeAusgeben,$standort,$preisanzeige,$get['week']);
		return $out;
	}
	
	public function gibWochenplanAus(&$cObj,$titel,&$tagesDaten,&$speisenListe,$standort,$preisanzeige,$woche) {
		$out = '<div class="mensa_speiseplan">';
		$logo = '<img src="fileadmin/images/layout/logos/logo_studentenwerk.png" />';
		$out .= '<div class="logo">
						' . $logo . '	
						</div>';
		$out .= '<div class="standort">' . $titel . '</div>';
    $out .= '<div class="content">';
    $out .= '<div id="tabs">';
    $tabList = '<ul>';
    $tabContent = '';

    for ($i=0;$i<count($tagesDaten);$i++) {
      $tag = $tagesDaten[$i]['Wochentag'];
      $wochentag = $this->gibWochentag($tag);
      $tabList .= '<li><a href="#tabs-' .  $i . '">' . $wochentag . ', ' . $tagesDaten[$i]['datum'] . '</a></li>';
      $tabContent .= '<div id="tabs-' .  $i . '">';
      $tabContent .= '<table class="tagesplan">';
      $bg = '';
      $speisenVorhanden = FALSE;
	    $ferien = $tagesDaten[$i]['ferien'];

	    if (!empty($ferien)) {
        $tabContent .= '<tr><td class="ferien"><h2>' . $ferien . '</h2></td></tr>';
      } else {
        foreach ($speisenListe as $speise) {
          if (!empty($tagesDaten[$i][$speise]['titel'])) {
            $speisenVorhanden = TRUE;
            if ($bg=='hell') {
              $bg = 'dunkel';
            } else {
              $bg = 'hell';
            }
            $tabContent .= '<tr class="' . $bg . '">
            <td class="gericht">' . $speise . '</td>
            <td class="titel">' . $tagesDaten[$i][$speise]['titel'] . '</td>
            ';
            if (!empty($tagesDaten[$i][$speise]['preis_stud']) ||
              $tagesDaten[$i][$speise]['preis_gast']) {
              $tabContent .= '<td class="preis">';
              if ($preisanzeige=='be') {
                $tabContent .= 'StudentIn: ' . $tagesDaten[$i][$speise]['preis_stud'] .
                  '&nbsp;&euro;<br />Gast: ' . $tagesDaten[$i][$speise]['preis_gast'] .
                  '&nbsp;&euro;';
              } else if ($preisanzeige=='ga') {
                $tabContent .= 'Gast: ' . $tagesDaten[$i][$speise]['preis_gast'] .
                  '&nbsp;&euro;';
              } else {
                $tabContent .= 'StudentIn: ' . $tagesDaten[$i][$speise]['preis_stud'] .
                  '&nbsp;&euro;';

              }
              $tabContent .= '
              </td>
              ';
            }
            $tabContent .= '</tr>';
          }
        }
        if (!$speisenVorhanden) {
          $tabContent .= '<tr>
            <td class="gericht">Keine Speisen für Ihre Auswahl</td>
            </tr>';
        }
      }
      $tabContent .= '</table>';
      $tabContent .= '</div>';
    }
    $typolink_conf = array(
        'parameter' => $GLOBALS['TSFE']->id,
    );
    if ($woche==1) {
      $typolink_conf['title'] = 'aktuelle Woche';
      $link = $cObj->typolink("<<", $typolink_conf);
    } else {
      $typolink_conf['additionalParams'] = '&week=1';
      $typolink_conf['title'] = 'kommende Woche';
      $link = $cObj->typolink(">>", $typolink_conf);
    }

    $tabList .= '</ul>';
    $tabList .= '<div class="next-week">' . $link . '</div>';
    $out .= $tabList;
    $out .= $tabContent;
    $out .= '</div>';
    $out .= '</div>';
		$out .= '</div>';
		return $out;
	}
		
	public function gibSpeisedaten(&$feldZuordnung,&$mensaDaten,&$speise) {
		foreach ($feldZuordnung[$speise] as $field=>$id) {
			$speiseDaten[$field] = $mensaDaten[$id]; 
		}
		return $speiseDaten;
	}
	
	public function pruefeSpeisenFilter(&$feldZuordnung,&$mensaDaten,$filterListe,$speise) {
		foreach ($filterListe as $filter) {
			$feldId = $feldZuordnung[$speise][$filter];
			if (!empty($mensaDaten[$feldId])) {
				return TRUE;
			}
		}
		return FALSE;
	}
	
	public function gibFeldZuordnung() {
		$felder = array(
				'datum'=>'Date',
				'id'=>'Beitrags-ID',
				'Beilagen'=> array(
							array(
									'titel' => 'Fixings 1',
									'preis_gast' => 'Fixings 1 price guest',
									'preis_stud' => 'Fixings 1 price student',
									'bio' => 'Fixings 1 icon bio',
									'fisch' => 'Fixings 1 icon fish',
									'veg' => 'Fixings 1 icon vegetarian',
									'lak' => 'Fixings 1 icon lactosefree',
									'zus' => 'Fixings 1 icon additive',
									),
							array(
									'titel' => 'Fixings 2',
									'preis_gast' => 'Fixings 2 price guest',
									'preis_stud' => 'Fixings 2 price student',
									'bio' => 'Fixings 2 icon bio',
									'fisch' => 'Fixings 2 icon fish',
									'veg' => 'Fixings 2 icon vegetarian',
									'lak' => 'Fixings 2 icon lactosefree',
									'zus' => 'Fixings 2 icon additive',
									),
							array(
									'titel' => 'Fixings 3',
									'preis_gast' => 'Fixings 3 price guest',
									'preis_stud' => 'Fixings 3 price student',
									'bio' => 'Fixings 3 icon bio',
									'fisch' => 'Fixings 3 icon fish',
									'veg' => 'Fixings 3 icon vegetarian',
									'lak' => 'Fixings 3 icon lactosefree',
									'zus' => 'Fixings 3 icon additive',
									),
							array(
									'titel' => 'Fixings 4',
									'preis_gast' => 'Fixings 4 price guest',
									'preis_stud' => 'Fixings 4 price student',
									'bio' => 'Fixings 4 icon bio',
									'fisch' => 'Fixings 4 icon fish',
									'veg' => 'Fixings 4 icon vegetarian',
									'lak' => 'Fixings 4 icon lactosefree',
									'zus' => 'Fixings 4 icon additive',
									),
				),
				'Nachspeisen'=> array(
							array(
									'titel' => 'Dessert 1',
									'preis_gast' => 'Dessert 1 price guest',
									'preis_stud' => 'Dessert 1 price student',
									'bio' => 'Dessert 1 icon bio',
									'fisch' => 'Dessert 1 icon fish',
									'veg' => 'Dessert 1 icon vegetarian',
									'lak' => 'Dessert 1 icon lactosefree',
									'zus' => 'Dessert 1 icon additive',
									),
							array(
									'titel' => 'Dessert 2',
									'preis_gast' => 'Dessert 2 price guest',
									'preis_stud' => 'Dessert 2 price student',
									'bio' => 'Dessert 2 icon bio',
									'fisch' => 'Dessert 2 icon fish',
									'veg' => 'Dessert 2 icon vegetarian',
									'lak' => 'Dessert 2 icon lactosefree',
									'zus' => 'Dessert 2 icon additive',
									),
							array(
									'titel' => 'Dessert 3',
									'preis_gast' => 'Dessert 3 price guest',
									'preis_stud' => 'Dessert 3 price student',
									'bio' => 'Dessert 3 icon bio',
									'fisch' => 'Dessert 3 icon fish',
									'veg' => 'Dessert 3 icon vegetarian',
									'lak' => 'Dessert 3 icon lactosefree',
									'zus' => 'Dessert 3 icon additive',
									),
				),
				'Tagesangebot'=> array(
						'titel' => 'Meal on a plate',
						'preis_gast' => 'Meal on a plate price guest',
						'preis_stud' => 'Meal on a plate price student',
						'bio' => 'Meal on a plate icon bio',
						'fisch' => 'Meal on a plate icon fish',
						'veg' => 'Meal on a plate icon vegetarian',
						'lak' => 'Meal on a plate icon lactosefree',
						'zus' => 'Meal on a plate icon additive',
						),
				'Hauptgericht'=> array(
						'titel' => 'Meal 1',
						'preis_gast' => 'Meal 1 price guest',
						'preis_stud' => 'Meal 1 price student',
						'bio' => 'Meal 1 icon bio',
						'fisch' => 'Meal 1 icon fish',
						'veg' => 'Meal 1 icon vegetarian',
						'lak' => 'Meal 1 icon lactosefree',
						'zus' => 'Meal 1 icon additive',
						),
				'Premium line'=> array(
						'titel' => 'Premium line',
						'preis_gast' => 'Premium line price guest',
						'preis_stud' => 'Premium line price student',
						'bio' => 'Premium line icon bio',
						'fisch' => 'Premium line icon fish',
						'veg' => 'Premium line icon vegetarian',
						'lak' => 'Premium line icon lactosefree',
						'zus' => 'Premium line icon additive',
						),
				'Bio'=> array(
						'titel' => 'Organic',
						'preis_gast' => 'Organic price guest',
						'preis_stud' => 'Organic price student',
						'bio' => 'Organic icon bio',
						'fisch' => 'Organic icon fish',
						'veg' => 'Organic icon vegetarian',
						'lak' => 'Organic icon lactosefree',
						'zus' => 'Organic icon additive',
						),
				'Renner'=> array(
						'titel' => 'Fast seller',
						'preis_gast' => 'Fast seller price guest',
						'preis_stud' => 'Fast seller price student',
						'bio' => 'Fast seller icon bio',
						'fisch' => 'Fast seller icon fish',
						'veg' => 'Fast seller icon vegetarian',
						'lak' => 'Fast seller icon lactosefree',
						'zus' => 'Fast seller icon additive',
					),
				'Tagessuppe'=> array(
						'titel' => 'Soup',
						'preis_gast' => 'Soup price guest',
						'preis_stud' => 'Soup price student',
						'bio' => 'Soup icon bio',
						'fisch' => 'Soup icon fish',
						'veg' => 'Soup icon vegetarian',
						'lak' => 'Soup icon lactosefree',
						'zus' => 'Soup icon additive',
				),
		);		
		$feldListe = $this->gibMensaDatenTitel();
		$feldZuordnungen = array();
		foreach ($felder as $feldTitel=>$feldDaten) {
			$datenNeu = array();
			if ($feldTitel=='id' || $feldTitel=='datum') {
				$datenNeu = array_search($feldDaten,$feldListe);
			} else if ($feldTitel=='Beilagen' || $feldTitel=='Nachspeisen') {
				foreach ($feldDaten as $eintrag) {
					$eintraegeNeu = array();
					foreach ($eintrag as $name=>$titel) {
						$id = array_search($titel,$feldListe);
						$eintraegeNeu[$name] = $id;
					}
					$datenNeu[] = $eintraegeNeu;
				}
			} else {
				foreach ($feldDaten as $name=>$titel) {
					$id = array_search($titel,$feldListe);
					$datenNeu[$name] = $id;
				}
			}
			$feldZuordnungen[$feldTitel] = $datenNeu;
		}
		return $feldZuordnungen;
	}
	
	function gibMensaDatenTitel() {
		$where = 'datum=0';
		$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_hetools_mensa',$where);
		$dbDaten = array();
		if ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
			$mensaDatenTitel = unserialize($daten['tagesplan']); 
		}
		return $mensaDatenTitel;
	}
	
	function gibMensaDaten() {
		$where = 'datum>0';
		$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_hetools_mensa',$where);
		$dbDaten = array();
		while ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
			$dbDaten[$daten['datum']] = unserialize($daten['tagesplan']); 
		}
		return $dbDaten;
	}
}



?>