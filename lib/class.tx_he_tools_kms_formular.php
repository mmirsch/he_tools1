<?php
class tx_he_tools_kms_formular  {
public $dbRes;
public $dB;

	public function initDb() {
		if (!($this->dbRes instanceof t3lib_db)) {
			$this->dbRes= t3lib_div::makeInstance('t3lib_db');
			$this->db = $this->dbRes->sql_pconnect("rzlx2000.hs-esslingen.de", "kmswwwuser", "cnG8fBvvF6sGKER4") or die("Unable to connect to database");
			$this->dbRes->sql_select_db("rz",$this->db) or die('Could not select database.');
		}
	}

	public function disconnectDb() {
		return $this->dbRes->connectDB();
	}
	
	public function sqlQuery($query) {
		$this->initDb();
    return $this->dbRes->sql_query($query);
	}
	
	public function sqlFetchAssoc($abfrage) {
		return $this->dbRes->sql_fetch_assoc($abfrage);
	}
	
	public function sqlInsert($table,$data,$noQuoteFields = FALSE) {
		$this->initDb();
		return $this->dbRes->exec_INSERTquery($table,$data,$noQuoteFields);
	}
	
	public function sqlUpdate($table,$where,$data) {
		$this->initDb();
		return $this->dbRes->UPDATEquery($table,$where,$data);
	}
	
	public function formularAnzeigen() {
		$GLOBALS['TSFE']->additionalHeaderData ['he_tools_css_1'] = '<link href="' . t3lib_extMgm::siteRelPath('he_tools') . 'res/css/kms_form.css" rel="stylesheet" type="text/css" />';
		
		$username = $GLOBALS['TSFE']->fe_user->user['username'];
		$top = '';
		$middle = '';
		$bottom = '';

		
		$slqQuery = 'SELECT * FROM kms WHERE login="' . $username . '"';
		$abfragePages = $this->sqlQuery($slqQuery);
		$data = $this->sqlFetchAssoc($abfragePages);
		$post = t3lib_div::_POST();
		$zustimmung = $post['zustimmung'];
		$anmelden = $post['anmelden'];
		$abbrechen = $post['abbrechen'];
		if (!empty($anmelden)) {
			if (empty($zustimmung)) {
				$error = '<h3 class="error">Sie müssen den Nutzungsrechten zustimmen!</h3>';
				$bottom = $this->gibAntragsFormular($error);
			} else {
				if (empty($data)) {
					/* Neuer Antrag: E-Mail-Versand */
					$top = $this->kmsEintragSpeichern($username);
				} else {
					/* Vorhandener Antrag: kein E-Mail-Versand */
					$top = 'Ihr KMS-Antrag wurde soeben gespeichert';
				}
				$bottom = $this->gibAnleitung();
				
			}
		} else if (!empty($abbrechen)) {
			$bottom = $this->gubAbbrechenText();
		} else {
			if (!empty($data)) {
				$top = $this->kmsEintragVorhanden($data['idate']);
				$bottom = $this->gibAnleitung();
// Zum Testen
//$bottom = $this->gibAntragsFormular();
			} else {
				$bottom = $this->gibAntragsFormular();
			}
		}
				
		$out = '<div class="kms_form">' . $top . $bottom . '</div>';
		$this->disconnectDb();
		return $out;
	}
	
