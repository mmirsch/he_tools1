<?php
require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(PATH_t3lib.'class.t3lib_parsehtml_proc.php');

require_once(t3lib_extMgm::extPath('he_tools') . 'lib/class.tx_he_tools_util.php');
require_once(t3lib_extMgm::extPath('he_portal') . 'lib/class.tx_he_portal_lib_util.php');
require_once(t3lib_extMgm::extPath('he_portal') . 'lib/class.tx_he_portal_lib_gadgets.php');
require_once(t3lib_extMgm::extPath('he_tools') . 'lib/class.tx_he_tools_lsf.php');
require_once(t3lib_extMgm::extPath('he_personen') . 'lib/class.tx_he_personen_util.php');

class tx_he_tools_lib_db_suche {
	
	public function initGadget($cObj) {
		$username = $GLOBALS['TSFE']->fe_user->user['username'];
		$gadgetId = tx_he_portal_lib_gadgets::gibGadgetId($GLOBALS['TSFE']->id);
		$out = tx_he_portal_lib_gadgets::renderGadgetHilfeText($gadgetId,$cObj,TRUE,$username);
		if (!empty($out)) {
			$out .= '<div class="hinweis">Anleitungstext ausblenden durch Anklicken des Icons 
							<img title="Einstellungen bearbeiten" style="vertical-align: -20%;" src="typo3conf/ext/he_portal/res/jquery/css/edit.gif" />
							in der Titelleiste dieses Gadgets.</div>';
		}
		return $out;
	}
	
	public function hochschuleABisZSucheGadget($parent) {
		$GLOBALS['TSFE']->additionalHeaderData ['he_tools_css_1'] = '<link href="' . t3lib_extMgm::siteRelPath('he_tools') . 'res/hochschule_a_z.css" rel="stylesheet" type="text/css" />';
		$out = $this->initGadget($parent->cObj);
		$out .= '
	<div class="a_bis_z_suche">
		<div style="margin: 10px 0;">
		Bitte geben Sie einen Suchbegriff ein.</div>
		<form action="" method="post" onSubmit="return false;">
			<input class="such_box" type="text" id="eingabe" name="eingabe" size="40" />
			<input id="absenden" type="submit" value="Absenden" />
		</form>
		<div id="namenListe">
		</div>
	</div>
		
		<script>
		$("#absenden").remove();
		$("#eingabe").bindWithDelay("keyup",function(event) {
			var eingabe = encodeURI($("#eingabe").val());
			if (eingabe.length>0) {
				$("#ergebnisliste").detach();
				$("<div id=\"ergebnisliste\"></div>").appendTo($("#namenListe"));
				$("#ergebnisliste").load("index.php?eID=he_tools&action=hochschule_a_bis_z_suche&eingabe=" + eingabe);
			} else {
				$("#ergebnisliste").detach();
			}

		});
		</script>
		';
		return $out;
	}
	
	public function hochschuleABisZSucheContent($parent,$eingabe,$buchstabe) {
		$GLOBALS['TSFE']->additionalHeaderData['he_tools_css_1'] = '
		<script src="' . t3lib_extMgm::siteRelPath('he_tools') . 'res/js/delay.js" type="text/javascript"></script>
		<link href="' . t3lib_extMgm::siteRelPath('he_tools') . 'res/hochschule_a_z.css" rel="stylesheet" type="text/css" />
		';
//		$GLOBALS['TSFE']->additionalHeaderData['he_tools_jquery'] = '<script src="' . t3lib_extMgm::siteRelPath('he_portal') . 'res/jquery/js/jquery-1.7.1b.min.js" type="text/javascript"></script>';
//		$out = $this->initGadget($parent->cObj);
		$out = '
	<div class="a_bis_z_suche dreispaltig">
		<script>
		function charClick(buchstabe) {
		var eingabe = document.getElementById("eingabe");
			eingabe.value = "";
			$("#ergebnisliste").detach();
			$("<div id=\'ergebnisliste\'></div>").appendTo($("#namenListe"));
			$("#ergebnisliste").load("index.php?eID=he_tools&action=hochschule_a_bis_z_suche&trenner=1&buchstabe=" + buchstabe);
		}
		</script>
	<form action="" method="post" onSubmit="return false;">
		<div class="azSelector azSelector-list-1">
			<ul class="azSelector azSelector-list-1">';
		$typolink_conf = array(
            'returnLast' => "url", 
            'parameter' => $GLOBALS['TSFE']->id, 
            'additionalParams' => '&azTab=alle', 
            ); 
    $link = $parent->cObj->typolink("", $typolink_conf);		
		$out .= '<li class="tab-alle selected">
							<a onclick="javascript:charClick(\'alle\');return false;" href="' . $link . '" title="Alle Einträge" target="_self">
							<span>Alle</span></a></li>';
		$azListe = array('A','B','C','D','E','F','G','H',
										 'I','J','K','L','M','N','O','PQ',
										 'R','S','T','U','V','W','XYZ');
		foreach ($azListe as $eintrag) {
			$typolink_conf = array(
	            'returnLast' => "url", 
	            'parameter' => $GLOBALS['TSFE']->id, 
	            'additionalParams' => '&azTab=' . $eintrag, 
	            ); 
	    $link = $parent->cObj->typolink("", $typolink_conf);		
			$out .= '<li class="tab-' . $eintrag . '">
								<a onclick="javascript:charClick(\'' . $eintrag . '\');return false;" href="' . $link . '" title="' . $eintrag . '" target="_self">
								<span>' . $eintrag . '</span></a></li>';
			
		}
		$out .= '</ul>
		</div>
		<div style="margin: 10px 0;">
		Bitte geben Sie einen Suchbegriff ein.</div>
	<input class="such_box" type="text" id="eingabe" name="eingabe" size="40" />
		<input id="absenden" type="submit" value="Absenden" />
	</form>
	<div id="namenListe">
			<div id="ergebnisliste">
			';
		$userName = $GLOBALS['TSFE']->fe_user->user['username'];
		$out .= $this->hochschuleABisZSucheGetListData($eingabe,$buchstabe,TRUE,$userName);
		$out .= '</div>
	</div>
	</div>
		
		<script>
		$("#absenden").remove();
		$("#eingabe").bindWithDelay("keyup",function(event) {
			var eingabe = encodeURI($("#eingabe").val());
			if (eingabe.length>0) {
				$("#ergebnisliste").detach();
				$("<div id=\'ergebnisliste\'></div>").appendTo($("#namenListe"));
				$("#ergebnisliste").load("index.php?eID=he_tools&action=hochschule_a_bis_z_suche&trenner=1&eingabe=" + eingabe);
			} else {
				$("#ergebnisliste").detach();
			}

		});
		</script>
		';
		return $out;
	}
	
