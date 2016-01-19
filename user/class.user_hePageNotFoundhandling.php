<?php

require_once(t3lib_extMgm::extPath('he_tools').'lib/globals.php');

class user_hePageNotFoundHandling {
var	$PNFH_DEBUG = TRUE;
	
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
	
	function pageUnavailable($params,$ref){
		$this->PNFH_DEBUG = FALSE;
		$alias = $params[currentUrl];
/*
		if (strpos($alias,'sechash')!==FALSE) {
			$PNFH_DEBUG = TRUE;
		} else {
			$PNFH_DEBUG = FALSE;
		}
*/
		
if ($this->PNFH_DEBUG) t3lib_div::devLog('params: ' . print_r($params,true), 'pageUnavailable', 0);
// if ($this->PNFH_DEBUG) t3lib_div::devLog('ref: ' . print_r($ref,true), 'pageUnavailable', 0);
		
		if (strpos($alias,'/en/')!==FALSE) {
			$sprache = 1;
		} else {
			$sprache = 0;
		}

		// Direkte Links aus Office-Dokumenten prüfen: 
		$accept = $_SERVER['HTTP_ACCEPT'];
		if (trim($accept)=='*/*') {
			$location = 'Location: http://www.hs-esslingen.de/typo3conf/ext/he_tools/user/redirect.php?zeit=0&grund=schutz&redirectEnc=' . base64_encode($alias);
			header($location);
			exit();
		}

//if ($this->PNFH_DEBUG) t3lib_div::devLog('user: ' . print_r($GLOBALS['TSFE']->fe_user->user,true), 'pageUnavailable', 0);
		if (is_array($params['pageAccessFailureReasons']['fe_group'])) {
			if (!empty($GLOBALS['TSFE']->fe_user->user['username'])) {
// Seite "Keine Berechtigung" anzeigen
// Sprachumschaltung auf englisch funktioniert derzeit nicht mit Realurl
				$where = 'page_id=92431 AND origparams LIKE "%&L=' . $sprache . '%"';	
				$realurlQuery = $GLOBALS['TYPO3_DB']->exec_SELECTquery('content','tx_realurl_urlencodecache',$where);
				if ($realUrl = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($realurlQuery)) {
					$url = BASE_URL_HTTP . '/' . $realUrl[content] . '?redirect_url=' . $alias;
				} else {
					$url = BASE_URL_HTTP . '/' . 'index.php?id=92431&L=' . $sprache . '&redirect_url=' . $alias;
				}
			} else {
/*
 * Endlos-Umleitung abfangen: 
 *   falls die URL bereits die URL der Login-Seite beinhaltet, wird zur Fehlerseite weitergeleitet
 */					
				if (strpos($alias,'index.php?id=33139')!==FALSE) {
					$url = BASE_URL_HTTP . '/de/fehler.html';
				} else {
/*
 * Auf die zentrale Login-Seite (Shibboleth-Login) umleiten
 */					
					$redirectUrl = base64_encode(BASE_URL_HTTP . $alias);
					$url = BASE_URL_HTTPS . '/index.php?id=33139&L=' . $sprache . 
							 '&redirectEnc=' . $redirectUrl;
				}
if ($this->PNFH_DEBUG) t3lib_div::devLog("url: ".$url, 'pageNotFound', 0);
			}
			t3lib_utility_Http::redirect($url,
																	 t3lib_utility_Http::HTTP_STATUS_401);
		}
/*
		$where = 'content="de/fehler.html"';	
		$realurlQuery = $GLOBALS['TYPO3_DB']->exec_SELECTquery('content','tx_realurl_urlencodecache',$where);
		if ($realUrl = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($realurlQuery)) {
			$url = BASE_URL_HTTP . '/' . $realUrl[content];
		} else {
			$url = BASE_URL_HTTP . '/de/fehler.html';
		}	
*/
		
		$url = BASE_URL_HTTP . '/de/fehler.html';
		t3lib_utility_Http::redirect($url,
																 t3lib_utility_Http::HTTP_STATUS_404);
  }
		
