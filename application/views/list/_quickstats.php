<?php
$list = HC_Html_Factory::widget('list')
	->add_attr('class', 'list-inline')
	->add_attr('class', 'list-separated')
	;

$detailed = FALSE;

$count = array(
	'shift_active'		=> 0,
	'shift_draft'		=> 0,
	'timeoff_active'	=> 0,
	'timeoff_draft'		=> 0,
	);
$duration = array(
	'shift_active'		=> 0,
	'shift_draft'		=> 0,
	'timeoff_active'	=> 0,
	'timeoff_draft'		=> 0,
	);

foreach( $shifts as $sh ){
	$type = '';
	switch( $sh->type ){
		case $sh->_const('TYPE_SHIFT'):
			switch( $sh->status ){
				case $sh->_const('STATUS_ACTIVE'):
					$type = 'shift_active';
					break;
				case $sh->_const('STATUS_DRAFT'):
					$type = 'shift_draft';
					break;
			}
			break;

		case $sh->_const('TYPE_TIMEOFF'):
			switch( $sh->status ){
				case $sh->_const('STATUS_ACTIVE'):
					$type = 'timeoff_active';
					break;
				case $sh->_const('STATUS_DRAFT'):
					$type = 'timeoff_draft';
					break;
			}
			break;
	}

	if( $type ){
		$count[$type]++;
		$duration[$type] += $sh->get_duration();
	}
}

$t = HC_Lib::time();
$shift = HC_App::model('shift');

