<?php

require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_util.php');

class tx_he_tools_jqplot  {
	public function temperaturKurve($zeitraum, $daten, $einheit) {
		$GLOBALS['TSFE']->additionalHeaderData ['he_tools_css_1'] = '<link href="' . t3lib_extMgm::siteRelPath('he_tools') . 'res/jqplot/jquery.jqplot.css" rel="stylesheet" type="text/css" />';
		$GLOBALS['TSFE']->additionalHeaderData ['he_tools_js'] = '
			<script class="include" language="javascript" type="text/javascript" src="' . t3lib_extMgm::siteRelPath('he_tools') . 'res/jqplot/jquery.jqplot.min.js" /></script>
			<script class="include" language="javascript" type="text/javascript" src="' . t3lib_extMgm::siteRelPath('he_tools') . 'res/jqplot/plugins/jqplot.cursor.min.js"></script>
			<script class="include" language="javascript" type="text/javascript" src="' . t3lib_extMgm::siteRelPath('he_tools') . 'res/jqplot/plugins/jqplot.dateAxisRenderer.min.js"></script>
			<script class="include" language="javascript" type="text/javascript" src="' . t3lib_extMgm::siteRelPath('he_tools') . 'res/jqplot/plugins/jqplot.categoryAxisRenderer.min.js"></script>
			<script class="include" language="javascript" type="text/javascript" src="' . t3lib_extMgm::siteRelPath('he_tools') . 'res/jqplot/plugins/jqplot.pointLabels.min.js"></script>
			';
		
		$out = '
		<h2>' . $einheit . ' ' . $zeitraum . ' in Esslingen Neckar</h2>
		<div id="wetter_chart1" style="height:400px;width:720px; "></div>
		
		<script type="text/javascript">
		$(document).ready(function(){
		  var line1=
		  [
		  ';
		foreach($daten as $eintrag) {
			$out .= '["' . $eintrag['zeit'] . '",' . $eintrag['wert'] . ']
			,';
		}

		$xSkala = "%2.1f";
		switch ($einheit) {
			case 'Aussentemperatur':
				$xSkala = '%2.1f °C';
				break;
			case 'Luftfeuchtigkeit':
				$xSkala = '%2.1f %';
				break;
			case 'Helligkeit':
				$xSkala = '%d Lux';
				break;
			case 'Windgeschwindigkeit':
				$xSkala = '%2.1f m/s';
				break;
			case 'Niederschlag':
				$xSkala = '%2.1f mm';
				break;
		}
		$out .= '];
		var plot1 = $.jqplot("wetter_chart1", [line1], {
	      gridPadding:{right:35},
	      axes:{
	        xaxis:{
	          renderer:$.jqplot.DateAxisRenderer,
            rendererOptions: {
                tickInset: 0
            },
            tickRenderer: $.jqplot.CanvasAxisTickRenderer,
	          tickOptions: {
	            formatString:"%#d.%m, %H:%M"
	          },
	        },
	        yaxis: {
						tickOptions:{formatString:"' . $xSkala . '"} ,
						tickInterval:"2"
	        }
	      },
	    	cursor: {
	    		show: true,
	    		zoom: true
	    	},
	      series:[{lineWidth:4, markerOptions:{style:"filledCircle"}}]
	  });
	});
		
		</script>
		';
		return $out;
	}
}

?>