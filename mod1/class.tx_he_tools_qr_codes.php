<?php 
require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_db.php');
require_once(t3lib_extMgm::extPath('he_tools').'res/qr/class.qrcode.php');

class tx_he_tools_qr_codes {
var $post;
var $get;

	public function main($parent,$pageId) {
		$this->post = t3lib_div::_POST();
		$this->get = t3lib_div::_GET();
		$erg = '<div class="qrCodes">';
		$erg .= '<h1 class="heading">QR-Codes</h1>';

		$filter = $this->post['filter'];
		$alias = $this->post['alias'];
		$url = $this->post['url'];
		$lang = $this->post['lang'];
		$uid = $this->post['uid'];
		$auswahl = $this->aliasListe($filter);
		$erg .= $auswahl;
		$erg .= '</div>';
		return $erg;
	}

	/**
	 * splitString
	 * @return (int)
	 */
	protected function splitString() {
		while (strlen($this->dataStr) > 0) {
			$mode = $this->identifyMode(0);
			switch ($mode) {
				case QR_MODE_NM: {
					$length = $this->eatNum();
					break;
				}
				case QR_MODE_AN: {
					$length = $this->eatAn();
					break;
				}
				case QR_MODE_KJ: {
					$length = $this->eat8();
					break;
				}
				default: {
					$length = $this->eat8();
					break;
				}
			}
			if ($length == 0) {
				return 0;
			}
			if ($length < 0) {
				return -1;
			}
			$this->dataStr = substr($this->dataStr, $length);
		}
		return 0;
	}

	/**
	 * Encode the input string to QR code
	 * @param $string (string) input string to encode
	 */
	protected function encodeString($string) {
		$this->dataStr = $string;
		if (!$this->casesensitive) {
			$this->toUpper();
		}
		$ret = $this->splitString();
		if ($ret < 0) {
			return NULL;
		}
		$this->encodeMask(-1);
	}

	private function image($frame, $pixelPerPoint = 4, $outerFrame = 4)	{
		$h = count($frame);
		$w = strlen($frame[0]);

		$imgW = $w + 2*$outerFrame;
		$imgH = $h + 2*$outerFrame;

		$base_image =ImageCreate($imgW, $imgH);

		$col[0] = ImageColorAllocate($base_image,255,255,255);
		$col[1] = ImageColorAllocate($base_image,0,0,0);

		imagefill($base_image, 0, 0, $col[0]);

		for($y=0; $y<$h; $y++) {
			for($x=0; $x<$w; $x++) {
				if ($frame[$y][$x] == '1') {
					ImageSetPixel($base_image,$x+$outerFrame,$y+$outerFrame,$col[1]);
				}
			}
		}

		$target_image = ImageCreate($imgW * $pixelPerPoint, $imgH * $pixelPerPoint);
		ImageCopyResized($target_image, $base_image, 0, 0, 0, 0, $imgW * $pixelPerPoint, $imgH * $pixelPerPoint, $imgW, $imgH);
		ImageDestroy($base_image);
		return $target_image;
	}

	public function jpg($frame, $filename = false, $pixelPerPoint = 8, $outerFrame = 4, $q = 85) {
		$image = $this->image($frame, $pixelPerPoint, $outerFrame);
		Header("Content-type: image/jpeg");
		ImageJpeg($image, null, $q);
		ImageDestroy($image);
	}


	public static function aliasListe($filter='') {
global $SCRIPT_PATH;
		$where = 'alias NOT LIKE "/mitarbeiter/%" AND 
							alias NOT LIKE "/de/%"';
		if (!empty($filter)) {
			$where .= ' AND alias LIKE "%' . $filter . '%" ';
		}
		$urlencoded = urlencode ($SCRIPT_PATH);
		$out = '<script src="../typo3conf/ext/he_portal/res/jquery/js/jquery-1.7.1b.min.js" type="text/javascript"></script>';
		$out .= '<script src="../typo3conf/ext/he_portal/res/jquery/js/portal.js" type="text/javascript"></script>';
		$out .= '<form name="filter" method="post" action="">';
		$out .= '<label for="quality">Qualität:</label>';
		$out .= '<select id="quality" name="quality">';
		$qualities = array(
			'minimal' => '100',
			'Für Webseiten' => '300',
			'Printqualität' => '600',
		);
		$defaultQuality = 600;
		foreach($qualities as $name=>$value) {
			if ($value==$defaultQuality) {
				$selected = ' selected="selected"';
			} else {
				$selected = '';
			}
			$out .= '<option value="' . $value . '"' . $selected . '>' . $name . ' (' . $value . ' Pixel)</option>';
		}
		$out .= '</select>';
		$out .= '<br/><br/><label for="eigeneUrl">Eigene Url eintragen:</label>';
		$out .= '<input type="text" id="eigeneUrl" size="100" />';
		$out .= '<span id="eigeneUrlButton"></span>';

		$out .= '<br/><br/><label for="aliasFilter">Vorhandenen Alias auswählen:</label>';
		$out .= '<input id="aliasFilter" type="text" name="filter" value="' . $filter . '"/>';
		$out .= '&nbsp;(mit * weden alle angezeigt)';
		$out .= '</form><div id="aliasListe"></div>';				
		$out .= '<script>
			function filterAktualisieren() {
			var eingabe = encodeURI($("#aliasFilter").val());
			var eigeneUrl = $("#eigeneUrl").val();
			var quality = $("#quality").val();
				$("#ergebnisliste").detach();
				if (eingabe.length>=1) {
					$("#aliasListe").load("../index.php?eID=he_tools&action=qr_alias_liste&val=" + eingabe + "&quality=" + quality);
				}
				$("#buttonEigeneUrl").detach();
				if (eigeneUrl.length>=1) {
					if (eigeneUrl.indexOf("://")<0) {
						eigeneUrl = "http://" + eigeneUrl;
					}
					var eigeneUrlEncoded = encodeURI(eigeneUrl);
					var ajaxUrl = "../index.php?eID=he_tools&action=qr_url&url=" + eigeneUrlEncoded + "&size=" + quality;
					$("#eigeneUrlButton").load(ajaxUrl);
				}
			}
			$("#eigeneUrl").keyup(function(event) {
				$("#aliasFilter").val("");
				filterAktualisieren();
			});
			$("#aliasFilter").keyup(function(event) {
				filterAktualisieren();
			});
			$("#quality").change(function() {
				filterAktualisieren();
			});
		</script>
		';
		return $out;
	}
	
