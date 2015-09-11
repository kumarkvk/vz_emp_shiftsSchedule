<?php
$t = HC_Lib::time();
$out = HC_Html_Factory::widget('list')
	->add_attr('class', array('list-unstyled'))
	// ->add_attr('class', array('list-separated'))
	;

$end_date = '';
if( strpos($state['date'], '_') !== FALSE ){
	list( $start_date, $end_date ) = explode('_', $state['date']);
}
else {
	$start_date = $state['date'];
}

$calendar_blank = HC_Html_Factory::widget('calendar');
if( $end_date ){
	$calendar_blank
		->set_date( $start_date )
		->set_end_date( $end_date )
		;
}
else {
	$calendar_blank
		->set_date( $start_date )
		->set_range( $state['range'] )
		;
}

$dates = $calendar_blank->dates();
$is_print = (isset($state['print']) && $state['print']) ? 1 : 0;

$current_user_id = $this->auth->user()->id;
$can_add = TRUE;
if( $is_print OR (! $current_user_id) ){
	$can_add = FALSE;
}

$DISPLAY = array();
$date_labels = array();
foreach( $dates as $date ){
	$t->setDateDb( $date );
	$date_label = HC_Html_Factory::element('h4')
		->add_child(
			HC_Html_Factory::widget('list')
				->add_attr('class', 'squeeze-in')
				->add_attr('class', 'list-unstyled')
				// ->add_attr('class', 'list-inline')
				->add_item( $t->formatWeekdayShort() )
				// ->add_item( '&nbsp;' )
				->add_item(
					HC_Html_Factory::element('small')
						->add_child( $t->formatDate() )
				)
		)
		;
	$date_labels[ $date ] = $date_label;
}

$ROW = array();

$this_state = $state;

/* stats view */
$quickstats_view = HC_Html_Factory::widget('module')
	->set_url( $rootlink . '/quickstats' )
	->pass_arg( array($shifts, $this_state) )
	->set_self_target( TRUE )
	->add_attr('class', 'page-status')
	;
foreach( $this_state as $k => $v ){
	if( $v OR ($v === 0) ){
		$quickstats_view->set_param( $k, $v );
	}
}
$ROW['quickstats'] = $quickstats_view;

reset( $dates );
foreach( $dates as $this_date ){
	$t->setDateDb( $this_date );

	$btns = NULL;
	if( $can_add ){
		$btns = HC_Html_Factory::widget('titled', 'a')
			->add_attr('href', HC_Lib::link('shifts/add/index')->url(array('date' => $this_date)) )
			// ->add_attr('class', 'hc-flatmodal-loader')
			->add_attr('class', 'hc-parent-loader')
			->add_attr('class', 'btn')
			->add_attr('class', 'btn-default')
			->add_attr('class', 'btn-block')
			->add_attr('class', 'btn-xs')
			->add_child( HC_Html::icon('plus') )
			->add_attr('title', HCM::__('Add'))
			;
		// $btns = HC_Html_Factory::element('div')
			// ->add_attr('style', 'float: right;')
			// ->add_child($btns)
			// ;
	}

	$this_shifts = array();
	reset($shifts);
	foreach( $shifts as $sh ){
		if( $sh->date > $this_date ){
			break;
		}
		if( $sh->date < $this_date ){
			continue;
		}
		$this_shifts[] = $sh;
	}

	$date_content = NULL;
	if( $this_shifts ){
		$date_content = HC_Html_Factory::widget('module')
			->set_url( $rootlink . '/day' )
			->pass_arg( array($this_shifts, $this_state) )
			->set_param( 'date', $this_date )
			->set_self_target( TRUE )
			;
	}
	
	if( $btns ){
		$date_content = HC_Html_Factory::widget('list')
			->add_attr('class', 'list-unstyled')
			->add_attr('class', 'list-separated')
			->add_item( $date_content )
			->add_item( $btns )
			;
	}

	$ROW[$this_date] = $date_content;
}

$DISPLAY[] = $ROW;

$calendar = HC_Html_Factory::widget('table')
	->set_engine('div')
	;

$rid = 0;
if( $state['range'] == 'week' ){
	/* add dates labels visible on wide screens only */
	$cid = 0;
	$calendar->set_cell( $rid, $cid, '' );
	$calendar->add_cell_attr( $rid, $cid, 
		array(
			'class'	=> array('cal-cell-title'),
			)
		);
	$cid++;

	$calendar->add_row_attr( $rid, 
		array(
			'class'	=> array('noborder', 'hidden-xs'),
			'style'	=> 'text-align: center;',
			)
		);

	reset( $dates );
	foreach( $dates as $date ){
		$date_view = $date_labels[ $date ];

		$calendar->set_cell( $rid, $cid, $date_view );
		$calendar->add_cell_attr( $rid, $cid, 
			array(
				// 'class'	=> array('cal-cell-title'),
				'class'	=> array('cal-cell-day'),
				'style'	=> 'padding: 0.5em;',
				)
			);
		$cid++;
	}
	$rid++;
}

