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

class tx_he_tools_wt_gallery_hooks {
	public function gibDamDateibeschreibung($pfad,$datei,$fieldsCaption,&$caption,$fieldsAlttext,&$altText) {
		$where = 'deleted=0 AND hidden=0 AND file_path="' . $pfad . '" AND file_name="' . $datei . '"';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,title,description,caption,creator','tx_dam',$where);
		$caption = '';
		$altText = '';
		if ($data = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$captionList = array();
			$altTextList = array();
			foreach($fieldsCaption as $field) {
				$fieldVal = htmlspecialchars(trim($data[$field]), ENT_QUOTES);
				if (!empty($fieldVal)) {
					$captionList[] = $fieldVal;
				}
			}
			foreach($fieldsAlttext as $field) {
				$fieldVal = htmlspecialchars(trim($data[$field]), ENT_QUOTES);
				if (!empty($fieldVal)) {
					$altTextList[] = $fieldVal;
				}
			}
			$caption = implode(', ',$captionList);
			$altText = implode(', ',$altTextList);
		}
	}
	
	function wt_gallery_list_outer(&$pObj,&$subpartArray) {
	}
	
	function wt_gallery_list_inner(&$pObj,&$ref) {
		if ($pObj->conf['list.']['description']==1) {
			$pfad = $pObj->markerArray['###DIRNAME###'] . '/';
			$datei = $pObj->markerArray['###BASENAME###'];
			$caption = '';
			$altText = '';
			$fieldsAlttext = $pObj->conf['list.']['alttext.'];
			if (empty($fieldsAlttext['fields'])) {
				$fieldsAlttext = array('description');
			} else {
				$fieldsAlttext = explode(',',$fieldsAlttext['fields']);
			}
			$fieldsCaption = $pObj->conf['list.']['caption.'];
			if (empty($fieldsCaption['fields'])) {
				$fieldsCaption = array('description');
			} else {
				$fieldsCaption = explode(',',$fieldsCaption['fields']);
			}
			
			$this->gibDamDateibeschreibung($pfad,$datei,$fieldsCaption,$caption,$fieldsAlttext,$altText);
			if (!empty($altText)) {
				$image = str_replace('<a','<a title="' . $altText . '"',$pObj->markerArray['###IMAGE###']);
				$image = preg_replace('/alt="([^"]*)"/i','alt="' . $altText . '"', $image);
				$image = preg_replace('/title="([^"]*)"/i','title="' . $altText . ' "', $image);
				$pObj->markerArray['###IMAGE###'] = $image;
			} else {
				//$altText = 'x';
			}
			
			$bildbreite = $pObj->conf['list.'][width];
			$bildbreite = str_replace('c','',$bildbreite);
// Text in der Listenansicht kürzen
			$strMax = 400;
			if (strlen($caption)>$strMax) {
				$caption = $this->substr_word($caption,$strMax);
				$caption .= ' ...<br/>(weiter mit Klick auf das Bild)';
			}
			$pObj->markerArray['###TEXT###'] = '<span style="width: ' . $bildbreite . 'px" class="image_decription">' . $caption .'</span>';
		}
    if ($pObj->conf['list.']['showOriginalLink']==1) {
      $bildbreite = $pObj->conf['list.'][width];
      $bildbreite = str_replace('c','',$bildbreite);
      $titel = str_replace('_',' ',$pObj->markerArray['###FILENAME###']) . ' herunterladen';
      $titel = 'herunterladen';
      $bildEncoded = base64_encode($pObj->cObj->data['picture']);
      $caption = '<a class="download" href="index.php?eID=he_tools&action=file_download&file=' . $bildEncoded . '" download="' . $titel . '">' . $titel . '</a>';
      $pObj->markerArray['###TEXT###'] = '<span style="width: ' . $bildbreite . 'px" class="image_decription">' . $caption .'</span>';
    }
	}

	function substr_word($string, $length) {
	  if (strlen($string) > $length) {
	    $string = substr($string, 0, $length);
	    $string = substr($string, 0, strrpos($string, ' '));
	  }
	  return $string;
	}
		

}
?>