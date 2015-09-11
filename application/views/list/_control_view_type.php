<?php
$print_state = $state;
$print_state['print'] = 1;
$all_options = array(
	'calendar'	=> HC_Html_Factory::element('a')
		->add_attr( 'href', $rootlink->url('calendar', $state) )
		->add_child( HC_Html::icon('calendar') )
/* translators: view type, i.e. Calendar View */
		->add_attr( 'title', HCM::__('Calendar') )
		,
	'browse'	=> HC_Html_Factory::element('a')
		->add_attr( 'href', $rootlink->url('browse', $state) )
		->add_child( HC_Html::icon('list') )
/* translators: view type, i.e. List View */
		->add_attr( 'title', HCM::__('List') )
		,
	'print'	=> HC_Html_Factory::element('a')
		->add_attr( 'href', $rootlink->url($tab, $print_state) )
		->add_child( HC_Html::icon('print') )
		->add_attr('target', '_blank')
		->add_attr( 'title', HCM::__('Print') )
		,
	'download'	=> HC_Html_Factory::element('a')
		->add_attr( 'href', $rootlink->url('download', $state) )
		->add_child( HC_Html::icon('download') )
		->add_attr( 'title', HCM::__('Download') )
		,
	);

$enabled_tabs[] = 'print';	
if( 1 OR in_array($tab, array('browse')) ){
	$enabled_tabs[] = 'download';
}

$options = array();
foreach( $all_options as $key => $val ){
	if( ! in_array($key, $enabled_tabs) ){
		continue;
	}
	$options[$key] = $val;
}

if( count($options) <= 1 ){
	return;
}

$nav = HC_Html_Factory::element('div')
	->add_attr('class', 'btn-group')
	->add_attr('class', 'common-link-parent')
	;

foreach( $options as $key => $option ){
	$option
		->add_attr('class', array('btn'))
		->add_attr('class', array('btn-sm'))
		;

	if( $key == $tab ){
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