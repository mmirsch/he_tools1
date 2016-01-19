<?php

require_once(t3lib_extMgm::extPath('he_tools').'res/blowfish/blowfish.class.php');

define(IP_TEST,false);
define("LATIN1_UC_CHARS", "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝ");
define("LATIN1_LC_CHARS", "àáâãäåæçèéêëìíîïðñòóôõöøùúûüý");
define("DAM_MEDIA_PID", 57429);
define("HE_UTIL_SESSION",'tx_heUtilSessionData_');


class tx_he_tools_util	{
	
	public static $bewerbungsZeitraeume = array(
			'ss' => array('15.04','15.07'),
			'ws' => array('15.10','15.01'),
	);
	
	public static function gibfakultaetenListe() {
		return array(
		'AN' => 'Angewandte Naturwissenschaften',
		'BW' => 'Betriebswirtschaft',
		'FZ' => 'Fahrzeugtechnik',
		'GS' => 'Graduate School',
		'GL' => 'Grundlagen',
		'IT' => 'Informationstechnik',
		'MB' => 'Maschinenbau',
		'ME' => 'Mechatronik und Elektrotechnik',
		'SP' => 'Soziale Arbeit, Gesundheit und Pflege',
		'GU' => 'Gebäude Energie Umwelt',
		'WI' => 'Wirtschaftsingenieurwesen'
		);
	}
	
	public static function bewerbungOnlineMoeglich() {
		if (!empty($GLOBALS['TSFE']->tmpl->setup['lib.']['globals_r.']['20.']['10.']['value'])) {
			$bewerbungMoeglich = TRUE;
		} else {
			$bewerbungMoeglich = FALSE;
		}
		return $bewerbungMoeglich;
	}
	
	public static function erzeugeEmailLink($email,&$cobj) {
		$EmailAnzeige = $email;
		$EmailLinkConf = array('parameter' => $email , 'ATagParams' => 'class="mail"');
		return $cobj->typolink($EmailAnzeige,$EmailLinkConf);
	}

	public static function gib_bildpfad($username) {
		$username = $username;
		$pfad = 'fileadmin/medien/mitarbeiter/'.$username;
		return $pfad;
	}

	public static function splitDateipfad($url,&$dateiPfad,&$dateiname) {
		$splitPos = strrpos($url,'/') + 1;
		$dateiPfad = substr($url,0,$splitPos);
		$dateiname = substr($url,$splitPos);
	}
	
	public static function bereinigeDateinamen($dateiname) {
		return rawurlencode(iconv("UTF-8", "ISO-8859-1", $dateiname));
//		return str_replace(' ','%20',$dateiname);
	}

	public static function kopiereExterneDatei($dateiNameTypo3,$quelle,$zielPfad,$dateiInfo,$zeit){
		$documentRoot = t3lib_div::getIndpEnv(TYPO3_DOCUMENT_ROOT);
		$zielPfadKomplett = $documentRoot . '/' . $zielPfad;
		if (file_exists($zielPfadKomplett)) {
			$fileTimeZieldatei = filemtime($zielPfadKomplett);
			if ($fileTimeZieldatei==$zeit) {
// Das Bild ist bereits vorhanden
				return true;
			}
		}
		if (!copy($quelle, $zielPfadKomplett)) {
			t3lib_div::devLog('Fehler beim Hochladen des Bildes "' . $quelle . '" nach:' . $zielPfadKomplett, 'he_personen', 0);	
			return false;				
		} else {
			touch($zielPfadKomplett,$zeit);
			$i = 0;
			$splitPos = strrpos($dateiNameTypo3,'.');
			$dateiStart = substr($dateiNameTypo3,0,$splitPos);
			$dateiEndung = substr($dateiNameTypo3,$splitPos);
			$dateiname = $dateiNameTypo3;
			$zielPfadKomplett = $documentRoot . '/uploads/pics/' . $dateiname;
			while (file_exists($zielPfadKomplett)) {
				$i++;
				$dateiname = sprintf('%s_%02d',$dateiStart,$i) . $dateiEndung;
				$zielPfadKomplett = $documentRoot . '/uploads/pics/' . $dateiname;
			}
			if (!copy($quelle, $zielPfadKomplett)) {
				t3lib_div::devLog('Fehler beim Hochladen des Bildes "' . $quelle . '" nach:' . $zielPfadKomplett, 'he_personen', 0);	
				return false;		
			}	else {
				$ergebnis[filename] = $dateiname;
			}	
			$ergebnis[damId] = self::indiziereDatei($zielPfad,$dateiInfo);
			return $ergebnis;
		}
	}

	public static function bestimmeDateiTimestamp($pfad) {
		$headers = get_headers($pfad); 
		$lastModified = str_ireplace('Last-modified:','',$headers[3]);
		$timestamp = strtotime($lastModified);
		return $timestamp;
	}
	
	public static function bestimmeTimestamp($zeitstring) {
		$datZeit = explode(' ',$zeitstring);
		if (count($datZeit)==2) {
			$datum = explode('-',$datZeit[0]);
			$zeit = explode(':',$datZeit[1]);
			if (count($datum)==3 && count($zeit)==3) {
				$erg = mktime($zeit[0], $zeit[1], $zeit[2], $datum[1], $datum[2], $datum[0]);
			} else {
				$erg = 0;
			}
		} else {
			$erg = 0;
		}
		return $erg;
	}

	public static function get_real_ip() {
		if ( getenv( 'HTTP_X_FORWARDED_FOR' ) ) { // Kommt von einem Proxy
			$ipString = @getenv('HTTP_X_FORWARDED_FOR');
			$addr = explode(",",$ipString); // falls mehrere IPs, die letzte nehmen
			return trim ( $addr[sizeof($addr)-1] ); 
		} elseif ( getenv( 'HTTP_CLIENT_IP' ) ) {
			return $_SERVER['HTTP_CLIENT_IP']; // eventuell ist dieser Header gesetzt vom letzten Proxy
		} elseif ( getenv( 'REMOTE_ADDR' ) ) {
			return $_SERVER['REMOTE_ADDR']; // kein Proxy, dann halt direkt
		}
		return "0.0.0.0"; // wenn garnichts gefunden wird, wenigtens eine korrekte IP ausgeben
	}
	
	public static function hochschulIP() {
		$intranet = false;
		$ip = self::get_real_ip();
		$ip_teile = explode ('.', $ip);
		if (($ip_teile[0] == 134) AND ($ip_teile[1] == 108) &&
				($ip_teile[2] >= 0) AND ($ip_teile[2] <= 127) ) {
			$intranet = true;

			// Testen des externen Zugangs ermöglichen indem $cmsvar[IP_TEST]
			// auf die eigene IP-Adresse gesetzt wird und $IP_TEST auf "true".
			$IP_MIRSCH = '134.108.64.35';
			$IP_JOSEFIAK = '134.108.64.18';
			if (($ip == $IP_JOSEFIAK  || $ip == $IP_MIRSCH) && IP_TEST) {
				$intranet = false;
			}

		}
		return $intranet;
	}
	
	public static function userEingeloggt() {
		if (!empty($GLOBALS['TSFE']->fe_user->user['username'])) {
			return true;
		} else {
			return false;
		}
	}

	public static function intranetSeite() {
		return ($GLOBALS['TSFE']->page[tx_six2t3_intranet]==1);
	}

	public static function intranetZugriff() {
		return self::userEingeloggt() || self::hochschulIP();
	}

