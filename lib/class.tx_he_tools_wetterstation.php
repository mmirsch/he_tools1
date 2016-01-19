<?php

require_once(t3lib_extMgm::extPath('he_tools').'lib/class.tx_he_tools_jqplot.php');

class tx_he_tools_wetterstation  {

  protected $messwertIds = array(
    1 => 'Aussentemperatur',
    2 => 'Luftfeuchtigkeit',
    3 => 'Helligkeit',
    4 => 'Globalstrahlung',
    5 => 'Azimuth',
    6 => 'Höhenwinkel',
    7 => 'Windgeschwindigkeit',
    8 => 'Windrichtung',
    9 => 'Niederschlag',
  );

  protected $zeitEinheiten = array(
    'min_05' => '05 Minuten',
    'min_15' => '15 Minuten',
    'min_30' => '30 Minuten',
    'hour' => 'Stunden',
    'day' => 'Tage',
    'week' => 'Wochen',
    'month' => 'Monate',
    'year' => 'Jahre',
  );

  protected $js = '
    <script type="text/javascript">
      $(document ).ready(function() {
          $(".datepicker").datepicker(
            dateFormat: "dd.mm.yy",
						changeMonth: true,
            changeYear: true,
						showButtonPanel: true
          );
      });
  </script>
  ';

  protected $css = '
    <style type="text/css">
        form.wetter label {
            float: left;
            width: 10em;
            text-align: right;
            margin-right: 4px;
        }
        form.wetter input[type=text],
        form.wetter select {
            float: left;
            width: 12em;
        }
        form.wetter div.row {
            clear: both;
            padding-top: 4px;
        }
        table.wetter {
            overflow: hidden;
            margin: 10px 0;
            width: 140px;
            background: #fff;
            border-collapse: separate;
        }

        table.wetter div {
            float: left;
            clear: left;
            margin: 2px 0;
        }

        table.wetter div.wettericon {
            padding-left: 30px;
        }

        table.wetter table.windrichtung {
            margin: auto;
        }

        table.windrichtung.hidden {
            display: none;
        }


        table.wetter td {
            padding: 0;
        }

        table.wetter table.werte {
            margin: auto;
        }
        table.wetter table.werte td {
            padding: 0 2px;
        }

        table.wetter table.werte td.label {
            text-align: right;
        }
        table.wetter table.werte td.value {
            text-align: right;
        }
        table.wetter table.werte td.unit {
            text-align: left;
            padding-left: 0;
        }

        table.windrichtung td {
            text-align: center;
            vertical-align: middle;
            margin: 0;
            padding: 2px;
        }

        .csc-frame-frame1 table.wetter {
            border: 2px solid #004666;
            margin-left: 25px;
        }

    </style>
  ';

