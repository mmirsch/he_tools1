<?php

require_once(t3lib_extMgm::extPath('he_tools') . 'lib/class.tx_he_tools_util.php');
require_once(t3lib_extMgm::extPath('solr') . 'classes/class.tx_solr_connectionmanager.php');
require_once(t3lib_extMgm::extPath('solr') . 'classes/class.tx_solr_solrservice.php');
require_once(t3lib_extMgm::extPath('solr') . 'lib/SolrPhpClient/Apache/Solr/Service.php');
require_once(t3lib_extMgm::extPath('solr') . 'lib/SolrPhpClient/Apache/Solr/Response.php');

class tx_he_tools_solr	{
	public $solr;
	
	function __construct(
			$solrHost = 'solr.hs-esslingen.de',
			$solrPort = '8080',
			$solrPath = '/solr/live_de/') {
			$this->solr = t3lib_div::makeInstance('tx_solr_ConnectionManager')->getConnection(
				$solrHost,$solrPort,$solrPath);
	}
	
	function commit() {
		return $this->solr->commit();
	}
	
	function search($suchBegriff,$offset=0,$anzahl=10) {
		$response = $this->solr->search($suchBegriff,$offset,$anzahl);
		$obj = json_decode($response->getRawResponse());
		return $obj->response->docs;
	}
	
	function searchAnz($suchBegriff,$offset=0,$anzahl=10) {
		$response = $this->solr->search($suchBegriff,$offset,$anzahl);
		$obj = json_decode($response->getRawResponse());
		return $obj->response->numFound;
	}
	
	function deleteSinglePage($pageId) {
		$result = $this->solr->deleteByQuery('uid:'. $pageId);
		if (!$result) {
			return FALSE;
		} else {
			return $this->solr->commit();
		}
	}
	
	function deletePageList(&$pageList,$logging=TRUE) {
    $currentDate = time();
		if (!empty($pageList)) {
			foreach ($pageList as $pageId) {
				$result = $this->solr->deleteByQuery('uid:'. $pageId);
				if (!$result) {
					return FALSE;
				}
				if ($logging) {
          $data = array(
            'pageId'=>$pageId,
            'tstamp'=>$currentDate);
					$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_hetools_solr_submitted_disabled_pages',$data);
				}
			}
			$result = $this->solr->commit();
			if (!$result) {
				return FALSE;
			}
		}
		return true;
	}
	
	function updateDisabledSolrPages() {
    /*
     * Aktivierte Seiten ermitteln
     */
		$wherePagesOnline = 'deleted=0 AND hidden=0
													AND uid IN (SELECT pageId FROM tx_hetools_solr_submitted_disabled_pages)';
		$pagesOnline = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid','pages',$wherePagesOnline);
    /*
     * Aktivierte Seiten aus der Tabelle löschen
     */
		while ($dataPagesOnline = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($pagesOnline)) {
			$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_hetools_solr_submitted_disabled_pages','pageId=' . $dataPagesOnline['uid']);
		}
    /*
     * In TYPO3 nicht mehr vorhandene Seiten aus der Tabelle löschen
     */
		$whereTypo3MissingPages = 'pageId not in (SELECT uid from pages)';
		$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_hetools_solr_submitted_disabled_pages',$whereTypo3MissingPages);
	}
	
