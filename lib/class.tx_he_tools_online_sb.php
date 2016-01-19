<?php 

class tx_he_tools_online_sb {
	public static $anonymerGast = 182;
	public static $sbAdmin = 183;
	public static $sbHiwi = 184;
	public static $studa = 78;
	public static $pidGastuser = 101294;
	public static $pidAnfragen = 131725;
	public static $kuerzelGastuser = 'sb_online_';
	protected $piBase;
	
	public function __construct(&$parent) {
		$this->piBase = &$parent;
		$GLOBALS['TSFE']->additionalHeaderData['he_tools'] = '<script src="' . t3lib_extMgm::siteRelPath('he_tools') . 'res/online_sb.js" type="text/javascript"></script>';
		$GLOBALS['TSFE']->additionalHeaderData['he_tools'] .= '<link rel="stylesheet" type="text/css" href="' . t3lib_extMgm::siteRelPath('he_tools') . 'res/online_sb.css" />';
	}
	
	function pruefeAnmeldeZustand() {
		if ($this->userPesonalisiertEingeloggt()) {
			$id = $GLOBALS['TSFE']->id;
			$name = $GLOBALS['TSFE']->fe_user->user['name'];
			$out = '<h3 class="error">Achtung, sie Sind als "' . $name . '" eingeloggt.</h3>';
			$out .= '<h4>Bitte klicken Sie auf den folgenden Link, um sich abzumelden: ' .
							'<a class="button" id="feUserLogout" href="#">Abmelden</a></h4>
					<script type="text/javascript">
					$("#feUserLogout").click(function(){
						return ajaxExecute("index.php?eID=he_tools&action=fe_logout","",true);
					});
					</script>';
			return $out;
		}
	}
	
	function userPesonalisiertEingeloggt() {
		$usergroups = explode(',',$GLOBALS['TSFE']->fe_user->user['usergroup']);
		if (count($usergroups)>1 && !in_array(self::$anonymerGast,$usergroups)) {
			return TRUE;
		}
		return FALSE;
	}
	
	function benutzerAnlegen() {
		$out = '';
		if (!$this->userPesonalisiertEingeloggt()) {
			$minChars = 8;
			$post = t3lib_div::_POST();
			$username = '';
			$anmeldungOk = FALSE;
			if (!empty($post['createFeUser']) && 
					!empty($post['benutzer']) && 
					!empty($post['password1']) && 
					!empty($post['password2'])) {
				$username = $post['benutzer'];
				if ($post['password1']!=$post['password2']) {
					$out .= '<h3 class="error">Die beiden Passwörter stimmen nicht überein.</h3>';
				} else if (strlen($post['password1'])<$minChars) {
					$out .= '<h3 class="error">Die beiden Passwörter müssen mindestens ' . $minChars . ' Zeichen lang sein.</h3>';
				} else {
					$out .= $this->createFeuser($username,$post['password1'],$anmeldungOk);
				}
			}
			$name = $GLOBALS['TSFE']->fe_user->user['username'];
			if (strpos($name,self::$kuerzelGastuser)!==FALSE) {
				$name = substr($name,strlen(self::$kuerzelGastuser));
			}
			if (!empty($name)) {
				$out .= '<h3 class="error">Sie Sind momentan als "' . $name . '" eingeloggt.<br />
								Um einen anonymen Benutzer anzulegen müssen Sie sich erst abmelden</h3>';
				$out .= '<h4>Bitte klicken Sie auf den folgenden Link, um sich abzumelden: ' .
								'<a class="button" id="feUserLogout" href="#">Abmelden</a></h4>
						<script type="text/javascript">
						$("#feUserLogout").click(function(){
							return ajaxExecute("index.php?eID=he_tools&action=fe_logout","",true);
						});
						</script>';
			} else if (!$anmeldungOk) {
				$id = $GLOBALS['TSFE']->id;
				$typolink_conf = array(
					'parameter' => $id,
					'returnLast' => 'url'
				);
				$formUrl = $this->piBase->cObj->typolink('',$typolink_conf);
				$out .= '<h1>Neu hier?<br />Anonyme Registrierung</h1>
								<form class="registration" action="' .  $formUrl . '" method="POST">
								<div class="row">
								<label for="benutzer">Benutzername:</label>
								<input type="text" size="40" id="benutzer" name="benutzer" value="' . $username . '">
								</div>
								<div class="row">
								<label for="password1">Passwort (mindestens ' . $minChars . ' Zeichen):</label>
								<input type="password" size="40" id="password1" name="password1">
								</div>
								<div class="row">
								<label for="password2">Passwort wiederholen:</label>
								<input type="password" size="40" id="password2" name="password2">
								</div>
								<div class="row">
								<input type="submit" name="createFeUser" value="Benutzer anlegen">
								</div>
								</form>
								<script type="text/javascript">
								$("#benutzer").blur(function(){
									var username = $(this).val();
									checkUsername(username,"' . self::$kuerzelGastuser . '");
								});
								$("form.registration").submit(function(){
									var username = "' . self::$kuerzelGastuser . '" + $(this).val();
									var usernameOk = checkUsername(username,"' . self::$kuerzelGastuser . '");
									var password1 = $("#password1").val();
									var password2 = $("#password2").val();
									var passwordOk = checkPasswords(password1,password2,' . $minChars . ');
									return (usernameOk && passwordOk); 
								});
								</script>';
			}
		}
		return $out;
	}
	
