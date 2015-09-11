<?php
$header_text = HCM::__('Add');

$type = $params->get('type');
if( $type !== NULL ){
	$shift = HC_App::model('shift');

	switch( $type ){
		case $shift->_const('TYPE_TIMEOFF'):
			$header_text = sprintf(HCM::_n('Request New Timeoff', 'Request %d New Timeoffs', 1), 1);
			break;

		case $shift->_const('TYPE_SHIFT'):
			$header_text = sprintf(HCM::_n('Add New Shift', 'Add %d New Shifts', 1), 1);
			break;
	}
}

$header = HC_Html_Factory::widget('list')
	->add_attr('class', 'list-unstyled')
	;
$header->add_item( $header_text );

echo HC_Html::page_header(
	HC_Html_Factory::element('h2')
		->add_child( $header )
	);
?>