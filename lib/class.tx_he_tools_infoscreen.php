<?php


class tx_he_tools_infoscreen {
protected $cObj;	
protected $delay;	
protected $forceReload;	
protected $categoryInfoscreen = 38;	
protected $maxEintraege = 3;
protected $alleTermine = FALSE;
protected $aktuelleZeit;
protected $get;
protected $pageId;
protected $reloadUrl;
protected $currentUrl;

protected $standorte = array(
	'efl'=>'Esslingen Flandernstraße',
	'esm'=>'Esslingen Stadtmitte',
	'gp'=>'Göppingen',
);

	function __construct(&$cObj,$delay=1000,$forceReload=3600) {		
		$this->cObj = &$cObj;
		if ($delay<1000) {
			$delay = 1000;
		}
		$this->delay = $delay;
		if ($forceReload<60) {
			$forceReload = 60;
		}
		$this->forceReload = $forceReload*1000;
    $this->delay = 60000;
    $this->forceReload = 3600000;

		$this->pageId = $GLOBALS['TSFE']->id;
		$this->reloadUrl = '"http://www.hs-esslingen.de/index.php?eID=he_tools&action=get_infoscreen_page_tstamp&uid=' . $this->pageId . '"';
		$this->currentUrl = 'http://www.hs-esslingen.de/index.php?id=' . $this->pageId;
		
		$this->get = t3lib_div::_GET();
		if ($this->get['datSim']==1 
				&& !empty($this->get['j'])
				&& !empty($this->get['m'])
				&& !empty($this->get['t'])
				) {
			$jahr = $this->get['j'];
			$monat = $this->get['m'];
			$tag = $this->get['t'];
			$stunde = $this->get['s'];
			if (empty($stunde)) {
				$stunde = '00';
			}
			$minute = $this->get['mi'];
			if (empty($minute)) {
				$minute = '00';
			}
			$sekunde = $this->get['sk'];
			if (empty($sekunde)) {
				$sekunde = '00';
			}
			$timeString = $jahr . '-' . $monat . '-' . $tag . ' ' . $stunde . ':' . $minute . ':' . $sekunde;
			$this->aktuelleZeit = strtotime($timeString);
		} else {
			$this->aktuelleZeit = time();
		}
		
// $this->forceReload = 2000;
	}
	
	public function initGeneral() {
$jsCode = '
	  
<script type="text/javascript">
//<![CDATA[
	
	var intervalReload = window.setInterval(reload, ' . $this->delay . '); 
	var intervalForceReload = window.setInterval(forceReload, ' . $this->forceReload . '); 
	var intervalRefreshTime = window.setInterval(refreshTime, 10000); 
						
	var tstamp; 
	var timeOffset; 
	var jetzt = new Date();
	var jetztZeit = jetzt.getTime();
	timeOffset = Math.ceil(' . $this->aktuelleZeit . ' - (jetzt.getTime()/1000));
	$.ajax({
	  url: ' . $this->reloadUrl . '
	}).done(function ( tstampAktuell ) {
		tstamp = tstampAktuell;
	});
	';
	
	if (!empty($this->get['datSim'])) {
		$jsCode .= 'function forceReload() {
			$.ajax({
			  url: ' . $this->reloadUrl . '
			}).done(function ( tstampAktuell ) {
				if(Number(tstampAktuell)) {
					var jetzt = new Date();
					var zeitAKtuell =  Math.ceil(jetzt.getTime()/1000);
					var datum = new Date(Math.ceil(zeitAKtuell+timeOffset)*1000);
					var jahr = datum.getFullYear();
					var monat = datum.getMonth()+1;
					var tag = datum.getDate();
					var stunde = datum.getHours();
					var minute = datum.getMinutes();
					if(monat<10) monat= "0" + monat;
					if(tag<10) tag= "0" + tag;
					if(stunde<10) stunde= "0" + stunde;
					if(minute<10) minute= "0" + minute;
					var args = "&datSim=1" +
			  						 "&j=" + jahr + 
			  						 "&m=" + monat + 
			  						 "&t=" + tag + 
			  						 "&s=" + stunde + 
			  						 "&mi=" + minute;
			  		
		  		window.location.href="' . $this->currentUrl . '" +  args;
		  	}
			});
		}
		';
	} else {
		$jsCode .= 'function forceReload() {
			$.ajax({
			  url: ' . $this->reloadUrl . '
			}).done(function ( tstampAktuell ) {
				if(Number(tstampAktuell)) {
		  		window.location.reload();
		  	}
			});
		}
		';
	}
	
	$jsCode .= 'function reload() {
		$.ajax({
		  url: ' . $this->reloadUrl . '
		}).done(function ( tstampAktuell ) {
		  if (tstamp!=tstampAktuell) {
				if(Number(tstampAktuell)) {
		  		window.location.reload();
		  	}
			}
		});
  }
	
	function refreshTime() {
		var jetzt = new Date();
		var zeitAKtuell =  Math.ceil(jetzt.getTime()/1000);
		var datum = new Date(Math.ceil(zeitAKtuell+timeOffset)*1000);
		var stunde = datum.getHours();
		var minute = datum.getMinutes();
		if(stunde<10) stunde= "0" + stunde;
		if(minute<10) minute= "0" + minute;
		var zeitString = stunde + ":" + minute + " Uhr";
		$("#time").text(zeitString);
	}
