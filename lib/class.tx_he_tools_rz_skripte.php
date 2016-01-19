<?php

require_once(t3lib_extMgm::extPath('fe_management') . 'model/modules_en/class.tx_femanagement_model_modules_en.php');
require_once(t3lib_extMgm::extPath('fe_management') . 'model/general/class.tx_femanagement_model_general_userdata.php');

class tx_he_tools_rz_skripte {
		
	static function rz_verfuegbarkeit() {
		if ( substr($_SERVER['REMOTE_ADDR'], 0,8) != '134.108.' ) die('intranet only');
		$color = "";
		$status_text ="";
		$output = "";

		$output .= '<meta http-equiv="refresh" content="30">';
		$output .= '<table width="100%" style="border:0; margin:0; padding:0; border-collapse:collapse; ">';
		$output .= "<tr><td colspan=\"4\"><b>Zentrale Dienste</b></td></tr>";
		
		$output .= self::rzCheckhtml('webmail.hs-esslingen.de',443,'Webmail');
		$output .= self::rzCheckhtml('mail.hs-esslingen.de',25,'Mailserver - SMTP');
		$output .= self::rzCheckhtml('comserver.hs-esslingen.de',22,'ssh-Login (comserver)');
		$output .= self::rzCheckhtml('134.108.34.5',53,'DNS Server');
		// $output .= rzCheckhtml('134.108.34.5',22,'DNS Server');
		$output .= self::rzCheckhtml('ldap3.hs-esslingen.de',389,'LDAP Authentifizierung');
		$output .= self::rzCheckhtml('ldap.hs-esslingen.de',389,'LDAP Adressbuch');
		$output .= self::rzCheckhtml('filer1.hs-esslingen.de',445,'Erster Datenspeicher');
		$output .= self::rzCheckhtml('filer2.hs-esslingen.de',445,'Zweiter Datenspeicher');
		
		$output .= "<tr><td colspan=\"4\"><b>Webserver</b></td></tr>";
		
		$output .= self::rzCheckhtml('www2.hs-esslingen.de',80,'Webserver Userhomes');
		$output .= self::rzCheckhtml('www3.hs-esslingen.de',80,'Webserver RZ/weitere');
		$output .= self::rzCheckhtml('www4.hs-esslingen.de',80,'Webserver RZ/internes');
		$output .= self::rzCheckhtml('moodle.hs-esslingen.de',80,'moodle-Plattform');
		
		$output .= "<tr><td colspan=\"4\"><b>Exchange Umgebung</b></td></tr>";
		
		$output .= self::rzCheckhtml('exchange.hs-esslingen.de',443,'Exchange Web-Access');
		
		$output .= "<tr><td colspan=\"4\"><b>Windows Dienste</b></td></tr>";
		
		$output .= self::rzCheckhtml('drucker-hze.hs-esslingen.de',445,'Druckserver HZE-Beschäftigte');
		$output .= self::rzCheckhtml('drucker-sm.hs-esslingen.de',445,'Druckserver SM-Beschäftigte');
		$output .= self::rzCheckhtml('drucker-gp.hs-esslingen.de',445,'Druckserver GP-Beschäftigte');
		$output .= self::rzCheckhtml('softxp.hs-esslingen.de',445,'Y:-Laufwerk (softxp)');
		
		$output .= "<tr><td colspan=\"4\"><b>Druckserver Hausdruckerei</b></td></tr>";
		
		$output .= self::rzCheckhtml('printsrv.hs-esslingen.de',445,'Druckserver');
		
		$output .= "<tr><td colspan=\"4\"><b>Andere Dienste</b></td></tr>";
		
		$output .= self::rzCheckhtml('ftp-stud.hs-esslingen.de',21,'<a target="_blank" href="http://ftp-stud.hs-esslingen.de/info/">FTP-Stud</a>');
		$output .= self::rzCheckhtml('svn.hs-esslingen.de',443,'<a target="_blank" href="/index.php?id=88030">Subversion Server</a>');
		#$output .= rzCheckhtml('udp://openvpn.hs-esslingen.de',36875,'OVPN');
		
		$output .= "</table><!-- rzCheck by mimeit01 / April 2007 -->";
		
		return $output;
	}

