<?php
/***************************************************************
*  Copyright notice
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


class tx_he_tools_news_hooks {
	// hook for tt_news
	function extraItemMarkerProcessor($markerArray, $row, $lConf, &$pObj) {

		$markerArray['###PROFESSOREN_LINK###'] = $this->gibProfessorenLink($row);
		$markerArray['###KANDIDAT###'] = $row['tx_hetools_kandidat_arbeit'];
		
		// configuration of chgallery
		$confDefault = $pObj->conf['genericmarkers.'];

		if (!is_array($confDefault)) {
			return $markerArray;
		}

		// merge with special configuration (based on chosen CODE [SINGLE, LIST, LATEST]) if this is available
		if (is_array($confDefault[$pObj->config['code'].'.'])) {
			$conf = t3lib_div::array_merge_recursive_overrule($confDefault, $confDefault[$pObj->config['code'].'.']);
		} else {
			$conf = $confDefault;
		}
		
		if (is_array($conf)) {

			if ($conf['data']!='') {
				foreach (t3lib_div::trimExplode(',',$conf['data']) as $key) {
					$pObj->cObj->data['generic_'.$key]    = $row[$key];
				}
			}


			foreach($conf as $key=>$value) {
				$key2 = trim($key, '.');
				$markerArray['###GENERIC_'.strtoupper($key2).'###'] = $pObj->cObj->cObjGetSingle($conf[$key2] , $conf[$key] );
			
			}
		}

		return $markerArray;
	}

	// hook for tt_news
	function extraGlobalMarkerProcessor(&$pObj, $markerArray) {
		
		// configuration of chgallery
		$confDefault = $pObj->conf['globalmarkers.'];
		
		if (!is_array($confDefault)) {
			return $markerArray;
		}
		
		// merge with special configuration (based on chosen CODE [SINGLE, LIST, LATEST]) if this is available
		if (is_array($confDefault[$pObj->config['code'].'.'])) {
			$conf = t3lib_div::array_merge_recursive_overrule($confDefault, $confDefault[$pObj->config['code'].'.']);
		} else {
			$conf = $confDefault;
		}
		
		if (is_array($conf)) {		

			
			
			foreach($conf as $key=>$value) {
				$key2 = trim($key, '.');
				$markerArray['###GLOBAL_'.strtoupper($key2).'###'] = $pObj->cObj->cObjGetSingle($conf[$key2] , $conf[$key] );
			}
		}
		
		
		return $markerArray;
	}

	function gibProfessorenLink($row) {
		$link = '';
		$pid = $row[pid];	
		$whereSeite = 'deleted=0 AND uid=' . $pid;
		$abfrageSeite = $GLOBALS['TYPO3_DB']->exec_SELECTquery('title','pages',$whereSeite);
		if ($datenSeite = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageSeite)) {
			$username = $datenSeite['title'];
			$whereFeUser = 'deleted=0 AND username="' . $username . '"';
			$abfrageFeUser = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','fe_users',$whereFeUser);
			if ($datenFeUser = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrageFeUser)) {
				$seitenId = $datenFeUser['tx_hepersonen_profilseite'];
				$name = $datenFeUser['first_name'] . ' ' . $datenFeUser['last_name'];
				if (!empty($datenFeUser['tx_hepersonen_akad_grad'])) {
					$name = $datenFeUser['tx_hepersonen_akad_grad'] . ' ' . $name;
				}
				$link = '<a href="index.php?id=' . $seitenId . '">' . $name . '</a>';
			}
		}
		return $link;
	}

	function processSingleViewLink(&$linkWrap, $url, $params, $parent) {
		if (!empty($parent->conf['he_link_target'])) {
			$linkWrap[0] = str_replace('target="_self"','target="' . $parent->conf['he_link_target'] . '"',$linkWrap[0]);
		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_tools/hooks/class.tt_news_hooks.php']) {
    include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_tools/hooks/class.tt_news_hooks.php']);
}
?>