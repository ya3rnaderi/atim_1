<?php

class SampleMaster extends ClinicalAnnotationAppModel
{
    var $name = 'SampleMaster';
    
	var $useTable = 'sample_masters';

    var $belongsTo 
    	= array('Collection' =>
	       array('className'  => 'Collection',
				'conditions' => '',
				'order'      => '',
				'foreignKey' => 'collection_id'));
		
	var $validate = array();

}

?>
