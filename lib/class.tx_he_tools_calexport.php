<?php

class tx_he_tools_calexport	{
var $extkey;

	public function tx_he_tools_calexport($extkey) {
		$this->extKey = $extkey;
	}

	public function main() {
		$this->post = t3lib_div::_POST();
		$absenden = $this->post[absenden];
		
		/* Informationen über Datenbankstruktur
		 * Hochschulkalender --> ID = 2 (tx_cal_calendar)
		 * Kategorie:
		 *  1 --> Hochschule
		 * 	13 --> Studierendetermin
		 *  14 --> Hochschulkalender
		 *  15 --> Sprechstunden
		 *  31 --> Stipendien
		 *  32 --> Semestertermin
		 *  
		 */
		if ($absenden!='') 
		{
			$kategorie = $this->post[kategorie];
			$startdatum = $this->post[startdate];
			$enddatum = $this->post[enddate];
			$whereClause = '';
			if ($startdatum != '')
			{
				$whereClause .= ' AND tx_cal_event.start_date >= '.$startdatum;			
			}
			if ($enddatum != '')
			{
				$whereClause .= ' AND tx_cal_event.end_date <= '.$enddatum;
			}
			
			switch ($kategorie)
			{
				case "JB":
					$select = 'DISTINCT tx_cal_event.title,tx_cal_event.start_time,tx_cal_event.end_time,tx_cal_event.start_date,tx_cal_event.end_date,tx_cal_event.location,tx_cal_event.location_id,tx_cal_event.description,tx_cal_event.organizer,tx_cal_event.organizer_id';
					$local_table ='tx_cal_event';
					$mm_table ='tx_cal_event_category_mm';
					$foreign_table = 'tx_cal_category';
					$whereClause .= ' AND tx_cal_event.hidden = 0 AND tx_cal_event.deleted = 0 AND tx_cal_event.uid NOT IN (SELECT uid_local FROM tx_cal_event_category_mm where uid_foreign=32)';
					$res = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query($select,$local_table,$mm_table,$foreign_table,$whereClause,$groupBy='','tx_cal_event.start_date,tx_cal_event.start_time',$limit='');
					break;
				case "ST":
					$select = 'title,start_time,end_time,start_date,end_date,location,location_id,description,organizer,organizer_id,category_id';
					$local_table ='tx_cal_event';
					$whereClause = 'hidden = 0 AND deleted = 0 AND category_id = 13';
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select,$local_table,$whereClause);
					break;	
				case "HK":
					$select = 'title,start_time,end_time,start_date,end_date,location,location_id,description,organizer,organizer_id,category_id';
					$local_table ='tx_cal_event';
					$whereClause = 'hidden = 0 AND deleted = 0 AND category_id = 14';
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select,$local_table,$whereClause);
					break;
				case "AN":
					$select = 'title,start_time,end_time,start_date,end_date,location,location_id,description,organizer,organizer_id,category_id';
					$local_table ='tx_cal_event';
					$whereClause = 'hidden = 0 AND deleted = 0 AND category_id = 2';
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select,$local_table,$whereClause);
					break;	
				case "BW":
					$select = 'title,start_time,end_time,start_date,end_date,location,location_id,description,organizer,organizer_id,category_id';
					$local_table ='tx_cal_event';
					$whereClause = 'hidden = 0 AND deleted = 0 AND category_id = 3';
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select,$local_table,$whereClause);
					break;
				case "FZ":
					$select = 'title,start_time,end_time,start_date,end_date,location,location_id,description,organizer,organizer_id,category_id';
					$local_table ='tx_cal_event';
					$whereClause = 'hidden = 0 AND deleted = 0 AND category_id = 4';
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select,$local_table,$whereClause);
					break;
				case "GL":
					$select = 'title,start_time,end_time,start_date,end_date,location,location_id,description,organizer,organizer_id,category_id';
					$local_table ='tx_cal_event';
					$whereClause = 'hidden = 0 AND deleted = 0 AND category_id = 5';
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select,$local_table,$whereClause);
					break;
				case "GS":
					$select = 'title,start_time,end_time,start_date,end_date,location,location_id,description,organizer,organizer_id,category_id';
					$local_table ='tx_cal_event';
					$whereClause = 'hidden = 0 AND deleted = 0 AND category_id = 6';
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select,$local_table,$whereClause);
					break;
				case "MB":
					$select = 'title,start_time,end_time,start_date,end_date,location,location_id,description,organizer,organizer_id,category_id';
					$local_table ='tx_cal_event';
					$whereClause = 'hidden = 0 AND deleted = 0 AND category_id = 7';
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select,$local_table,$whereClause);
					break;
				case "ME":
					$select = 'title,start_time,end_time,start_date,end_date,location,location_id,description,organizer,organizer_id,category_id';
					$local_table ='tx_cal_event';
					$whereClause = 'hidden = 0 AND deleted = 0 AND category_id = 8';
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select,$local_table,$whereClause);
					break;
				case "IT":
					$select = 'title,start_time,end_time,start_date,end_date,location,location_id,description,organizer,organizer_id,category_id';
					$local_table ='tx_cal_event';
					$whereClause = 'hidden = 0 AND deleted = 0 AND category_id = 9';
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select,$local_table,$whereClause);
					break;
				case "SP":
					$select = 'title,start_time,end_time,start_date,end_date,location,location_id,description,organizer,organizer_id,category_id';
					$local_table ='tx_cal_event';
					$whereClause = 'hidden = 0 AND deleted = 0 AND category_id = 10';
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select,$local_table,$whereClause);
					break;	
				case "VU":
					$select = 'title,start_time,end_time,start_date,end_date,location,location_id,description,organizer,organizer_id,category_id';
					$local_table ='tx_cal_event';
					$whereClause = 'hidden = 0 AND deleted = 0 AND category_id = 11';
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select,$local_table,$whereClause);
					break;
				case "WI":
					$select = 'title,start_time,end_time,start_date,end_date,location,location_id,description,organizer,organizer_id,category_id';
					$local_table ='tx_cal_event';
					$whereClause = 'hidden = 0 AND deleted = 0 AND category_id = 12';
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select,$local_table,$whereClause);
					break;	
					
			}
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){ 
			                $daten = array();
			                $daten[title] = $row[title];
			                $daten[start_time] = gmdate("H:i",$row[start_time]); 
							$daten[end_time] = gmdate("H:i",$row[end_time]); 
			                $daten[start_date] = substr($row[start_date],6,2).'.'.substr($row[start_date],4,2).'.'.substr($row[start_date],0,4);
							$daten[end_date] = substr($row[end_date],6,2).'.'.substr($row[end_date],4,2).'.'.substr($row[end_date],0,4);
							$daten[location] = $row[location];
							if ($row[location_id] != 0)
							{
								$location =$GLOBALS['TYPO3_DB']->exec_SELECTquery(
									'name',   #select
								    'tx_cal_location', #from
								    'deleted=0 and uid ='.$row[location_id].''
								    );
								if($row2 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($location)){ 
								   $daten[location] = $row2[name];
								}
							}
							$daten[description] = $row[description];
							$daten[organizer] = $row[organizer];
							if ($row[organizer_id] != 0)
							{
								$organizer =$GLOBALS['TYPO3_DB']->exec_SELECTquery(
									'name',   #select
								    'tx_cal_organizer', #from
								    'deleted=0 and uid ='.$row[organizer_id].''
								    );
								if($row3 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($organizer)){ 
								   $daten[organizer] = $row3[name];
								}
							}
							$eintraege[] = $daten;
					}
			$exportListe = array('title','Startzeit','Endzeit','Startdatum','Enddatum','Ort','Beschreibung','Organisator');
			$erg .= $this->eintraegeExportierenCsv($eintraege,$exportListe);
		} 
		$erg .= '
		<script src="typo3conf/ext/he_tools/res/jquery-1.7.2.min.js"></script>
		<script src="typo3conf/ext/he_tools/res/jquery-ui-1.8.19.custom.min.js"></script>
		';
		$erg .= '<div class="Tools">';
		$erg .= '<form name="Kalenderexport" method="post" action="">';
		$erg .= '<p>Wählen Sie Ihren Kalenderexport aus: <select name="kategorie"></p>';
		$erg .= '<option value="JB">Jahresbericht</option>';
		$erg .= '<option value="ST">Studierendetermin</option>';
		$erg .= '<option value="HK">Hochschulkalender</option>';
		$erg .= '<option value="AN">Angewandte Naturwissenschaften</option>';
		$erg .= '<option value="BW">Betriebswirtschaft</option>';
		$erg .= '<option value="FZ">Fahrzeugtechnik</option>';
		$erg .= '<option value="GS">Graduate School</option>';
		$erg .= '<option value="GL">Grundlagen</option>';
		$erg .= '<option value="IT">Informationstechnik</option>';
		$erg .= '<option value="BW">Betriebswirtschaft</option>';
		$erg .= '<option value="MB">Maschinenbau</option>';
		$erg .= '<option value="ME">Mechatronik und Elektrotechnik</option>';
		$erg .= '<option value="SP">Soziale Arbeit, Gesundheit und Pflege</option>';
		$erg .= '<option value="VU">Versorgungstechnik</option>';
		$erg .= '<option value="WI">Wirtschaftsingenieurwesen</option>';
		$erg .= '</select>';
		$erg .= '<p>Startdatum :<br><input id="startdate" name="startdate" type="text" size="30" maxlength="30"></p>';
		$erg .= '<p>Enddatum :<br><input id="enddate" name="enddate" type="text" size="30" maxlength="30"></p>';
		$erg .= '<input type="submit" name="absenden" value="Absenden"/><br/>';
		$erg .= '</form>';				
		$erg .= '</div>
						<script>
						$(function() {
							$( "#startdate" ).datepicker({ dateFormat: "yymmdd" });
							$( "#enddate" ).datepicker({ dateFormat: "yymmdd" });
							});
						</script>';
		return $erg;
	}

	protected function eintraegeExportierenCsv($eintraege,$exportListe,$fakultaet='') {
		$fak = strtolower($fakultaet);
		$jahr = date('Y');
		$monat = date('m');
		$tag = date('d');
		$dateiname = 'module_six' . '_' . $jahr. '_' . $monat. '_' . $tag . '.csv';
		$dateiname = 'praxissemesterdaten_export_' . $fak . '.csv';
		header("Content-type: text/csv");
		header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Content-Disposition: inline; filename="'.$dateiname.'"');
		header('Pragma: no-cache');
		$nl = chr(13) . chr(10);
		foreach($exportListe as $feldTitel) {
			$titelZeile[] = '"' . iconv('UTF-8','CP1252',str_replace('"',"'",$feldTitel)) . '"';
		}
		$out .=  implode(';', $titelZeile) . $nl;
		foreach($eintraege as $zeile) {
			$ergebnisZeile = array();
			foreach($zeile as $spalte) {
				$ergebnisZeile[] = '"' . iconv('UTF-8','CP1252',str_replace('"',"'",$spalte)) . '"';
			}
			$out .=  implode(';', $ergebnisZeile) . $nl;
		}
		print $out;
		exit();
	}

	
}
?>
