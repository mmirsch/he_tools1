<?php

class tx_he_tools_suchergebnisse	{

var $extkey;
	public function tx_he_tools_suchergebnisse($extkey) {
		$this->extKey = $extkey;
	}

	public function suchergebnisse($mode,$eingabe) {
		switch ($mode) {
			case 'AZ':
				return $this->suchergebnisseAZ($eingabe);
			case 'PERS':
				return $this->suchergebnissePERS($eingabe);
			default:
				return 'Modus "' . $mode . '" Nicht implementiert';
		}
	}

	public function suchergebnisseAZ($eingabe) {
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
		return $out;
	}
	
	public function gibTelefonnummern($telefonNummern,$international=FALSE) {
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
	
	public function suchergebnissePERS($eingabe) {
		$out = '';
		$where = 'deleted=0 AND disable=0 AND 
							NOT FIND_IN_SET(71,usergroup)>0 AND
							pid=22881 AND
							(last_name LIKE "' . trim($eingabe) . '%" OR
							 username LIKE "%' . trim($eingabe) . '%")
							';
		$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery('DISTINCT first_name,last_name,email,tx_hepersonen_raumnummer,tx_hepersonen_profilseite,telephone','fe_users', $where,'','last_name,first_name');			
		$bg = 'hg_hellblau';
		$tabContent = '';
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
			$tel = $this->gibTelefonnummern($row['telephone']);
			if (empty($tel)) {
				$tel = '-';
			}
			$raum = $row['tx_hepersonen_raumnummer'];
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
		if (empty($tabContent)) {
			$out = '<h3>Es wurde keine Person für die Eingabe "' . $eingabe . '" gefunden !</h3>';
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
	
}
?>