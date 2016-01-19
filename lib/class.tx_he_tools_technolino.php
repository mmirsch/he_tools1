<?php

require_once(t3lib_extMgm::extPath('fe_management') . 'model/events/class.tx_femanagement_model_events.php');
require_once(t3lib_extMgm::extPath('fe_management') . 'model/events/class.tx_femanagement_model_events_dates.php');
require_once(t3lib_extMgm::extPath('fe_management') . 'model/events/class.tx_femanagement_model_events_anmeldungen.php');

class tx_he_tools_technolino {
  protected $germanMonth = array(
    1 => 'Januar',
    2 => 'Februar',
    3 => 'März',
    4 => 'April',
    5 => 'Mai',
    6 => 'Juni',
    7 => 'Juli',
    8 => 'August',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Dezember',
  );

  protected $germaWeekday = array(
    1 => 'Montag',
    2 => 'Dienstag',
    3 => 'Mittwoch',
    4 => 'Donnerstag',
    5 => 'Freitag',
    6 => 'Samstag',
    7 => 'Sonntag',
  );

  function zeige_termine(&$piBase,$pid) {
    $GLOBALS['TSFE']->additionalHeaderData['hetools'] .= '
				<link rel="stylesheet" type="text/css" href="' . t3lib_extMgm::siteRelPath('fe_management') . 'res/events/css/events.css"/>
				<link rel="stylesheet" type="text/css" href="' . t3lib_extMgm::siteRelPath('he_tools') . 'res/technolino.css"/>
				';
    $heute = time();
    setlocale(LC_ALL, "de_DE.UTF-8");
    $uidBooking = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_hetools.']['buchungs_seite'];
    $linkUrl = 'index.php?id=' . $uidBooking;
    /** @var  $terminModel tx_femanagement_model_events */
    $terminModel = t3lib_div::makeInstance('tx_femanagement_model_events',$piBase,$pid);
    /** @var  $anmeldungenModel tx_femanagement_model_events_anmeldungen */
    $anmeldungenModel = t3lib_div::makeInstance('tx_femanagement_model_events_anmeldungen',$piBase,$pid);
    $eventListComplete = $terminModel->getEventList($piBase,$pid,$heute);
    $out = '<div class="events anmeldungen">';
    foreach($eventListComplete as $eventId=>$eventList) {
      $eventDaten = $terminModel->getEventData($eventId);
      $title = $eventDaten['title'];
      $pfad = 'uploads/tx_femanagement_events/' . $eventDaten['pic'];

      $imgConfig = array();
      $imgConfig['file'] = $pfad;
      $imgConfig['file.']['maxW'] = 150;
      $imgConfig['file.']['maxH'] = 100;
      $bildadresse = $piBase->cObj->IMG_RESOURCE($imgConfig);
      $bild = '<a title="' . $title . '" class="lightbox" href="' . $pfad . '"><img src="' . $bildadresse . '" /></a>';

      $out .= '<h1>' . $title . '</h1>' .
        '<h2>Thema: ' . $eventDaten['subtitle'] . '</h2>' .
        '<div class="pic left">' . $bild . '</div>' .
        '<div class="data left"><div class="description">' . $eventDaten['description']  . '</div>' .
        '<div class="contact">' . $eventDaten['contact']  . '</div></div>';
      foreach($eventList as $year=>$yearEvents) {
        foreach($yearEvents as $month=>$monthEvents) {
          $monthGerman = $this->germanMonth[$month];
          $out .= '<h2>' . $monthGerman . ' ' . $year . '</h2>';
          $out .= '<table class="grid">';
          $numDayEvents = 3;
          foreach($monthEvents as $day=>$dayEvents) {
            $weekDay = date('N',$day);
            $datum = $this->germaWeekday[$weekDay] . ', ' . date('d.m.Y',$day);
            $out .= '<tr class="title"><th colspan="' . $numDayEvents . '">' . $datum . '</th></tr>';
            $out .= '<tr class="data">';
            foreach ($dayEvents as $dateId=>$dateData) {
              $terminBelegt =  $anmeldungenModel->anmeldungVorhanden($eventId, $dateId);
              $von = gmdate('H:i',$dateData['start']);
              $bis = gmdate('H:i',$dateData['end']);
              if ($terminBelegt) {
                $out .= '<td title="Dieser Termin ist bereits belegt" class="complete">' . $von . ' - ' . $bis . '</td>';
              } else {
                $href= $linkUrl . '&eventId=' . $eventId . '&eventDateId=' . $dateId . '&tx_femanagement[mode]=new&popup=1';
                $jsData = ' data-url="' . $href . '" data-eventId="' . $eventId . '" data-eventDateId="' . $dateId . '" ';
                $out .= '<td class="free"><a class="bookEvent" target="_blank" title="anmelden" href="' . $href . '" ' . $jsData . '>' . $von . ' - ' . $bis . '</a></td>';
              }
            }
            $out .= '</tr>';
            $out .= '<tr class="space"><td colspan="' . $numDayEvents . '"> </td></tr>';
          }
          $out .= '</table>';
        }
      }
    }
    $out .= '</div>';
    $out .= '
      <script type="text/javascript">
       $(".bookEvent").click(function() {
          var url = "http://www.hs-esslingen.de/" + $(this).attr("data-url");
          var fenster = window.open(url, "Veranstaltung buchen", "left=50,top=50,width=600,height=600,scrollbars=yes,resizable=yes");
          fenster.focus();
		      return false;
      });
      </script>
      ';
    return $out;
	}

