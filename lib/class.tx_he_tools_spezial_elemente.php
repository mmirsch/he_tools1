<?php 
require_once(t3lib_extMgm::extPath('he_portal') . 'lib/class.tx_he_portal_lib_gadgets.php');

class tx_he_tools_spezial_elemente {
	
	public function initGadget(&$cObj,$username,$gadgetId) {
		$out = tx_he_portal_lib_gadgets::renderGadgetHilfeText($gadgetId,$cObj,TRUE,$username);
		if (!empty($out)) {
			$out .= '<div class="hinweis">Anleitungstext ausblenden durch Anklicken des Icons 
							<a onclick="window.open(\'https://www.hs-esslingen.de/index.php?eID=he_portal&action=editGadgetSettings&gadgetId=34\',\'Einstellungen bearbeiten\',\'width=10,height=10,left=100,top=100\');">
							<img title="Einstellungen bearbeiten" style="vertical-align: -20%;" src="typo3conf/ext/he_portal/res/jquery/css/edit.gif" />
							</a>in der Titelleiste dieses Gadgets.</div>';
		}
		return $out;
	}
	
	function faqs(&$parent,$eintraege,$layout,$pageListview=131635) {
		$out = '';
		if ($layout=='FAQ_CLOSED') {
			$out .= '
			<div class="toggle_buttons">
			<a href="#" class="expand-all" data-elemClass="csc-textpic-text">Alle ausklappen</a>
			<a href="#" class="collapse-all" data-elemClass="csc-textpic-text">Alle einklappen</a>
			</div>
				';
		}
		if (!empty($eintraege)) {
			$eintragsListe = explode(',',$eintraege);

			$GLOBALS['TSFE']->additionalHeaderData['info_blase'] .= '
			<link rel="stylesheet" type="text/css" href="' . t3lib_extMgm::siteRelPath('he_tools') . 'res/css/infotexte.css" />
			';
			foreach ($eintragsListe as $faqId) {
				$felder = 'a,q,tx_hetools_max_words';
				$where = 'hidden=0 and deleted=0 and uid=' . $faqId;
				$select = $GLOBALS['TYPO3_DB']->exec_SELECTquery($felder,'tx_irfaq_q',$where);
				if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($select)) {
					$titel = $row['q'];
					$text = $parent->pi_RTEcssText($row['a']);
					$maxWords = $row['tx_hetools_max_words'];
					if (empty($maxWords)) {
						$maxWords = 50;
					}
					switch ($layout) {
						case 'BUBBLE':
							$out .= '<div class="info_blase">
											<em></em>
											<div class="title">' . $titel . '</div>
											<div class="text">' . $this->getFaqBubbleText($parent,$faqId,$text,$maxWords,$pageListview) . '</div>
											</div>
											';
							break;
						case 'FAQ_OPEN':
							$out .= '<div id="faq_' . $faqId . '">
											<h2>' . $titel . '</h2>
											<div class="text">' . $text . '</div>
											</div>
											';
							break;
						case 'FAQ_CLOSED':
							$get = t3lib_div::_GET();
							$collapsed = ' collapsed';
							$expanded = '';
							if (!empty($get['faqId'])) {
								if ($get['faqId']==$faqId) {
									$collapsed = ' expanded';
									$expanded = ' class="expanded"';
								}
							}
							$out .= '<div class="csc-default layout-100">
											<div id="faq_' . $faqId . '"  class="csc-header">
											<h2' . $expanded . '>' . $titel . '</h2>
											<div class="csc-textpic-text' . $collapsed .'">' . $text . '</div>
											</div></div>
											';
							break;
					}
				}
			}
		}
		return $out;
	}
	
	function getFaqBubbleText(&$parent,$id,$text,$maxWords,$pageListview) {
		$words = explode(' ',$text);
		$textNew = '';
		if (count($words)>$maxWords) {
			for($i=0;$i<$maxWords;$i++) {
				$textNew .= $words[$i] . ' ';
			}
			$maxChars = strlen($textNew);
			$pos1 = strripos(substr($text,0,$maxChars),'<a');
			$pos2 = strpos($text,'</a>',$pos1)+4;
			if ($pos1>0 && $pos2>$maxChars) {
				$maxChars = $pos2;
			}
			$out = substr($text,0,$maxChars);	
					
			$typolink_conf = array(
					'returnLast' => "url",
					'parameter' => $pageListview,
					'useCacheHash' => 1,
					'additionalParams' => '&faqId=' . $id,
			);
			$linkUrl = $parent->cObj->typolink('', $typolink_conf);
			$out .= ' ... <a href="' . $linkUrl . '">weiter</a>';
		} else {
			$out = $text; 
		}
		return $out;
	}
	
	function getFaqEntries(&$config) {
		$aktuelleSeite = $config['row']['pid'];
		$sqlSelect = 'SELECT pid FROM pages';
		$sqlWhere = ' WHERE deleted=0 AND uid=' . $aktuelleSeite;
		$sqlQuery = $sqlSelect . $sqlWhere;
		$select = $GLOBALS['TYPO3_DB']->sql_query($sqlQuery);
		$erg = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($select);
		if (count($erg)==1) {
			$elternSeite = $erg['pid'];
			$sqlSelect = 'SELECT tx_irfaq_q.uid,tx_irfaq_q.q,tx_irfaq_q.a FROM pages';
			$sqlJoin = ' INNER JOIN tx_irfaq_q ON tx_irfaq_q.pid=pages.uid';
			$sqlWhere = ' WHERE pages.deleted=0 AND tx_irfaq_q.deleted=0 AND tx_irfaq_q.hidden=0' .
					' AND pages.pid=' . $elternSeite .
					' AND pages.module="irfaq" AND pages.doktype=254';
			$sqlQuery = $sqlSelect . $sqlJoin . $sqlWhere;
			$select = $GLOBALS['TYPO3_DB']->sql_query($sqlQuery);
			$optionList = array();
			$PA['fieldConf']['config'] = array(
					'items' => array());
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($select)) {
				$text = substr(trim($row['q']) . ' / ' . trim($row['a']),0,400);
				$optionList[] = array(0=>$text, 1=>$row['uid']);
			}
			$config['items'] = $optionList;
			return $config;
		}
	}
	
	function getHePersonen(&$config) {
		$aktuelleSeite = $config['row']['pid'];
		$sqlQuery = 'SELECT uid,username,first_name,last_name FROM fe_users WHERE deleted=0 and disable=0 AND
									(
									FIND_IN_SET("86",usergroup ) OR
									FIND_IN_SET("73",usergroup ) OR
									FIND_IN_SET("111",usergroup ) OR
									FIND_IN_SET("72",usergroup ) OR
									FIND_IN_SET("113",usergroup ) OR
									FIND_IN_SET("112",usergroup ) OR
									FIND_IN_SET("99",usergroup ) OR
									FIND_IN_SET("74",usergroup )
									) ORDER BY last_name,first_name';
		$select = $GLOBALS['TYPO3_DB']->sql_query($sqlQuery);
		$optionList = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($select)) {
			$anzeige = $row['last_name'] . ', ' . $row['first_name'] . ' (' . $row['username'] . ')';
			$optionList[] = array(0=>$anzeige, 1=>$row['uid']);
		}
		$config['items'] = $optionList;
		return $config;
	}
	
	function showVvsIframe(&$parent) {
		$vvsStandortCode = array (
				'start' => '<iframe style="float: left; width: 220px; height: 250px; overflow: hidden;" ',
				'src' => ' src="https://www.vvs.de/fileadmin/templatesvvs/efaaufhp/scripts/php/efaaufhp_generator.php?htmlcode=formular&amp;fpauskunft=anabreise&amp;standort=adresse&amp;',
				'standorte' => array (
						'sm' => 'standort_bez=Kanalstra%C3%9Fe+33&amp;standort_ort=Esslingen-Neckar&amp;standort_name=Standort%2BStadtmitte',
						'fl' => 'standort_bez=Flandernstra%C3%9Fe+101&amp;standort_ort=Esslingen-Neckar&amp;standort_name=Standort%2BFlandernstra%25C3%259Fe',
						'gp' => 'standort_bez=G%C3%B6ppingen+%2F+ZOB&amp;standort_ort=G%C3%B6ppingen&amp;standort_name=Standort%2BG%C3%B6ppingen',						
						),
				'ende' => '&amp;fpausgabe=neues_fenster&amp;fpsprache=de&amp;fpshowtime=true"></iframe>',
				);
		$username = $GLOBALS['TSFE']->fe_user->user['username'];
		$gadgetId = tx_he_portal_lib_gadgets::gibGadgetId($GLOBALS['TSFE']->id);
		$out = $this->initGadget($parent->cObj,$username,$gadgetId);
		
		$gadgetEinstellungen = tx_he_portal_lib_gadgets::gadgetEinstellungenLaden($gadgetId, $username);
				
		$standorte = tx_he_portal_lib_gadgets::gibGadgetEinstellungenWert($gadgetEinstellungen,'st');
		$standortListe = array();
		if (count($standorte)>0) {
			foreach ($standorte as $st=>$val) {
				if ($val=='on') {
					$standortListe[] = $st;
				}
			}
		}
		foreach ($standortListe as $standort) {
			$out .= $vvsStandortCode['start'] . $vvsStandortCode['src']  . $vvsStandortCode['standorte'][$standort] . $vvsStandortCode['ende'];
		}
		return $out;
	}
	
}
?>