	public function zeigeRegistrierungsdaten() {
		$get = t3lib_div::_GET();
		if (!empty($get['registrationData'])) {
			$regData = unserialize(base64_decode($get['registrationData']));
			if (!empty($regData['un']) && !empty($regData['pw'])) {
				$out .= '<h2>Ein Benutzer mit den folgenden Zugangsdaten wurde eingerichtet:</h2>';
				$out .= 'Benutzername: ' . $regData['un'] .
								'<br />Passwort: ' . $regData['pw'] .
								'<br /><h3>Bitte notieren Sie sich die Zugangsdaten, um sich in Zukunft in der Online-Studienberatung anzumelden.</h3>';
			}	
		} else {
			$out = '';
		}
		return $out;
	}
	
	protected function createFeuser($username,$password,&$anmeldungOk) {
		$data['pid'] = self::$pidGastuser;
		$data['crdate'] = time();
		$data['tstamp'] = time();
		$data['disable'] = 0;
		$data['deleted'] = 0;
		$data['username'] = self::$kuerzelGastuser . $username;
		$data['password'] = md5($password);
		$data['usergroup'] = self::$anonymerGast;
		$result = $GLOBALS['TYPO3_DB']->exec_INSERTquery('fe_users',$data);
		if ($result) {
			$anmeldungOk = true;
			$erg = $this->benutzerEinloggen($username,$password);
			if (!$erg) {
				$out .= '<h3 class="error">Beim Login gab es einen Fehler.</h3>';
			} else {
				/* Umleitung auf die konfigurierte Seite */
				$redirectPid = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_hetools_pi1.']['sb_online.']['redirect_pid'];
				if (empty($redirectPid)) {
					$redirectPid = $GLOBALS['TSFE']->id;
				}
				$regData = base64_encode(serialize(array('un'=>$username,'pw'=>$password)));
				$typolink_conf = array(
						'parameter' => $redirectPid,
						'additionalParams' => '&registrationData=' . $regData,
						'returnLast' => 'url'
				);
				$redirectUrl = $this->piBase->cObj->typolink('',$typolink_conf);
//t3lib_utility_Debug::debugInPopUpWindow('createFeuser');
				t3lib_utility_Http::redirect($redirectUrl);
				exit();
			}
		} else {
			$out .= '<h3 class="error">Ihre Benutzerkennung konnte nicht angelegt werden!</h3>';
			$anmeldungOk = false;
		}
		return $out;
	}
	
	public function logoutFormular() {
		$name = $GLOBALS['TSFE']->fe_user->user['username'];
		if (strpos($name,self::$kuerzelGastuser)!==FALSE) {
			$name = substr($name,strlen(self::$kuerzelGastuser));
		}
		if (!empty($name)) {
			$redirectPid = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_hetools_pi1.']['sb_online.']['redirect_pid'];
			$typolink_conf = array(
					'parameter' => $redirectPid,
					'returnLast' => 'url'
			);
			$redirectUrl = $this->piBase->cObj->typolink('',$typolink_conf);
			$out .= '<h3 class="error">Sie Sind momentan als "' . $name . '" eingeloggt.</h3>';
			$out .= '<h4>Bitte klicken Sie auf den folgenden Link, um sich abzumelden: ' .
							'<a class="button" id="feUserLogout" href="#">Abmelden</a></h4>
					<script type="text/javascript">
					$("#feUserLogout").click(function(){
						return ajaxExecute("index.php?eID=he_tools&action=fe_logout","' . $redirectUrl . '",true);
					});
					</script>';
		}
		return $out;
	}
	
