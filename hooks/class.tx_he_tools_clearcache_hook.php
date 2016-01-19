<?php
/***************************************************************
*  Copyright notice
*
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_util.php');

class tx_he_tools_clearcache_hook  {
	public function clearCachePostProc(&$params, &$pObj) {
	$_DEBUG = FALSE;
		if ($params[table]=='tx_dam' && isset($params[uid])) {
if ($_DEBUG) t3lib_div::devLog('$params: '. print_r($params,true), 'tx_he_tools_damcache', 0);
			$pages = array();
			$abfrageFelder = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query('tt_content.uid','tx_dam','tx_dam_mm_ref','tt_content',$where);
			
			$abfrage = 'SELECT tt_content.pid FROM tt_content ' . 
								 'INNER JOIN tx_dam_mm_ref ON ' . 
								 'tx_dam_mm_ref.uid_foreign = tt_content.uid ' . 
								 'INNER JOIN tx_dam ON ' . 
								 'tx_dam.uid = tx_dam_mm_ref.uid_local ' . 
								 'where tx_dam.uid=' . $params[uid];		
			$abfrageFelder = $GLOBALS['TYPO3_DB']->sql_query($abfrage);
if ($_DEBUG) t3lib_div::devLog('abfrage: '. $abfrage, 'tx_he_tools_damcache', 0);
			
			while ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageFelder)) {
				if (!in_array($daten[pid],$pages)) {
					$pages[] = $daten[pid];
				}
			}
			$where = 'bodytext LIKE "%<media ' . $params[uid] . '%"';
			$abfrageFelder = $GLOBALS['TYPO3_DB']->exec_SELECTquery('pid','tt_content',$where);
			while ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageFelder)) {
				if (!in_array($daten[pid],$pages)) {
					$pages[] = $daten[pid];
				}
			}
			
			$where = 'uid = ' . $params[uid];
			$abfrageFelder = $GLOBALS['TYPO3_DB']->exec_SELECTquery('file_path','tx_dam',$where);
			if ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageFelder)) {
				$pfad = $daten['file_path'];
				
				$where = 'select_key LIKE "' . $pfad . '%"';
				$abfrageFelder = $GLOBALS['TYPO3_DB']->exec_SELECTquery('pid','tt_content',$where);
				while ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageFelder)) {
if ($_DEBUG) t3lib_div::devLog('Seite mit Dateiverweisen im Pfad: '. $daten[pid], 'tx_he_tools_damcache', 0);
					if (!in_array($daten[pid],$pages)) {
						$pages[] = $daten[pid];
					}
				}
			}
			if (count($pages)>0) {
				$seiten = trim(implode(',',$pages),',');
if ($_DEBUG) t3lib_div::devLog('Geloeschte pageids im Cache: '. $seiten, 'tx_he_tools_damcache', 0);
				tx_he_tools_util::loescheTypo3Seitencache($seiten);
			}
		}
	}
}
?>