	function gibAntragsFormular($error='') {
		$msLink = '<a href="http://www.microsoft.com/de-de/licensing/about-licensing/product-licensing.aspx">';
		$rzLink = '<a href="http://www2.hs-esslingen.de/work/projekt-RZ/fh-intern/Software_Bezug/HE_MS_Hinweise_zur_Aktivierung.pdf">';
		$out .= '<h2>Aktivierung von über das Campus Agreement bezogenen Lizenzen von \'MS Windows Enterprise\' und \'MS Office Professional\' über OpenVPN</h2>
<p>Die auf Rechnern außerhalb der Hochschule genutzten Installationen von \'MS Windows Enterprise\' und \'MS Office Professional\' müssen spätestens alle 6 Monate immer wieder neu aktiviert werden.</p>
<p>Damit diese Aktivierung erfolgen kann, muss Ihr Hochschul-Account dafür freigeschalten werden.</p>
<p>Es wird ausdrücklich darauf hingewiesen, dass Sie Produkte, für die Sie bereits eine Lizenz besitzen, 
	 nur dann (re-)aktivieren dürfen, wenn Sie die Nutzungsrechte und -beschränkungen für die oben genannten 
	 Software-Produkte gelesen und verstanden haben und mit den Bestimmungen einverstanden sind.</p>
<p>Die ' . $msLink . 'Produktbenutzungsrechte (Product Use Rights - PURs)</a> hierzu können ' . $msLink . 'online</a> eingesehen werden. </p>
<p>Bitte beachten Sie auch die ' . $rzLink . 'Ausführungen des Rechenzentrums</a>.</p>
<p>Das Rechenzentrum der Hochschule Esslingen weist darauf hin, dass Ihre hiermit geforderte Zustimmung vorschriftsgemäß, 
	lediglich für hochschul-interne Zwecke, mitprotokolliert wird.</p>
';

		$out .= $error;
		$out .= '<form action="" method="post">';
/*
$out .= '<div class="zustimmung">
				 <input id="zustimmung1" name="zustimmung1" type="checkbox" value="zustimmung" />
				 <label for="zustimmung1">Ich habe verstanden, dass ich keinerlei technische Unterstützung im Zusammenhang mit einem Heimarbeitsplatz erhalte.</label>
				 </div>';

$out .= '<div class="zustimmung">
				 <input id="zustimmung2" name="zustimmung2" type="checkbox" value="zustimmung" />
				<label for="zustimmung2">Ich habe verstanden, dass die so aktivierte Software nach Ablauf der Lizenzvereinbarung mit Microsoft wieder von dem Gerät entfernt werden muss. Aktueller Stichtag ist der 30.04.2017.</label>
				</div>';

$out .= '<div class="zustimmung">
				 <input id="zustimmung3" name="zustimmung3" type="checkbox" value="zustimmung" />
				 <label for="zustimmung3">Ich habe verstanden, dass ich die so aktivierte Software nicht für private Zwecke nutzen darf.</label>
				 </div>';

$out .= '<div class="zustimmung">
				 <input id="zustimmung4" name="zustimmung4" type="checkbox" value="zustimmung" />
				 <label for="zustimmung4">Ich habe verstanden, dass ich die Informationen die ich hier erhalte nicht weitergeben darf.</label>
				 </div>';
*/				 
	 
		$out .= '<div class="zustimmung">
						 <input id="zustimmung" name="zustimmung" type="checkbox" />
						 <label for="zustimmung">Ich bestätige hiermit, dass ich die Nutzungsrechte und -beschränkungen für die oben genannten Software-Produkte gelesen habe und diesen zustimme.</label>
						 </div>';
/*
		$out .= '<div class="zustimmung">
						 <input id="datenschutz" name="datenschutz" type="checkbox" />
						 <label for="datenschutz">Ich bestätige hiermit, dass ich mit der Speicherung meiner Daten durch das Rechenzentrum der Hochschule Esslingen einverstanden bin.</label>
						 </div>';
*/						 
		$out .= '<input id="anmelden" type="submit" name="anmelden" value="Verbindlich anmelden" />';
		$out .= '<input id="abbrechen" type="submit" name="abbrechen" value="Abbrechen" />';
		$out .= '</form></p>';
		return $out;
	}
	
	function gubAbbrechenText() {
		$out .= '<h2>Die Registrierung wurde abgebrochen</h2>';
		return $out;
	}
	
	function gibAnleitung() {
		$pfad = 'fileadmin/medien/einrichtungen/Rechenzentrum/Softwareangebot/kms/';
		$dateien = array('kms_aktivierung_windows.bat','kms_aktivierung_office.bat');
		$out.= '<h2>Anleitung</h2>';
		$out .= '<p>Um Windows Enterprise oder Office Professional zu aktivieren müssen Sie einen passenden CD-Key und einen entsprechenden KMS-Server eintragen.</p>
						<p>Dazu benutzen Sie bitte die beiden Batch-Dateien:</p>
						<ul>
						';
		foreach ($dateien as $dateiname) {
			$out .= '<li><a href="' . $pfad . $dateiname . '">' . $dateiname . '</a></li>';
		}
		$out .= '</ul>
						 <p>Einfach herunterladen und mit Rechtsklick->"als Administrator ausführen" starten.</p>
						 <p>Ab diesem Zeitpunkt erneuert Windows die Aktivierungen immer selbst, sobald Sie eine Verbindung mit OpenVPN hergestellt haben.</p>
						 <p>Sollte Ihr Notebook/PC vom Rechenzentrum installiert worden sein, so sind diese Vorbereitungen bereits durchgeführt und Sie müssen nichts weiter tun als Sich mindestens alle 6 Monate einmal über OpenVPN mit dem Hochschulnetz zu verbinden.</p>
						 <p>Bei Fragen wenden Sie sich bitte an das <a href="mailto:rz+kms@hs-esslingen.de">KMS-Team des Rechenzentrums der Hochschule Esslingen</a>.</p>
		';
		return $out;
	}

	function kmsEintragVorhanden($datum) {
		$name = $GLOBALS['TSFE']->fe_user->user['name'];
		$out = '<p>Sehr geehrte/r ' . $name . ',<br />
						Sie haben den Microsoft Linzenz-Antrag bereits zu folgendem Zeitpunkt abgesendet:<br/>' .
						$datum . '</p>';
		return $out;
	}
	