	public function abfallABisZSucheContent($parent,$eingabe,$buchstabe) {
		$GLOBALS['TSFE']->additionalHeaderData ['he_tools_css_1'] = '<link href="' . t3lib_extMgm::siteRelPath('he_tools') . 'res/hochschule_a_z.css" rel="stylesheet" type="text/css" />';
		$GLOBALS['TSFE']->additionalHeaderData ['he_tools_jquery'] = '
		<script src="' . t3lib_extMgm::siteRelPath('he_tools') . 'res/js/delay.js" type="text/javascript"></script>
		<script src="' . t3lib_extMgm::siteRelPath('he_tools') . 'res/js/jquery-1.7.2.min.js" type="text/javascript"></script>';
		$out = $this->initGadget($parent->cObj);
		$out .= '
	<div class="a_bis_z_suche weiss">
		<script>
		function charClick(buchstabe) {
		var eingabe = document.getElementById("eingabe");
			eingabe.value = "";
			$("#ergebnisliste").detach();
			$("<div id=\'ergebnisliste\'></div>").appendTo($("#namenListe"));
			$("#ergebnisliste").load("index.php?eID=he_tools&action=abfall_a_bis_z_suche&trenner=1&buchstabe=" + buchstabe);
		}
		</script>
	<form action="" method="post" onSubmit="return false;">
		<div class="azSelector azSelector-list-1">
			<ul class="azSelector azSelector-list-1">';
		$typolink_conf = array(
            'returnLast' => "url", 
            'parameter' => $GLOBALS['TSFE']->id, 
            'additionalParams' => '&azTab=alle', 
            ); 
    $link = $parent->cObj->typolink("", $typolink_conf);		
		$out .= '<li class="tab-alle selected">
							<a onclick="javascript:charClick(\'alle\');return false;" href="' . $link . '" title="Alle Einträge" target="_self">
							<span>Alle</span></a></li>';
		$azListe = array('A','B','C','D','E','F','G','H',
										 'I','J','K','L','M','N','O','PQ',
										 'R','S','T','U','V','W','XYZ');
		foreach ($azListe as $eintrag) {
			$typolink_conf = array(
	            'returnLast' => "url", 
	            'parameter' => $GLOBALS['TSFE']->id, 
	            'additionalParams' => '&azTab=' . $eintrag, 
	            ); 
	    $link = $parent->cObj->typolink("", $typolink_conf);		
			$out .= '<li class="tab-' . $eintrag . '">
								<a onclick="javascript:charClick(\'' . $eintrag . '\');return false;" href="' . $link . '" title="' . $eintrag . '" target="_self">
								<span>' . $eintrag . '</span></a></li>';
			
		}
		$out .= '</ul>
		</div>
		<div style="margin: 10px 0;">
		Bitte geben Sie einen Suchbegriff ein.</div>
	<input class="such_box" type="text" id="eingabe" name="eingabe" size="40" />
		<input id="absenden" type="submit" value="Absenden" />
	</form>
	<div id="namenListe">
			<div id="ergebnisliste">
			';
		$out .= $this->abfallABisZSucheGetListData($eingabe,$buchstabe,TRUE);
		$out .= '</div>
	</div>
	</div>
		
		<script>
		$("#absenden").remove();		
		$("#eingabe").bindWithDelay("keyup",function(event) {
			var eingabe = encodeURI($("#eingabe").val());
			if (eingabe.length>0) {
				$("#ergebnisliste").detach();
				$("<div id=\'ergebnisliste\'></div>").appendTo($("#namenListe"));
				$("#ergebnisliste").load("index.php?eID=he_tools&action=abfall_a_bis_z_suche&trenner=1&eingabe=" + eingabe);
			} else {
				$("#ergebnisliste").detach();
			}

		});
		</script>
		';
		return $out;
	}
	
	public function hochschuleABisZSucheGetList($eingabe,$buchstabe='',$trenner='',$username='') {
		$out = $this->hochschuleABisZSucheGetListData($eingabe,$buchstabe,$trenner,$username);
		print($out);
		return TRUE;
	}
	
