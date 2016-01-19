<?php 

class MyHandler {
	var $counter = 0;
	var $data = array();
	var	$self;
	var	$parent;
	var $conv;

	var $debug;

	function MyHandler($self,$parent){
		$this->self = $self;
		$this->parent = $parent;
		$this->conv  = new myIconv();
	}

	function getHTML($pageId=0) {
		for ($i=1;$i<=count($this->data);$i++) {
			if (isset($this->data[$i][o])) {
				$ergebnis .= "<".$this->data[$i][o];
				if (isset($this->data[$i][a])) {
					if (isset($this->data[$i][a][action])) {
						$this->data[$i][a][action] = $this->wandleUrls($this->data[$i][a][action],$target,$pageId);
					}
					if (isset($this->data[$i][a][href])) {
						$this->data[$i][a][href] = $this->wandleUrls($this->data[$i][a][href],$target,$pageId);
						if ($target!='') {
							$this->data[$i][a][target] = $target;
						}
					}
					if (isset($this->data[$i][a][onclick])) {
						$this->data[$i][a][onclick] = $this->wandleJavascript($this->data[$i][a][onclick]);
					}
					if (isset($this->data[$i][a][onkeypress])) {
						$this->data[$i][a][onkeypress] = $this->wandleJavascript($this->data[$i][a][onkeypress]);
					}
					if (isset($this->data[$i][a][src])) {
						$this->data[$i][a][src] = $this->wandleImgUrls($this->data[$i][a][src]);
					}
					foreach ($this->data[$i][a] as $key=>$value) {
						//		    			$ergebnis .= " $key=\"".htmlspecialchars($value)."\"";
						if ($key!='href' && $key!='alt' && $key!='title') {
							$iso = " $key=\"".htmlspecialchars($value)."\"";
							$utf8 = iconv("ISO-8859-1", "UTF-8", $iso);
						} else {
							$value = iconv("ISO-8859-1", "UTF-8", $value);
							$utf8 = " $key=\"".$value."\"";
						}
						$ergebnis .= $utf8; 
							
					}
				}
				if ($name=="br" || $name=="hr" || $name=="img" ||
				$name=="input" || $name=="script") {
					$ergebnis .= " />";
				} else {
					$ergebnis .= ">";
				}
			} else if (isset($this->data[$i][c])) {
				$ergebnis .= "</".$this->data[$i][c].">";
			} else if (isset($this->data[$i][d])) {
				$entities = htmlentities($this->data[$i][d], ENT_QUOTES, "Windows-1252");
				$utf8 = html_entity_decode($entities, ENT_QUOTES , "utf-8");
				$ergebnis .= $utf8 . ' ';
			}
		}
		return $ergebnis;
	}

	function getHtsearchHTML() {
		for ($i=1;$i<=count($this->data);$i++) {
			if (isset($this->data[$i][o])) {
				$ergebnis .= "<".$this->data[$i][o];
				if (isset($this->data[$i][a])) {
					if (isset($this->data[$i][a][action])) {
						$this->data[$i][a][action] = $this->wandleUrls($this->data[$i][a][action],$target);
					}
					if (isset($this->data[$i][a][href])) {
						$this->data[$i][a][href] = $this->wandleUrls($this->data[$i][a][href],$target);
						if ($target!='') {
							$this->data[$i][a][target] = $target;
						}
					}
					if (isset($this->data[$i][a][onclick])) {
						$this->data[$i][a][onclick] = $this->wandleJavascript($this->data[$i][a][onclick]);
					}
					if (isset($this->data[$i][a][onkeypress])) {
						$this->data[$i][a][onkeypress] = $this->wandleJavascript($this->data[$i][a][onkeypress]);
					}
					if (isset($this->data[$i][a][src])) {
						$this->data[$i][a][src] = $this->wandleImgUrls($this->data[$i][a][src]);
					}
					foreach ($this->data[$i][a] as $key=>$value) {
						//		    			$ergebnis .= " $key=\"".htmlspecialchars($value)."\"";
						if ($key!='href' && $key!='alt' && $key!='title') {
							$iso = " $key=\"".htmlspecialchars($value)."\"";
							$utf8 = iconv("ISO-8859-1", "UTF-8", $iso);
						} else {
							$value = iconv("ISO-8859-1", "UTF-8", $value);
							$utf8 = " $key=\"".$value."\"";
						}
						$ergebnis .= $utf8;
							
					}
				}
				if ($name=="br" || $name=="hr" || $name=="img" ||
				$name=="input" || $name=="script") {
					$ergebnis .= " />";
				} else {
					$ergebnis .= ">";
				}
			} else if (isset($this->data[$i][c])) {
				$ergebnis .= "</".$this->data[$i][c].">";
			} else if (isset($this->data[$i][d])) {
				$entities = htmlentities($this->data[$i][d], ENT_QUOTES, "Windows-1252");
				$utf8 = html_entity_decode($entities, ENT_QUOTES , "utf-8");
//				$ergebnis .= $utf8."\n";
				$ergebnis .= $this->data[$i][d] . "\n";
			}
		}
		return $ergebnis;
	}

