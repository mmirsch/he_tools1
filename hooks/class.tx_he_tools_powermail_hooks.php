<?php
require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_online_sb.php');


class tx_he_tools_powermail_hooks extends tslib_pibase {

	public function PM_SubmitBeforeMarkerHook(&$powermail,&$markerArray, &$sessiondata) {
		if ($powermail->cObj->cObjGetSingle($powermail->conf['allow.']['sender_overwrite'], 
																				$powermail->conf['allow.']['sender_overwrite.'])) {
			$sessiondata['sender'] = 'no-reply@hs-esslingen.de';
		}
		if (!empty($powermail->conf['replace_uid'])) {
			$uid = $powermail->conf['receiverlist'];
			$where  = 'deleted=0 AND uid=' . $uid;
			$data = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow(
					          'flexform',' tx_powermail_fields',$where);  	    
			$GLOBALS['TYPO3_DB']->sql_free_result($result);
						
//			preg_match('>|(.*?)|'.$uid,$data['flexform'], $thementitel);
			$teile = explode("\n", $data['flexform']);
			
		 	foreach ($teile as $value) {
    			$splitarray = explode("|", $value);
		 		if(count($splitarray)==2) {
		 			if(trim($splitarray[1]) == $sessiondata['uid' . $uid]) {
		 				$thema = $splitarray[0];
		 			}
		 		}
			}
			$powermail->sessiondata['uid'.$uid] = $thema;
//			$powermail->sessionfields['uid'.$uid] = $thema;
			$powermail->cObj->data = $powermail->cObj->substituteMarkerInObject(
																	$powermail->cObj->data,
																	array('###THEMA###'=>$thema)
																);
		 
		}
		if (isset($powermail->conf['teilnehmernummer.'])) {
			if (!isset($sessiondata['teilnehmernummer'])) {
				$anzNum = $powermail->conf['teilnehmernummer.']['stellen'];
				$pid = $powermail->conf['teilnehmernummer.']['pid'];
	      $where  = 'deleted=0 AND pid=' . $pid;
				$index = $GLOBALS['TYPO3_DB']->exec_SELECTcountRows(
						          'uid','tx_powermail_mails',$where);  	    
				$GLOBALS['TYPO3_DB']->sql_free_result($result);
				$teilnehmerNummer = sprintf('%0' . $anzNum . 'd',$index+1);
				$sessiondata['teilnehmernummer'] = $teilnehmerNummer;
			}
			$powermail->cObj->data = $powermail->cObj->substituteMarkerInObject(
																	$powermail->cObj->data,
																	array('###TEILNEHMERNUMMER###'=>$teilnehmerNummer)
																);
		}
		$conf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_hetools_pi1.']['powermail.'];
		$replaceConf = '';
		if (!empty($conf['receiverlist_replace.'])) {
			$replaceConf = $conf['receiverlist_replace.'];
		} else if (!empty($conf['field_replace.'])) {
			$replaceConf = $conf['field_replace.'];
		}
		if (!empty($replaceConf)) {
			$selectFieldUid = $replaceConf['selectFieldUid'];
			$selectVal = $powermail->sessionfields['uid' .$selectFieldUid];
			$selectCondition = $replaceConf['uid_condition.'];
			$uid = $selectCondition[$selectVal];
			$where  = 'deleted=0 AND uid=' . $uid;
			$data = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow(
					'flexform',' tx_powermail_fields',$where);
			$GLOBALS['TYPO3_DB']->sql_free_result($result);
			$teile = explode("\n", $data['flexform']);
			$aktuellerWert = $powermail->sessiondata['uid' . $uid];
			foreach ($teile as $value) {
				$splitarray = explode("|", $value);
				if(count($splitarray)==2) {
					if(trim($splitarray[1]) == $aktuellerWert) {
						$thema = $splitarray[0];
					}
				}
			}
			$powermail->sessiondata['uid' . $uid] = $thema;
			$powermail->cObj->data = $powermail->cObj->substituteMarkerInObject(
					$powermail->cObj->data,
					array('###THEMA###'=>$thema)
			);
			$replaceMarker = $replaceConf['replace_marker.'];
			foreach ($replaceMarker as $title=>$replaceData) {
				$replaceVal[$title] = $replaceData[$selectVal];
				$powermail->sessiondata['uid' . $selectFieldUid] = $replaceVal[$title];
				$powermail->cObj->data = $powermail->cObj->substituteMarkerInObject(
						$powermail->cObj->data,
						array('###UID' . $selectFieldUid . '###'=>$replaceVal[$title])
				);
			}
		}
		
		if (isset($conf['studienberatung.'])) {
			if (isset($conf['studienberatung.']['anfrageBearbeitenPid']) &&
					isset($conf['studienberatung.']['marker'])) {
				
				$benutzer = $GLOBALS['TSFE']->fe_user->user['username'];
				$themaId = $powermail->sessionfields['uid' . $uid];
				$zielGruppe = $replaceVal['zielgruppe.'];
				$anfrage = $powermail->sessiondata['uid' . $conf['studienberatung.']['uidAnfrage']];
				$onlineSb = t3lib_div::makeInstance('tx_he_tools_online_sb',$this);
				$id = $onlineSb->erzeugeNeueAnfrage($thema,$themaId,$zielGruppe,$anfrage,$benutzer);
				$anfrageBearbeitenPid = $conf['studienberatung.']['anfrageBearbeitenPid'];
				$args = '&anfrageId=' . $id;
				$bearbeitungsLink = '<a href="http://www.hs-esslingen.de/index.php?id=' . $anfrageBearbeitenPid . $args . '">' .
														'Anfrage bearbeiten</a>';
				$marker = '###' . strtoupper($conf['studienberatung.']['marker']) . '###';
				$powermail->cObj->data = $powermail->cObj->substituteMarkerInObject(
						$powermail->cObj->data,
						array($marker=>$bearbeitungsLink)
				);
			}
		}
		return FALSE;
	}
	
