<?php

class tx_he_tools_hochschulexpress	{

	public function bereichAnzeigenNeu(&$cObj,$bild,$bereich,$ueberschrift,$spalteLinks,$spalteRechts) {
		$spaltenBreite = 260;
		$spaltenAbstand = 10;
		$spaltenBreiteRechts = ($spaltenBreite+$spaltenAbstand)*2;
		$spaltenAbstandOben = 8;
		$zeilenAbstandUnten = 20;
		$bildBreite = 200;
		$imgConfig = array();
		$imgConfig['file'] = $bild;
		$imgConfig['file.']['maxW'] = 200;
		$imgConfig['file.']['maxH'] = 150;
		$bildadresse = $cObj->IMG_RESOURCE($imgConfig);
		
		$imgConfigSpacerOben = array();
		$imgConfigSpacerOben['file'] = 'fileadmin/images/layout/leer.gif';
		$imgConfigSpacerOben['file.']['height'] = $spaltenAbstandOben;
		$imgConfigSpacerOben['file.']['width'] = $spaltenBreite;
		$pfadSpacerOben = $cObj->IMG_RESOURCE($imgConfigSpacerOben);
		
		$imgConfigSpacerUnten = array();
		$imgConfigSpacerUnten['file'] = 'fileadmin/images/layout/leer.gif';
		$imgConfigSpacerUnten['file.']['height'] = $zeilenAbstandUnten;
		$imgConfigSpacerUnten['file.']['width'] = $bildBreite;
		$pfadSpacerUnten = $cObj->IMG_RESOURCE($imgConfigSpacerUnten);
		
		$spacerOben = '<img height="' . $spaltenAbstandOben . '" width="' . $spaltenBreite . '" style="padding: 0; margin: 0; display: block; height: ' . $spaltenAbstandOben . 'px; width: ' . $spaltenBreite . 'px;" src="http://www.hs-esslingen.de/' . $pfadSpacerOben . '" />';
		$spacerUnten = '<img height="' . $zeilenAbstandUnten . '" width="' . $bildBreite . '" style="padding: 0; margin: 0; display: block; height: ' . $zeilenAbstandUnten . 'px; width: ' . $bildBreite . 'px;" src="http://www.hs-esslingen.de/' . $pfadSpacerUnten . '" />';
		$spalteRechts = $this->spaltenLinksKorrigieren($spalteRechts);
		$out .= '<table border="0" cellspacing="0" cellpadding="0" style="border-collapse: collapse; font-family: Verdana, Helvetica, sans-serif;">
							<tr style="background: #ECF2F5;color: #004666;">
							<th width="200" valign="top" style="font-size: 14px;font-weight: bold;padding: 4px ' . $spaltenAbstand . 'px; text-align: left;">' . $bereich . '</th>
							<th width="' . $spaltenBreiteRechts . '" valign="top" style="font-size: 14px;font-weight: bold;padding: 4px 10px; text-align: left;">' . $ueberschrift . '</th>
							</tr>
							<tr>
							<td valign="top" align="left" border="0" style="width: 210px; padding: 0; margin: 0;">
								<img width="200" style="padding: 0; margin: 0; display: block;" src="http://www.hs-esslingen.de/' . $bildadresse . '" />
								' . $spacerUnten . '
							</td>
							<td valign="top">
								<table width="' . $spaltenBreiteRechts . '" border="0" cellspacing="0" cellpadding="0" style="width: ' . $spaltenBreiteRechts . 'px; border-collapse: collapse; font-family: Verdana, Helvetica, sans-serif;">
									<tr>
									<td valign="top" border="0" style="width: ' . $spaltenBreite . 'px;font-size: 11px; padding:  0 0 0 ' . $spaltenAbstand . 'px;">' . $spacerOben . $spalteLinks . '</td>
									<td valign="top" border="0" style="width: ' . $spaltenBreite . 'px;font-size: 11px; padding:  0 0 0 ' . $spaltenAbstand . 'px;">' . $spacerOben . $spalteRechts . '</td>
									</tr>
								</table>
							</td>
							</tr>
							</table>
							';
		return $out;
	}
	
