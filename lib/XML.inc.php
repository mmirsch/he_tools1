<?php
/**
* An XML-RPC implementation by Keith Devens, version 2.5e.
* http://www.keithdevens.com/software/xmlrpc/
* 
* Release history available at:
* http://www.keithdevens.com/software/xmlrpc/history/
* 
* This code is Open Source, released under terms similar to the Artistic License.
* Read the license at http://www.keithdevens.com/software/license/
* 
* Note: this code requires version 4.1.0 or higher of PHP.
*/

/**
* XML class.
*/
class XML {
	var $parser; #a reference to the XML parser
	var $document; #the entire XML structure built up so far
	var $current; #a pointer to the current item - what is this
	var $parent; #a pointer to the current parent - the parent will be an array
	var $parents; #an array of the most recent parent at each level

	var $last_opened_tag;
	var $trennzeichen = '|';

	function XML($data=null){
			$this->parser = xml_parser_create();

			xml_parser_set_option ($this->parser, XML_OPTION_CASE_FOLDING, 0);
			xml_set_object($this->parser, $this);
			xml_set_element_handler($this->parser, "open", "close");
			xml_set_character_data_handler($this->parser, "data");
	}

	function count_numeric_items(&$array){
		return is_array($array) ? count(array_filter(array_keys($array), 'is_numeric')) : 0;
	}
	
	function destruct(){
		xml_parser_free($this->parser);
	}

	function parse(&$data,$attributesNested=FALSE){
		$this->document = array();
		$this->parent = &$this->document;
		$this->parents = array();
		$this->last_opened_tag = NULL;
		xml_parse($this->parser, $data);
		return $this->document;
	}

	function open($parser, $tag, $attributes){
		#echo "Opening tag $tag<br>\n";
		$this->data = "";
		$this->last_opened_tag = $tag; #tag is a string
		if(array_key_exists($tag, $this->parent)){
			#echo "There's already an instance of '$tag' at the current level ($level)<br>\n";
			if(is_array($this->parent[$tag]) and array_key_exists(0, $this->parent[$tag])){ #if the keys are numeric
				#need to make sure they're numeric (account for attributes)
				$key = $this->count_numeric_items($this->parent[$tag]);
				#echo "There are $key instances: the keys are numeric.<br>\n";
			}else{
				#echo "There is only one instance. Shifting everything around<br>\n";
				$temp = &$this->parent[$tag];
				unset($this->parent[$tag]);
				$this->parent[$tag][0] = &$temp;

				if(array_key_exists($tag . $this->trennzeichen . 'attr', $this->parent)){
					#shift the attributes around too if they exist
					$temp = &$this->parent[$tag . $this->trennzeichen . 'attr'];
					unset($this->parent[$tag . $this->trennzeichen . 'attr']);
					$this->parent[$tag]['0' . $this->trennzeichen . 'attr'] = &$temp;
				}
				$key = 1;
			}
			$this->parent = &$this->parent[$tag];
		}else{
			$key = $tag;
		}
		if($attributes){
			$this->parent[$key . $this->trennzeichen . 'attr'] = $attributes;
		}

		$this->parent[$key] = array();
		$this->parent = &$this->parent[$key];
		array_unshift($this->parents, &$this->parent);
	}

	function data($parser, $data){
		#echo "Data is '", htmlspecialchars($data), "'<br>\n";
		if($this->last_opened_tag != NULL){
			$this->data .= $data;
		}
	}

	function close($parser, $tag){
		#echo "Close tag $tag<br>\n";
		if($this->last_opened_tag == $tag){
			$this->parent = $this->data;
			$this->last_opened_tag = NULL;
		}
		array_shift($this->parents);
		$this->parent = &$this->parents[0];
	}
		
}

/* Give typo3 the possibility to replace this class with an extension. */
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_tools/lib/XML.inc.php'])  {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/he_tools/lib/XML.inc.php']);
}
?>