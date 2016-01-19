<?php 
require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_db.php');

class tx_he_tools_alias {
var $post;
var $get;

	public function main($parent,$pageId) {
		$this->post = t3lib_div::_POST();
		$this->get = t3lib_div::_GET();
		$erg = '<script src="../typo3conf/ext/he_portal/res/jquery/js/jquery-1.7.1b.min.js" type="text/javascript"></script>
						<script src="../typo3conf/ext/he_portal/res/jquery/js/portal.js" type="text/javascript"></script><br>
						<div class="aliasVerwaltung">
						<h1 class="heading">Alias Verwaltung</h1>
						<form method="post" action="">';
		$aliasListe = $this->post['aliasListe'];
		$aliasEingabe = $this->post['aliasEingabe'];
		$aliasSpeichern = $this->post['aliasSpeichern'];
		
		$filter = $this->post['filter'];
		$alias = $this->post['alias'];
		$url = $this->post['url'];
		$lang = $this->post['lang'];
		$uid = $this->post['uid'];
		$auswahl = '';
		if (empty($aliasListe) && 
				empty($aliasEingabe) && 
				empty($aliasSpeichern) &&
				empty($abbrechen)) {
			$action = $this->get['action'];
			if ($action!='') {
				switch ($action) {
				case 'edit':
					$aliasUid = $this->get['aliasUid'];
					if (!empty($aliasUid)) {
						$auswahl = $this->aliasBearbeiten($aliasUid,$pageId);
					}
					break; 
				}
			}
		}	else if ($aliasEingabe!='') {
			$auswahl = $this->aliasEingabe($alias,$url,$lang,$pageId);
		}	else if ($aliasSpeichern!='') {
			$erg .= $this->aliasSpeichern($alias,$url,$lang,$uid);
		}
		if (empty($auswahl)) {
			$erg .= $this->aliasEingabe($alias,$url,$lang,$pageId);
			$erg .= $this->aliasListe($filter);
		} else {
			$erg .= $auswahl;
		}
		$erg .= '</div>';
		return $erg;
	}
		
	protected function aliasListe($filter='') {
global $SCRIPT_PATH;
		$where = 'alias NOT LIKE "/mitarbeiter/%" AND 
							alias NOT LIKE "/de/%"';
		if (!empty($filter)) {
			$where .= ' AND alias LIKE "%' . $filter . '%" ';
		}
		$urlencoded = urlencode ($SCRIPT_PATH);
		$out = '<hr />
						<form name="filter" method="post" action="">
						<label for="aliasFilterSearch">Alias-Suche:</label>
						<input id="aliasFilterSearch" type="text" name="filter" value="' . $filter . '"/>
						&nbsp;(mit * weden alle angezeigt)
						</form><div id="aliasListe"></div>

						<script type="text/javascript">
			function fensterOeffnen(url) {
				var fenster = window.open(url, "Alias testen", "top=50,left=50,width=1000,height=600,scrollbars=yes");
				fenster.focus;
			}
			function loeschenRueckfrage(aliasUid) {
  			var antwort = confirm("Soll dieser Alias wirklich gelöscht werden?");	
				if(antwort) {
					$.ajax({
						url: "../index.php?eID=he_tools",
						data: {
							action: "typo3_be_aliasLoeschen",
							aliasUid: aliasUid
						},
						success: function(result, request) {
							window.location.reload();
						},
						failure: function (result, request) { 
							msgBox(left,top,"Fehler beim Löschen des alias: " + result.responseText); 
						} 
					});
				}
				return false;
			}
			$("#alias").keyup(function(event) {
				var alias = $("#alias").val();
				if (alias.length>=1) {
					$("#buttonlist").removeClass("hidden");
				} else {
					$("#buttonlist").addClass("hidden");
				}
			});
			$("#aliasFilterSearch").keyup(function(event) {
				var eingabe = encodeURI($("#aliasFilterSearch").val());
				if (eingabe.length>=1) {
					$("#ergebnisliste").detach();
					$("#aliasListe").load("../index.php?eID=he_tools&action=typo3_be_aliasliste_search&scriptUrl=' . $urlencoded . '&val=" + eingabe);
				} else {
					$("#ergebnisliste").detach();
				}
			});
			$("#sucheAliasId").click(function(event) {
				var url = $("#url").val();
				if (url.length>=1) {
					$("#ergebnisliste").detach();
					$("#aliasListe").load("../index.php?eID=he_tools&action=typo3_be_aliasliste_id&scriptUrl=' . $urlencoded . '&val=" + url);
				} else {
					$("#ergebnisliste").detach();
				}
			});
			$("#erzeugeTinyUrl").click(function(event) {
				var anzZeichen = $("#anzTinyUrlZeichen").val();
				var neueUrl;
				$.ajax({
					url: "../index.php?eID=he_tools&action=erzeugeKurzUrl&length=" + anzZeichen,
					async: false,
					success: function(data, request) {
						neueUrl = data;
					}
				});
				$("#alias").val(neueUrl);
				$("#buttonlist").removeClass("hidden");
			});

		</script>
		';
		return $out;
	}
	