  public function veranstaltungBuchen(&$piBase,$pid,$eventId,$eventDateId) {
    $GLOBALS['TSFE']->additionalHeaderData['hetools'] .= '
				<link rel="stylesheet" type="text/css" href="' . t3lib_extMgm::siteRelPath('fe_management') . 'res/femanagement.css"/>
				<link rel="stylesheet" type="text/css" href="' . t3lib_extMgm::siteRelPath('he_tools') . 'res/technolino.css"/>
				';
    /** @var  $terminModel tx_femanagement_model_events */
    $terminModel = t3lib_div::makeInstance('tx_femanagement_model_events',$piBase,$pid);
    $eventData = $terminModel->getEventData($eventId);
    $out = '<h1>' . $eventData['title'] . '</h1>' .
            '<h2>Thema: ' . $eventData['subtitle'] . '</h2>';
    $out .= '<form class="technolino" method="POST" action="">';
    $out .= $this->getInputField('organization', 'Name Ihrer Einrichtung');
    $out .= $this->getInputField('first_name', 'Vorname');
    $out .= $this->getInputField('last_name', 'Nachname');
    $out .= $this->getInputField('street', 'Straße/Hausnummer');
    $out .= $this->getInputField('zip', 'Plz');
    $out .= $this->getInputField('city', 'Ort');
    $out .= $this->getInputField('email', 'E-Mail-Adresse');
    $out .= $this->getInputField('phone', 'Tel.');
    $out .= $this->getTextareaField('remarks', 'Bemerkungen', false);
    $out .= '<div id="buttons" class="field">
        <input type="submit" name="anmelden" value="Verbindlich anmelden" />
         </div>';
    $out .= '</form>';
    return $out;
  }

  function getInputField($fieldname,$title,$required=true) {
    if ($required) {
      $requiredText = 'required="required"';
      $requiredLabel = '<span class="notify_required">*</span>';
    } else {
      $requiredText = '';
      $requiredLabel = '';
    }
    return '
        <div id="field" class="field">
          <label class="input" for="' . $fieldname . '">' . $title . $requiredLabel . '</label>
          <div class="field_data">
            <input ' . $requiredText . ' id="' . $fieldname . '" name="' . $fieldname . '" type="text" size="80" value="" class="' . $fieldname . '">
          </div>
        </div>
    ';
  }

  function getTextareaField($fieldname,$title,$required=true) {
    if ($required) {
      $requiredText = 'required="required"';
      $requiredLabel = '<span class="notify_required">*</span>';
    } else {
      $requiredText = '';
      $requiredLabel = '';
    }
    return '
        <div id="field" class="field">
          <label class="input" for="' . $fieldname . '">' . $title . $requiredLabel . '</label>
          <div class="field_data">
            <textarea ' . $requiredText . ' cols="80" rows="8" id="' . $fieldname . '" name="' . $fieldname . '" type="text" size="80" value="" class="' . $fieldname . '"></textarea
          </div>
        </div>
    ';
  }

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_tools/lib/class.tx_he_tools_technolino.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_tools/lib/class.tx_he_tools_technolino.php.php']);
}
?>