	static function rz_access_points() {
		$apAdmin = 0;
		if ($_REQUEST['x'] == 'y') {
			$apAdmin = 1;
		}
		
		if ($apAdmin==0 AND substr($_SERVER['REMOTE_ADDR'], 0,8) != '134.108.' ) {
			die('intranet only');
		}
	
		$color = "";
		$status_text ="";
		$output = "";
		
		$output .= '<meta http-equiv="refresh" content="15">';
		
		$output .= '<table width="100%" style="border:0; font-family:Arial; font-size:10pt; margin:0; padding:0; border-collapse:collapse; ">';
		
		$output .= "<tr><td colspan=\"4\"><b>Hochschulzentrum</b></td></tr>";
		
		$output .= self::apCheckhtml($apAdmin,'rhap0025.hs-esslingen.de','F01, H1 / H2 / Aquarium', '802.11AG');
		$output .= self::apCheckhtml($apAdmin,'rhap0026.hs-esslingen.de','F01, H3 / H4', '802.11AG');
		$output .= self::apCheckhtml($apAdmin,'rhap0023.hs-esslingen.de','F01, H5 / H6', '802.11AG');
		$output .= self::apCheckhtml($apAdmin,'rhap0017.hs-esslingen.de','F01, Erdgeschoss', '802.11AG');
		$output .= self::apCheckhtml($apAdmin,'rhap0022.hs-esslingen.de','F01, Empore', '802.11AG');
		$output .= '<tr><td bgcolor="#dddddd" height="2" colspan="4"></tr>';
		
		$output .= self::apCheckhtml($apAdmin,'rhap0015.hs-esslingen.de','F01, 1. Stock mitte (SAGP)', '802.11AG');
		$output .= self::apCheckhtml($apAdmin,'rhap0024.hs-esslingen.de','F01, 1. Stock (SAGP)', '802.11AG');
		$output .= self::apCheckhtml($apAdmin,'rhap0018.hs-esslingen.de','F01, 2. Stock (RZ)', '802.11AG');
		$output .= self::apCheckhtml($apAdmin,'rhap0019.hs-esslingen.de','F01, 4. Stock (IT)', '802.11AG');
		$output .= self::apCheckhtml($apAdmin,'rhap0020.hs-esslingen.de','F01, 3. Stock (IT)', '802.11AG');
		$output .= '<tr><td bgcolor="#dddddd" height="2" colspan="4"></tr>';
		
		$output .= self::apCheckhtml($apAdmin,'rhap0021.hs-esslingen.de','F01, 4a. Stock (IT)', '802.11AG');
		$output .= self::apCheckhtml($apAdmin,'rhap0031.hs-esslingen.de','F01, 5. Stock (Bibliothek)', '802.11AG');
		$output .= self::apCheckhtml($apAdmin,'rhap0032.hs-esslingen.de','F01, IT Lernraum im Keller', '802.11AG');
		$output .= self::apCheckhtml($apAdmin,'rhap0013.hs-esslingen.de','F02, BW Pools', '802.11AG');
		$output .= self::apCheckhtml($apAdmin,'rhap0036.hs-esslingen.de','F02, GS Pools', '802.11AG');
		$output .= '<tr><td bgcolor="#dddddd" height="2" colspan="4"></tr>';
		
		$output .= self::apCheckhtml($apAdmin,'rhap0030.hs-esslingen.de','F02, GS Lernecke', '802.11AG');
		$output .= self::apCheckhtml($apAdmin,'rhap0035.hs-esslingen.de','F02, H10', '802.11AG');
		$output .= self::apCheckhtml($apAdmin,'rhap0034.hs-esslingen.de','F02, BW Sitzecke', '802.11AG');
		$output .= self::apCheckhtml($apAdmin,'rhap0012.hs-esslingen.de','F02, H8 / H9 (Physik)', '802.11AG');
		$output .= self::apCheckhtml($apAdmin,'rhap0029.hs-esslingen.de','F02, SP Pool 4 / Werkstatt', '802.11AG');
		$output .= '<tr><td bgcolor="#dddddd" height="2" colspan="4"></tr>';
		
		$output .= self::apCheckhtml($apAdmin,'rhap0027.hs-esslingen.de','F02, H7', '802.11AG');
		$output .= self::apCheckhtml($apAdmin,'rhap0028.hs-esslingen.de','F02, BW Mitarbeiter / Ebene 4', '802.11AG');
		$output .= self::apCheckhtml($apAdmin,'rhap0033.hs-esslingen.de','F03, Mensa', '802.11AG');
		$output .= self::apCheckhtml($apAdmin,'rhap0003.hs-esslingen.de','F01, Wiese vor Haupteingang', '802.11G');
		$output .= self::apCheckhtml($apAdmin,'rhap0001.hs-esslingen.de','F03, Mitarbeiter SAGP', '802.11G');
		
		
		$output .= "<tr><td colspan=\"4\">&nbsp;</td></tr>";
		$output .= "<tr><td colspan=\"4\">&nbsp;</td></tr>";
		$output .= "<tr><td colspan=\"4\">&nbsp;</td></tr>";
		$output .= "<tr><td colspan=\"4\"><b>Stadtmitte</b></td></tr>";
		
		$output .= self::apCheckhtml($apAdmin,'rsap0005.hs-esslingen.de','?', '802.11AG');
		$output .= self::apCheckhtml($apAdmin,'rsap0007.hs-esslingen.de','?', '802.11AG');
		$output .= self::apCheckhtml($apAdmin,'rsap0009.hs-esslingen.de','?', '802.11AG');
		$output .= self::apCheckhtml($apAdmin,'rsap0010.hs-esslingen.de','?', '802.11AG');
		$output .= self::apCheckhtml($apAdmin,'rsap0011.hs-esslingen.de','?', '802.11AG');
		$output .= '<tr><td bgcolor="#dddddd" height="2" colspan="4"></tr>';
		
		$output .= self::apCheckhtml($apAdmin,'rsap0012.hs-esslingen.de','?', '802.11AG');
		$output .= self::apCheckhtml($apAdmin,'rsap0016.hs-esslingen.de','?', '802.11AG');
		$output .= self::apCheckhtml($apAdmin,'rsap0017.hs-esslingen.de','?', '802.11AG');
		$output .= self::apCheckhtml($apAdmin,'rsap0018.hs-esslingen.de','?', '802.11AG');
		$output .= self::apCheckhtml($apAdmin,'rsap0019.hs-esslingen.de','?', '802.11AG');
		$output .= '<tr><td bgcolor="#dddddd" height="2" colspan="4"></tr>';
		
		$output .= self::apCheckhtml($apAdmin,'rsap0020.hs-esslingen.de','?', '802.11AG');
		$output .= self::apCheckhtml($apAdmin,'rsap0021.hs-esslingen.de','?', '802.11AG');
		$output .= self::apCheckhtml($apAdmin,'rsap0022.hs-esslingen.de','?', '802.11AG');
		$output .= self::apCheckhtml($apAdmin,'rsap0023.hs-esslingen.de','?', '802.11AG');
		$output .= self::apCheckhtml($apAdmin,'rsap0024.hs-esslingen.de','?', '802.11AG');
		$output .= '<tr><td bgcolor="#dddddd" height="2" colspan="4"></tr>';
		
		$output .= self::apCheckhtml($apAdmin,'rsap0025.hs-esslingen.de','?', '802.11AG');
		$output .= self::apCheckhtml($apAdmin,'rsap0026.hs-esslingen.de','?', '802.11AG');
		$output .= self::apCheckhtml($apAdmin,'rsap0027.hs-esslingen.de','?', '802.11AG');
		$output .= self::apCheckhtml($apAdmin,'rsap0030.hs-esslingen.de','S07, RZ Pools', '802.11AG');
		// $output .= self::apCheckhtml($apAdmin,'rsap0024.hs-esslingen.de','S01', '802.11AG');
		// $output .= '<tr><td bgcolor="#dddddd" height="2" colspan="4"></tr>';
		
		$output .= "<tr><td colspan=\"4\">&nbsp;</td></tr>";
		$output .= "<tr><td colspan=\"4\">&nbsp;</td></tr>";
		$output .= "<tr><td colspan=\"4\">&nbsp;</td></tr>";
		$output .= "<tr><td colspan=\"4\"><b>G&ouml;ppingen</b></td></tr>";
		$output .= "<tr><td colspan=\"4\">In Goeppingen gibt es ebenfalls ein WLAN-Netz, hier findet zur Zeit eine Umstruckturierung statt, daher sind hier keine Daten vorhanden.</td></tr>";
		
		$output .= "</table><!-- APCheck by mimeit01 / Okt 2007 - Aug 2009 -->";
		return $output;
	}