//]]>
</script>
				 ';	
	return $jsCode;
	}
	
	public function initCal($sponsoren='') {
    $hash = md5(time());
		$jsCode = '
		<script src="http://www.hs-esslingen.de/typo3conf/ext/he_tools/res/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script src="http://www.hs-esslingen.de/typo3conf/ext/he_tools/res/js/jquery.easy-ticker.js" type="text/javascript"></script>
  	<script src="http://www.hs-esslingen.de/typo3conf/ext/he_tools/res/js/jquery.marquee.min.js"></script>
	  <link rel="stylesheet" type="text/css" href="http://www.hs-esslingen.de/typo3conf/ext/he_tools/res/css/infoscreen_cal.css?' . $hash . '" media="all" />
	  ';
		if (empty($sponsoren)) {
			$jsCode .= '
				<style type="text/css">
				#gebaeude {
					padding: 1% 0;
				}
				</style>
				';
		}
		$jsCode .= $this->initGeneral();
		$GLOBALS['TSFE']->additionalHeaderData ['he_tools'] = $jsCode;				 		
	}
	
	public function initVideo() {
		$jsCode = '
		<script src="http://www.hs-esslingen.de/typo3conf/ext/he_tools/res/js/jquery-1.7.2.min.js" type="text/javascript"></script>
  	<script src="http://www.hs-esslingen.de/typo3conf/ext/he_tools/res/js/video.min.js"></script>
	  <script type="text/javascript">_V_.options.flash.swf = "http://www.hs-esslingen.de/typo3conf/ext/he_tools/res/video-js.swf";</script>
		';
		$jsCode .= $this->initGeneral();
		$GLOBALS['TSFE']->additionalHeaderData ['he_tools'] = $jsCode;				 		
	}
	
	public function gibSponsoren($pidSponsoren) {
		$sponsoren = array();
		$sqlQuery = 'SELECT DISTINCT title FROM tx_hetools_infoscreen_elemente
								 WHERE raum<>"" AND deleted=0 and hidden=0 and pid=' . $pidSponsoren . '
								 ORDER BY title';
		$result = $GLOBALS['TYPO3_DB']->sql_query($sqlQuery);
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
			$sponsoren[] = $row['title'];
		}		
		return $sponsoren;
	}
	
	function get_real_ip() {
		// This one *should* be trustworthy if found
		if ( getenv( 'HTTP_CLIENT_IP' ) )
			return $_SERVER['HTTP_CLIENT_IP'];
	
		// Otherwise we'll check if there's a proxy present
		if ( getenv( 'HTTP_X_FORWARDED_FOR' ) ) {
			$ipString = @getenv('HTTP_X_FORWARDED_FOR');
			$addr = explode(",",$ipString);
			return trim ( $addr[sizeof($addr)-1] );
		}
		// Nothing else found
		#return $_SERVER['REMOTE_ADDR'];
		return "unknown";
	}
	
	public function redirectMain() {
		$ip = $_SERVER[REMOTE_ADDR];
		if ($ip == "134.108.33.101" || $ip == "134.108.33.102") {
			# wir kommen via load balancer
			#$ip = $_SERVER[HTTP_X_FORWARDED_FOR];
			$ip = $this->get_real_ip();
		}
		
		$redirect = '';
		$sqlQuery = 'SELECT redirect_url FROM tx_hetools_infoscreen_redirects' .
								' WHERE ip="' . $ip . '" AND deleted=0 and hidden=0';
		$result = $GLOBALS['TYPO3_DB']->sql_query($sqlQuery);
		if  ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
			$redirect = $row['redirect_url'];
		}
/*
t3lib_div::devlog("IP: " . $ip,"infoscreens",0);
t3lib_div::devlog("redirect: " . $redirect,"infoscreens",0);
*/
		if (!empty($redirect)) {
			if (is_numeric($redirect)) {
				$pageUrl = 'http://www.hs-esslingen.de/index.php?id=' . $redirect;
			} else {
				$pageUrl = $redirect;
			}
			t3lib_utility_Http::redirect($pageUrl);
		}

		
		switch ($ip) {
			case '134.108.64.35':
				$id = 95110;
				break;
				
			case '134.108.190.113': /* RSLX1901 */
			case '134.108.190.114': /* RSLX1902 */	
			case '134.108.190.115': /* RSLX1903 */	
/* Infoscreen Stadtmitte 1 */
				$id = 94598;
				break;
			case '134.108.190.99': /* RHLX1903 */
			case '134.108.190.111':
/* Infoscreen Flandernstrasse 1*/
				$id = 94684;
				break;
				
			default:
				$id = 94598;
				break;
		}
		$pageUrl = 'http://www.hs-esslingen.de/index.php?id=' .$id;
		t3lib_utility_Http::redirect($pageUrl);
	}

	public function zeigeKalendertermine($category,$standardmeldung,$dauerAnzeige,$dauerUebergang,$pidSponsoren,$gebaeudeText,$standort) {
		$this->initCal($pidSponsoren);
		date_default_timezone_set(DateTimeZone::EUROPE);

		$this->alleTermine = TRUE;
		$datumHeute = date('Ymd',$this->aktuelleZeit);
		$where = 'tx_cal_event.deleted=0 and tx_cal_event.hidden=0 AND tx_cal_event.uid IN
							(SELECT uid_local FROM tx_cal_event_category_mm  
							 WHERE uid_foreign=' . $category . ')';
		$where .= ' AND (tx_cal_event.start_date="' . $datumHeute . '"  OR ' . 
							' (tx_cal_event.start_date<="' . $datumHeute . '" AND tx_cal_event.end_date>="' . $datumHeute . '"))';
		$sqlQuery = 'SELECT tx_cal_event.title,tx_cal_event.tx_femanagement_cal_title_infoscreen,tx_cal_event.start_date,
												tx_cal_event.start_time,tx_cal_event.end_time,
												tx_cal_event.allday,tx_cal_location.name,
												tx_cal_location.tx_femanagement_cal_campus,
												tx_cal_location.tx_femanagement_cal_building,
												tx_cal_location.tx_femanagement_cal_room
												FROM tx_cal_event
								 LEFT JOIN tx_cal_location ON tx_cal_location.uid=tx_cal_event.location_id
								 WHERE ' . $where . '
								 ORDER BY tx_cal_event.allday DESC,start_date,tx_cal_event.start_time';
		$result = $GLOBALS['TYPO3_DB']->sql_query($sqlQuery);
		$forceReload = 999999;
		$anzTermine = $GLOBALS['TYPO3_DB']->sql_num_rows($result);
		$localtime = localtime($this->aktuelleZeit);
		$uhrzeitAktuell = $localtime[2]*3600 + $localtime[1]*60 + $localtime[0];
		$i = 0;
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
			if (!empty($row['tx_femanagement_cal_title_infoscreen'])) {
				$title = $row['tx_femanagement_cal_title_infoscreen'];
			} else {
				$title = $row['title'];
			}
			if ($row['allday'])	{
				$uhrzeit = 'ganztägige Veranstaltung';
				$uhrzeit = '';
				$bis = sprintf("%02d:%02d",00,01);
			} else {
				$reloadTime = $row['end_time']-$uhrzeitAktuell;
				if ($reloadTime<$forceReload && $reloadTime>0) {
					$forceReload = $reloadTime;
				}
				$start = $row['start_time'];
				$stunden = $start / 3600;
				$minuten = ($start % 3600) / 60;
				$von = sprintf("%02d:%02d",$stunden,$minuten);
				$ende = $row['end_time'];
				$stunden = $ende / 3600;
				$minuten = ($ende % 3600) / 60;
				$bis = sprintf("%02d:%02d",$stunden,$minuten);
				$uhrzeit = $von . ' - ' . $bis . ' Uhr';
			}
			if ($this->alleTermine || $anzTermine<=$this->maxEintraege) {
				$datenAktuell[$bis . $i] = array('title'=>$title,
												 'uhrzeit'=>$uhrzeit,
												 'standort'=>$row['tx_femanagement_cal_campus'],
												 'gebaeude'=>$row['tx_femanagement_cal_building'],
												 'raum'=>$row['tx_femanagement_cal_room'],
											);
			} else {
				if($row['allday']) {
					$datenGanztaegig[$i] = array('title'=>$title,
							'uhrzeit'=>$uhrzeit,
							'standort'=>$row['tx_femanagement_cal_campus'],
							'gebaeude'=>$row['tx_femanagement_cal_building'],
							'raum'=>$row['tx_femanagement_cal_room'],
					);
				} else if($row['end_time']>$uhrzeitAktuell) {
					$datenAktuell[$bis . $i] = array('title'=>$title,
							'uhrzeit'=>$uhrzeit,
							'standort'=>$row['tx_femanagement_cal_campus'],
							'gebaeude'=>$row['tx_femanagement_cal_building'],
							'raum'=>$row['tx_femanagement_cal_room'],
					);
				} else {
					$datenVergangen[$bis . $i] = array('title'=>$title,
							'uhrzeit'=>$uhrzeit,
							'standort'=>$row['tx_femanagement_cal_campus'],
							'gebaeude'=>$row['tx_femanagement_cal_building'],
							'raum'=>$row['tx_femanagement_cal_room'],
					);
				}
			}
			$i++;
		}
		$daten = array();
		$anzGanztaegig = count($datenGanztaegig);
		$anzAktuell = count($datenAktuell);
		$anzVergangen = count($datenVergangen);
		if (($anzGanztaegig+$anzAktuell)<$this->maxEintraege) {
			if ($anzVergangen>0) {
				ksort($datenVergangen);
				$datenAuffuellen = array_reverse($datenVergangen);
			} else {
				$datenAuffuellen = array();
			}
			$anzUeberspringen = $anzVergangen-($this->maxEintraege-$anzAktuell-$anzGanztaegig);
			if ($anzUeberspringen>0) {
				for ($i=0;$i<$anzUeberspringen;$i++) {
					array_pop($datenAuffuellen);
				}
			}
			if (!empty($datenGanztaegig)) {
				foreach ($datenGanztaegig as $eintrag) {
					$daten[] = $eintrag;
				}
			}
			$datenVergangen = array_reverse($datenAuffuellen);
			if (!empty($datenVergangen)) {
				foreach ($datenVergangen as $eintrag) {
					$daten[] = $eintrag;
				}
			}
			if (!empty($datenAktuell)) {
				foreach ($datenAktuell as $eintrag) {
					$daten[] = $eintrag;
				}
			}
		} else {
			foreach ($datenAktuell as $eintrag) {
				$daten[] = $eintrag;
			}
		}