	public function hochschuleABisZSucheGetListData($eingabe,$buchstabe='',$trenner='',$username='') {
		$out = '';
		$additionalWhere = 'TRUE';
		
		if (empty($username)) {
			$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid','tx_hebest_hauptkategorie', 'deleted=0 AND title="Intranet"');			
			if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
				$hauptkategorie = $row['uid'];
				$additionalWhere = '(NOT FIND_IN_SET (' . $hauptkategorie . ',hauptkategorie))';
			}
		}
		$data = $this->getListData(92967,'produktname','produktname,link,ansprechpartner,bemerkung','tx_hebest_artikel',$eingabe,$buchstabe,FALSE,'produktname',$additionalWhere);
		$tabContent = '';
		$letzterBuchstabe = '';
		foreach ($data as $elemData) {
			$ersterBuchstabe = mb_strtoupper(mb_substr($elemData['produktname'],0,1,'UTF-8'));
			if (!empty($trenner) && $letzterBuchstabe!=$ersterBuchstabe) {
				$tabContent .= '<h2>' . $ersterBuchstabe . '</h2>';
			}
			$letzterBuchstabe = $ersterBuchstabe;
      $link = '';
      if (!empty($elemData['link'])) {
        if (is_numeric($elemData['link'][0])) {
          $link = 'http://www.hs-esslingen.de/index.php?id=' . $elemData['link'];
        } else {
          $link = str_replace('https://','',$elemData['link']);
          $link = str_replace('http://','',$link);
          $link = 'http://' . $link;
        }
        $tabContent .= '<span class="eintrag"><span class="linkwrap">
							 <a target="_blank" href="' . $link . '">' .
          $elemData['produktname'] .
          '</a></span></span>';
      } else {
        if (!empty($elemData['ansprechpartner'])) {
          $link = 'mailto:' . $elemData['ansprechpartner'];
          if (!empty($elemData['bemerkung'])) {
            $link .= '&subject=' . $elemData['bemerkung'];
          }
          $tabContent .= '<span class="eintrag"><span class="linkwrap">
							 <a href="' . $link. '">' . $elemData['produktname'] .
            '</a></span></span>';
        }
      }

		}
		if (empty($tabContent)) {
			$out = '<h3>Es wurden keine Ergebnisse für die Eingabe "' . $eingabe . '" gefunden !</h3>';
		} else {
			$out = '<div class="hochschule_a_z">' .
						 $tabContent .
						 '</div>';
		}
		return $out;
	}
	
	public function abfallABisZSucheGetList($eingabe,$buchstabe='',$trenner='') {
		$out = $this->abfallABisZSucheGetListData($eingabe,$buchstabe,$trenner);
		print($out);
		return TRUE;
	}
	
	public function abfallABisZSucheGetListData($eingabe,$buchstabe='',$trenner='') {
		$out = '';
		$data = $this->getListData(93833,'produktname','produktname,bemerkung','tx_hebest_artikel',$eingabe,$buchstabe,FALSE,'produktname');
		$tabContent = '';
		$letzterBuchstabe = '';
		$parseObj = t3lib_div::makeInstance('t3lib_parsehtml_proc');
		foreach ($data as $elemData) {
			$ersterBuchstabe = mb_strtoupper(mb_substr($elemData['produktname'],0,1,'UTF-8'));
			if (!empty($trenner) && $letzterBuchstabe!=$ersterBuchstabe) {
				$tabContent .= '<h2>' . $ersterBuchstabe . '</h2>';
				$rowType = 'even';
			}
			if ($rowType=='even') {
				$rowType = 'odd';
			} else {
				$rowType = 'even';
			}
			$letzterBuchstabe = $ersterBuchstabe;
			
			$beschreibung = str_replace('https://www.hs-esslingen.de/?id=','http://www.hs-esslingen.de/index.php?id=',$elemData['bemerkung']);
			$tabContent .= '<div class="row ' . $rowType . '">' . 
										'<span class="title">' . $elemData['produktname'] . '</span>'. 
										'<span class="content">' . $beschreibung . '</span>'. 
										'</div>';
													
		}
		if (empty($tabContent)) {
			$out = '<h3>Es wurden keine Ergebnisse für die Eingabe "' . $eingabe . '" gefunden !</h3>';
		} else {
			$out = '<div class="hochschule_a_z">' .
						 $tabContent .
						 '</div>';
		}
		return $out;
	}
	
	public function getJs($app,$pid) {
		switch ($app) {
			case 'hochschuleABisZ':
				return $this->getJsHochschuleABisZ($pid);
			case '':
				return $this->getJsAbfallABisZ($pid);
			case 'edvVorzugsListe':
				return $this->getJsEdvVorzugsListe($pid);
			case 'hersteller':
				return $this->getJsHersteller($pid);
			case 'lieferanten':
				return $this->getJsLieferanten($pid);
			case 'shopBueromaterialGup':
				return $this->getJsBueromaterialGup($pid);
		}
	}
	
	public function getJsHochschuleABisZ($pid) {
	}
	
	public function getJsAbfallABisZ($pid) {
	}
	
	public function getJsBueromaterialGup($pid) {
		$minBuchstaben = 3;
		$out = '
		<script>
		var buchstabe = "";
		function charClick(c) {
		buchstabe = c;
		absenden();
		}
		
		function absenden() {
			var eingabe = encodeURI($("#eingabe").val());
			var eingabeClean = $("#eingabe").val();
			var hauptkategorie = encodeURI($("#hauptkategorie").val());
			var unterkategorie = encodeURI($("#unterkategorie").val());
			if (eingabeClean.length>=' . $minBuchstaben . ' || buchstabe!="" || hauptkategorie!="0" || unterkategorie!="0") {
				$("#ergebnisliste").detach();
				$("<div id=\'ergebnisliste\'></div>").appendTo($("#namenListe"));
				$("#ergebnisliste").load("index.php?eID=he_tools&action=ajaxContentForm&pid=' . $pid . '&app=shopBueromaterialGup&trenner=1&eingabe=" + eingabe +
				"&buchstabe=" + buchstabe + "&hauptkategorie=" + hauptkategorie + "&unterkategorie=" + unterkategorie);
			} else {
				$("#ergebnisliste").detach();
				if (eingabeClean.length>0) {
					$("<div id=\"ergebnisliste\">Bitte geben Sie mindestens ' . $minBuchstaben . ' Buchstaben ein.</div>").appendTo($("#namenListe"));
				}
			}
		}
		$("#absenden").remove();
		$("#eingabe").keyup(function(event) {
			absenden();
		});
		$("#hauptkategorie").change(function(event) {
			absenden();
		});
		$("#unterkategorie").change(function(event) {
			absenden();
		});
		$("#reset").click(function(event) {
			$("#suchform").clearForm();
			absenden();
		});
		</script>
		';
		return $out;
		
	}
	
	public function getJsHersteller($pid) {
		$minBuchstaben = 3;
		$out = '
	<script> 
		var buchstabe = "";
		function charClick(c) {
			buchstabe = c;
			absenden();
		}
		
		function absenden() {
			var eingabe = encodeURI($("#eingabe").val());
			var eingabeClean = $("#eingabe").val();
			var keyword1 = encodeURI($("#keyword1").val());
			var keyword2 = encodeURI($("#keyword2").val());
			if (eingabeClean.length>=' . $minBuchstaben . ' || buchstabe!="" || keyword1!="0" || keyword2!="0") {
				$("#ergebnisliste").detach();
				$("<div id=\"ergebnisliste\"></div>").appendTo($("#namenListe"));
				$("#ergebnisliste").load("index.php?eID=he_tools&action=ajaxContentForm&pid=' . $pid . '&app=hersteller&eingabe=" + eingabe + 
																						"&buchstabe=" + buchstabe + "&keyword1=" + keyword1 + "&keyword2=" + keyword2);
			} else {
				$("#ergebnisliste").detach();
				if (eingabeClean.length>0) {
					$("<div id=\"ergebnisliste\">Bitte geben Sie mindestens ' . $minBuchstaben . ' Buchstaben ein.</div>").appendTo($("#namenListe"));
				}
			}
		}
		$("#absenden").remove();
		$("#eingabe").keyup(function(event) {
			absenden();
		});
		$("#keyword1").change(function(event) {
			absenden();
		});
		$("#keyword2").change(function(event) {
			absenden();
		});
		$("#reset").click(function(event) {
			$("#suchform").clearForm()
 			absenden();
		});
	</script>
		';
		return $out;
	}
	
	public function getJsLieferanten($pid) {
		$minBuchstaben = 3;
		$out = '
	<script> 
		var buchstabe = "";
		function charClick(c) {
			buchstabe = c;
			absenden();
		}
		
		function absenden() {
			var eingabe = encodeURI($("#eingabe").val());
			var eingabeClean = $("#eingabe").val();
			var keyword1 = encodeURI($("#keyword1").val());
			var keyword2 = encodeURI($("#keyword2").val());
			if (eingabeClean.length>=' . $minBuchstaben . ' || buchstabe!="" || keyword1!="0" || keyword2!="0") {
				$("#ergebnisliste").detach();
				$("<div id=\"ergebnisliste\"></div>").appendTo($("#namenListe"));
				$("#ergebnisliste").load("index.php?eID=he_tools&action=ajaxContentForm&pid=' . $pid . '&app=lieferanten&eingabe=" + eingabe + 
																						"&buchstabe=" + buchstabe + "&keyword1=" + keyword1 + "&keyword2=" + keyword2);
			} else {
				$("#ergebnisliste").detach();
				if (eingabeClean.length>0) {
					$("<div id=\"ergebnisliste\">Bitte geben Sie mindestens ' . $minBuchstaben . ' Buchstaben ein.</div>").appendTo($("#namenListe"));
				}
			}
		}
		$("#absenden").remove();
		$("#eingabe").keyup(function(event) {
			absenden();
		});
		$("#keyword1").change(function(event) {
			absenden();
		});
		$("#keyword2").change(function(event) {
			absenden();
		});
		$("#reset").click(function(event) {
			$("#suchform").clearForm()
 			absenden();
		});
	</script>
		';
		return $out;
	}
	
	public function getJsEdvVorzugsListe($pid) {
		$minBuchstaben = 3;
		$out = '
	<script> 
		var buchstabe = "";
		function charClick(c) {
			buchstabe = c;
			absenden();
		}
		
		function absenden() {
			var eingabe = encodeURI($("#eingabe").val());
			var eingabeClean = $("#eingabe").val();
			var hauptkategorie = encodeURI($("#hauptkategorie").val());
			var unterkategorie = encodeURI($("#unterkategorie").val());
			var eigenschaft1 = encodeURI($("#eigenschaft1").val());
			var eigenschaft2 = encodeURI($("#eigenschaft2").val());
			var hersteller = encodeURI($("#hersteller").val());
			var lieferant = encodeURI($("#lieferant").val());
			if (eingabeClean.length>=' . $minBuchstaben . ' || buchstabe!="" || hauptkategorie!="0" || unterkategorie!="0") {
				$("#ergebnisliste").detach();
				$("<div id=\"ergebnisliste\"></div>").appendTo($("#namenListe"));
				$("#ergebnisliste").load("index.php?eID=he_tools&action=ajaxContentForm&pid=' . $pid . '&app=edvVorzugsListe&trenner=1&eingabe=" + eingabe + 
																						"&buchstabe=" + buchstabe + "&hauptkategorie=" + hauptkategorie + "&unterkategorie=" + unterkategorie + 
																						"&eigenschaft1=" + eigenschaft1 + "&eigenschaft2=" + eigenschaft2 +
																					  "&hersteller=" + hersteller + "&lieferant=" + lieferant);
			} else {
				$("#ergebnisliste").detach();
				if (eingabeClean.length>0) {
					$("<div id=\"ergebnisliste\">Bitte geben Sie mindestens ' . $minBuchstaben . ' Buchstaben ein.</div>").appendTo($("#namenListe"));
				}
			}
		}
		$("#absenden").remove();
		$("#eingabe").keyup(function(event) {
			absenden();
		});
		$("#hauptkategorie").change(function(event) {
			absenden();
		});
		$("#unterkategorie").change(function(event) {
			absenden();
		});
		$("#eigenschaft1").change(function(event) {
			absenden();
		});
		$("#eigenschaft2").change(function(event) {
			absenden();
		});
		$("#hersteller").change(function(event) {
			absenden();
		});
		$("#lieferant").change(function(event) {
			absenden();
		});
		$("#reset").click(function(event) {
			$("#suchform").clearForm()
 			absenden();
		});
	</script>
		';
		return $out;
	}
	
	public function getFilterHerstellerLieferanten($pid) {
		$filter = '';
		
		$keyword1 = $this->getTableTitles($pid,'tx_hebest_keyword1','title','uid','title');
		$filter .= '<div class="selectbox"><span class="selectbox_title">Eigenschaft 1</span>';
		$filter .= '<select id="keyword1">
                <option value="">Alle Einträge</option>';
		foreach ($keyword1 as $id=>$title) {
			$filter .= '<option value="' . $id . '">' . $title . '</option>';
		}
		$filter .= '</select></div>';
		
		$keyword2 = $this->getTableTitles($pid,'tx_hebest_keyword2','title','uid','title');
		$filter .= '<div class="selectbox"><span class="selectbox_title">Eigenschaft 2</span>';
		$filter .= '<select id="keyword2">
                <option value="">Alle Einträge</option>';
		foreach ($keyword2 as $id=>$title) {
			$filter .= '<option value="' . $id . '">' . $title . '</option>';
		}
		$filter .= '</select></div>';
		
		return $filter;
	}
	
	public function getFilterEdvVorzugsListe($pid) {
		$filter = '';
		$hauptkategorie = $this->getTableTitles($pid,'tx_hebest_hauptkategorie','title','uid','title');
		$filter .= '<div class="selectbox"><span class="selectbox_title">Hauptkategorie</span>';
		$filter .= '<select id="hauptkategorie">
                <option value="">Alle Einträge</option>';
		foreach ($hauptkategorie as $id=>$title) {
			$filter .= '<option value="' . $id . '">' . $title . '</option>';
		}
		$filter .= '</select></div>';
		
		$unterkategorie = $this->getTableTitles($pid,'tx_hebest_unterkategorie','title','uid','title');
		$filter .= '<div class="selectbox"><span class="selectbox_title">Unterkategorie</span>';
		$filter .= '<select id="unterkategorie">
                <option value="">Alle Einträge</option>';
		foreach ($unterkategorie as $id=>$title) {
			$filter .= '<option value="' . $id . '">' . $title . '</option>';
		}
		$filter .= '</select></div>';
		
		$eigenschaft1 = $this->getTableTitles($pid,'tx_hebest_eigenschaft1','title','uid','title');
		$filter .= '<div class="selectbox"><span class="selectbox_title">Eigenschaft 1</span>';
		$filter .= '<select id="eigenschaft1">
                <option value="">Alle Einträge</option>';
		foreach ($eigenschaft1 as $id=>$title) {
			$filter .= '<option value="' . $id . '">' . $title . '</option>';
		}
		$filter .= '</select></div>';
		
		$eigenschaft2 = $this->getTableTitles($pid,'tx_hebest_eigenschaft2','title','uid','title');
		$filter .= '<div class="selectbox"><span class="selectbox_title">Eigenschaft 2</span>';
		$filter .= '<select id="eigenschaft2">
                <option value="">Alle Einträge</option>';
		foreach ($eigenschaft2 as $id=>$title) {
			$filter .= '<option value="' . $id . '">' . $title . '</option>';
		}
		$filter .= '</select></div>';
		
		$hersteller = $this->getTableTitles($pid,'tx_hebest_hersteller','title','uid','title');
		$filter .= '<div class="selectbox"><span class="selectbox_title">Hersteller</span>';
		$filter .= '<select id="hersteller">
                <option value="">Alle Einträge</option>';
		foreach ($hersteller as $id=>$title) {
			$filter .= '<option value="' . $id . '">' . $title . '</option>';
		}
		$filter .= '</select></div>';
		
		$lieferant = $this->getTableTitles($pid,'tx_hebest_lieferanten','title','uid','title');
		$filter .= '<div class="selectbox"><span class="selectbox_title">Lieferanten</span>';
		$filter .= '<select id="lieferant">
                <option value="">Alle Einträge</option>';
		foreach ($lieferant as $id=>$title) {
			$filter .= '<option value="' . $id . '">' . $title . '</option>';
		}
		$filter .= '</select></div>';
		return $filter;
	}
	
	public function getFilterShopBueromaterialGup($pid) {
		$filter = '';
		$hauptkategorie = $this->getTableTitles($pid,'tx_hebest_hauptkategorie','title','uid','title');
		$filter .= '<div class="selectbox"><span class="selectbox_title">Kategorie</span>';
		$filter .= '<select id="hauptkategorie">
                <option value="">Alle Einträge</option>';
		foreach ($hauptkategorie as $id=>$title) {
			$filter .= '<option value="' . $id . '">' . $title . '</option>';
		}
		$filter .= '</select></div>';
		
		$unterkategorie = $this->getTableTitles($pid,'tx_hebest_unterkategorie','title','uid','title');
		$filter .= '<div class="selectbox"><span class="selectbox_title">Produktgruppe</span>';
		$filter .= '<select id="unterkategorie">
                <option value="">Alle Einträge</option>';
		foreach ($unterkategorie as $id=>$title) {
			$filter .= '<option value="' . $id . '">' . $title . '</option>';
		}
		$filter .= '</select></div>';
		return $filter;
	}
	
	public function bueromaterialGupRenderListData($data) {
		$tabContent = '';
		$parseObj = t3lib_div::makeInstance('t3lib_parsehtml_proc');
		$tabContent = '<table class="contenttable>
						<thead>
						<tr>
						<th class="bild_links">&nbsp;</th>
						<th class="title">Produktbezeichnung</th>
						<th class="kategorie">Kategorie</th>
						<th class="preis">Preis</th>
						</tr>
						</thead>
						<tbody>';
		$rowType = 'even';
		foreach ($data as $elemData) {
			if ($rowType=='even') {
				$rowType = 'odd';
			} else {
				$rowType = 'even';
			}
			$beschreibung = str_replace('https://www.hs-esslingen.de/?id=','http://www.hs-esslingen.de/index.php?id=',$elemData['bemerkung']);
			if (!empty($elemData['bild'])) {
				$bild = '<span class="image"><img width="20px" src="/uploads/tx_hebest/' . $elemData['bild'] . '" /></span>';
			} else {
				$bild = '';
			}
			$tabContent .= '<tr class="row ' . $rowType . '">' . 
										'<td class="bild_links">' . $bild . '</td>' .
										'<td class="title">' . $elemData['produktname'] . '</td>'. 
										'<td class="kategorie">' . $elemData['kategorie'] . '</td>'. 
										'<td class="preis">' . $elemData['preis'] . '</td>'. 
										'</tr>';
													
		}
		$tabContent .= '</tbody></table>';
		if (empty($tabContent)) {
			$out = '<h3>Es wurden keine Ergebnisse für Ihre Auswahl gefunden !</h3>';
		} else {
			$out = '<div class="edv_vorzugsliste">' .
						 $tabContent .
						 '</div>';
		}
		return $out;
	}
	
	public function edvVorzugsListeRenderListData($data) {
		$tabContent = '';
		$parseObj = t3lib_div::makeInstance('t3lib_parsehtml_proc');
		$rowType = 'even';
		foreach ($data as $elemData) {
			if ($rowType=='even') {
				$rowType = 'odd';
			} else {
				$rowType = 'even';
			}
			$beschreibung = str_replace('https://www.hs-esslingen.de/?id=','http://www.hs-esslingen.de/index.php?id=',$elemData['bemerkung']);
			if (!empty($elemData['bild'])) {
				$bild = '<span class="image"><img width="100px" src="/uploads/tx_hebest/' . $elemData['bild'] . '" /></span>';
			} else {
				$bild = '';
			}
			$tabContent .= '<div class="row ' . $rowType . '">' . 
										'<span class="title">' . $elemData['produktname'] . '</span>'. 
										'<span class="image">' . $bild .
										'<span class="content"><h3>allgemeine Beschreibung</h3>' . $beschreibung . '</span>'. 
										'</div>';
													
		}
		if (empty($tabContent)) {
			$out = '<h3>Es wurden keine Ergebnisse für Ihre Auswahl gefunden !</h3>';
		} else {
			$out = '<div class="edv_vorzugsliste">' .
						 $tabContent .
						 '</div>';
		}
		return $out;
	}
	
	public function bueromaterialGupGetListData($data) {
		$eingabe = $data['eingabe'];
		$buchstabe = $data['buchstabe'];
		$hauptkategorie = $data['hauptkategorie'];
		$unterkategorie = $data['unterkategorie'];
		$pid = $data['pid'];
		
		$whereFilter = 'TRUE';
		if (!empty($hauptkategorie)) {
			$whereFilter .= ' AND hauptkategorie=' . $hauptkategorie;
		}
		if (!empty($unterkategorie)) {
			$whereFilter .= ' AND unterkategorie=' . $unterkategorie;
		}
		$searchFields = 'produktname';
		$out = '';
		$daten = $this->getListData($pid,$searchFields,'produktname,artikelnummer,bild,preis','tx_hebest_artikel',$eingabe,$buchstabe,FALSE,'produktname',$whereFilter);
		return array('data' => $daten, 'searchFields' =>$searchFields);
	}
	
	public function edvVorzugsListeGetListData($data) {
		$eingabe = $data['eingabe'];
		$buchstabe = $data['buchstabe'];
		$hauptkategorie = $data['hauptkategorie'];
		$unterkategorie = $data['unterkategorie'];
		$eigenschaft1 = $data['eigenschaft1'];
		$eigenschaft2 = $data['eigenschaft2'];
		$hersteller = $data['hersteller'];
		$lieferant = $data['lieferant'];
		$pid = $data['pid'];
		
		$whereFilter = 'TRUE';
		if (!empty($hauptkategorie)) {
			$whereFilter .= ' AND hauptkategorie=' . $hauptkategorie;
		}
		if (!empty($unterkategorie)) {
			$whereFilter .= ' AND unterkategorie=' . $unterkategorie;
		}
		if (!empty($eigenschaft1)) {
			$whereFilter .= ' AND eigenschaft1=' . $eigenschaft1;
		}
		if (!empty($eigenschaft2)) {
			$whereFilter .= ' AND eigenschaft2=' . $eigenschaft2;
		}
		if (!empty($lieferant)) {
			$whereFilter .= ' AND lieferant=' . $lieferant;
		}
		if (!empty($hersteller)) {
			$whereFilter .= ' AND hersteller=' . $hersteller;
		}
		$searchFields = 'produktname';
		$out = '';
		$daten = $this->getListData($pid,$searchFields,'produktname,bemerkung,artikelnummer,bild','tx_hebest_artikel',$eingabe,$buchstabe,FALSE,'produktname',$whereFilter);
		return array('data' => $daten, 'searchFields' =>$searchFields);
	}
	
	public function getTableTitles($pid,$table,$titleField,$idField,$orderBy='') {
		if (empty($orderBy)) {
			$orderBy = $titleField;
		}
		$where = '(deleted=0 AND  pid=' . $pid . ')';
		$daten = array();
		$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery($titleField . ',' . $idField,$table, $where,'',$orderBy);
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
			$daten[$row[$idField]] = $row[$titleField];
		}	
		return $daten;
	}
	
	public function getListData($pid,$searchFields,$selectFields,$table,$eingabe='',$buchstabe='',$searchFromStartOnly=FALSE,$orderBy='',$additionalWhere='TRUE') {
		$out = '';
		$searchFieldArray = explode(',',$searchFields);
		$where = '(deleted=0 AND hidden=0 AND 
							pid=' . $pid . ')';
		if (!empty($eingabe)) {
			foreach ($searchFieldArray as $field) {
				if ($searchFromStartOnly) {
					$where .= ' AND (' . $field . ' LIKE "' . trim($eingabe) . '%")';
				} else {
					$where .= ' AND (' . $field . ' LIKE "%' . trim($eingabe) . '%")';
				}
			}
			
		}
		$where .= ' AND ' . $additionalWhere;
		
		$buchstabe = strtolower($buchstabe);
		if (!empty($buchstabe)) {
			switch ($buchstabe) {
			case 'alle': 
				break;	
			case 'pq': 
				foreach ($searchFieldArray as $field) {
					$where .= ' AND (' . $field . ' LIKE "p%" OR 
													 ' . $field . ' LIKE "q%")';
				}
				break;	
			case 'xyz': 
				foreach ($searchFieldArray as $field) {
					$where .= ' AND (' . $field . ' LIKE "x%" OR 
													 ' . $field . ' LIKE "y%" OR 
													 ' . $field . ' LIKE "z%")';
				}
				break;	
			default:
				foreach ($searchFieldArray as $field) {
					$where .= ' AND (' . $field . ' LIKE "' . trim($buchstabe) . '%")';
				}
				break;
			}
		}
		$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery($selectFields,$table, $where,'',$orderBy);			
		$daten = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
			$daten[] = $row;
		}
		return $daten;
	}
	
	public function personenSuche(&$parent) {
		$GLOBALS['TSFE']->additionalHeaderData ['he_tools_css_1'] = '
		<link href="' . t3lib_extMgm::siteRelPath('he_tools') . 'res/personensuche.css" rel="stylesheet" type="text/css" />
		<script src="' . t3lib_extMgm::siteRelPath('he_tools') . 'res/js/delay.js" type="text/javascript"></script>
		';
		
		$username = $GLOBALS['TSFE']->fe_user->user['username'];
		$gadgetId = tx_he_portal_lib_gadgets::gibGadgetId($GLOBALS['TSFE']->id);
		$out = $this->initGadget($parent->cObj,$username,$gadgetId);
		$gadgetEinstellungen = tx_he_portal_lib_gadgets::gadgetEinstellungenLaden($gadgetId, $username);
		$mitEnterAbsenden = tx_he_portal_lib_gadgets::gibGadgetEinstellungenWert($gadgetEinstellungen,'modus');
		$minBuchstaben = tx_he_portal_lib_gadgets::gibGadgetEinstellungenWert($gadgetEinstellungen,'anz_zeichen');
		if ($mitEnterAbsenden=='on') {
			$minBuchstaben = 99;
		}
		$out = $this->initGadget($parent->cObj);
		$url = t3lib_div::getIndpEnv('TYPO3_SITE_URL') . 
					 'index.php?id=' . $GLOBALS['TSFE']->id;
		$post = t3lib_div::_POST();
		if (isset($post['eingabe'])) {
			$eingabe = $post['eingabe'];
		} else {
			$eingabe = '';
		}
		if (isset($post['bereich'])) {
			$bereich = $post['bereich'];
		} else {
			$bereich = '';
		}
		if (isset($post['rolle'])) {
			$rolle = $post['rolle'];
		} else {
			$rolle = '';
		}
		if ($minBuchstaben==99) {
			$hinweis = 'Bitte betätigen Sie Ihre Eingabe mit ENTER';
		} else {
			$hinweis = 'Bitte geben Sie mindestens ' . $minBuchstaben . ' Zeichen ein, um die Suche zu starten.';
		}
		if (!empty($eingabe) || !empty($bereich) || !empty($rolle)) {
			$postErgebnis = '<div id="personenliste">' .
											$this->personenSucheDaten($eingabe,$bereich,$rolle) .
											'</div>';
		} else {
			$postErgebnis = '<div id="personenliste">' .
											$hinweis .
											'</div>';
		}
		$bereichsListe = '';
		$bereichsGruppen = array(
														 'Alle'=>'Alle',
														 'AN'=>'Angewandte Naturwissenschaft',
														 'BW'=>'Betriebswirtschaft',
														 'FZ'=>'Fahrzeugtechnik',
														 'GL'=>'Grundlagen',
														 'GS'=>'Graduate School',
														 'GU'=>'Gebäude Energie Umwelt',
														 'IT'=>'Informationstechnik',
														 'MB'=>'Maschinenbau',
														 'ME'=>'Mechatronik',
														 'SP'=>'Soziale Arbeit, Gesundheit und Soziales',
														 'WI'=>'Wirtschaftsingenieurwesen',
														 'AUSLBEZ'=>'Akademisches Auslandsamt',
														 'DZ'=>'Didaktikzentrum',
														 'FINA'=>'Finanzabteilung',
														 'GUP'=>'Grundsatz und Planungsabteilung',
														 'IFS'=>'Institut für Fremdsprachen',
														 'OEFFENTLICHKEITSARBEIT'=>'Öffentlichkeitsarbeit (RÖM)',
														 'PERSABT'=>'Personalabteilung',
														 'PERSONALRAT'=>'Personalrat',
														 'RZ'=>'Rechenzentrum',
													   'REKTORAT'=>'Rektorat',
													   'STUDA'=>'Studentische  Abteilung',
														 'TECHNABT'=>'Technische Abteilung',
														 );
		
		foreach ($bereichsGruppen as $kuerzel=>$name) {
			$usergroup = tx_he_personen_util::gibBenutzergruppe($kuerzel);
			$bereichsListe .= '<option value="' . $usergroup . '">' . $name . '</option>
												';
		}
		$gruppenListe = '';
		$funktionsGruppen = array(
														 'Alle'=>'Alle',
														 'AUSZUBILDENDE'=>'Auszubildende',
														 'DEKANAT'=>'Dekanat',
														 'LB'=>'Lehrbeauftragte',
														 'LEITUNG'=>'Leitung',
														 'HE-MITARBEITER'=>'Mitarbeiter',
														 'PROFESSOR'=>'Professoren',
														 'SEKRETARIAT'=>'Sekretariat',
														  );
		foreach ($funktionsGruppen as $kuerzel=>$name) {
			$usergroup = tx_he_personen_util::gibBenutzergruppe($kuerzel);
			$gruppenListe .= '<option value="' . $usergroup . '">' . $name . '</option>
												';
		}
		$out .= self::getJsAnimationCode();
		$out .= '
	<style>
	#personenliste {
		width: 100%;
	}
	</style>
	<div class="personensuche">
	<form id="suchform" action="' . $url . '" method="POST">
		<div class="filter name">
			<label for="eingabe">Nachname: </label>
			<input id="eingabe" name="eingabe" title="' . $hinweis . '" size="30" />
		</div>
		<div class="filter bereich">
			<label for="bereich">Bereich: </label>
			<select id="bereich" name="bereich">
			' . $bereichsListe . '
			</select>
		</div>
		<div class="filter rolle">
			<label for="rolle">Rolle: </label>
			<select id="rolle" name="rolle">
			' . $gruppenListe . '
			</select>
		</div>
		<div class="reset">
			</br><input id="reset" type="reset" title="Eingaben zurücksetzen" value="X" />
		</div>
		<input id="absenden" type="submit" value="Absenden" />
	</form>
	<div id="namenListe">' .
	$postErgebnis .
	'</div>
	</div>
		
		<script>
		$.fn.clearForm = function() {
		  return this.each(function() {
		    var type = this.type, tag = this.tagName.toLowerCase();
		    if (tag == "form")
		      return $(":input",this).clearForm();
		    if (type == "text" || type == "password" || tag == "textarea")
		      this.value = "";
		    else if (type == "checkbox" || type == "radio")
		      this.checked = false;
		    else if (tag == "select")
		      this.selectedIndex = 0;
		  });
		};
		function executeAjax(url){
      var result=""
      $.ajax({
        url: url,
        async: false,
        beforeSend : function(){
          processingAnimation("start","bitte warten");
        },
        success: function(data, request) {
          processingAnimation("stop");
          result = data;
        }
      });
      return result;
    }

		function absenden() {
			var eingabe = encodeURI($("#eingabe").val());
			var eingabeClean = $("#eingabe").val();
			var bereich = encodeURI($("#bereich").val());
			var rolle = encodeURI($("#rolle").val());
			if (eingabeClean.length>=' . $minBuchstaben . ' || bereich!="0" || rolle!="0") {
				processingAnimation("start","bitte warten");
				$("#personenliste").detach();
//				$("<div id=\'personenliste\'></div>").appendTo($("#namenListe"));
				var erg = executeAjax("index.php?eID=he_tools&action=personensuche&eingabe=" + eingabe + "&bereich=" + bereich + "&rolle=" + rolle + "&minChars=' . $minBuchstaben . '");
        if (erg=="") {
					$("<div id=\'personenliste\' class=\"rot\"><br/>Bitte aktualisieren Sie das Browserfenster (z.B. mit der Taste F5)!<br/></div>").appendTo($("#namenListe"));
				} else {
					$("<div id=\'personenliste\'>" + erg + "</div>").appendTo($("#namenListe"));
				}
/*
				$("#personenliste").load("index.php?eID=he_tools&action=personensuche&eingabe=" + eingabe + "&bereich=" + bereich + "&rolle=" + rolle, function() {
					processingAnimation("stop");
				});
*/
		} else {
				$("#personenliste").detach();
				if (eingabeClean.length>0) {
					$("<div id=\"personenliste\">Bitte geben Sie mindestens ' . $minBuchstaben . ' Buchstaben ein.</div>").appendTo($("#namenListe"));
				}
			}
		}
		$("#absenden").remove();';
		if ($minBuchstaben<99) {
			$out .= '
			 		$("#eingabe").bindWithDelay("keyup",function(event) {
			 			absenden();
			 		});';
		}
		$out .= '
						 $("#bereich").change(function(event) {
								absenden();
						 });
						 $("#rolle").change(function(event) {
								absenden();
						 });
						 $("#reset").click(function(event) {
						 		$("#suchform").clearForm()
 						 		absenden();
						 });
						 </script>
		';
		return $out;
	}
	
	public function gibTelefonNummern($telefonNummern,$international=FALSE) {
		$telAusgabe = array();
		$telDaten = explode(',',$telefonNummern);
		foreach ($telDaten as $tel) {
			if (!empty($tel)) {
				if ($tel{0} == 1) {
					$telAusgabe[] = '<b>07161/679-' . $tel . '</b>';
				} else {
					$telAusgabe[] = '<b>0711/397-' . $tel . '</b>';
				}			
			}
		}
		return implode('<br/>',$telAusgabe);
	}
	
	public function gibRaumNummern($raumNummern) {
		$raumNummern = str_replace(',','<br/>',$raumNummern);
		$raumNummern = str_replace(' ','&nbsp;',$raumNummern);
		return $raumNummern;
	}
	
	public function personenSucheGetList($eingabe,$bereich='',$rolle='') {
		$out = $this->personenSucheDaten($eingabe,$bereich,$rolle);
		print($out);
		return TRUE;
	}
	
	public function personenSucheDaten($eingabe,$bereich='',$rolle='') {
		$out = '';
		$where = '(deleted=0 AND disable=0 AND 
							NOT (FIND_IN_SET(71,usergroup)>0) AND pid=22881)';
		if (!empty($eingabe)) {
/*
			$where .= ' AND
							(last_name LIKE "' . trim($eingabe) . '%" OR
							 username LIKE "%' . trim($eingabe) . '%")
							';
*/			
			$where .= ' AND
							(last_name LIKE "%' . trim($eingabe) . '%")
							';
		}
		if (!empty($bereich) && $bereich!=0) {
			$bereichsGruppen = array();
			$findListe = array();
			tx_he_personen_util::gibBenutzergruppenRekusiv($bereich,$bereichsGruppen,1);
			foreach ($bereichsGruppen as $group) {
				$findListe[] = 'FIND_IN_SET(' . $group . ',usergroup)>0';
			}
			$where .= ' AND (' . implode(' OR ', $findListe) . ')';
		}
		$zweiEbenen = array(133,169);
		if (!empty($rolle) && $rolle!=0) {
			$funktionsGruppen = array();
			$findListe = array();
			if (in_array($rolle,$zweiEbenen)) {
				$ebenen = 2;
			} else {
				$ebenen = 1;
			}
			tx_he_personen_util::gibBenutzergruppenRekusiv($rolle,$funktionsGruppen,$ebenen);
			foreach ($funktionsGruppen as $group) {
				$findListe[] = 'FIND_IN_SET(' . $group . ',usergroup)>0';
			}			
			$where .= ' AND (' . implode(' OR ', $findListe) . ')';
		}
