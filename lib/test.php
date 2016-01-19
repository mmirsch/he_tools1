<?php

require 'SOAP/Client.php';

/**
 * This client runs against the example server in SOAP/example/server.php.  It
 * does not use WSDL to run these requests, but that can be changed easily by
 * simply adding '?wsdl' to the end of the url.
 */
$soapclient = new SOAP_Client('http://www3.hs-esslingen.de/qislsf/services/dbinterface');
$rpcMethod = 'getDataXML';
		$getDataRequest = '<SOAPDataService>
		      <general>
		        <object>modulList</object>
		      </general>
		      <condition>
		      <abschluss>84</abschluss>
		      <studiengang>BSA</studiengang>
		      <version>2</version>
		      </condition>
		   </SOAPDataService>';
		$soapclient->setOpt('curl', CURLOPT_SSL_VERIFYPEER, 0);
		$params = array('arg0' => $getDataRequest);
		
		$returnValue = $soapclient->call($rpcMethod, $params);
		print_r($returnValue);
?>