	public function loginFormular() {
		$post = t3lib_div::_POST();
//t3lib_div::devlog("HE-Tools: loginFormular","tx_hetools_pi1",0,$post);
		
		$username = '';
		$minChars = 8;
		$anmeldungOk = FALSE;		
		if (!empty($post['login']) &&
				!empty($post['benutzer']) &&
				!empty($post['password'])) {
			$erg = $this->benutzerEinloggen($post['benutzer'],$post['password']);
			if (!$erg) {
				$out .= '<h3 class="error">Beim Login gab es einen Fehler.</h3>';
			} else {
				$redirectPid = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_hetools_pi1.']['sb_online.']['redirect_pid'];
				if (empty($redirectPid)) {
					$redirectPid = $GLOBALS['TSFE']->id;
				}
				$typolink_conf = array(
						'parameter' => $redirectPid,
						'returnLast' => 'url'
				);
				$redirectUrl = $this->piBase->cObj->typolink('',$typolink_conf);
//t3lib_utility_Debug::debugInPopUpWindow('loginOk');
				t3lib_utility_Http::redirect($redirectUrl);
				exit();
			}
		}
		
		$name = $GLOBALS['TSFE']->fe_user->user['username'];
		if (strpos($name,self::$kuerzelGastuser)!==FALSE) {
			$name = substr($name,strlen(self::$kuerzelGastuser));
		}
		if (!$anmeldungOk) {
			$id = $GLOBALS['TSFE']->id;
			$typolink_conf = array(
					'parameter' => $id,
					'returnLast' => 'url'
			);
			$formUrl = $this->piBase->cObj->typolink('',$typolink_conf);
			$out .= '<h1>Schon hier gewesen?<br />Anonymer Login</h1>
							<form class="login" action="' .  $formUrl . '" method="POST">
							<div class="row">
							<label for="benutzer">Benutzername:</label>
							<input type="text" size="40" id="benutzer" name="benutzer" value="' . $username . '">
							</div>
							<div class="row">
							<label for="password1">Passwort (mindestens ' . $minChars . ' Zeichen):</label>
							<input type="password" size="40" id="password" name="password">
							</div>
							<div class="row">
							<input type="submit" name="login" value="Einloggen">
							</div>
							</form>
							';
		}
		return $out;
	}
	
	protected function benutzerEinloggen($username,$password) {
		$loginData = array(
				'uname' => self::$kuerzelGastuser . $username, 
				'uident_text' => md5($password), 
				'status' => 'login'
		);
		$GLOBALS['TSFE']->fe_user->checkPid = 0;
		$info = $GLOBALS['TSFE']->fe_user->getAuthInfoArray();
		$user = $GLOBALS['TSFE']->fe_user->fetchUserRecord($info['db_user'],$loginData['uname']);
		$ok = $GLOBALS['TSFE']->fe_user->compareUident($user,$loginData,'normal');
		if ($ok) {
			//login successfull
			$GLOBALS['TSFE']->fe_user->createUserSession($user);
			return true;
		} else {
			return false;
		}
	}
	
	public function erzeugeNeueAnfrage($thema,$themaId,$zielgruppe,$anfrage,&$benutzer) {
		if (strpos($benutzer,self::$kuerzelGastuser)===FALSE) {
			$benutzer = self::$kuerzelGastuser . $benutzer;
		}
		$data['pid'] = $this->pidAnfragen;
		$data['deleted'] = 0;
		$data['hidden'] = 0;
		$data['tstamp'] = time();
		$data['crdate'] = time();
		$data['thema'] = trim($thema);
		$data['themaId'] = $themaId;
		$data['zielgruppe'] = trim($zielgruppe);
		$data['anfrage'] = $anfrage;
		$data['username'] = $benutzer;
		$data['nr'] = 0;
		$data['original_id'] = 0;
		$result = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_hetools_sb_online_anfragen',$data);
		$uid = $GLOBALS['TYPO3_DB']->sql_insert_id();
		if (strpos($name,self::$kuerzelGastuser)!==FALSE) {
			$benutzer = substr($benutzer,strlen(self::$kuerzelGastuser));
		}
		return $uid;
	}
	
	public function antwortSpeichern($anfrageId,$antwort,$nr) {
		$data['pid'] = $this->pidAnfragen;
		$data['deleted'] = 0;
		$data['hidden'] = 0;
		$data['tstamp'] = time();
		$data['crdate'] = time();
		$data['thema'] = '';
		$data['themaId'] = '';
		$data['zielgruppe'] = '';
		$data['anfrage'] = $antwort;
		$data['username'] = $GLOBALS['TSFE']->fe_user->user['username'];
		$data['nr'] = $nr;
		$data['original_id'] = $anfrageId;
		$result = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_hetools_sb_online_anfragen',$data);
		$uid = $GLOBALS['TYPO3_DB']->sql_insert_id();
		return $uid;
	}
	
