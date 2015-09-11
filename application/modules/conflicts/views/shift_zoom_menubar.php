<?php
$label = NULL;

if( $entries ){
	$label = array();
	$icon = HC_Html::icon( HC_App::icon_for('conflict') )
		->add_attr('class', 'text-danger')
		;

	$label[] = $icon;
	$label[] = sprintf( HCM::_n('%d Conflict', '%d Conflicts', count($entries)), count($entries) );
	$label = join('', $label);

	$label = HC_Html_Factory::widget('titled', 'span')
		->add_attr('class', array('alert', 'alert-danger'))
		->add_attr('class', array('alert-condensed'))
		->add_attr('class', array('display-block'))
		->add_child( $label )
		;
}

if( $label ){
	$out = HC_Html_Factory::widget('container')
		->add_item( $label )
		;
	echo $out;
}