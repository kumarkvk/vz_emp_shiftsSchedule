<?php
$current_user_id = $this->auth->user()->id;

$list = HC_Html_Factory::widget('list')
	->add_attr('class', 'list-unstyled')
	->add_attr('class', 'list-separated2')
	;

$total_count = 0;

reset( $locations );
foreach( $locations as $location ){
	$entity_id = $location->id ? $location->id : 0;

	$this_shifts = array();
	reset( $shifts );
	foreach( $shifts as $sh ){
		$total_count++;
		if( $sh->location_id != $location->id ){
			continue;
		}
		$this_shifts[] = $sh;
	}

	if( ! $this_shifts ){
		continue;
	}

	$list->add_item( 
		HC_Html_Factory::element('h4')
			->add_child( $location->present_title() )
		);

	$this_state = $state;
	$this_state['wide'] = 1;
	$this_state['location'] = array($entity_id);
	$this_state['form'] = TRUE;

	/* stats view */
	if( $current_user_id ){
		$quickstats_view = HC_Html_Factory::widget('module')
			->set_url( $rootlink . '/quickstats' )
			->pass_arg( array($this_shifts, $this_state) )
			->set_param( 'location', $entity_id )
			->set_show_empty( TRUE )
			->add_attr('class', 'hc-rfr-' . 'loc-' . $entity_id)
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
		->pass_arg( array($this_shifts, $this_state) )
		// ->set_param( 'date', $this_date )
		->set_param( 'location', $entity_id )
		->add_attr('class', 'hc-rfr-' . 'loc-' . $entity_id)
		;
	foreach( $this_state as $k => $v ){
		if( $v OR ($v === 0) ){
			$content->set_param( $k, $v );
		}
	}

	$list->add_item( $content );
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