<?php
$t = HC_Lib::time();
$current_user_id = $this->auth->user()->id;

$is_print = (isset($state['print']) && $state['print']) ? 1 : 0;
$can_add = TRUE;
if( $is_print OR (! $current_user_id) ){
	$can_add = FALSE;
}

$t->setDateDb( $state['date'] );
$dates = $t->getDates( $state['range'] );

/* date labels */
$week_starts_on = $t->weekStartsOn();
$week_border_days = array();

$DATE_LABELS = array();
reset( $dates );
foreach( $dates as $date ){
	$t->setDateDb( $date );
	if( $t->getWeekDay() == $week_starts_on ){
		$week_border_days[] = $date;
	}

	$date_label = HC_Html_Factory::element('div')
		->add_attr('class', 'text-center')
		->add_attr('class', 'text-smaller')
		->add_child(
			HC_Html_Factory::widget('list')
				->add_attr('class', 'squeeze-in')
				->add_attr('class', 'list-unstyled')
				->add_item( 
					HC_Html_Factory::element('small')
						->add_child( $t->formatWeekdayShort() )
					)
				->add_item(
					$t->getDayShort()
				)
		)
		;
	$DATE_LABELS[ $date ] = $date_label;
}

/* titles */
$TITLES = array();
reset( $staffs );
foreach( $staffs as $staff ){
	if( $staff->id ){
		$entity_title = $staff->present_title();
	}
	else {
		$entity_title = HCM::__('Open Shifts');
	}
	$TITLES[$staff->id] = $entity_title;
}

/* compile the cells content */
$CELLS = array();
$QUICKSTATS = array();
$LINKS = array();

$has_shifts = array();
reset( $staffs );
foreach( $staffs as $staff ){
	$entity_id = $staff->id;

	$entity_shifts = array();
	reset($shifts);
	foreach( $shifts as $sh ){
		if( $sh->user_id != $entity_id ){
			continue;
		}
		$has_shifts[ $entity_id ] = 1;
		$entity_shifts[] = $sh;
	}

	$this_state = $state;
	$this_state['staff'] = array($entity_id);
	$this_state['wide'] = 'mini';

	/* stats view */
	$quickstats_view = HC_Html_Factory::widget('module')
		->set_url( $rootlink . '/quickstats' )
		->pass_arg( array($entity_shifts, $this_state) )
		->set_params( $this_state )
		->set_param( 'staff', $entity_id )
		->set_self_target( TRUE )
		->set_show_empty( TRUE )
		->add_attr('class', 'hc-rfr-' . 'use-' . $entity_id)
		;
	foreach( $this_state as $k => $v ){
		if( $v OR ($v === 0) ){
			$quickstats_view->set_param( $k, $v );
		}
	}
	$QUICKSTATS[$entity_id] = $quickstats_view;

	reset( $dates );
	foreach( $dates as $this_date ){
		$t->setDateDb( $this_date );

		$this_shifts = array();
		reset($entity_shifts);
		foreach( $entity_shifts as $sh ){
			if( $sh->date > $this_date ){
				break;
			}
			if( $sh->date < $this_date ){
				continue;
			}
			$this_shifts[] = $sh;
		}

		$date_content = NULL;
		$this_state['date'] = $this_date;

		$date_content = HC_Html_Factory::widget('module')
			->set_url( $rootlink . '/day' )
			->pass_arg( array($this_shifts, $this_state) )
			->set_param( 'date', $this_date )
			->set_param( 'staff', $entity_id )
			->set_param( 'wide', 'mini' )
			->set_self_target( TRUE )
			->add_attr('class', 'hc-rfr-' . 'dat-' . $this_date . '-use-' . $entity_id)
			;

		$cell_key = $entity_id . '_' . $this_date;
		$CELLS[$cell_key] = $date_content;

	/* links */
		$LINKS[$cell_key] = NULL;
		if( $can_add ){
			$btns = HC_Html_Factory::element('a')
				->add_attr('href', 
					HC_Lib::link('shifts/add/index')
						->url(
							array(
								'date' => $this_date,
								'user' => $entity_id,
								)
							)
						)
				->add_attr('class', 'hc-flatmodal-loader')
				->add_attr('class', 'btn')
				->add_attr('class', 'btn-xs')
				->add_attr('class', 'btn-archive')
				->add_attr('class', 'display-block')
				->add_attr('style', 'text-align: center;')
				->add_attr('style', 'padding: 0 0;')
				->add_child( HC_Html::icon('plus') )
				->add_attr('title', HCM::__('Add'))
				;
			$LINKS[$cell_key] = $btns;
		}
	}
}