	public function PM_SubmitEmailHook(&$subpart,&$maildata,&$sessiondata,&$markerArray,&$powermail) {
		if ($powermail->cObj->cObjGetSingle($powermail->conf['allow.']['sender_overwrite'], 
																				$powermail->conf['allow.']['sender_overwrite.'])) {
			if ($subpart == 'sender_mail') {
				$email = $powermail->cObj->cObjGetSingle($powermail->conf['email.']['sender_mail.']['receiver.']['email'], 
																								 $powermail->conf['email.']['sender_mail.']['receiver.']['email.']);
				if (t3lib_div::validEmail($email)) { 
					$maildata['receiver'] = $email; 
				}
			}		
			$senderName = $powermail->cObj->cObjGetSingle($powermail->conf['email.']['sender_mail.']['sender.']['name'], 
																								 $powermail->conf['email.']['sender_mail.']['sender.']['name.']);
			if (!empty($senderName)) {
				if ($senderName=='empty') {
					$maildata['sendername'] = $maildata['sender'];
				} else {
					$maildata['sendername'] = $senderName;
				}
			}
		}
		$conf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_hetools_pi1.']['powermail.'];
		if (!empty($conf['receiverlist_replace.'])) {
			$replaceConf = $conf['receiverlist_replace.'];
		} else if (!empty($conf['field_replace.'])) {
			$replaceConf = $conf['field_replace.'];
		}
		if (!empty($replaceConf)) {
			$uid = $selectCondition[$selectVal];
			$thema = $powermail->sessiondata['uid' . $uid];
			$maildata['subject'] = str_replace('###THEMA###',$thema,$maildata['subject']);			
		}
		if (!empty($conf['receiverlist_replace.'])) {
		
			$selectFieldUid = $conf['receiverlist_replace.']['selectFieldUid'];
			$selectVal = $powermail->sessionfields['uid' .$selectFieldUid];
			$selectCondition = $conf['receiverlist_replace.']['uid_condition.'];
			
			$uid = $selectCondition[$selectVal];
			$mailId = $powermail->sessionfields['uid' . $uid];
			$thema = $powermail->sessiondata['uid' . $uid];
			
			$senderName = $powermail->sessiondata['uid' . $conf['receiverlist_replace.']['senderName']];
			
			$senderEmail = $powermail->sessiondata['uid' . $conf['receiverlist_replace.']['senderEmail']];
			if (empty($senderName)) {
				$senderName = $maildata['sendername'];
			}
			if (empty($senderEmail)) {
				$senderEmail = $maildata['sender'];
			}
			$where  = 'deleted=0 AND uid=' . $mailId;
			$data = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('email',' tx_he_personen',$where);
			$GLOBALS['TYPO3_DB']->sql_free_result($result);
			if ($subpart == 'sender_mail') {
				$maildata['receiver'] = $senderEmail;
				$maildata['receivername'] = $senderName;
				$maildata['sender'] = $data['email'];
				$maildata['sendername'] = 'Hochschule Esslingen';
			} else {
				$maildata['receiver'] = $data['email'];
				$maildata['sender'] = $senderEmail;
				$maildata['sendername'] = $senderName;
			}
		}
/*		
if ($GLOBALS['TSFE']->fe_user->user['username']=='mmirsch' || $GLOBALS['TSFE']->fe_user->user['username']=='sb_online_mmirsch') {
	t3lib_div::devlog("maildata nachher","PM_SubmitEmailHook",0,$maildata);
	t3lib_div::devlog("sendername nachher","PM_SubmitEmailHook",0,$senderName);
}
*/
		
	}	

