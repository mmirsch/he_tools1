<?php 

class tx_he_tools_echug {

	function umfrage() {
		$get = t3lib_div::_GET();
		$post = t3lib_div::_POST();
		$out = '<h1> Hier kommt eine Umfrage </h1>';
		$out .= '<h3>Get: ' . print_r($get,true) . '</h3>';
		$out .= '<h3>Post: ' . print_r($post,true) . '</h3>';
		return $out;
	}
	
	
	function auswertung() {
		$get = t3lib_div::_GET();
		$post = t3lib_div::_POST();
		$out = '<h1> Hier kommt eine Auswertung </h1>';
		$out .= '<h3>Get: ' . print_r($get,true) . '</h3>';
		$out .= '<h3>Post: ' . print_r($post,true) . '</h3>';
		return $out;
	}
	
}

?>