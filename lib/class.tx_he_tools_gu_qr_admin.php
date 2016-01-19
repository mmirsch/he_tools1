<?php

class tx_he_tools_gu_qr_admin {

  protected $css = '

    h2.error {
      background-color: #E11C3E;
      border: 1px solid #E11C3E;
      color: #fff;
    }
    div.row {
      clear: left;
    }
    fieldset {
      border: 1px solid #004666;
      padding: 10px;
    }
    legend {
      position: relative;
      font-size: 120%;
      font-weight: bold;
      color: #3b7089;
      padding: 10px 0px 15px 0px;
    }
    label {
        width: 200px;
        text-align: right;
        float: left;
        padding-right: 6px;
    }
    input[type=text] {
        width: 150px;
        text-align: left;
        float: left;
        padding-left: 4px;
    }

    input[type=text].em1 {
        width: 1em;
    }

    input[type=text].em2 {
        width: 2em;
    }

    input[type=text].em3 {
        width: 3em;
    }

    input[type=text].em5 {
        width: 5em;
    }

    input[type=text].em30 {
        width: 30em;
    }

    select {
      float: left;
    }
    
    input.hidden {
    	display: none;
    }
    
    .content {
    	padding: 20px 40px;
    }
    
  ';

  protected $js = '
  $( document ).ready(function() {
  	$("#gewerk").change(function() {
  		if ($(this).val()=="U") {
  			$("#untergeschoss").removeClass("hidden");
  			if (! $("#obergeschoss").hasClass("hidden")) {
  				$("#obergeschoss").addClass("hidden");
  			}  			
			} else if ($(this).val()=="O") {
  			$("#obergeschoss").removeClass("hidden");
  			if (! $("#untergeschoss").hasClass("hidden")) {
  				$("#untergeschoss").addClass("hidden");
  			}  			
			} else {
				if (! $("#obergeschoss").hasClass("hidden")) {
  				$("#obergeschoss").addClass("hidden");
  			} 
				if (! $("#untergeschoss").hasClass("hidden")) {
  				$("#untergeschoss").addClass("hidden");
  			} 
			}
		});
	});
  ';
  
  public function main(&$piBase,$get) {
  	/*
  	 * Stylesheet und Javascript einbinden
  	 */
    $GLOBALS['TSFE']->additionalHeaderData['he_tools'] .= 
      '<style type="text/css">' . $this->css . '</style>' .
      '<script type="text/javascript">' . $this->js . '</script>'
      ;
      /*
       * Adminzugang für spezielle Funktionen festlegen
       */
		if ($GLOBALS['TSFE']->fe_user->user['username']=='mdepenna' ||
    		$GLOBALS['TSFE']->fe_user->user['username']=='mmirsch') {
			$adminLogin = true;
		} else {
			$adminLogin = false;
		}
		/*
		 * Formulardaten per POST abfragen 
		 */
    $post = t3lib_div::_POST();
    
    if (!empty($get['export']) && $adminLogin) {
      $out = $this->eintraegeExportierenCsv();
    } else if (empty($get['code']) && empty($post['qr_id'])) {
      $out = $this->suchFormular($post);
    } else {
    	$code = $get['code'];
    	if (empty($get['code'])) {
    		$code = $post['qr_id'];
    	}
    	if (strlen(utf8_decode($code))!=15) {
    		$out = '<h2>Der übergebene Code ist ungültig!</h2>';
    	} else {
    		$eintrag = $this->eintragVorhanden($code,'dp');
    		if ($eintrag) {
    			$out = $this->eintragAusgeben($eintrag);
    		} else {
    			if (!empty($post['absenden'])) {
    				$fehlerListe = $this->pruefeEingaben($post);
    				if (!empty($fehlerListe)) {
    					$out = $this->formularAnzeigen($post, $code, $fehlerListe);
    				} else {
    					$erg = $this->eintragAnlegen($post);
    					if (!$erg) {
    						$out = '<h2 class="error">Der Eintrag konnte nicht gespeichert werden!</h2>';
    					} else {
    						$out = '<h2>Der Eintrag wurde erfolgreich gespeichert!</h2>';
    						$eintrag = $this->eintragVorhanden($post['qr_id'],'dp');
    						$out .= $this->eintragAusgeben($eintrag);
    					}
    				}
    			} else {
    				$out = $this->formularAnzeigen($post, $code);
    			}
    		}
    	}
    }
    if ($adminLogin) {
    	$id = $GLOBALS['TSFE']->id;
    	$out .= '<p><br><a class="button" target="_blank" href="index.php?id=' . $id . '&export=1">Daten exportieren</a></p>';
    }
    return $out;
  }