//return;
		$out = '';
		$anzahl = count($daten);
	
		if ($anzahl>1) {
			$wrapId = 'kalenderTerminWrapMulti';
    } else if ($anzahl==1) {
      $wrapId = 'kalenderTerminWrapSingle';
    } else {
			$wrapId = 'seiteninhalt';
		}
		setlocale(LC_TIME, "de_DE");
		$datumHeute =  strftime("%e. %B %Y",$this->aktuelleZeit);
		$zeitAktuell =  strftime("%H:%M",$this->aktuelleZeit) . ' Uhr';
		$out .= '<div id="head">
						 <div id="logo">
						 <img src="http://www.hs-esslingen.de/fileadmin/images/banner/Portal_banner/Logo_weiss.png" >
						 </div>';
		$out .= '<div id="date">' . $datumHeute . '</div>';
		$out .= '<div id="time">' . $zeitAktuell . '</div>';
		$out .= '</div>';
    $wrapDiv = '<div id="' . $wrapId . '">';
		if ($anzahl>1) {
      $out .= $wrapDiv;
			$out .= $this->gibKalenderterminListeAus($daten,$anzahl,$dauerAnzeige,$dauerUebergang,$standort);
      $out .= '</div>';
		} else if ($anzahl==1) {
      $out .= $wrapDiv;
      $out .= $this->gibKalenderterminAus($daten[0],$standort);
      $out .= '</div>';
		} else {
			$out .= $this->gibStandardmeldungAus($standardmeldung,$wrapDiv);
		}

		$sponsorenScript = '';
		if (!empty($pidSponsoren)) {
			$out .= '<div id="lauftext">';
			$sponsorenListe = $this->gibSponsoren($pidSponsoren);
			$out .= '<div class="text">
									<span class="title">Die Raumsponsoren der Hochschule Esslingen:</span> ' . implode(' - ', $sponsorenListe) .
							'</div>
							</div>
						';
			$sponsorenScript = '
			var htmlOriginal = "";
			$.fn.textWidth = function() {
			  var org = $(this)
				var html = $(\'<div style="position:absolute;width:auto;left:-9999px">\' + (org.html()) + "</div>");
				var fontFamily = org.css("font-family");
		    var fontWeight = org.css("font-weight");
		    var fontSize = org.css("font-size");
		    html.css("font-family", fontFamily);
		    html.css("font-weight", fontWeight);
		    html.css("font-size", fontSize);
		  	$("body").append(html);
			  var width1 = html.width();
			  var width = html.outerWidth();
			  html.remove();
			  return width;
			}
			
			function marquee(lauftext) {
				if (htmlOriginal=="") {
					htmlOriginal = lauftext.html();
				}
				var abstandProzent = 30;									
				var abstandPixel;									
				var fensterBreite = $(window).width();
				abstandPixel = Math.ceil(fensterBreite*(abstandProzent/100));
				var textBreite = lauftext.textWidth() + abstandPixel + 10;
				var anzKopie = Math.ceil(fensterBreite/textBreite);
				var anzeigeText = htmlOriginal;
				for (var i=0;i<anzKopie;i++) {
					anzeigeText = anzeigeText + htmlOriginal;
				}
				lauftext.html(anzeigeText);
				lauftext.find(".text").css("margin-left", abstandPixel + "px");
				var textBreiteGesamt = (anzKopie+1)*textBreite;
				lauftext.css({
					"width": textBreiteGesamt,
					"left": 0
				});
				var dauer = 30;
				
				function scroll() {
					var bLeft = lauftext.position().left;
					if (bLeft <= -textBreite) {
						lauftext.css("left", 0);
						scroll();
					} else {
						var time = parseInt(dauer * (parseInt(bLeft, 10) + textBreite) * (10000 / textBreite) / 10);
						lauftext.animate({
							"left": -textBreite
						}, time, "linear", function() {
							scroll();
						});
					}
				}
				scroll();
			}
			
			function initLauftext() {
			var laufTextHoehe;
				marquee($("#lauftext"));
				laufTextHoehe = Math.ceil($("#lauftext").outerHeight(true)*1.1);
				$("#gebaeude").css("bottom",laufTextHoehe + "px");
			}
			
			$(document).ready(function(){
				initLauftext();
			});
			
			$(window).resize(function() {
		  	initLauftext();
			});
		';
		}

    if (!empty($pidSponsoren)) {
      $out .= $this->gibFusszeileNormal($wrapId,$gebaeudeText,$sponsorenScript,$forceReload);
    } else {
      $out .= $this->gibFusszeileAus($wrapId, $gebaeudeText, 'cal');
    }

		return $out;
  }

  public function gibFusszeileNormal($wrapId,$gebaeudeText,$sponsorenScript,$forceReload) {
    $out = $this->gibGebaeudeAus($gebaeudeText);
    $out .= '
		<script type="text/javascript">';
    if ($forceReload>0 && $forceReload<85000) {
      $out .= '
			var intervalForceReload = window.setInterval(forceReload, ' . $forceReload*1000 . ');';
    }
    $out .= '
		function initElementSizes() {
			var fensterHoehe = $(window).height();
			var fensterBreite = $(window).width();
			var fontGroesse;
			var elemTop;

			if (fensterHoehe>(fensterBreite*9/16)) {
				fontGroesse = (fensterBreite*9/16/110);
			}	 else {
				fontGroesse = fensterHoehe/110;
			}
			$("html").css("font-size",fontGroesse + "px");
			$("#logo img").css("height",Math.ceil(fensterHoehe/10) + "px");

			$("#head").css("height",Math.ceil($("#logo img").height()+fensterHoehe/100) + "px");

			$("#' . $wrapId . ' .kalenderTermin").css("width",Math.ceil(fensterBreite*0.9) + "px");


			var headerHoehe = $("#logo").offset().top+$("#head").outerHeight(true);
			var footerHoehe = $("#gebaeude").outerHeight(true) + $("#lauftext").outerHeight(true);
			var elemMaxHoehe = fensterHoehe-headerHoehe-footerHoehe;
			var elemHoehe = $("#' . $wrapId . '").outerHeight(true);
			var seitenInhaltHoehe = $("#seiteninhalt").outerHeight(true);
			var count = 0;
			while (elemHoehe>elemMaxHoehe && count<10) {
			  var fontGroesseAktuell = parseFloat($("#' . $wrapId . '").css("font-size"));
			  fontGroesse = Math.ceil(fontGroesseAktuell * elemMaxHoehe / elemHoehe * 0.9);
			  $("#' . $wrapId . '").css("font-size", fontGroesse + "px");
				headerHoehe = $("#logo").offset().top+$("#head").outerHeight(true);
				footerHoehe = $("#gebaeude").outerHeight(true) + $("#lauftext").outerHeight(true);
				elemMaxHoehe = fensterHoehe-headerHoehe-footerHoehe;
				elemHoehe = $("#' . $wrapId . '").outerHeight(true);
				count++;
			}

      if (elemHoehe>elemMaxHoehe) {
        elemTop = Math.ceil(headerHoehe)*1.3;
      } else {
        elemTop = Math.ceil(headerHoehe+(elemMaxHoehe*1.1-elemHoehe)/2);
      }

//			$("#' . $wrapId . ' #sponsoren").css("bottom",footerHoehe + "px");


			$("#' . $wrapId . '").css("top",elemTop + "px");
			
			$("#' . $wrapId . '").css("height",elemMaxHoehe + "px");
			if ($.browser.msie) {
			  if ($.browser.version<9) {
					$("#' . $wrapId . '").css("margin","0");
			  	$("#' . $wrapId . '").css("padding","0");
					elemHoehe = $("#' . $wrapId . '").outerHeight(true);
			  	$("#' . $wrapId . '").css("width",Math.ceil(fensterBreite*0.9) + "px");
			  	$("#' . $wrapId . '").css("height",elemHoehe + "px");
			  	$("#' . $wrapId . '").css("left",Math.ceil(fensterBreite*0.05) + "px");
			 		$("#' . $wrapId . '").css("top",Math.ceil(fensterHoehe-elemHoehe) + "px");
				}
			}
		}
		' . $sponsorenScript . '
		$(window).resize(function() {
		  initElementSizes();
		});
		initElementSizes();
		</script>
		';
    return $out;
  }
	
	public function zeigeSeitenInhalt($contentId,$dauerAnzeige,$dauerUebergang,$anzeigeObjekt,$gebaeudeText) {
		$this->initCal($anzeigeObjekt);
		$out = $this->gibKopfzeileAus();
		$out .= $this->gibSeiteninhaltAus($contentId);
		if (!empty($anzeigeObjekt)) {
			$out .= $this->gibSponsorenLauftext($anzeigeObjekt);
		}
		$out .= $this->gibFusszeileAus('seiteninhalt', $gebaeudeText, 'content_static');
		return $out;
	}

	public function zeigeSeitenInhaltListe($ttContentPid,$dauerAnzeige,$dauerUebergang,$anzeigeObjekt,$gebaeudeText,$standardText) {
		$contentIdListe = tx_he_tools_util::getContentElements($ttContentPid);
		$this->initCal($anzeigeObjekt);
		$out = $this->gibKopfzeileAus();
		$out .= $this->gibSeitenInhaltListeAus($contentIdListe,$dauerAnzeige,$dauerUebergang,$standardText);
		if (!empty($anzeigeObjekt)) {
			$out .= $this->gibSponsorenLauftext($anzeigeObjekt);
		}
    if (count($contentIdListe)>1) {
      $mode = 'content_scrolling';
    } else {
      $mode = 'single';
    }
		$out .= $this->gibFusszeileAus('seiteninhalt', $gebaeudeText, $mode);
		return $out;
	}

	public function zeigeFlexiblenInhalt($standardmeldung,$elemente,$dauerAnzeige,$dauerUebergang,$anzeigeObjekt,$gebaeudeText,$standort) {
		$localtime = localtime($this->aktuelleZeit);
		$uhrzeitAktuell = $localtime[2]*3600 + $localtime[1]*60 + $localtime[0];
		$where = 'deleted=0 and hidden=0 AND uid IN (' . $elemente . 
						 ') AND von<=' . $this->aktuelleZeit . ' AND bis>=' . $this->aktuelleZeit;
		$sqlQuery = 'SELECT *	FROM tx_hetools_infoscreen_anzeige_zeitraeume	WHERE ' . $where . 
								' ORDER BY von';
		$result = $GLOBALS['TYPO3_DB']->sql_query($sqlQuery);
		$anzElemente = $GLOBALS['TYPO3_DB']->sql_num_rows($result);
		if ($anzElemente==0) {
			$where = 'deleted=0 and hidden=0 AND uid IN (' . $elemente . ') AND von=0 AND bis=0';
			$sqlQuery = 'SELECT *	FROM tx_hetools_infoscreen_anzeige_zeitraeume	WHERE ' . $where .
			' ORDER BY von';
			$result = $GLOBALS['TYPO3_DB']->sql_query($sqlQuery);
			$anzElemente = $GLOBALS['TYPO3_DB']->sql_num_rows($result);
		}
		$erg = '';
		if ($anzElemente>0) {
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result);
			if (count($row)>0) {
				switch($row['anzeigetyp']) {
					case 'SEITENINHALT':
						if (!empty($row['inhaltsElement'])) {
							if (!empty($row['anzeigeObjekt'])) {
								$anzeigeObjekt = $row['anzeigeObjekt'];
							}
							$erg = $this->zeigeSeitenInhalt($row['inhaltsElement'],$dauerAnzeige,$dauerUebergang,$anzeigeObjekt,$gebaeudeText);							
						}
						break;
					case 'KALENDERTERMINE':
						if (!empty($row['kalenderKategorie'])) {
							if (!empty($row['anzeigeObjekt'])) {
								$anzeigeObjekt = $row['anzeigeObjekt'];
							}
              if (!empty($row['inhaltsElement'])) {
                $standardmeldung = $row['inhaltsElement'];
              }
							$erg = $this->zeigeKalendertermine($row['kalenderKategorie'],$standardmeldung,$dauerAnzeige,$dauerUebergang,$anzeigeObjekt,$gebaeudeText,$standort);
						}
						break;
				}
			}
		}
		if (empty($erg)) {
			$erg = $this->zeigeKalendertermine(0,$standardmeldung,$dauerAnzeige,$dauerUebergang,$anzeigeObjekt,$gebaeudeText,$standort);
		}
		return $erg;
	}
	
	public function gibSponsorenLauftext($pidSponsoren) {
			$out = '<div id="lauftext">';
			$sponsorenListe = $this->gibSponsoren($pidSponsoren);
			$out .= '<div class="text">
									<span class="title">Die Raumsponsoren der Hochschule Esslingen:</span> ' . implode(' - ', $sponsorenListe) .
							'</div>
							</div>
			
			<script type="text/javascript">
			$.fn.textWidth = function() {
			  var org = $(this)
				var html = $(\'<div style="position:absolute;width:auto;left:-9999px">\' + (org.html()) + "</div>");
		    html.css("font-family", org.css("font-family"));
		    html.css("font-weight", org.css("font-weight"));
		    html.css("font-size", org.css("font-size"));
		  	$("body").append(html);
			  var width1 = html.width();
			  var width = html.outerWidth();
			  html.remove();
			  return width;
			}
			
			function marquee(lauftext) {
				var htmlOriginal = lauftext.html();
				var abstandProzent = 30;									
				var abstandPixel;									
				//lauftext.html(htmlOriginal + "&nbsp;-&nbsp;");
				var fensterBreite = $(window).width();
				abstandPixel = Math.ceil(fensterBreite*(abstandProzent/100));
				var textBreite = lauftext.textWidth() + abstandPixel;
				var anzKopie = Math.ceil(fensterBreite/textBreite);
				var anzeigeText = htmlOriginal;
				for (var i=0;i<anzKopie;i++) {
					anzeigeText = anzeigeText + htmlOriginal;
				}
				lauftext.html(anzeigeText);
				lauftext.find(".text").css("margin-left", abstandPixel + "px");
				var textBreiteGesamt = (anzKopie+1)*textBreite;
				lauftext.css({
					"width": Math.ceil(textBreiteGesamt*1.1),
					"left": 0
				});
				var dauer = 30;
				
				function scroll() {
					var bLeft = lauftext.position().left;
					if (bLeft <= -textBreite) {
						lauftext.css("left", 0);
						scroll();
					} else {
						time = parseInt(dauer * (parseInt(bLeft, 10) + textBreite) * (10000 / textBreite) / 10);
						lauftext.animate({
							"left": -textBreite
						}, time, "linear", function() {
							scroll();
						});
					}
				}
			
				scroll();
			}
		
		$(document).ready(function(){
			var fensterBreite = $(window).width();
			var gebaeudeText = $("#gebaeude span").html();
			$("#gebaeude").css("bottom",Math.ceil($("#lauftext").height()) + "px");
			marquee($("#lauftext"));
		});
		</script>';
		return $out;
	}
	
	public function gibSeiteninhaltAus($contentId) {
		$out = '<div id="seiteninhalt">'; 
		$config = array('tables' => 'tt_content','source' => $contentId);
		$out .= $this->cObj->RECORDS($config);
		$out .= '</div>';
		return $out;
	}
	
	public function gibKopfzeileAus() {
		date_default_timezone_set(DateTimeZone::EUROPE);
		setlocale(LC_TIME, "de_DE");
		$datumHeute =  strftime("%e. %B %Y",$this->aktuelleZeit);
		$zeitAktuell =  strftime("%H:%M",$this->aktuelleZeit) . ' Uhr';
		$out = '<div id="head">
						 <div id="logo">
						 <img src="http://www.hs-esslingen.de/fileadmin/images/banner/Portal_banner/Logo_weiss.png" >
						 </div>';
		$out .= '<div id="date">' . $datumHeute . '</div>';
		$out .= '<div id="time">' . $zeitAktuell . '</div>';
		$out .= '</div>';
		return $out;
	}
	
	public function gibFusszeileAus($wrapId, $gebaeudeText, $mode, $elemWrap='eintrag', $forceReload=0) {
		$out = $this->gibGebaeudeAus($gebaeudeText);
		$out .= '
		<script type="text/javascript">
		';
		if ($forceReload>0 && $forceReload<85000) {
			$out .= '
			var intervalForceReload = window.setInterval(forceReload, ' . $forceReload*1000 . ');';
		}
		$out .= '
		function initElementSizes() {
			var fensterHoehe = $(window).height();
			var fensterBreite = $(window).width();
			var fontGroesse;
				if (fensterHoehe>(fensterBreite*9/16)) {
					fontGroesse =  Math.ceil(fensterBreite*9/16/110);
				}	 else {
					fontGroesse = Math.ceil(fensterHoehe/110);
				}
			$("html").css("font-size",fontGroesse + "px");
			$("#logo img").css("height",Math.ceil(fensterHoehe/10) + "px");
			$("#head").css("height",Math.ceil($("#logo img").height()+fensterHoehe/100) + "px");
					
			$("#' . $wrapId . ' .kalenderTermin").css("width",Math.ceil(fensterBreite*0.9) + "px");
			var headerHoehe = $("#logo").offset().top+$("#head").outerHeight(true);
			var headerBreite = $("#head").outerWidth(true);
			var footerHoehe = $("#gebaeude").outerHeight(true) + $("#lauftext").outerHeight(true);
			var elemMaxHoehe = Math.ceil((fensterHoehe-headerHoehe-footerHoehe) * 0.9);
			var elemHoehe = $("#' . $wrapId . '").outerHeight(true);
			var elemBreite = $("#' . $wrapId . '").outerWidth(true);
			var elemMaxBreite = fensterBreite;
			var seitenInhaltHoehe = $("#seiteninhalt").outerHeight(true);
			';
		
		switch ($mode) {	
			case 'content_scrolling':
				$out .= '
					var paddingContent = Math.ceil(fensterHoehe / 20);
					elemMaxBreite = $("#' . $wrapId . '").innerWidth(true) - paddingContent;
					elemMaxHoehe = Math.ceil((fensterHoehe-headerHoehe-footerHoehe) * 0.9 - paddingContent);
					$("#' . $wrapId . ' .' . $elemWrap . '").each(function() {

						var contentId = $(this).attr("data-contentid");
						fontGroesse = 48;
						var hidden = $(this).hasClass("hidden");
						if (hidden) {
							$(this).removeClass("hidden");
						}
						$("#" + contentId).css("display","table");
						$("#" + contentId).css("margin","auto");
						elemHoehe = $("#" + contentId).outerHeight(true)+paddingContent;
						elemBreite = $("#" + contentId).outerWidth(true)+paddingContent;
						while (fontGroesse>2 && (elemHoehe>elemMaxHoehe || elemBreite>elemMaxBreite)) {
							fontGroesse--;
							$(this).css("font-size", fontGroesse + "px");
							elemHoehe = $("#" + contentId).outerHeight(true)+paddingContent;
							elemBreite = $("#" + contentId).outerWidth(true)+paddingContent;
						}
						$(this).css("font-size", fontGroesse + "px");
						if (hidden) {
							$(this).addClass("hidden");
						}
						var elemTop = Math.ceil((elemMaxHoehe-elemHoehe)/2);
						$(this).css("padding-top",elemTop + "px");
					});

					';
				break;
			default:
				$out .= '
					var count = 0;
					if (elemHoehe>elemMaxHoehe) {
						while (elemHoehe>elemMaxHoehe && count<10) {
						  var fontGroesseAktuell = parseFloat($("#' . $wrapId . '").css("font-size"));
						  fontGroesse = Math.ceil(fontGroesseAktuell * elemMaxHoehe / elemHoehe * 0.9);
						  $("#' . $wrapId . '").css("font-size", fontGroesse + "px");
							headerHoehe = $("#logo").offset().top+$("#head").outerHeight(true);
							footerHoehe = $("#gebaeude").outerHeight(true) + $("#lauftext").outerHeight(true);
							elemMaxHoehe = Math.ceil((fensterHoehe-headerHoehe-footerHoehe) * 0.9);
							elemHoehe = $("#' . $wrapId . '").outerHeight(true);
							count++;
						}
					}
			  	';
				break;
		}
		$out .= '			  
      var elemHoehe = $("#' . $wrapId . '").outerHeight(true);
			var elemBreite = $("#' . $wrapId . '").outerWidth(true);
			';
		switch ($mode) {
			case 'content_scrolling':
				$out .= '
					var elemTop = Math.ceil(headerHoehe + paddingContent);
					var elemHoehe = Math.ceil(elemMaxHoehe - (2*paddingContent));
					var elemLeft = Math.ceil((fensterBreite-headerBreite)/2);		
					var elemBreite = headerBreite;
					$("#' . $wrapId . '").css("top",elemTop + "px");
					$("#' . $wrapId . '").css("height",elemHoehe + "px");
					';
				break;
			default:
				$out .= '
					elemMaxHoehe = fensterHoehe-headerHoehe-footerHoehe;
					var elemTop = Math.ceil(headerHoehe+(elemMaxHoehe-elemHoehe)/2);
					var elemLeft = Math.ceil((fensterBreite-elemBreite)/2);		
					
					$("#' . $wrapId . '").css("top",elemTop + "px");
					$("#' . $wrapId . '").css("height",elemMaxHoehe + "px");
					';
				break;
		}
		$out .= '
			if ($.browser.msie) {
			  if ($.browser.version<9) {
					$("#' . $wrapId . '").css("margin","0");
			  	$("#' . $wrapId . '").css("padding","0");
					elemHoehe = $("#' . $wrapId . '").outerHeight(true);
			  	$("#' . $wrapId . '").css("width",Math.ceil(fensterBreite*0.9) + "px");
			  	$("#' . $wrapId . '").css("height",elemHoehe + "px");
			  	$("#' . $wrapId . '").css("left",Math.ceil(fensterBreite*0.05) + "px");
			 		$("#' . $wrapId . '").css("top",Math.ceil(fensterHoehe-elemHoehe) + "px");
			 		$("#' . $wrapId . '").css("left",Math.ceil(fensterBreite-elemBreite) + "px");
				}
			}			
		}
		$(window).resize(function() {
		  initElementSizes();
		});
		initElementSizes();
		</script>
		';		
		return $out;
	}
	
	public function gibGebaeudeAus($gebaeudeText) {
		return '<div id="gebaeude"><span>' . $gebaeudeText . '</span></div>';
	}
	
	public function gibStandardmeldungAus($standardmeldung, $wrapDiv) {
    if (is_numeric($standardmeldung)) {
      $out = $this->gibSeiteninhaltAus($standardmeldung);
    } else {
      $out = $wrapDiv .
            '<div class="kalenderTermin">
                <div class="infoTitle single">' . $standardmeldung . '</div>
               </div>
               </div>';
    }
		return $out;
	}
	
	public function gibKalenderterminListeAus($daten,$anzahl,$dauerAnzeige,$dauerUebergang,$standort) {
    $out = '';
    $slide = false;
    if ($anzahl>$this->maxEintraege) {
      $slide = true;
    }
		$eintraege = array();
		$anzahl = 0;
		foreach ($daten as $eintrag) {
			if ($anzahl<$this->maxEintraege) {
				$eintraege[] = $this->gibKalenderterminAus($eintrag,$standort,$slide);
			} else {
				$eintraege[] = $this->gibKalenderterminAus($eintrag,$standort,$slide,true);
			}
			$anzahl++;
		}

    if ($slide) {
      $out .= '<div id="slider"><div>';
      $out .= implode("\n",$eintraege);
      $out .= '</div></div>
      <script text="text/javascript">
      $(document).ready(function(){
        var fensterHoehe = $(window).height();
        var headerHoehe = $("#logo").offset().top+$("#head").outerHeight(true);
        var footerHoehe = $("#gebaeude").outerHeight(true);
        var elemMaxHoehe = (fensterHoehe-headerHoehe-footerHoehe) * 0.8;

        var dd = $("#slider").easyTicker({
          direction: "up",
          easing: "",
          speed: ' . $dauerUebergang . ',
          interval: ' . $dauerAnzeige . ',
          height: "auto",
          visible: 0,
          mousePause: 0
        }).data("easyTicker");
      });
      </script>
      ';
    } else {
      $out .= implode('<hr>',$eintraege);
    }
		return $out;
	}

	public function gibSeitenInhaltListeAus($contentIds,$dauerAnzeige,$dauerUebergang,$standardmeldung) {
    if (count($contentIds)==0) {
      $out = '<div id="seiteninhalt"><h2 class="align-center">' . $standardmeldung . '</h2></div>';
    } else {
      $out = '<div id="seiteninhalt">';
      $out .= '<div id="slider"><div>';
      $anzahl = 0;
      foreach ($contentIds as $contentId) {
        $config = array('tables' => 'tt_content','source' => $contentId);
        if ($anzahl==0) {
          $out .= '<div data-contentid="c' . $contentId . '" class="eintrag">';
        } else {
          $out .= '<div data-contentid="c' . $contentId . '" class="eintrag hidden">';
        }
        $anzahl++;
        $out .= $this->cObj->RECORDS($config);
        $out .= '</div>';
      }
      $out .= '</div></div></div>';
      if ($anzahl>1) {
        $out .= '
	      <script text="text/javascript">
	      $(document).ready(function(){
	        var dd = $("#slider").easyTicker({
	          direction: "up",
	          easing: "linear",
	          speed: ' . $dauerUebergang . ',
	          interval: ' . $dauerAnzeige . ',
	          height: "auto",
	          showElems: 1,
	          visible: 0,
	          mousePause: 0
	        }).data("easyTicker");
	      });
	      </script>
	      ';
      }
    }
		return $out;
	}

	public function gibKalenderterminAus($daten,$standort,$slide=false,$hidden=false) {
		if ($hidden) {
			$cssClass = 'class="kalenderTermin hidden"';
		} else {
			$cssClass = 'class="kalenderTermin"';
		}
		$out = '<div ' . $cssClass . '>';
		$out .= '<div class="infoTitle">' . $daten['title'] . '</div>';
		$out .= '<div class="uhrzeit">' . $daten['uhrzeit'] . '</div>';
		$out .= '<div class="ort">';
		$andererStandort = '';
		if ($daten['standort']!=$standort && isset($this->standorte[$daten['standort']])) {
			$andererStandort = $this->standorte[$daten['standort']];
		}
		if (empty($daten['gebaeude'])) {
			$out .= '<div class="gebaeude">' . $andererStandort . '</div>';
		} else {
			if (!empty($andererStandort)) {
				$daten['gebaeude'] = $andererStandort . ', ' . $daten['gebaeude'];
			}
			$out .= '<div class="gebaeude">' . $daten['gebaeude'] . '</div>';
		}
		if (empty($daten['raum'])) {
			$out .= '<div class="raum"></div>';
		} else {
			$stockwerkBez = '';
			$posStockwerk = strpos($daten['raum'],'.');
			if (strpos($daten['raum'],', ')!==FALSE || strpos($daten['raum'],' (')!==FALSE) {
				$zusaetzlicheRaumInfo = TRUE;
			} else {
				$zusaetzlicheRaumInfo = FALSE;
			}
			if (!$zusaetzlicheRaumInfo && $posStockwerk>0) {
				$stockwerk = substr($daten['raum'],$posStockwerk+1,1);
				if ($stockwerk=='-') {
					$stockwerk = substr($daten['raum'],$posStockwerk+2,1);
					$stockwerkBez = ', ' . $stockwerk . '. UG';
				} else if ($stockwerk==0) {
					$stockwerkBez = ', EG'; 
				} else if ($stockwerk>0) {
					$stockwerkBez = ', ' . $stockwerk . '. OG'; 
				} 
			}
			$out .= '<div class="raum">' . $daten['raum'] . $stockwerkBez . '</div>';
		}
		$out .= '</div>'; // Ort
    if ($slide) {
      $out .= '<hr/>';
    }
		$out .= '</div>';
		return $out;
	}
	
	public function zeigeVideo($videoPfad) {
		if (strpos($videoPfad,'http://')===FALSE) {
			$videoPfad = 'http://www.hs-esslingen.de/' . $videoPfad;
		}
		exec('D:\\tmp\\vlc.bat');
return;
		$this->initVideo();
		$endung = strtolower(substr($videoPfad,strrpos($videoPfad,'.')+1));
		switch ($endung) {
			case 'flv': $videoType = 'video/flv'; break;
			case 'mp4': $videoType = 'video/mp4'; break;
			case 'ogv': $videoType = 'video/ogg'; break;
			case 'webm': $videoType = 'video/webm'; break;
			default: $videoType = 'video/flv'; break;
		}
		if (strpos($videoPfad,'http://')===FALSE) {
			$videoPfad = 'http://www.hs-esslingen.de/' . $videoPfad;
		}
		
		return '
<video id="video_infoscreen" class="video-js" autoplay loop preload="none" data-setup="{}">
<source src="' .  $videoPfad. '" type="' . $videoType . '" />
</video> 
<script type="text/javascript">
$(document).ready(function () {
	var fensterBreite = $(window).width();
	var fensterHoehe = $(window).height();
	$("#video_infoscreen").attr("width",fensterBreite);
	$("#video_infoscreen").attr("height",fensterHoehe);
});

function initVideoSize() {
	var video = document.getElementById("video_infoscreen");
	video.addEventListener("loadedmetadata", function(e){
		var videoBreite = video.videoWidth;
		var videoHoehe = video.videoHeight;
    
		var fensterBreite = $(window).width();
		var fensterHoehe = $(window).height();
		var breiteQuot = fensterBreite/videoBreite;
		var hoeheQuot = fensterHoehe/videoHoehe;
		var fensterBreiteNeu = 0;
		var fensterHoeheNeu = 0;
		var top;
		var left;
		if (breiteQuot<hoeheQuot) {
				fensterHoeheNeu = Math.ceil(fensterBreite/videoBreite*videoHoehe);
				top = Math.ceil((fensterHoehe-fensterHoeheNeu)/2); 
				$(".vjs-tech").attr("width",fensterBreite + "px");
				$(".vjs-tech").attr("height",fensterHoeheNeu + "px");
				$(".vjs-tech").css("top",top + "px");
		} else {
				fensterBreiteNeu = Math.ceil(fensterHoehe/videoHoehe*videoBreite); 
				left = Math.ceil((fensterBreite-fensterBreiteNeu)/2); 
				$(".vjs-tech").attr("height",fensterHoehe + "px");
				$(".vjs-tech").attr("width",fensterBreiteNeu + "px");
				$(".vjs-tech").css("left",left + "px");
	}
	});
}
document.addEventListener("DOMContentLoaded", initVideoSize, false);
</script>
'; 
	}
	
	function zeigeUebersichtsSeite() {
		$where = 'title LIKE "% - %" AND deleted=0 AND hidden=0 AND pid=94682';
		$query = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,title','pages',$where,'','sorting');
		$infoscreenListe = array();
		while ($daten = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)) {
			$infoscreenListe[$daten['uid']] = $daten['title'];
		}
		
		$infoscreenListeX = array(
				94598=>'Infoscreen Stadtmitte',
				94684=>'Infoscreen Flandernstraße',
				95110=>'Infoscreen Göppingen',
				136062=>'Infoscreen Testseite',
		);
		$GLOBALS['TSFE']->additionalHeaderData ['he_tools'] = '
		<script src="http://www.hs-esslingen.de/typo3conf/ext/he_portal/res/jquery/js/jquery-1.7.1b.min.js" type="text/javascript"></script>
		<script type="text/javascript">		
			function reloadCurrentTime() {
				var datum = new Date();
				var jahr = datum.getFullYear();
				var monat = datum.getMonth()+1;
				var tag = datum.getDate();
				var stunde = datum.getHours();
				var minute = datum.getMinutes();
				if(monat<10) monat= "0" + monat;
				if(tag<10) tag= "0" + tag;
				if(stunde<10) stunde= "0" + stunde;
				if(minute<10) minute= "0" + minute;
				var args = "&datSim=1" +
		  						 "&j=" + jahr + 
		  						 "&m=" + monat + 
		  						 "&t=" + tag + 
		  						 "&s=" + stunde + 
		  						 "&mi=" + minute;
		  	window.location.href="' . $this->currentUrl . '" +  args;
		  	return false;
		  }
		</script>
		<style type="text/css" media="screen">
			.infoscreenFrame {
				width: 24%;
				margin: 0 0.5%;
				float: left;
			}
			.floatLeft {
				float: left;
			}
			#infoscreens {
				clear: left;
				overflow: hidden;
			}
			</style>		
				';
		$out = '<form class="floatLeft" action="" method="GET">
							<input type="hidden" name="id" value="' . $GLOBALS['TSFE']->id . '" />
							<input type="hidden" id="datSim" name="datSim" value="1" />
							Jahr:<input size="4" id="j" name="j" value="' . $this->get['j'] . '" />
							Monat:<input size="2" id="m" name="m" value="' . $this->get['m'] . '" />
							Tag:<input size="2" id="t" name="t" value="' . $this->get['t'] . '" />
							Stunde:<input size="2" id="s" name="s" value="' . $this->get['s'] . '" />
							Minute:<input size="2" id="mi" name="mi" value="' . $this->get['mi'] . '" />
							<input type="submit" value="Datum/Uhrzeit setzen" />
						</form>
						<a class="button floatLeft" href="" onclick="reloadCurrentTime();return false;">Aktuelle Uhrzeit setzen</a>
						<a class="button floatLeft" href="http://www.hs-esslingen.de/index.php?id=' . $GLOBALS['TSFE']->id . '">Datum/Uhrzeit löschen (normale Ansicht)</a>
						<div id="infoscreens">';
		$arg = '';
		foreach ($this->get as $param=>$value) {
			if ($param!='id') {
				$arg .= '&' . $param . '=' . $value;
			}
		}
		
		foreach ($infoscreenListe as $seite=>$standort) {
			$url = 'http://www.hs-esslingen.de/index.php?id=' . $seite . $arg;
			$out .= '<div class="infoscreenFrame">
							 <h2><a target="_blank" href="' . $url . '">'  . $standort . '</a></h2>
							 ';
			$out .= '<iframe src="' . $url . '"></iframe>';
			$out .= '</div>';
		}
		$out .= '
		</div>
		<script type="text/javascript">';
		$out .= '
		function initFrameSizes() {
			var frameBreite = $(".infoscreenFrame iframe").width();
			$(".infoscreenFrame iframe").height((frameBreite*3/4));
		}
		$(window).resize(function() {
		  initFrameSizes();
		});
		initFrameSizes();
		</script>
		';		
		return $out;
	}

  public static function getInfoscreenPageTstamp($uid) {
    /*
     * Timestamp der Seite abfragen
     */
    $pageResult = $GLOBALS['TYPO3_DB']->sql_query('SELECT tstamp from pages WHERE uid=' . $uid);
    if ($dataPage = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($pageResult)) {
      $tstamp = $dataPage['tstamp'];
    } else {
      $tstamp = 0;
    }

    $where = 'deleted=0 AND hidden=0 AND pid=' . $uid;
    $result = $GLOBALS['TYPO3_DB']->sql_query('SELECT pi_flexform from tt_content WHERE ' . $where);
    if ($data = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
      $flexformData = t3lib_div::xml2array($data['pi_flexform']);
      $settings = $flexformData['data']['settings']['lDEF'];

      if ($settings['toolskuerzel']['vDEF']=='INFOSCREEN') {
        switch ($settings['infoscreen_app']['vDEF']) {
          case 'SEITENINHALT':
            $contentElem = $settings['infoscreen_seiteninhalt']['vDEF'];
            $contentResult = $GLOBALS['TYPO3_DB']->sql_query('SELECT pid from tt_content WHERE uid=' . $contentElem);
            if ($dataContent = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($contentResult)) {
              $pageResult = $GLOBALS['TYPO3_DB']->sql_query('SELECT tstamp from pages WHERE uid=' . $dataContent['pid']);
              if ($dataPage = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($pageResult)) {
                $tstamp = $dataPage['tstamp'];
              }
            }
          break;
          case 'SEITENINHALT_LISTE':
            $tstamp = 0;
            $page = $settings['infoscreen_seiteninhalt_seite']['vDEF'];
            $pageResult = $GLOBALS['TYPO3_DB']->sql_query('SELECT tstamp from pages WHERE uid=' . $page);
            if ($dataPage = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($pageResult)) {
              $tstamp = $dataPage['tstamp'];
              $contentResult = $GLOBALS['TYPO3_DB']->sql_query('SELECT tstamp from tt_content WHERE deleted=0 AND hidden=0 AND pid=' . $page);
              while ($dataContent = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($contentResult)) {
                if ($dataContent['tstamp']>$tstamp) {
                  $tstamp = $dataContent['tstamp'];
                }
              }
            }
            break;
        }
      }
      return $tstamp;
    }
  }

}
?>