  public function aktuellesWetter($detailed=false) {
    $GLOBALS['TSFE']->additionalHeaderData['he_tools'] .= $this->css;
    $url = 'https://www14.hs-esslingen.de/wetterstation/datenpunkte.xml';
    $report = array();

    $content = tx_he_tools_rz_skripte::getURL($url, false, false, $report);
    $data = simplexml_load_string($content) or die("Error: Cannot create object");
    $zeitpunkt = date("d.m.Y H:i",time());
    $messdaten = array();
    $elemente = $data->{'datenpunkt'};

    foreach ($elemente as $element) {
      $id = '' . $element->attributes()->{'ID'};
      $messdaten[$id] = array(
        'label'=>$element->attributes()->beschreibung[0],
        'einheit'=> $element->attributes()->einheit[0],
        'value'=> $element->wert,
      );
      if (!empty($element->datenpunkt)) {
        foreach ($element->datenpunkt as $unterElement) {
          $id = '' . $unterElement->attributes()->{'ID'};
          $messdaten[$id] = array(
            'label'=>$unterElement->attributes()->beschreibung[0],
            'einheit'=> $unterElement->attributes()->einheit[0],
            'value'=> $unterElement->wert,
          );
        }
      }
    }
    if (!$detailed) {
      $windrichtungsIcon = sprintf("wind%02d.png", floor($messdaten['8']['value'] / 10));
      $windrichtung = sprintf("Windrichtung: %.2f Grad", floatval($messdaten['8']['value']));
      $temperatur = sprintf("%.2f", floatval($messdaten['1']['value']));
      $luftfeuchtigkeit = sprintf("%.2f", floatval($messdaten['2']['value']));
      $windgeschwindigkeit = sprintf("%.2f", floatval($messdaten['7']['value']) * 3.6);

      $messWerte['temperatur'] = floatval($messdaten['1']['value']);
      $messWerte['niederschlag'] = floatval($messdaten['9']['value']);
      $messWerte['helligkeit'] = floatval($messdaten['3']['value']);
      if (floatval($messdaten['7']['value'])<=1) {
        $windrichtungClass = ' hidden';
      } else {
        $windrichtungClass = '';
      }

      /* Wolkig? */
      if ($messWerte['helligkeit']<700) {
        /* Regen? */
        if ($messWerte['niederschlag']>0) {
          $wetterIcon = 'night_rain.png';
          $wetterIconAlt = 'regnerisch';
        } else {
          $wetterIcon = 'night.png';
          $wetterIconAlt = 'trocken';
        }

      } else if ($messWerte['helligkeit']<7000) {
        /* Regen? */
        if ($messWerte['niederschlag']>0) {
          $wetterIcon = 'cloudy_rain.png';
          $wetterIconAlt = 'bewölkt mit Regen';
        } else {
          $wetterIcon = 'cloudy_no_sun.png';
          $wetterIconAlt = 'bewölkt';
        }
      /* Wolkig mit Sonne? */
      } else if ($messWerte['helligkeit']<25000) {
        /* Regen? */
        if ($messWerte['niederschlag']>0) {
          $wetterIcon = 'cloudy_sun_rain.png';
          $wetterIconAlt = 'bewölkt mit leichtem Regen';
        } else {
          $wetterIcon = 'cloudy_sun.png';
          $wetterIconAlt = 'leicht bewölkt';
        }
      } else {
        $wetterIcon = 'sunny.png';
        $wetterIconAlt = 'sonnig';
      }

      $out =
      $out = '<table class="wetter">' .
          '<tr><td>' .
            '<div class="wettericon"><img alt="' . $wetterIconAlt . '" title="' . $wetterIconAlt . '" src="' . t3lib_extMgm::siteRelPath('he_tools') . 'res/images/wetter/' . $wetterIcon . '" /></div>' .
          '</td></tr>' .
          '<tr><td>' .
            '<table class="werte">' .
            '<tr><td class="label">T:</td><td class="value">' . $temperatur . '</td><td class="unit">°C</td></tr>' .
            '<tr><td class="label">r.F:</td><td class="value">' . $luftfeuchtigkeit . '</td><td class="unit">%</td></tr>' .
            '<tr><td class="label">Wind:</td><td class="value">' . $windgeschwindigkeit . '</td><td class="unit"> km/h</td></tr>' .
            '</table>' .
          '</td></tr>' .
          '<tr><td>' .
              '<table class="windrichtung' . $windrichtungClass . '">
                <tr><td> </td><td>N</td><td> </td></tr>
                <tr><td>W</td><td><img alt="' . $windrichtung . '" title="' . $windrichtung . '" src="' . t3lib_extMgm::siteRelPath('he_tools') . 'res/images/wetter/' . $windrichtungsIcon . '" /></td><td>O</td></tr>
                <tr><td> </td><td>S</td><td> </td></tr>
              </table>' .
          '</td></tr>' .
        '</table>';
    } else {
      $out = '<h2>Uhrzeit der letzten Messung: ' . $zeitpunkt . '</h2>';
      $out .= '<table class="tab50 zweifarbig grid">';
      $out .= '<tr><th>Bezeichnung</th><th>Messwert</th></tr>';
      foreach($messdaten as $id=>$eintrag) {
        $out .= '<tr><td>' . $eintrag['label'] . '</td><td>' . $eintrag['value'] .  $eintrag['einheit'] . '</td></tr>';
      }
      $out .= '</table>';
    }

    return $out;
  }

  public function wetterFormular() {
    $GLOBALS['TSFE']->additionalHeaderData['he_tools'] .= '
		<link href="' . t3lib_extMgm::siteRelPath('fe_management') . 'res/jquery.ui.datepicker.css" rel="stylesheet" type="text/css" />
		<script src="' . t3lib_extMgm::siteRelPath('fe_management') . 'res/jquery-ui-1.8.20.custom.min.js" type="text/javascript"></script>
    ' .
      $this->js .
      $this->css;

    /*
		 * Formulardaten per POST abfragen
		 */
    $post = t3lib_div::_POST();
    $fehlerListe = '';
    $wetterDaten = '';
    if (!empty($post['export'])) {
      $wetterDaten = $this->wetterDatenExportieren($post);
    } else if (!empty($post['absenden'])) {
      $fehlerListe = $this->pruefeEingaben($post);
      if (empty($fehlerListe)) {
        $wetterDaten = $this->wetterDatenAusgeben($post);
      }
    }
    $formularDaten = $this->formularAnzeigen($post, $fehlerListe);
    $out = $formularDaten . $wetterDaten;
    return $out;
  }