  /*
   * Formulardaten validieren
   */
  public function pruefeEingaben(&$post) {
    $fehlerListe = array();
    foreach($post as $key=>$val) {
    	if ($key=='untergeschoss') {
    		if ($post['gewerk']=='U' && empty($val) && $val!='0') {
    			$fehlerListe[] = $key;
    		}
    	} else if ($key=='obergeschoss') {
    		if ($post['gewerk']=='O' && empty($val) && $val!='0') {
    			$fehlerListe[] = $key;
    		}
    	} else {
    		if (empty($val) && $val!='0') {
        	$fehlerListe[] = $key;
    		}
      }
    }
    return $fehlerListe;
  }

  /*
   * Diese Methode überprüft, ob ein Eintrag mit dem angegebenen Code bereits in der Datenbank existiert
   */
  public function eintragVorhanden($eingabe,$modus) {
    if ($modus=='ow') {
      $where = 'dp_key="' . $eingabe . '" AND deleted=0 AND hidden=0';
    } else {
      $where = 'qr_id="' . $eingabe . '" AND deleted=0 AND hidden=0';
    }
    $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('dp_key,klartext,qr_id','tx_he_qr_info',$where);
    $eintrag = false;
    if ($data = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
      $eintrag = $data;
    }
    return $eintrag;
  }

  /*
   * Gefundenen Eintrag ausgeben
   */
  function eintragAusgeben($eintrag)  {
    $klartext = $eintrag['klartext'];
    $dpKey = $eintrag['dp_key'];
    $qrId = $eintrag['qr_id'];
    $out =  '<h1>Datenpunkt-Adressierungsschlüssel gefunden</h1>' .
            '<h2>Bezeichnung</h2>' . $klartext . '<br />' .
            '<h2>Datenpunkt-Adressierungsschlüssel</h2>' . $dpKey .
            '<h2>OW Datenpunktschlüssel</h2>' . $qrId;
    return $out;
  }

  /*
   * Formulardaten in der Datenbank anleen
   */
  public function eintragAnlegen($post) {
    $id = $GLOBALS['TSFE']->id;
    $data['pid'] = $id;
    $data['tstamp'] = time();
    $data['crdate'] = time();
    $data['klartext'] = $post['klartext'];
    $data['qr_id'] = $post['qr_id'];
    if (!empty($post['untergeschoss'])) {
    	$post['gewerk'] = 'u' . $post['untergeschoss'];
    } else if (!empty($post['obergeschoss'])) {
    	$post['gewerk'] = sprintf('%02d',$post['obergeschoss']);
    }
    /*
     * Eingabedaten in festgelegtes Format umwandeln
     */
		$data['dp_key'] = sprintf('%\'_5s-%\'_3s-%-\'_2s%02d-%3s-%02d-%\'_3s-%3s-%02d-%2s-%2s',
			$post['liegenschaft'],
	    $post['gebaeude'],
	    $post['gewerk'],
	    $post['gewerk_nr'],
	    $post['anlagebezeichnung'],
	    $post['lfd_nr'],
	    $post['regelfabrikat'],
	    $post['anlagenteil'],
	    $post['lfd_nr2'],
	    $post['datenpunktart'],
	    $post['datenpunktbeschreibung']
    );
    $res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_he_qr_info',$data);
    return $res;
  }

  /*
   * Suchformular
   */
  public function suchFormular($post) {
    if (!empty($post['eingabe'])) {
      $eingabe = htmlspecialchars($post['eingabe']);
    } else {
      $eingabe = '';
    }
    $out = '';
    $id = $GLOBALS['TSFE']->id;
    if ((!empty($post['dp-suchen']) || !empty($post['ow-suchen']))&& !empty($eingabe)) {
      if (!empty($post['dp-suchen'])) {
        $modus = 'dp';
      } else {
        $modus = 'ow';
      }
			/*
			 * Eintrag in der Datenbank suchen
			 */
      $eintrag = $this->eintragVorhanden($eingabe,$modus);
      if ($eintrag) {
        $out = $this->eintragAusgeben($eintrag);
      } else {
        $out = '<h2 class="error">Der Eintrag konnte nicht gefunden werden!</h2>';
      }
    }
    /*
     * Formular
     */
    $out .= '<h1>Datenpunkt-Adressierungsschlüssel/OW Datenpunktschlüssel Suche</h1>';
    $out .= '<form action="index.php?id=' . $id . '" method="POST">';
    $out .= '<fieldset><legend>Bezeichnung</legend>';
    $out .= '<div class="row">' .
      '<label for="eingabe">Bezeichnung (maximal 30 Zeichen)</label>' .
      '<input class="em30" type="text" id="eingabe" name="eingabe" size="30" maxlength="40" value="' . $eingabe . '" />' .
      '</div>';
    $out .= '<div class="row">' .
      '<input type="submit" id="dp-suchen" name="dp-suchen" value="Datenpunktschlüssel suchen" />' .
      '<input type="submit" id="ow-suchen" name="ow-suchen" value="OW-Datenpunktschlüssel suchen" />' .
      '</div>';
    $out .= '</fieldset></form>';
    return $out;
  }

