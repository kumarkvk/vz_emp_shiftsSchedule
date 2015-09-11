<?php
$t = HC_Lib::time();
$current_user_id = $this->auth->user()->id;
$test_shift = HC_App::model('shift');

$is_print = (isset($state['print']) && $state['print']) ? 1 : 0;
$can_add = TRUE;
if( $is_print OR (! $current_user_id) ){
	$can_add = FALSE;
}

$t->setDateDb( $state['date'] );
$t->setStartMonth();
$month_matrix = $t->getMonthMatrix();

$t->setDateDb( $state['date'] );
$dates = $t->getDates( $state['range'] );

$DATE_LABELS = array();
reset( $dates );
foreach( $dates as $date ){
	$t->setDateDb( $date );
	$date_label = HC_Html_Factory::element('h4')
		->add_attr('class', 'text-center')
		->add_child(
			HC_Html_Factory::widget('list')
				->add_attr('class', 'squeeze-in')
				->add_attr('class', 'list-unstyled')
				->add_item( $t->formatWeekdayShort() )
				->add_item(
					HC_Html_Factory::element('small')
						->add_child( $t->formatDate() )
				)
		)
		;
	$DATE_LABELS[ $date ] = $date_label;
}

/* compile the cells content */
$CELLS = array();
$LINKS = array();

reset( $dates );
foreach( $dates as $this_date ){
	$t->setDateDb( $this_date );

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

	$this_state = $state;
	$date_content = NULL;
	$this_state['date'] = $this_date;
	$date_content = HC_Html_Factory::widget('module')
		->set_url( $rootlink . '/day' )
		->pass_arg( array($this_shifts, $this_state) )
		->set_params( $this_state )
		->set_param( 'date', $this_date )
		->add_attr('class', 'hc-rfr-' . 'dat-' . $this_date)
		;

	$CELLS[$this_date] = $date_content;

/* links */
	$LINKS[$this_date] = NULL;
	if( $can_add ){
		$href_details = array(
			'date'	=> $this_date,
			// 'type'		=> $test_shift->_const('TYPE_SHIFT'),
			);
		if( isset($state['staff']) && $state['staff'] && (count($state['staff']) == 1) ){
			$href_details['user'] = $state['staff'][0];
		}
		if( isset($state['location']) && $state['location'] && (count($state['location']) == 1) ){
			$href_details['location'] = $state['location'][0];
		}

		$btns = HC_Html_Factory::element('a')
			->add_attr('href', 
				HC_Lib::link('shifts/add/index')
					->url( $href_details )
					)
			->add_attr('class', 'hc-flatmodal-loader')
			->add_attr('class', 'btn')
			// ->add_attr('class', 'btn-default')
			->add_attr('class', 'btn-archive')
			->add_attr('class', 'display-block')
			->add_attr('style', 'text-align: center;')
			// ->add_attr('class', 'btn-xs')
			->add_attr('class', 'btn-sm')
			->add_child( HCM::__('Add') )
			->add_child( HC_Html::icon('plus') )
			->add_attr('title', HCM::__('Add'))
			;
		$LINKS[$this_date] = $btns;
	}
}

/* stats view */
$quickstats_view = HC_Html_Factory::widget('module')
	->set_url( $rootlink . '/quickstats' )
	->pass_arg( array($shifts, $this_state) )
	->set_show_empty( TRUE )
	->add_attr('class', 'hc-rfr')
	;
foreach( $this_state as $k => $v ){
	if( $v OR ($v === 0) ){
		$quickstats_view->set_param( $k, $v );
	}
}
$QUICKSTATS = $quickstats_view;

/* now display */
$full_out = HC_Html_Factory::widget('list')
	->add_attr('class', 'list-unstyled')
	->add_attr('class', 'list-separated')
	;

$full_out->add_item( $QUICKSTATS );

$out = HC_Html_Factory::widget('table')
	->set_engine('div')
	;

$rid = 0;
foreach( $month_matrix as $week => $week_dates ){
	$cid = 0;
	foreach( $week_dates as $date ){
		$cell_content = NULL;

		if( isset($DATE_LABELS[$date]) ){
			$cell_content = array(
				$DATE_LABELS[$date],
				$CELLS[$date],
				);

			if( $LINKS[$date] ){
				$links = HC_Html_Factory::element('div')
					->add_attr('class', 'hover-visible')
					->add_child($LINKS[$date])
					;
				$cell_content[] = $links;
			}
		}

		$out->set_cell( $rid, $cid,
			$cell_content
			);

		if( $cell_content === NULL ){
			$out->add_cell_attr( $rid, $cid,
				array(
					'class'	=> 'noborder',
					)
				);
		}
		else {
			$out->add_cell_attr( $rid, $cid,
				array(
					'class'	=> array('padded', 'hover-parent'),
					)
				);
		}

		$cid++;
	}
	$rid++;
}

$full_out->add_item( $out );
echo $full_out->render();
// echo $out->render();
?>