	protected function aliasBearbeiten($aliasUid,$pageId) {
		$where = 'uid = ' . $aliasUid;
		$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery ('alias,url,lang','tx_six2t3_six_alias',$where);
		if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
			$out = $this->aliasEingabe($row['alias'],$row['url'],$row['lang'],$pageId,'Alias bearbeiten',$aliasUid,TRUE);
		} else {
			$out = '<div class="error">Der Alias wurde nicht gefunden!</div>';
		}
		return $out;
	}
	
	protected function aliasEingabe($alias,$url,$lang,$pageId,$titel='Neuen Alias anlegen',$uid='',$edit=FALSE) {
		$out = '<form class="aliasFormular" name="aliasEingabe" method="post" action="">';
		if (!empty($uid)) {
			$out .= '<input type="hidden" name="uid" value="' . $uid . '"/>';
		}
		if (empty($url)) {
			$url = $pageId;
		}
		$out .= '<div>
						 <label for="anzTinyUrlZeichen">Anzahl Zeichen für Kurz-URL</label>
						 <select id="anzTinyUrlZeichen" name="anzTinyUrlZeichen">
						 <option value="4">4 Zeichen</option>
						 <option value="5">5 Zeichen</option>
						 <option selected="selected" value="6">6 Zeichen</option>
						 <option value="7">7 Zeichen</option>
						 <option value="8">8 Zeichen</option>
						 </select>
						 <input class="button" type="button" id="erzeugeTinyUrl" value="Kurz-URL erzeugen"/>
						 </div>';
		$out .= '<div>
						 <br/>
						 <label for="alias">Alias</label>
						 <input id="alias" type="text" size="80" name="alias" value="' . $alias . '"/>
						 </div>';
		$out .= '<div>
 						<br/>
						 <label for="url">TYPO3-Seitenid oder komplette URL</label>
						 <input id="url" type="text" size="80" name="url" value="' . $url . '"/>
						 <input class="button" type="button" id="sucheAliasId" value="Aliase zu dieser Seite anzeigen"/>
						 </div>';
		$out .= '<div>
						 <br/>
						 <label for="lang">Sprache</label>
						 <select id="lang" name="lang">
						 ';
		if ($lang=='en') {
			$selectedDe = '';
			$selectedEn = ' selected="selected" ';
		} else {
			$selectedDe = ' selected="selected" ';
			$selectedEn = '';
		}
		$out .= '<option value="de" ' . $selectedDe . '>deutsch</option>';
		$out .= '<option value="en" ' . $selectedEn . '>englisch</option>';
		$out .= '</select>
						 </div>';

		if ($edit) {
			$hidden = '';
		} else {
			$hidden = 'hidden';
		}
		$out .= '<div id="buttonlist" class="' . $hidden . '">
						 <br/>
						 <input type="submit" name="aliasSpeichern" value="Alias speichern"/>
						 <input type="submit" name="abbrechen" value="Abbrechen"/>
						 </div>';
		$out .= '</form>';
		return $out;
	}
	
	protected function aliasSpeichern($alias,$url,$lang,$uid='') {
		if ($alias[0]!='/') {
			$aliasNeu = '/' . $alias;
		}	else {
			$aliasNeu = $alias;
		}
		$letzterSlash = strrpos ($aliasNeu,'/');
		if ($letzterSlash==strlen($aliasNeu)-1) {
			$aliasNeu = substr($aliasNeu,0,$letzterSlash);
		}
		$daten['alias'] = $aliasNeu;
		$daten['url'] = $url;
		if ($lang=='en') {
			$daten['lang'] = 1;
		} else {
			$daten['lang'] = 0;
		}
		$daten['tstamp'] = time();
		if (!empty($uid)) {
			$where = 'uid = ' . $uid;
			$erg = $GLOBALS['TYPO3_DB']->exec_UPDATEquery ('tx_six2t3_six_alias',$where,$daten);
			if ($erg) {
				$out = '<div>Der Alias "' . $alias . '" wurde geändert!</div>';
			} else {
				$out = '<div>Beim Speichern des Alias "' . $alias . '" gab es einen Fehler!</div>';
			}	
		} else {
			$where = 'alias = "' . $aliasNeu . '"';
			$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery ('alias','tx_six2t3_six_alias',$where);
			if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
				$out = 'uid: ' . $uid . '<br>' .  '<div class="error">Der Alias "' . $alias . '" ist bereits vorhanden!</div>';
			} else {
				$erg = $GLOBALS['TYPO3_DB']->exec_INSERTquery ('tx_six2t3_six_alias',$daten);
				if ($erg) {
					$out = '<div>Der Alias "' . $alias . '" wurde angelegt!</div>';
				} else {
					$out = '<div>Beim Anlegen des Alias "' . $alias . '" gab es einen Fehler!</div>';
				}
			}
		}
		return $out;
	}

	public static function aliasVorhanden($alias) {
		$where = 'alias = "' . $alias . '"';
		$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid', 'tx_six2t3_six_alias', $where, '', 'alias');
		if ($abfrage) {
			if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
				return true;
			}
		}
		return false;
	}

	public static function getAliasliste($where, &$aliasListe)	{
		$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,alias,url,lang', 'tx_six2t3_six_alias', $where, '', 'alias');
		while ($data = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
			$aliasListe[] = $data;
		}
	}

	public static function printAliaslisteSearch($scriptUrl,$eingabe) {
		$where = 'alias NOT LIKE "/mitarbeiter/%" AND
							alias NOT LIKE "/de/%" AND
							alias NOT LIKE "%.pdf"';
		if (!empty($eingabe) && $eingabe!='*') {
			$where .= ' AND alias LIKE "/' . $eingabe . '%" ';
		}
		$aliasListe = array();
		self::getAliasliste($where, $aliasListe);
		self::printAliasliste($scriptUrl, $aliasListe);
	}

	public static function printAliaslisteId($scriptUrl, $aliasId) {
		$where = 'url ="' . $aliasId . '" ';
		$aliasListe = array();
		self::getAliasliste($where, $aliasListe);
		self::printAliasliste($scriptUrl, $aliasListe);
	}

	public static function printAliasliste($scriptUrl, &$aliasListe) {

		$data = '';
		foreach ($aliasListe as $row) {
			$link = 'http://www.hs-esslingen.de' . $row['alias'];
			$data .= '<tr>';
			$alias = $row['alias'];
			if ($alias[0]=='/') {
				$alias = substr($alias,1);
			}
			$data .= '<td class="td_300"><a onclick="fensterOeffnen(\'' . $link . '\')">' . $alias . '</a></td>';
/*
			$size = 300;
			$urlencodedAlias = base64_encode($link);
			$qrDownloadLink = '../index.php?eID=he_tools&action=downloadQrCode&url=' . $urlencodedAlias . '&size=' . $size;
			$data .= '<td class="td_200"><a href="' . $qrDownloadLink . '">QR-Code herunterladen</a></td>';
*/
			$data .= '<td class="td_200">' . $row['url'] . '</td>';
			if ($row['lang']==1) {
				$lang = 'englisch';
			} else {
				$lang = 'deutsch';
			}
			$scriptUrlDecoded = urldecode($scriptUrl);
			$data .= '<td class="td_100">' . $lang . '</td>';
			$data .= '<td class="aktionen">' .
				'<a href="' . $scriptUrlDecoded . '&action=edit&aliasUid=' . $row['uid'] . '">' .
				'<img title="Alias bearbeiten" src="sysext/t3skin/icons/gfx/edit2.gif" /></a> ' .
				'<img title="Alias löschen" onclick="loeschenRueckfrage(' . $row['uid'] . ')" src="sysext/recycler/mod1/moduleicon.gif" /> ' .
				'</td>';

			$data .= '</tr>';
		}
		if (empty($data)) {
			$out = '<h2>Keine Einträge für diese Auswahl vorhanden!</h2>';
		} else {
			$out =
				'<table class="grid" id="ergebnisliste">
								<tr>
							  <th class="head td_300">Alias</th>
							  <th class="head td_200">URL bzw. TYPO3-Id</th>
							  <th class="head td_100">Sprache</th>
							  <th class="head td_100">Aktionen</th>
							  </tr>' . $data . '</table></form>';
		}
		print $out;
		exit();
	}

	public static function aliasLoeschen($aliasUid) {
		$where = 'uid = ' . $aliasUid;
		return $GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_six2t3_six_alias',$where);
	}


	public static function randomString($anzahl) {
		$characters = 'abcdefghijklmnopqrstuvwxyz';
    $randstring = '';
    for ($i = 0; $i < $anzahl; $i++) {
			$randstring .= $characters[rand(0, strlen($characters))];
		}
    return $randstring;
	}

	public static function erzeugeKurzUrl($length=4) {
		$aliasVorhanden = true;
		$maxcount = 10;
		while ($aliasVorhanden && $maxcount>0) {
			$maxcount--;
			$alias = self::randomString($length);
			$aliasVorhanden = self::aliasVorhanden($alias);
		}
		return $alias;
	}




}


?>