	function kmsEintragSpeichern($username) {
		$zeit =  strftime("%d.%m.%Y %H:%M:S",time());
		
		$data['login'] = $username;
		$data['idate'] = 'now()';
//		$data['locked'] = 'no';
		$res = $this->sqlInsert('kms',$data,'idate');
		
		$this->disconnectDb();
		if (!$res) {
			t3lib_div::devlog('KMS Speichern fehlgeschlagen!','kms_formular',0);
			return 'Beim Speichern Ihres Antrags gab es einen Fehler';
		}
		$name = $GLOBALS['TSFE']->fe_user->user['name'];
		$out = 'Sehr geehrte/r ' . $name . ',
						Ihr KMS-Antrag wurde soeben gespeichert.<br />
						Sie erhalten per E-Mail eine Bestätigung.<hr />';
		
		$fromUser = array('rz+kms@hs-esslingen.de'=>'KMS Information');
		$toUser = array($username . '@hs-esslingen.de'=>$name);
		$subjectUser = 'Ihr KMS-Antrag wurde soeben gespeichert';
		$bodyHtmlUser = '
		<h2>KMS - Key Management Service - Informationen:</h2>
		<p>Sehr geehrte/r ' . $name . ',<br />
		Ihr KMS-Antrag wurde registriert.</p>
		<p>Rechtliche und Technische Bedingungen zur Aktivierung verschiedener Microsoft Produkte
  (Betriebsystem und Anwendungen) ueber den zentralen KMS Dienst
  an der Eberhard Karls Universitaet Tuebingen</p>
  1. Sie sind Mitarbeiter oder Professor<br />
  2. Sie aktivieren Microsoft Produkte auf ausschliesslich hochschuleigenen Rechnern<br />
  3. Sie sind per OpenVPN über den Server openvpn-mia.hs-esslingen.de verbunden<br />
  4. Sie routen den gesamten Datenverkehr ueber die bestehende OpenVPN Verbindung<br />
     - alternativ den Datenverkehr zur Hochschule Esslingen und zusätzlich den
       zur Universitaet Tuebingen: Netzwerk 134.2.0.0 Netzmaske 255.255.0.0

  <p>Mit freundlichen Gruessen<br />
  Ihr RZ KMS Team</p>
		';
		$this->sendEmail($fromUser,$toUser,$subjectUser,$bodyHtmlUser);
		$fromAdmin = array($username . '@hs-esslingen.de'=>$name);
		$toAdmin = array('rz+kms@hs-esslingen.de'=>'KMS Team');
		$toAdmin = array('mmirsch@hs-esslingen.de'=>'KMS Team');
		$subjectUser = 'Neuer KMS-Antrag';
		$bodyHtmlAdmin = '
		<p>
		Datum: ' . $zeit . ',<br />
		Ein KMS-Antrag wurde soeben von ' . $name . ' eingereicht,</p>
		<p> RZ KMS Team</p>
		';
		$this->sendEmail($fromAdmin,$toAdmin,$subjectUser,$bodyHtmlAdmin);
		return $out;
	}
	
	public function sendEmail($from,$to,$subject,$bodyHtml,$bodyPlain='') {
		$mail = t3lib_div::makeInstance('t3lib_mail_Message');
		if (empty($bodyPlain)) {
			$bodyPlain = preg_replace('/(<br>|<br \/>|<br\/>)\s*/i', PHP_EOL, $bodyHtml);
			$bodyPlain = strip_tags($this->bodyPlain);
		}
		$mail->setFrom($from);
		$mail->setTo($to);
		$mail->setSubject($subject);
		$htmlComplete = $this->initHtml() .
										$bodyHtml .
										$this->exitHtml();
		$mail->setBody($htmlComplete, 'text/html');
		$mail->addPart($bodyPlain, 'text/plain');
		$erg = $mail->send();
		if (!$erg) {
			$failedRecipients = $this->mail->getFailedRecipients();
			t3lib_div::devlog('E-Mail-Versand fehlgeschlagen!','kms_formular',0,$failedRecipients);
		}
		return $erg;
	}
	
	function initHtml() {
		return '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Kalendertermine</title>
		</head>
		<body >
		<div id="header" style="padding: 20px;">
		<img src="http://www.hs-esslingen.de/fileadmin/images/banner/logo.png"
		width="200" height="46"/>
		</div>
	
		<div id="content"
		style="font-family: verdana, arial, helvetica, sans-serif; padding: 0 20px; font-size:80%;">
		';
	}
	
	function exitHtml() {
		return '
		</div>
		</body>
		</html>
		';
	}
}
?>
