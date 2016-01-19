<?php

class tx_he_tools_zeitschriftenlisten	{
var $extkey;
var $buchstabenListe = array('A','B','C','D','E','F','G',
														 'H','I','J','K','L','M','N',
														 'O','P','Q','R','S','T','U',
														 'V','W','X','Y','Z');

	public function tx_he_tools_zeitschriftenlisten($extkey) {
		$this->extKey = $extkey;
	}

	public function renderZeitschriftenListe() {
		$GLOBALS ['TSFE']->additionalHeaderData [$this->extKey . 'he_tools_css_1'] = '<link href="' . t3lib_extMgm::siteRelPath($this->extKey ) . 'res/zeitschriften.css" rel="stylesheet" type="text/css" />';
		$zeitschriftenListe = array();
		$this->gibZeitschriftenListe($zeitschriftenListe);
		$out .= '<a name="top"></name>';
		$out .= $this->renderBuchstabenLinks($zeitschriftenListe);
		$out .= '<table class="tab100 zeitschriften">
		<tbody>
		';
		foreach($zeitschriftenListe as $buchstabe=>$liste) {
			$out .= $this->renderBuchstabenBlock($buchstabe,$liste);
		}
		$out .= '</table>';
		return $out;
	}
	
	public function gibBuchstabeZeitschriften($anfangsBuchstabe,&$liste) {
		$where = 'deleted=0 AND hidden=0 AND sortiertitel LIKE "' . $anfangsBuchstabe . '%"';
		$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_he_zeitschriftenliste',$where,'','sortiertitel');
		while ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
			$liste[] = $daten;
		}
	}

	public function gibZeitschriftenListe(&$zeitschriftenListe) {
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