  /*
   * Formulardaten validieren
   */
  protected function pruefeEingaben(&$post) {
    $fehlerListe = array();
    foreach($post as $key=>$val) {
      if (empty($val) && $val!='0') {
        $fehlerListe[] = $key;
      }
    }
    return $fehlerListe;
  }

  /*
   * Datenexport
   */


  public function wetterDatenExportieren($post) {
    $heute = date("d.m.Y",time());
    if (isset($post['startdatum'])) {
      $startZeitpunkt = htmlspecialchars($post['startdatum']);
    } else {
      $startZeitpunkt = $heute;
    }
    if (isset($post['enddatum'])) {
      $endZeitpunkt = htmlspecialchars($post['enddatum']);
    } else {
      $endZeitpunkt = $heute;
    }
    $startZeitpunktSql = substr($startZeitpunkt,6,4) . '-' .  substr($startZeitpunkt,3,2) . '-' . substr($startZeitpunkt,0,2) . ' 00:00:00';
    $endZeitpunktSql = substr($endZeitpunkt,6,4) . '-' .  substr($endZeitpunkt,3,2) . '-' . substr($endZeitpunkt,0,2) . ' 23:59:59';

    /** @var t3lib_DB $db */
    $db = he_tools_db::mysql2Connect();
//    $whereZeiteinheit = '';
    $abfrage = $db->sql_query('SELECT * FROM werte WHERE Zeitstempel>= "' . $startZeitpunktSql . '" AND Zeitstempel<= "' . $endZeitpunktSql . '"');

    $nl = chr(13) . chr(10);
    $messdaten = array();
    while ($daten = $db->sql_fetch_assoc($abfrage)) {
      if (!isset($messdaten[$daten['Zeitstempel']])) {
        $messdaten[$daten['Zeitstempel']] = array();
      }
      $messdaten[$daten['Zeitstempel']][$daten['ID']] = $daten['Wert'];
    }
    $out = '';
    foreach ($messdaten as $zeit=>$zeitDaten) {
      if (empty($out)) {
        $spaltenTitel = array('Datum/Uhrzeit');
        foreach ($zeitDaten as $id=>$wert) {
          $spaltenTitel[] = iconv('UTF-8','CP1252',$this->messwertIds[$id]);
        }
        $out .=  '"' . implode('";"',$spaltenTitel) . '"' . $nl;
      }
      $zeitMessungen = array($zeit);
      foreach ($zeitDaten as $id=>$wert) {
        $zeitMessungen[] = $wert;
      }
      $out .= '"' . implode('";"',$zeitMessungen) . '"' . $nl;
    }

    $dateiname = 'wetterdaten_export.csv';
    header("Content-type: text/csv");
    header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('Content-Disposition: inline; filename="'.$dateiname.'"');
    header('Pragma: no-cache');
    print $out;
    exit();

  }

  /*
 * Formular zum Eintragen eines neuen Elements
 */
  public function formularAnzeigen($post, $fehlerListe=array()) {
    $id = $GLOBALS['TSFE']->id;
    $heute = date("d.m.Y",time());
    $out = '';
    if (!empty($fehlerListe)) {
      $out .= '<h2 class="error">Sie haben nicht alle Felder ausgefüllt!</h2>';
    }
    $out .= '<form class="wetter" action="index.php?id=' . $id . '" method="POST">';
    if (isset($post['startdatum'])) {
      $value = htmlspecialchars($post['startdatum']);
    } else {
      $value = $heute;
    }
    $out .= '<div class="row">' .
      '<label for="startdatum">Startdatum</label>' .
      '<input class="datepicker" type="text" id="startdatum" name="startdatum" size="30" maxlength="30" value="' . $value . '" />' .
      '</div>';
    if (isset($post['enddatum'])) {
      $value = htmlspecialchars($post['enddatum']);
    } else {
      $value = $heute;
    }
    $out .= '<div class="row">' .
      '<label for="enddatum">Enddatum</label>' .
      '<input class="datepicker" type="text" id="enddatum" name="enddatum" size="30" maxlength="30" value="' . $value . '" />' .
      '</div>';

    if (isset($post['zeiteinheit'])) {
      $value = htmlspecialchars($post['zeiteinheit']);
    } else {
      $value = 'hour';
    }
    $out .= '<div class="row">' .
      '<label for="zeiteinheit">Zeiteinheit</label>' .
      '<select id="zeiteinheit" name="zeiteinheit">' .
      '<option value="">Bitte auswählen</option>';
    foreach ($this->zeitEinheiten as $key=>$label) {
      if ($value == $key) {
        $selected = ' selected="selected" ';
      } else {
        $selected = '';
      }
      $out .= '<option ' . $selected . ' value="' . $key . '">' . $label . '</option>';
    }
    $out .=  '</select>';
    $out .=  '</div>';

    if (isset($post['messwerte'])) {
      $value = htmlspecialchars($post['messwerte']);
    } else {
      $value = 1;
    }
    $out .= '<div class="row">' .
      '<label for="messwerte">Messwerte</label>' .
      '<select id="messwerte" name="messwerte">' .
      '<option value="">Bitte auswählen</option>';
    foreach ($this->messwertIds as $key=>$label) {
      if ($value == $key) {
        $selected = ' selected="selected" ';
      } else {
        $selected = '';
      }
      $out .= '<option ' . $selected . ' value="' . $key . '">' . $label . '</option>';
    }
    $out .=  '</select>';
    $out .=  '</div>';

    $out .= '<div class="row">' .
      '<input type="submit" id="absenden" name="absenden" value="Wetterdaten anzeigen" />' .
      '<input type="submit" id="export" name="export" value="Wetterdaten exportieren" />' .
      '</div>';
    $out .= '</form>';
    return $out;
  }