	public function anzahlEintraege($conditions,$pid) {
		$summe = 0;
		if (!empty($conditions['tests']) && count($conditions['tests'])>0) {
			foreach($conditions['tests'] as $condition)  {
				$where = 'deleted=0 AND hidden=0 AND pid=' . $pid;
				if (!empty($condition['db_field']) && !empty($condition['check_string'])) {
					$where .= ' AND ' . $conditions['db_field'] . ' LIKE "%' . $condition['check_string'] . '%"';
				}
			
				$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid','tx_powermail_mails',$where);
				$anzahl = $GLOBALS['TYPO3_DB']->sql_num_rows($abfrage);
				$summe += $anzahl*$condition['count'];
				$GLOBALS['TYPO3_DB']->sql_free_result($abfrage);
			}
		} else if (!empty($conditions['maxCount'])) {
			$where = 'deleted=0 AND hidden=0 AND pid=' . $pid;
			$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid','tx_powermail_mails',$where);
			$anzahl = $GLOBALS['TYPO3_DB']->sql_num_rows($abfrage);
			$summe += $anzahl;
			$GLOBALS['TYPO3_DB']->sql_free_result($abfrage);
		}
		return $summe;
	}
	
	public function blockElem($cssClass,$fieldType,&$pObj,$hinweis) {
		$startString = '<' . $fieldType . ' class="' . $cssClass;
		$endString = '</' . $fieldType . '>';
		$startFieldset = strpos($pObj->content,$startString);
		$endFieldset = strpos($pObj->content,$endString,$startFieldset)+strlen($endString);
		$contentNew = substr($pObj->content,0,$startFieldset-1) . 
									$hinweis .
									substr($pObj->content,$endFieldset);
		
		$pObj->content = $contentNew;
	}	

	public function PM_ConfirmationHook(&$markerArray,&$powermail) {		
		$conf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_hetools_pi1.']['powermail.'];
		$replaceConf = '';
		if (!empty($conf['receiverlist_replace.'])) {
			$replaceConf = $conf['receiverlist_replace.'];
		} else if (!empty($conf['field_replace.'])) {
			$replaceConf = $conf['field_replace.'];
		}
		if (!empty($replaceConf)) {
			$selectFieldUid = $replaceConf['selectFieldUid'];
			$selectVal = $powermail->sessionfields['uid' .$selectFieldUid];
			$selectCondition = $replaceConf['uid_condition.'];
			$uid = $selectCondition[$selectVal];
			$where  = 'deleted=0 AND uid=' . $uid;
			$data = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow(
					'flexform',' tx_powermail_fields',$where);
			$GLOBALS['TYPO3_DB']->sql_free_result($result);
			$teile = explode("\n", $data['flexform']);
			$aktuellerWert = $markerArray['###UID' . $uid . '###'];
			foreach ($teile as $value) {
				$splitarray = explode("|", $value);
				if(count($splitarray)==2) {
					if(trim($splitarray[1]) == $aktuellerWert) {
						$neuerWert = $splitarray[0];
					}
				}
			}
			$markerArray['###POWERMAIL_ALL###'] = str_replace($aktuellerWert,$neuerWert,$markerArray['###POWERMAIL_ALL###']);
			
			$replaceMarker = $replaceConf['replace_marker.'];
			foreach ($replaceMarker as $title=>$replaceData) {
				$replaceVal[$title] = $replaceData[$selectVal];
				$markerArray['###POWERMAIL_ALL###'] = str_replace($selectVal,$replaceVal[$title],$markerArray['###POWERMAIL_ALL###']);
			}
		}
	}
	
