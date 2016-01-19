<?php 
require_once(t3lib_extMgm::extPath('he_portal').'lib/class.tx_he_portal_lib_userconfig.php');
require_once(t3lib_extMgm::extPath('he_portal').'lib/class.tx_he_portal_lib_gadgets.php');
require_once(t3lib_extMgm::extPath('he_personen').'lib/class.tx_he_personen_util.php');

class tx_he_tools_portal {
var $post;
var $get;

	public function main2($parent,$pageId) {
		$importSession = tx_he_personen_util::sessionFetch('import_daten');
		$importIndex = tx_he_personen_util::sessionFetch('import_index');
		$importDb = tx_he_personen_util::sessionFetch('import_db');
		if ($importIndex<$importSession[anzDaten]) {
			$erg .= tx_he_personen_util::executeBatchSkriptNeu($importSession,$importIndex,$this,$importDb);
		} else {
			$ergebnisDaten = tx_he_personen_util::sessionFetch('ergebnis_daten');
			if (count($ergebnisDaten)>0) {
				$erg .= tx_he_personen_util::printErgebnisDaten();
				$erg .= '<div style="width:80%;background: #004666;"></div>';
				tx_he_personen_util::sessionClear('ergebnis_daten');
			}
			tx_he_personen_util::sessionClear('import_daten');
			$this->post = t3lib_div::_POST();
			$erg .= '<div class="portalFunktionen">';
			$erg .= '<h1>Portal Funktionen</h1>';
			$erg .= '<form name="alias" method="post" action="">';
			$gadgetsAktivieren = $this->post['gadgetsAktivieren'];
			$gadgetAuswahl = $this->post['gadgetAuswahl'];
			if (!empty($gadgetsAktivieren) ||  !empty($gadgetAuswahl)) {
				$erg .= $this->gadgetsAktivieren($gadgetAuswahl);
			}
			$erg .= '<input type="submit" name="gadgetsAktivieren" value="Gadgets für alle aktivieren"/>';
			$erg .= '</form>';
			$erg .= '</div>';
		}			
		return $erg;
	}
	

	public function main($parent,$pageId) {
		$this->post = t3lib_div::_POST();
		$this->get = t3lib_div::_GET();
		$erg .= '<div class="portalFunktionen">';
		$erg .= '<h1>Portal Funktionen</h1>';
		$erg .= '<form name="alias" method="post" action="">';
		$gadgetsAktivieren = $this->post['gadgetsAktivieren'];
		$gadgetAuswahl = $this->post['gadgetAuswahl'];
		if (!empty($gadgetsAktivieren) ||  !empty($gadgetAuswahl)) {
			$erg .= $this->gadgetsAktivieren($gadgetAuswahl);
		} 
		$erg .= '<input type="submit" name="gadgetsAktivieren" value="Gadgets für alle aktivieren"/>';
		$erg .= '</form>';
		$erg .= '</div>';
		return $erg;
	}
		
	protected function gadgetsAktivieren($gadgetAuswahl) {
		if (!empty($gadgetAuswahl)) {
			return $this->gadgetsAktivierenAllUsers($gadgetAuswahl);
		} else {
			$out .= '<br><br><form name="gadgetsAktivieren" method="post" action="">';
			$out .= '<label for="gadgetAuswahl">Gadget auswählen:</label>';
			$out .= '<select name="gadgetAuswahl" id="gadgetAuswahl">';
			$out .= '<option>Bitte auswählen</option>';
			$where = 'deleted=0 AND hidden=0';
			$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery ('uid,titel','tx_heportal_gadgets',$where,'','titel');
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
				$out .= '<option value="' . $row['uid'] . '">' . $row['titel'] . '</option>';
			}
		}
		$out .= '</select><br><br>';
		return $out;
	}	
	
	
	protected function gadgetsAktivierenUser($gadgetAuswahl,$username) {
		$portalDaten = tx_he_portal_lib_gadgets::gadgetDatenLaden($username);
		$minDaten = 99;
		$minSpalte = 0;
		for ($spalte=0;$spalte<count($portalDaten);$spalte++) {
			for ($zeile=0;$zeile<count($portalDaten[$spalte]);$zeile++) {
				if ($portalDaten[$spalte][$zeile]['id']==$gadgetAuswahl) {
					return FALSE;
				}
			}
			if (count($portalDaten[$spalte])<$minDaten) {
				$minDaten = count($portalDaten[$spalte]);
				$minSpalte = $spalte;
			}
		}
		$portalDaten[$minSpalte][] = array('id'=>$gadgetAuswahl);
		tx_he_portal_lib_gadgets::gadgetDatenSpeichern($username, $portalDaten);
		return TRUE;
	}
	
	protected function gadgetsAktivierenAllUsers($gadgetAuswahl) {

		$anzahl = 0;
		$where = 'deleted=0 AND disable=0';
		$where = 'gadget_infos NOT LIKE "%' . $gadgetAuswahl . '%"';
		
		$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery ('username','tx_heportal_userconfig',$where,'','username','0,500');
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
			$ok = $this->gadgetsAktivierenUser($gadgetAuswahl,$row['username']);
			if ($ok) {
				$anzahl++;
			}
		}
		return $ok . 'Datensätze aktualisiert';
	}
}


?>