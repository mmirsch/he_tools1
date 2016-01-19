<?php

class  tx_he_tools_pers_verwaltung {
var $benutzerListe = array();

	public function main($url) {
		$out = '<div class="UserVerwaltung">
					 <form name="userVerwaltung" method="post" action="">
					 <input type="submit" name="mode" value="Backend-User" />
					 <input type="submit" name="mode" value="Frontend-User" />
					 </form><br/><br/>
		';
		$modus = t3lib_div::_GP('mode');
		switch ($modus) {
		case 'Frontend-User':
			$out .= $this->frontend();
			break;
		case 'Backend-User':
			$out .= $this->backend();
			break;
		}
		return $out;
	}
	
	public function frontend() {
		$pfad = urlencode(t3lib_div::getIndpEnv('REQUEST_URI'));
		$erg .= self::gibJquery();
		$erg .= '<div class="UserVerwaltung">';
		$erg .= '<form name="UserVerwaltung" method="post" action="">';
		$erg .= '<label for="modUsersFilter">Filter: </label>';
		$erg .= '<input type="text" id="modUsersFilter"/>';
		$erg .= '<label for="modUsersStudiCheck">keine Studierenden: </label>';
		$erg .= '<input type="checkbox" id="modUsersStudiCheck"/>';
		$erg .= '<label for="modUsersGroupsCheck">in Benutzergruppen suchen: </label>';
		$erg .= '<input type="checkbox" id="modUsersGroupsCheck"/><br/><br/>';
		$erg .= '<div id="benutzerliste">';
		$erg .= '</div>';
		
		$erg .= '<span id="modUsersEditLinks" style="display: none;">
				<a href="#" id="modUsersBearbeitenLink">
				<img id="modUsersBearbeitenImg" width="16" height="16" alt="" title="Bitte erst einen Mitarbeiter auswählen" src="sysext/t3skin/icons/gfx/edit2.gif"/>
				</a>
				</span>
				</form>
				</div>
				
		<script>
	 	function updateLinks(auswahl) {
			var uid = auswahl.options[auswahl.selectedIndex].value;
			var user = auswahl.options[auswahl.selectedIndex].text;
			var bearbeitenLink = document.getElementById("modUsersBearbeitenLink");
			var bearbeitenImg = document.getElementById("modUsersBearbeitenImg");
			var bearbeitenOnclick = "window.location.href=\'alt_doc.php?edit[fe_users]["+uid+"]=edit&returnUrl=' . 
													 		$pfad . '\'; return false;";
			
			bearbeitenLink.setAttribute("onClick", bearbeitenOnclick);
			bearbeitenLink.onClick = bearbeitenOnclick;
			bearbeitenLink.href = "#";
			bearbeitenImg.title = user+" bearbeiten";
			
			var editLinks = document.getElementById("modUsersEditLinks");
			editLinks.style.display = "inline";
		}
			$("#modUsersStudiCheck").attr("checked", "checked");
			$("#modUsersFilter").keyup(function(event) {
			var eingabe = encodeURI($("#modUsersFilter").val());
			if (eingabe.length>1) {
				var studis = !($("#modUsersStudiCheck").is(":checked")); 
				var groups = ($("#modUsersGroupsCheck").is(":checked")); 
				$("#ergebnisliste").detach();
				$("<select id=\"ergebnisliste\" size=\"30\" style=\"background: #fff; float: left;width:600px;\" onchange=\"updateLinks(this)\"></select>").appendTo($("#benutzerliste"));
				$("#ergebnisliste").load("../index.php?eID=he_tools&action=typo3_fe_userliste&val=" + eingabe + "&studis="+ studis + "&groups="+ groups);
			} else {
				$("#ergebnisliste").detach();
			}
			
		});
		</script>
				';
		return $erg;
	}
	