	public function anfrageBeantwortet($uid) {
		$where = 'deleted=0 AND hidden=0 AND (uid=' . $uid . ' OR original_id=' . $uid . ')';
		$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('nr,username,original_id','tx_hetools_sb_online_anfragen',$where,'','nr');
		$beantwortet = FALSE;
		while ($data = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
			if ($data['original_id']==0) {
				$originalUsername = $data['username'];
			}
			if ($data['username']!=$originalUsername) {
				$beantwortet = TRUE;
			} else {
				$beantwortet = FALSE;
			}
		}
		return $beantwortet;
	}
	
	public function gibAnfragenListeAus(&$anfragenListe,$eingeklappt=TRUE) {
		if (!$eingeklappt) {
			$cssClassDt = ' class="expanded" ';
		} else {
			$cssClassDt = '';
		}
		
		$out .= '<h2>Anfrage vom ' . $anfragenListe['datum'] . '<br />Thema: ' . $anfragenListe['thema'] . '</h2>';
		$out .= '<dl class="anfrage">';
		foreach ($anfragenListe['daten'] as $eintrag) {
			$out .= '<dl class="' . $eintrag['mode'] . '">';
			if ($eintrag['last']) {
				if (!$eingeklappt) {
					$cssClassDd = ' class="last" ';
				} else {
					$cssClassDd = ' class="last" ';
				}
				if ($eintrag['mode']=='anfrage') {
					$label = 'Letzte Frage: ';
				} else {
					$label = 'Letzte Antwort: ';
				}
				$out .= '<dt' . $cssClassDt . '>' . $label . '</dt><dd' . $cssClassDd . '>' . $eintrag['text'] . '</dd>';
			} else {
				if ($eintrag['mode']=='anfrage') {
					$label = 'Frage: ';
				} else {
					$label = 'Antwort: ';
				}
				$out .= '<dt' . $cssClassDt . '>' . $label . '</dt><dd>' . $eintrag['text'] . '</dd>';
			}
		}
		foreach ($anfragenListe['daten'] as $eintrag) {
			$out .= '</dl>';
		}
		$out .= '</dl>';
		return $out;
	}
	