	function wandleJavascript($text) {
		$text = str_replace('/static/','http://www6.hs-esslingen.de/static/',$text);
		$text = str_replace('/sixcms/','http://www6.hs-esslingen.de/sixcms/',$text);
		return $text;
	}

	function wandleUrls($sixUrl,&$target,$pageId=0) {
		if (strpos($sixUrl,'www.hs-esslingen.de/index.php')!==FALSE) {
			return $sixUrl;
		}
		
		if (strpos($sixUrl,'template=d_')!==FALSE) {
			// Zeigt Link auf ein Modul-PDF, dann wird der direkte Link ausgegeben?
			$url = str_replace('http://www6.hs-esslingen.de/','/',$sixUrl);
			if (strpos($sixUrl,'template=d_modul_detail')==FALSE) {
				$target = '_blank';
			}
			return SIX_BASE_URL.$url;
		} 

		// externe URLs unverändert übernehmen und target='_blank' setzen
		if ((strpos($sixUrl,'www.')!==FALSE 
				 || strpos($sixUrl,'http://')!==FALSE 
				 || strpos($sixUrl,'https://')!==FALSE ) && 
				 strpos($sixUrl,'www.hs-esslingen.de')===FALSE) {
			$target = '_blank';
			return $sixUrl;
		}
		
// Mitarbeiterseiten (zunächst) unverändert lassen
		if (strpos($sixUrl,'hs-esslingen.de/mitarbeiter/')!==FALSE ||
				strpos($sixUrl,'mitarbeiter/')==1 ){
			return SIX_BASE_URL . $sixUrl;
		}
		
		if (strpos($sixUrl,'#')==0 && strpos($sixUrl,'#')!==FALSE) {
			return iconv("ISO-8859-1", "UTF-8", $sixUrl);
		}
		
		$args = '';
		$target = '';
		$text = $sixUrl;
		$teile = explode("?",$sixUrl);
		if (count($teile)==2) {
			$text = $teile[0];
			$args = '&'.$teile[1];
		}
		$lang = 0;
		$id = '';
		// ggf. "http://www6.hs-esslingen.de" entfernen
		$url = str_replace('http://www6.hs-esslingen.de/','/',$text);
		// ggf. abschließenden Slash entfernen
		$url = preg_replace('#(.*)/$#','\\1',$url);
		$url = str_replace('/de/','/',$url);
		$sixAlias = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_six2t3_six_alias','alias="'.$url.'"');
		if ($GLOBALS['TYPO3_DB']->sql_num_rows($sixAlias)==1) {
			$aliasDaten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($sixAlias);
			$alias = $aliasDaten[url];
			if (is_numeric($alias)) {
				$url = $this->parent->cObj->typoLink_URL(array(
																								'parameter' => $alias,
																								'additionalParams' => '&L='.$lang.$args,
				));
				return $url;
			} else {
				return $alias.$args;
			}
		}	else if (preg_match('$/cgi-bin/htsearch$i',$text)) {
			return $this->parent->cObj->typoLink_URL(array(
																								'parameter' => ID_SUCHSEITE,
																								'additionalParams' => '&'.$teile[1],
			));
		} else if (preg_match('$^[0-9][0-9]*$i',$text)) {
			$id = $text;
			$lang = 0;
		} else if (preg_match('$^detail.php\?id=[0-9]*.*$i',$text)) {
			$replace = '\\1';
			$id = preg_replace('$^detail.php\?id=([0-9]*)(.*)$i',$replace,$text);
			$lang = 0;
		} else if (preg_match('$http://www6.hs-esslingen.de/de/([0-9]*)$i',$text)) {
			$replace = '\\1';
			$id = preg_replace('$http://www6.hs-esslingen.de/de/([0-9]*)$i',$replace,$text);
			$lang = 0;
		} else if (preg_match('$http://www6.hs-esslingen.de/fhte/([0-9]*)$i',$text)) {
			$replace = '\\1';
			$id = preg_replace('$http://www6.hs-esslingen.de/fhte/([0-9]*)$i',$replace,$text);
			$lang = 0;
		} else if (preg_match('$http://www6.hs-esslingen.de/en/([0-9]*)$i',$text)) {
			$replace = '\\1';
			$id = preg_replace('$http://www6.hs-esslingen.de/en/([0-9]*)$i',$replace,$text);
			$lang = 1;
		} else if (preg_match('$^/de/([0-9]*)$i',$text)) {
			$replace = '\\1';
			$id = preg_replace('$/de/([0-9]*)$i',$replace,$text);
			$lang = 0;
		} else if (preg_match('$^/fhte/([0-9]*)$i',$text)) {
			$replace = '\\1';
			$id = preg_replace('$/fhte/([0-9]*)$i',$replace,$text);
		} else if (preg_match('$^/en/([0-9]*)$i',$text)) {
			$replace = '\\1';
			$id = preg_replace('$/en/([0-9]*)$i',$replace,$text);
			$lang = 1;
		} else if (preg_match('$^/de/$i',$text)) {
			$url = str_replace('/de/','http://www6.hs-esslingen.de/de/',$text);
		} else if (preg_match('$^/fhte/$i',$text)) {
			$url = str_replace('/fhte/','http://www6.hs-esslingen.de/de/',$text);
		} else if (preg_match('$^/en/$i',$text)) {
			$url = str_replace('/en/','http://www6.hs-esslingen.de/en/',$text);
		} else if (preg_match('$^/static/$i',$text)) {
			$url = str_replace('/static/','http://www6.hs-esslingen.de/static/',$text);
		} else if (preg_match('$^/sixcms/$i',$text)) {
			$url = str_replace('/sixcms/','http://www6.hs-esslingen.de/sixcms/',$text);
		} else if (preg_match('$^/mitarbeiter/$i',$text)) {
			$url = str_replace('/mitarbeiter/','http://www6.hs-esslingen.de/mitarbeiter/',$text);
		} else {
			$url = $text;
		}
		if ($id=='') {
			// Fehlerseite aufrufen
			return $url.$args;
		}
		$seiten = $GLOBALS['TYPO3_DB']->exec_SELECTquery('typo3_id',
																														'tx_six2t3_pages',
																														'deleted=0 AND id="'.$id.'"');	 
		if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($seiten)) {
			$typo3Id = $row[typo3_id];
			$url = $this->parent->cObj->typoLink_URL(array(
																							'parameter' => $typo3Id,
																							'additionalParams' => '&L='.$lang.$args,)
			);
			return $url;
		} else {
			// Seite ist nicht in Typo3 angelegt
			// dies betrifft aktuelle Meldungen, Modulbeschreibungen etc.
			
// Personen-Seiten auf www6 umleiten, solange diese noch nicht importiert sind
			$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery('sixID','tx_six_personen_seiten','sixID="'.$id.'"');
			if ($seite = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
				return SIX_BASE_URL . '/de/' . $id;
			}
			
			// Wird zu dieser Seite eine navid übergeben, so wird geprüft,
			// ob zu dieser die entsprechende Typo3-Seite vorhanden ist
			// falls nicht, wird die aktuelle Seite aufgerufen
			// als Argument wird die sixID übergeben, die den Inhalt liefert
			$argumente = explode ("=",$args);
			if (count($argumente)>0) {
				$index = array_search('naviid',$argumente);
			}
			$typo3Id = 0;
			// Wurde eine naviid übergeben?
			if ($index>0) {
				$seiten = $GLOBALS['TYPO3_DB']->exec_SELECTquery('typo3_id',
																														'tx_six2t3_pages',
																														'deleted=0 AND id="'.$argumente[$index].'"');	 
				// Wurde die Seite mit der naviid in Typo3 angelegt?
				if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($seiten)) {
					$typo3Id = $row[typo3_id];
				}
			}
				