	public static function pruefeIntranetZugriff() {
		if (self::intranetSeite() && !self::intranetZugriff()) {
			$siteURL = t3lib_div::getIndpEnv(TYPO3_SITE_URL);
			header("HTTP/1.0 404 Not Found");
			header('Location: ' . $siteURL . 'de/kein_zugriff.html');
		}
	}

	public static function indiziereDatei($dateiPfad,$dateiInfo) {
		// Falls die Extension 'dam' installiert ist, wird das Bild sofort indiziert
		if (t3lib_extMgm::isLoaded('dam')) {
			require_once(t3lib_extMgm::extPath('dam') . 'lib/class.tx_dam.php');
			require_once(t3lib_extMgm::extPath('dam') . 'lib/class.tx_dam_indexing.php');

      // initiate DAM indexing-object
      $damIndexing = t3lib_div::makeInstance('tx_dam_indexing');

			$damIndexing->init();
			$damIndexing->initEnabledRules(); 
			$damIndexing->setRunType('auto');
			$damIndexing->setDryRun(true);
			
			$dam = t3lib_div::makeInstance('tx_dam');
			$damUid = $dam->file_isIndexed($dateiPfad);
			if (!$damUid) {
				$data = $damIndexing->indexFile($dateiPfad,time(),DAM_MEDIA_PID);
				$damDaten['pid'] = $data[fields]['pid'];
				$damDaten['hidden'] = $data[fields]['hidden'];
				$damDaten['starttime'] = $data[fields]['starttime'];
				$damDaten['endtime'] = $data[fields]['endtime'];
				$damDaten['file_type_version'] = $data[fields]['file_type_version'];
				$damDaten['file_size'] = $data[fields]['file_size'];
				$damDaten['file_orig_location'] = $data[fields]['file_orig_location'];
				$damDaten['file_orig_loc_desc'] = $data[fields]['file_orig_loc_desc'];
				$damDaten['file_creator'] = $data[fields]['file_creator'];
				$damDaten['file_mime_type'] = $data[fields]['file_mime_type'];
				$damDaten['file_mime_subtype'] = $data[fields]['file_mime_subtype'];
				$damDaten['file_ctime'] = $data[fields]['file_ctime'];
				$damDaten['file_mtime'] = $data[fields]['file_mtime'];
				$damDaten['ident'] = $data[fields]['ident'];
				$damDaten['creator'] = $data[fields]['creator'];
				$damDaten['publisher'] = $data[fields]['publisher'];
				$damDaten['copyright'] = $data[fields]['copyright'];
				$damDaten['alt_text'] = $data[fields]['alt_text'];
				$damDaten['date_cr'] = $data[fields]['date_cr'];
				$damDaten['date_mod'] = $data[fields]['date_mod'];
				$damDaten['loc_desc'] = $data[fields]['loc_desc'];
				$damDaten['loc_country'] = $data[fields]['loc_country'];
				$damDaten['loc_city'] = $data[fields]['loc_city'];
				$damDaten['language'] = $data[fields]['language'];
				$damDaten['hres'] = $data[fields]['hres'];
				$damDaten['vres'] = $data[fields]['vres'];
				$damDaten['hpixels'] = $data[fields]['hpixels'];
				$damDaten['vpixels'] = $data[fields]['vpixels'];
				$damDaten['color_space'] = $data[fields]['color_space'];
				$damDaten['width'] = $data[fields]['width'];
				$damDaten['height'] = $data[fields]['height'];
				$damDaten['height_unit'] = $data[fields]['height_unit'];
				$damDaten['pages'] = $data[fields]['pages'];
				$damDaten['category'] = $data[fields]['category'];
				$damDaten['parent_id'] = $data[fields]['parent_id'];
				$damDaten['deleted'] = $data[fields]['deleted'];
				$damDaten['file_name'] = $data[fields]['file_name'];
				$damDaten['file_path'] = $data[fields]['file_path'];
				$damDaten['file_inode'] = $data[fields]['file_inode'];
				$damDaten['file_hash'] = $data[fields]['file_hash'];
				$damDaten['file_type'] = $data[fields]['file_type'];
				$damDaten['index_type'] = $data[fields]['index_type'];
				$damDaten['media_type'] = $data[fields]['media_type'];
				$damDaten['title'] = $data[fields]['title'];
				$damDaten['keywords'] = $data[fields]['keywords'];
				$damDaten['search_content'] = $data[fields]['search_content'];
				$damDaten['caption'] = $data[fields]['caption'];
				$damDaten['abstract'] = $data[fields]['abstract'];
				$damDaten['file_dl_name'] = $data[fields]['file_dl_name'];
				$damDaten['crdate'] = $data[fields]['crdate'];
				$damDaten['tstamp'] = $data[fields]['tstamp'];
				$damDaten['cruser_id'] = $data[fields]['cruser_id'];
/*
 * Spezielle Daten aus übergebenem $dateiInfo übernehmen
 */
				if (count($dateiInfo)>0) {
					foreach($dateiInfo as $key=>$value) {
						$damDaten[$key] = $value;
					}
				}
				$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_dam',$damDaten);
				$ergebnis = $GLOBALS['TYPO3_DB']->sql_insert_id();
			} else {
				$ergebnis = $damUid;
			}
			return $ergebnis;
		} else {
			return false;
		}
	}

	public static function gibFakultaeten() {
		return array_keys(self::gibfakultaetenListe());
	}

	public static function gibFakultaetsBenutzergruppen() {
		$fakultaeten = self::gibFakultaeten();
		$benutzerGruppen = array();
		foreach ($fakultaeten as $fakultaet) {
			$benutzerGruppen[] = self::gibBenutzergruppe($fakultaet);
		}
		return $benutzerGruppen;
	}

	public static function gibBenutzergruppe($name) {
		switch ($name) {
			case 'BW_WI': $name= 'WI_WI'; break;
			case 'AN_CI': $name= 'AN_CIB'; break;
			case 'AN_BT': $name= 'AN_BTB'; break;
			case 'AN_TB': $name= 'AN_TBB'; break;
			case 'FZ_FA': $name= 'FZ_A_FA'; break;
			case 'FZ_FK': $name= 'FZ_B_FK'; break;
			case 'IT_TI': $name= 'IT_TIB'; break;
			case 'IT_SW': $name= 'IT_SWB'; break;
			case 'MB_EK': $name= 'MB_EKB'; break;
			case 'MB_PO': $name= 'MB_POB'; break;
			case 'ME_FTD': $name= 'ME_FTB'; break;
			case 'ME_ATD': $name= 'ME_ATB'; break;
			case 'ME_ETD': $name= 'ME_ETB'; break;
			case 'BW_TB': $name= 'BW_TBB'; break;
			case 'SAGP': $name= 'SP'; break;
			case 'GL_MAP': $name= 'GL_IPM'; break;
			case 'GL_FMP': $name= 'GL_IPM'; break;
			case 'IT_NT':
			case 'ME_EMD':
			case 'SP_SPA': $name= 'ALT'; break;
			case 'TA': $name= 'TECHNABT'; break;
		}
		$benutzerGruppen = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid','fe_groups',"title='".tx_he_tools_util::toupper($name)."' AND deleted=0");
		if ($GLOBALS['TYPO3_DB']->sql_num_rows($benutzerGruppen)==1) {
			$gruppe = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($benutzerGruppen);
			return $gruppe[uid];
		} else {
			t3lib_div::devLog('Benutzergruppe nicht gefunden: "'.$name.'"', 'he_personen', 0);
			//			$this->fehlendeGruppen[] = $name;
			return 0;
		}
	}

