<?php
$app_conf = HC_App::app_conf();
$calendar_ajax = $app_conf->get('calendar_ajax');
$calendar_ajax = 1;

$control = ( $layout->has_partial('control') ) ? $layout->partial('control') : '';
$filter = ( $layout->has_partial('filter') ) ? $layout->partial('filter') : '';
$list = $layout->partial('list');

if( ! ($is_module OR $is_print) ){
	if( $calendar_ajax ){
		$out = HC_Html_Factory::widget('flatmodal');
		$out->set_content( $list );
		$list = $out->render();
	}

	if( $filter OR $control ){
		$out = HC_Html_Factory::widget('list')
			->add_attr('class', 'list-unstyled')
			;

		if( $filter ){
			$out->add_item( 'filter', $filter );
			$out->add_item_attr( 'filter', 'class', array('text-smaller') );
		}

		if( $control ){
			$out->add_item( $control );
		}

		$out->add_item( $list );

		echo $out->render();
	}
	else {
		echo $list;
	}
}
else {
	if( $is_print ){
		/* DATE */
		$date_nav = HC_Html_Factory::widget('date_nav');
		$date_nav->set_range( $state['range'] );
		$date_nav->set_date( $state['date'] );
		echo HC_Html_Factory::element('p')
			->add_child( $date_nav->render(TRUE) )
			;
	}
	echo $list;
}
?>