	static function rzCheck($domain, $port=22) {
    $starttime = microtime(true);
    $file      = fsockopen ($domain, $port, $errno, $errstr, 1);
    $stoptime = microtime(true);
    $status    = 0;

    if (!$file) {
    	$status = -1;  // Site is down
    } else {
      fclose($file);
      $status = ($stoptime - $starttime) * 1000;
      $status = floor($status);
    }
    return $status;
	}

	static function apCheckhtml($apAdmin,$domain, $name, $norm) {
		$domainbase = $domain;
		$status = -1;
		$status = self::rzCheck($domainbase,22);
		if ($status != -1) {
			$color = "green";
			if ($status < 0) { $status = 0; }
			$status_text = "OK"; 
		} else {
			$color = "red";
			$status_text = 'nicht erreichbar';
		}
		if ( $apAdmin == 1 ) { 
			$name = $name . " (" . $domain . ")"; 
		}
		
		$output = "<tr><td bgcolor=\"$color\">&nbsp;&nbsp;&nbsp;</td><td>$name</td><td>$norm</td><td>$status_text</td></tr>";
		return $output;
	}
	
	static function rzCheckhtml($domain,$port,$name) {
		$domainbase = $domain;
		$port2 = $port;
		$status = -1;
		$status = self::rzCheck($domainbase,$port2);
		if ($status != -1) {
			$color = "green";
			if ($status < 0) { 
				$status = 0; 
			}
			$status_text = "OK ($status ms)"; 
		} else {
			$color = "red";
			$status_text = 'Fehler, siehe <a href="http://www2.hs-esslingen.de/~info/motd">motd</a>'; 
		}
		$output = "<tr><td bgcolor=\"$color\">&nbsp;&nbsp;&nbsp;</td><td>$name</td><td>$domain : $port</td><td>$status_text</td></tr>";
		return $output;
	}

	static function motd() {
		$search = array(
										'@<table[^>]*?>[\s]*@siu',
										'@</table[^>]*?>[\s]*@siu',
										'@<tr[^>]*?>[\s]*@siu',
										'@</tr[^>]*?>[\s]*@siu',
										'@<td[^>]*?>[\s]*@siu',
										'@</td[^>]*?>[\s]*@siu',
										'@<pre[^>]*?>[\s]*@siu',
										'@</pre[^>]*?>[\s]*@siu',
										'@---[-]*[\s]*@siu'
		);
		$replace = '';
		$url = 'http://www2.hs-esslingen.de/~info/motd/inc.six.php';
		$report = array();
		
		$content = t3lib_div::getURL($url, 0, false, $report);
		// analyze the response
		if ($report['error']) {
			return 'Die URL "' . $url . '" konnte nicht gelesen werden';
		} else {
/*
			preg_match('@.*Betrifft:[^<]*<b>([^<]*)</b>@siu',$content,$matches);
			if (count($matches>=1)) {
				$betreff = $matches[1];
			}
			preg_match('@.*Zeitraum/punkt:[^<]*<b>([^<]*)</b>@siu',$content,$matches);
			if (count($matches>=1)) {
				$zeitraum = $matches[1];
			}
			preg_match('@.*Typ der Meldung:[^<]*<b>([^<]*)</b>@siu',$content,$matches);
			if (count($matches>=1)) {
				$meldungstyp = $matches[1];
			}
*/
			$content = preg_replace('@<b>([^<]*)</b>@siu','<h3 class="dunkel_blau">$1</h3>',$content,1);
			$content = preg_replace($search,$replace,$content);
			$content = str_replace("\n",'<br>',$content);
			return '<hr class="clearer" />' .
							$content ;
		}
	}