	public function bereichAnzeigen(&$cObj,$bild,$bereich,$ueberschrift,$spalteLinks,$spalteRechts) {
		$spaltenBreite = 260;
		$spaltenAbstandOben = 8;
		$zeilenAbstandUnten = 20;
		$bildBreite = 200;
		$imgConfig = array();
		$imgConfig['file'] = $bild;
		$imgConfig['file.']['maxW'] = 200;
		$imgConfig['file.']['maxH'] = 150;
		$bildadresse = $cObj->IMG_RESOURCE($imgConfig);
		
		$imgConfigSpacerOben = array();
		$imgConfigSpacerOben['file'] = 'fileadmin/images/layout/leer.gif';
		$imgConfigSpacerOben['file.']['height'] = $spaltenAbstandOben;
		$imgConfigSpacerOben['file.']['width'] = $spaltenBreite;
		$pfadSpacerOben = $cObj->IMG_RESOURCE($imgConfigSpacerOben);
		
		$imgConfigSpacerUnten = array();
		$imgConfigSpacerUnten['file'] = 'fileadmin/images/layout/leer.gif';
		$imgConfigSpacerUnten['file.']['height'] = $zeilenAbstandUnten;
		$imgConfigSpacerUnten['file.']['width'] = $bildBreite;
		$pfadSpacerUnten = $cObj->IMG_RESOURCE($imgConfigSpacerUnten);
		
		$spacerOben = '<img height="' . $spaltenAbstandOben . '" width="' . $spaltenBreite . '" style="padding: 0; margin: 0; display: block; height: ' . $spaltenAbstandOben . 'px; width: ' . $spaltenBreite . 'px;" src="http://www.hs-esslingen.de/' . $pfadSpacerOben . '" />';
		$spacerUnten = '<img height="' . $zeilenAbstandUnten . '" width="' . $bildBreite . '" style="padding: 0; margin: 0; display: block; height: ' . $zeilenAbstandUnten . 'px; width: ' . $bildBreite . 'px;" src="http://www.hs-esslingen.de/' . $pfadSpacerUnten . '" />';
		$spalteRechts = $this->spaltenLinksKorrigieren($spalteRechts);
		$out = '<table border="0" cellspacing="0" cellpadding="0" style="border-collapse: collapse; font-family: Verdana, Helvetica, sans-serif;">
							<tr style="font-size: 14px;font-weight: bold;background: #ECF2F5;color: #004666;">
							<th style="padding: 4px 10px; text-align: left;">' . $bereich . '</th>
							<th colspan="2" style="padding: 4px 10px; text-align: left;">' . $ueberschrift . '</th>
							</tr>
							<tr>
							<td valign="top" align="left" border="0" style="width: 210px; padding: 0; margin: 0;">
								<img width="200" style="padding: 0; margin: 0; display: block;" src="http://www.hs-esslingen.de/' . $bildadresse . '" />
								' . $spacerUnten . '
							</td>
							<td valign="top" border="0" style="width: ' . $spaltenBreite . 'px;font-size: 11px; padding:  0 0 0 10px;">' . $spacerOben . $spalteLinks . '</td>
							<td valign="top" border="0" style="width: ' . $spaltenBreite . 'px;font-size: 11px; padding:  0 0 0 10px;">' . $spacerOben . $spalteRechts . '</td>
							</tr>
							</table>
							';
		return $out;
	}