	public static function gibBenutzergruppenName($gruppe) {
		$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('title','fe_groups','uid='.$gruppe . ' AND deleted=0');
		if ($GLOBALS['TYPO3_DB']->sql_num_rows($abfrage)==1) {
			$name = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage);
			return $name[title];
		} else {
			return 'unbekannte Benutzergruppe';
		}
	}

	public static function gibFakultaetsName($gruppe) {
		$kuerzel = self::gibBenutzergruppenName($gruppe);
		$fakultaeten = self::gibfakultaetenListe();
		$name = $fakultaeten[$kuerzel];
		return $name;
	}
	
	public static function toupper ($str) {
		$str = strtoupper(strtr($str, LATIN1_LC_CHARS, LATIN1_UC_CHARS));
		return strtr($str, array("ß" => "SS"));
	}

	public static function utf8json($inArray) {
	
		static $depth = 0;
	
		/* our return object */
		$newArray = array();
	
		/* safety recursion limit */
		$depth ++;
		if($depth >= '30') {
			return false;
		}
	
		/* step through inArray */
		foreach($inArray as $key=>$val) {
			if ($key!='success') {
	
				if(is_array($val)) {
					/* recurse on array elements */
					$newArray[$key] = self::utf8json($val);
				} else {
					/* encode string values */
					$newArray[$key] = is_string($val) ? utf8_encode($val) : $val;
				}
	
			}else{
				$newArray[$key] = $val;
			}
		}
		/* return utf8 encoded array */
		return $newArray;
	}
	
	public static function setDeletedUnreferenzierteSeiten() {
		$deleted[deleted] = 1;
		$anzahl = 0;
		$query = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid','pages','pid NOT IN (SELECT uid FROM pages where deleted=0) AND pid<>0');
		while ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)) {
			$gespeichert = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('pages',"uid=".$daten[uid],$deleted);
			$anzahl += 1;
		}
		return $anzahl.' Seiten wurden auf deleted gesetzt';
	}

	public static function cleanCSListe($liste) {
		$feld = split(',',$liste);
		$feldUnique = array_unique($feld);
		return join(',',$feldUnique);
	}
	
	public function loescheTypo3Seitencache($pidList) {
		if (TYPO3_UseCachingFramework) {
			$pageIds = t3lib_div::trimExplode(',', $pidList);
			$pageCache = t3lib_div::makeInstance('t3lib_cache_Manager');
			try {
				$pageCache = $GLOBALS['typo3CacheManager']->getCache(
						'cache_pages'
				);
			} catch(t3lib_cache_exception_NoSuchCache $e) {
				t3lib_cache::initPageCache();
		
				$pageCache = $GLOBALS['typo3CacheManager']->getCache(
						'cache_pages'
				);
			}
			foreach ($pageIds as $pageId) {
				$pageCache->flushByTag('pageId_' . (int) $pageId);
			}
			return true;
		} else {
			return $GLOBALS['TYPO3_DB']->exec_DELETEquery('cache_pages', 'page_id IN ('.$GLOBALS['TYPO3_DB']->cleanIntList($pidList).')');
		}		
	}
	
	static function linkThisScriptStraight($params)	{
		global $SCRIPT_PATH;
		$parts = $SCRIPT_PATH;
		$pString = t3lib_div::implodeArrayForUrl('',$params);
		return $pString ? $parts . '&' . preg_replace('/^&/', '', $pString) : $parts;
	}
	
	public static function getJsRedirect($params) {
		return '<script type="text/javascript">  window.location.href = ' . 
					 'unescape("'.t3lib_div::rawUrlEncodeJS(self::linkThisScriptStraight($params)) . '"); </script>';
	}

	public static function sessionWrite($id,$data) {
		$GLOBALS['BE_USER']->setAndSaveSessionData(HE_UTIL_SESSION . $id, $data);
	}
	
	public static function sessionAppend($id,$data) {
		$sessionData = $GLOBALS['BE_USER']->getSessionData(HE_UTIL_SESSION . $id);
		$sessionData[] = $data;
		$GLOBALS['BE_USER']->setAndSaveSessionData(HE_UTIL_SESSION . $id, $sessionData);
	}
	
	public static function sessionFetch($id) {
		return $GLOBALS['BE_USER']->getSessionData(HE_UTIL_SESSION . $id);
	}

	public static function sessionClear($id) {
		$GLOBALS['BE_USER']->setAndSaveSessionData(HE_UTIL_SESSION . $id, array());
	}
	
	public static function executeBatchSkript($importDaten,$importIndex,$import) {
		self::sessionWrite('import_index',0);
		$start = $importIndex;
		$anzDurchlauf = $importDaten[anzahl];
		$anzDaten = $importDaten[anzDaten];
		if ($start+$anzDurchlauf>$anzDaten) {
			$max = $anzDaten;
			$ende = TRUE;
		} else {
			$max = $start+$anzDurchlauf;
			$ende = FALSE;
		}
		for ($i=$start;$i<$max;$i++) {
			$ergebnis = call_user_func(array($import,$importDaten[methode]),$importDaten[daten][$i]);
			if (!empty($ergebnis)) {
				self::sessionAppend('ergebnis_daten',$ergebnis);
			}
		}
		$endZeit = time();
		$verbleibendeZeit = intval(($endZeit-$importDaten[startZeit]) * ($anzDaten-$max) / $max) ;
		$minuten = intval($verbleibendeZeit/60);
		$sekunden = intval($verbleibendeZeit-$minuten*60);
		$zeitAnzeige = ', ca. ' . date("i:s", $verbleibendeZeit) . ' Minuten verbleibend';
		self::sessionWrite('import_index',$max);
		$params['ende'] = $ende;
		$params['sessionID'] = $importDaten['ID'];
		$redirect = self::getJsRedirect($params);	
		$prozent = $max*80/$anzDaten;
		$out = '<h2>' . $importDaten['titel'] . '</h2>
						 <div style="height: 20px; width:80%;background: #fff;">'.
						'<span style="height: 20px; position: absolute; display: block; background: #D5E2E7; width: ' . 
						$prozent . '%">' . '</span>' . 
						'<span style="padding: 2px; position: absolute; color: #D30334;">' .
						$max . ' von ' . $anzDaten . ' Datensätzen bearbeitet' . $zeitAnzeige .
						
						'</span>' . 
						'</div>';
		$out .= $redirect;
		return $out;
	}

	public static function initBatchSkript($daten,$methode,$forceImport,$maxProDurchlauf=50,$maxDurchlaeufe=100) {
		$skriptDaten['ID'] = uniqid('tx_heUtilImport');
		$skriptDaten[startZeit] = time();
		$skriptDaten[anzDaten] = count($daten);
		$skriptDaten[daten] = $daten;
		$skriptDaten[methode] = $methode;
		$anzDurchlaeufe = round($skriptDaten[anzDaten]/$maxDurchlaeufe);
		if ($maxProDurchlauf<$anzDurchlaeufe) {
			$skriptDaten[anzahl] = $anzDurchlaeufe;
		} else {
			$skriptDaten[anzahl] = $maxProDurchlauf;
		}

		self::sessionClear('ergebnis_daten');
		self::sessionWrite('import_daten',$skriptDaten);
		self::sessionWrite('import_index',0);
		self::sessionWrite('forceImport',$forceImport);
		
		$params['sessionID'] = $skriptDaten['ID'];
		$redirect = self::getJsRedirect($params);
		$out = 'Das Skript wird gestartet<br>';
		$out .= $redirect;
		return $out;
	}

	public static function printErgebnisDaten() {
		$ergebnisDaten = self::sessionFetch('ergebnis_daten');
return '<pre>' . print_r($ergebnisDaten,true) . '</pre>';
		return self::printErgebnisListe($ergebnisDaten,0);
	}
	
	public static function gibTypo3Alias($alias,&$typo3Id,&$externeUrl) {
		$typo3Id = FALSE;
		$externeUrl = FALSE;
		// Alias-Liste durchlaufen, um externe Aliase zu behandeln
// ggf. abschließenden Slash entfernen
		$alias2 = preg_replace('#(.*)/$#','\\1',$alias);
		$alias3 = str_replace('/en/','/de/',$alias);
		$whereAlias = 'alias="'.$alias.'"';
		$whereAlias .= ' OR alias="/'.$alias2.'"';
		$whereAlias .= ' OR alias="/'.$alias3.'"';
		$resAlias = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_six2t3_six_alias',$whereAlias);
		if ($aliasDaten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resAlias)) {
			if (is_numeric($aliasDaten[url])) {
// Alias auf eine TYPO3-Seite gefunden 
				$typo3Id = $aliasDaten[url];
			} else {
// externer Alias gefunden 
				$externeUrl = $aliasDaten[url];
			}
		}
	}
	
   public static function gibTypo3Id($url,&$typo3Id,&$externeUrl){
		$url = strtolower($url); 
		$typo3Id = FALSE;
		$externeUrl = FALSE;

		$replace = '\\1';
	  if (preg_match('!^/de/([0-9]*)!',$url)) {
			$url = intval(preg_replace('!^/de/([0-9]*)!i',$replace,$url));
		} else if (preg_match('!^/fhte/([0-9]*)!i',$url)) {
			$url = intval(preg_replace('!^/fhte/([0-9]*)!i',$replace,$url));
		} else if (preg_match('!^/en/([0-9]*)!i',$url)) {
			$url = intval(preg_replace('!^/en/([0-9]*)!i',$replace,$url));
		}
		if (preg_match('!^[0-9][0-9]*!i',$url)) {
			$typo3SeitenQuery = $GLOBALS['TYPO3_DB']->exec_SELECTquery('typo3_id','tx_six2t3_pages','id='.$url);
			if ($typo3Daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($typo3SeitenQuery)) {
				$typo3Id = $typo3Daten[typo3_id];	
			} else {
				t3lib_div::devLog("Keine TYPO3-Seite zur folgenden URL gefunden: $url", 'gibTypo3Id', 0);
			}
			return;
		}
/*
 * Externe URLs abfragen
 */  	
		if ((strpos($url,'http://www')!==FALSE || strpos($url,'https://www')!==FALSE) && 
				strpos($url,'www.hs-esslingen.de')===FALSE) {
			$externeUrl = $url;
		} else {

/*
 * interne Seite
 */  	
			$sixId = str_replace('https://www.hs-esslingen.de/de/','',$url);
			$sixId = str_replace('http://www.hs-esslingen.de/de/','',$sixId);
			$sixId = str_replace('https://www.hs-esslingen.de/en/','',$sixId);
			$sixId = str_replace('http://www.hs-esslingen.de/en/','',$sixId);
			$typo3SeitenQuery = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_six2t3_pages','id='.$sixId);
			if ($typo3Daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($typo3SeitenQuery)) {
				$typo3Id = $typo3Daten[typo3_id];	
			} else {
/* 
 * Alias prüfen
 */ 
				$url = str_replace('https://www.hs-esslingen.de/','',$url);
				$url = str_replace('http://www.hs-esslingen.de/','',$url);
				self::gibTypo3Alias($url,$typo3Id,$externeUrl);
			}
			
			if (!$typo3Id) {
/* 
 * Id in Realurl suchen
 */ 
				$where = 'spurl="' . $url . '"';	
				$realurlQuery = $GLOBALS['TYPO3_DB']->exec_SELECTquery('page_id','tx_realurl_urldecodecache',$where);
				if ($realUrl = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($realurlQuery)) {
					$typo3Id = $realUrl[page_id];
				}
			}
		}
  }
		
	static function erzeugeRootline(&$cobj,$seite, $seiteHinzufuegen=FALSE, $titelKuerzen=999) {
		$divider = ' > ';
		$lang = 'de';
		$get = t3lib_div::_GET();
		if (isset($get[L])) {
			if ($get[L]==1) {
				$lang = 'en';
			}
		}
		$excludedPages = array(1,35971);
		$excludedDoktypes = array(254);

		$mp = $get['MP'];
		$sysPageObj = t3lib_div::makeInstance('t3lib_pageSelect');
		$rootLineArray = $sysPageObj->getRootLine($seite,$mp);
//		$rootLineArray = $GLOBALS['TSFE']->sys_page->getRootLine($seite,$mp,TRUE);
		$rootLinePath = $sysPageObj->getPathFromRootline($rootLineArray);
// t3lib_div::devLog("rootLineArray: " . print_r($rootLineArray,true), 'rootline', 0);
// t3lib_div::devLog("rootLinePath: " . print_r($rootLinePath,true), 'rootline', 0);

		$rootLineArray = array_reverse($rootLineArray);
		$rlEintraege = array();
		foreach($rootLineArray as $eintrag) {
			if (! in_array($eintrag['uid'],$excludedPages) &&
					! in_array($eintrag['doktype'],$excludedDoktypes) &&
					$eintrag['hidden']==0) {
				$titel = $eintrag['title'];
				if ($lang=='en') {
					$pageOverlay = $sysPageObj->getPageOverlay($eintrag['uid'],'1');
					if (!empty($pageOverlay['title'])) {
						$titel = $pageOverlay['title'];		
					}	
				}
				if (strlen($titel)>$titelKuerzen) {
					$titel = substr($titel,0,$titelKuerzen) . '&#0133;';
				}
				if (!empty($eintrag['_MP_PARAM'])) {
					$args['MP'] = $eintrag['_MP_PARAM'];
				}
				$rlEintraege[] = $cobj->pi_linkToPage($titel,$eintrag['uid'],'',$args);
				
			}
		}
		$rootline = '<a href="http://www.hs-esslingen.de/' . $lang . '/">Home</a>';
		if (count($rlEintraege>0)) {
			$rootline .= $divider . implode($divider,$rlEintraege);
		}
		if ($seiteHinzufuegen!=FALSE) {
			$seitenDaten = self::gibSeitenInfos($seiteHinzufuegen);
			$titel = $seitenDaten['title'];
			if (strlen($titel)>$titelKuerzen) {
				$titel = substr($titel,0,$titelKuerzen) . '&#0133;';
			}
			$rootline .= $divider . $cobj->pi_linkToPage($titel,$seiteHinzufuegen,'');
		}

		return '<div id="rootline">' . $rootline . '</div>';
	}	

	static function gibSeitenInfos($seite) {
		$lang = 'de';
		$get = t3lib_div::_GET();
		if (isset($get[L])) {
			if ($get[L]==1) {
				$lang = 'en';
			}
		}
		$where = 'deleted=0 AND uid='.$seite;
		$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('pid,title,doktype,hidden','pages',$where);
		if ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
			if ($lang=='en') {
				$whereEn = 'sys_language_uid=1 AND deleted=0 AND pid='.$seite;
				$abfrageEn = $GLOBALS['TYPO3_DB']->exec_SELECTquery('title','pages_language_overlay',$whereEn);
				if ($datenEn = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageEn)) {
					$daten['title'] = $datenEn['title'];
				}
			}
		}

		return $daten;
	}	
	
	public static function inPageTree($pageId, $pageRoot, $level=999) {
		if ($level<=0) {
			return FALSE;
		}
		$inTree = FALSE;
		// Rootpage selbst prüfen		
		if ($pageId==$pageRoot) {
			$inTree = TRUE;
		} else {
			$where = 'deleted=0 AND pid=' . $pageRoot;
			$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid','pages',$where);
			
			while (!$inTree && $daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
				if ($pageId==$daten['uid']) {
					$inTree = TRUE;
				} else {
					$inTree = self::inPageTree($pageId, $daten['uid'],$level-1);
				}
			}
		}
		return $inTree;
  }

  public static function getNavPageTitle(&$pageEntry,$lang) {
    $title = '';
    if ($lang=='en') {
      $abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('nav_title,title','pages_language_overlay','pid=' . $pageEntry['uid']);
      if ($data = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
        if (!empty($data['nav_title'])) {
          $title = $data['nav_title'];
        } else {
          $title = $data['title'];
        }
      }
    } else {
      if (!empty($pageEntry['nav_title'])) {
        $title = $pageEntry['nav_title'];
      } else {
        $title = $pageEntry['title'];
      }
    }
    return $title;
  }

  public static function getChildPages($pageRoot,$lang) {
    $children = array();
    $whereMenuPage = 'deleted=0 AND hidden=0 AND nav_hide=0 AND doktype<>254';
    $whereChildPage = ' AND pid=' . $pageRoot;
    $where = $whereMenuPage . $whereChildPage;
    $abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,doktype,title,nav_title','pages',$where,'sorting');
    while ($eintrag = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
      $title = self::getNavPageTitle($eintrag,$lang);
      if (!empty($title)) {
        $children[$eintrag[uid]] = $title;
      }
    }
    return $children;
  }

	public static function getChildrenPages($pageId, &$pageList = array()) {
		$parent = 0;
		$where = 'deleted=0 AND hidden=0 AND nav_hide=0 AND doktype<>254 AND pid=' . $pageId;
		$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid','pages',$where);
		while ($eintrag = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
			$pageList[] = $eintrag[uid];
		}
	}

	public static function getParentPage($pageId) {
    $parent = 0;
    $where = 'uid=' . $pageId;
    $abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('pid','pages',$where);
    if ($eintrag = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
      $parent = $eintrag['pid'];
    }
    return $parent;
  }

  public static function getParentPages($pageId, &$pageList = array()) {
    $parent = self::getParentPage($pageId);
    if ($parent!=0) {
    	$pageList[] = $parent;
      self::getParentPages($parent, $pageList);
    }
  }
  
  public static function getMainMenuPage($pageId) {
    $parent = self::getParentPage($pageId);
    if ($parent==1) {
      return $pageId;
    } else if ($parent!=0){
      return self::getMainMenuPage($parent);
    } else {
      return 1;
    }
  }

  static public function createJpgImage($src, $width, &$height='', $quality=80) {
  	$tsfe = t3lib_div::makeInstance('tslib_fe', $GLOBALS['TYPO3_CONF_VARS'], 0, 0);
  	$tsfe->initTemplate();
  	$GLOBALS['TSFE']->tmpl = $tsfe->tmpl;
  
  	$imageSize = getimagesize($src);
  	if ($imageSize === FALSE) {
  		return NULL;
  	}
  	if (empty($height)) {
  		$height = intval($width*$imageSize[1]/$imageSize[0]);
  	}
  
  	$cObj = new tslib_cObj();
  	$conf = array(
  			'file' => $src,
  			'file.' => array(
  					'width' => $width,
  					'height' => $height,
  					'ext' => 'jpg',
  					'params' => ' -quality ' . $quality,
  
  			)
  	);

  	$imgResource = $cObj->getImgResource($conf['file'], $conf['file.']);
  	return $imgResource[3];
  
  }
  
  public static function createMainNavHe($pageId, &$cobj) {
    $pageRoot = 1;
    $lang = 'de';
    $showThirdLevelPages = array(35741,87945,87810,35959,90552);
    $getL = t3lib_div::_GP('L');
    if ($getL==1) {
       $lang = 'en';
    }
    if ($pageId==1) {
      $mainMenuPage = 35971;
    } else {
      $mainMenuPage = self::getMainMenuPage($pageId);
    }

    $out = '<a id="navigation" name="navigation"></a>' . "\n";
    /*
     * Erste Ebene
     */
    $out .= '<ul class="level1">' . "\n";
    $firstEntry = TRUE;
    $children = self::getChildPages($pageRoot,$lang);
    foreach ($children as $uid=>$title) {
      $cssClass = 'submenu';
      if ($firstEntry) {
        $cssClass .= ' first';
      }
      $conf = array('parameter' => $uid);
      if ($uid==$mainMenuPage) {
        $conf['ATagParams'] = 'class="act"';
      }
      $link =  $cobj->cObj->typolink($title,$conf);
      $out .= '<li class="' . $cssClass . '">' . $link . "\n";
      $children2 = self::getChildPages($uid,$lang);
      if (empty($children2)) {
        $out .= '</li>';
      } else {
        $out .= '<ul class="level2">' . "\n";
        foreach ($children2 as $uid2=>$title2) {
          $link =  $cobj->pi_linkToPage($title2,$uid2,'');
          $children3 = self::getChildPages($uid2,$lang);
          if (empty($children3) || !in_array($uid2,$showThirdLevelPages)) {
            $out .= '<li>' . $link . '</li>' . "\n";
          } else {
            $out .= '<li class="submenu">' . $link . "\n";
            $out .= '<ul class="level3">' . "\n";
            foreach ($children3 as $uid3=>$title3) {
              $link =  $cobj->pi_linkToPage($title3,$uid3,'');
              $out .= '<li>' . $link . '</li>';
            }
            $out .= '</ul>' . "\n";
            $out .= '</li>';
          }

          $out .= '</li>';
        }
        $out .= '</ul>' . "\n";
        $out .= '</li>';
      }
    }
    $out .= '</ul>' . "\n";
    return $out;
  }

	public static function getPageTree($pageId,&$pageList,$deleted=0,$hidden='all',$doktype='all') {
		$where = 'pid=' . $pageId;
    if ($deleted!='all') {
      $where .= ' AND deleted=' . $deleted;
    }
    if ($hidden!='all') {
      $where .= ' AND hidden=' . $hidden;
    }
    if ($doktype!='all') {
      $where .= ' AND doktype=' . $doktype;
    }
    // Rootpage selbst prüfen
		$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid','pages',$where);
		if (!in_array($pageId,$pageList)) {
			$pageList[] = $pageId;
		}
		while ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
			self::getPageTree($daten['uid'],$pageList,$deleted,$hidden,$doktype);
			if (!in_array($daten['uid'],$pageList)) {
				$pageList[] = $daten['uid'];
			}
		}
	}