// return $where;	
		$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery('DISTINCT first_name,last_name,email,tx_hepersonen_raumnummer,tx_hepersonen_profilseite,telephone','fe_users', $where,'','last_name,first_name','0,100');			
		$bg = 'hg_hellblau';
		$tabContent = '';
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
			if (empty($eingabe) ||
				  strpos(mb_strtolower($row['last_name'],'UTF-8'),
				  			 mb_strtolower(trim($eingabe),'UTF-8'))!==FALSE) {
				$tel = $this->gibTelefonNummern($row['telephone']);
				if (empty($tel)) {
					$tel = '-';
				}
				$raum = $this->gibRaumNummern($row['tx_hepersonen_raumnummer']);
				if (empty($raum)) {
					$raum = '-';
				}
				if ($bg=='') {
					$bg=='hg_hellblau';
				} else {
					$bg=='';
				}
				$tabContent .= '<tr class="' . $bg . '">
								 	<td class="name">
								 		<a target="_blank" href="http://www.hs-esslingen.de/index.php?id=' . $row['tx_hepersonen_profilseite'] . '">' . 
											$row['last_name'] . ', ' . $row['first_name'] . '
										</a>
										</td>
								 	<td class="tel">' . $tel . '</td>
								 	<td class="raum">' . $raum . '</td>
								 	<td class="mail">
								 		<a title="E-Mail an ' . $row['first_name'] . ' ' .$row['last_name'] . ' senden" href="mailto:' . $row['email'] . '">' .
										'<img src="/fileadmin/images/css/mail.gif" />' .
										'</a>
										</td>
								 	</tr>';
			}
		}
		if (empty($tabContent)) {
			$out = '<h3>Es wurde keine Person für Ihre Sucheingabe gefunden !</h3>';
		} else {
			$out = '<table class="tab100"><tr class="ueberschrift">
							 <th>Name</th>
							 <th>Telefon</th>
							 <th>Raum</th>
							 <th>E-Mail</th>
							 </tr>' . $tabContent . '</table>';
			
		}
		return $out;
	}

	public function ajaxContentForm($app,$data) {
		$GLOBALS['TSFE']->additionalHeaderData ['he_tools_css_1'] = '<link href="' . t3lib_extMgm::siteRelPath('he_tools') . 'res/data_view.css" rel="stylesheet" type="text/css" />';
		//$GLOBALS['TSFE']->additionalHeaderData['he_tools_css_1'] = '<link href="' . t3lib_extMgm::siteRelPath('he_tools') . 'res/' . $cssDatei . '" rel="stylesheet" type="text/css" />';
		$daten = $this->ajaxContentFormGetListData($app,$data);
		$filter = $this->getFilter($app,$data);
		
		$out = '<div class="filter">' . $filter . '</div>';
		$out .= '
		<div class="a_bis_z_suche ' . $app . '">
	<form action="" method="post" onSubmit="return false;">
	<div class="azSelector azSelector-list-1">
	<ul class="azSelector azSelector-list-1">';
		$link = 'index.php?id=' . $GLOBALS['TSFE']->id . '&azTab=alle';
		$out .= '<li class="tab-alle selected">
		<a onclick="javascript:charClick(\'alle\');return false;" href="' . $link . '" title="Alle Einträge" target="_self">
		<span>Alle</span></a></li>';
		$azListe = array('A','B','C','D','E','F','G','H',
				'I','J','K','L','M','N','O','PQ',
				'R','S','T','U','V','W','XYZ');
		foreach ($azListe as $eintrag) {
			$link = 'index.php?id=' . $GLOBALS['TSFE']->id . '&azTab=' . $eintrag;
			$out .= '<li class="tab-' . $eintrag . '">';
			$searchFields = explode(',',$daten['searchFields']);
			$buchstabeVorhanden = FALSE;
			foreach ($daten['data'] as $entry) {
				foreach($searchFields as $field) {
					if (strtoupper($entry[$field][0])==$eintrag) {
						$buchstabeVorhanden = TRUE;
					}
				}
			}
			if ($buchstabeVorhanden) {
				$out .= '<a onclick="javascript:charClick(\'' . $eintrag . '\');return false;" href="' . $link . '" title="' . $eintrag . '" target="_self">
								<span>' . $eintrag . '</span></a>';
			} else {
				$out .= '<span class="disable">' . $eintrag . '</span>';
			}
			$out .= '</li>';
		}
		$out .= '</ul>
		</div>
		<div style="margin: 10px 0;">
		Bitte geben Sie einen Suchbegriff ein.</div>
		<input class="such_box" type="text" id="eingabe" name="eingabe" size="40" />
		<input id="absenden" type="submit" value="Absenden" />
		</form>
		<div id="namenListe">
		<div id="ergebnisliste">
		';
		$userName = $GLOBALS['TSFE']->fe_user->user['username'];
		$out .= $this->ajaxContentFormRenderListData($app,$daten['data']);
		$out .= '</div>
		</div>
		</div>
		';
		$out .= $this->getJs($app,$data['pid']);
		return $out;
	}
	
	public function ajaxContentFormGetList($app,$data) {
		$daten = $this->ajaxContentFormGetListData($app,$data);
		$out = $this->ajaxContentFormRenderListData($app,$daten['data']);
		print($out);
		return TRUE;
	}
	
	public function ajaxContentFormGetListData($app,$data) {
		$eingabe = $data['eingabe'];
		$buchstabe = $data['buchstabe'];
		$trenner = $data['trenner'];
		$username = $data['username'];
		switch ($app) {
		case 'hochschuleABisZ':
			return $this->hochschuleABisZSucheGetListData($eingabe,$buchstabe,$trenner,$username);
		case 'abfallABisZ':
			return $this->abfallABisZSucheGetListData($eingabe,$buchstabe,$trenner);
		case 'edvVorzugsListe':
			return $this->edvVorzugsListeGetListData($data);
		case 'shopBueromaterialGup':
			return $this->bueromaterialGupGetListData($data);
		}
	}
	
	public function ajaxContentFormRenderListData($app,$data) {
		switch ($app) {
		case 'hochschuleABisZ':
			return $this->hochschuleABisZSucheRenderListData($data);
		case 'abfallABisZ':
			return $this->abfallABisZSucheRenderListData($data);
		case 'edvVorzugsListe':
			return $this->edvVorzugsListeRenderListData($data);
		case 'shopBueromaterialGup':
			return $this->bueromaterialGupRenderListData($data);
		}
	}
	
	public function getFilter($app,$data) {
		switch ($app) {
			case 'hochschuleABisZ':
				return '';
			case 'abfallABisZ':
				return '';
			case 'edvVorzugsListe':
				return $this->getFilterEdvVorzugsListe($data['pid']);
			case 'hersteller':
			case 'lieferanten':
				return $this->getFilterHerstellerLieferanten($data['pid']);
			case 'shopBueromaterialGup':
				return $this->getFilterShopBueromaterialGup($data['pid']);
			}
	}
	
	public function raumSuche($eingabe) {
		$soap = new tx_he_tools_lsf();
		print_r($soap->raeumeListe($eingabe));
	}

	public static function getJsAnimationCode() {
		return '<script type="text/javascript">
	function processingAnimation(mode,message) {
		  var aHeight = $(window).height();
		  var aWidth = $(window).width(); 
		
		  if ($("#spinOverlay")) {
		  	$("#spinOverlay").remove();
		  }
		  if ($("#spinOverlayMessage")) {
		  	$("#spinOverlayMessage").remove();
		  }
		  if (mode=="start") {
				$("body").append("<div id=\'spinOverlay\'></div>");
			  $("#spinOverlay").css("height", aHeight).css("width", aWidth);	
				if (message) {
					$("#spinOverlay").append("<div id=\'spinOverlayMessage\'>" + message + "</div>");
					var left = Math.ceil((aWidth - $("#spinOverlayMessage").width()) / 2);
					var top = Math.ceil((aHeight - $("#spinOverlayMessage").height()) / 2)+30;
				  $("#spinOverlayMessage").css("left", left).css("top", top);	
				}
		  }
		}
	</script>
		';
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_portal/gadgets/class.tx_he_tools_lib_db_suche.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_portal/gadgets/class.tx_he_tools_lib_db_suche.php']);
}
?>