if( $detailed ){
	if( $count['shift_active'] ){
		$title_title = $shift->set('type', $shift->_const('TYPE_SHIFT'))->set('status', $shift->_const('STATUS_ACTIVE'))->present_status(HC_PRESENTER::VIEW_RAW);
		$title_class = $shift->set('type', $shift->_const('TYPE_SHIFT'))->set('status', $shift->_const('STATUS_ACTIVE'))->present_status_class();

		$item = HC_Html_Factory::element('span')
			->add_attr('class', 'display-block')
			->add_attr('class', 'alert')
			->add_attr('class', 'alert-condensed2')
			->add_attr('class', 'text-smaller2')
			->add_attr('title', $title_title )

			->add_child( HC_Html::icon(HC_App::icon_for('shift')) )
			->add_child( $count['shift_active'] )
			->add_child( ' ' )
			->add_child( HC_Html::icon(HC_App::icon_for('time')) )
			->add_child( $t->formatPeriod($duration['shift_active'], 'hour') )
			;
		foreach( $title_class as $tc ){
			$item->add_attr( 'class', 'alert-' . $tc );
		}
		$list->add_item( $item );
	}

	if( $count['shift_draft'] ){
		$title_title = $shift->set('type', $shift->_const('TYPE_SHIFT'))->set('status', $shift->_const('STATUS_DRAFT'))->present_status(HC_PRESENTER::VIEW_RAW);
		$title_class = $shift->set('type', $shift->_const('TYPE_SHIFT'))->set('status', $shift->_const('STATUS_DRAFT'))->present_status_class();

		$item = HC_Html_Factory::element('span')
			->add_attr('class', 'display-block')
			->add_attr('class', 'alert')
			->add_attr('class', 'alert-condensed2')
			->add_attr('class', 'text-smaller2')
			->add_attr('title', $title_title )

			->add_child( HC_Html::icon(HC_App::icon_for('shift')) )
			->add_child( $count['shift_draft'] )
			->add_child( ' ' )
			->add_child( HC_Html::icon(HC_App::icon_for('time')) )
			->add_child( $t->formatPeriod($duration['shift_draft'], 'hour') )
			;
		foreach( $title_class as $tc ){
			$item->add_attr( 'class', 'alert-' . $tc );
		}
		$list->add_item( $item );
	}

	if( $count['timeoff_active'] ){
		$title_title = $shift->set('type', $shift->_const('TYPE_TIMEOFF'))->set('status', $shift->_const('STATUS_ACTIVE'))->present_status(HC_PRESENTER::VIEW_RAW);
		$title_class = $shift->set('type', $shift->_const('TYPE_TIMEOFF'))->set('status', $shift->_const('STATUS_ACTIVE'))->present_status_class();

		$item = HC_Html_Factory::element('span')
			->add_attr('class', 'display-block')
			->add_attr('class', 'alert')
			->add_attr('class', 'alert-condensed2')
			->add_attr('class', 'text-smaller2')
			->add_attr('title', $title_title )

			->add_child( HC_Html::icon(HC_App::icon_for('timeoff')) )
			->add_child( $count['timeoff_active'] )
			->add_child( ' ' )
			->add_child( HC_Html::icon(HC_App::icon_for('time')) )
			->add_child( $t->formatPeriod($duration['timeoff_active'], 'hour') )
			;
		foreach( $title_class as $tc ){
			$item->add_attr( 'class', 'alert-' . $tc );
		}
		$list->add_item( $item );
	}

	if( $count['timeoff_draft'] ){
		$title_title = $shift->set('type', $shift->_const('TYPE_TIMEOFF'))->set('status', $shift->_const('STATUS_DRAFT'))->present_status(HC_PRESENTER::VIEW_RAW);
		$title_class = $shift->set('type', $shift->_const('TYPE_TIMEOFF'))->set('status', $shift->_const('STATUS_DRAFT'))->present_status_class();

		$item = HC_Html_Factory::element('span')
			->add_attr('class', 'display-block')
			->add_attr('class', 'alert')
			->add_attr('class', 'alert-condensed2')
			->add_attr('class', 'text-smaller2')
			->add_attr('title', $title_title )

			->add_child( HC_Html::icon(HC_App::icon_for('timeoff')) )
			->add_child( $count['timeoff_draft'] )
			->add_child( ' ' )
			->add_child( HC_Html::icon(HC_App::icon_for('time')) )
			->add_child( $t->formatPeriod($duration['timeoff_draft'], 'hour') )
			;
		foreach( $title_class as $tc ){
			$item->add_attr( 'class', 'alert-' . $tc );
		}
		$list->add_item( $item );
	}
}
else {
	if( ($count['shift_active'] + $count['shift_draft']) ){
		$title_title = $shift->set('type', $shift->_const('TYPE_SHIFT'))->set('status', $shift->_const('STATUS_ACTIVE'))->present_status(HC_PRESENTER::VIEW_RAW);
		$title_class = $shift->set('type', $shift->_const('TYPE_SHIFT'))->set('status', $shift->_const('STATUS_ACTIVE'))->present_status_class();

		$item = HC_Html_Factory::element('span')
			->add_attr('class', 'display-block')
			->add_attr('class', 'alert')
			->add_attr('class', 'alert-condensed2')
			->add_attr('class', 'text-smaller2')
			->add_attr('title', $title_title )

			// ->add_child( HC_Html::icon(HC_App::icon_for('shift')) )
			// ->add_child( $count['shift_active'] )
			// ->add_child( ' ' )
			// ->add_child( HC_Html::icon(HC_App::icon_for('time')) )
			->add_child( $t->formatPeriodExtraShort( ($duration['shift_active']+$duration['shift_draft']), 'hour') )
			;
		foreach( $title_class as $tc ){
			$item->add_attr( 'class', 'alert-' . $tc );
		}
		$list->add_item( $item );
	}
}

/* extensions */
$extensions = HC_App::extensions();
$more_content = $extensions->run('list/quickstats', $shifts, $list);
$sublist = HC_Html_Factory::widget('list')
	->add_attr('class', 'list-inline')
	->add_attr('class', 'list-separated')
	;

foreach( $more_content as $subtab => $subcontent ){
	if( $subcontent ){
		$sublist->add_item( $subcontent );
	}
}
if( $sublist->items() ){
	$list->add_item( $sublist );
}

if( $list->items() ){
	echo $list->render();
}