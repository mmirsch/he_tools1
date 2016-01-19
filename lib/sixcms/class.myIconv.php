<?php
class myIconv {
  var $tabelle;
  
  public function myIconv() {
		$allEntities = get_html_translation_table(HTML_ENTITIES, ENT_NOQUOTES);
		$specialEntities = get_html_translation_table(HTML_SPECIALCHARS, ENT_NOQUOTES);
		$entities = array_diff($allEntities, $specialEntities);
 		$entities['\''] = '&apos;';
		$entities['‚'] = '&sbquo;';
		$entities['‘'] = '&lsquo;';
		$entities['’'] = '&rsquo;';
		$entities['„'] = '&bdquo;';
		$entities['“'] = '&ldquo;';
		$entities['”'] = '&rdquo;';
		$this->tabelle = $entities;
  }

  function utf2iso($string) {
    return str_replace($this->utf8, $this->cp1252, $string);
	}
	
  function iso2utf($string) {
		return strtr($string, $this->tabelle);
	}
}
?>