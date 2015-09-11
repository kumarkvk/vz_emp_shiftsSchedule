<?php
$link = HC_Lib::link('shifts/add/insert');

/* count how many shifts will be created */
$pa = $params->to_array();
$shifts_count = 1;
$shifts_count *= count($dates);

$check = array('location', 'user');
foreach( $check as $ch ){
	if( is_array($pa[$ch]) )
		$shifts_count *= count($pa[$ch]);
}

$display_form = HC_Html_Factory::widget('form')
	->add_attr('action', $link->url($params->to_array()) )
	// ->add_attr('class', 'form-horizontal')
	->add_attr('class', 'form-condensed')
	->add_attr('class', 'form-small-labels')
	;

$out = HC_Html_Factory::widget('list')
	->add_attr('class', array('list-unstyled', 'list-separated') )
	;

/* STATUS CHECKBOX */
$status_input = NULL;
$statuses = $params->get_options('status');
if( count($statuses) > 1 ){
	$status_input = $form->input('status');
	$status_input
		->set_inline( TRUE )
		;
	foreach( $statuses as $status_option ){
		$status_input->add_option( 
			$status_option,
			$model->set('status', $status_option)->present_status()
			);
	}
}
elseif( count($statuses) == 1 ){
	reset( $statuses );
	$status_input = $model->set('status', key($statuses))->present_status();
}

if( $status_input !== NULL ){
	$out->add_item(
		HC_Html_Factory::widget('label_row')
			->set_label( HCM::__('Status') )
			->set_content(
				$status_input
				)
		);
}

/* ADD NOTE IF POSSIBLE */
$new_shift = HC_App::model('shift');
$extensions = HC_App::extensions();
$more_content = $extensions->run('shifts/add/confirm', $new_shift);
if( $more_content ){
	$more_holder = HC_Html_Factory::widget('list')
		->add_attr('class', 'list-unstyled')
		->add_attr('class', 'list-separated2')
		;

	foreach($more_content as $mc ){
		$more_holder->add_item( $mc );
	}

	$out->add_item( $more_holder );
}

$out->add_divider();

$add_btn_label = HCM::__('Add');
$type = $params->get('type');
if( $type !== NULL ){
	$shift = HC_App::model('shift');

	switch( $type ){
		case $shift->_const('TYPE_TIMEOFF'):
			$add_btn_label = sprintf( HCM::_n('Request New Timeoff', 'Request %d New Timeoffs', $shifts_count), $shifts_count );
			break;

		case $shift->_const('TYPE_SHIFT'):
			$add_btn_label = sprintf( HCM::_n('Add New Shift', 'Add %d New Shifts', $shifts_count), $shifts_count );
			break;
	}
}

$out->add_item(
	HC_Html_Factory::element('input')
		->add_attr('type', 'submit')
		->add_attr('class', array('btn', 'btn-success'))
		->add_attr('title', $add_btn_label )
		->add_attr('value', $add_btn_label )
	);

$display_form->add_item( $out );
echo $display_form->render();
?>