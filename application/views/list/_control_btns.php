<?php
$temp_shift = HC_App::model('shift');

/* create buttons */
$create_btns = array();
$create_btns['shift'] = 
	HC_Html_Factory::element('a')
		->add_attr('class', array('btn'))
		// ->add_attr('class', array('btn-success-o'))
		->add_attr('class', array('btn-success'))
		// ->add_attr('class', array('display-block'))
		->add_attr('href', HC_Lib::link('shifts/add/index')->url(array('type' => $temp_shift->_const('TYPE_SHIFT'))) )
		->add_child( HC_Html::icon(HC_App::icon_for('shift')) )
		->add_attr( 'title', sprintf(HCM::_n('Add New Shift', 'Add %d New Shifts', 1), 1) )
	;
$create_btns['timeoff'] = 
	HC_Html_Factory::element('a')
		->add_attr('class', array('btn'))
		->add_attr('class', array('btn-warning-o'))
		// ->add_attr('class', array('btn-warning'))
		// ->add_attr('class', array('display-block'))
		->add_attr('href', HC_Lib::link('shifts/add/index')->url(array('type' => $temp_shift->_const('TYPE_TIMEOFF'))) )
		->add_child( HC_Html::icon(HC_App::icon_for('timeoff')) )
		->add_child( sprintf(HCM::_n('Request New Timeoff', 'Request %d New Timeoffs', 1), 1) )
		->add_attr( 'title', sprintf(HCM::_n('Request New Timeoff', 'Request %d New Timeoffs', 1), 1) )
	;

$btns = HC_Html_Factory::widget('list')
	->add_attr('class', 'list-inline')
	// ->add_attr('class', 'list-unstyled')
	// ->add_attr('class', 'list-separated-hori')
	->add_attr('class', 'list-separated')
	// ->add_attr('class', 'pull-right')
	;

$this_user_id = $this->auth->user()->id;
$acl = HC_App::acl();
$test_shift = HC_App::model('shift');
$test_shift->user_id = $this_user_id;

$add_btns = array();

if( isset($fix) && isset($fix['type']) && $fix['type'] && is_array($fix['type']) && (count($fix['type']) == 1) ){
	switch( $fix['type'][0] ){
		case $test_shift->_const('TYPE_TIMEOFF'):
			$test_shift->type = $fix['type'][0];
			if( $acl->set_object($test_shift)->can('validate') ){
				$add_btns[] = $create_btns['timeoff'];
			}
			break;
	}
}
else {
/*
	$test_shift->type = $test_shift->_const('TYPE_SHIFT');
	if( $acl->set_object($test_shift)->can('validate') ){
		$add_btns[] = $create_btns['shift'];
	}

	$test_shift->type = $test_shift->_const('TYPE_TIMEOFF');
	if( $acl->set_object($test_shift)->can('validate') ){
		$add_btns[] = $create_btns['timeoff'];
	}
*/
}

if( $add_btns ){
/* 
	$btns->add_item( 
		HC_Html::icon('plus')
	);
 */	
	foreach( $add_btns as $btn ){
		$btns->add_item( $btn );
	}
}

$items = $btns->items();
if( count($items) ){
	echo $btns->render();
}
?>