<?php

require_once(PATH_tslib.'class.tslib_pibase.php');
class tx_browserhooks extends tslib_pibase {

  var $prefixId      = 'tx_browserhooks';   // Same as class name
  var $extKey        = 'browser_hooks'; // The extension key.

  function BR_TemplateElementsTransformedHook($obj) {
t3lib_div::devLog('$obj: ' . print_r($obj,true), 'BR_TemplateElementsTransformedHook', 0);
  }
}
?>