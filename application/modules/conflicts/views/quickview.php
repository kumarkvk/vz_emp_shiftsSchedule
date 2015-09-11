<?php
$out = HC_Html_Factory::widget('list')
	->add_attr('class', array('list-unstyled'))
	->add_attr('class', array('list-separated'))
	;

/* ok */
if( $count_ok ){
	$item = HC_Html_Factory::element('span')
		->add_attr('class', array('alert', 'alert-success'))
		->add_attr('class', array('alert-condensed'))
		// ->add_attr('class', array('display-block'))
		;

	$icon = HC_Html::icon('check-circle');
	$icon->add_attr('class', 'text-success');
	$item->add_child( $icon );
	$title = ($count_fail + $count_ok) > 1 ? $count_ok : HCM::__('OK');
	$item->add_child( $title );

	$item->add_attr('title', HCM::__('OK'));

	if( ! $count_fail ){
		$out->add_item( $item );
	}
}

$t = HC_Lib::time();

/* failed */
if( $count_fail ){
	/* link to detailed conflicts view */
	$item = HC_Html_Factory::widget('titled', 'span')
		->add_attr('class', array('alert', 'alert-danger'))
		->add_attr('class', array('alert-condensed'))
		;

	$icon = HC_Html::icon(HC_App::icon_for('conflict'));
	$icon->add_attr('class', 'text-danger');
	$item->add_child( $icon );
	
	$title = sprintf( HCM::_n('%d Conflict', '%d Conflicts', $count_fail), $count_fail );
	$item->add_child( $title );

	$item
		->add_child( ' ' )
		->add_child(
			HC_Html_Factory::element('b')
				->add_attr('class', 'caret')
			)
		;

	/* red border for wrap */
	if( $wrap ){
		$wrap->add_attr('style', 'border-width: 2px; border-color: red;');
	}

	/* now add detailed view */
	// _print_r( $conflicts );

	$conflict_list = HC_Html_Factory::widget('list')
		->add_attr('class', array('list-unstyled'))
		->add_attr('class', array('list-separated'))
		;

	foreach( $conflicts as $date => $date_conflicts ){
		if( ! $date_conflicts ){
			continue;
		}
		$t->setDateDb( $date );
		$conflict_list->add_item( 
			HC_Html_Factory::element('strong')
				->add_child( $t->formatDateFull() )
			);

		foreach( $date_conflicts as $e ){
			$item_view = HC_Html_Factory::widget('list')
				->add_attr('class', array('list-unstyled'))
				->add_attr('class', array('list-separated'))
				;
			$item_view->add_item( $e->present_type() );
			$item_view->add_item( $e->present_details() );
		}

		$conflict_list->add_item( $item_view );
	}

	$conflicts_view = HC_Html_Factory::widget('collapse')
		->set_title( $item )
		->set_content( $conflict_list )
		// ->set_panel( array('danger', 'condensed') )
		;
}

if( $count_fail ){
	// $out->add_item( $conflict_list );
	$out->add_item( $conflicts_view );
}

if( ! $wrap ){
	echo $out->render();
}
?>