<?php

require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_util.php');

class tx_he_tools_gast_kennungen  {
	
	public $bereichsGruppen = array(
				''=>'------ Fakultäten ------',
				'AN'=>'Fakultät Angewandte Naturwissenschaft',
				'BW'=>'Fakultät Betriebswirtschaft',
				'FZ'=>'Fakultät Fahrzeugtechnik',
				'GL'=>'Fakultät Grundlagen',
				'GS'=>'Fakultät Graduate School',
				'GU'=>'Fakultät Gebäude Energie Umwelt',
				'IT'=>'Fakultät Informationstechnik',
				'MB'=>'Fakultät Maschinenbau',
				'ME'=>'Fakultät Mechatronik',
				'SP'=>'Fakultät Soziale Arbeit, Gesundheit und Soziales',
				'WI'=>'Fakultät Wirtschaftsingenieurwesen',
				' '=>'------ Institute/Einrichtungen ------',
				'AUSLBEZ'=>'Akademisches Auslandsamt',
				'DZ'=>'Didaktikzentrum',
				'FINA'=>'Finanzabteilung',
				'GUP'=>'Grundsatz und Planungsabteilung',
				'IAF_ES'=>'IAF - Energetische Systeme',
				'IAF_GS'=>'IAF - Institut für angewandete Forschung Gesundheit und Soziales',
				'IAF_ME'=>'IAF - Institut für Mechatronik',
				'IFS'=>'Institut für Fremdsprachen',
				'INEM'=>'Institut für nachhaltige Energietechnik und Mobilität',
				'KEIM'=>'Fraunhofer-Anwendungszentrum KEIM',
				'OEFFENTLICHKEITSARBEIT'=>'Öffentlichkeitsarbeit (RÖM)',
				'PERSABT'=>'Personalabteilung',
				'PERSONALRAT'=>'Personalrat',
				'RZ'=>'Rechenzentrum',
				'REKTORAT'=>'Rektorat',
				'STUDA'=>'Studentische  Abteilung',
				'TECHNABT'=>'Technische Abteilung',
		);

	public $disclaimer = 'Bitte beachten Sie die Benutzungsordnung des RZ der Hochschule Esslingen: http://www.hs-esslingen.de/RZ_Benutzungsordnung. Bitte verbinden Sie sich mit dem folgenden Netzwerk: Standort Flandernstrasse: Guests-HZE bzw. Standort Stadtmitte: Guests-SM';

	public function kennungenVerwalten() {
		if (!tx_he_tools_util::userEingeloggt()) {
			return $this->zeigeLoginLink();
		}
		$username = $GLOBALS['TSFE']->fe_user->user['username'];
		$get = t3lib_div::_GET();
		$post = t3lib_div::_POST();
		if (isset($get['antragsId']) && isset($get['csvExport'])) {
			$uid = $get['antragsId'];
			if ($this->zugriffErlaubt($username,$uid,'csvExport')) {
				return $this->csvExport($username,$uid);
			}
		} 
		if (isset($get['antragsId']) && isset($get['angelegt'])) {
			$uid = $get['antragsId'];
			if ($this->zugriffErlaubt($username,$uid,'angelegt')) {
        if ($get['noEmail']==1) {
          return $this->kennungenAngelegt($uid,false);
        } elseif ($get['confirm']==1) {
					return $this->kennungenAngelegt($uid);
				} else {
					return $this->zeigeFormularKennungenAngelegt($uid);
				}
			}
		}
		if (isset($get['antragsId']) && $get['loeschen']==1) {
      $antragsId = $get['antragsId'];
      if ($this->zugriffErlaubt($username,$antragsId,'loeschen')) {
				$this->antragLoeschen($antragsId,$username);
        $page = $GLOBALS['TSFE']->id;
        $pageUrl = 'https://www.hs-esslingen.de/index.php?id=' . $page;
        t3lib_utility_Http::redirect($pageUrl);
        exit();
			}
		}
    $GLOBALS['TSFE']->additionalHeaderData ['he_tools_css_1'] .= '
		<link href="' . t3lib_extMgm::siteRelPath('he_tools') . 'res/css/cisco_guests_form.css" rel="stylesheet" type="text/css" />
		<link href="/typo3/sysext/t3skin/stylesheets/sprites/t3skin.css" rel="stylesheet" type="text/css" />
		';
		if (!empty($get['antragsId'])) {
			$out = $this->antragsdatenEinzeln($username,$get['antragsId']);
		} else {
			$out = $this->antragsdatenListe($username);
		}
		return $out;
	}
	
