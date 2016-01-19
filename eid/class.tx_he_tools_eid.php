<?php
require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(t3lib_extMgm::extPath('he_tools') . 'lib/class.tx_he_tools_a_bis_z.php');
require_once(t3lib_extMgm::extPath('he_tools') . 'lib/class.tx_he_tools_lib_db_suche.php');
require_once(t3lib_extMgm::extPath('he_tools') . 'mod1/class.tx_he_tools_pers_verwaltung.php');
require_once(t3lib_extMgm::extPath('he_tools') . 'mod1/class.tx_he_tools_alias.php');
require_once(t3lib_extMgm::extPath('he_tools') . 'mod1/class.tx_he_tools_qr_codes.php');
require_once(t3lib_extMgm::extPath('he_tools') . 'lib/class.tx_he_tools_solr.php');
require_once(t3lib_extMgm::extPath('he_tools') . 'lib/class.tx_he_tools_lsf.php');
require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_gast_kennungen.php');
require_once(t3lib_extMgm::extPath('he_tools') . 'lib/class.tx_he_tools_util.php');
require_once(t3lib_extMgm::extPath('he_tools') . 'lib/class.tx_he_tools_infoscreen.php');


define('HTML_START','<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN '.
										'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'  . "\n" .
										'<html dir="ltr" xmlns="http://www.w3.org/1999/xhtml"'.
										' xml:lang="de-DE" lang="de-DE">' . "\n");
define('HTML_ENDE','</html>'."\n");

class tx_he_tools_eid extends tslib_pibase {

  function main() { 	
  	$feUserObj = tslib_eidtools::initFeUser(); // Initialize FE user object		
    tslib_eidtools::connectDB(); //Connect to database
    
    
    
    $id = t3lib_div::_GP('id');
    $action = t3lib_div::_GP('action');
    $username = $feUserObj->user[username];
    $get = t3lib_div::_GET();
    $post = t3lib_div::_POST();
    
    
//t3lib_div::devLog('$get: ' . print_r($get,true), 'tx_he_tools_eid', 0);
//t3lib_div::devLog('$post: ' . print_r($post,true), 'tx_he_tools_eid', 0);
    /** @var  $dbSuche  tx_he_tools_lib_db_suche */
    $dbSuche = t3lib_div::makeInstance('tx_he_tools_lib_db_suche');
		if ($action==='hochschule_a_bis_z_suche') {
    	$buchstabe = $get['buchstabe'];
    	$eingabe = $get['eingabe'];
    	$trenner = $get['trenner'];
    	return $dbSuche->hochschuleABisZSucheGetList($eingabe,$buchstabe,$trenner,$username);
    } else if ($action==='abfall_a_bis_z_suche') {
    	$buchstabe = $get['buchstabe'];
    	$eingabe = $get['eingabe'];
    	$trenner = $get['trenner'];
    	return $dbSuche->abfallABisZSucheGetList($eingabe,$buchstabe,$trenner);
    } else if ($action==='ajaxContentForm') {
    	$data = array();
			foreach ($get as $key=>$val) {
				if ($key!='eID' && $key!='app' && $key!='action') {
					$data[$key] = $val;
				}
				$data['username'] = $username;
			}
    	return $dbSuche->ajaxContentFormGetList($get['app'],$data);
    } else if ($action==='personensuche') {
    	if (empty($username)) {
	    	exit();
	    }
    	
    	if (isset($get['eingabe'])) {
				$eingabe = $get['eingabe'];
			} else {
				$eingabe = '';
			}
			if (isset($get['bereich'])) {
				$bereich = $get['bereich'];
			} else {
				$bereich = '';
			}
			if (isset($get['rolle'])) {
				$rolle = $get['rolle'];
			} else {
				$rolle = '';
			}
     	return $dbSuche->personenSucheGetList($eingabe,$bereich,$rolle);
    } else if ($action==='typo3_be_userliste') {
    	return tx_he_tools_pers_verwaltung::printBenutzerlisteBackend($get['val'],$get['groups']);
    } else if ($action==='typo3_fe_userliste') {
    	return tx_he_tools_pers_verwaltung::printBenutzerlisteFrontend($get['val'],$get['studis'],$get['groups']);
    } else if ($action==='typo3_fe_userliste_ohne_backend') {
    	return tx_he_tools_pers_verwaltung::printBenutzerlisteFrontendOhneBackend(trim($get['val']));
    } else if ($action==='addBeUser') {
    	$returnUrl = $get['returnUrl'];
    	$username = trim($get['fe_username']);
    	return tx_he_tools_pers_verwaltung::addBackendUserFromFrontendUserData($username,$returnUrl);
		} else if ($action==='typo3_be_aliasliste_search') {
			return tx_he_tools_alias::printAliaslisteSearch($get['scriptUrl'],$get['val']);
		} else if ($action==='typo3_be_aliasliste_id') {
			return tx_he_tools_alias::printAliaslisteId($get['scriptUrl'],$get['val']);
    } else if ($action==='typo3_be_aliasLoeschen') {
			return tx_he_tools_alias::aliasLoeschen($get['aliasUid']);
		} else if ($action==='erzeugeKurzUrl') {
			if (isset($get['length'])) {
				$length = $get['length'];
			} else {
				$length = '';
			}
			$kurzUrl = tx_he_tools_alias::erzeugeKurzUrl($length);
			self::returnTextData($kurzUrl);
		} else if ($action==='qr_url') {
			$url = $get['url'];
			if (isset($get['size'])) {
				$size = $get['size'];
			} else {
				$size = '';
			}
			return tx_he_tools_qr_codes::getUrlLink($url,$size);
		} else if ($action==='download_qr_code') {
			if (!isset($get['url'])) {
				return 'Keine URL übergeben!';
			} else {
				$url = $get['url'];
				if (isset($get['size'])) {
					$size = $get['size'];
				} else {
					$size = '';
				}
				if (isset($get['alias'])) {
					$alias = $get['alias'];
				} else {
					$alias = '';
				}
				return tx_he_tools_qr_codes::downloadQrCode($url,$alias,$size);
			}

		} else if ($action==='qr_alias_liste') {
			if (isset($get['quality'])) {
				$quality = $get['quality'];
			} else {
				$quality = '';
			}
			return tx_he_tools_qr_codes::printAliasliste($get['val'],$quality);
		} else if ($action==='solr_action') {
    	$solr = t3lib_div::makeInstance('tx_he_tools_solr');
    	return $solr->eidAction($get);
    } else if ($action==='get_page_tstamp') {
      $res = tx_he_tools_util::getPageTstamp($get['uid']);
      $this->returnTextData($res);
    } else if ($action==='get_infoscreen_page_tstamp') {
      $res = tx_he_tools_infoscreen::getInfoscreenPageTstamp($get['uid']);
      $this->returnTextData($res);
    } else if ($action==='fe_logout') {
    	$where = 'ses_id = "' . $feUserObj->user['ses_id'] . '" AND ses_name = "fe_typo_user"';
    	$GLOBALS['TYPO3_DB']->exec_DELETEquery('fe_sessions',$where);
    	print 1;
    	exit();    	
    } else if ($action==='fe_user_exists') {
     	$username = $get['username'];
     	$result = 0;
     	if (!empty($username)) {
    		$where = 'username = "' . $username . '" AND deleted=0 and disable=0';
    		$anzahl = $GLOBALS['TYPO3_DB']->exec_SELECTcountRows('uid','fe_users',$where);
    		if ($anzahl>0) {
    			$result = 1;
    		}
     	}
    	print $result;
    	exit();    	
    } else if ($action==='gib_lsf_modb_vertiefungen') {
    	$lsf = t3lib_div::makeInstance('tx_he_tools_lsf');
    	$modulId = $get['modId'];
    	$data = $lsf->gibVertiefungenSelect($modulId);
    	$this->returnTextData($data);
    } else if ($action==='gib_lsf_modb_versionen') {
    	$lsf = t3lib_div::makeInstance('tx_he_tools_lsf');
    	$vertiefung = $get['vertiefung'];
    	$version = $get['version'];
    	$data = $lsf->gibVersionenSelect($vertiefung);
    	$this->returnTextData($data);
    } else if ($action==='test_gastkennungen_csv_exportiert') {
    	$gastKennungen = t3lib_div::makeInstance('tx_he_tools_gast_kennungen');
    	$uid = $get['uid'];
    	$csvTest = $gastKennungen->csvDatenExportiert($uid);
    	$this->returnJsonData($csvTest);
    	exit();
    } else if ($action==='gastkennung_loeschen') {
      $gastKennungen = t3lib_div::makeInstance('tx_he_tools_gast_kennungen');
      $antragsId = $get['antragsId'];
      $gastKennungen->gastKennungLoeschen($antragsId,$username);
      exit();
    } else if ($action==='file_download') {
      $fileUrl = base64_decode($get['file']);
      return tx_he_tools_util::downloadFile($fileUrl);
    }
    return false;
  }