foreach( $DISPLAY as $ROW ){
	$title = HC_Html_Factory::widget('list')
		->add_attr('class', 'list-unstyled')
		->add_attr('class', 'list-separated')
		;

	$cid = 1;
	foreach( $ROW as $kr => $kv ){
		switch( $kr ){
			case 'staff_title':
			case 'quickstats':
				$title->add_item( $kv );
				break;

			case 'calendar':
				break;

			default:
				$date_view = $date_labels[ $date ];
				$date_view = HC_Html_Factory::element('div')
					->add_attr('class', 'visible-xs')
					->add_child( $date_view )
					;

				$kv = array( $date_view, $kv );
				
				$calendar->set_cell( $rid, $cid, $kv );
				$calendar->add_cell_attr( $rid, $cid, 
					array(
						'class'	=> array('cal-cell-day'),
						'style'	=> 'padding: 0.5em;',
						)
					);
				$cid++;
				break;
		}
		$calendar->set_cell( $rid, 0, $title );
		$calendar->add_cell_attr( $rid, 0, 
			array(
				'class'	=> array('cal-cell-title'),
				)
			);
	}

	// $calendar->set_title( $title, $rid );
	$rid++;
}

echo $calendar->render();
return;
?>

<?php
$t = HC_Lib::time();

$end_date = '';
if( strpos($state['date'], '_') !== FALSE ){
	list( $start_date, $end_date ) = explode('_', $state['date']);
}
else {
	$start_date = $state['date'];
}

$calendar = HC_Html_Factory::widget('calendar');
if( $end_date ){
	$calendar
		->set_date( $start_date )
		->set_end_date( $end_date )
		;
}
else {
	$calendar
		->set_date( $start_date )
		->set_range( $state['range'] )
		;
}

$wide = 0;
if( $state['range'] == 'week' ){
	$wide = 1;
}
$calendar->set_wide_slot( $wide );

$dates = $calendar->dates();
reset( $dates );

$is_print = (isset($state['print']) && $state['print']) ? 1 : 0;

$current_user_id = $this->auth->user()->id;
$can_add = TRUE;
if( $is_print OR (! $current_user_id) ){
	$can_add = FALSE;
}

foreach( $dates as $this_date ){
	$t->setDateDb( $this_date );

	$btns = NULL;
	if( $can_add ){
		$btns = HC_Html_Factory::element('a')
			->add_attr('href', HC_Lib::link('shifts/add/index')->url(array('date' => $this_date)) )
			// ->add_attr('class', 'hc-flatmodal-loader')
			->add_attr('class', 'hc-parent-loader')
			->add_attr('class', 'btn')
			->add_attr('class', 'btn-default')
			->add_attr('class', 'btn-xs')
			->add_child( HC_Html::icon('plus') )
			->add_attr('title', HCM::__('Add'))
			;
	}

	$date_label = HC_Html_Factory::element('h4')
		->add_child(
			HC_Html_Factory::widget('list')
				->add_attr('class', 'squeeze-in')
				->add_attr('class', 'list-unstyled')
				->add_attr('class', 'list-inline')
				->add_item( $t->formatWeekdayShort() )
				->add_item( '&nbsp;' )
				->add_item(
					HC_Html_Factory::element('small')
						->add_child( $t->formatDate() )
				)
		)
		;

	if( $btns ){
		$date_label = HC_Html_Factory::widget('grid')
			->set_slim(TRUE)
			->add_item( $date_label, 9 )
			->add_item( $btns, 3, array('style' => 'text-align: right;') )
			;
	}

	$this_shifts = array();
	foreach( $shifts as $sh ){
		if( $sh->date > $this_date ){
			break;
		}
		if( $sh->date < $this_date ){
			continue;
		}
		$this_shifts[] = $sh;
	}

	$this_state = $state;
	$this_state['wide'] = $wide;
	$date_content = HC_Html_Factory::widget('module')
		->set_url( $rootlink . '/day' )
		->pass_arg( array($this_shifts, $this_state) )
		->set_self_target( TRUE )
		;
	foreach( $state as $k => $v ){
		if( $v OR ($v === 0) ){
			$date_content->set_param( $k, $v );
		}
	}

	$date_content->set_param( 'date', $this_date );
	$date_content->set_param( 'wide', $wide );

	$calendar->set_date_content(
		$this_date,
		array(
			$date_label,
			$date_content
			)
		);
}

if( $dates ){
	$out = HC_Html_Factory::widget('list')
		->add_attr('class', 'list-unstyled')
		->add_attr('class', 'list-separated')
		;

/* stats view */
	$quickstats_view = HC_Html_Factory::widget('module')
		->set_url( $rootlink . '/quickstats' )
		->pass_arg( array($shifts, $state) )
		->set_self_target( TRUE )
		->add_attr('class', 'page-status')
		;
	foreach( $state as $k => $v ){
		if( $v OR ($v === 0) ){
			$quickstats_view->set_param( $k, $v );
		}
	}

	$out->add_item( $quickstats_view );
	$out->add_item( $calendar );
	echo $out->render();
}
else {
	echo HCM::__('Nothing');
}
?>