	function pageNotFound($params,$ref){
		$this->PNFH_DEBUG = FALSE;

	 	$typo3Id = 0;
	 	$sixUmleitung = FALSE;
  	$alias = rawurldecode($params[currentUrl]);

//		t3lib_div::devLog("alias: ".print_r(rawurldecode($alias),true), 'pageNotFound', 0);

/* 
 * Sonderprogrammierung SixCMS-Startseite - START

if ($alias=='/de/44' || $alias=='/de/44/' || 
		$alias=='/44') {
$ip = self::get_real_ip();
t3lib_div::devLog('ip: ' . $ip, 'six_44', 0);
			
$location = 'Location: http://www.hs-esslingen.de/de/';
header($location);
exit();
} 	
/* 
 * Sonderprogrammierung SixCMS-Startseite - ENDE
 */

/*
		if (strpos($alias,'26e92607')!==FALSE) {
			$this->PNFH_DEBUG = TRUE;
		} else {
			$this->PNFH_DEBUG = FALSE;
		}
*/		
  	// Defaultsprache ist deutsch
		$sprache = 0;
		if (strpos($alias,'/en/')!==FALSE) {
			$sprache = 1;
		}
		
		// Direkte Links aus Office-Dokumenten prüfen: 
		$accept = $_SERVER['HTTP_ACCEPT'];
		if (trim($accept)=='*/*') {
			$location = 'Location: http://www.hs-esslingen.de/typo3conf/ext/he_tools/user/redirect.php?zeit=0&grund=schutz&redirectEnc=' . base64_encode($alias);
			header($location);
			exit();
		}

//if ($this->PNFH_DEBUG) t3lib_div::devLog("fe_user: ".print_r($GLOBALS['TSFE']->fe_user->user,true), 'pageNotFound', 0);
if ($this->PNFH_DEBUG) t3lib_div::devLog("params: ".print_r($params,true), 'pageNotFound', 0);
		// Zugriffsfehler (nicht eingeloggt) auf die Seite "Kein Zugriff" umlenken
		if (is_array($params['pageAccessFailureReasons']['fe_group'])) {
			$feGroups = array_values($params['pageAccessFailureReasons']['fe_group']);
			if ($feGroups[0]!=0) {
				if (!empty($GLOBALS['TSFE']->fe_user->user)) {
	// Seite "Keine Berechtigung" anzeigen
	// Sprachumschaltung auf englisch funktioniert derzeit nicht mit Realurl
					$where = 'page_id=92431 AND origparams LIKE "%&L=' . $sprache . '%"';	
					$realurlQuery = $GLOBALS['TYPO3_DB']->exec_SELECTquery('content','tx_realurl_urlencodecache',$where);
					if ($realUrl = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($realurlQuery)) {
						$url = BASE_URL_HTTP . '/' . $realUrl[content] . '?redirect_url=' . $alias;
					} else {
						$url = BASE_URL_HTTP . '/' . 'index.php?id=92431&L=' . $sprache . '&redirect_url=' . $alias;
					}
				} else {
/*
 * Auf die zentrale Login-Seite (Shibboleth-Login) umleiten
 */				
					$redirectUrl = 	base64_encode(BASE_URL_HTTP . $alias);
					$url = BASE_URL_HTTPS . '/index.php?id=33139&L=' . $sprache . 
								 '&redirectEnc=' . $redirectUrl;
if ($this->PNFH_DEBUG) t3lib_div::devLog("url: ".$url, 'pageNotFound', 0);
				}
				t3lib_utility_Http::redirect($url,
																		 t3lib_utility_Http::HTTP_STATUS_401);
			}
		}

		if (strpos($alias,'/mitarbeiter/')!==FALSE) {
			if (strpos($alias,'.')===FALSE) {
				$alias = str_replace('/de','',$alias);
				return $this->redirectAlias($alias);
			} else {
				$alias2 = str_replace('.html','',$alias);
				$alias2 = str_replace(' ','-',$alias2);
				$alias2 = str_replace('%20','-',$alias2);
				$alias2 = str_replace('.','-',$alias2);
				$alias2 .= '.html';
				if ($alias!=$alias2) {
					t3lib_utility_Http::redirect(strtolower($alias2),
																			 t3lib_utility_Http::HTTP_STATUS_301);
				}
			}
		}
		
// Alias-Liste durchlaufen, um externe Aliase zu behandeln
// ggf. führendes '/de' und abschließenden Slash entfernen
//		$alias2 = preg_replace('#(/de){0,1}(/[^/]*)(/)*$#','\\2',$alias);
//		$alias2 = preg_replace('#(.*)/$#','\\1',$alias);
		$alias2 = preg_replace('#(/de/)(.*)(/)*$#iU','/$2',$alias);
		$alias2 = preg_replace('#(/en/)(.*)(/)*$#iU','/$2',$alias2);
/*
		$alias2 = preg_replace('#(/de)*(/[^/]*)(/)*$#','$2',$alias);

if ($GLOBALS['TSFE']->fe_user->user['username']=='mmirsch') {
	t3lib_div::debug($alias,$alias2);
	exit();
}

*/


		$whereAlias = 'deleted=0 AND alias="' . $alias . '"';
		if ($alias2!=$alias) {
			$whereAlias .= ' OR alias="' . $alias2 . '"';
		}

if ($this->PNFH_DEBUG) t3lib_div::devLog("whereAlias: $whereAlias", 'pageNotFound', 0);
		$sixAlias = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_six2t3_six_alias',$whereAlias);
		if ($aliasDaten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($sixAlias)) {
			$url = $aliasDaten[url];
			$sprache = $aliasDaten[lang];
			$args = $aliasDaten[args];
			if (is_numeric($url)) {
// Alias auf eine Seite aus SixCMS gefunden 
				$typo3Id = $url;
			} else { 
// externer Alias gefunden 
if ($this->PNFH_DEBUG) t3lib_div::devLog("Umleitung1 auf sixUrl: $alias, Url: $url", 'pageNotFound', 0);
				t3lib_utility_Http::redirect($url,
																		 t3lib_utility_Http::HTTP_STATUS_301);
			}
		}

		if ($typo3Id!=0) {
			if ($typo3Id==1) {
				$url =  '/de/';
			} else {
				$where = 'page_id=' . $typo3Id . ' AND mpvar =  "" AND language_id=' . $sprache;	
				$realurlQuery = $GLOBALS['TYPO3_DB']->exec_SELECTquery('pagepath','tx_realurl_pathcache',$where);
				if ($realUrl = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($realurlQuery)) {
					if ($sprache==1) {
						$sprachAbschnitt = 'en/';
					} else {
						$sprachAbschnitt = 'de/';
					}
					$url =  '/' . $sprachAbschnitt.$realUrl[pagepath];
				} else {
					$url = '/index.php?id=' . $typo3Id . '&L=' . $sprache;
				}
			}
			
			t3lib_utility_Http::redirect($url, t3lib_utility_Http::HTTP_STATUS_301);		
		}	
		
		if (strpos($params[currentUrl],'template=d_')!==FALSE) {
				t3lib_utility_Http::redirect(SIX_BASE_URL . '/' . $params[currentUrl],
																		 t3lib_utility_Http::HTTP_STATUS_301);
		}
		
		if (strpos($alias,'/en/')!==FALSE) {
			$aliasDe = str_replace('/en/','/de/',$alias);
			$url = BASE_URL_HTTP . $aliasDe;
if ($this->PNFH_DEBUG) t3lib_div::devLog("Umleitung3 auf deutsche Seite: $url", 'pageNotFound', 0);
			t3lib_utility_Http::redirect($url,t3lib_utility_Http::HTTP_STATUS_302);
		}
		
		
// Seite nicht auflösbar, da entweder  keine Typo3-ID dazu
		return $this->redirectError();
  }
  