	public function PM_MandatoryHook(&$error, $markerArray, $innerMarkerArray, &$sessionfields, &$pObj) {
		$conf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_hetools_pi1.']['powermail.'];
		if (is_array($conf['mandatory_dependencys.'])) {
			foreach($conf['mandatory_dependencys.'] as $mandatoryData) {
				$fieldId = $mandatoryData['mandatory_field'];
				$dependentFieldId = $mandatoryData['dependent_field'];
				$dependendValue =  $mandatoryData['dependent_value'];
				$dependendValueList = explode(',',$dependendValue);
				$mandatoryFieldId = $mandatoryData['mandatory_field']; 
				$selectVal = $sessionfields['uid' . $dependentFieldId];
				if (!in_array($selectVal,$dependendValueList)) {
					if (isset($sessionfields['ERROR'][$mandatoryFieldId])) {
						unset($sessionfields['ERROR'][$mandatoryFieldId]);
					}
				}
				$selectVal = $sessionfields['uid' . $dependentFieldId];
				$condition1 = 'dependentVal!="' . implode('" && dependentVal!="',$dependendValueList) . '"';
				$condition2 = 'dependentVal=="' . implode('" || dependentVal=="',$dependendValueList) . '"';
			}
		}
		
		if (is_array($conf['block_elements.'])) {
			$blockElementConfig = $conf['block_elements.'];
			foreach ($blockElementConfig as $cssClass=>$data) {
				switch ($data['type']) {
				case 'fieldset':
					$ids = array();
					$where = 'fieldset=' . $data['uid'] . ' AND deleted=0 AND hidden=0';
					$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid',' tx_powermail_fields',$where);
					while ($data = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
						$ids[] = $data['uid'];
					}
					$GLOBALS['TYPO3_DB']->sql_free_result($abfrage);
					foreach($ids as $id) {
						if (isset($sessionfields['ERROR'][$id])) {
							unset($sessionfields['ERROR'][$id]);
						}
					}
					break;
				case 'field':
					if (isset($sessionfields['ERROR'][$data['uid']])) {
						unset($sessionfields['ERROR'][$data['uid']]);
					}
					break;
				}
			}
		}
		if (empty($sessionfields['ERROR'])) {
			unset($sessionfields['ERROR']);
			$error = 0;
		}
	}	

