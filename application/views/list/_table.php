<?php
$current_user_id = $this->auth->user()->id;

$list = HC_Html_Factory::widget('list')
	->add_attr('class', 'list-unstyled')
	->add_attr('class', 'list-separated2')
	;

$total_count = 0;

$this_state = $state;
$this_state['wide'] = 1;
$this_state['form'] = TRUE;

/* stats view */
if( $current_user_id ){
	$quickstats_view = HC_Html_Factory::widget('module')
		->set_url( $rootlink . '/quickstats' )
		->pass_arg( array($shifts, $this_state) )
		->set_show_empty( TRUE )
		->add_attr('class', 'hc-rfr')
		;
	foreach( $this_state as $k => $v ){
		if( $v OR ($v === 0) ){
			$quickstats_view->set_param( $k, $v );
		}
	}
	$list->add_item( $quickstats_view );
}

$content = HC_Html_Factory::widget('module')
	->set_url( $rootlink . '/listing' )
	->pass_arg( array($shifts, $this_state) )
	// ->set_param( 'date', $this_date )
	->add_attr('class', 'hc-rfr')
	;
foreach( $this_state as $k => $v ){
	if( $v OR ($v === 0) ){
		$content->set_param( $k, $v );
	}
}

$total_count = count($shifts);
$list->add_item( $content );

if( ! $total_count ){
	$list->add_item( HCM::__('Nothing') );
}

if( $form ){
	$display = HC_Html_Factory::widget('module')
		->set_url('shift_groups/form')
		->pass_arg( 'content' )
		->pass_arg( $list )
		->pass_param('count', $total_count)
		->set_self_target( FALSE )
		;
}
else {
	$display = $list->render();
}

echo $display;
return;
?>

<?php
$list = HC_Html_Factory::widget('list')
	->add_attr('class', 'list-unstyled')
	->add_attr('class', 'list-separated2')
	;

$total_count = 0;
$shift_view = HC_Html_Factory::widget('shift_view');

$iknow = array();
// $iknow[] = 'date';

if(
	( isset($state['staff']) && (count($state['staff']) == 1) ) OR
	( count($staffs) == 1 ) OR
	( count($all_staffs) == 1 )
	){
	$iknow[] = 'user';
}

if( 
	( isset($state['location']) && (count($state['location']) == 1) ) OR
	( count($locations) == 1 ) OR
	( count($all_locations) == 1 )
	){
	$iknow[] = 'location';
}

/* stats view */
if( $shifts ){
	$quickstats_view = HC_Html_Factory::widget('module')
		->set_url( $rootlink . '/quickstats' )
		->pass_arg( array($shifts, $state) )
		->set_self_target( TRUE )
		->set_show_empty( TRUE )
		->add_attr('class', 'hc-rfr')
		;
	foreach( $state as $k => $v ){
		if( $v OR ($v === 0) ){
			$quickstats_view->set_param( $k, $v );
		}
	}
	$list->add_item( $quickstats_view );
}

$shift_view->set_iknow( $iknow );

foreach( $shifts as $shift ){
	$shift_view->set_shift( $shift );

	if( $form ){
		$this_view = HC_Html_Factory::widget('list')
			->add_attr('class', array('list-table'))
			;

		$this_view
			->add_item(
				'checkbox',
				$form->input('id')
					->add_option( $shift->id )
					->render_one( $shift->id, FALSE )
				)
			;

		$this_view->add_item(
			'shift_view',
			$shift_view->render()
			);

		$this_view->add_item_attr('shift_view',	'style',	'width: 100%;');
		$this_view->add_item_attr('checkbox',	'style',	'padding-right: 0.5em;');
	}
	else {
		$this_view = $shift_view->render();
	}

	$list->add_item( $this_view );
	$total_count++;
	continue;
}

if( ! $total_count ){
	$list->add_item( HCM::__('Nothing') );
}

if( $form ){
	$display = HC_Html_Factory::widget('module')
		->set_url('shift_groups/form')
		->pass_arg( 'content' )
		->pass_arg( $list )
		->pass_param('count', $total_count)
		->set_self_target( FALSE )
		;
}
else {
	$display = $list->render();
}
echo $display;
?>