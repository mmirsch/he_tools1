<?php
require_once(t3lib_extMgm::extPath('he_portal') . 'lib/class.tx_he_portal_lib_gadgets.php');

class tx_he_tools_a_bis_z	{

var $extkey;
var $parent;
var $buchstabenListe = array('A','B','C','D','E','F','G',
														 'H','I','J','K','L','M','N',
														 'O','P','Q','R','S','T','U',
														 'V','W','X','Y','Z');
	public function tx_he_tools_a_bis_z($extkey) {
		$this->extKey = $extkey;
	}

	public function render($parent) {

		$GLOBALS ['TSFE']->additionalHeaderData [$this->extKey . 'he_tools_css_1'] = '<link href="' . t3lib_extMgm::siteRelPath($this->extKey) . 'res/hochschule_a_z.css" rel="stylesheet" type="text/css" />';
		$zeitschriftenListe = array();
		$this->gibArtikel($zeitschriftenListe);
		$out .= '<a name="top"></name>';
		$out .= $this->renderBuchstabenLinks($zeitschriftenListe);
		
		$out = '';
		$username = $GLOBALS['TSFE']->fe_user->user['username'];
		$gadgetId = tx_he_portal_lib_gadgets::gibGadgetId($GLOBALS['TSFE']->id);
		
		if (tx_he_portal_lib_gadgets::gadgetHilfeTextAnzeigen($gadgetId, $username)) {
			$hilfeId = tx_he_portal_lib_gadgets::gibGadgetHilfeUid($gadgetId);
			$config = array('tables' => 'tt_content','source' => $hilfeId);
			$out .= $parent->cObj->RECORDS($config);
		}
		
		$out .= '
	<div class="a_bis_z_suche">
		<div style="margin: 10px 0;">
		Bitte geben Sie einen Suchbegriff ein.</div>
	<form action="" method="POST">
		<input class="such_box" type="text" id="eingabe" name="eingabe" size="40" />
		<input id="absenden" type="submit" value="Absenden" />
	</form>
	<div id="namenListe" style="margin-top:2em; font-family:Arial">
	</div>
	
	</div>
		
		<script>
		$("#absenden").remove();
		$("#eingabe").keyup(function(event) {
			var eingabe = encodeURI($("#eingabe").val());
			if (eingabe.length>0) {
				$("#ergebnisliste").detach();
				$("<div id=\"ergebnisliste\"></div>").appendTo($("#namenListe"));
				$("#ergebnisliste").load("index.php?eID=he_tools&action=a_bis_z_suche&val=" + eingabe);
			} else {
				$("#ergebnisliste").detach();
			}
			
		});
		</script>
		';
		return $out;
	}
	
	public static function suchergebnis($eingabe) {
		$out = '';
		$where = 'deleted=0 AND hidden=0 AND 
							pid=92967 AND
							(produktname LIKE "%' . trim($eingabe) . '%")
							';
		$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery('produktname,link','tx_hebest_artikel', $where,'','produktname');			
		$tabContent = '';
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
			$tabContent .= '<span class="eintrag"><span class="linkwrap">
							 <a target="_blank" href="http://www.hs-esslingen.de/index.php?id=' . $row['link'] . '">' . 
										$row['produktname'] . 
								'</a></span></span>';
		}
		if (empty($tabContent)) {
			$out = '<h3>Es wurde kein Ergebnis für die Eingabe "' . $eingabe . '" gefunden !</h3>';
		} else {
			$out = '<div class="hochschule_a_z">' .
						 $tabContent .
						 '</div>';
		}
		print($out);
		return TRUE;
	}
	
	
	public function gibBuchstabeZeitschriften($anfangsBuchstabe,&$liste) {
		$where = 'deleted=0 AND hidden=0 AND sortiertitel LIKE "' . $anfangsBuchstabe . '%"';
		$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_he_zeitschriftenliste',$where,'','sortiertitel');
		while ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
			$liste[] = $daten;
		}
	}

	public function gibArtikel(&$zeitschriftenListe) {
		foreach ($this->buchstabenListe as $anfangsBuchstabe) {
			$liste = array();
			$this->gibBuchstabeZeitschriften($anfangsBuchstabe,$liste);
			$zeitschriftenListe[$anfangsBuchstabe] = $liste;
		}
	}
	
	public function renderBuchstabenBlock($buchstabe,$liste) {
		if (count($liste)>0) {
			$out .= '<tr class="hg_dunkelblau">
			<th class="zeitschriften_titel">
				<a name="' . $buchstabe . '"></a>' . $buchstabe . ' - Zeitschriften
				<a href="#top" title="zum Seitenanfang"><img src="fileadmin/images/layout/pfeil-oben.png" alt="zum Seitenanfang"/></a>
			</th>
			<th class="zeitschriften_signatur">Signatur</th>
			<th class="zeitschriften_bestandsnachweis">Bestandsnachweis
				<a href="#top" title="zum Seitenanfang"><img src="fileadmin/images/layout/pfeil-oben.png" alt="zum Seitenanfang"/></a>
			</th>
		</tr>
		';
			$zeileGerade = false;
			foreach ($liste as $zeitschrift) {
				if ($zeileGerade) {
					$bg = '';
					$zeileGerade = FALSE;
				} else {
					$bg = ' class="hg_hellblau"';
					$zeileGerade = TRUE;
				}
				$out .= '<tr' . $bg . '>';
				$out .= '<td>' . $zeitschrift[titel] . '</td>';
				$out .= '<td>' . $zeitschrift[signatur] . '</td>';
				$out .= '<td>' . $zeitschrift[bestandsnachweis] . '</td>';
				$out .= '</tr>' . "\n";
			}
		}
		return $out;
	}
	
	protected function renderBuchstabenLinks(&$zeitschriftenListe) {
		$out .= '<div class="buchstaben_links">';
		foreach ($this->buchstabenListe as $anfangsBuchstabe) {
			if (count($zeitschriftenListe[$anfangsBuchstabe])>0) {
				$out .= '<a href="#'. $anfangsBuchstabe . '" title="zum Buchstaben \''. $anfangsBuchstabe . '\' springen">'. $anfangsBuchstabe . '</a>';
			}
		}
		$out .= '</div>';
		return $out;
	}
	
}
?>