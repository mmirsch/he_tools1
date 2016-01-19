<?php 
	$redirectEnc = $_GET['redirectEnc'];
	if (empty($redirectEnc)) {
		header('Location: http://www.hs-esslingen.de/de/fehler.html');
		exit();
	} 
	
	$zeit = $_GET['zeit'];
	$grund = $_GET['grund'];
	$heUrl = base64_decode($redirectEnc);
	$alias = 'http://www.hs-esslingen.de' . $_GET['alias'];
//echo $alias;exit;
	$url = 'http://www.hs-esslingen.de' . $heUrl;
	if ($zeit>30) {
		$zeit = 30;
	}
	switch ($grund) {
	case	'schutz':
		$meldungGrund = '<h1>Link auf gesch&uuml;tzte Inhalte</h1>
					<p>Sie haben einen Link auf zugriffsgesch&uuml;tzte Inhalte über ein externes Dokument aufgerufen. </p>';
		$break;
	case	'six':
		$meldungGrund = '<h1>Veralteter Link</h1>
								<h3>Sie haben einen veralteten Link der Hochschule Esslingen aufgerufen: </h3>
								<h4>' . $alias . '</h4>
								<h3>Die neue Url lautet:</h3>
								<h4>' . $url . '</h4>
								 <p>Bitte ändern Sie ggf. Ihr Lesezeichen.</p>';
		$break;
	}
	if ($zeit==0) {
		$html = '<html><head>
						 <meta http-equiv="refresh"  content="0;url=' . $url . '">
						 </head><body></body></html>';
	} else {
		$textWeiterleitung = '<p>Sie werden';
		if ($zeit>0) {
			$textWeiterleitung .= ' in <span style="font-weight: bold;" id="countdown">' . $zeit . '</span> Sekunden';
		}
		$textWeiterleitung .= ' automatisch auf die folgende Seite weitergeleitet:<br/>' .
													'<b>' . $url . '</b>' . 
													'<br/>(ggf. &uuml;ber die zentrale Anmeldeseite der Hochschule Esslingen).</p>';
		$html = '<html>
					<head>
					<style>
					html {
					font-family: Verdana, Helvetica, sans-serif;
					font-size: 75%;
					color: #444;
					}
					h1 {
						font-size: 120%;
						background: #719DB0;
						color: white;
						display: block;
						padding: 5px 5px 5px 5px;
						margin: 10px 0;
						font-weight: bold;
					}
					a:link, a:visited {
						color: #004666;
					}
					</style>
					<meta http-equiv="refresh"  content="' . $zeit . ';url=' . $url . '">
					</head>
					<body>
				    <div style="margin: 0 auto; width: 800px; background: #fff; overflow: hidden; padding: 30px;">
									<a href="http://www.hs-esslingen.de/de/">
										<img src="http://www.hs-esslingen.de/fileadmin/images/banner/logo.png" width="200" height="46" alt="Logo Hochschule Esslingen" title="Startseite" />
									</a>
		      	</div>

				    <div style="margin: 0 auto; width: 800px; background: #fff; overflow: hidden; padding: 30px;">
					' . $meldungGrund . '
					<p>' . 
					$textWeiterleitung . 
					'Falls Sie nicht automatisch weitergeleitet werden klicken Sie bitte auf den folgenden Link:<br>
					<p><a href="' . $url . '">' . $url . '</a></p>
					<br>
					<p>Über den folgenden Link gelangen Sie</p>
					<strong><a href="http://www.hs-esslingen.de/de">zur Startseite der Hochschule Esslingen</a></strong>
				  </div>
				  ';
					if ($zeit>0) {
						$html .= '	
					  <script>
					  var counter = ' . $zeit . ';
					  function CountdownAnzeigen() {
					  	counter--;
					  	var countdown = document.getElementById("countdown");
					  	countdown.innerHTML = counter;
					  	if (counter>0) {
					  		window.setTimeout("CountdownAnzeigen()", 1000);
					  	}
					  }
					  window.setTimeout("CountdownAnzeigen()", 1000);
					  </script>
						';
					} 
					$html .= '</body></html>';
	}
	echo $html;
?>