	public function gibAnfragenHistorie($anfrageId,&$letzteNr) {
		$where = 'deleted=0 AND hidden=0 AND ' . 
						 '(uid=' . $anfrageId . ' OR original_id=' . $anfrageId . ')';
		$abfrageLetzter = $GLOBALS['TYPO3_DB']->exec_SELECTquery('crdate,original_id,thema,anfrage,nr,username','tx_hetools_sb_online_anfragen',$where,'','nr DESC');
		$data = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageLetzter);
		$letzteAnfrageNr = $data['nr'];
		$anfragenListe = array();
		$anfragenListe['daten'] = array();
		$letzteNr = -1;
		$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('crdate,original_id,thema,anfrage,nr,username','tx_hetools_sb_online_anfragen',$where,'','nr');
		while ($data = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
			if ($data['original_id']==0) {
				$anfragenListe['thema'] = $data['thema'];
				$anfragenListe['user'] = $data['username'];
				$anfragenListe['datum'] = date('d.m.Y',$data['crdate']);
			}
			if ($data['username']==$anfragenListe['user']) {
				$eintrag['mode'] = 'anfrage';
			} else {
				$eintrag['mode'] = 'antwort';
			}
			$eintrag['text'] = nl2br($data['anfrage']);
			if ($letzteAnfrageNr==$data['nr']) {
				$eintrag['last'] = TRUE;
			} else {
				$eintrag['last'] = FALSE;
			}
			$anfragenListe['daten'][] = $eintrag;
			$letzteNr = $data['nr'];
		}	
		return $anfragenListe;
	}
	
	public function gibBenutzeranfragen($username) {		
		$where = 'deleted=0 AND hidden=0 AND original_id=0 AND username="' . $username . '"';
		$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,crdate,thema','tx_hetools_sb_online_anfragen',$where,'','uid');
		$anfragenListe = array();
		while ($data = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
			$datum = date('d.m.Y',$data['crdate']);
			$beantwortet = $this->anfrageBeantwortet($data['uid']);
			$anfragenListe[$data['uid']] = array('datum'=>$datum,'thema'=>$data['thema'],'beantwortet'=>$beantwortet);
		}
		return $anfragenListe;
	}
	
	public function gibBenutzerAnfragenListeAus($anfragenListe) {
		$out = '<ul class="anfragen">';
		$id = $GLOBALS['TSFE']->id;
		foreach($anfragenListe as $uid=>$daten) {
			if ($daten['beantwortet']) {
				$cssClass = ' class="gruen" ';
			} else {
				$cssClass = ' class="rot" ';
			}
			$out .= '<li ' . $cssClass . '><a href="index.php?id=' .  $id . '&anfrageId=' . $uid . '">' . $daten['datum'] . ': ' . $daten['thema'] . '</a></li>';
		}
		$out .= '</ul>';
		return $out;
	}
	
	public function anfragenstatistikAnzeigen() {
		$sqlBenutzer = $GLOBALS['TYPO3_DB']->sql_query('SELECT DISTINCT username FROM tx_hetools_sb_online_anfragen WHERE deleted=0 AND hidden=0 AND nr=0');
		$anzBenutzer = $GLOBALS['TYPO3_DB']->sql_num_rows($sqlBenutzer);
		$sqlAnfragen = $GLOBALS['TYPO3_DB']->sql_query('SELECT DISTINCT uid FROM tx_hetools_sb_online_anfragen WHERE deleted=0 AND hidden=0 AND nr=0');
		$anzAnfragen = $GLOBALS['TYPO3_DB']->sql_num_rows($sqlAnfragen);
		$sqlNachfragen = $GLOBALS['TYPO3_DB']->sql_query('SELECT anfragen1.uid FROM tx_hetools_sb_online_anfragen as anfragen1
Inner JOIN tx_hetools_sb_online_anfragen as anfragen2 ON
anfragen2.uid = anfragen1.original_id 
WHERE anfragen1.deleted=0 AND anfragen1.hidden=0 AND  
			anfragen2.deleted=0 AND anfragen2.hidden=0 AND  
			anfragen1.username=anfragen2.username');
		$anzNachfragen = $GLOBALS['TYPO3_DB']->sql_num_rows($sqlNachfragen);
		$sqlAntworten = $GLOBALS['TYPO3_DB']->sql_query('SELECT anfragen1.uid FROM tx_hetools_sb_online_anfragen as anfragen1
Inner JOIN tx_hetools_sb_online_anfragen as anfragen2 ON
anfragen2.uid = anfragen1.original_id 
WHERE anfragen1.deleted=0 AND anfragen1.hidden=0 AND  
			anfragen2.deleted=0 AND anfragen2.hidden=0 AND  
			anfragen1.username<>anfragen2.username');
		$anzAntworten = $GLOBALS['TYPO3_DB']->sql_num_rows($sqlAntworten);
//		$sqlGesamt = $GLOBALS['TYPO3_DB']->sql_query('SELECT uid FROM tx_hetools_sb_online_anfragen WHERE deleted=0 AND hidden=0');
		$anzGesamt = $anzAnfragen+$anzNachfragen+$anzAntworten;
		
/* Studieninteressierte */
		$sqlAnfragenStudienInteressierte = $GLOBALS['TYPO3_DB']->sql_query('SELECT DISTINCT uid FROM tx_hetools_sb_online_anfragen WHERE zielgruppe LIKE "Studieninteressierte%" AND deleted=0 AND hidden=0 AND nr=0');
		$anzAnfragenStudienInteressierte = $GLOBALS['TYPO3_DB']->sql_num_rows($sqlAnfragenStudienInteressierte);
		$sqlNachfragenStudienInteressierte = $GLOBALS['TYPO3_DB']->sql_query('SELECT anfragen1.uid FROM tx_hetools_sb_online_anfragen as anfragen1
				Inner JOIN tx_hetools_sb_online_anfragen as anfragen2 ON
				anfragen2.uid = anfragen1.original_id
				WHERE anfragen1.deleted=0 AND anfragen1.hidden=0 AND
				anfragen2.deleted=0 AND anfragen2.hidden=0 AND 
				anfragen2.zielgruppe LIKE "Studieninteressierte%" AND 
				anfragen1.username=anfragen2.username');
		$anzNachfragenStudienInteressierte = $GLOBALS['TYPO3_DB']->sql_num_rows($sqlNachfragenStudienInteressierte);
		$sqlAntwortenStudienInteressierte = $GLOBALS['TYPO3_DB']->sql_query('SELECT anfragen1.uid FROM tx_hetools_sb_online_anfragen as anfragen1
				Inner JOIN tx_hetools_sb_online_anfragen as anfragen2 ON
				anfragen2.uid = anfragen1.original_id
				WHERE anfragen1.deleted=0 AND anfragen1.hidden=0 AND
				anfragen2.deleted=0 AND anfragen2.hidden=0 AND
				anfragen2.zielgruppe LIKE "Studieninteressierte%" AND 
				anfragen1.username<>anfragen2.username');
		$anzAntwortenStudienInteressierte = $GLOBALS['TYPO3_DB']->sql_num_rows($sqlAntwortenStudienInteressierte);
		$anzGesamtStudienInteressierte = $anzAnfragenStudienInteressierte+$anzNachfragenStudienInteressierte+$anzAntwortenStudienInteressierte;
		
		/* Studierende */
		$sqlAnfragenStudierende = $GLOBALS['TYPO3_DB']->sql_query('SELECT DISTINCT uid FROM tx_hetools_sb_online_anfragen WHERE zielgruppe LIKE "Studierende%" AND deleted=0 AND hidden=0 AND nr=0');
		$anzAnfragenStudierende = $GLOBALS['TYPO3_DB']->sql_num_rows($sqlAnfragenStudierende);
		$sqlNachfragenStudierende = $GLOBALS['TYPO3_DB']->sql_query('SELECT anfragen1.uid FROM tx_hetools_sb_online_anfragen as anfragen1
				Inner JOIN tx_hetools_sb_online_anfragen as anfragen2 ON
				anfragen2.uid = anfragen1.original_id
				WHERE anfragen1.deleted=0 AND anfragen1.hidden=0 AND
				anfragen2.deleted=0 AND anfragen2.hidden=0 AND
				anfragen2.zielgruppe LIKE "Studierende%" AND
				anfragen1.username=anfragen2.username');
		$anzNachfragenStudierende = $GLOBALS['TYPO3_DB']->sql_num_rows($sqlNachfragenStudierende);
		$sqlAntwortenStudierende = $GLOBALS['TYPO3_DB']->sql_query('SELECT anfragen1.uid FROM tx_hetools_sb_online_anfragen as anfragen1
				Inner JOIN tx_hetools_sb_online_anfragen as anfragen2 ON
				anfragen2.uid = anfragen1.original_id
				WHERE anfragen1.deleted=0 AND anfragen1.hidden=0 AND
				anfragen2.deleted=0 AND anfragen2.hidden=0 AND
				anfragen2.zielgruppe LIKE "Studierende%" AND
				anfragen1.username<>anfragen2.username');
		$anzAntwortenStudierende = $GLOBALS['TYPO3_DB']->sql_num_rows($sqlAntwortenStudierende);
		$anzGesamtStudierende = $anzAnfragenStudierende+$anzNachfragenStudierende+$anzAntwortenStudierende;
		
		
		$out .= '<h1>Nutzungsstatistik für anonyme Anfragen</h1>';
		$out .= '<table class="rahmen">';
		$out .= '<tr><th></th><th>Gesamt</th><th>Studieninteressierte</th><th>Studierende</th></tr>';
		$out .= '<tr><th>Anzahl Besucher: </th><td>' . $anzBenutzer . '</td><td> </td><td> </td></tr>';
		$out .= '<tr><th>Anzahl Anfragen: </th><td>' . $anzAnfragen . '</td><td>' . $anzAnfragenStudienInteressierte . '</td><td>' . $anzAnfragenStudierende . '</td></tr>';
		$out .= '<tr><th>Anzahl Nachfragen: </th><td>' . $anzNachfragen . '</td><td>' . $anzNachfragenStudienInteressierte . '</td><td>' . $anzNachfragenStudierende . '</td></tr>';
		$out .= '<tr><th>Anzahl Antworten: </th><td>' . $anzAntworten . '</td><td>' . $anzAntwortenStudienInteressierte . '</td><td>' . $anzAntwortenStudierende . '</td></tr>';
		$out .= '<tr><th>Anzahl Einträge gesamt: </th><td>' . $anzGesamt . '</td><td>' . $anzGesamtStudienInteressierte . '</td><td>' . $anzGesamtStudierende . '</td></tr>';
		$out .= '</tbody></table>';
		return $out;
	}
	
	public function gibThemenEmail($anfrageId) {
		$beantwortet = $this->anfrageBeantwortet($anfrageId);
		if (!$beantwortet) {
			$email = '';
			$where = 'deleted=0 AND hidden=0 AND uid=' . $anfrageId;
			$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('themaId','tx_hetools_sb_online_anfragen',$where,'','nr');
			if ($data = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
				$themaId = $data['themaId'];
				$whereEmail = 'deleted=0 AND hidden=0 AND uid=' . $themaId;
				$abfrageEmail = $GLOBALS['TYPO3_DB']->exec_SELECTquery('email','tx_he_personen',$whereEmail);
				if ($dataEmail = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageEmail)) {
					$email = $dataEmail['email'];
				}
			}
		}
		return $email;
	}
	
	public function versendeBearbeiterEmail($anfrageId,$nachfrage,$email) {
		$initHtml =	'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Anonyme Anfrage</title>
		</head>
		<body >
		<div id="content"
		style="font-family: verdana, arial, helvetica, sans-serif; padding: 0 20px; font-size:80%;">
		';
		
		$exitHtml =	'
		</div>
		</body>
		</html>
		';
		
		$anfrageBearbeitenPid = $GLOBALS['TSFE']->id;
		$args = '&anfrageId=' . $anfrageId;
		$bearbeitungsLink = '<a href="http://www.hs-esslingen.de/index.php?id=' . $anfrageBearbeitenPid . $args . '">' .
												'Nachfrage bearbeiten</a>';
		
		$bodyHtml = '<p>Folgende Nachfrage wurde von einem anonymen Benutzer gestellt:</p>' .
								'<p><b>Nachfrage:</b><br/>' . $nachfrage . '</p>
								 <p><b>Zur Bearbeitung:</b> ' . $bearbeitungsLink . '</p>';
		$mail = t3lib_div::makeInstance('t3lib_mail_Message');
		$bodyPlain = preg_replace('/(<br>|<br \/>|<br\/>)\s*/i', PHP_EOL, $bodyHtml);
		$bodyPlain = strip_tags($bodyPlain);
		$mail->setFrom(array('no.reply@hs-esslingen.de'=>'Anonyme Anfrage'));
		$mail->setTo($email);
		$mail->setSubject('Nachfrage eines Benutzers');
		$htmlComplete = $initHtml .$bodyHtml . $exitHtml;
		$mail->setBody($htmlComplete, 'text/html');
		$mail->addPart($bodyPlain, 'text/plain');
		$erg = $mail->send();
		return $erg;
	}

	public function istAdmin() {
		$erg = FALSE;
		$usergroups = explode(',',$GLOBALS['TSFE']->fe_user->user['usergroup']);
		if (count($usergroups)>1 && in_array(self::$sbAdmin,$usergroups)) {
			$erg = TRUE;
		}
		return $erg;		
	}

	public function istStuda() {
		$erg = FALSE;
		$usergroups = explode(',',$GLOBALS['TSFE']->fe_user->user['usergroup']);
		if (count($usergroups)>1 && in_array(self::$studa,$usergroups)) {
			$erg = TRUE;
		}
		return $erg;		
	}

	public function istHiwi() {
		$erg = FALSE;
		$usergroups = explode(',',$GLOBALS['TSFE']->fe_user->user['usergroup']);
		if (count($usergroups)>1 && in_array(self::$sbHiwi,$usergroups)) {
			$erg = TRUE;
		}
		return $erg;		
	}

	public function zugrifferlaubt($anfrageId) {
		$zugriff = FALSE;
		$where = 'deleted=0 AND hidden=0 AND uid=' . $anfrageId;
		$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('username','tx_hetools_sb_online_anfragen',$where);
		if ($data = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
			$username = $GLOBALS['TSFE']->fe_user->user['username'];
			if ($data['username']==$username || 
					$this->istAdmin() || 
					$this->istStuda()
					) {
				$zugriff = TRUE;
			}
		}
		return $zugriff;
	}
	
	public function anfrageBearbeiten() {
		$get = t3lib_div::_GET();
		$post = t3lib_div::_POST();
		
		if (!empty($post['anfrageId'])) {
			$anfrageId = $post['anfrageId'];
		} else if (!empty($get['anfrageId'])) {
			$anfrageId = $get['anfrageId'];
		}
		if (!empty($anfrageId)) {
			$access = $this->zugrifferlaubt($anfrageId);
			if (!$access) {
				$meldung = '';
				$username = $GLOBALS['TSFE']->fe_user->user['username'];
				$meldung .= '<h3 class="error">Sie haben keine Berechtigung für diese Aktion!</h3>';
				if (strpos($username,self::$kuerzelGastuser)!==FALSE) {
					$meldung .= '<p>Sie sind als "' . $username . '" eingeloggt!. 
												Um Anfragen zu bearbeiten loggen Sie sich bitte stattdessen mit Ihrem Hochschul-Benutzernamen ein.</p>'; 
				}
				return $meldung;
			}
		}
		if (!empty($post['anfrageId'])) {
			$anfrageId = $post['anfrageId'];
			$antwort = $post['antwort'];
			$nr = $post['nr'];
			if (!empty($anfrageId) && !empty($antwort) && !empty($nr)) {
				$ergebnis = $this->antwortSpeichern($anfrageId,$antwort,$nr);
				if ($ergebnis) {
					$email = $this->gibThemenEmail($anfrageId);
					if ($email) {
						$this->versendeBearbeiterEmail($anfrageId,$antwort,$email);
					}
					$out = '<h2>Die Antwort wurde erfolgreich gespeichert</h2>';
					$anfragenListe = $this->gibAnfragenHistorie($anfrageId,$letzteNr);
					$out .= $this->gibAnfragenListeAus($anfragenListe,FALSE);
				} else {
					$out = '<h3 class="error">Beim Speichern der Antwort gab es einen Fehler!</h3>';
				}
			} else {
				$fehlerMeldung = '<h3 class="error">Bitte geben Sie einen Anfragetext ein!</h3>';
				$out = $this->gibAnfrageFormular($anfrageId,$fehlerMeldung);
			}
		} else {
			if (!empty($get['anfrageId'])) {
				$anfrageId = $get['anfrageId'];
				$out = $this->gibAnfrageFormular($anfrageId);
			} else {
				$out .= '<h2>Keine Anfrage-ID übergeben</h2>';
			}
		}
		return $out;
	}
	
	public function gibAnfrageFormular($anfrageId,$fehlerMeldung='') {
		$anfragenListe = $this->gibAnfragenHistorie($anfrageId,$letzteNr);
		$out = $this->gibAnfragenListeAus($anfragenListe,FALSE);
		$nr = $letzteNr+1;
		$id = $GLOBALS['TSFE']->id;
		$beantwortet = $this->anfrageBeantwortet($anfrageId);
		$out .= $fehlerMeldung;
		if ($beantwortet) {
			$out .= '<h2>Nachfrage erstellen</h2>';
		} else {
			$out .= '<h2>Anfrage beantworten</h2>';
		}
		$typolink_conf = array(
			'parameter' => $id,
			'returnLast' => 'url'
		);
		$formUrl = $this->piBase->cObj->typolink('',$typolink_conf);
		$out .= '<form class="antwort" action="' .  $formUrl . '" method="POST">
		<input type="hidden" name="nr" value="' . $nr . '">
		<input type="hidden" name="anfrageId" value="' . $anfrageId . '">
		<div class="row">
		<textarea rows="20" cols="80" name="antwort"></textarea>
		</div>
		<div class="row">
		<input type="submit" name="absenden" value="Absenden">
		</div>
		</form>
		';
		return $out;
	}
	
	public function anfragenAnzeigen() {
		$out = '<h2>Ihre Anfragen</h2>';
		$out .= '<p>Offene Anfragen sind <b class="rot">rot</b>, beantwortete Fragen
						<b class="gruen">grün</b> markiert.</p>';
		$username = $GLOBALS['TSFE']->fe_user->user['username'];
		if (strpos($username,self::$kuerzelGastuser)===FALSE) {
			$username = self::$kuerzelGastuser . $username;
		}
		$anfrageListe = $this->gibBenutzeranfragen($username);
		$out .= $this->gibBenutzerAnfragenListeAus($anfrageListe,FALSE);
		$get = t3lib_div::_GET();
		if (!empty($get['anfrageId'])) {
			$anfragenListe = $this->gibAnfragenHistorie($get['anfrageId'],$letzteNr);
			$out .= $this->gibAnfragenListeAus($anfragenListe,FALSE);
			$anfragenBearbeitenPid = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_hetools_pi1.']['sb_online.']['anfragen_bearbeiten_pid'];
			$anfragenNeuPid = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_hetools_pi1.']['sb_online.']['anfragen_neu_pid'];
			$out .= '<div class="buttons">
								<a class="button" href="index.php?id=' . $anfragenBearbeitenPid . '&anfrageId=' . $get['anfrageId'] . '">Nachfrage stellen</a>
								<a class="button" href="index.php?id=' . $anfragenNeuPid . '&anfrageId=' . $get['anfrageId'] . '">Neue Anfrage stellen</a>
							</div>';
		} else {
			$anfragenNeuPid = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_hetools_pi1.']['sb_online.']['anfragen_neu_pid'];
			$out .= '<div class="buttons">
			<a class="button" href="index.php?id=' . $anfragenNeuPid . '">Neue Anfrage stellen</a>
			</div>';
			
		}
		return $out;	
	}
	
	
}
?>