/*
  public static function getPageTreeWithTitle($pageId,&$pageTitleList,$anzEbenen,$ebene=0) {
    $where = 'uid=' . $pageId;
    $abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('title','pages',$where);
    $daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage);
    $pageTitleList[$pageId] = array('title'=>$daten['title']);

    if ($anzEbenen==$ebene) {
      $childrenList = array();
      self::getPageTree($pageId,$childrenList,'0','0','1');
      $childCount = count($childrenList);
      $pageTitleList[$pageId]['childrenCount'] = $childCount;
    } else {
      $pageTitleList[$pageId]['childrenList'] = array();
      $where = 'pid=' . $pageId . ' AND deleted=0 AND hidden=0 AND doktype=1';

      $abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid','pages',$where);
      while ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
        $childrenList = array();
        self::getPageTreeWithTitle($daten['uid'],$childrenList,$anzEbenen,$ebene+1);
        $pageTitleList[$pageId]['childrenList'][$daten['uid']] = $childrenList[$daten['uid']];
      }
      $pageTitleList[$pageId]['childrenCount'] = 1;
    }
  }
*/

  public static function getPageTreeWithTitle($pageId,&$pageTitleList,$anzEbenen,$ebene=0,&$childCount) {
    $where = 'uid=' . $pageId;
    $abfrageSelf = $GLOBALS['TYPO3_DB']->exec_SELECTquery('title,doktype','pages',$where);
    $datenSelf = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageSelf);
    $pageTitleList[$pageId] = array('title'=>$datenSelf['title']);

    if ($anzEbenen==$ebene) {
      $childrenList = array();
      self::getPageTree($pageId,$childrenList,0,0,1);
      $childCount = count($childrenList);
      $pageTitleList[$pageId]['childrenCount'] = $childCount;
    } else {
      if ($datenSelf['doktype']==1) {
        $currentChildCount = 1;
      } else {
        $currentChildCount = 0;
      }
      $pageTitleList[$pageId]['childrenList'] = array();
      $where = 'pid=' . $pageId . ' AND deleted=0 AND hidden=0';

      $abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid','pages',$where);
      while ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
        $childrenList = array();
        $subChildrenCount = 0;
        self::getPageTreeWithTitle($daten['uid'],$childrenList,$anzEbenen,$ebene+1,$subChildrenCount);
        $currentChildCount += $subChildrenCount;
        $pageTitleList[$pageId]['childrenList'][$daten['uid']] = $childrenList[$daten['uid']];
      }
      $childCount = $currentChildCount;
      $pageTitleList[$pageId]['childrenCount'] = $childCount;
    }
  }

  public static function getPageSubTreeWithTitle($pageId,&$pageTitleList,$anzEbenen) {
     $childCount = 0;
    tx_he_tools_util::getPageTreeWithTitle($pageId,$pageTitleList,$anzEbenen,$childCount);
  }

  public static function popupFenster() {
	}

	public static function htaccessErzeugen() {
		$fehler = FALSE;
		$out = '';
		$titel = 'Bitte geben Sie die zugangsdaten ein';
		$users = '';
		$passwords = '';
		if (isset($GLOBALS['TSFE']->fe_user->user["username"])) {
			$username = $GLOBALS['TSFE']->fe_user->user["username"];
			$bereich = strtolower(tx_he_personen_util::gibProfilWert($username,'hb_sva'));
		} else {
			$username = '';
			$bereich = '';
		}
		if (isset($_POST['bereich'])) {
			$bereich = $_POST['bereich'];
		}
		if (isset($_POST['htaccess-anfordern'])) {				
			$out .= 'AuthName "' . $_POST['AuthName'] . '"' . "\n";
		  $out .= 'AuthUserFile ' . $_POST['AuthUserFile'] . "\n";
		  $out .= 'AuthType ' . $_POST['AuthType'] . "\n";
		  $out .= 'require ' . $_POST['require'] ;
		  $dateiname = '.htaccess';
		  header("Content-type: application/text");
		  header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		  header('Content-Disposition: attachment; filename="' . $dateiname . '"');
		  header('Pragma: no-cache');
		  print $out;
		  exit();
		} else if (isset($_POST['htpasswd-anfordern'])) {
		  $out = $_POST['postDaten'];
		  $dateiname = '.htpasswd';
		  header("Content-type: application/text");
		  header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		  header('Content-Disposition: attachment; filename="' . $dateiname . '"');
		  header('Pragma: no-cache');
		  print $out;
		  exit();
		} else if ($_POST['dateien-erzeugen']) {
			if (!empty($_POST['username']) && 
					!empty($_POST['users']) && 
		    	!empty($_POST['passwords']) && 
		    	!empty($_POST['titel']) && 
		    	!empty($_POST['bereich'])
		    	) {
			  $users = $_POST['users'];
			  $passwords = $_POST['passwords'];
			  $username = $_POST['username'];
			  $titel = $_POST['titel'];
			  $u = explode("\n", $users);
				$pw = explode("\n", $passwords);
			
				$AuthName = $titel;
				$AuthUserFile = '/home/rsns01/staff/' . $bereich . '/' . 
			                  $username . '/unix/.htpasswd';
				$AuthType = 'Basic';
				$require = 'valid-user';
				$downloadHtaccess = '<form name="getHtaccess" method="post" action="">
				  <input type="hidden" name="AuthName" value="' . $AuthName . '" />
				  <input type="hidden" name="AuthUserFile" value="' . $AuthUserFile . '" />
				  <input type="hidden" name="AuthType" value="' . $AuthType . '" />
				  <input type="hidden" name="require" value="' . $require . '" />
			    <input type="submit" name="htaccess-anfordern" value=".htaccess-Datei anfordern">
			    </form>
			      ';
			
				$out .= 'Die folgenden Zeilen bitte in die Datei .htaccess kopieren 
			        oder auf den folgenden Button klicken, um die erzeugte Datei herunterzuladen.' .
			        $downloadHtaccess . '<br>';
				$out .= 'AuthName ' . $AuthName . '<br>';
				$out .= 'AuthUserFile ' . $AuthUserFile . '<br>';
				$out .= 'AuthType ' . $AuthType . '<br>';
				$out .= 'require ' . $require . '<br>';
				$out .= '<br><hr><br>';
			
			  $postDaten = '';
			  $ausgabe = '';
				for ($i = 0; $i < count($u); $i++) {
					if (isset($u[$i]) && isset($pw[$i])) {				
						$password = crypt(trim($pw[$i]),base64_encode(CRYPT_STD_DES));
						$zeile = trim($u[$i]) . ':' . $password;
			      $postDaten .= $zeile . "\n";
						$ausgabe .= $zeile . '<br>';			
					}
				}
			
				$downloadHtpasswd = '<form name="getHtpasswd" method="post" action="">
				  <input type="hidden" name="postDaten" value="' . $postDaten . '" />
			    <input type="submit" name="htpasswd-anfordern" value=".htpasswd-Datei anfordern">
			    </form>
			      ';
				$out .= 'Diese Daten bitte in die Datei .httpasswd kopieren
			        	oder auf den folgenden Button klicken, um die erzeugte Datei herunterzuladen.<br>
			        	Bitte speichern Sie diese Datei bitte direkt in Ihrem Home-Verzeichnis (Laufwerk S).' .
			        $downloadHtpasswd . '<br>' . $ausgabe . '<br><hr><br>';
		
			} else {
				$out .= '<h3 class="rot">Bitte füllen Sie alle Felder aus!</h3>';
				$fehler = TRUE;
			}
		}
		
		$out .= '
		<form name="form1" method="post" action="">';
		if ($fehler && empty($titel)) {
			$out .= '<label class="rot" for=titel">Titel des Loginfensters:</label>';
		} else {
			$out .= '<label for=titel">Titel des Loginfensters:</label>';
		}
		$out .= '<br/><input id="titel" name="titel" size="40"  value="' . $titel . '"/>
						<br/>';
		if ($fehler && empty($username)) {
			$out .= '<label class="rot" for=username">Ihr Benutzername an der Hochschule Esslingen:</label>';
		} else {
			$out .= '<label for=username">Ihr Benutzername an der Hochschule Esslingen:</label>';
		}
		$out .= '<br/><input id="username" name="username" size="20"  value="' . $username . '"/>
						 <br/>';
		if ($fehler && empty($bereich)) {
			$out .= '<label class="rot" for=bereich">Fakultätskürzel:</label>';
		} else {
			$out .= '<label for=bereich">Fakultätskürzel:</label>';
		}
		$out .= '<br/><input id="bereich" name="bereich" size="8" value="' . $bereich . '" />
						 <br/><br/>
						 <span class="rot">Achtung! Bitte Benutzernamen und Passwörter wählen, die NICHT an der Hochschule verwendet werden
						  und notfalls geknackt werden können (z.B. welche, die auch sonst im Internet für weniger wichtige Dinge verwendet werden).</span>
						 <br/>';
		if ($fehler && empty($users)) {
			$out .= '<label class="rot" for=users">Bitte geben Sie einen Benutzernamen pro Zeile an:</label>';
		} else {
			$out .= '<label for=users">Bitte geben Sie einen Benutzernamen pro Zeile an:</label>';
		}
		$out .= '<br/><textarea id="users" name="users" rows="8" cols="60">' . $users . '</textarea>
						 <br/>';
		if ($fehler && empty($passwords)) {
			$out .= '<label class="rot" for=passwords">Bitte geben Sie für jeden Benutzernamen ein Passwort pro Zeile an:</label>';
		} else {
			$out .= '<label for=passwords">
			Bitte geben Sie für jeden Benutzernamen ein Passwort pro Zeile an<br/>
			</label>';
		}
		$out .= '<br/><textarea id="passwords" name="passwords" rows="8" cols="60">' . $passwords . '</textarea>
							<br/>
							<input type="submit" name="dateien-erzeugen" value=".htpasswd und .htaccess erzeugen">
							</form>
							';
		
		return $out;			
	}
	
	public static function renderContentElem(&$cObj,$uid) {
		$config = array('tables' => 'tt_content','source' => $uid);
		print $cObj->RECORDS($config);
		exit();		
	}
	
	public static function renderIframe($url,$hoehe='400') {
		if (strpos($url,'output=embed')===FALSE) {
			$url .= '&amp;output=embed';
		}
		$out = '<iframe style="border: none; width:100%; height: ' . $hoehe . 'px;" src="' . $url . '"></iframe>';	
		return $out;
	}

	public static function getPageTstamp($uid) {
		$result = $GLOBALS['TYPO3_DB']->sql_query('SELECT tstamp from pages WHERE uid=' . $uid);			
		if ($data = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
			return $data['tstamp'];
		} else {
			return 0;
		}
		
	}

	public static function benutzerIstInGruppe($userGroup) {
		/*
		 * Der eingeloggte Benutzer muss in mindestens einer der Benutzergruppen Mitglied sein
		*/
		$username = $GLOBALS['TSFE']->fe_user->user['username'];
		if (empty($username)) {
			return FALSE;
		}
		$mitglied = FALSE;
		$whereUser = '(deleted=0 AND disable=0 AND username="' . $username . '" AND FIND_IN_SET('. $userGroup .',usergroup)>0)';
		$resultUser = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid','fe_users', $whereUser);
		if ($rowUser = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resultUser)) {
			$mitglied = TRUE;
		}
		return $mitglied;
	}

	public static function getBeGroupsWithPageAccess($pageList,$excludeGroups='') {
		$groupList = array();
		if (empty($pageList)) {
			$where = '(deleted=0 AND hidden=0 AND db_mountpoints!="")';
			if (!empty($excludeGroups)) {
				$where .= ' AND uid NOT IN (' . $excludeGroups . ')';
			}
			$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid','be_groups', $where);
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
				$groupList[] = $row['uid'];
			}			
		} else {
			foreach ($pageList as $page) {
				$where = '(deleted=0 AND hidden=0 AND FIND_IN_SET('. $page .',db_mountpoints)>0)';
				if (!empty($excludeGroups)) {
					$where .= ' AND uid NOT IN (' . $excludeGroups . ')';
				}
				$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid','be_groups', $where);
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
					$groupList[] = $row['uid'];
				}
			}
			
		}
		return $groupList;	
	}
	
	public static function getBeUsers($userGroups, $excludeUsers='') {
		$userList = array();
		foreach ($userGroups as $userGroup) {
			$sqlQuery = 'SELECT be_users.username, fe_users.first_name,fe_users.last_name FROM be_users ' .
									'INNER JOIN fe_users ON be_users.username=fe_users.username ' .
									'WHERE be_users.deleted=0 AND be_users.disable=0 AND ' .
									'fe_users.deleted=0 AND fe_users.disable=0 AND ' .
									'FIND_IN_SET('. $userGroup .',be_users.usergroup)>0';
			if (!empty($excludeUsers)) {
				$sqlQuery .= ' AND be_users.username NOT IN (' . $excludeUsers . ')';
			}
			$resultUser = $GLOBALS['TYPO3_DB']->sql_query($sqlQuery);
			while ($rowUser = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resultUser)) {
				$userList[$rowUser['last_name'] . $rowUser['first_name']] = $rowUser['username'];
			}
		}
		ksort($userList);
		return $userList;
	}

	public static function getLastLogin($username) {
		$lastLogin = 0;
		$whereUser = 'deleted=0 AND disable=0 AND username="' . $username . '"';
		$resultUser = $GLOBALS['TYPO3_DB']->exec_SELECTquery('lastlogin', 'be_users', $whereUser);
		if ($rowUser = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resultUser)) {
			$lastLogin = $rowUser['lastlogin'];
		}
		return $lastLogin;
	}
	
	public static function getDbMounts($username) {
		$excludeList = '84005';		
		$dbMounts = array();
		$whereUser = 'deleted=0 AND disable=0 AND username="'. $username .'"';			
		$resultUser = $GLOBALS['TYPO3_DB']->exec_SELECTquery('usergroup','be_users', $whereUser);
		if ($rowUser = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resultUser)) {
			$userGroups = $rowUser['usergroup'];			
			if (!empty($userGroups)) {
				$groupList = explode(',',$userGroups);
				foreach ($groupList as $group) {
					$whereGroups = 'deleted=0 AND hidden=0 AND uid='. $group;
					$resultGroups = $GLOBALS['TYPO3_DB']->exec_SELECTquery('db_mountpoints','be_groups', $whereGroups);
					while ($rowGroups = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resultGroups)) {
						if (!empty($rowGroups['db_mountpoints'])) {
							$wherePage = 'doktype<>254 AND deleted=0 AND hidden=0 AND uid IN ('. $rowGroups['db_mountpoints'] . ') AND uid not IN (' . $excludeList . ')';
							$resultPage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,title','pages', $wherePage);
							while ($rowPage = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resultPage)) {
								$dbMounts[$rowPage['title'] . $rowPage['uid']] = array('uid'=>$rowPage['uid'], 'title'=>$rowPage['title']);
							}
						}
					}
				}
			}
			ksort($dbMounts);
		}
		return $dbMounts;	
	}
	
	public static function getFeUserData($username) {
		$userData = array();
		$whereUser = 'deleted=0 AND disable=0 AND username="'. $username .'"';			
		$resultUser = $GLOBALS['TYPO3_DB']->exec_SELECTquery('first_name,last_name,email,tx_hepersonen_profilseite','fe_users', $whereUser);
		if ($rowUser = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resultUser)) {
			$userData = $rowUser;
		}
		return $userData;	
	}
	
	public static function wandleUtfUmlaute($wort) {
    $normalizeChars = array(
      'Š'=>'S', 'š'=>'s', 'Ð'=>'D','Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'Ae',
      'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
      'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'Oe', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
      'Û'=>'U', 'Ü'=>'Ue', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'ae',
      'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
      'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'oe', 'ø'=>'o', 'ù'=>'u',
      'ú'=>'u', 'û'=>'u', 'ü'=>'ue', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f', 'ß'=>'ss', 'ğ'=>'g',
    );
    $str = strtr($wort, $normalizeChars);
    return $str;
  }

  public static function encodeString($string,$key) {
		$blowfish = t3lib_div::makeInstance('tx_hetools_lib_blowfish',$key);
		return $blowfish->Encrypt($string);
	}
	
	public static function decodeString($string,$key) {
		$blowfish = t3lib_div::makeInstance('tx_hetools_lib_blowfish',$key);
		return $blowfish->Decrypt($string);
	}
	
	public static function weiterleitung($url) {
		t3lib_utility_Http::redirect($url);
		exit();
	}
	
	public static function gibSessionWert($app, $key) {
		$sessionData = unserialize($GLOBALS['TSFE']->fe_user->getKey('ses',$app));
		if (isset($sessionData[$key])) {
			return $sessionData[$key];
		}
		return '';
	}
	
	public static function speichereSessionWert($app, $key, $wert) {
		$sessionData = unserialize($GLOBALS['TSFE']->fe_user->getKey('ses',$app));
		$sessionData[$key] = $wert;
		$sessionDataSerialized = serialize($sessionData);
		$GLOBALS['TSFE']->fe_user->setKey('ses',$app,$sessionDataSerialized);
		$GLOBALS['TSFE']->fe_user->storeSessionData();
	}
	
	public static function loescheSessionWert($app, $key='') {
		if (empty($key)) {
			$GLOBALS['TSFE']->fe_user->setKey('ses', $app, NULL);
			$GLOBALS['TSFE']->fe_user->storeSessionData();
		} else {
			self::speichereSessionWert($app,$key,NULL);
		}
	}
	
	public static function getContentElements($pageId,$orderBy='sorting') {
		$where = 'pid=' . $pageId . ' AND deleted=0 AND hidden=0';
		$query = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid','tt_content',$where,$orderBy);
		$contentElems = array();
		while ($data = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)) {
			$contentElems[] = $data['uid'];
		}
		return $contentElems;
	}

	public static function getPageTitle($pageId) {
		$where = 'uid=' . $pageId . ' AND deleted=0 AND hidden=0';
		$query = $GLOBALS['TYPO3_DB']->exec_SELECTquery('title','pages',$where);
		$pageTitle = '';
		if ($data = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)) {
			$pageTitle = $data['title'];
		}
		return $pageTitle;
	}

	public static function getHeLink($pageId, $protocol='https') {
		return $protocol . '://www.hs-esslingen.de/index.php?id=' . $pageId;
	}

	public static function getURL($url, $includeHeader = 0, $cookie = NULL) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, $includeHeader ? 1 : 0);
