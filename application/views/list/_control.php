<?php
$link = HC_Lib::link( 'list/' . $tab, $state );
$rootlink_this = HC_Lib::link( $rootlink . '/' . $tab, $state );
$rootlink = HC_Lib::link( $rootlink, $state );
$current_user_id = $this->auth->user()->id;

/* DATE */
$date_nav = HC_Html_Factory::widget('date_nav');
$date_nav->set_link( $rootlink_this );
$date_nav->set_range( $state['range'] );
$date_nav->set_date( $state['date'] );
$date_nav->set_submit_to( $link->url(array('customdates' => 1)) );

$enabled_date_options = array('week', 'month');
if( $current_user_id ){
	$enabled_date_options[] = 'custom';
	$enabled_date_options[] = 'upcoming';
	$enabled_date_options[] = 'all';
}
$date_nav->set_enabled( $enabled_date_options );

$temp_shift = HC_App::model('shift');

/* VIEW TYPE */
/* BUTTONS */
$add_btns = $this->render(
	'list/_control_btns',
	array(
		'tab'		=> $tab,
		'rootlink'	=> $rootlink,
		'state'		=> $state,
		)
	);

$view_by_nav = HC_Html_Factory::widget('list')
	->add_attr('class', 'list-inline')
	->add_attr('class', 'list-separated')
	->add_attr('class', 'list-separated-ver')
	;

$view_nav = $this->render(
	'list/_control_view_type',
	array(
		'tab'			=> $tab,
		'enabled_tabs'	=> $enabled_tabs,
		'rootlink'		=> $rootlink,
		'state'			=> $state,
		)
	);
$view_by_nav->add_item( $view_nav );

$by_nav = '';
if( (count($all_locations) > 1) OR (count($all_staffs) > 1) ){
	/* GROUP BY */
	$by_nav = $this->render(
		'list/_control_group_by',
		array(
			'tab'		=> $tab,
			'rootlink'	=> $rootlink,
			'state'		=> $state,
			)
		);
	$view_by_nav->add_item( $by_nav );
}

$out = HC_Html_Factory::widget('grid')
	// ->add_attr('style', 'padding-bottom: 1em; border-bottom: #eee 1px solid;')
	->add_attr('style', 'margin-bottom: 1em;')
	;

$out->add_item( $date_nav,
	6,
	array(
		'style'	=> 'margin-bottom: 0.5em',
		)
	);
$out->add_item( 
	$view_by_nav,
	6,
	array(
		'style'	=> 'margin-bottom: 0.5em',
		)
	);

$out->add_item(
	$add_btns,
	12,
	array(
		'style'	=> 'margin-bottom: 0.5em',
		)
	);
 

echo $out->render();
?>