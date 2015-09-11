<?php
$t = HC_Lib::time();
$shift_view = HC_Html_Factory::widget('shift_view');
$shift_view->set_wide( $state['wide'] );

if( array_key_exists('form', $state) ){
}
else {
	$form = NULL;
}

$iknow = array();
if( isset($state['range']) && ($state['range'] == 'day') ){
	$iknow[] = 'date';
}

if(
	( ! isset($state['staff']) ) OR 
	( count($state['staff']) == 1 ) OR
	( count($staffs) == 1 ) OR
	( count($all_staffs) == 1 )
	){
	$iknow[] = 'user';
}

if( 
	// ( ! isset($state['location']) ) OR 
	( isset($state['location']) && (count($state['location']) == 1) ) OR
	( count($locations) == 1 ) OR
	( count($all_locations) == 1 )
	){
	$iknow[] = 'location';
}

$fold_by = 'time';
if( in_array('user', $iknow) ){
	$fold_by = '';
}
if( isset($state['wide']) && ($state['wide'] === 'mini') ){
	$fold_by = '';
}
if( ! in_array('date', $iknow) ){
	$fold_by = 'date';
}

$folded = array();
$folded_labels = array();

switch( $fold_by ){
	case 'time':
		$iknow[] = 'time';
		break;
	case 'location':
		$iknow[] = 'location';
		break;
	case 'date':
		$iknow[] = 'date';
		break;
}

$shift_view->set_iknow($iknow);

/* folded */
if( $fold_by ){
	foreach( $shifts as $sh ){
		if( (isset($state['location']) && $state['location']) && (! $state['staff']) ){
			if( ! in_array($sh->location_id, $state['location']) ){
				continue;
			}
		}

		$folding_key = array();

		switch( $fold_by ){
			case 'time':
				$folding_key[] = $sh->start;
				$folding_key[] = $sh->end;
				break;
			case 'location':
				$folding_key[] = $sh->location_id;
				break;
			case 'date':
				$folding_key[] = $sh->date;
				break;
		}

		$folding_key = join( '-', $folding_key );
		if( ! isset($folded[$folding_key]) ){
			$folded[$folding_key] = array();
			$folded_labels[$folding_key] = array();

			switch( $fold_by ){
				case 'time':
					$folded_labels[$folding_key][] = $sh->present_time(HC_PRESENTER::VIEW_RAW);
					break;
				case 'location':
					$folded_labels[$folding_key][] = $sh->present_location();
					break;
				case 'date':
					$folded_labels[$folding_key][] = $sh->present_date();
					break;
			}
		}
		$folded[$folding_key][] = $sh;
	}
}

$out = HC_Html_Factory::widget('list')
	->add_attr('class', array('list-unstyled'))
	;

if( $shifts ){
	$out_shifts = HC_Html_Factory::widget('list')
		->add_attr('class', array('list-unstyled'))
		->add_attr('class', array('list-separated'))
		;

	if( $fold_by ){
		foreach( $folded as $fk => $fshifts ){
		// label
			$this_label = $folded_labels[$fk][0];

		// shifts
			$list_shifts = HC_Html_Factory::widget('list')
				// ->add_attr('class', array('nav', 'nav-stacked'))
				->add_attr('class', array('list-unstyled'))
				->add_attr('class', array('list-separated'))
				;
			reset( $fshifts );
			foreach( $fshifts as $sh ){
				$shift_view->set_shift( $sh );

				if( $form ){
					$this_view = HC_Html_Factory::widget('list')
						->add_attr('class', array('list-table'))
						;

					$this_view
						->add_item(
							'checkbox',
							$form->input('id')
								->add_option( $sh->id )
								->render_one( $sh->id, FALSE )
							)
						;

					$this_view->add_item(
						'shift_view',
						$shift_view->render()
						);

					$this_view->add_item_attr('shift_view',	'style',	'width: 100%;');
					$this_view->add_item_attr('checkbox',	'style',	'padding-right: 0.5em;');
				}
				else {
					$this_view = $shift_view->render();
				}

				$list_shifts->add_item(
					// $shift_view->render()
					$this_view
					);
			}

			$folder = HC_Html_Factory::widget('collapse');
			$folder->set_default_in( TRUE );
			$folder->set_indented( FALSE );

			$toggler = HC_Html_Factory::element('a')
				->add_attr('class', 'squeeze-in')
				->add_child( $this_label )
				;

			switch( $fold_by ){
				case 'date':
					$toggler
						->add_attr('class', 'alert')
						->add_attr('class', 'alert-default-o')
						->add_attr('class', 'noborder')
						// ->add_attr('style', 'padding-left: 0;')
						;
					break;
				default:
					$toggler
						->add_attr('class', 'label')
						->add_attr('class', 'label-default')
						;
					break;
			}


			$folder->set_title( $toggler );
			$folder->set_content( $list_shifts );
			$out_shifts->add_item( $folder );
		}
	}
	else{
		foreach( $shifts as $sh ){
			if( (isset($state['location']) && $state['location']) && (! $state['staff']) ){
				if( ! in_array($sh->location_id, $state['location']) ){
					continue;
				}
			}

			$shift_view->set_shift($sh);

			if( $form ){
				$this_view = HC_Html_Factory::widget('list')
					->add_attr('class', array('list-table'))
					;

				$this_view
					->add_item(
						'checkbox',
						$form->input('id')
							->add_option( $sh->id )
							->render_one( $sh->id, FALSE )
						)
					;

				$this_view->add_item(
					'shift_view',
					$shift_view->render()
					);

				$this_view->add_item_attr('shift_view',	'style',	'width: 100%;');
				$this_view->add_item_attr('checkbox',	'style',	'padding-right: 0.5em;');
			}
			else {
				$this_view = $shift_view->render();
			}

			$out_shifts->add_item(
				// $shift_view->render()
				$this_view
				);
		}
	}

	$out->add_item( $out_shifts );
}

echo $out->render();
?>