	public static function getUrl($url, $includeHeader = 0, $requestHeaders = FALSE, &$report = NULL) {
		$content = FALSE;

		if (isset($report)) {
			$report['error'] = 0;
			$report['message'] = '';
		}

		// use cURL for: http, https, ftp, ftps, sftp and scp
		if ($GLOBALS['TYPO3_CONF_VARS']['SYS']['curlUse'] == '1' && preg_match('/^(?:http|ftp)s?|s(?:ftp|cp):/', $url)) {
			if (isset($report)) {
				$report['lib'] = 'cURL';
			}

			// External URL without error checking.
			if (!function_exists('curl_init') || !($ch = curl_init())) {
				if (isset($report)) {
					$report['error'] = -1;
					$report['message'] = 'Couldn\'t initialize cURL.';
				}
				return FALSE;
			}

			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, $includeHeader ? 1 : 0);
			curl_setopt($ch, CURLOPT_NOBODY, $includeHeader == 2 ? 1 : 0);
			curl_setopt($ch, CURLOPT_HTTPGET, $includeHeader == 2 ? 'HEAD' : 'GET');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_FAILONERROR, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, max(0, intval($GLOBALS['TYPO3_CONF_VARS']['SYS']['curlTimeout'])));
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$followLocation = @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

			if (is_array($requestHeaders)) {
				curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
			}

