<?php
$link = HC_Lib::link( 'list/' . $tab, $state );
$rootlink_this = HC_Lib::link( $rootlink . '/' . $tab, $state );
$rootlink = HC_Lib::link( $rootlink, $state );
$current_user_id = $this->auth->user()->id;

/* FILTER STAFF */
$filter1 = NULL;
if( count($all_staffs) > 1 ){
	$filter1 = HC_Html_Factory::widget('filter');
	$filter1
		->set_btn_size( 'xs' )
		->set_link( $rootlink_this )
		->set_param_name( 'staff' )
		->set_title( HCM::__('Staff') )
		->set_panel('default')
		;
	foreach( $all_staffs as $oid => $obj ){
		$filter1->set_option( $oid, $obj->present_title() );
	}
	if( isset($state['staff']) ){
		$filter1->set_selected( $state['staff'] );
	}
}

/* FILTER LOCATIONS */
$filter2 = NULL;
if( count($all_locations) > 1 ){
	$filter2 = HC_Html_Factory::widget('filter');
	$filter2
		->set_btn_size( 'xs' )
		->set_link( $rootlink_this )
		->set_param_name( 'location' )
		->set_title( HCM::__('Location') )
		->set_panel('default')
		;
	foreach( $all_locations as $oid => $obj ){
		$attr = array(
			'style'	=> 'background-color: ' . $obj->present_color() . ';'
			);
		$filter2->set_option( $oid, $obj->present_title(), $attr );
	}

	if( isset($state['location']) ){
		$filter2->set_selected( $state['location'] );
	}
}

$temp_shift = HC_App::model('shift');

/* CUSTOM FILTER */
$filter3 = NULL;
if( array_key_exists('filter', $fix) && ($fix['filter'] === NULL) ){
	$filter3 = NULL;
}
elseif(1){
// else {
	$fixed_filter = array_key_exists('filter', $fix) ? $fix['filter'] : NULL;

	$filter3 = HC_Html_Factory::widget('filter');
	$filter3
		->set_btn_size( 'xs' )
		->set_allow_multiple( FALSE )
		->set_link( $rootlink_this )
		->set_param_name( 'filter' )
		->set_title( HCM::__('More Filters') )
		->set_panel('default')
		->set_readonly(TRUE)
		;
	$extensions = HC_App::extensions();

	$more_content = $extensions->run('list/filter', 'label');
	foreach( $more_content as $subtab => $subtitle ){
		if( $subtitle ){
			if( $fixed_filter && ($subtab != $fixed_filter) ){
				continue;
			}
			$filter3->set_option(
				$subtab,
				$subtitle
				);
		}
	}

	if( $fixed_filter ){
		$filter3->set_selected( $fixed_filter );
		$filter3->set_fixed( TRUE );
	}
	elseif( isset($state['filter']) ){
		$filter3->set_selected( $state['filter'] );
	}

	if( ! $filter3->options() ){
		$filter3 = NULL;
	}
}

$out = HC_Html_Factory::widget('grid')
	;

$show_filters = array();
if( $filter1 ){
	$show_filters[] = $filter1;
}
if( $filter2 ){
	$show_filters[] = $filter2;
}
/*
if( $filter3 ){
	$show_filters[] = $filter3;
}
*/
if( ! $show_filters ){
	return;
}

if( count($show_filters) == 3 ){
	$grid_col = 4;
}
elseif( count($show_filters) == 2 ){
	$grid_col = 6;
}
elseif( count($show_filters) == 1 ){
	$grid_col = 12;
}

foreach( $show_filters as $f ){
	$out->add_item( $f, $grid_col );
}


$full_out = HC_Html_Factory::widget('list')
	->add_attr('class', 'list-unstyled')
	->add_attr('class', 'list-separated')
	;

$full_out->add_item( $out );

if( $filter3 ){
	$full_out->add_item( $filter3 );
}

echo $full_out->render();
?>