	function submitDeletedPages() {
		$where = '(deleted=1 OR hidden=1) AND doktype<>254 AND
               uid NOT IN (
                SELECT tx_hetools_solr_submitted_disabled_pages.pageId FROM tx_hetools_solr_submitted_disabled_pages
                INNER JOIN pages ON tx_hetools_solr_submitted_disabled_pages.pageId=pages.uid
                where pages.tstamp<tx_hetools_solr_submitted_disabled_pages.tstamp
              )
  ';
		$disabledPages = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid','pages',$where,'','','0,100');
		while ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($disabledPages)) {
			$pageList[] = $daten['uid'];
		}
		return $this->deletePageList($pageList);
		
	}
	
	public function eidAction($args) {
		$result = 1;
		switch ($args['cmd']) {
			case 'deleteEntry':
				if (!empty($args['uid'])) {
					$result = $this->deleteSinglePage($args['uid']);
				}
				break;
		}
		echo $result;
		exit();
	}
	
	public function main(&$parent,$seite) {
		$post = t3lib_div::_POST();
		$seitenId = trim($post[seitenId]);
		if (empty($seitenId)) {
			$seitenId = $seite;
		}
		$suchindexLoeschen = trim($post[suchindexLoeschen]);
		$suchindexCommit = trim($post[suchindexCommit]);
		$suchBegriff = trim($post[suchBegriff]);
		$suchBegriffTesten = trim($post[suchBegriffTesten]);
		$solrSuchbegriffAnzeigen = trim($post[solrSuchbegriffAnzeigen]);
		$suchBegriffLoeschen = trim($post[suchBegriffLoeschen]);
	
		$erg = '<h2>Seitenbaum aus dem Suchindex löschen</h2>';
		$erg .= '<div class="artikelexporte">';
		$erg .= '<form name="artikel-export" method="post" action="'.$this->file.'">';
		$erg .= '<input type="text" name="seitenId" value="' . $seitenId . '"/><br/><br/>';
		$erg .= '<input type="submit" name="suchindexLoeschen" value="Seitenbaum im SOLR-Index löschen"/><br/><br/>';
	
		$erg .= '<input type="text" name="suchBegriff" value="' . $suchBegriff . '"/><br/>';
		$erg .= '<input type="submit" name="suchBegriffTesten" value="Suchbegriff testen"/>';
	
		$erg .= '&nbsp;<input type="submit" name="solrSuchbegriffAnzeigen" value="Alle Suchergebnisse anzeigen"/>';
		$erg .= '&nbsp;<input type="submit" name="suchBegriffLoeschen" value="Alle Einträge zum Suchbegriff in SOLR löschen"/><br/><br/>';
	
		$erg .= '<input type="submit" name="suchindexCommit" value="Suchindex übertragen (commit)"/><br/><br/>';
		$erg .= '</form>';
	
		if (!empty($seitenId) && !empty($suchindexLoeschen)) {
			$erg .= $this->solrSeitenbaumLoeschen($seitenId);
		}	else if (!empty($suchindexCommit)) {
			$erg .= $this->commit();
		}	else if (!empty($suchBegriff) && !empty($suchBegriffTesten)) {
			$erg .= $this->solrSuchbegriffTesten($suchBegriff);
		}	else if (!empty($suchBegriff) && !empty($solrSuchbegriffAnzeigen)) {
			$erg .= $this->solrSuchbegriffSeitenAnzeigen($suchBegriff);
		}	else if (!empty($suchBegriff) && !empty($suchBegriffLoeschen)) {
			$erg .= $this->solrSuchbegriffLoeschen($suchBegriff);
		}	else {
			$seitenId = $seite;
		}
	
		$erg .= '</div>';
		return $erg;
	}
	
	function solrSeitenbaumLoeschen($seitenId) {
		$seitenListe = array();
		tx_he_tools_util::getPageTree($seitenId,$seitenListe);
		$erg = $this->deletePageList($seitenListe);
		if ($erg) {
			$out = count($seitenListe) . ' Seiten aus dem SOLR-Index gelöscht';
		} else {
			$out = 'Fehler beim Löschen des SOLR-Index';
		}
		return $out;
	
	}
	
	function solrSuchbegriffTesten($suchBegriff) {
		$seitenListe = array();
		$anzahl = $this->searchAnz($suchBegriff,0,1000);
		if ($anzahl) {
			$out = $anzahl . ' Suchergebnisse wurden gefunden';
		} else {
			$out = 'Es gibt kein Ergebnis zu diesem Suchbegriff';
		}
		return $out;
	
	}
	
	function solrSuchbegriffSeitenAnzeigen($suchBegriff) {
		$ergebnisse = $this->search($suchBegriff,0,1000);
		if (count($ergebnisse>0)) {
			$seiten = array();
			foreach ($ergebnisse as $suchergebnis) {
				if (!in_array($seiten,$suchergebnis->uid)) {
					$seiten[$suchergebnis->title] = $suchergebnis->uid;
				}
			}
			ksort($seiten);
			$eidUrl = 'index.php?eID=he_tools&action=solr_action&cmd=deleteEntry';
			$out =  '<script src="../fileadmin/res/jquery/js/jquery-1.9.1.min.js" type="text/javascript"></script>
				<script type="text/javascript">
				function executeAjax(url,reload){
					var result="";
					$.ajax({
						url: url,
						async: false,
						success: function(data, request) {
							if (reload) {
								window.location.reload();			
							}
							result = data; 
						}
					});
					return result;
				}
				</script>
				';
			
			$out .= '<table class="solr_results">';
			$row = 'even';
			foreach ($seiten as $title=>$id) {
				if ($row=='even') {
					$row = 'odd';
				} else {
					$row = 'even';
				}
				$out .= '<tr class="' . $row . '" >';
				$out .= '<td>';
				$out .= '<a target="_blank" href="http://www.hs-esslingen.de/index.php?id=' . $id . '">' .
								$title . ' (' . $id . ')</a><br />';
				$out .= '</td>';
				$out .= '<td>';
				$url = $eidUrl . '&uid=' . $id;
				$out .= '<a target="#" onclick="executeAjax(\'' . $url . '\',true);return false;">
								<span class="icon-actions t3-icon-edit-delete" title="Sucheintrag löschen">
								</span></a>';
				$out .= '</td>';
				$out .= '</tr>';
			}
			$out .= '</table>';
		} else {
			$out = 'Es gibt kein Ergebnis zu diesem Suchbegriff';
		}
		return $out;
	}
	
	function solrSuchbegriffLoeschen($suchBegriff) {
		$seitenListe = array();
		$ergebnisse = $this->search($suchBegriff,0,1000);
	
		if (count($ergebnisse>0)) {
			$seiten = array();
			foreach ($ergebnisse as $suchergebnis) {
				if (!in_array($seiten,$suchergebnis->uid)) {
					$seitenListe[] = $suchergebnis->uid;
				}
			}
			$erg = $this->deletePageList($seitenListe,FALSE);
			if ($erg) {
				$out = count($seitenListe) . ' Seiten wurden aus dem SOLR-Index gelöscht';
			} else {
				$out = 'Fehler beim Löschen des SOLR-Index';
			}
		} else {
			$out = 'Es gibt kein Ergebnis zu diesem Suchbegriff';
		}
		return $out;
	}
	
	
	public function main2($parent,$seite) {
/*
		$url = $parent->file;
		$post = t3lib_div::_POST();
		$seiteLoeschen = $post['seiteLoeschen'];
		if ($seiteLoeschen!='') {
			$seite = $post['seitenId'];
			$erg .= '<h2>Klicke die folgenden beiden Links um die Seite aus dem SOLR-Index zu entfernen!</h2>';
			$erg .= '<a href="http://solr.hs-esslingen.de:8080/solr/live_de/update?stream.body=<delete><query>uid:' . $seite . '</query></delete>" target="_blank">Seite löschen</a><br/>';
			$erg .= '<a href="http://solr.hs-esslingen.de:8080/solr/live_de/update?stream.body=<commit />" target="_blank">Commit</a><br/>';
		}
*/
		$erg = '<h2>Klicke die folgenden beiden Links um die Seite aus dem SOLR-Index zu entfernen!</h2>';
		$erg .= '<a href="http://solr.hs-esslingen.de:8080/solr/live_de/update?stream.body=<delete><query>uid:' . $seite . '</query></delete>" target="_blank">Seite löschen</a><br/>';
		$erg .= '<a href="http://solr.hs-esslingen.de:8080/solr/live_de/update?stream.body=<commit />" target="_blank">Commit</a><br/>';
		return $erg;
		
		
		$erg .= '<div class="solrVerwaltung">';
		$erg .= '<form name="solrVerwaltung" method="post" action="'.$url.'">';
		$erg .= '<label for="seiteLoeschen">Aktuelle Seite aus dem SOLR-Index löschen: </label>';
		$erg .= '<input type="hidden" name="seitenId" value="' . $seite . '" /><br/>';
		$erg .= '<input type="submit" name="seiteLoeschen" value="Seite Löschen" /><br/>';
		$erg .= '</form></div>';
		return $erg;
		
	}
}
?>