  function returnJsonData($data) {
		header('Content-type: application/json');
		print json_encode($data);
		exit();  	
  }
  
  function returnTextData($data) {
		header('Content-Type: text/html; charset=utf-8');
		print($data);
		exit();  	
  }
  
  function defaultJs() {
		return '<script type="text/javascript">
		document.domain = "hs-esslingen.de";
		function fensterSchliessen() {	
			self.close(); 
		}

		function updateParent() {	
			opener.location.reload();
		}
		
		</script>' . "\n";
  }

	function importJs($js) {
		$out = $this->defaultJs();
		if (!empty($js)) {
			$out .= $js . "\n";
		}
		return $out;
	}

  function importCss($css) {
		$out = '<link type="text/css" rel="stylesheet" href="/fileadmin/css/eigenes_css/rte.css" />
						<link type="text/css" rel="stylesheet" href="/fileadmin/css/eigenes_css/portal.css" />
						<link type="text/css" rel="stylesheet" href="/fileadmin/css/eigenes_css/extjs_popup.css" />
						';
		$out .= $css . "\n";
		return $out;
	}

	function htmlBody($body) {
		$out = '<body id="tx_sgeb_popup">' . "\n";
		$out .= $body . "\n";
		$out .= '</body>' . "\n";
		$out .= HTML_ENDE;
		return $out;
	}

	function htmlHead($title,$css='',$js='') {
		$out = HTML_START;
		$out .= '<head>' . "\n";
		$out .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . "\n";
		$out .= '<title>' . $title . '</title>' . "\n";
		$out .= $this->importCss($css) . "\n";
		$out .= $this->importJs($js) . "\n";
		$out .= '</head>' . "\n";
		return $out;
  }
}

$output = t3lib_div::makeInstance('tx_he_tools_eid');
$output->main();

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_tools/eid/class.tx_he_tools_eid.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_tools/eid/class.tx_he_tools_eid.php']);
}
?>