/* now display */
$out = HC_Html_Factory::widget('table')
	->set_engine('div')
	;

/* dates row */
$rid = 0;
$cid = 0;
$out->set_cell( $rid, $cid,
	''
	);
$out->add_cell_attr( $rid, $cid, 
	array(
		'class'	=> array('cal-cell-title'),
		)
	);

$cid++;
reset( $dates );
foreach( $dates as $date ){
	$out->set_cell( $rid, $cid,
		$DATE_LABELS[$date]
		);
	$out->add_cell_attr( $rid, $cid, 
		array(
			'class'	=> array('cal-cell-mday-short'),
			)
		);
	if( in_array($date, $week_border_days) ){
		$out->add_cell_attr( $rid, $cid, 
			array(
				'class'	=> array('hc-div-table-cell-start-week'),
				)
			);
	}
	$cid++;
}
$out->add_row_attr( $rid, 
	array(
		'class'	=> array('hidden-xs'),
		)
	);

$rid++;

/* content */
$cid = 0;

$out->set_cell( $rid, $cid,
	''
	);
$out->add_cell_attr( $rid, $cid, 
	array(
		'class'	=> array('cal-cell-title'),
		)
	);

reset( $staffs );
foreach( $staffs as $staff ){
	$entity_id = $staff->id;

	if( ! $current_user_id ){
		if( ! ( isset($has_shifts[$entity_id]) && $has_shifts[$entity_id] ) ){
			continue;
		}
	}

	$cid = 0;
	if( $current_user_id ){
		$title = HC_Html_Factory::widget('list')
			->add_attr('class', 'list-unstyled')
			->add_attr('class', 'list-separated')
			;
		$title->add_item( $TITLES[$entity_id] );
		$title->add_item( $QUICKSTATS[$entity_id] );
	}
	else {
		$title = $TITLES[$entity_id];
	}

	$out->set_cell( $rid, $cid,
		$title
		);
	$out->add_cell_attr( $rid, $cid, 
		array(
			'class'	=> 'padded',
			)
		);

	$cid++;
	reset( $dates );
	foreach( $dates as $date ){
		$cell_key = $entity_id . '_' . $date;

		$date_label = $DATE_LABELS[$date];
		$date_label = HC_Html_Factory::element('div')
			->add_attr('class', 'visible-xs')
			->add_child( $date_label )
			;

		$cell_content = array(
			$date_label,
			$CELLS[$cell_key],
			);

		if( $LINKS[$cell_key] ){
			$links = HC_Html_Factory::element('div')
				->add_attr('class', 'hover-visible')
				->add_child($LINKS[$cell_key])
				;
			$cell_content[] = $links;
		}

		$out->set_cell( $rid, $cid,
			$cell_content
			);
		$out->add_cell_attr( $rid, $cid, 
			array(
				'class'	=> array('padded', 'hover-parent'),
				)
			);
		if( in_array($date, $week_border_days) ){
			$out->add_cell_attr( $rid, $cid, 
				array(
					'class'	=> array('hc-div-table-cell-start-week'),
					)
				);
		}
		$cid++;
	}
	$rid++;
}

echo $out->render();
?>