	public function backend() {
		$pfad = urlencode(t3lib_div::getIndpEnv('REQUEST_URI'));
		$erg .= $this->gibJquery();
		$erg .= '<div class="UserVerwaltung">';
		$erg .= '<form name="UserVerwaltung" method="post" action="">';
		$erg .= '<label for="modUsersFilter">Filter: </label>';
		$erg .= '<input type="text" id="modUsersFilter"/>';
		$erg .= '<label for="modUsersGroupsCheck">in Benutzergruppen suchen: </label>';
		$erg .= '<input type="checkbox" id="modUsersGroupsCheck"/><br/><br/>';
		$erg .= '<div id="benutzerliste">';
		$erg .= '</div>';
		
		$erg .= '<span id="modUsersEditLinks" style="display: none;">
				<a href="#" id="modUsersBearbeitenLink">
				<img id="modUsersBearbeitenImg" width="16" height="16" alt="" title="Bitte erst einen Mitarbeiter auswählen" src="sysext/t3skin/icons/gfx/edit2.gif"/>
				</a>
				<a href="#" target="_top" id="modUsersWechselnLink">
				<img id="modUsersWechselnImg" width="16" height="16" border="0" align="top" alt="" title="Bitte erst einen Mitarbeiter auswählen" src="sysext/t3skin/icons/gfx/su_back.gif"/>
				</a>
				</span>
				</form>
				</div>
				
		<script>
	 	function updateLinks(auswahl) {
			var uid = auswahl.options[auswahl.selectedIndex].value;
			var user = auswahl.options[auswahl.selectedIndex].text;
			var wechselnLink = document.getElementById("modUsersWechselnLink");
			var wechselnImg = document.getElementById("modUsersWechselnImg");
			var bearbeitenLink = document.getElementById("modUsersBearbeitenLink");
			var bearbeitenImg = document.getElementById("modUsersBearbeitenImg");
			var bearbeitenOnclick = "window.location.href=\'alt_doc.php?edit[be_users]["+uid+"]=edit&returnUrl=' . 
													 $pfad . '\'; return false;";
			
			bearbeitenLink.setAttribute("onClick", bearbeitenOnclick);
			bearbeitenLink.onClick = bearbeitenOnclick;
			bearbeitenLink.href = "#";
			bearbeitenImg.title = user+" bearbeiten";
			
			wechselnLink.href = "/typo3/mod.php?M=tools_beuser&SwitchUser="+uid+"&switchBackUser=1";
			wechselnImg.title = "zum Benutzer \'"+user+"\' wechseln [switch-back mode]";
			
			var editLinks = document.getElementById("modUsersEditLinks");
			editLinks.style.display = "inline";
		}
						
			$("#modUsersFilter").keyup(function(event) {
			var eingabe = encodeURI($("#modUsersFilter").val());
			if (eingabe.length>1) {
				$("#ergebnisliste").detach();
				$("<select id=\"ergebnisliste\" size=\"30\" style=\"background: #fff; float: left;width:600px;\" onchange=\"updateLinks(this)\"></select>").appendTo($("#benutzerliste"));
				var groups = ($("#modUsersGroupsCheck").is(":checked")); 
				$("#ergebnisliste").load("../index.php?eID=he_tools&action=typo3_be_userliste&val=" + eingabe + "&groups="+ groups);
			} else {
				$("#ergebnisliste").detach();
			}
			
		});
		</script>
				';
		return $erg;
	}
	
	public function addBackendUser() {
		$pfad = urlencode(t3lib_div::getIndpEnv('REQUEST_URI'));
		$erg .= self::gibJquery();
		$erg .= '<div class="UserVerwaltung">';
		$erg .= '<form name="UserVerwaltung" method="post" action="">';
		$erg .= '<label for="modUsersFilter">Filter: </label>';
		$erg .= '<input type="text" id="modUsersFilter"/>';
		$erg .= '<div id="benutzerliste">';
		$erg .= '</div>';
		
		$erg .= '<span id="modUsersEditLinks" style="display: none;">
				<img id="modBeUserAddImg" width="16" height="16" alt="" style="cursor: pointer;" title="Backend-Benutzer anlegen" 
					src="sysext/t3skin/icons/gfx/add.gif"/>
				</a>
				</span>
				</form>
				</div>
				
		<script>
		 	function updateButton() {
				var username = $("#userlist :selected").val();
		 		$("#modUsersEditLinks").css("display","inline");
				$("#modBeUserAddImg").attr("data-username",username);
			}
			
			$("#modUsersFilter").keyup(function(event) {
			var eingabe = encodeURI($("#modUsersFilter").val());
			if (eingabe.length>1) {
				$("#userlist").detach();
				$("<select id=\"userlist\" size=\"10\" style=\"background: #fff; float: left;width:600px;\" onchange=\"updateButton()\"></select>").appendTo($("#benutzerliste"));
				$("#userlist").load("../index.php?eID=he_tools&action=typo3_fe_userliste_ohne_backend&val=" + eingabe);
			} else {
				$("#userlist").detach();
			}
			
		});
			$("#modBeUserAddImg").click(function() {
				username = $("#modBeUserAddImg").attr("data-username");
				var antwort = confirm("Soll der Frontend-Benutzer " + username + " als Backend-Benutzer angelegt werden?");
				if (antwort) {
					$.ajax({
						url: "../index.php?eID=he_tools&action=addBeUser&fe_username=" + username + "&returnUrl=' . $pfad . '",
						async: false,
						success: function(result, request) {
							if (result!="ok") {
								alert("Der Backend-Benutzer konnte nicht erstellt werden!");							
							} else {
								alert("Der Backend-Benutzer " + username + " wurde erstellt!");		
							}
						}
					});
				}			
			});
		
		</script>
				';
		return $erg;
	}
	