  /*
   * Formular zum Eintragen eines neuen Elements 
   */
  public function formularAnzeigen($post, $qrId, $fehlerListe=array()) {
    $id = $GLOBALS['TSFE']->id;
    $out = '<h1>Datenpunkt-Adressierungsschlüssel eintragen</h1>';
    if (!empty($fehlerListe)) {
      $out .= '<h2 class="error">Sie haben nicht alle Felder ausgefüllt!</h2>';
    }
    $out .= '<form action="index.php?id=' . $id . '" method="POST">';
    $out .= '<input type="hidden" name="qr_id" value="' . $qrId . '" />';
    if (isset($post['klartext'])) {
      $value = htmlspecialchars($post['klartext']);
    } else {
      $value = '';
    }
    $out .= '<fieldset><legend>Bezeichnung</legend>';
    $out .= '<div class="row">' .
              '<label for="klartext">Bezeichnung (maximal 30 Zeichen)</label>' .
              '<input class="em30" type="text" id="klartext" name="klartext" size="30" maxlength="30" value="' . $value . '" />' .
            '</div>';
    $out .= '</fieldset>';
    $out .= '<fieldset><legend>Datenpunkt-Adressierungsschlüssel</legend>';
    if (isset($post['liegenschaft'])) {
      $value = htmlspecialchars($post['liegenschaft']);
    } else {
      $value = '';
    }
    $out .= '<div class="row">' .
              '<label for="liegenschaft">Liegenschaftsnummer (Auszug)</label>' .
              '<input class="em5" type="text" id="liegenschaft" name="liegenschaft" size="5" maxlength="5" value="' . $value . '" />' .
            '</div>';
    if (isset($post['gebaeude'])) {
      $value = htmlspecialchars($post['gebaeude']);
    } else {
      $value = '';
    }
    $out .= '<div class="row">' .
      '<label for="gebaeude">Gebäude-Nummer</label>' .
      '<input class="em3" type="text" id="gebaeude" name="gebaeude" size="3" maxlength="3" value="' . $value . '" />' .
      '</div>';

    $out .= '<div class="row">' .
      '<label for="gebaeude">Gewerk</label>' .
      '<select id="gewerk" name="gewerk">' .
      '<option value="">Bitte auswählen</option>' .
      '<option value="A">Aufzugs- und Fördertechnik</option>' .
      '<option value="E">Elektrotechnische Anlagen</option>' .
      '<option value="G">Gasanlagen</option>' .
      '<option value="H">Heizungstechnische Anlagen</option>' .
      '<option value="I">Informationstechnische und Sicherheitstechnische Anlagen</option>' .
      '<option value="K">Kältetechnische Anlagen</option>' .
      '<option value="L">Lufttechnische Anlagen</option>' .
      '<option value="N">Nutzungsspezifische Anlagen</option>' .
      '<option value="S">Sanitärtechnische Anlagen</option>' .
      '<option value="Y">Gewerkeübergreifende Anlagen</option>' .
      '<option value="Z">Energiemanagement/Verbrauchserfassung</option>' .
      '<option value="R">Raum</option>' .
      '<option value="U">Untergeschoss</option>' .
      '<option value="EG">Erdgeschoss</option>' .
      '<option value="O">Obergeschoss</option>' .      
      '</select>' .
      '<input class="hidden em1" type="text" id="untergeschoss" name="untergeschoss" size="1" maxlength="1" />' .
      '<input class="hidden em2" type="text" id="obergeschoss" name="obergeschoss" size="2" maxlength="2" />' .
      '</div>';
  
    
    if (isset($post['gebauede_nr'])) {
      $value = htmlspecialchars($post['gebauede_nr']);
    } else {
      $value = '';
    }
    $out .= '<div class="row">' .
      '<label for="gebaeude">Laufende Nummer Gewerk</label>' .
       '<input class="em2" type="text" id="gewerk_nr" name="gewerk_nr" size="2" maxlength="2" value="' . $value . '" />' .
      '</div>';

    $out .= '<div class="row">' .
      '<label for="anlagebezeichnung">Bezeichnung der Anlage</label>' .
      '<select id="anlagebezeichnung" name="anlagebezeichnung">' .
      '<option value="">Bitte auswählen</option>' .
      '<option value="AA_">Abluftanlage</option>' .
      '<option value="AFF">Antennen-, Funk-, Fernsehanlage</option>' .
      '<option value="ANA">Anlage Nachtauskühlung (Fensterlüftung)</option>' .
      '<option value="AUF">Hebeanlage</option>' .
      '<option value="AUL">Aufzug Lasten</option>' .
      '<option value="AUP">Aufzug Personen</option>' .
      '<option value="AV_">Allgemeine Stromversorgung</option>' .
      '<option value="AWA">Abwasseranlage</option>' .
      '<option value="AWB">Abwasserbehandlungsanlage</option>' .
      '<option value="AWH">Abwasserhebeanlage</option>' .
      '<option value="BEA">Beleuchtungsanlage außen</option>' .
      '<option value="BEI">Beleuchtungsanlage innen</option>' .
      '<option value="BES">Beleuchtung als Sicherheitsbeleuchtung</option>' .
      '<option value="BMA">Brandmeldeanlage</option>' .
      '<option value="BNR">Behindertennotruf</option>' .
      '<option value="BUS">Übertragungsnetz, Komponenten</option>' .
      '<option value="CO_">CO-Warnanlage</option>' .
      '<option value="DEA">Druckerhöhungsanlage</option>' .
      '<option value="DLA">Druckluftanlage</option>' .
      '<option value="DRA">Druckregelanlage</option>' .
      '<option value="DSA">Dosieranlage</option>' .
      '<option value="DV_">Datenverarbeitung</option>' .
      '<option value="EFA">Entfeuchtungsanlage</option>' .
      '<option value="EHA">Enthärtungsanlage</option>' .
      '<option value="EIN">Einspeisung (öffentliches Versorgungsnetz)</option>' .
      '<option value="ELA">Elektroakustische Anlage</option>' .
      '<option value="EMA">Einbruchmeldeanlage (auch Überfall)</option>' .
      '<option value="EOA">Energieoptimierungsanlage</option>' .
      '<option value="ERD">Erdungs- und Blitzschutzanlage</option>' .
      '<option value="ESA">Entsalzungsanlage</option>' .
      '<option value="FIA">Filteranlage</option>' .
      '<option value="FLA">Feuerlöschanlage (Sprinkleranlage, Gaslöschanlage)</option>' .
      '<option value="FTS">Fluchttürsysteme, -steueranlage, Rettungswegtechnik</option>' .
      '<option value="GAL">Gaslagerungsanlage</option>' .
      '<option value="GAR">Gasregelstrecke</option>' .
      '<option value="GAW">Gaswarnanlage</option>' .
      '<option value="GHV">Gebäudehauptverteilung</option>' .
      '<option value="GUV">Gebäudeunterverteilung</option>' .
      '<option value="HAS">Hausanschlussstation (HAST), Übergabe-, Trafostation</option>' .
      '<option value="HER">Be-Heizung (Einläufe, Rinnen, Rohre)</option>' .
      '<option value="HKR">Heizkreis</option>' .
      '<option value="ISP">Informationsschwerpunkt</option>' .
      '<option value="KEA">Kälteerzeugungsanlage</option>' .
      '<option value="KTA">Küchentechnische Anlage</option>' .
      '<option value="KVA">Kälteverteilanlage</option>' .
      '<option value="LFA">Leichtflüssigkeitsabscheider</option>' .
      '<option value="LWA">Leckwarnanlage</option>' .
      '<option value="MLA">Medizin-/Labortechnische Anlage</option>' .
      '<option value="MMA">Multimediaanlage</option>' .
      '<option value="MSA">Mittelspannungsanlage</option>' .
      '<option value="MSS">Mittelspannungsschaltanlage</option>' .
      '<option value="MZE">Mess-/Zählwerterfassung, Wetterstation</option>' .
      '<option value="NEA">Netzersatzanlage</option>' .
      '<option value="NEU">Neutralisationsanlage</option>' .
      '<option value="NSS">Niederspannungsschaltanlage</option>' .
      '<option value="PVA">Photovoltaikanlage</option>' .
      '<option value="RBS">Raumbedienstation</option>' .
      '<option value="RKA">Rückkühlanlage</option>' .
      '<option value="RWA">Rauchwärmeabzugsanlage</option>' .
      '<option value="SIG">Signalanlage</option>' .
      '<option value="SON">Sonnenschutz- und Verdunklungsanlage</option>' .
      '<option value="SRA">Schrankenanlage</option>' .
      '<option value="SV_">Sicherheitsstromversorgung</option>' .
      '<option value="TAA">Tankstellen-, Tankanlage</option>' .
      '<option value="TEL">Telekommunikationsanlage</option>' .
      '<option value="TKA">Teilklimaanlage</option>' .
      '<option value="TTA">Tür-, Toranlage</option>' .
      '<option value="USV">unterbrechungsfreie Stromversorgung</option>' .
      '<option value="VID">Video-, Überwachungsanlage</option>' .
      '<option value="VKA">Vollklimaanlage</option>' .
      '<option value="WAA">Wasseranlage</option>' .
      '<option value="WAU">Wasseraufbereitungsanlage a. Trinkwasser)</option>' .
      '<option value="WEA">Wärmeerzeugungsanlage</option>' .
      '<option value="WED">Wassererwärmer dezentral</option>' .
      '<option value="WEZ">Wassererwärmungsanlage zentral</option>' .
      '<option value="WPU">Wärmepumpe</option>' .
      '<option value="WRG">Wärmerückgewinnungsanlage</option>' .
      '<option value="WVN">Wärmeverteilnetz</option>' .
      '<option value="ZA_">Zuluftanlage</option>' .
      '<option value="ZAA">Zu- und Abluftanlage</option>' .
      '<option value="ZDA">Zeitdienstanlage</option>' .
      '<option value="ZKA">Zugangskontrollanlage</option>' .
      '</select>' .
      '</div>';
    if (isset($post['lfd_nr'])) {
      $value = htmlspecialchars($post['lfd_nr']);
    } else {
      $value = '';
    }
    $out .= '<div class="row">' .
      '<label for="lfd_nr">Laufende Nummer Anlage</label>' .
      '<input class="em2" type="text" id="lfd_nr" name="lfd_nr" size="2" maxlength="2" value="' . $value . '" />' .
      '</div>';
    if (isset($post['regelfabrikat'])) {
      $value = htmlspecialchars($post['regelfabrikat']);
    } else {
      $value = '';
    }
    $out .= '<div class="row">' .
      '<label for="regelfabrikat">Regelfabrikat</label>' .
      '<input class="em3" type="text" id="regelfabrikat" name="regelfabrikat" size="3" maxlength="3" value="' . $value . '" />' .
      '</div>';
    $out .= '<div class="row">' .
      '<label for="anlagenteil">Anlagenteil</label>' .
      '<select id="anlagenteil" name="anlagenteil">' .
      '<option value="">Bitte auswählen</option>' .
      '<option value="AB_">Abluft</option>' .
      '<option value="AKT">Aktor allgemein</option>' .
      '<option value="AST">Automatisierungsstation (allgemein)</option>' .
      '<option value="ATR">Antrieb (Tür, Tor, Schranke, Fenster, Jalousie)</option>' .
      '<option value="AU_">Außenluft</option>' .
      '<option value="AUK">Außenluftklappe</option>' .
      '<option value="AUM">Aufzugsmaschine</option>' .
      '<option value="AUT">Aufzugstür</option>' .
      '<option value="BAT">Batterie/Akkumulatoren</option>' .
      '<option value="BEF">Befeuchter</option>' .
      '<option value="BSK">Brandschutzklappe</option>' .
      '<option value="BRE">Brenner</option>' .
      '<option value="DAE">Dacheinlauf, Dachrinne (Heizung)</option>' .
      '<option value="DE_">Druckerhöhung</option>' .
      '<option value="DH_">Druckhaltung</option>' .
      '<option value="DR_">Druckreduzierung</option>' .
      '<option value="EG_">Endgerät allgemein(auch Datenendgerät)</option>' .
      '<option value="EH_">Erhitzer</option>' .
      '<option value="EIN">Spannungsversorgung</option>' .
      '<option value="ELS">Endlagenschalter</option>' .
      '<option value="ENK">Entrauchungsklappe</option>' .
      '<option value="ERR">Einzelraumregler</option>' .
      '<option value="FBH">Fußbodenheizung</option>' .
      '<option value="FI_">Filter allgemein</option>' .
      '<option value="FIA">Filter Abluft</option>' .
      '<option value="FIG">Filter Gas</option>' .
      '<option value="FIL">Filter Luft</option>' .
      '<option value="FIR">Rückspülfilter</option>' .
      '<option value="FIU">Filter Außenluft</option>' .
      '<option value="FIW">Filter Wasser</option>' .
      '<option value="FIZ">Filter Zuluft</option>' .
      '<option value="FO_">Fortluft</option>' .
      '<option value="FOK">Fortluftklappe</option>' .
      '<option value="FU_">Frequenzumrichter</option>' .
      '<option value="GEN">Generator</option>' .
      '<option value="HKE">Heizkessel</option>' .
      '<option value="INT">intern (anlagenintern)</option>' .
      '<option value="ISO">Isolationsüberwachung</option>' .
      '<option value="JAK">Jalousieklappe</option>' .
      '<option value="KGK">Küchenkühlgerät</option>' .
      '<option value="KGW">Küchenwärmegerät</option>' .
      '<option value="KLK">Kühlwasserkreislauf</option>' .
      '<option value="KLP">Primärkreislauf</option>' .
      '<option value="KLS">Sekundärkreislauf</option>' .
      '<option value="KLW">Kaltwasserkreislauf</option>' .
      '<option value="KM_">Kältemaschine/Kälteerzeuger</option>' .
      '<option value="KOM">Kompressor, Verdichter</option>' .
      '<option value="KON">Kondensator, Verflüssiger</option>' .
      '<option value="KUD">Kühl-/Lüftungsdecke</option>' .
      '<option value="KUL">Kühler, Verdampfer</option>' .
      '<option value="KUR">Kühlraum/Kühlzelle/Tiefkühltruhe</option>' .
      '<option value="LBF">Luftbefeuchter</option>' .
      '<option value="LEA">Leuchte allgemein</option>' .
      '<option value="LER">Leuchte Rettungsweg (Rettungsweg-Kennzeichenleuchte)</option>' .
      '<option value="LES">Leuchte Sicherheitsbeleuchtung</option>' .
      '<option value="LE_">Luft-Erhitzer</option>' .
      '<option value="LK_">Luft-Kühler</option>' .
      '<option value="LKE">Luft-Kühler und -Erhitzer</option>' .
      '<option value="LNK">Luft-Nachkühler</option>' .
      '<option value="LNE">Luft-Nacherhitzer</option>' .
      '<option value="LVE">Luft-Vorerhitzer</option>' .
      '<option value="LVK">Luft-Vorkühler</option>' .
      '<option value="LS_">Leistungsschalter (allgemein)</option>' .
      '<option value="LSF">Fehlerstromschutzschalter</option>' .
      '<option value="LSR">Leser</option>' .
      '<option value="LTS">Lasttrennschalter</option>' .
      '<option value="LZ_">Leitzentrale</option>' .
      '<option value="M__">Melder allgemein</option>' .
      '<option value="MBM">Bewegungs-/Präsenzmelder</option>' .
      '<option value="MBR">Brandmelder</option>' .
      '<option value="MEB">Einbruchmelder (auch Überfallmelder)</option>' .
      '<option value="MGM">Gasmelder</option>' .
      '<option value="MRM">Rauchmelder</option>' .
      '<option value="MES">Messeinrichtung (physikalischer Größen)</option>' .
      '<option value="MI_">Mischluft</option>' .
      '<option value="MOT">Motor (allgemein)</option>' .
      '<option value="PU_">Pumpe allgemein</option>' .
      '<option value="PUE">Erhitzerpumpe</option>' .
      '<option value="PUK">Kühlwasserpumpe</option>' .
      '<option value="PUL">Ladepumpe</option>' .
      '<option value="PUP">Pumpe Primärkreislauf</option>' .
      '<option value="PUU">Umwälzpumpe</option>' .
      '<option value="PUW">Kaltwasserpumpe</option>' .
      '<option value="PUZ">Zirkulationspumpe</option>' .
      '<option value="R__">Raum(-luft)</option>' .
      '<option value="REG">Regler/Controller</option>' .
      '<option value="RL_">Rücklauf</option>' .
      '<option value="SCH">Schalter</option>' .
      '<option value="SEN">Sensor allgemein</option>' .
      '<option value="SI_">Sicherung, Auslöser</option>' .
      '<option value="SIE">Sicherheitseinrichtung</option>' .
      '<option value="SIG">Signalisierung (optisch, akustisch)</option>' .
      '<option value="SIL">Sicherungslasttrennschalter</option>' .
      '<option value="SP_">Speicher</option>' .
      '<option value="SPU">Spannungsüberwachung (Phasenüberwachg.)</option>' .
      '<option value="TAN">Tank/Behälter</option>' .
      '<option value="TL_">Terminal/Tableau</option>' .
      '<option value="TRA">Transformator</option>' .
      '<option value="TRO">Trockner</option>' .
      '<option value="TTF">Tür-, Tor-, Fensterkontakt</option>' .
      '<option value="UM_">Umluft</option>' .
      '<option value="UMK">Umluftklappe</option>' .
      '<option value="USS">Überspannungsschutz, Phasenwächter V__ Ventil</option>' .
      '<option value="VB_">Ventil Bypass</option>' .
      '<option value="VD_">Ventil Durchgang</option>' .
      '<option value="VE_">Ventil Erhitzer</option>' .
      '<option value="VHK">Ventil Heizkreis</option>' .
      '<option value="VK_">Ventil Kühler</option>' .
      '<option value="VNE">Ventil Nacherhitzer</option>' .
      '<option value="VNK">Ventil Nachkühler</option>' .
      '<option value="VVE">Ventil Vorerhitzer</option>' .
      '<option value="VVK">Ventil Vorkühler</option>' .
      '<option value="VWR">Ventil Wärmerückgewinnung</option>' .
      '<option value="VWT">Ventil Wärmetauscher</option>' .
      '<option value="VL_">Vorlauf</option>' .
      '<option value="VR_">Ventilator</option>' .
      '<option value="VRA">Abluftventilator</option>' .
      '<option value="VRU">Umluftventilator</option>' .
      '<option value="VRZ">Zuluftventilator</option>' .
      '<option value="VSR">Volumenstromregler</option>' .
      '<option value="VST">Verstärker</option>' .
      '<option value="WE_">Wärmerzeuger</option>' .
      '<option value="WRG">Wärmerückgewinnung</option>' .
      '<option value="WT_">Wärmetauscher</option>' .
      '<option value="Z__">Zähler (Verbrauchszähler/Maximumzähler)</option>' .
      '<option value="ZTR">Zentrale allgemein</option>' .
      '<option value="ZU_">Zuluft</option>' .
      '</select>' .
      '</div>';
    $out .= '<div class="row">' .
      '<label for="lfd_nr2">Laufende Nummer Anlagenteil</label>' .
      '<input class="em2" type="text" id="lfd_nr2" name="lfd_nr2" size="2" maxlength="2" value="' . $value . '" />' .
      '</div>';
    $out .= '<div class="row">' .
      '<label for="datenpunktart">Datenpunktart</label>' .
      '<select id="datenpunktart" name="datenpunktart">' .
      '<option value="">Bitte auswählen</option>' .
      '<option value="AM">Alarm-/Gefahrenmeldung</option>' .
      '<option value="BM">Betriebsmeldung</option>' .
      '<option value="SM">Störmeldung</option>' .
      '<option value="WM">Wartungsmeldung</option>' .
      '<option value="SB">Schaltbefehl</option>' .
      '<option value="ST">Stellbefehl</option>' .
      '<option value="MW">Messwert</option>' .
      '<option value="ZW">Zählwert</option>' .
      '<option value="PA">Parameter, Sollwert (-verstellung)</option>' .
      '<option value="VA">Virtueller Datenpunkt analog</option>' .
      '<option value="VD">Virtueller Datenpunkt digital</option>' .
      '<option value="VS">Virtueller Datenpunkt »Szene«</option>' .
      '<option value="VZ">Virtueller Datenpunkt »Zeitprogramm«</option>' .
      '<option value="ZP">Zeitprogramm</option>' .
      '</select>' .
      '</div>';
    $out .= '<div class="row">' .
      '<label for="datenpunktbeschreibung">Datenpunktbeschreibung</label>' .
      '<select id="datenpunktbeschreibung" name="datenpunktbeschreibung">' .
      '<option value="">Bitte auswählen</option>' .
      '<option value="00">Sonderfall</option>' .
      '<option value="01">Aus, Zu</option>' .
      '<option value="02">Ein, Auf, Stufe 1</option>' .
      '<option value="03">Stufe 2</option>' .
      '<option value="04">Stufe 3</option>' .
      '<option value="05">Stufe 4</option>' .
      '<option value="06">Zentral Aus, Zentral Zu</option>' .
      '<option value="07">Zentral Ein, Zentral Auf, Hauptschaltbefehl</option>' .
      '<option value="08">Anforderung</option>' .
      '<option value="09">Freigabe</option>' .
      '<option value="10">Betrieb</option>' .
      '<option value="11">Wartung</option>' .
      '<option value="12">Warnung</option>' .
      '<option value="13">Alarm, Gefahr, Notruf</option>' .
      '<option value="14">Not-Aus</option>' .
      '<option value="15">Störung</option>' .
      '<option value="16">Sammelstörung</option>' .
      '<option value="17">Störung allg. elektrisch</option>' .
      '<option value="18">Netzausfall</option>' .
      '<option value="19">Ausfall Hilfsenergie</option>' .
      '<option value="20">Schaltzustandsfehler</option>' .
      '<option value="21">SV-Betrieb</option>' .
      '<option value="22">Störung allg. mechanisch</option>' .
      '<option value="23">Keilriemen</option>' .
      '<option value="24">Stellantrieb</option>' .
      '<option value="25">Auslösung (Melder)</option>' .
      '<option value="26">Scharfschaltung</option>' .
      '<option value="27">Quittierung</option>' .
      '<option value="28">Hand/Auto(-Umschaltung)</option>' .
      '<option value="29">Anlagenschalter</option>' .
      '<option value="30">Reparaturschalter</option>' .
      '<option value="31">Endlage »Zu«</option>' .
      '<option value="32">Endlage »Auf«</option>' .
      '<option value="33">Regelantrieb</option>' .
      '<option value="34">Regelantrieb (Rückmeldung)</option>' .
      '<option value="35">Sollwert (berechnet)</option>' .
      '<option value="36">Sollwertanhebung, Sollwertsteller</option>' .
      '<option value="37">Hysterese</option>' .
      '<option value="38">Krümmung (Heizkurve)</option>' .
      '<option value="39">Steigung (Heizkurve)</option>' .
      '<option value="40">Sicherheitstemperaturwächter (STW)</option>' .
      '<option value="41">Druckwächter</option>' .
      '<option value="42">Durchflusswächter</option>' .
      '<option value="43">Frostschutzwächter</option>' .
      '<option value="44">Feuchte-/Kondensations-Leckagewächter</option>' .
      '<option value="45">Max-Wächter</option>' .
      '<option value="46">Sicherheitstemperaturbegrenzer (STB)</option>' .
      '<option value="47">Sicherheitsdruckbegrenzer (SDB)</option>' .
      '<option value="48">Durchflussbegrenzer</option>' .
      '<option value="49">Temperatur</option>' .
      '<option value="50">Temperatur min./unten</option>' .
      '<option value="51">Temperatur max./oben</option>' .
      '<option value="52">Temperatur Mittelwert/Mitte</option>' .
      '<option value="53">Vorlauftemperatur</option>' .
      '<option value="54">Rücklauftemperatur</option>' .
      '<option value="55">Luftqualität</option>' .
      '<option value="56">Enthalpie</option>' .
      '<option value="57">Feuchte</option>' .
      '<option value="58">Druck</option>' .
      '<option value="59">Druck max., Hochdruck</option>' .
      '<option value="60">Druck min., Niederdruck</option>' .
      '<option value="61">Differenzdruck</option>' .
      '<option value="62">Volumenstrom</option>' .
      '<option value="63">Durchfluss/Menge</option>' .
      '<option value="64">Durchfluss max./Menge max.</option>' .
      '<option value="65">Durchfluss min./Menge min.</option>' .
      '<option value="66">pH-Wert</option>' .
      '<option value="67">Drehzahl (Geschwindigkeit)</option>' .
      '<option value="68">Schlupf</option>' .
      '<option value="69">Wassermangel</option>' .
      '<option value="70">Füllstand</option>' .
      '<option value="71">Füllstand min.</option>' .
      '<option value="72">Füllstand max.</option>' .
      '<option value="73">Wind</option>' .
      '<option value="74">Windrichtung</option>' .
      '<option value="75">Niederschlag, Eis</option>' .
      '<option value="76">Helligkeit innen/außen</option>' .
      '<option value="77">Spannung</option>' .
      '<option value="78">Spannung min. (auch Unterspannung)</option>' .
      '<option value="79">Spannung max.</option>' .
      '<option value="80">Strom</option>' .
      '<option value="81">Strom min.</option>' .
      '<option value="82">Strom max.</option>' .
      '<option value="83">Leistung</option>' .
      '<option value="84">Leistung max. (1/4 h Leistung)</option>' .
      '<option value="85">Blindleistungswert cos j</option>' .
      '<option value="86">Zeit (Laufzeit, Betriebsstunden)</option>' .
      '<option value="87">elektrische Arbeit, elektrische Arbeit HT</option>' .
      '<option value="88">elektrische Arbeit NT</option>' .
      '<option value="89">Wärmemenge</option>' .
      '<option value="90">Gasmenge</option>' .
      '<option value="91">Ölmenge</option>' .
      '<option value="92">Dampfmenge</option>' .
      '<option value="93">Kaltwasser</option>' .
      '<option value="94">Warmwasser</option>' .
      '<option value="95">Fahrtenanzahl</option>' .
      '<option value="96">Haltestelle</option>' .
      '<option value="97">Feuerwehrsteuerung, Sonder-(fahrt)steuerung</option>' .
      '<option value="98">Szenenaufruf</option>' .
      '</select>' .
      '</div>';
    $out .= '</fieldset>';
    $out .= '<div class="row">' .
      '<input type="submit" id="absenden" name="absenden" value="Eintrag speichern" />' .
      '</div>';
    $out .= '</form>';
    return $out;
  }
  