	protected function aliasBearbeiten($aliasUid,$pageId) {
		$where = 'uid = ' . $aliasUid;
		$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery ('alias,url,lang','tx_six2t3_six_alias',$where);
		if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
			$out = $this->aliasEingabe($row['alias'],$row['url'],$row['lang'],$pageId,'Alias bearbeiten',$aliasUid,TRUE);
		} else {
			$out = '<div class="error">Der Alias wurde nicht gefunden!</div>';
		}
		return $out;
	}

	public static function printAliasliste($eingabe,$size=300) {
		$where = 'alias NOT LIKE "/mitarbeiter/%" AND 
							alias NOT LIKE "/de/%" AND 
							alias NOT LIKE "%.pdf"';
		if (!empty($eingabe) && $eingabe!='*') {
			$where .= ' AND alias LIKE "%' . $eingabe . '%" ';
		}
		$abfrage = $GLOBALS['TYPO3_DB']->exec_SELECTquery ('uid,alias,url,lang','tx_six2t3_six_alias',$where,'','alias');
		$skript = '<script type="text/javascript">
							function fensterOeffnen(url) {
								var fenster = window.open(url, "Alias testen", "top=50,left=50,width=1000,height=600,scrollbars=yes");
								fenster.focus;
							}
							</script>
							';
		$data = '';
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($abfrage)) {
			$link = 'http://www.hs-esslingen.de' . $row['alias'];
			$data .= '<tr>';
			$alias = $row['alias'];
			if ($alias[0]=='/') {
				$alias = substr($alias,1);
			}
			$data .= '<td class="td_300"><a onclick="fensterOeffnen(\'' . $link . '\')">' . $alias . '</a></td>';
			$urlencodedAlias = base64_encode($link);
			$qrDownloadLink = '../index.php?eID=he_tools&action=download_qr_code&url=' . $urlencodedAlias . '&size=' . $size;
			$data .= '<td class="td_200">' . $row['url'] . '</td>';
			$data .= '<td class="td_100"><a class="button" href="' . $qrDownloadLink . '">herunterladen</a></td>';
		}
		if (empty($data)) {
			$out = '<h2>Keine Einträge für diese Auswahl vorhanden!</h2>';
		} else {
			$out = $skript .
							'<table class="grid" id="ergebnisliste">
								<tr>
							  <th class="head td_300">Alias</th>
							  <th class="head td_200">URL bzw. TYPO3-Id</th>
							  <th class="head td_100">QR-Code</th>
							  </tr>' . $data . '</table></form>';
		}
		print $out;
		exit();
	}

	public static function getUrlLink($url, $size=300) {
		$urlEncoded = base64_encode($url);
		$qrDownloadLink = '../index.php?eID=he_tools&action=download_qr_code&url=' . $urlEncoded . '&size=' . $size;
		$out = '<a title="QR-Code für die URL \'' . $url . '\' herunterladen" class="button" id="buttonEigeneUrl" href="' . $qrDownloadLink . '">QR-Code herunterladen</a>';
		print $out;
		exit();
	}
	
	public static function downloadQrCode($url, $alias, $size=300) {
		$urlDecoded = base64_decode($url);
		if ($size>300) {
			$errorCorrection = 'high';
		} elseif ($size>100) {
			$errorCorrection = 'medium';
		} else {
			$errorCorrection = 'low';
		}

		if (empty($alias)) {
			$alias = $urlDecoded;
			$slashPos = strrpos($urlDecoded,'.de/');
			if ($slashPos!==false) {
				$alias = substr($urlDecoded,$slashPos+strlen('.de/'));
			} else {
				$slashPos = strrpos($urlDecoded, '/');
				if ($slashPos!==false) {
					$alias = substr($urlDecoded,$slashPos+1);
				}
			}
		}
		$qrCode = new Endroid\QrCode\QrCode();
		$qrCode->setText($urlDecoded)
			->setSize($size)
			->setPadding(10)
			->setErrorCorrection($errorCorrection)
			->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
			->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
		;
		$image = $qrCode->get();

		$filename = preg_replace('#[^A-Za-z0-9._-]#', '', $alias)  . '_' . $size . '.png';
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename=' . $filename);
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . strlen($image));
		print($image);
		exit();
	}


}


?>