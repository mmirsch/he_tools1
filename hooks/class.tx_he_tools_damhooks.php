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

class tx_he_tools_damhooks {
	public function getDamFromDatabase($contentUid,$addField,$ident) {
		$select = 'tx_dam.sys_language_uid, tx_dam.uid, '.$addField.' tx_dam.file_type';
		$dateiPfad = trim($this->pObj->pObj->cObj->data['select_key']);
		if($dateiPfad!='' && 
			 ($GLOBALS['T3_VAR']['ext']['dam_filelinks']['setup']['readFromPathDam']==1 || 
			  $GLOBALS['T3_VAR']['ext']['dam_filelinks']['setup']['ctype_media_add_orig_field']==0)) {
			$orderBy = 'tx_dam.sorting';
			$limit = 1000;
			$configArray = t3lib_div::trimExplode('|',$this->pObj->pObj->cObj->data['select_key']);
			$dateiTypen = $this->pObj->pObj->cObj->data['tx_hetools_filelist_dateitypen'];
			$sortierfeld = $this->pObj->pObj->cObj->data['tx_hetools_filelist_sortierfeld'];
			$sortierReihenfolge = $this->pObj->pObj->cObj->data['tx_hetools_filelist_sortierung'];
			if (count($configArray)>1) {
				$c_directory = '"' . $GLOBALS['TYPO3_DB']->quoteStr($configArray[0],'tx_dam') . '"';
			} else {
				$c_directory = '"' . $GLOBALS['TYPO3_DB']->quoteStr($dateiPfad,'tx_dam') . '"';
			}
			if (strpos('*',$c_directory)!=-1){
				$c_directory= 'tx_dam.file_path LIKE ' . str_replace('*','%',$c_directory);
			} else {
				$c_directory = 'tx_dam.file_path=' . $c_directory;
			};
			
			$global_extension_array = $GLOBALS['T3_VAR']['ext']['dam_filelinks']['setup']['allowedExtReadFromPath'];
			$c_global_extension_array = t3lib_div::trimExplode(',',$global_extension_array);
			if (empty($dateiTypen)) {
				$dateiTypen = $configArray[1];
			}
			$c_extension = '';
			if($dateiTypen != ''){
				$c_extension_array = t3lib_div::trimExplode(',',$dateiTypen);
				if (count($c_extension_array)>0){
					foreach ($c_extension_array as $c_arr){
						if (in_array($c_arr, $c_global_extension_array)) {
							$c_extension .= ',"'.$GLOBALS['TYPO3_DB']->quoteStr($c_arr,'tx_dam').'"';
						}
					}
					$c_extension = trim($c_extension,',');
				}
			} else {
				if ($global_extension_array !=''){
					foreach ($c_global_extension_array as $c_arr){
						$c_extension .=',"'.$GLOBALS['TYPO3_DB']->quoteStr($c_arr,'tx_dam').'"';
					}
					$c_extension = trim($c_extension,',');
				}
			}
			$erweiterungen = '';
			if ($c_extension!=''){
				$erweiterungen = ' AND tx_dam.file_type IN(' . $c_extension . ') ';
			}
				
			$c_sorting_arr['name']='tx_dam.file_name';
			$c_sorting_arr['size']='tx_dam.file_size';
			$c_sorting_arr['ext']='tx_dam.file_type';
			$c_sorting_arr['date']='tx_dam.file_mtime';
			$c_sorting_arr['title']='tx_dam.title';
			$c_sorting_arr['ident']='tx_dam.ident';
			$c_sorting_arr['tx_hetools_dam_sortiernummer']='tx_dam.tx_hetools_dam_sortiernummer';
			$c_sorting_arr['caption']='tx_dam.caption';
			$c_sorting_arr['dl_name']='tx_dam.file_dl_name';
			
			if (empty($sortierfeld)) {
				$sortierfeld = $configArray[2];
			} 
			if($sortierfeld=='name' || 
				 $sortierfeld=='size' || 
				 $sortierfeld=='ext' || 
				 $sortierfeld=='date' || 
				 $sortierfeld=='title' || 
				 $sortierfeld=='tx_hetools_dam_sortiernummer' || 
				 $sortierfeld=='ident' || 
				 $sortierfeld=='caption' || 
				 $sortierfeld=='dl_name'){
				$orderBy = $c_sorting_arr[$sortierfeld];
			};
			
			if ($sortierReihenfolge=='r') {
				$orderBy .= ' DESC';
			} else if($configArray[3]=='r'){
				$orderBy .= ' DESC';
			};
			$whereClause = $c_directory . ' AND tx_dam.sys_language_uid < 1 ' .
										 $GLOBALS['TSFE']->sys_page->enableFields('tx_dam') . $erweiterungen;	
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				$select,
				'tx_dam',
				$whereClause,
				'',
				$orderBy,
				$limit
			);
		} else {
		 	$whereClause = ' AND tx_dam_mm_ref.ident="'.$GLOBALS['TYPO3_DB']->quoteStr($ident,'tx_dam_mm_ref').'" AND tx_dam_mm_ref.tablenames="'.$GLOBALS['TYPO3_DB']->quoteStr('tt_content','tx_dam_mm_ref').'" AND tx_dam.sys_language_uid < 1';
			$orderBy = 'tx_dam_mm_ref.sorting';
			if($GLOBALS['T3_VAR']['ext']['dam_filelinks']['setup']['dam_1_0_9']==1){
				$orderBy = 'tx_dam_mm_ref.sorting_foreign';
			}
			$limit=10000;
			$res = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
				$select,
				'tx_dam',
				'tx_dam_mm_ref',
				'tt_content',
				'AND tt_content.uid IN ('.$contentUid.')  '.$GLOBALS['TSFE']->sys_page->enableFields('tx_dam').' '.$whereClause,
				$groupBy,
				$orderBy,
				$limit
			);
		};
		return $res;
	}
}
	
?>