  protected function wetterDatenAusgeben($paramater) {
    /** @var tx_he_tools_jqplot $jqPlot */
    $jqPlot = t3lib_div::makeInstance('tx_he_tools_jqplot');

    /** @var t3lib_DB $db */
    $db = he_tools_db::mysql2Connect();
    $startZeitpunkt = $paramater['startdatum'];
    $endZeitpunkt = $paramater['enddatum'];
    $startZeitpunktSql = substr($startZeitpunkt,6,4) . '-' .  substr($startZeitpunkt,3,2) . '-' . substr($startZeitpunkt,0,2) . ' 00:00:00';
    $endZeitpunktSql = substr($endZeitpunkt,6,4) . '-' .  substr($endZeitpunkt,3,2) . '-' . substr($endZeitpunkt,0,2) . ' 23:59:59';
    $zeiteinhait = $paramater['zeiteinheit'];

    $whereZeiteinheit = '';
    switch ($zeiteinhait) {
      case 'min_05':
        $whereZeiteinheit = ' AND (Zeitstempel LIKE "%:00:0%" OR Zeitstempel LIKE "%:05:0%" OR Zeitstempel LIKE "%:10:0%" OR ' .
          'Zeitstempel LIKE "%:15:0%" OR Zeitstempel LIKE "%:20:0%" OR Zeitstempel LIKE "%:25:0%" OR ' .
          'Zeitstempel LIKE "%:30:0%" OR Zeitstempel LIKE "%:35:0%" OR Zeitstempel LIKE "%:40:0%" OR ' .
          'Zeitstempel LIKE "%:45:0%" OR Zeitstempel LIKE "%:50:0%" OR Zeitstempel LIKE "%:55:0%"' .
          ')';
        break;
      case 'min_15':
        $whereZeiteinheit = ' AND (Zeitstempel LIKE "%:00:0%" OR Zeitstempel LIKE "%:15:0%" OR ' .
          'Zeitstempel LIKE "%:30:0%" OR Zeitstempel LIKE "%:45:0%"' .
          ')';
        break;
      case 'min_30':
        $whereZeiteinheit = ' AND (Zeitstempel LIKE "%:00:0%" OR Zeitstempel LIKE "%:30:0%"' .
          ')';
        break;
      case 'hour':
      case 'day':
      case 'week':
      case 'month':
      case 'year':
      $whereZeiteinheit = ' AND Zeitstempel LIKE "%:00:0%"';
        break;
    }

//    $whereZeiteinheit = '';
    $abfrage = $db->sql_query('SELECT * FROM werte WHERE ID=' . $paramater['messwerte'] .
                              ' AND Zeitstempel>= "' . $startZeitpunktSql . '" AND Zeitstempel<= "' . $endZeitpunktSql . '"' .
                              $whereZeiteinheit);

    $messdaten = array();
    while ($daten = $db->sql_fetch_assoc($abfrage)) {
      $zeit = substr($daten['Zeitstempel'],11,5);
      $zeit = substr($daten['Zeitstempel'],0,16);
      $messdaten[] = array('zeit'=>$zeit,'wert'=>$daten['Wert']);
    }
    if ($startZeitpunkt!=$endZeitpunkt) {
      $zeitraum = 'vom ' . $startZeitpunkt . ' bis ' . $endZeitpunkt;
    } else {
      $zeitraum = 'am ' . $startZeitpunkt;
    }
    $out = $jqPlot->temperaturKurve($zeitraum,$messdaten,$this->messwertIds[$paramater['messwerte']]);
    return $out;
  }


}

?>