  protected function redirectError() {
		$where = 'content="de/fehler.html"';	
		$realurlQuery = $GLOBALS['TYPO3_DB']->exec_SELECTquery('content','tx_realurl_urlencodecache',$where);
		if ($realUrl = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($realurlQuery)) {
			$url = BASE_URL_HTTP . '/' . $realUrl[content];
		} else {
			$url = BASE_URL_HTTP . '/index.php?id=22880';
		}
		
		t3lib_utility_Http::redirect($url, t3lib_utility_Http::HTTP_STATUS_404);
  }
  
  protected function redirectAlias($alias) {

		$whereAlias = 'deleted=0 AND alias="' . $alias . '"';

  	$sixAlias = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_six2t3_six_alias',$whereAlias);
		if ($aliasDaten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($sixAlias)) {
			$url = $aliasDaten[url];
			$sprache = $aliasDaten[lang];
			if (is_numeric($url)) {			
				$where = 'page_id=' . $url . ' AND mpvar =  "" AND language_id=' . $sprache;	
				$realurlQuery = $GLOBALS['TYPO3_DB']->exec_SELECTquery('pagepath','tx_realurl_pathcache',$where);
				if ($realUrl = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($realurlQuery)) {
					if ($sprache==1) {
						$sprachAbschnitt = 'en/';
					} else {
						$sprachAbschnitt = 'de/';
					}
					$url =  '/' . $sprachAbschnitt . $realUrl[pagepath];
				} else {
					$url = '/index.php?id=' . $url . '&L=' . $sprache;
				}
				t3lib_utility_Http::redirect(BASE_URL_HTTP . $url,
																		 t3lib_utility_Http::HTTP_STATUS_301);
				exit();																		 
			}	else {
				t3lib_utility_Http::redirect($url,
																		 t3lib_utility_Http::HTTP_STATUS_301);
				exit();
			}	
		}
		return $this->redirectError();
	}
}

?>