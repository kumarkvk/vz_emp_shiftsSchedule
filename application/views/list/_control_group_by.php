<?php
if( ! ((count($all_staffs) > 1) OR (count($all_locations) > 1)) ){
	return;
}

$options = array(
	'none'	=> HC_Html_Factory::element('a')
		->add_attr( 'href', $rootlink->url($tab, array('by' => NULL)) )
		->add_child( HC_Html::icon('ellipsis-v') )
/* translators: list output without grouping */
		->add_attr( 'title', HCM::__('No Grouping') )
		,
	);
if( count($all_staffs) > 1 ){
	$options['staff'] = HC_Html_Factory::element('a')
		->add_attr( 'href', $rootlink->url($tab, array('by' => 'staff')) )
		->add_child( HC_Html::icon(HC_App::icon_for('user')) )
		->add_attr( 'title', HCM::__('Group By Staff') )
		;
}
if( count($all_locations) > 1 ){
	$options['location'] = HC_Html_Factory::element('a')
		->add_attr( 'href', $rootlink->url($tab, array('by' => 'location')) )
		->add_child( HC_Html::icon(HC_App::icon_for('location')) )
		->add_attr( 'title', HCM::__('Group By Location') )
		;
}

$state_by = $state['by'] ? $state['by'] : 'none';

$nav = HC_Html_Factory::element('div')
	->add_attr('class', 'btn-group')
	;

foreach( $options as $key => $option ){
	$option
		->add_attr('class', array('btn'))
		->add_attr('class', array('btn-sm'))
		;

	if( $key == $state_by ){
		$option
			->add_attr('class', array('btn-archive'))
			;
	}
	else {
		$option
			->add_attr('class', array('btn-default'))
			;
	}

	$nav->add_child( $option );
}

echo $nav->render();
?>