			// (Proxy support implemented by Arco <arco@appeltaart.mine.nu>)
			if ($GLOBALS['TYPO3_CONF_VARS']['SYS']['curlProxyServer']) {
				curl_setopt($ch, CURLOPT_PROXY, $GLOBALS['TYPO3_CONF_VARS']['SYS']['curlProxyServer']);

				if ($GLOBALS['TYPO3_CONF_VARS']['SYS']['curlProxyTunnel']) {
					curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, $GLOBALS['TYPO3_CONF_VARS']['SYS']['curlProxyTunnel']);
				}
				if ($GLOBALS['TYPO3_CONF_VARS']['SYS']['curlProxyUserPass']) {
					curl_setopt($ch, CURLOPT_PROXYUSERPWD, $GLOBALS['TYPO3_CONF_VARS']['SYS']['curlProxyUserPass']);
				}
			}
			$content = curl_exec($ch);
			if (isset($report)) {
				if ($content === FALSE) {
					$report['error'] = curl_errno($ch);
					$report['message'] = curl_error($ch);
				} else {
					$curlInfo = curl_getinfo($ch);
					// We hit a redirection but we couldn't follow it
					if (!$followLocation && $curlInfo['status'] >= 300 && $curlInfo['status'] < 400) {
						$report['error'] = -1;
						$report['message'] = 'Couldn\'t follow location redirect (PHP configuration option open_basedir is in effect).';
					} elseif ($includeHeader) {
						// Set only for $includeHeader to work exactly like PHP variant
						$report['http_code'] = $curlInfo['http_code'];
						$report['content_type'] = $curlInfo['content_type'];
					}
				}
			}
			curl_close($ch);

		} elseif ($includeHeader) {
			if (isset($report)) {
				$report['lib'] = 'socket';
			}
			$parsedURL = parse_url($url);
			if (!preg_match('/^https?/', $parsedURL['scheme'])) {
				if (isset($report)) {
					$report['error'] = -1;
					$report['message'] = 'Reading headers is not allowed for this protocol.';
				}
				return FALSE;
			}
			$port = intval($parsedURL['port']);
			if ($port < 1) {
				if ($parsedURL['scheme'] == 'http') {
					$port = ($port > 0 ? $port : 80);
					$scheme = '';
				} else {
					$port = ($port > 0 ? $port : 443);
					$scheme = 'ssl://';
				}
			}
			$errno = 0;
			// $errstr = '';
			$fp = @fsockopen($scheme . $parsedURL['host'], $port, $errno, $errstr, 2.0);
			if (!$fp || $errno > 0) {
				if (isset($report)) {
					$report['error'] = $errno ? $errno : -1;
					$report['message'] = $errno ? ($errstr ? $errstr : 'Socket error.') : 'Socket initialization error.';
				}
				return FALSE;
			}
			$method = ($includeHeader == 2) ? 'HEAD' : 'GET';
			$msg = $method . ' ' . (isset($parsedURL['path']) ? $parsedURL['path'] : '/') .
				($parsedURL['query'] ? '?' . $parsedURL['query'] : '') .
				' HTTP/1.0' . CRLF . 'Host: ' .
				$parsedURL['host'] . "\r\nConnection: close\r\n";
			if (is_array($requestHeaders)) {
				$msg .= implode(CRLF, $requestHeaders) . CRLF;
			}
			$msg .= CRLF;

			fputs($fp, $msg);
			while (!feof($fp)) {
				$line = fgets($fp, 2048);
				if (isset($report)) {
					if (preg_match('|^HTTP/\d\.\d +(\d+)|', $line, $status)) {
						$report['http_code'] = $status[1];
					} elseif (preg_match('/^Content-Type: *(.*)/i', $line, $type)) {
						$report['content_type'] = $type[1];
					}
				}
				$content .= $line;
				if (!strlen(trim($line))) {
					break; // Stop at the first empty line (= end of header)
				}
			}
			if ($includeHeader != 2) {
				$content .= stream_get_contents($fp);
			}
			fclose($fp);

		} elseif (is_array($requestHeaders)) {
			if (isset($report)) {
				$report['lib'] = 'file/context';
			}
			$parsedURL = parse_url($url);
			if (!preg_match('/^https?/', $parsedURL['scheme'])) {
				if (isset($report)) {
					$report['error'] = -1;
					$report['message'] = 'Sending request headers is not allowed for this protocol.';
				}
				return FALSE;
			}
			$ctx = stream_context_create(array(
					'http' => array(
						'header' => implode(CRLF, $requestHeaders)
					)
				)
			);

			$content = @file_get_contents($url, FALSE, $ctx);

			if ($content === FALSE && isset($report)) {
				$report['error'] = -1;
				$report['message'] = 'Couldn\'t get URL: ' . implode(LF, $http_response_header);
			}
		} else {
			if (isset($report)) {
				$report['lib'] = 'file';
			}

			$content = @file_get_contents($url);

			if ($content === FALSE && isset($report)) {
				$report['error'] = -1;
				$report['message'] = 'Couldn\'t get URL: ' . implode(LF, $http_response_header);
			}
		}

		return $content;
	}

	static function flinc() {
		$GLOBALS['TSFE']->additionalHeaderData ['he_tools_css_1'] = '
							<link rel="stylesheet" href="https://flinc.org/assets/widgets-datauri.css" />
							<link href="' . t3lib_extMgm::siteRelPath('he_tools') . 'res/css/flinc.css" rel="stylesheet" type="text/css" />
			';
		$url = 'https://flinc.org/groups/386-hochschule-esslingen/schedule';
		$report = array();
		$content = self::getUrl($url, 0, false, $report);
		if ($report['error']) {
			return 'Die URL "' . $url . '" konnte nicht gelesen werden:<br>' . print_r($report,true);
		} else {
			$content = str_replace('a href="/"', 'a target="_blank" href="https://flinc.org/', $content);
	    $content = str_replace('a href=', 'a target="_blank" href=', $content);
			$pos_begin = strpos($content, "<div class='scheduleWidget'");
	    $pos_begin = strpos($content, '<table>', $pos_begin);
	    $pos_end = strpos($content, '</table>', $pos_begin) + 8; 
	    $out = '<div class="scheduleWidget">';
	    $out .=  substr($content, $pos_begin, $pos_end - $pos_begin);
	    $out .= '</div>';
			$out .= '<script type="text/javascript">$("table tr").click( function() { window.open("' . $url . '","_blank"); });</script>';
		}
	  return $out;
	}
	
	static function campusLeben($link,$imgLink,$email) {
		$bildPfad = 'fileadmin/medien/einrichtungen/studentische_Einrichtungen/CampusLeben/gadget';
		$handle = opendir ($bildPfad);
		
		$dateien = array();
		$datumHeute = date('Y-m-d',time());
			
		$img = 'campusleben.png';
		while ($datei = readdir ($handle)) {
			if ($datei!='.' && $datei!='..' && $datei!=$img) {			
//				$timestr = filectime($bildPfad . '/' . $datei);
				if (strpos($datei,'201')!==FALSE) {
					$dateien[] = $datei;
				}
			}				
		}
		closedir($handle);
		arsort($dateien);
		foreach ($dateien as $dateiname) {
			if (strcmp($dateiname,$datumHeute)>0) {
				$img = $dateiname;
			}
		}
		$bildurl = $bildPfad . '/' . $img;
		$grafik = '<img style="width: 100%;" src="' . $bildurl . '" />';
		if (!empty($imgLink)) {
			$grafik = '<a target="_blank" href="' . $imgLink . '">"' . $grafik . '</a>';
		}
		$out = $grafik . '<br />';
		$out .= '<h3><a target="_blank" href="' . $link . '">' . $link . '</a></h3>';
		$out .= '<h3><a href="mailto:' . $email . '">' . $email . '</a></h3>';
		return $out;
	}
	
	static function studiengebuehrenListe($pageLink) {
		$get = t3lib_div::_GET();
		$semesterIndex = $get[semesterIndex];
    $semesterliste = array(
      '10' => 'WS 2011/12',
    	'9' => 'SS 2011',
      '8' => 'WS 2010/11',
    	'7' => 'SS 2010',
    	'6' => 'WS 2009/10',
      '5' => 'SS 2009',
      '4' => 'WS 2008/09',
      '3' => 'SS 2008',
      '2' => 'WS 2007/08',
      '1' => 'SS 2007',
    );
    $out = '<p>';
    foreach($semesterliste as $semester => $titel) {
      $out .= '<a href="'. $pageLink .'?semesterIndex='.$semester.'"><h3>'.$titel.'</h3></a>'."\n";
    }
    $out .= '</p>';
    if (!empty($semesterIndex)) {
			$semesterliste = array(
	      '1' => '20071.txt',
	      '2' => '20072.txt',
	      '3' => '20081.txt',
	      '4' => '20082.txt',
	      '5' => '20091.txt',
	      '6' => '20092.txt',
	      '7' => '20101.txt',
	      '8' => '20102.txt',
	      '9' => '20111.txt',
	      '10' => '20112.txt',
			);
	    $dateiName = $semesterliste[$semesterIndex];
	    $datei = 'http://www8.hs-esslingen.de/uploads/tx_tuitionfees/exports/'. $dateiName;
	    $handle = fopen($datei, "r");
			if (!$handle) {
	    	$out = 'Die Datei "' . $datei . '" konnte nicht gelesen werden';
			} else {
				while (!feof ($handle)) {
			    $out .= fgets ($handle, 1024);
				}
			}
			fclose($handle);	    
	  }
		return $out;
	}
	
	public static function erzeugeShibLoginMitRedirect($anzeigeText='') {
		if (empty($anzeigeText)) {
			return '<h2>Bitte <a href="https://www.hs-esslingen.de/de/nc/login.html?redirect_url=http://www.hs-esslingen.de/index.php?id=' . $GLOBALS['TSFE']->id . '">melden Sie sich mit Ihrem Hochschulkonto an</a> um diese Seite zu öffnen.</h2>';
		} else {
			return '<h2><a href="https://www.hs-esslingen.de/de/nc/login.html?redirect_url=http://www.hs-esslingen.de/index.php?id=' . $GLOBALS['TSFE']->id . '">' . $anzeigeText . '</a></h2>';
			
		}
		
	}

	public static function showBrowserInfo() {
		return '<table border="0" width="400">
  <tr>
    <td width="100%" align="center"><font face="Arial" size="3"><b>Browser Informationen</b></font></td>
  </tr>
  <tr>
    <td width="100%">&nbsp;</td>
  </tr>
  <tr>
    <td width="100%">
      <table border="0" width="100%" style="border: 1px solid #CCCCCC">
        <tr>
          <td width="100%" colspan="2"><font face="Arial" size="2"><b><u>allgemeine Informationen:</u></b></font></td>
        </tr>
        <tr>
          <td width="120" align="right"><font face="Arial" size="2"><b>Browsertyp:</b></font></td>
          <td width="280">
<script type="text/javascript">
<!--

var Benutzeragent14 = navigator.userAgent.toLowerCase();
var Browsernamen14 = new Array("Konqueror","SeaMonkey","Firebird","Galeon","Epiphany","Camino","OmniWeb","Chrome","Iron","Lynx","amaya","iCab","Netscape","Navigator","MSIE","Firefox","Opera","Safari","rv:","Mozilla");
var Namensindex14 = -1;

for(var i=0;i<19;i++)
{
if(Benutzeragent14.indexOf(Browsernamen14[i].toLowerCase())>-1)
{
Namensindex14 = i;
break;
}
}

document.write(unescape(\'%3Cfont face="Arial" size="2">\'+(Namensindex14<0 ? "unbekannt":(Namensindex14==13 ? "Netscape":(Namensindex14==14 ? "Internet Explorer":(Namensindex14==18 ? "Mozilla":Browsernamen14[Namensindex14]))))+\'%3C/font>\'));
//-->
</script></td>
        </tr>
        <tr>
          <td width="120" align="right"><font face="Arial" size="2"><b>Browser-Version:</b></font></td>
          <td width="280">
<script type="text/javascript">
<!--
function Versionsausgabe14(Text14)
{
if(parseInt(Text14.charAt(0))+""=="NaN")
Text14 = Text14.substring(1);

if(parseInt(Text14.charAt(0))+""!="NaN")
{
var i = 0;

while(parseInt(Text14.charAt(i))+""!="NaN"||Text14.charAt(i)==".")
i++;

Text14 = Text14.substring(0,i);
}
if(parseInt(Text14)+""=="NaN")
Text14 = "unbekannt";

return Text14;
}

with(Benutzeragent14)
{
if(indexOf("version")>-1)
Browsernamen14[Namensindex14] = "version";

document.write(unescape(\'%3Cfont face="Arial" size="2">\'+(Namensindex14>-1 ? Versionsausgabe14(substring(indexOf(Browsernamen14[Namensindex14].toLowerCase())+(Browsernamen14[Namensindex14]+"*").indexOf("*"))):"unbekannt")+\'%3C/font>\'));
}
//-->
</script></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td width="100%">&nbsp;</td>
  </tr>
  <tr>
    <td width="100%">
      <table border="0" width="100%" style="border: 1px solid #CCCCCC">
        <tr>
          <td width="100%" colspan="2"><font face="Arial" size="2"><b><u>Browsereigene Angaben:</u></b></font></td>
        </tr>
        <tr>
          <td width="120" align="right"><font face="Arial" size="2"><b>Browsername:</b></font></td>
          <td width="280">
<script type="text/javascript">
<!--
document.write(unescape(\'%3Cfont face="Arial" size="2">\'+navigator.appName+\'%3C/font>\'));
//-->
</script></td>
        </tr>
        <tr>
          <td width="120" align="right"><font face="Arial" size="2"><b>Spitzname:</b></font></td>
          <td width="280">
<script type="text/javascript">
<!--
document.write(unescape(\'%3Cfont face="Arial" size="2">\'+navigator.appCodeName+\'%3C/font>\'));
//-->
</script></td>
        </tr>
        <tr>
          <td width="120" align="right"><font face="Arial" size="2"><b>Mozilla-Version:</b></font></td>
          <td width="280">
<script type="text/javascript">
<!--
with(Benutzeragent14)
document.write(unescape(\'%3Cfont face="Arial" size="2">\'+(indexOf("mozilla")>-1 ? Versionsausgabe14(substring(indexOf("mozilla")+7)):"unbekannt")+\'%3C/font>\'));
//-->
</script></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td width="100%">&nbsp;</td>
  </tr>
  <tr>
    <td width="100%">
      <table border="0" width="100%" style="border: 1px solid #CCCCCC">
        <tr>
          <td width="100%" colspan="2"><font face="Arial" size="2"><b><u>Informationen über Details:</u></b></font></td>
        </tr>
        <tr>
          <td width="120" align="right" valign="top"><font face="Arial" size="2"><b>Cookies elaubt:</b></font></td>
          <td width="280">
<script type="text/javascript">
<!--
document.write(unescape(\'%3Cfont face="Arial" size="2">\'+(navigator.cookieEnabled||navigator.cookieEnabled==false ? (navigator.cookieEnabled ? "Ja":"Nein"):"Der Browser kann diese Eigenschaft nicht auslesen.")+\'%3C/font>\'));
//-->
</script></td>
        </tr>
        <tr>
          <td width="120" align="right" valign="top"><font face="Arial" size="2"><b>Browsersprache:</b></font></td>
          <td width="280">
<script type="text/javascript">
<!--
document.write(unescape(\'%3Cfont face="Arial" size="2">\'+(navigator.language ? navigator.language:"Der Browser kann diese Eigenschaft nicht auslesen.")+\'%3C/font>\'));
//-->
</script></td>
        </tr>
        <tr>
          <td width="120" align="right" valign="top"><font face="Arial" size="2"><b>Betriebssystem:</b></font></td>
          <td width="280">
<script type="text/javascript">
<!--
document.write(unescape(\'%3Cfont face="Arial" size="2">\'+(navigator.platform ? navigator.platform:"Der Browser kann diese Eigenschaft nicht auslesen.")+\'%3C/font>\'));
//-->
</script></td>
        </tr>
        <tr>
          <td width="120" align="right" valign="top"><font face="Arial" size="2"><b>Benutzer-Agent</b></font></td>
          <td width="280">
<script type="text/javascript">
<!--
document.write(unescape(\'%3Cfont face="Arial" size="2">\'+navigator.userAgent+\'%3C/font>\'));
//-->
</script></td>
        </tr>
      </table>
    </td>
  </tr>
</table>
		
<div class="csc-textpic-text">
<h1>Eckig oder rund?</h1>
		<!--  Text: [begin] -->
			<table class="zentriert tab50" summary=""><tbody>
			<tr class="z3"><td class="td50"><span class="button_mittelblau">
			<a href="http://www.hs-esslingen.de/index.php?id=132993">Test1</a></span></td>
			<td class="td50"><span class="button_mittelblau"><a href="http://www.hs-esslingen.de/index.php?id=132993">Test2</a></span></td>
			</tr></tbody></table>
			
		<!--  Text: [end] -->
			</div>		
		
		';
	}

	public static function roemGeschenke(&$cObj) {
		$out = '<div class="csc-textpic-text">
			<table class="zentriert tab100"0><tbody>
			';
		$where = 'deleted=0 and hidden=0 AND pid=93127 AND hauptkategorie=46';
		$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('produktname,bild,preis','tx_hebest_artikel',$where,'','produktname');
		$dbDaten = array();
		while ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
			$dbDaten[] = $daten;
		}
		
		foreach ($dbDaten as $eintrag) {
			$out .= '<tr>';
			if (!empty($eintrag['bild'])) {
				$pfad = 'uploads/tx_hebest/' . $eintrag['bild'];
				$imgConfig = array();
				$imgConfig['file'] = $pfad;
				$imgConfig['file.']['maxW'] = 50;
				$imgConfig['file.']['maxH'] = 50;
				$bildAdresse = $cObj->IMG_RESOURCE($imgConfig);
				
//				$bild = '<img width="50px" src="' . $bildAdresse . '" />';
				$bild = '<a class="lightbox" title="' . $eintrag['produktname'] . '" data-fancybox-group="lightbox_werbegeschenke" href="' . $pfad . '"><img width="50" src="' . $bildAdresse . '" /></a>';
			} else {
				$bild = '';
			}
			$out .= '<td>' . $bild . '</td>';
			$out .= '<td>' . $eintrag['produktname'] . '</td>';
			$preisFloat = floatval(str_replace(',','.',$eintrag['preis']));
			$preis = number_format($preisFloat, 2, ',', '') . '&nbsp;&euro;';
			$out .= '<td style="text-align: right;">' . $preis . '</td>';
			$out .= '</tr>';
		}
		$out .= '</tbody></table></div>';
		return $out;
	}
	
	public static function modulbeschreibungenEnglisch(&$piBase, $pid=137077) {
		$model = t3lib_div::makeInstance('tx_femanagement_model_modules_en',$piBase,$pid);
		$modelFeUser = t3lib_div::makeInstance('tx_femanagement_model_general_userdata');
		$standorte = $model->gibStandortListe('en','uid');
		$out = '<table class="tab100"><tbody>';
		foreach ($standorte as $uid=>$title) {
			$out .= self::standortModuleAusgeben($model, $modelFeUser, $uid, $title);
		}
		$out .= '</tbody></table>';
		return $out;
	}
	
	public static function standortModuleAusgeben(&$model, &$modelFeUser, $standort, $titel) {
		$out = '<tr class="hg_rot"> <td colspan="7"><h3>' . $titel . '</h3></td></tr>
						<tr><td colspan="7"> </td></tr>
		';
		$out .= '<tr class="hg_dunkelblau">
							<th>Departments/ <br>Study programs</th> 
							<th>Title</th> 
							<th>Lecturer</th>
							<th>ECTS <br>credits</th>
							<th>Bachelor Level A <br>(1.-2. Sem.)</th>
							<th>Bachelor Level B <br>(3.-7. Sem.)</th>
							<th>Semester </th>
							</tr>';
		$fakultaeten = $model->gibFakultaetenMitModulen($standort,'en');
		foreach ($fakultaeten as $fakultaet=>$title) {
			$out .= self::fakultaetsModuleAusgeben($model, $modelFeUser, $fakultaet, $title);
		}
		$out .= '<tr> <td colspan="7">&nbsp;</td></tr>
		';
		return $out;
	}
	
	public static function fakultaetsModuleAusgeben(&$model, &$modelFeUser, $fakultaet,$titel) {
		$hgModus = '';
		$out = '<td colspan="7"><h3 class="faculty">' . $titel . '</h3></td>
		';
		$module = $model->gibFakultaetsModule($fakultaet);
		$sprungmarkeEnde = '</a>';
		foreach ($module as $modulDaten) {
			$studiengaenge = '';
			$studiengangListe = unserialize($modulDaten['studiengang']);
			$studiengangTitel = array();
			foreach ($studiengangListe as $studiengang) {
				$studiengangTitel[] = $model->gibStudiengangTitel($studiengang['studiengang'],'en');
			}
			$studiengaenge .= implode('<br />' , $studiengangTitel);
			
			$verantwortliche = unserialize($modulDaten['verantwortliche']);
			$eintraege = array();
			foreach ($verantwortliche as $eintrag) {
				$username = $eintrag['value'];
				$benutzerDaten = $modelFeUser->selectFields('username',$username,'fe_users','tx_hepersonen_akad_grad,first_name,last_name,email,tx_hepersonen_profilseite');
				if (empty($benutzerDaten)) {
					$eintraege[] = $eintrag['valueSelect'];
				} else {
					$name = $benutzerDaten['first_name'] . ' ' . $benutzerDaten['last_name'];
					if (!empty($benutzerDaten['tx_hepersonen_akad_grad'])) {
						$name = $benutzerDaten['tx_hepersonen_akad_grad'] . ' ' . $name;
					}
					$eintrag = '<a target="_blank" href="index.php?id=' . $benutzerDaten['tx_hepersonen_profilseite'] . '">' . $name . '</a>';
					$eintraege[] = $eintrag;
				}
			}
			$verantwortliche = implode('<br />',$eintraege);
			
			$url = '';
			if (!empty($modulDaten['link'])) {
				$url = $modulDaten['link'];
			} else if (!empty($modulDaten['download'])) {
				$url = 'uploads/tx_femanagement_module_en/' . $modulDaten['download'];
			} 
			if (!empty($url)) {
				$titel = '<a target="_blank" href="' . $url . '">' . $modulDaten['title'] . '</a>';
			} else {
				$titel = $modulDaten['title'];
			}
			if (!empty($modulDaten['zusatz'])) {
				$titel .= '<br />' . $modulDaten['zusatz'];
			}
			$credits = $modulDaten['credits'];
			$level = $modulDaten['level'];
			$level1 = ' ';
			$level2 = ' ';
			if ($level==3) {
				$level1 = 'x';
				$level2 = 'x';
			} else if ($level==2) {
				$level1 = ' ';
				$level2 = 'x';
			} else if ($level==1) {
				$level1 = 'x';
				$level2 = ' ';
			}
			$semester = $modulDaten['semester'];
			$angebotenesSemester = '';
			if ($semester==3) {
				$angebotenesSemester = 'SS/WS';
			} else if ($semester==2) {
				$angebotenesSemester = 'WS';
			} else if ($semester==1) {
				$angebotenesSemester = 'SS';
			}
			if ($hgModus=='hell') {
				$rowClass = ' class="hg_dunkel"';
				$hgModus = 'dunkel';
			} else {
				$rowClass = '';
				$hgModus = 'hell';
				
			}
			$zeilenId = 'uid' . $modulDaten['uid'];
			$sprungmarkeStart = '<a id="' . $zeilenId  . '" name="' . $zeilenId . '">';
			$out .= '<tr' . $rowClass . '>';
			$out .= '<td>' . $sprungmarkeStart . $studiengaenge . $sprungmarkeEnde . '</td>';
			$out .= '<td>' . $titel . '</td>';
			$out .= '<td>' . $verantwortliche . '</td>';
			$out .= '<td>' . $credits . '</td>';
			$out .= '<td>' . $level1 . '</td>';
			$out .= '<td>' . $level2 . '</td>';
			$out .= '<td>' . $angebotenesSemester . '</td>';
			$out .= '</tr>';
/*
			<td><a href="en/staff/joachim-domnick.html">Prof. Dr.-Ing. Joachim Domnick</a></td> 
			<td>4 </td> <td>&nbsp; </td> <td>x </td> <td>SS/WS </td></tr>
*/			
		}
		return $out;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_personen/php_scripts/class.rz_access_points.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_personen/php_scripts/class.rz_access_points.php.php']);
}
?>