  /*
   * Export aller Daten als CSV-Datei
   * Die erste Zeile enthält die Spaltentitel
   */
  protected function eintraegeExportierenCsv() {
  	$jahr = date('Y');
  	$monat = date('m');
  	$tag = date('d');
  	$dateiname = 'export_data' . '_' . $jahr. '_' . $monat. '_' . $tag . '.csv';
  	header("Content-type: text/csv");
  	header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
  	header('Content-Disposition: inline; filename="'.$dateiname.'"');
  	header('Pragma: no-cache');
  	$nl = chr(13) . chr(10);
  	/*
  	 * Überschriftenzeile erstellen
  	 */
  	$out = iconv('UTF-8','CP1252','16-Bit Code;Datenpunktschlüssel;Klartext') . $nl;
  	/*
  	 * Daten aus der Datenbank abrufen
  	 */
  	$where = 'deleted=0 AND hidden=0';
  	$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('qr_id,dp_key,klartext','tx_he_qr_info',$where,'qr_id');
  	while ($data = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
  		$ergebnisZeile = array();
  		foreach($data as $spalte) {
  			$ergebnisZeile[] = '"' . iconv('UTF-8','CP1252',str_replace('"',"'",$spalte)) . '"';
  		}
  		$out .=  implode(';', $ergebnisZeile) . $nl;
  	}
   	print $out;
  	exit();
  }
  
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_tools/lib/class.tx_he_tools_gu_qr_admin.php'])	{
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_tools/lib/class.tx_he_tools_gu_qr_admin.php.php']);
}
?>