	public function bereichAnzeigenZweispaltig(&$cObj,$bild,$bereich,$spalteRechts) {
		$spaltenBreite = 400;
		$spaltenAbstandOben = 8;
		$zeilenAbstandUnten = 20;
		$bildBreite = 400;
		$imgConfig = array();
		$imgConfig['file'] = $bild;
		$imgConfig['file.']['maxW'] = $bildBreite;
		$imgConfig['file.']['maxH'] = intval($bildBreite*3/4);
		$bildadresse = $cObj->IMG_RESOURCE($imgConfig);

		$imgConfigSpacerOben = array();
		$imgConfigSpacerOben['file'] = 'fileadmin/images/layout/leer.gif';
		$imgConfigSpacerOben['file.']['height'] = $spaltenAbstandOben;
		$imgConfigSpacerOben['file.']['width'] = $spaltenBreite;
		$pfadSpacerOben = $cObj->IMG_RESOURCE($imgConfigSpacerOben);

		$imgConfigSpacerUnten = array();
		$imgConfigSpacerUnten['file'] = 'fileadmin/images/layout/leer.gif';
		$imgConfigSpacerUnten['file.']['height'] = $zeilenAbstandUnten;
		$imgConfigSpacerUnten['file.']['width'] = $bildBreite;
		$pfadSpacerUnten = $cObj->IMG_RESOURCE($imgConfigSpacerUnten);

		$spacerOben = '<img height="' . $spaltenAbstandOben . '" width="' . $spaltenBreite . '" style="padding: 0; margin: 0; display: block; height: ' . $spaltenAbstandOben . 'px; width: ' . $spaltenBreite . 'px;" src="http://www.hs-esslingen.de/' . $pfadSpacerOben . '" />';
		$spacerUnten = '<img height="' . $zeilenAbstandUnten . '" width="' . $bildBreite . '" style="padding: 0; margin: 0; display: block; height: ' . $zeilenAbstandUnten . 'px; width: ' . $bildBreite . 'px;" src="http://www.hs-esslingen.de/' . $pfadSpacerUnten . '" />';
		$spalteRechts = $this->spaltenLinksKorrigieren($spalteRechts);
		$out = '<table border="0" cellspacing="0" cellpadding="0" style="border-collapse: collapse; font-family: Verdana, Helvetica, sans-serif;">
							<tr style="font-size: 14px;font-weight: bold;background: #ECF2F5;color: #004666;">
							<th colspan="2" style="padding: 4px 10px; text-align: left;">' . $bereich . '</th>
							</tr>
							<tr>
							<td valign="top" align="left" border="0" style="width: ' . $bildBreite . 'px; padding: 0 10px 0 0; margin: 0;">
								<img width="' . $bildBreite . '" style="padding: 0; margin: 0; display: block;" src="http://www.hs-esslingen.de/' . $bildadresse . '" />
								' . $spacerUnten . '
							</td>
							<td valign="top" border="0" style="width: ' . $spaltenBreite . 'px;font-size: 11px; padding:  0 0 0 10px;">' . $spacerOben . $spalteRechts . '</td>
							</tr>
							</table>
							';
		return $out;
	}

	public function spaltenLinksKorrigieren($text) {
		$imageUrl = 'http://www.hs-esslingen.de/fileadmin/images/layout/pfeil-rechts-newsletter.png';
		$match = array('#<li>#',
									 '#</li>#',
									 '#<ul[^>]*>#',
									 '#</ul>#',
		);
		$replace = array('<tr><td valign="top"><img src="' . $imageUrl . '" /></td><td valign="top">',
										 '</td></tr>',
										 '<table border="0">',
										 '</table>',
		);
		$out = preg_replace($match, $replace, $text);
		return $out;
	}
	
	public function createTitleImage(&$cObj,$title,$fontSize) {
		$ts['img']='IMG_RESOURCE';    
		$ts['img.']['file']='GIFBUILDER';		  
		$ts['img.']['file.']['format']='png';
		$ts['img.']['file.']['XY']='540,60';    
		
		// insert text
		$ts['img.']['file.']['10']='TEXT';
		$ts['img.']['file.']['10.']['text'] = $title;
		$ts['img.']["file."]["backColor"] = "#ffffff";
//		$ts['img.']["file."]["transparentBackground"] = "1";
//		$ts['img.']["file."]["transparentColor"] = "#ffffff";
		// style textappearance
		$ts['img.']['file.']['10.']['fontSize'] = $fontSize;
		$ts['img.']['file.']['10.']['fontFile'] = 'fileadmin/fonts/ScalaSansOTBold.otf';
		$ts['img.']['file.']['10.']['fontColor'] = '#a8ccdd';
		$ts['img.']['file.']['10.']['nicetext'] = '1';
		$ts['img.']['file.']['10.']['offset'] = '0,46';
		
		$out = '<img alt="' . $title . '" title="' . $title . '" src="' . $cObj->IMG_RESOURCE($ts['img.']) . '">
						<hr class="clearer">';
		return $out;
	}

}
?>
