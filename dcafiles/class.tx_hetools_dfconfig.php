<?php
/**
 * This class provides a set of configs for DynaFlex. It contains a complete DCA that configures
 * how an existing flexform is modified by DynaFlex.
 * It also contains a simple example for a hook, that modfies the DCA.
 */
class tx_hetools_dfconfig	{
	var $rowChecks = array (
			'list_type' => 'he_tools_pi2',
			'CType' => 'list',
	);
	
	var $DCA = array (
			0 => array (
			// set the basic path! This is the field inside the TCA where everything is performed in
					'path' => 'tt_content/columns/flexdata/config/ds/default',
	
					// modifications
					'modifications' => array (
							array (
									'method' => 'add',							// add something
									'path' => 'ROOT/el',						// on root level
									'type' => 'field',							// with type "field"
									'field_config' => array (					// a field that has the the config
											'name' => 'dummy',					// it has the name "df_field_0"
											'label' => 'dummy',		// and is labeled with this
											'config' => array (
													'type' => 'input'					// it has the TYPO3 fieldtype "input"
											)
									)
							),
	
							array (
									'method' => 'add',
									'type' => 'sheet',
									'name' => 'allgemein',
									'label' => 'Allgemein',
							),
	
							// moving all the stuff we created before to sheet 0, but only if it was created
							array (
									'method' => 'move',
									'source' => 'allgemeines',					// what should be copied
									'dest' => 'sheets/allgemein/ROOT/el'		// where should it be copied to
							),
					)
					
			)
	);
	
	var $cleanUpField = 'flexdata';

	var $hooks = array(
		'EXT:he_tools/dcafiles/class.tx_hetools_dcahooks.php:tx_hetools_dcahooks'
	);

}
?>
