<?php

require_once(t3lib_extMgm::extPath('solr').'interfaces/interface.tx_solr_responseprocessor.php');

class tx_he_tools_solr_hooks implements tx_solr_ResponseProcessor  {

  public function processResponse(tx_solr_Query $query, Apache_Solr_Response $response) {
t3lib_div::devLog('processResponse: ' . print_r($response,true), 'tx_he_tools_solr_hooks', 0);
  }

  public function modifyResultSet($resultCommand, array $resultSet) {
t3lib_div::devLog('modifyResultSet: ' . print_r($resultSet,true), 'tx_he_tools_solr_hooks', 0);
  	return $resultSet;
  }
  
}
?>
 