//    curl_setopt($ch, CURLOPT_HTTPGET, $includeHeader == 2 ? 'HEAD' : 'GET');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//    curl_setopt($ch, CURLOPT_FAILONERROR, 1);

    if ($cookie=='GET') {
      curl_setopt($ch, CURLOPT_COOKIEJAR, t3lib_extMgm::extPath('he_tools') . '/res/cookies.txt');
    }

    if ($cookie=='SET') {
      curl_setopt($ch, CURLOPT_COOKIEFILE, t3lib_extMgm::extPath('he_tools') . '/res/cookies.txt');
    }
    $content = curl_exec ($ch); // execute the curl command

    curl_close ($ch);
    unset($ch);
    return $content;
  }

  public static function downloadFile($filePath) {
    $path_parts = pathinfo($filePath);
    $file_name  = self::wandleUtfUmlaute($path_parts['basename']);
    $file_ext   = $path_parts['extension'];
    $file_path  = $_SERVER['DOCUMENT_ROOT'] . '/' . $filePath;

    // allow a file to be streamed instead of sent as an attachment
    $is_attachment = isset($_REQUEST['stream']) ? false : true;

// make sure the file exists
    if (is_file($file_path)) {
      $file_size  = filesize($file_path);
      $file = @fopen($file_path,"rb");
      if ($file) {
        // set the headers, prevent caching
        header("Pragma: public");
        header("Expires: -1");
        header("Cache-Control: public, must-revalidate, post-check=0, pre-check=0");
        header("Content-Disposition: attachment; filename=\"$file_name\"");
        // set the mime type based on extension, add yours if needed.
        $ctype_default = "application/octet-stream";
        $content_types = array(
          "exe" => "application/octet-stream",
          "zip" => "application/zip",
          "mp3" => "audio/mpeg",
          "mpg" => "video/mpeg",
          "avi" => "video/x-msvideo",
        );
        $ctype = isset($content_types[$file_ext]) ? $content_types[$file_ext] : $ctype_default;
        header("Content-Type: " . $ctype);

        //check if http_range is sent by browser (or download manager)
        if(isset($_SERVER['HTTP_RANGE'])) {
          list($size_unit, $range_orig) = explode('=', $_SERVER['HTTP_RANGE'], 2);
          if ($size_unit == 'bytes') {
            //multiple ranges could be specified at the same time, but for simplicity only serve the first range
            //http://tools.ietf.org/id/draft-ietf-http-range-retrieval-00.txt
            list($range, $extra_ranges) = explode(',', $range_orig, 2);
          } else {
            $range = '';
            header('HTTP/1.1 416 Requested Range Not Satisfiable');
            exit;
          }
        } else {
          $range = '';
        }

        //figure out download piece from range (if set)
        list($seek_start, $seek_end) = explode('-', $range, 2);

        //set start and end based on range (if set), else set defaults
        //also check for invalid ranges.
        $seek_end   = (empty($seek_end)) ? ($file_size - 1) : min(abs(intval($seek_end)),($file_size - 1));
        $seek_start = (empty($seek_start) || $seek_end < abs(intval($seek_start))) ? 0 : max(abs(intval($seek_start)),0);

        //Only send partial content header if downloading a piece of the file (IE workaround)
        if ($seek_start > 0 || $seek_end < ($file_size - 1)) {
          header('HTTP/1.1 206 Partial Content');
          header('Content-Range: bytes '.$seek_start.'-'.$seek_end.'/'.$file_size);
          header('Content-Length: '.($seek_end - $seek_start + 1));
        } else {
          header("Content-Length: $file_size");
        }
        header('Accept-Ranges: bytes');
        set_time_limit(0);
        fseek($file, $seek_start);
        while(!feof($file)) {
          print(@fread($file, 1024*8));
          ob_flush();
          flush();
          if (connection_status()!=0) {
            @fclose($file);
            exit;
          }
        }
        // file save was a success
        @fclose($file);
        exit;
      } else {
        // file couldn't be opened
        header("HTTP/1.0 500 Internal Server Error");
        exit;
      }
    } else {
      // file does not exist
      header("HTTP/1.0 404 Not Found");
      exit;
    }
  }
}
?>