	public static function printBenutzerlisteBackend($eingabe,$groups=FALSE) {
		if ($groups!='false') {
			$whereGroups = '(be_users.username LIKE "%' . $eingabe . '%" OR be_users.realName LIKE "%' . $eingabe . '%")
								 	OR 
									(be_groups.uid IN (SELECT uid FROM be_groups WHERE title LIKE "%' . $eingabe . '%"))';
		} else {
			$whereGroups = '(be_users.username LIKE "%' . $eingabe . '%" OR be_users.realName LIKE "%' . $eingabe . '%")';
		}
		$where = ' WHERE (be_users.username NOT LIKE "%_cli%" AND 
								be_users.deleted=0 AND 
								(' . $whereGroups . ')
							 )';

/*		
		$where = 'username NOT LIKE "%_cli%" AND 
							deleted=0 AND
							(username LIKE "%' . $eingabe . '%" OR
							realName LIKE "%' . $eingabe . '%")';
*/							
		
		$select = 'SELECT DISTINCT be_users.username,be_users.realName,be_users.uid,be_users.usergroup FROM be_users LEFT JOIN be_groups ON ( FIND_IN_SET( be_groups.uid, be_users.usergroup ) ) ';
		$orderby = ' ORDER BY username ';
		$limit = ' LIMIT 0,50';
		$sqlQuery = $select . $where . $orderby . $limit;
		
//t3lib_utility_Debug::debugInPopUpWindow($sqlQuery);
		
		$abfrage = $GLOBALS['TYPO3_DB']->sql_query($sqlQuery);
//		$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery ('username,realName,uid,usergroup,disable','be_users',$where,'','username','0,50');
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
			if ($row['disable']) {
				$disabled = ' class="disabled" ';
				$status = ' [deaktiviert] ';
			} else {
				$disabled = '';
				$status = '';
			}
			$gruppen = self::gibBeUserGruppen($row['usergroup']);
			$anzeige = trim($row[realName]) . ' (' . trim($row[username]) . ') ' . $status . ' - ' . $gruppen;
			$erg .= '<option ' . $disabled . ' value="' . $row[uid]  . '">'.
								$anzeige . '</option>';
		}
		print ($erg);
		return TRUE;
	}
	
	public static function printBenutzerlisteFrontend($eingabe,$studis,$groups=FALSE) {
		if ($groups!='false') {
			$whereGroups = '(fe_users.username LIKE "%' . $eingabe . '%" OR fe_users.name LIKE "%' . $eingabe . '%")
								 	OR 
									(fe_groups.uid IN (SELECT uid FROM fe_groups WHERE title LIKE "%' . $eingabe . '%"))';
		} else {
			$whereGroups = '(fe_users.username LIKE "%' . $eingabe . '%" OR fe_users.name LIKE "%' . $eingabe . '%")';
		}
		$where = ' WHERE (fe_users.username NOT LIKE "%_cli%" AND 
								fe_users.deleted=0 AND 
								(' . $whereGroups . ')
							 )';
		if ($studis=='false') {
			$where .= ' AND (NOT FIND_IN_SET("71",fe_users.usergroup ))';
		}
		$select = 'SELECT DISTINCT fe_users.username,fe_users.name,fe_users.uid,fe_users.usergroup FROM fe_users LEFT JOIN fe_groups ON ( FIND_IN_SET( fe_groups.uid, fe_users.usergroup ) ) ';
		
		$orderby = ' ORDER BY username ';
		$limit = ' LIMIT 0,50';
		$sqlQuery = $select . $where . $orderby . $limit;
