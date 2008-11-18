<?php

class SampleControl extends InventoryManagementAppModel
{
    var $name = 'SampleControl';
	
	var $useTable = 'sample_controls';
	
	var $hasMany = array('DerivedSampleLink' =>
		array('className'   => 'DerivedSampleLink',
			'conditions'  => '',
			'order'       => '',
			'limit'       => '',
			'foreignKey'  => 'source_sample_control_id',
			'dependent'   => true,
			'exclusive'   => false,
			'finderSql'   => ''));
							
	var $validate = array();
	
}

?>
