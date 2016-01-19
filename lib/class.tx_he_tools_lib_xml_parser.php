<?php 

class tx_he_tools_lib_xml_parser {
	var $parser;
	var $name;
	var $attr;
	var $data  = array();
	var $stack = array();
	var $keys;
	
	// parse XML data
	function parse(&$xmlContent) 	{
		$data = '';
		$this->parser = xml_parser_create();
		xml_set_object($this->parser, $this);
		xml_set_element_handler($this->parser, 'startXML', 'endXML');
		xml_set_character_data_handler($this->parser, 'charXML');
	
		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false);
	
		$lines = explode("\n",$xmlContent);
		foreach ($lines as $val) {
			if (trim($val) == '') {
				continue;
			}
			$data = $val . "\n";
			if (!xml_parse($this->parser, $data)) {
				$this->error(sprintf('XML error at line %d column %d',
						xml_get_current_line_number($this->parser),
						xml_get_current_column_number($this->parser)));
			}
		}
//		$this->cleanup($this->data);
		return $this->data;
	}
	
	function cleanup(&$data) {
		if (is_array($data) && count($data)>0) {
			foreach($data as $key=>$value) {
				$this->cleanup($value);
				$keySplit = explode('|',$key);
				if (count($keySplit)>1) {
					$keyString = '[' . implode('][',$keySplit) . ']';
					$evalString = '$data' . $keyString . '= $value;';
					eval ($evalString);
					unset($data[$key]);
				}
			}
		}
	}
	
	function startXML($parser, $name, $attr) {
		$this->stack[$name] = array();
		$keys = '';
		$total = count($this->stack)-1;
		$i=0;

		foreach ($this->stack as $key => $val) {
			if (count($this->stack) > 1) {
				if ($total == $i) {
					$keys .= $key;
				} else {
					$keys .= $key . '|'; // The separator
				}
			} else {
				$keys .= $key;
			}
			$i++;
		}
		if (array_key_exists($keys, $this->data)) {
			$this->data[$keys][] = $attr;
		} else {
			$this->data[$keys] = $attr;
		}
		$this->keys = $keys;
	}
	
	function endXML($parser, $name) {
		end($this->stack);
		if (key($this->stack) == $name) {
			array_pop($this->stack);
		}
	}
	
	function charXML($parser, $data) {
		if (trim($data) != '') {
			$this->data[$this->keys]['data'][] = trim(str_replace("\n", '', $data));
		}
	}
	
	function error($msg)    {
		return "<div align=\"center\">
		<font color=\"red\"><b>Error: $msg</b></font>
		</div>";
		exit();
	}	
	
}

?>