//t3lib_utility_Debug::debugInPopUpWindow($sqlQuery);		
		$abfrage = $GLOBALS['TYPO3_DB']->sql_query($sqlQuery);
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
			$gruppen = self::gibFeUserGruppen($row['usergroup']);
			$anzeige = trim($row[name]).' ('.trim($row[username]).') - ' . $gruppen;
			$erg .= '<option data-username="' . trim($row[username]) . '" value="' . $row[uid] . '">'.
								$anzeige . '</option>';
		}
		print ($erg);
		return TRUE;
	}
	
	public static function printBenutzerlisteFrontendOhneBackend($eingabe) {
		$where = 'deleted=0 AND disable=0';
		$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery ('username','be_users',$where);
		$beUsers = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
			$beUsers[] = $row['username'];
		}
		
		$where = '(username NOT LIKE "%_cli%" AND 
							deleted=0 AND disable=0 AND 
							(username LIKE "%' . $eingabe . '%" OR
							name LIKE "%' . $eingabe . '%"))';
		$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery ('username,name,uid,usergroup','fe_users',$where,'','username','0,50');
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
			$feUsername = trim($row['username']);
			if (!in_array($feUsername,$beUsers)) {
				$anzeige = trim($row['name']).' ('. $feUsername . ')';
				$erg .= '<option value="' . $feUsername . '">' . $anzeige . '</option>';
			}
		}
		print ($erg);
		return TRUE;
	}
	
	public static function addBackendUserFromFrontendUserData($username,$returnUrl) {
		$where = '(deleted=0 AND disable=0 AND username = "' . $username . '")';
		$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery ('name,username,email','fe_users',$where);
		if ($feUserData = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
			$beUserSetup = array(
					'thumbnailsByDefault' => '',
					'emailMeAtLogin' => '',
					'condensedMode' => '',
					'noMenuMode' => '',
					'startModule' => 'help_txt3desktopM1',
					'hideSubmoduleIcons' => 0,
					'helpText' => 'on',
					'titleLen' => 30,
					'edit_wideDocument' => '',
					'edit_showFieldHelp' => 'icon',
					'edit_RTE' => 'on',
					'edit_docModuleUpload' => 'on',
					'disableCMlayers' => '',
					'navFrameWidth' => '',
					'navFrameResizable' => true,
					'lang' => 'de',
					'copyLevels' => '',
					'recursiveDelete' => 0,
      );
			
			// ################### Backend-Benutzer anlegen ####################
			$be_user['username'] = $feUserData['username'];
			//Generierung eines zufälligen MD5 Kennworts
			$be_user['password'] = md5(rand(1,10000000));
			$be_user['deleted'] = 0;
			$be_user['disable'] = 1;
			$be_user['pid'] = 0;
			$be_user['lang'] = 'de';
			$be_user['usergroup'] = 2;
			$be_user['workspace_perms'] = 1;
			$be_user['fileoper_perms'] = 1;
      $be_user['options'] = 3;
      $be_user['tx_hepersonen_externer_user'] = 1;
      $be_user['tstamp'] = time();
			$be_user['crdate'] = time();
			// Standardeinstellungen setzen
			$be_user['uc'] = serialize($beUserSetup);
			$be_user['email'] = $feUserData['email'];
			$be_user['realName'] = $feUserData['name'];
			// Backend-User in der Datenbank Anlegen
			$gespeichert = $GLOBALS['TYPO3_DB']->exec_INSERTquery('be_users',$be_user);
			if (!$gespeichert) {
				$ergebnis = 'error';			
			} else {
				$ergebnis = 'ok';
//				$uid = $GLOBALS['TYPO3_DB']->sql_insert_id();
//				$redirectUrl = 'https://www.hs-esslingen.de/typo3/alt_doc.php?edit[be_users][' . $uid . ']=edit$returnUrl=' . $returnUrl;
//				t3lib_utility_Http::redirect($redirectUrl);
//				exit();
			}
			header('Content-Type: text/html; charset=utf-8');
			print $ergebnis;		
			exit();
		}
	}
	
	public static function gibJquery() {
		return '<script src="../typo3conf/ext/he_portal/res/jquery/js/jquery-1.7.1b.min.js" type="text/javascript"></script>';
	}
	
	public static function gibBeUserGruppen($gruppen) {
		$liste = explode(',',$gruppen);
		$titelListe = array();
		foreach ($liste as $gruppe) {
			$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery ('title','be_groups',"uid=" . $gruppe);
			if ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
				$titelListe[] = $daten['title'];
	    }
			
		}
    return implode(',',$titelListe);
	}

	public static function gibFeUserGruppen($gruppen) {
		$liste = explode(',',$gruppen);
		$titelListe = array();
		foreach ($liste as $gruppe) {
			$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery ('title','fe_groups',"uid=" . $gruppe);
			if ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
				$titelListe[] = $daten['title'];
	    }
			
		}
    return implode(',',$titelListe);
	}
	
}
	

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_tools/mod1/class.tx_he_tools_pers_verwaltung.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_tools/mod1/class.tx_he_tools_pers_verwaltung.php']);
}

?>