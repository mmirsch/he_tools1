<?php
class tx_he_tools_templavoila_hooks {
	
	public function renderElement_preProcessRow(&$row, $table, $parent) {
		$portalSeite = FALSE;
		if ($table=='pages') {
			$get = t3lib_div::_GET();
			$idGet = $get['id'];
/*
 * Verweis testen: 
 * bei Seiten vom Typ Verweis werden nicht die Daten der Originalseite
 * sondern die Daten der Zielseite übergeben (andere Id)
 */ 
			if (!empty($idGet) && $idGet!=$row['uid']) {
				$dokType = $this->getPageData($idGet,'doktype');
			} else {
				$dokType = $row['doktype'];
			}
/*
 *  Seiten vom Typ Verweis nicht ändern
 */
			if ($dokType!=4) {
				$mp = $get['MP'];
				$uid = $row['uid'];
				$pid = $row['pid'];
				if ($uid==92125) {
					$portalSeite = TRUE;
				} else {
					if (!empty($mp)) {
						$mountPageLists = explode(',',$mp);
						$mountPages = explode('-',$mountPageLists[0]);
						$pid = $mountPages[1];
					}		
					if ($this->isParentPage($pid,92125)) {
						$portalSeite = TRUE;
					}
				}
				if ($portalSeite) {
/*
t3lib_div::devLog("MP: " . print_r($mp,true), 'tx_he_tools_templavoila_hooks', 0);
//t3lib_div::devLog("row: " . print_r($row,true), 'tx_he_tools_templavoila_hooks', 0);
t3lib_div::devLog("Portalseite: $uid, tx_templavoila_ds: " . $row['tx_templavoila_ds'], 'tx_he_tools_templavoila_hooks', 0);
*/
					if (!empty($row['tx_templavoila_ds'])) {
						switch ($row['tx_templavoila_ds']) {
						// dreispaltig
						case 4: 
							$row['tx_templavoila_ds'] = 19; 
							$row['tx_templavoila_to'] = 39;
							break;
						// zweispaltig
						case 9: 
							$row['tx_templavoila_ds'] = 18; 
							$row['tx_templavoila_to'] = 36; 
							break;
						}
					}
				}
			}
		}
	}
	
	public function isParentPage($child,$parent) {
		$pid = $this->getPageData($child,'pid');
		if ($pid==0) {
			return FALSE;
		}
		if ($pid==$parent) {
			return TRUE;
		} else {
			return $this->isParentPage($pid,$parent);
		}
	}

	public function getPageData($uid,$feld) {
		$pages = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows($feld,'pages','uid=' . $uid);
		return $pages[0][$feld];
	}
	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_tools/hooks/class.tx_he_tools_templavoila_hooks.php']) {
    include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_tools/hooks/class.tx_he_tools_templavoila_hooks.php']);
}
?>