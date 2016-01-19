<?php 

class tx_he_tools_datenimport {
	
	public function importRzDaten($modus) {
		switch ($modus) {
			case 'MATERIAL':
				$this->importMaterial();
				break;
			case 'SOFTWARE':
				$this->importSoftware();
				break;
		}
	}
	
	public function importMaterial() {
		/*
		 Die importierten Daten haben folgende Spalten:
		[0] => Kategorie
		[1] => Hersteller
		[2] => Artikelnummer
		[3] => Produktname
		[4] => Hersteller-Bezeichnung
		[5] => Preis
		*/
		$importPfad = "http://www2.hs-esslingen.de/work/projekt-RZ/fh-intern/DV-Materialverbrauch/Material-Liste-fuer-Typo3.csv";
		$headers = get_headers($importPfad);
		$lastModified = str_ireplace('Last-modified:','',$headers[3]);
		$timestamp = strtotime($lastModified);
		$pid = 90049;
		$where = 'tstamp<' . $timestamp . ' AND deleted=0 AND pid=' . $pid;
		//		$where = 'deleted=0 AND pid=' . $pid;
		$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('tstamp,uid','tx_hebest_artikel',$where);
	
		if ($GLOBALS['TYPO3_DB']->sql_num_rows($abfrage)>0) {
			$handle = fopen ($importPfad, "r");
			if ($handle) {
				while ( ($data = fgetcsv ($handle, 1000, ";")) !== FALSE ) { // Daten werden aus der Datei
					$daten[] = $data;
				}
				fclose ($handle);
				$aktuelleZeit = time();
				$importierteArtikel = array();
				foreach ($daten as $zeile) {
					$artikel[hauptkategorie] = $this->gibKategorieId(iconv("ISO-8859-1", "UTF-8", $zeile[0]),$pid);
					$hersteller = str_ireplace('Sonstiges','Sonstige',$zeile[1]);
					$artikel[hersteller] = $this->gibHerstellerId(iconv("ISO-8859-1", "UTF-8", $hersteller),$pid);
					$artikel[artikelnummer] = $zeile[2];
					$artikel[produktname] = iconv("ISO-8859-1", "UTF-8", $zeile[3]);
					$artikel[hersteller_bezeichnung] = iconv("ISO-8859-1", "UTF-8", $zeile[4]);
					$preis = str_ireplace(',','.',$zeile[5]);
					$preis = sscanf($preis,"%f");
					$artikel[preis] = number_format($preis[0], 2, '.', '');
					$artikel[tstamp] = $aktuelleZeit;
					$artikel[pid] = $pid;
					$where = 'artikelnummer="' . $artikel[artikelnummer] . '" AND deleted=0 AND pid=' . $pid;
					$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid','tx_hebest_artikel',$where);
					if ($vorhanden = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
						$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_hebest_artikel','uid=' . $vorhanden[uid], $artikel);
					} else {
						$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_hebest_artikel',$artikel);
					}
					$importierteArtikel[] = $artikel[artikelnummer];
				}
				$where = 'deleted=0 AND pid=' . $pid;
				$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,artikelnummer','tx_hebest_artikel',$where);
				$geloescht[deleted] = 1;
				while ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
					if (!in_array($daten[artikelnummer],$importierteArtikel)) {
						$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_hebest_artikel','uid=' . $daten[uid], $geloescht);
//						$ausgabe .= '<br>' . $daten[artikelnummer];
					}
				}
			}
		}
		return TRUE;
	}

	public function importMensaSpeisaplan() {
		$importPfad = "http://www.studierendenwerk-stuttgart.de/speiseangebot.csv";
		$handle = fopen ($importPfad, "r");
		if ($handle) {
			while ( ($data = fgetcsv ($handle, 2000, ',')) !== FALSE ) {
				$daten[] = $data;
			}
			fclose ($handle);
      $dbDaten['tstamp'] = time();
			$dbDaten['pid'] = 129003;
			if (count($daten)>=7) {
				$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_hetools_mensa','TRUE');
				// Titelzeilen importieren
				$dbDaten['datum'] = 0;
				$dbDaten['tagesplan'] = serialize($daten[0]);
				$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_hetools_mensa',$dbDaten);
				for ($i=1;$i<count($daten);$i++) {
					$datum = $this->gibTimestamp($daten[$i][0]);
					$daten[$i]['w'] = date("w", $datum);
					$daten[$i]['d'] = date("d.m.", $datum);
					$dbDaten['datum'] = $datum; //floor($datum/3600/24)*3600*24;
					$dbDaten['tagesplan'] = serialize($daten[$i]);
					$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_hetools_mensa',$dbDaten);
				}
			}
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	public function gibTimestamp($datum) {
		$datum = str_replace('.','',$datum);
		$datumsTeile = explode(' ',$datum);
		switch($datumsTeile[1]){
			case 'Januar':
				$monat = 1;
				break;
			case 'Februar':
				$monat = 2;
				break;
			case 'März':
				$monat = 3;
				break;
			case 'April':
				$monat = 4;
				break;
			case 'Mai':
				$monat = 5;
				break;
			case 'Juni':
				$monat = 6;
				break;
			case 'Juli':
				$monat = 7;
				break;
			case 'August':
				$monat = 8;
				break;
			case 'September':
				$monat = 9;
				break;
			case 'Oktober':
				$monat = 10;
				break;
			case 'November':
				$monat = 11;
				break;
			case 'Dezember':
				$monat = 12;
				break;
		}
		$tag = intval($datumsTeile[0]);
		$jahr = intval($datumsTeile[2]);
		return gmmktime(0, 0, 0, $monat, $tag, $jahr);
	}
	
	public function gibHerstellerId($hersteller,$pid) {
		$where = 'deleted=0 AND pid=' . $pid . ' AND title="' . $hersteller . '"';
		$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid','tx_hebest_hersteller',$where);
		if ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
			return $daten['uid'];
		}
	}
	
	public function gibKategorieId($kategorie,$pid) {
		$where = 'deleted=0 AND pid=' . $pid . ' AND title LIKE "%' . $kategorie . '%"';
		$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid','tx_hebest_hauptkategorie',$where);
		if ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
			return $daten['uid'];
		}
	}
	
}

?>