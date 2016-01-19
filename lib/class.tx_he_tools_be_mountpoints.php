<?php 

class tx_he_tools_be_mountpoints {
	public function temporaraerenMountPointAuswaehlen($id) {
		$userId = $GLOBALS['BE_USER']->user[ses_userid];
//		$sesId = $GLOBALS['BE_USER']->user[ses_id];
		$this->post = t3lib_div::_POST();
		$mountpointAuswahl = $this->post[mountpointAuswahl];
		$mountpointHinzufuegen = $this->post[mountpointHinzufuegen];
		$mountpointOrdnerHinzufuegen = $this->post[mountpointOrdnerHinzufuegen];
		$mountpointLoeschen = $this->post[mountpointLoeschen];
		$mountpointOrdnerLoeschen = $this->post[mountpointOrdnerLoeschen];
		$mountpointBearbeiten = $this->post[mountpointBearbeiten];
		$mountpointOrdnerBearbeiten = $this->post[mountpointOrdnerBearbeiten];
		$mountpointOrdnerSpeichern = $this->post[mountpointOrdnerSpeichern];
		$anzahlebenen = $this->post[ebenentiefe];
 		
		if (is_array($mountpointHinzufuegen)) {
			$ordnerKeys = array_keys($mountpointHinzufuegen);
			$lesezeichen = split(',',$ordnerKeys[0]);
			$this->fuegeUserLesezeichenHinzu($lesezeichen[0],$lesezeichen[1]);
		} else if (is_array($mountpointLoeschen)) {
			$loeschdatenListe = array_keys($mountpointLoeschen);
			$loeschdaten = split(',',$loeschdatenListe[0]);
			$this->loescheUserLesezeichen($loeschdaten[0],$loeschdaten[1]);
		} else if (is_array($mountpointOrdnerLoeschen)) {
			$loeschdatenListe = array_keys($mountpointOrdnerLoeschen);
			$this->loescheUserLesezeichenOrdner($loeschdatenListe[0]);
		} else if (is_array($mountpointOrdnerHinzufuegen)) {
			$ordnerKeys = array_keys($mountpointOrdnerHinzufuegen);
			$this->fuegeLesezeichenOrdnerHinzu($ordnerKeys[0]);
		} else if (is_array($mountpointOrdnerBearbeiten)) {
			$ordnerKeys = array_keys($mountpointOrdnerBearbeiten);
			$out .= $this->mountpointOrdnerBearbeiten($ordnerKeys[0]);
		} else if (is_array($mountpointOrdnerSpeichern)) {
			$ordnerKeys = array_keys($mountpointOrdnerSpeichern);
			$this->mountpointOrdnerSpeichern($ordnerKeys[0],
																			 $this->post[mountpointOrdnerName],
																			 $this->post[mountpointOrdnerSeitenId]);
		}
			
		if (is_array($mountpointAuswahl)) {
			$mountpoints = array_keys($mountpointAuswahl);
			$mountpoint = $mountpoints[0];
			$whereData = 'uid=' . $userId;
			$abfrageData = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uc','be_users',$whereData);
			$whereSesData = 'ses_userid=' . $userId . ' AND ses_id="' . $sesId . '"';
			$abfrageSesData = $GLOBALS['TYPO3_DB']->exec_SELECTquery('ses_data,ses_id','be_sessions',$whereSesData);
			if ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageData)) {
				$sessionDaten = unserialize($daten[uc]);
				if ($sessionDaten[pageTree_temporaryMountPoint]>0) {
//					$this->seitenbaumToggle($sessionDaten[pageTree_temporaryMountPoint],'close',99);
				}
				$sessionDaten[pageTree_temporaryMountPoint] = $mountpoint;
				$sesData[uc] = serialize($sessionDaten);
				$whereUpdate = 'uid=' . $userId;
				$erg = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('be_users',$whereUpdate,$sesData);
				if ($erg) {
//					$this->seitenbaumToggle($mountpoint,'open',$anzahlebenen);
					return '<script type="text/javascript">
									top.window.location.href = "/typo3/backend.php";
									</script>';
				}
			}
		}
		$ebenenListe = array(0 => 'keine' , 1 => 1, 2 => 2, 999 => 'alle');
		$out .= '<form name="Temporaraeren-MountPoints" method="post" action="">';
		$out .= '<div id="ueberschrift">
						';
		$out .= '<label for="anzahl_ebenen">Anzahl der Ebenen die ausgeklappt werden</label>
						<select id="anzahl_ebenen" name="ebenentiefe">
						';
		foreach ($ebenenListe as $wert => $label) {
			$out .= '<option value="' . $wert . '">' . $label . '</option>';
		}
		$out .= '</select>
						</div>';

		
		$mountPointListe = $this->gibUserLesezeichen();
		$neueSeite = FALSE;
		if( $id>0 && $this->neuesLesezeichen($id)) {
			$neueSeite = $id;
		}
		$out .= '<div style="padding: 10px;">';
		$out .= '<p><input type="submit" name="mountpointOrdnerHinzufuegen[' . $id . ']" value="Neuen Lesezeichenordner anlegen"/></p>' . "\n";
		$out .= '</div>';
		foreach ($mountPointListe as $listenIndex=>$daten) {
			$titel = $daten[0];
			$linkId = $daten[1];
			$eintraege = $daten[2];
			
			$out .= $this->gibTemporaraerenMountPointsAus($titel,$linkId,$eintraege,$neueSeite,$listenIndex);
		}
		$out .= '</form>';
		return $out;
	}
	
	public function neuesLesezeichen($mountPointListe,$id) {
		$neu = TRUE;
		foreach($mountPointListe as $ordnerName=>$ordner) {
			foreach ($ordner as $seitenTitel=>$seitenId) {
				if ($seitenId==$id) {
					$neu = FALSE;
				}
			}
		}
		return $neu;
	}
	
	public function loescheUserLesezeichen($ordner,$seite) {
		$mountPointListe = $this->gibUserLesezeichen();
		foreach ($mountPointListe as $index=>$daten) {
			if ($daten[0]==$ordner) {
				unset($mountPointListe[$index][2][$seite]);
				$this->speichereUserLesezeichen($mountPointListe);
				return;
			}
		}
	}
	
	public function loescheUserLesezeichenOrdner($ordner) {
		$mountPointListe = $this->gibUserLesezeichen();
		foreach ($mountPointListe as $index=>$daten) {
			if ($daten[0]==$ordner) {
				unset($mountPointListe[$index]);
				$this->speichereUserLesezeichen($mountPointListe);
				return;
			}
		}
	}
	
	public function fuegeUserLesezeichenHinzu($ordner,$seite) {
		$mountPointListe = $this->gibUserLesezeichen();
		$whereSeite = 'deleted=0 AND uid=' . $seite;
		$abfrageSeite = $GLOBALS['TYPO3_DB']->exec_SELECTquery('title','pages',$whereSeite);
		if ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageSeite)) {
			$titel = $daten[title];
			foreach ($mountPointListe as $index=>$daten) {
				if ($daten[0]==$ordner) {
					if (!is_array($mountPointListe[$index][2])) {
						$mountPointListe[$index][2] = array($titel => $seite);
					} else {
						$mountPointListe[$index][2][$titel] = $seite;
					}
					$this->speichereUserLesezeichen($mountPointListe);
					return;
				}
			}
		}
	}
	
	public function mountpointOrdnerBearbeiten($index) {
		$mountPointListe = $this->gibUserLesezeichen();
		$ordner = $mountPointListe[$index][0];
		$seitenId = $mountPointListe[$index][1];
		$out .= '<div style="padding: 10px;">';
		$out .= 'Ordnername: <input type="text" name="mountpointOrdnerName" value="' . $ordner . '"/> ' . "\n";
		$out .= 'SeitenId: <input type="text" name="mountpointOrdnerSeitenId" value="' . $seitenId . '"/><br/>' . "\n";
		$out .= '<input type="submit" name="mountpointOrdnerSpeichern[' . $index . ']" value="Änderung Speichern"/>' . "\n";
		$out .= '<input type="submit" name="abbrechen" value="Abbrechen"/>' . "\n";
		$out .= '</div>';
		return $out;
	}
	
	public function mountpointOrdnerSpeichern($ordnerId,$ordnerName,$seitenId) {
		$mountPointListe = $this->gibUserLesezeichen();
		$mountPointListe[$ordnerId][0] = $ordnerName;
		$mountPointListe[$ordnerId][1] = $seitenId;
		$this->speichereUserLesezeichen($mountPointListe);
	}
	
	public function fuegeLesezeichenOrdnerHinzu($seite) {
		$mountPointListe = $this->gibUserLesezeichen();
		$whereSeite = 'deleted=0 AND uid=' . $seite;
		$abfrageSeite = $GLOBALS['TYPO3_DB']->exec_SELECTquery('title','pages',$whereSeite);
		if ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageSeite)) {
			$titel = $daten[title];
			$mountPointListe[] = array($titel,$seite,array());
		} else {
			$mountPointListe[] = array('Neuer Ordner',0,array());
		}
		$this->speichereUserLesezeichen($mountPointListe);
	}
	
	public function speichereUserLesezeichen($mountPointListe) {
		$userId = $GLOBALS['BE_USER']->user[ses_userid];
		$whereBeUser = 'deleted=0 AND uid=' . $userId;
		$daten[tx_hetools_lesezeichen] = serialize($mountPointListe);
		$erg = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('be_users',$whereBeUser,$daten);
	}
	
	public function gibUserLesezeichen() {
		$mountPoints[mmirsch] = array(
			array (
				'Fakultäten',
				35741,
				array(
					'AN' => 35882,
					'BW' => 35881,
					'FZ' => 36008,
					'GS' => 37977,
					'GL' => 36068,
					'IT' => 36002,
					'MB' => 35824,
					'ME' => 36052,
					'SAGP' => 35754,
					'VU' => 36070,
					'WI' => 37669,
				),
			),
			array (
				'Organisation',
				87945,
				array(
					'Rektorat' => 84562,
					'Verwaltungsleitung' => 35923,
					'Senat' => 35925,
					'Hochschulrat' => 35926,
				),
			),
			array (
				'Verwaltung',
				88107,
				array(
					'Grundsatz- und Planungsabteilung' => 36793,
					'Öffentlichkeitsarbeit' => 35929,
					'Personalabteilung' => 35930,
					'Studentische Abteilung' => 35901,
					'Technische Abteilung' => 35931,
				),
			),
			array (
				'Serviceeinrichtungen',
				35936,
				array(
					'AAA' => 35937,
					'Bibliothek' => 35938,
					'Didaktik' => 37486,
					'Institut für Fremdsprachen' => 87890,
					'Hochschulsport' => 35944,
					'RZ' => 35992,
				),
			),
			array (
				'Diverses',
				0,
				array(
					'Hochschule' => 35971,
					'Aktuelles' => 33120,
					'Intranet' => 35742,
					'Hinweise und Formulare' => 36015,
					'HE-Online Portal' => 87896,
					'Subnavi -Startseite' => 33131,
					'Bestellwesen' => 38590,
				),
			),
			array (
				'Systemordner',
				0,
				array(
					'General Storage' =>    29,
					'Aktuelles' => 33124,
					'FE User' => 22882,
				),
			),
			array (
				'Testseiten',
				0,
				array(
				'Pressemitteilungen' => 85895,
				),
			),
		);

		$mountPoints[mschmid] = $mountPoints[mmirsch];
		$mountPoints[djosef] = $mountPoints[mmirsch];
		
		$mountPoints['default'] = array(
			array (
				'Fakultäten',
				35741,
				array(
					'AN' => 35882,
					'BW' => 35881,
					'FZ' => 36008,
					'GS' => 37977,
					'GL' => 36068,
					'IT' => 36002,
					'MB' => 35824,
					'ME' => 36052,
					'SAGP' => 35754,
					'VU' => 36070,
					'WI' => 37669,
				),
			),
			array (
				'Organisation',
				87945,
				array(
					'Rektorat' => 84562,
					'Verwaltungsleitung' => 35923,
					'Senat' => 35925,
					'Hochschulrat' => 35926,
				),
			),
			array (
				'Verwaltung',
				88107,
				array(
					'Grundsatz- und Planungsabteilung' => 36793,
					'Öffentlichkeitsarbeit' => 35929,
					'Personalabteilung' => 35930,
					'Studentische Abteilung' => 35901,
					'Technische Abteilung' => 35931,
				),
			),
			array (
				'Serviceeinrichtungen',
				35936,
				array(
					'AAA' => 35937,
					'Bibliothek' => 35938,
					'Didaktik' => 37486,
					'Institut für Fremdsprachen' => 87890,
					'Hochschulsport' => 35944,
					'RZ' => 35992,
				),
			),
			array (
				'Diverses',
				0,
				array(
					'Hochschule' => 35971,
					'Aktuelles' => 33120,
					'Intranet' => 35742,
					'Hinweise und Formulare' => 36015,
					'HE-Online Portal' => 87896,
					'Subnavi -Startseite' => 33131,
					'Bestellwesen' => 38590,
				),
			),
		);

		$userLesezeichen = '';
		$userId = $GLOBALS['BE_USER']->user[ses_userid];
		$whereBeUser = 'deleted=0 AND uid=' . $userId;
		$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('tx_hetools_lesezeichen','be_users',$whereBeUser);
		if ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
			$userLesezeichen = $daten[tx_hetools_lesezeichen];
		}
		
		if (!empty($userLesezeichen)) {
			$mountPointListe = unserialize($userLesezeichen);
			if (count($mountPointListe[0])!=3) {
				$username = $GLOBALS['BE_USER']->user[username];
				if (isset($mountPoints[$username])) {
					$mountPointListe = $mountPoints[$username];			
				} else {
					$mountPointListe = $mountPoints['default'];			
				}	
			}
		} else {
			$username = $GLOBALS['BE_USER']->user[username];
			if (isset($mountPoints[$username])) {
				$mountPointListe = $mountPoints[$username];			
			} else {
				$mountPointListe = $mountPoints['default'];			
			}	
		}
		return $mountPointListe;
	}
	
	public function seitenbaumToggle($uid,$modus,$anzahlebenen) {
		$userId = $GLOBALS['BE_USER']->user[ses_userid];
		$whereBeUser = 'deleted=0 AND uid=' . $userId;
		$abfrageBeUser = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uc','be_users',$whereBeUser);
		if ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageBeUser)) {
			$uc = unserialize($daten[uc]);
			$ucBrowsePages = unserialize($uc[browseTrees][browsePages]);
			$seiten = array_keys($ucBrowsePages[0]);
			if ($modus=='open') {
				if (!in_array($uid,$seiten)) {
					$ucBrowsePages[0][$uid] = 1;
					$uc[browseTrees][browsePages] = serialize($ucBrowsePages);
					$daten[uc] = serialize($uc);
					$erg = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('be_users',$whereBeUser,$daten);
					if (!$erg) {
						die ('Fehlern beim Speichern der Zuzuklappenden Seite, userid: ' . $userId);
					}
				}
			} else {
				if (in_array($uid,$seiten)) {
					unset($ucBrowsePages[0][$uid]);
					$uc[browseTrees][browsePages] = serialize($ucBrowsePages);
					$daten[uc] = serialize($uc);
					$erg = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('be_users',$whereBeUser,$daten);
					if (!$erg) {
						die ('Fehlern beim Speichern der Aufzuklappenden Seite, userid: ' . $userId);
					}
				}
			}
		}
		if ($anzahlebenen>0) {
/*
 * Kinderseiten abfragen
 */
			$wherePage = 'deleted=0 AND pid=' . $uid;
			$abfragePage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid','pages',$wherePage);
			while ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfragePage)) {
				$this->seitenbaumToggle($daten[uid],$modus,$anzahlebenen-1);
			}
		}
		return $out;
	}
	
	public function gibTemporaraerenMountPointsAus($titel,$linkId,$daten,$neueSeite,$listenIndex) {
		$out .= '<div class="filemount-block">';
		if ($linkId!=0) {
			$ausgabeTitel = '<input id="' . $linkId . '"type="submit" name="mountpointAuswahl[' . $linkId . ']" value="' . $titel . '"/>' . "\n";
		} else {
			$ausgabeTitel = $titel;
		}
		$ausgabeTitel .= '<input class="icon" src="/typo3/sysext/t3skin/icons/gfx/garbage.gif" type="image" name="mountpointOrdnerLoeschen[' . $titel . ']" title="den gesamten Lesezeichen-Ordner löschen" />' . "\n";
		$ausgabeTitel .= '<input class="icon" src="/typo3/sysext/t3skin/icons/gfx/edit.gif" type="image" name="mountpointOrdnerBearbeiten[' . $listenIndex . ']" title="Eintrag bearbeiten" />' . "\n";
		if ($neueSeite) {
			$ausgabeTitel .= '<input src="/typo3conf/ext/he_tools/static/icon_plus.gif" type="image" name="mountpointHinzufuegen[' . $titel . ',' . $neueSeite . ']" title="ausgewählte Seite als Lesezeichen hinzufügen" />' . "\n";
		}
		$out .= '<h2>' . $ausgabeTitel . '</h2>';
		foreach($daten as $name=>$uid) {
			$out .= '<div class="button_zeile">';
			$out .= '<input id="' . $uid . '"type="submit" name="mountpointAuswahl[' . $uid . ']" value="' . $name . '"/>' . "\n";
			$out .= '<input class="icon" src="/typo3/sysext/t3skin/icons/gfx/garbage.gif" type="image" name="mountpointLoeschen[' . $titel . ',' . $name . ']" title="Lesezeichen löschen" />' . "\n";
			$out .= '</div>';
		}
		$out .= '</div>';
		return $out;
	}
					
	
}

?>