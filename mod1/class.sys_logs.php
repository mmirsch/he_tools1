<?php

class he_tools_sys_logs {

	public function main($id) {
		$post = t3lib_div::_POST();
		$suchen = $post[suchen];
		if ($suchen!='') {
			$uid = $post[uid];
			$tabelle = $post[tabelle];
			$erg .= $this->suchen($uid,$tabelle);
		}

		$erg .= '<div class="sys_logs">';
		$erg .= '<form name="sys_logs" method="post" action="">';
		$erg .= 'ID: <input type="text" name="uid" value="' . $id . '"/>';
		$erg .= ' Tabelle: <select name="tabelle">
						 <option value="pages" selected="selected">pages</option>
						 <option value="tt_content">tt_content</option>
						 </select><br/>';
		$erg .= '<input type="submit" name="suchen" value="Logeinträge suchen"/><br/><br/>';
		
		$erg .= '
				</form>
				</div>';
		return $erg;
	}
	
	public function suchen($uid,$tabelle) {
		if ($tabelle=='pages') {
			$wherePages = 'pid=' . $uid;
			$uids = array();
			$abfragePages = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid','tt_content',$wherePages);
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfragePages)) {
				$ttContentUid = $row[uid];
				if (!in_array($ttContentUid,$uids)) {
					$uids[] = $ttContentUid;
				}
			}
			$uidListe = implode(',',$uids);
			$where = '(tablename="tt_content" AND recuid IN (' . $uidListe . ')) OR 
								(tablename="pages" AND recuid=' . $uid . ')';
		} else {
			$where = 'tablename="' . $tabelle . '" AND recuid=' . $uid;
		}
		$GLOBALS['TYPO3_DB']->sql_select_db('t3logs');
		for ($i=1;$i<=31;$i++) {
			$table = sprintf('sys_log_%02d',$i);
			$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('tstamp,userid,details,log_data',$table,$where);
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
				$zeit = strftime('%d.%m.%Y - %H:%M',$row['tstamp']);
				$user = $row['userid'];
				$meldung = vsprintf ($row['details'],unserialize($row['log_data']));
				$ergebnis[] = array('zeit'=>$zeit, 'user'=>$user,'meldung'=>$meldung);
			}
		}
		$GLOBALS['TYPO3_DB']->connectDB();
// Aktuelle DB ebenfalls durchsuchen
		$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('tstamp,userid,details,log_data','sys_log',$where);
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
			$zeit = strftime('%d.%m.%Y - %H:%M',$row['tstamp']);
			$user = $row['userid'];
			$meldung = vsprintf ($row['details'],unserialize($row['log_data']));
			$ergebnis[] = array('zeit'=>$zeit, 'user'=>$user,'meldung'=>$meldung);
		}
		
		if ($tabelle=='pages') {
			$out = '<h2>Suchergebnisse für die Seite ' . $uid . '</h2>';
		} else {
			$out = '<h2>Suchergebnisse für das Element ' . $uid . '</h2>';
		}
		$out.= '<table class="grid">';
		$out .= '<tr>';
		$out .= '<th>Datum - Zeit</th>';
		$out .= '<th>Benutzer</th>';
		$out .= '<th>Aktion</th>';
		$out .= '</tr>';
		if (count($ergebnis)>0) {
			foreach ($ergebnis as $eintrag) {
				$out .= '<tr>';
				$out .= '<td>' . $eintrag['zeit'] . '</td>
								<td>' . $this->gibBenutzernamen($eintrag['user']) . '</td>
								<td>' . $eintrag['meldung'] . '</td>';
				$out.= '</tr>';
			}
		}
		$out.= '</table>';
		return $out;
	}
	
	public function gibBenutzernamen($uid) {
		$ergebnis = $uid;
		$where = 'deleted=0 and disable=0 and uid=' . $uid;
		$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('realName','be_users',$where);
		if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
			$ergebnis = $row['realName'];
		}
		return $ergebnis;
	}
}
	

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_tools/mod1/class.sys_logs.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_tools/mod1/class.sys_logs.php']);
}

?>