	public function PM_MainContentAfterHook($content, $piVars, &$pObj) {
		$conf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_hetools_pi1.']['powermail.'];
		if ($conf['pid']==$GLOBALS['TSFE']->id) {
			if (is_array($conf['redirect_select_val.'])) {
				$fieldId = 'uid' . $conf['redirect_select_val.']['condition.']['field_id'];
				$fieldVal = $conf['redirect_select_val.']['condition.']['field_val'];
				$op = $conf['redirect_select_val.']['condition.']['compare_op'];
				switch ($op) {
					case 'equal':
						$redirect = $piVars[$fieldId] == $fieldVal;
						break;
					case 'unequal':
						$redirect = $piVars[$fieldId] != $fieldVal;
						break;
					default:
						$redirect = FALSE;
				}
				if ($redirect) {
					$id = $conf['redirect_select_val.']['redirect_page'];
					if (!empty($id)) {
						$conf = array(
								'parameter' => $id,
								'returnLast' => 'url',
						);
						$redirectUrl = $pObj->cObj->typoLink('', $conf);
						t3lib_utility_Http::redirect($redirectUrl);
					}
				}
				
			}
		}

		if ($piVars['mailID'] > 0 || $piVars['sendNow'] > 0) {
			return ; 
		}
		
		if ($conf['pid']==$GLOBALS['TSFE']->id) {
			if (is_array($conf['redirect.']['condition.'])) {
				$redirectConditions = array();
				$redirectConfig = $conf['redirect.']['condition.'];
				$redirectFieldsets = $redirectConfig['fieldsets.'];
				foreach ($redirectFieldsets as $cssClass=>$data) {
					$cssClass = substr($cssClass,0,strlen($cssClass)-1);
					$conditions[$cssClass] = FALSE;
					$conditions = array();
					foreach ($data as $name=>$val) {
						switch ($name) {
							case 'count':
								$maxCount = $val;
								$conditions['maxCount'] = $val;
								break;
							case 'db_field':
								$conditions['db_field'] = $val;
								break;
							case 'conditions.':
								foreach ($val as $id=>$conditionData) {
									$conditions['tests'][] = array('check_string'=>$conditionData['check_string'],
											'count'=>$conditionData['count']);
								}
								break;
							case 'field_conditions.':
								foreach ($val as $id=>$conditionData) {
									$conditions['tests'][] = array('check_string'=>$conditionData['check_string'],
											'count'=>$conditionData['count']);
								}
								break;
						}
					}

					if (!empty($maxCount) && !empty($conditions)) {
						$anzahlEintraege = $this->anzahlEintraege($conditions,$conf['pid']);

						if ($anzahlEintraege>=$maxCount) {
							$redirectConditions[$cssClass] = TRUE;
						}
					}
				}
				
				if (!empty($redirectConditions)) {
					
					$boolOp = strtoupper(trim($redirectConfig['bool_op']));
					$redirect = FALSE;
					if ($boolOp=='AND') {
						$redirect = TRUE;
						foreach ($redirectConditions as $field=>$val) {
							if (!$val) {
								$redirect = FALSE;
							}
						}
					} else if ($boolOp=='OR') {
						$redirect = FALSE;
						foreach ($redirectConditions as $field=>$val) {
							if ($val) {
								$redirect = TRUE;
							}
						}
					}
					if ($redirect) {
						$id = $conf['redirect.']['redirect_page'];
						if (!empty($id)) {
							$conf = array(
									'parameter' => $id,
									'returnLast' => 'url',
							);
							$redirectUrl = $pObj->cObj->typoLink('', $conf);
							t3lib_utility_Http::redirect($redirectUrl);
						}
					}
				}
			}
			if (is_array($conf['block_elements.'])) {
				$blockElementConfig = $conf['block_elements.'];
				foreach ($blockElementConfig as $cssClass=>$data) {
					$cssClass = substr($cssClass,0,strlen($cssClass)-1);
					$conditions = array();
					foreach ($data as $name=>$val) {
						switch ($name) {
							case 'type':
								$fieldType = $val;
								break;
							case 'count':
								$maxCount = $val;
								break;
							case 'hinweis':
								$hinweis = $val;
								break;
							case 'db_field':
								$conditions['db_field'] = $val;
								break;
							case 'conditions.':
								foreach ($val as $id=>$conditionData) {
									$conditions['tests'][] = array('check_string'=>$conditionData['check_string'],
											'count'=>$conditionData['count']);
								}
								$hinweis = $val;
								break;
			
						}
					}
					$anzahlEintraege = $this->anzahlEintraege($conditions,$conf['pid']);
					if ($anzahlEintraege>=$maxCount) {
						$this->blockElem($cssClass,$fieldType,$pObj,$hinweis);
					}
				}
			}
			if (is_array($conf['mandatory_dependencys.'])) {
				$additionalJs = '
				<script type="text/javascript">
				$(document).ready(function() {
				';
				foreach($conf['mandatory_dependencys.'] as $mandatoryData) {
					$fieldId = $mandatoryData['mandatory_field'];
					$dependentFieldId = $mandatoryData['dependent_field'];
					$dependendValue =  $mandatoryData['dependent_value'];
					$dependendValueList = explode(',',$dependendValue);
					$condition1 = 'dependentVal!="' . implode('" && dependentVal!="',$dependendValueList) . '"';
					$condition2 = 'dependentVal=="' . implode('" || dependentVal=="',$dependendValueList) . '"';
					$additionalJs .= '
						var dependentVal = $("#uid' . $dependentFieldId . '").val();
						if (' . $condition1 . ') {
						$("#uid' . $fieldId . '").removeAttr("required");
						}
						$("#uid' . $dependentFieldId . '").change(function() {
							var dependentVal = $("#uid' . $dependentFieldId . '").val();
							if (' . $condition2 . ') {
								$("#uid' . $fieldId . '").attr("required","required");
							} else {
								$("#uid' . $fieldId . '").removeAttr("required");
							}
						})
					';
				}
				$additionalJs .= '
			});
			</script>
			';
			$pObj->content = $pObj->content . $additionalJs;
			}
		}
		if (!empty($conf['replace_uid'])) {
			$uid = $conf['receiverlist'];
			$where  = 'deleted=0 AND uid=' . $uid;
			$data = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow(
					'flexform',' tx_powermail_fields',$where);
			$GLOBALS['TYPO3_DB']->sql_free_result($result);
//			preg_match('>|(.*?)|'.$uid,$data['flexform'], $thementitel);
			$teile = explode("\n", $data['flexform']);
		
			foreach ($teile as $value) {
				$splitarray = explode("|", $value);
				if(count($splitarray)==2) {
					if(trim($splitarray[1]) == $sessiondata['uid' . $uid]) {
						$wert = $splitarray[0];
					}
				}
			}
/*			
			$powermail->sessiondata['uid' . $uid] = $wert;
			$powermail->sessionfields['uid' . $uid] = $wert;
			$powermail->cObj->data = $powermail->cObj->substituteMarkerInObject(
					$powermail->cObj->data,
					array('###THEMA###'=>$wert)
			);
*/			
		}
	}
	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_tools/hooks/class.tx_he_powermail_hooks.php']) {
    include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_tools/hooks/class.tx_he_powermail_hooks.php']);
}
?>