			// Wurde keine naviid übergeben bzw. nicht in Typo3 angelegt,
			// so wird die aktuelle Seite aufgerufen
			
			$args = preg_replace('§&naviid=[0-9]*§','',$args);
			$args = preg_replace('§&sixId=[0-9]*§','',$args);
			
			if ($typo3Id==0) {
				$cHash = t3lib_div::generateCHash('&sixId=' . $id);
				if ($pageId==0) {
					$url = $this->self . '?sixId=' . $id . '&cHash=' . $cHash;
				} else {
					$url = $this->parent->cObj->typoLink_URL(array(
																									'parameter' => $pageId,
																									'additionalParams' => '&L='.$lang.$args.
																																				'&sixId='.$id,
																									'useCacheHash' => 1,
																									));
					
				}
			} else {
				$url = $this->parent->cObj->typoLink_URL(array(
																								'parameter' => $typo3Id,
																								'additionalParams' => '&L='.$lang.$args.
																																			'&moduleId='.$id,
																								'useCacheHash' => 1,
																								));
			}
			return $url;
		}
	}

	function wandleImgUrls($text) {
		if (preg_match('$^/static/$i',$text)) {
			$ergebnis = str_replace('/static/','http://www6.hs-esslingen.de/static/',$text);
		} else if (preg_match('$^/sixcms/$i',$text)) {
			$ergebnis = str_replace('/sixcms/','http://www6.hs-esslingen.de/sixcms/',$text);
		} else if (preg_match('$^/htdig/$i',$text)) {
			$ergebnis = str_replace('/htdig/','http://www9.hs-esslingen.de/htdig/',$text);
		} else {
			$ergebnis = $text;
		}
		return $ergebnis;
	}

	function openHandler(& $parser,$name,$attrs) {
		$this->counter++;
		$this->data[$this->counter][o] =  $name;
		if (count($attrs)>0) {
			$this->data[$this->counter][a] =  $attrs;
		}
	}

	function closeHandler(& $parser,$name) {
		if ($name!="br" && $name!="hr" &&
		$name!="img" && $name!="input") {
			$this->counter++;
			$this->data[$this->counter][c] =  $name;
		}
	}

	function dataHandler(& $parser,$data) {
		$this->counter++;
		$this->data[$this->counter][d] = $data;
	}

	function escapeHandler(& $parser,$data) {
		$this->counter++;
		$this->data[$this->counter][esc] =  $data;
	}

	function piHandler(& $parser,$target,$data) {
		$this->counter++;
		$this->data[$this->counter][pi][d] =  $data;
		$this->data[$this->counter][pi][t] =  $target;
	}

	function jaspHandler(& $parser,$data) {
		$this->counter++;
		$this->data[$this->counter][jasp] =  $data;
	}
}

?>