	public function zugriffErlaubt($username,$uid,$action) {
		$erlaubt = FALSE;
		$gruppeRz = tx_he_tools_util::gibBenutzergruppe('RZ');
		$rzBenutzer = tx_he_tools_util::benutzerIstInGruppe($gruppeRz);
		switch ($action) {
			case 'csvExport':
			case 'angelegt':
			case 'loeschen':
				$erlaubt = $rzBenutzer;
			default:
				$where = 'deleted=0 AND hidden=0 AND uid=' . $uid;
				if (!$rzBenutzer) {
					$where .= ' AND username="' . $username . '"';
				}
				$query = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid','tx_hetools_antrage_gastkennungen',$where);
				if ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)) {
					$erlaubt = TRUE;
				}
		}
		return $erlaubt;
	}
	
	public function personenDatenEntschluesseln(&$antragsPersonenDaten,$key) {
		$personenDaten = array();
		$dataEncoded = unserialize($antragsPersonenDaten);
		foreach($dataEncoded as $eintrag) {
			$eintrag['passwort'] = tx_he_tools_util::decodeString($eintrag['passwort'],$key);
			$personenDaten[] = $eintrag;
		}
		return $personenDaten;
	}
	
	public function personenDatenVerschluesseln(&$antragsPersonenDaten,$key) {
		$personenDaten = array();
		foreach($antragsPersonenDaten as $eintrag) {
			$eintrag['passwort'] = tx_he_tools_util::encodeString($eintrag['passwort'],$key);
			$personenDaten[] = $eintrag;
		}
		return serialize($personenDaten);
	}
	
	public function gibAntragsdaten($where) {
		$eintrag = '';
		$query = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_hetools_antrage_gastkennungen',$where,'','tstamp');
		if ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)) {
			$eintrag = $daten;
			$eintrag['personen'] = $this->personenDatenEntschluesseln($daten['kennungen'],$daten['crdate']);
		}
		return $eintrag;
	}
	
	public function antragsdatenEinzeln($username,$uid,$buttonsAnzeigen=TRUE) {
    $GLOBALS['TSFE']->additionalHeaderData ['he_tools_css_1'] .= '
		<link href="' . t3lib_extMgm::siteRelPath('he_tools') . 'res/css/jquery.ui.core.css" rel="stylesheet" type="text/css" />
		<link href="' . t3lib_extMgm::siteRelPath('he_tools') . 'res/css/jquery.ui.base.css" rel="stylesheet" type="text/css" />
		<link href="' . t3lib_extMgm::siteRelPath('he_tools') . 'res/css/jquery.ui.theme.css" rel="stylesheet" type="text/css" />
		<link href="' . t3lib_extMgm::siteRelPath('he_tools') . 'res/css/jquery.ui.datepicker.css" rel="stylesheet" type="text/css" />
		';
		$gruppeRz = tx_he_tools_util::gibBenutzergruppe('RZ');
		$rzBenutzer = tx_he_tools_util::benutzerIstInGruppe($gruppeRz);
		$where = 'deleted=0 AND hidden=0 AND uid=' . $uid;
		if (!$rzBenutzer) {
			$where .= ' AND kennungen_angelegt=TRUE AND username="' . $username . '"';
		}
		$eintrag = $this->gibAntragsdaten($where);
		if (empty($eintrag)) {
			$out = '<h3>Sie haben keinen Zugriff auf diesen Antrag</h3>';
		} else {
			$out = $this->formatiereAntragsDaten($eintrag,TRUE);
			if ($rzBenutzer && $buttonsAnzeigen) {
				$out .= '<p>';
				$out .= $this->gibCsvExportLink($uid);
				if (!$this->kennungIstAngelegt($uid)) {
					$out .= $this->gibKennungenAngelegtLink($uid);
				}
				$out .= $this->gibListenansichtLink();
				$out .= '</p>';
			}
		}
		return $out;
	}
	
	public function zeigeFormularKennungenAngelegt($uid) {
		$page = $GLOBALS['TSFE']->id;
		$out = '<h3>Eintrag als "angelegt" markieren und E-Mail an Antragsteller versenden.</h3>';
		$username = $GLOBALS['TSFE']->fe_user->user['username'];
		$out .= $this->antragsdatenEinzeln($username,$uid,FALSE);
		$out .= '<p>
		            <a title="als angelegt markieren aber keine E-Mail senden" class="button" href="https://www.hs-esslingen.de/index.php?id=' . $page . '&antragsId=' . $uid . '&angelegt=1&noEmail=1">Angelegt markieren ohne E-Mail</a>
								<a title=Als angelegt markieren und E-Mail an Antragsteller senden"" class="button" href="https://www.hs-esslingen.de/index.php?id=' . $page . '&antragsId=' . $uid . '&angelegt=1&confirm=1">Angelegt markieren und E-Mail</a>
								<br/><br/><a class="button" href="https://www.hs-esslingen.de/index.php?id=' . $page . '">zurück zur Listenansicht</a>
						</p>';
		return $out;
	}
	
	public function kennungenAngelegt($uid,$sendEmail=true) {
		$daten['kennungen_angelegt'] = 1;
		$where = 'deleted=0 AND hidden=0 AND uid=' . $uid;
		$query = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_hetools_antrage_gastkennungen',$where,$daten);
		if ($query) {
      if ($sendEmail) {
        $this->versendeAntragstellerEmail($uid);
      }
			$page = $GLOBALS['TSFE']->id;
			$pageUrl = 'index.php?id=' . $page;
      header('HTTP/1.1 301 Moved Permanently');
      header('Location: ' .$pageUrl);
//			t3lib_utility_Http::redirect($pageUrl,);
		} else {
			return 'Fehler beim Ändern des Eintrags!';
		}
	}

  public function gastKennungLoeschen($antragsId) {
    $daten['deleted'] = 1;
    $where = 'deleted=0 AND hidden=0 AND uid=' . $antragsId;
    $query = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_hetools_antrage_gastkennungen',$where,$daten);
  }
	
	public function antragLoeschen($antragsId, $username) {
    if ($this->zugriffErlaubt($username,$antragsId,'loeschen')) {
      $this->gastKennungLoeschen($antragsId);
    }
	}
	
	public function gibCsvExportLink($uid) {
		$page = $GLOBALS['TSFE']->id;
		if ($this->csvDatenExportiert($uid)) {
			$cssClass = ' class="button disable" title="Achtung, die Daten wurden bereits exportiert!" ';
		} else {
			$cssClass = ' class="button"';
		}
		$url = 'https://www.hs-esslingen.de/index.php?id=' . $page . '&antragsId=' . $uid . '&csvExport=1';
		$out = '<a id="csv_export" ' . $cssClass . ' href="' . $url . '">CSV-Daten exportieren</a>';
		$out .= '<script type="text/javascript">
						function executeAjax(url,reload){
							var result=""
							$.ajax({
								url: url,
								async: false,
								success: function(data, request) {
									result = data; 
								}
							});
							return result;
						}
			
						$("#csv_export").click(function() {
							if (!$("#csv_export").hasClass("disable")) {
								$("#csv_export").addClass("disable");
							}
							var csvExportiert = executeAjax("index.php?eID=he_tools&action=test_gastkennungen_csv_exportiert&uid=' . $uid . '");
							if (csvExportiert=="1") {
								var antwort = confirm("Die csv-Datei wurde bereits exportiert!\nSoll sie erneut exportiert werden?");
								if(antwort) {
									return true;
								} else {
									return false;
								}
							}
						});
						</script>';		
		return $out;
	}
	
	public function gibKennungenAngelegtLink($uid) {
		$page = $GLOBALS['TSFE']->id;
		return '<a class="button" href="https://www.hs-esslingen.de/index.php?id=' . $page . '&antragsId=' . $uid . '&angelegt=1">Kennung angelegt</a>';
	}
	
	public function gibEinzelansichtLink($username,$uid,$tstamp,$kennungenAngelegt,$anzKennungen,$rzBenutzer,$mode) {
		$page = $GLOBALS['TSFE']->id;
		$datum = date('d.m.Y',$tstamp);
		if ($anzKennungen==1) {
			$msgKennungen = 'für 1 Kennung';
		} else {
			$msgKennungen = 'für ' . $anzKennungen . ' Kennungen';
		}
		if (!$kennungenAngelegt) {
			$kennungAngelegtLink = '<a class="icon-actions t3-icon-edit-add" data-antragsId="' . $uid . '" href="https://www.hs-esslingen.de/index.php?id=' .
															$page . '&antragsId=' . $uid . '&angelegt=1" title="als angelegt markieren und E-Mail versenden"></a>';
			$cssClass = ' class="' . $mode . ' row angelegt"';
		} else {
			$kennungAngelegtLink = '';
			$cssClass = ' class="' . $mode . ' row"';
		}
		if ($rzBenutzer) {
			return '<tr' . $cssClass . '>' .
					'<td><a href="https://www.hs-esslingen.de/index.php?id=' . $page . '&antragsId=' . $uid . '"  title="Eintrag bearbeiten">Antrag vom ' . $datum . ' (' . $username . ') ' . $msgKennungen . '</a></td>' . 
					'<td><a class="icon-actions t3-icon-edit-delete" data-antragsId="' . $uid . '" href="https://www.hs-esslingen.de/index.php?id=' . $page . '&antragsId=' . $uid . '&loeschen=1" title="Eintrag löschen"></a>' .
					$kennungAngelegtLink .
					'</td></tr>';
		} else {
			return '<tr>' .
					'<td><a class="button" href="https://www.hs-esslingen.de/index.php?id=' . $page . '&antragsId=' . $uid . '">Antragsdaten vom ' . $datum . ' ansehen</a></td>' .
					'</tr>';
			
		}
	}
	
	public function gibListenansichtLink($title='zur Listenansicht') {
		$page = $GLOBALS['TSFE']->id;
		return '<a class="button" href="https://www.hs-esslingen.de/index.php?id=' . $page . '">' . $title . '</a>';
	}
	
	public function antragsdatenListe($username) {
		$gruppeRz = tx_he_tools_util::gibBenutzergruppe('RZ');
		$rzBenutzer = tx_he_tools_util::benutzerIstInGruppe($gruppeRz);
		$where = 'deleted=0 AND hidden=0';
		if (!$rzBenutzer) {
			$where .= ' AND kennungen_angelegt=FALSE AND username="' . $username . '"';
		}
		$eintraege = array();
		$query = $GLOBALS['TYPO3_DB']->exec_SELECTquery('username,uid,tstamp,kennungen,kennungen_angelegt','tx_hetools_antrage_gastkennungen',$where,'','tstamp');
		while ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)) {
			$eintraege[] = $daten;
		}
		if (empty($eintraege)) {
			$out = '<h3>Es gibt keine offenen Anträge</h3>';
		} else {
			$out = '<div class="listView">';
			$mode = 'even';
			$out .= '<table>';
			foreach ($eintraege as $eintrag) {
				$anzKennungen = count(unserialize($eintrag['kennungen']));
				if ($mode=='even') {
					$mode = 'odd';
				} else {
					$mode = 'even';
				}
				$out .= $this->gibEinzelansichtLink($eintrag['username'],$eintrag['uid'],$eintrag['tstamp'],$eintrag['kennungen_angelegt'],$anzKennungen,$rzBenutzer,$mode);				
			}
			$out .= '</table>';
			$out .= '</div>';
		}
    $out .= '<script type="text/javascript">
						function executeAjax(url,reload){
							$.ajax({
								url: url,
								async: false,
								success: function(data, request) {
									if (reload) {
                    window.location.reload();
                  }
								}
							});
						}

						$(".t3-icon-edit-delete").click(function() {
							var antragsId = $(this).attr("data-antragsId");
							var url = "index.php?eID=he_tools&action=gastkennung_loeschen&antragsId=" + antragsId;
							executeAjax(url,true);
							return false;
						});
						</script>';
    return $out;
	}
	
	public function userEingeloggt() {
		if (!empty($GLOBALS['TSFE']->fe_user->user['username'])) {
			return TRUE;
		}
		return FALSE;
	}
	
	public function zeigeLoginLink() {
		$redirectUrl = base64_encode(t3lib_div::getIndpEnv('TYPO3_REQUEST_URL'));
		return '<h2>Bitte Loggen Sie sich über <a href="https://www.hs-esslingen.de/de/nc/login.html?redirectEnc=' . $redirectUrl . '">diesen Link</a> ein.</h2>';
	}
	
	public function formularAnzeigen() {
		$GLOBALS['TSFE']->additionalHeaderData ['he_tools_css_1'] = '
			<link href="' . t3lib_extMgm::siteRelPath('he_tools') . 'res/css/cisco_guests_form.css" rel="stylesheet" type="text/css" />
			<link href="/typo3/sysext/t3skin/stylesheets/sprites/t3skin.css" rel="stylesheet" type="text/css" />
			<link href="' . t3lib_extMgm::siteRelPath('he_tools') . 'res/css/jquery.ui.core.css" rel="stylesheet" type="text/css" />
			<link href="' . t3lib_extMgm::siteRelPath('he_tools') . 'res/css/jquery.ui.base.css" rel="stylesheet" type="text/css" />
			<link href="' . t3lib_extMgm::siteRelPath('he_tools') . 'res/css/jquery.ui.theme.css" rel="stylesheet" type="text/css" />
			<link href="' . t3lib_extMgm::siteRelPath('he_tools') . 'res/css/jquery.ui.datepicker.css" rel="stylesheet" type="text/css" />
		';
		$post = t3lib_div::_POST();
		$absenden = $post['absenden'];
		if (!empty($post['abbrechen'])) {
			$out = '<h2>Die Eingabe wurde abgeprochen</h2>';
		} elseif (!empty($absenden)) {
			if (empty($post['bereich']) || 
					empty($post['veranstaltung']) || 
					empty($post['personen']) || 
					empty($post['ende']) || 
					$post['zustimmung']!='on' 
					) {
				$out = $this->gibFehlermeldung($post);
				$out .= $this->gibAntragsFormular();
			} else {
				$out = $this->gibAntragsDatenAus($post);
			}
		} elseif (!empty($post['gastkennungen_beantragen'])) {
			$out = $this->gastkennungenExportieren($post);
		} else {
			$out = $this->gibAntragsFormular();
		}
		return $out;
	}
	
	function gibAntragsFormular() {
		$post = t3lib_div::_POST();
		$bereichSelected = $post['bereich'];
		$veranstaltungSelected = $post['veranstaltung'];
		$endeSelected = $post['ende'];
		if (empty($endeSelected)) {
			$endeSelected  = date('d.m.Y',time()+86400);
		}
		if ($zustimmung = $post['zustimmung']=='on') {
			$zustimmungSelected = ' checked="checked" ';
		} else {
			$zustimmungSelected = '';
		}
		$anmelden = $post['anmelden'];
		$abbrechen = $post['abbrechen'];
		
		$username = $GLOBALS['TSFE']->fe_user->user['username'];
		$vorname = $GLOBALS['TSFE']->fe_user->user['first_name'];
		$nachname = $GLOBALS['TSFE']->fe_user->user['last_name'];
		$email = $GLOBALS['TSFE']->fe_user->user['email'];
		

		
		$out = '<form id="form_gastzugaenge" action="" method="post">';
		$out .= '<div class="gastdaten">
						';
/*		
		$out .= '<div class="row">
						 <label for="vorname">Vorname:</label><input size="80" id="vorname" name="vorname" type="text" readonly="readonly" value="' . $vorname . '" />
						</div>
						<div class="row">
						 <label for="nachname">Nachname:</label><input size="80" id="nachname" name="nachname" type="text" readonly="readonly" value="' . $nachname . '" />
						</div>
						<div class="row">
						 <label for="email">E-Mail-Adresse:</label><input size="80" id="email" name="email" type="text" readonly="readonly" value="' . $email . '" />
						</div>
*/						
		$out .= '<div class="row">
						 <label for="bereich">Fakultät/Einrichtung für die Gastkennungen beantragt werden:</label>
						 <select id="bereich" name="bereich">
						 ';
			foreach ($this->bereichsGruppen as $key=>$title) {
				if ($key==$bereichSelected) {
					$selected = ' selected="selected" ';
				} else {
					$selected = '';
				}
				$out .= '<option ' . $selected . ' value="' . $key . '">' . $title . '</option>';
			}
      $maxDays = 90;
      $maxTstamp = (time() + (3600*24*$maxDays))*1000;
      $endDateValidationScript = 'function(wert) {
								$(".error_ende").detach();

								if (wert.match(/^\d\d?\.\d\d?\.\d\d\d\d$/)) {
								var dArr = wert.split(".");
                var ts = new Date(dArr[2], (dArr[1]-1), dArr[0], 0, 0, 0).getTime();
                  if (ts>' . $maxTstamp . ') {
                    $("#ende").parent().before("<label class=\'error error_ende\'>Sie können Gastkennungen maximal für ' . $maxDays . ' Tage beantragen!</label>");
                    $("#ende").val("' . $endeSelected . '");
                    return false;
                  } else {
                    return true;
                  }
								} else {
									$("#ende").parent().before("<label class=\'error error_ende\'>Bitte wählen Sie ein Datum aus</label>");
									$("#ende").val("' . $endeSelected . '");
									return false;
								}
							}';
			$out .= '</select>
						</div>
						<div class="row">
						 <label for="veranstaltung">Titel der Veranstaltung/Projekt etc. (maximal 32 Buchstaben):</label>
						 <input maxlength="32" size="32" id="veranstaltung" name="veranstaltung" type="text" value="' . $veranstaltungSelected . '" />
						</div>
						<div class="row">
						 <label for="anzahl">Personenangaben zu den benötigten Gastkennungen:</label>
						 ' . $this->gibPersonenTabelle($post) . '
						</div>
						<div class="row">
						 <label for="anzahl">Ablaufdatum der Kennung(en), Konto aktiv bis einschliesslich:</label>
						 <input id="ende" name="ende" type="input"  class="enddatum"  value="' . $endeSelected . '"/>
					 	<script type="text/javascript">
							$("#ende").datepicker({ 
							  "dateFormat": "dd.mm.yy",
								"class": "picker_ende",
								"changeMonth": 1,
								"changeYear": 1,
								"showButtonPanel": 1,
								"minDate": "0",
								"maxDate": "+' . $maxDays . 'd",
								"onSelect": ' . $endDateValidationScript . ',
								"onClose": ' . $endDateValidationScript . ',
								"prevText": "&#x3c;zurück",
                "prevJumpText": "&#x3c;&#x3c;",
                "prevJumpStatus": "",
                "nextText": "Vor&#x3e;",
                "nextStatus": "",
                "nextJumpText": "&#x3e;&#x3e;",
                "nextJumpStatus": "",
                "currentText": "heute",
                "currentStatus": "",
                "todayText": "heute",
                "todayStatus": "",
                "clearText": "-",
                "clearStatus": "",
                "closeText": "schließen",
                "closeStatus": "",
								"monthNames": ["Januar","Februar","März","April","Mai","Juni",
                "Juli","August","September","Oktober","November","Dezember"],
                "monthNamesShort": ["Jan","Feb","Mär","Apr","Mai","Jun",
                "Jul","Aug","Sep","Okt","Nov","Dez"],
                "dayNames": ["Sonntag","Montag","Dienstag","Mittwoch","Donnerstag","Freitag","Samstag"],
                "dayNamesShort": ["So","Mo","Di","Mi","Do","Fr","Sa"],
                "dayNamesMin": ["So","Mo","Di","Mi","Do","Fr","Sa"]
						 });
						 </script>
						</div>
						<div class="row">
						 <label for="zustimmung">Ich bestätige hiermit, dass ich die Nutzungsrechte und -beschränkungen für Gastkennungen gelesen habe und diesen zustimme.</label>
						 <input id="zustimmung" name="zustimmung" type="checkbox"  ' . $zustimmungSelected . '/>
						</div>
						</div>
		';
					 
		$out .= '<input id="anmelden" type="submit" name="absenden" value="Absenden" />';
		$out .= '<input id="abbrechen" type="submit" name="abbrechen" value="Abbrechen" />';
		$out .= '</form>';
		$out .= '<script type="text/javascript">
							function validateBereich() {
								var wert;
								wert = $("#bereich").val();
								$(".error_bereich").detach();
								if (wert=="" || wert==" ") {
									$("#bereich").parent().before("<label class=\'error error_bereich\'>Bitte wählen Sie eine Einrichtung aus.</label>");
									return false;
								} else {
									return true;
								}
							}
							function validateVeranstaltung() {
								var wert;
								wert = $("#veranstaltung").val();
								$(".error_veranstaltung").detach();
								if (wert=="") {
									$("#veranstaltung").parent().before("<label class=\'error error_veranstaltung\'>Bitte tragen Sie einen Veranstaltungstitel ein</label>");
									return false;
								} else {
									return true;
								}
							}
							function validateDatum() {
								var wert;
								wert = $("#ende").val();
								$(".error_ende").detach();
								if (wert.match(/^\d\d?\.\d\d?\.\d\d\d\d$/)) {
									var tag = parseInt(wert.substr(0,2));
									var monat = parseInt(wert.substr(3,2))-1;
									var jahr = parseInt(wert.substr(6,4));
									var d = new Date(jahr,monat,tag,0,0,0);
									var timestamp = d.getTime()/1000;
									if (timestamp<' . time() . ') {
										$("#ende").parent().before("<label class=\"error error_ende\">Das gewählte Datum liegt in der Vergangenheit</label>");
										return false;
									} else {
										return true;
									}
								} else {
									$("#ende").parent().before("<label class=\"error error_ende\">Bitte wählen Sie ein Datum aus</label>");
									return false;
								}
							}
							function validatePersonen() {
							var table = $("#dyn_table_personen");
							var rows = $("tr:gt(0)",table);
							var meldungAlleFelder = "Bitte geben Sie mindestens die Felder \'Vorname\' und \'Nachname\' ein!";
							var meldungMindestensEinEintrag = "Bitte geben Sie mindestens eine Person ein!";
							var valid = true;
							var filledRows = 0;
								rows.each(function(){
									if (valid) {
								  	var requiredCols = ["vorname", "nachname" ];
								  	var checkCols = ["vorname", "nachname" ];
								  	var vorname = "";
								  	var nachname = "";
								  	var cols = $("input",this);
								  	var colName;
								  	var rowFilled = false;
								  	cols.each(function(){
								  		var wert = "";
								  		if ($(this).attr("type")!="hidden") {
								  			if ($(this).attr("type")=="checkbox") {
								  				if ($(this).is(":checked")) {
								  					wert = "on";
								  				}
								  			} else {	 
								  				wert = $(this).val();
								  			}
								  			if (wert!="") {
								  				rowFilled = true;
								  			}
								  		}
								  	});
							  		if (rowFilled) {
							  			filledRows++;
							  			for (var i=0;valid && i<requiredCols.length; i++) {
							  				var wert = $(this).find("." + requiredCols[i]).val();
							  				if (wert=="") {
							  					valid = false;
												}
							  			}
							  		}
									}
								});
								$(".error_personen").detach();
				 				if (filledRows==0) {
									$("#dyn_table_personen").parent().before("<label class=\'error error_personen\'>" + meldungMindestensEinEintrag + "</label>");
									return false;
								} else if (!valid) {
									$("#dyn_table_personen").parent().before("<label class=\'error error_personen\'>" + meldungAlleFelder + "</label>");
									return false;
								} else {
									return true;
								}
							}
							function validateZustimmung() {
								$(".error_zustimmung").detach();
				 				if (!($("#zustimmung").is(":checked"))) {
									$("#zustimmung").parent().before("<label class=\'error error_zustimmung\'>Sie müssen den Nutzungsbestimmungen zustimmen</label>");
									return false;
								} else {
									return true;
								}
							}
							$("#bereich").change(function() { 
								validateBereich();
							});
							$("#veranstaltung").change(function() { 
								validateVeranstaltung();
							});
							$("#personen").change(function() { 
								validatePersonen();
							});
							$("#ende").change(function() { 
								validateDatum();
							});
							$("#zustimmung").change(function() { 
								validateZustimmung();
							});
							$("#form_gastzugaenge :submit").click(function() {
								$(this).closest("form").data("submitbutton", $(this).attr("name"));
							});
							$("#form_gastzugaenge").submit(function() {
								var submitButton = $(this).data("submitbutton");
								if (submitButton=="abbrechen") {
									return true;
							  } 
								var valid = true;
								var test;
								test = validateBereich();
								if (!test) {
									valid = false;
								}
								test = validateVeranstaltung();
								if (!test) {
									valid = false;
								}
								test = validatePersonen();
								if (!test) {
									valid = false;
								}
								test = validateDatum();
								if (!test) {
									valid = false;
								}
								test = validateZustimmung();
								if (!test) {
									valid = false;
								}
								return valid;
						 });
						 </script>				
						 ';
		return $out;
	}
	
	function gibFehlermeldung($post) {
		$out = '<div class="fehlermeldungen">';
		if (empty($post['bereich'])) {
			$out .= '<label class="error">Bitte wählen Sie eine Einrichtung aus.</label><br />';
		}
		if (empty($post['veranstaltung'])) {
			$out .= '<label class="error">Bitte tragen Sie einen Veranstaltungstitel ein.</label><br />';
		}
		if (empty($post['personen'])) {
			$out .= '<label class="error">Bitte tragen Sie mindestens Vornamen und Nachnamen einer Person ein.</label><br />';
		}
		if (empty($post['ende'])) {
			$out .= '<label class="error">Bitte wählen Sie ein Datum aus.</label><br />';
		}
		if ($post['zustimmung']!='on') {
			$out .= '<label class="error">Sie müssen den Nutzungsbestimmungen zustimmen.</label><br />';
		}
		$out .= '</div>';
		return $out;
	}
			
	function gibPersonenTabelle($post)	{
		$numRows = 5;
		$colTitles = array('vorname'=>'Vorname','nachname'=>'Nachname');
		$colTypes = array('vorname'=>'input','nachname'=>'input');
		$personenValue = $post['personen'];
		if (empty($personenValue)) {
			$personenValue = array();
			for ($i=0;$i<$numRows;$i++) {
				$row = array();
				foreach ($colTitles as $key=>$val) {
					$row[$key] = '';
				}
				$personenValue[] = $row;
			}
		}
		$out = '
		<table id="dyn_table_personen" class="dyn_table"><thead>
		<tr>' . "\n";
		foreach($colTitles as $key=>$title) {
			$out .= '<th>' . $title . '</th>' . "\n";
		}
		$out .= '<th class="actions"></th>';
			$out .= '</tr></thead>
		<tbody>' . "\n";
		$row = 0;
		$valuesNew = array();
		foreach($personenValue as $rowValues) {
			$rowValueNew = array();
			foreach ($colTypes as $key=>$colType) {
				if (!isset($rowValues[$key]) && $colType=='label') {
					$rowValueNew[$key] = '';
				} else {
					$rowValueNew[$key] = $rowValues[$key];
				}
			}
			$valuesNew[] = $rowValueNew;
		}
		foreach($valuesNew as $rows) {
			$out .= '<tr id="row_' . $row . '">' . "\n";
			foreach($rows as $key=>$val) {
				$out .= '<td><input class="' . $key . '" size="40" type="text" ' .
											'name="personen[' . $row . '][' . $key . ']" ' .
											'value="' . $val . '" /></td>' . "\n";
			}
			$out .= '<td class="actions">';
			$out .= '<span class="icon-actions t3-icon-edit-delete delete_row_personen" title="Zeile Löschen" row="' . $row . '"></span>';
			if ($row==(count($personenValue)-1)) {
				$out .= '<span class="icon-actions t3-icon-move-down move_down_personen hidden" title="Zeile nach unten verschieben" row="' . $row . '"></span>';
			} else {
				$out .= '<span class="icon-actions t3-icon-move-down move_down_personen" title="Zeile nach unten verschieben" row="' . $row . '"></span>';
			}
			if ($row==0) {
				$out .= '<span class="icon-actions t3-icon-move-up move_up_personen hidden" title="Zeile nach oben verschieben" row="' . $row . '"></span>';
			} else {
				$out .= '<span class="icon-actions t3-icon-move-up move_up_personen" title="Zeile nach oben verschieben" row="' . $row . '"></span>';
			}
	
			$out .= '</td></tr>' . "\n";
			$row++;
		}
		$emptyRow = '';
		foreach($colTitles as $key=>$title) {
			$emptyRow .= '<td><input type="text" ' .
					' class="' . $key . '"' . 
					' name="personen[###numRows###][' . $key . ']" /></td>';
		}
		$out .= '</tbody></table>';
		$out .= '<div>';
		$out .= '<input id="addElem_personen" type="button" value="Weitere Zeile hinzufügen">';
		$out .= '</div>';
		$colNames = implode(',',array_keys($colTitles));
		$out .= '	<input type="hidden" required="required" id="personen" value="empty" />
					<script type="text/javascript">
				function renumberTable_personen(){
					var table = $("#dyn_table_personen");
					var rows = $("tr:gt(0)",table);
					rows.each(function(indexRows){
						$(this).attr("id","row_" + indexRows);
						var cols = $("input",this);
						cols.each(function(){
							var colName = $(this).attr("class");
							$(this).attr("name","personen[" + indexRows + "][" + colName + "]");
						});
						var label = $("label",this);
						label.each(function(){
							var title = $(this).attr("data-title");
							$(this).html(title + " " + (indexRows+1));
						});
						var actions = $(".t3-icon-move-down",this);
						actions.each(function(){
							$(this).removeClass("hidden");
							$(this).attr("row",indexRows);
							if (indexRows==(rows.length-1)) {
								$(this).addClass("hidden");
							}
						});
						var actions = $(".t3-icon-move-up",this);
						actions.each(function(){
							$(this).removeClass("hidden");
							$(this).attr("row",indexRows);
							if (indexRows==0) {
								$(this).addClass("hidden");
							}
						});
					});
				}
				function addTableRow_personen(table,cols){
					var colNames = cols.split(",");
					var numRows = $("tr", table).length-1;
					var n = colNames.length;
					var tds = "<tr id=\"row_" + numRows + "\">";
					var newRow = \'' . $emptyRow . '\';
					tds += newRow.replace(/###numRows###/g,numRows);
					tds += "<td class=\"action\">";
			';
		$out .= 'tds += "<span class=\"icon-actions t3-icon-edit-delete delete_row_personen\" title=\"Zeile Löschen\" row=\"" + numRows + "\"></span>";
			';
		$out .= 'tds += "<span class=\"icon-actions t3-icon-move-down move_down_personen\" title=\"Zeile nach oben verschieben\" row=\"" + numRows + "\"></span>";
					tds += "<span class=\"icon-actions t3-icon-move-up move_up_personen\" title=\"Zeile nach oben verschieben\" row=\"" + numRows + "\"></span>";
					tds += "</td>";
					tds += "</tr>";
					if($("tbody", table).length > 0){
						$("tbody", table).append(tds);
					}else {
						$(table).append(tds);
					}
				}
				
				$("#addElem_personen" ).click(function(){
					addTableRow_personen($("#dyn_table_personen"),"' . $colNames . '");
					renumberTable_personen();
				});
				$("table#dyn_table_personen").delegate(".delete_row_personen","click", function(){
					var row = $(this).attr("row");
					$("table#dyn_table_personen #row_" + row).remove();
					renumberTable_personen();
				});
				$("table#dyn_table_personen").delegate(".move_down_personen","click", function(){
					var rows = $("tr:gt(0)",$("#dyn_table_personen"));
					var row = parseInt($(this).attr("row"));
					if (row<rows.length-1) {
						var first = "table#dyn_table_personen tr#row_" + (row+1);
						var second = "table#dyn_table_personen tr#row_" + row;
						$(first).after($(second));
						renumberTable_personen();
					}
				});
				$("table#dyn_table_personen").delegate(".move_up_personen","click", function(){
					var row = parseInt($(this).attr("row"));
					if (row>0) {
						var first = "table#dyn_table_personen tr#row_" + row;
						var second = "table#dyn_table_personen tr#row_" + (row-1);
						$(first).after($(second));
						renumberTable_personen();
					}
				});
		</script>
	';
		return $out;
	}
	
	function formatiereAntragsDaten(&$daten,$showUserPasswd=FALSE) {
		$out = '<hr />
		<h3>Daten zu den beantragten Gastkennungen</h3>
		<h4>Fakultät/Einrichtung für die Gastkennungen beantragt werden:</h4><p class="value">' . $this->bereichsGruppen[$daten['bereich']] . '</p>
		<h4>Titel der Veranstaltung/Projekt etc.:</h4><p class="value">' . $daten['veranstaltung'] . '</p>
		<h4>Gastkennungen für folgende Personen:</h4><p class="value">
		';
		$out .= '<table><thead><tr>';
		$out .= '<th>Vorname</th>';
		$out .= '<th>Nachname</th>';
		if ($showUserPasswd) {
			$out .= '<th>Benutzername</th>';
			$out .= '<th>Passwort</th>';
		}
		$out .= '</tr></thead><tbody>';
		foreach ($daten['personen'] as $person) {
			if (!empty($person['vorname']) &&!empty($person['nachname'])) {
				$out .= '<tr>';
				$out .= '<td>' . $person['vorname'] . '</td>';
				$out .= '<td>' . $person['nachname'] . '</td>';
				if ($showUserPasswd) {
					$out .= '<td>' . $person['account'] . '</td>';
					$out .= '<td>' . $person['passwort'] . '</td>';
				}
				$out .= '<tr>';
			}
		}
		$out .= '</tbody></table>';
		$out .= '</p>';
		$out .= '<h4>Gültigkeitsende der Gastkennungen:</h4><p class="value">' . $daten['ende'] . '</p>
		';
		return $out;		
	}
	
	function formatiereAntragsDatenPlain(&$daten,$showUserPasswd=FALSE) {
		$out = '----------------------------------------------------------------------------------

Daten zu den beantragten Gastkennungen

Fakultät/Einrichtung für die Gastkennungen beantragt werden:
' . $this->bereichsGruppen[$daten['bereich']] . '

Titel der Veranstaltung/Projekt etc.:
' . $daten['veranstaltung'] . '

Gastkennungen für folgende Personen:
';
		foreach ($daten['personen'] as $person) {
			if (!empty($person['vorname']) &&!empty($person['nachname'])) {
				$out .= '  - ' .$person['vorname'] . ' ' . $person['nachname'] . "\n";
			}
		}
		$out .= '

Gültigkeitsende der Gastkennungen:
' . $daten['ende'] . '
';
		return $out;		
	}
	
	function gibAntragsDatenAus(&$post) {
		$out = '<form id="form_gastzugaenge" action="" method="post">';
		$out .= '<div class="gastdaten">
		<h2>Angaben überpfüfen</h2>
		<h3>Sie haben folgende Angaben gemacht:</h3>
		';
/*		
		<hr />
		<h4>Daten des Antragstellers</h4>
		<div class="row"><label>Vorname:</label><div class="value">' . $post['vorname'] . '</div></div>
		<div class="row"><label>Nachname:</label><div class="value">' . $post['nachname'] . '</div></div>
		<div class="row"><label>E-Mail-Adresse:</label><div class="value">' . $post['email'] . '</div></div>
*/		

		$out .= $this->formatiereAntragsDaten($post);
		$out .= '<input type="hidden" name="bereich" value="' . $post['bereich'] . '" />
		<input type="hidden" name="ende" value="' . $post['ende'] . '" />
		<input type="hidden" name="veranstaltung" value="' . $post['veranstaltung'] . '" />
		<input type="hidden" name="zustimmung" value="' . $post['zustimmung'] . '" />
		';
		$index = 0;
		foreach ($post['personen'] as $person) {
			if (!empty($person['vorname']) &&!empty($person['nachname'])) {
				$out .= '<input type="hidden" name="personen[' . $index . '][vorname]" value="' . $person['vorname'] . '" />
							';
				$out .= '<input type="hidden" name="personen[' . $index . '][nachname]" value="' . $person['nachname'] . '" />
							';
				$index++;
			}
		}
		$out .= '<input id="anmelden" type="submit" name="gastkennungen_beantragen" value="Gastkennungen verbindlich beantragen" />
			<input id="abbrechen" type="submit" name="zurueck" value="Zurück" />
			</div>
			</form>
			';
		return $out;
	}
	
	function random_pwd($length){
//    $specialChars = array('!','@','#','$','%','&','*','(',')','_','-','+','=','[',']','<','>','?','/');
    $chars = array_merge(range('a','z'), range('A','Z'), range(0,9));
    // Einzelne Buchstaben entfernen
    unset($chars[array_search('i',$chars)]);
    unset($chars[array_search('l',$chars)]);
    unset($chars[array_search('o',$chars)]);
    unset($chars[array_search('I',$chars)]);
    unset($chars[array_search('O',$chars)]);
    unset($chars[array_search('Q',$chars)]);
    unset($chars[array_search('0',$chars)]);
    unset($chars[array_search('1',$chars)]);
    $chars = array_values($chars);
    $maxChars = count($chars);
    $pwd = array();
    for ($i=0;$i<=$length;$i++) {
    	$pwd[] = $chars[rand(0,$maxChars)];
    }
    return implode('',$pwd);
  }	
  
  function generiereGastkennung($vorname,$nachname,$bereich,$veranstaltung,$dauer,$index) {
		$heute = date('my',time());
		$beginn  = date('d.m.Y',time());
		$ende  = date('d.m.Y',time()+$dauer);
		if ($index<10) {
			$index = '0' . $index;
		}
		$vornameKuerzel = strtolower($this->wandleUtfUmlaute(mb_substr($vorname, 0, 2, 'UTF-8')));
		$nachnameKuerzel = strtolower($this->wandleUtfUmlaute(mb_substr($nachname, 0, 2, 'UTF-8')));
		$account = $vornameKuerzel . $nachnameKuerzel . '_' . strtolower($bereich) . '_' . $heute . '_' . $index;
		
		$passwort = $this->random_pwd(10);
		$usernameAntragsteller = $GLOBALS['TSFE']->fe_user->user['username'];
		$vornameAntragsteller = $GLOBALS['TSFE']->fe_user->user['first_name'];
		$nachnameAntragsteller = $GLOBALS['TSFE']->fe_user->user['last_name'];
		$emailAntragsteller = $GLOBALS['TSFE']->fe_user->user['email'];
/*		$disclaimer = 'Die Gäste erklaeren ihr Einverständnis mit den Nutzungsbestimmungen des Rechenzentrums der Hochschule Esslingen für Gast-Zugänge.';
	  $disclaimer = $veranstaltung . ' - ' . $this->bereichsGruppen[$bereich] . ' - von ' . $beginn . ' bis ' . $ende .
		  '. Beantragt von: ' . $vornameAntragsteller . ' ' . $nachnameAntragsteller . ' (' . $usernameAntragsteller . ') - E-Mail: ' . $emailAntragsteller;
*/
		$description = $veranstaltung;
		return array('account'=>$account, 'passwort'=>$passwort, 'ende'=>$dauer, 'description'=>$description,'disclaimer'=>$this->disclaimer,'vorname'=>$vorname,'nachname'=>$nachname);
	}
	
  function wandleUtfUmlaute($wort) {
		$normalizeChars = array(
		    'Š'=>'S', 'š'=>'s', 'Ð'=>'D','Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A',
		    'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
		    'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
		    'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
		    'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
		    'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
		    'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f', 'ß'=>'s', 'ğ'=>'g', 
		);			  	
  	$str = strtr($wort, $normalizeChars);
  	return $str;
  }
		  
 	function csvExport($username,$uid) {
 		$gruppeRz = tx_he_tools_util::gibBenutzergruppe('RZ');
		$rzBenutzer = tx_he_tools_util::benutzerIstInGruppe($gruppeRz);
 		$where = 'deleted=0 AND hidden=0 AND uid=' . $uid;
 		if (!$rzBenutzer) {
 			$where .= ' AND kennungen_angelegt=TRUE AND username="' . $username . '"';
 		}
 		$eintrag = $this->gibAntragsdaten($where);
 		if (empty($eintrag)) {
 			$out = '<h3>Sie haben keinen Zugriff auf diesen Antrag</h3>';
 		} else {
 			$daten['csv_exportiert'] = 1;
 			$where = 'deleted=0 AND hidden=0 AND uid=' . $uid;
 			$query = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_hetools_antrage_gastkennungen',$where,$daten);
 			$out = $this->erstelleCsvDatei($eintrag['personen']);
 		}
 		return $out;
 	}
 	
	function erstelleCsvDatei($eintragsDaten) {
		$zeit  = date('Ymd_Hi',time());
		$dateiname = 'gastkennungen_' . $zeit . '.csv';
		$nl = chr(13) . chr(10);
		$out = '';
		foreach ($eintragsDaten as $eintrag) {
			$daten = array($eintrag['account'],$eintrag['passwort'],$eintrag['ende'],$eintrag['description'],$eintrag['disclaimer']);
      $out .= implode(',', $daten) . $nl;
		}
    
		header("Content-type: text/csv");
		header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Content-Disposition: inline; filename="' . $dateiname . '"');
		header('Pragma: no-cache');
		print $out;
		exit();
  }
		  
	function gastkennungenExportieren(&$post) {
		$username = $GLOBALS['TSFE']->fe_user->user['username'];
		$endDatum = DateTime::createFromFormat('d.m.Y H:i:s', $post['ende'] . ' 00:00:00');
		$endStamp = $endDatum->getTimestamp();
		$dauer = $endStamp-time()+86400;
		$bereich = $post['bereich'];
		$personen = $post['personen'];
		$veranstaltung = $post['veranstaltung'];
		/* Einträge generieren */
		$eintragsDaten = array();
		$index = 1;
		foreach ($personen as $person) {
			if (!empty($person['vorname']) && !empty($person['nachname'])) {
				$eintragsDaten[] = $this->generiereGastkennung($person['vorname'],$person['nachname'],$bereich,$veranstaltung,$dauer,$index);
				$index++;
			}
		}
		
		$datenGastKennung['tstamp'] = time();
		$datenGastKennung['crdate'] = time();
		$datenGastKennung['pid'] = 131974;
		$datenGastKennung['username'] = $username;
		$datenGastKennung['bereich'] = $bereich;
		$datenGastKennung['veranstaltung'] = $veranstaltung;
		$datenGastKennung['ende'] = $post['ende'];
		$datenGastKennung['kennungen'] = $this->personenDatenVerschluesseln($eintragsDaten,$datenGastKennung['crdate']);
		
		$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_hetools_antrage_gastkennungen',$datenGastKennung);
		$antragsId = $GLOBALS['TYPO3_DB']->sql_insert_id();
		
		$antragsDatenHtml = $this->formatiereAntragsDaten($post);
		$antragsDatenPlain = $this->formatiereAntragsDatenPlain($post);
		
		$name = $GLOBALS['TSFE']->fe_user->user['name'];
		$out = '<h3>Sehr geehrte/r ' . $name . ',</h3>
		<p>Ihr Antrag auf Erstellung von Gastkennungen wurde soeben gespeichert.</p>
		<p>Sie erhalten in Kürze per E-Mail eine Bestätigung.</p>
		<p><br />Mit freundlichen Gruessen<br />
		Ihr RZ Team</p>';
		
		$fromUser = array('rz-guest-account@hs-esslingen.de'=>'Gastzugänge');
		$toUser = array($username . '@hs-esslingen.de'=>$name);
		$subjectUser = 'Antrag auf Erstellung von Gastkennungen';
		$bodyHtmlUser = '<p>Sehr geehrte/r ' . $name . ',<br/>
		Ihr Antrag auf Erstellung von Gastkennungen wurde soeben gespeichert</p>
		' . $antragsDatenHtml . '
		<p>Sie erhalten eine weitere E-Mail, sobald die Kennungen angelegt wurden.
		</p>
		<p>Mit freundlichen Gruessen<br />
		Ihr RZ Team</p>
		';
		
		$heute  = date('d.m.Y',time()+$dauer);
		
		$this->sendEmail($fromUser,$toUser,$subjectUser,$bodyHtmlUser);
		$fromAdmin = array($username . '@hs-esslingen.de'=>$name);
//		$toAdmin = array('rz+gastzugaenge@hs-esslingen.de'=>'KMS Team');
//		$toAdmin = array('mmirsch@hs-esslingen.de'=>'Gastzugänge');
		
		$toAdmin = array('rz-guest-account@hs-esslingen.de'=>'Gastzugänge');
		$subjectUser = 'Neuer Antrag auf Erstellung von Gastkennungen';
		$csvBearbeitungsLink = '<p><a href="https://www.hs-esslingen.de/index.php?id=131973&antragsId=' . $antragsId . '">Antrag bearbeiten</p>';
		$ciscoBearbeitungsLink = '<p>Zur <a href="https://wlan-mgmt.hs-esslingen.de">Konfigurationswebseite</p>';
		$antragstellerEmailLink = '<p><a href=https://www.hs-esslingen.de/index.php?id=131973&antragsId=' . $antragsId . '&angelegt=1">E-Mail an Antragsteller senden</p>';
		
		$bodyHtmlAdmin = '
		<h4>
		Ein Antrag auf Erstellung von Gastkennungen wurde soeben von ' . $name . ' eingereicht,</h4>
		' . $antragsDatenHtml . $csvBearbeitungsLink . $ciscoBearbeitungsLink . $antragstellerEmailLink;
		$bodyPlainAdmin = '
Ein Antrag auf Erstellung von Gastkennungen wurde soeben von ' . $name . ' eingereicht.

' . $antragsDatenPlain . 
'
Antrag bearbeiten: 
https://www.hs-esslingen.de/index.php?id=131973&antragsId=' . $antragsId . '

Zur Konfigurationswebseite:
https://wlan-mgmt.hs-esslingen.de

E-Mail an Antragsteller senden: 
https://www.hs-esslingen.de/index.php?id=131973&antragsId=' . $antragsId . '&angelegt=1

';
		$this->sendEmail($fromAdmin,$toAdmin,$subjectUser,$bodyHtmlAdmin,$bodyPlainAdmin);
		return $out;

	}
	
	public function sendEmail($from,$to,$subject,$bodyHtml,$bodyPlain='') {
		$mail = t3lib_div::makeInstance('t3lib_mail_Message');
		if (empty($bodyPlain)) {
			$bodyPlain = preg_replace('/(<br>|<br \/>|<br\/>)\s*/i', PHP_EOL, $bodyHtml);
			$bodyPlain = strip_tags($bodyPlain);
		}
		$mail->setFrom($from);
		$mail->setTo($to);
		$mail->setSubject($subject);
		$htmlComplete = $this->initHtml() .
										$bodyHtml .
										$this->exitHtml();
		$mail->setBody($htmlComplete, 'text/html');
		$mail->addPart($bodyPlain, 'text/plain');
		if (!empty($anhang)) {
			$mail->attach(Swift_Attachment::fromPath($anhang));
		}
		$erg = $mail->send();
		if (!$erg) {
			$failedRecipients = $this->mail->getFailedRecipients();
			t3lib_div::devlog('E-Mail-Versand fehlgeschlagen!','cisco_guest_formular',0,$failedRecipients);
		}
		return $erg;
	}
	
	function gibAntragsUsername($uid) {
		$where = 'uid=' . $uid;
		$query = $GLOBALS['TYPO3_DB']->exec_SELECTquery('username','tx_hetools_antrage_gastkennungen',$where);
		if ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)) {
			$username  = $daten['username'];
		}
		return $username;
	}
	
	function gibAntragsDatum($uid) {
		$where = 'uid=' . $uid;
		$query = $GLOBALS['TYPO3_DB']->exec_SELECTquery('crdate','tx_hetools_antrage_gastkennungen',$where);
		if ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)) {
			$datum  = date('d.m.Y',$daten['crdate']);
		}
		return $datum;
	}
	
	function csvDatenExportiert($uid) {
		$where = 'uid=' . $uid;
		$query = $GLOBALS['TYPO3_DB']->exec_SELECTquery('csv_exportiert','tx_hetools_antrage_gastkennungen',$where);
		if ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)) {
			return $daten['csv_exportiert'];
		}
		return false;
	}
	
	function kennungIstAngelegt($uid) {
		$where = 'uid=' . $uid;
		$query = $GLOBALS['TYPO3_DB']->exec_SELECTquery('kennungen_angelegt','tx_hetools_antrage_gastkennungen',$where);
		if ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)) {
			return $daten['kennungen_angelegt'];
		}
		return false;
	}
	
	function versendeAntragstellerEmail($uid) {
		$username = $this->gibAntragsUsername($uid);
		$datum = $this->gibAntragsDatum($uid);
		$where = 'username="' . $username . '"';
		$query = $GLOBALS['TYPO3_DB']->exec_SELECTquery('name','fe_users',$where);
		if ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)) {
			$name = $daten['name'];
		} else {
			$name = $username;
		}
		$fromUser = array('rz+gastzugaenge@hs-esslingen.de'=>'Gastzugänge');
		$toUser = array($username . '@hs-esslingen.de'=>$name);
		$subjectUser = 'Gastkennungen eingerichtet';
		$bodyHtmlUser = '<p>Sehr geehrte/r ' . $name . ',
		<p>Die von Ihnen am ' . $datum . ' beantragten Gastkennungen wurde soeben eingerichtet.</p>
		<p>Die angelegten Gastkennungen können Sie (nach Login) unter dem folgenden Link sehen:<br />
		<a href="https://www.hs-esslingen.de/index.php?id=131973&antragsId=' . $uid . '"">Gastkennungen</a>
		</p>
		<p>Mit freundlichen Gruessen<br />
		Ihr RZ Team</p>
		';
		$this->sendEmail($fromUser,$toUser,$subjectUser,$bodyHtmlUser);
	}
	
	function initHtml() {
		return '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Antrag auf Erstellung von Gastkennungen</title>
		</head>
		<body >
		</div>
	
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
}
?>
