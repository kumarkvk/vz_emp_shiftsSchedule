<?php
class Shift_HC_Presenter extends HC_Presenter
{
	function label( $model, $vlevel = HC_PRESENTER::VIEW_HTML )
	{
		$return = '';
		switch( $model->type ){
			case SHIFT_HC_MODEL::TYPE_SHIFT:
				switch( $vlevel ){
					case HC_PRESENTER::VIEW_HTML:
					case HC_PRESENTER::VIEW_HTML_ICON:
						$return = HC_Html::icon(HC_App::icon_for('shift'));
						break;
					case HC_PRESENTER::VIEW_TEXT:
						$return = HCM::__('Shift');
						break;
				}
				break;

			case SHIFT_HC_MODEL::TYPE_TIMEOFF:
				switch( $vlevel ){
					case HC_PRESENTER::VIEW_HTML:
					case HC_PRESENTER::VIEW_HTML_ICON:
						$return = HC_Html::icon(HC_App::icon_for('timeoff'));
						break;
					case HC_PRESENTER::VIEW_TEXT:
						$return = HCM::__('Timeoff');
						break;
				}
				break;
		}
		return $return;
	}

	public function property_name( $model, $pname, $vlevel = HC_PRESENTER::VIEW_HTML )
	{
		$return = parent::property_name( $model, $pname, $vlevel );

		switch( $pname ){
			case 'user':
				$return = $model->user->present_label($vlevel);
				break;
			case 'location':
				$return = $model->location->present_label($vlevel);
				break;
			case 'start':
				$return = HCM::__('Start Time');
				break;
			case 'end':
				$return = HCM::__('End Time');
				break;
			case 'release_request':
				$return = HCM::__('Shift Release');
				break;
		}
		return $return;
	}

	function id( $model, $vlevel = HC_PRESENTER::VIEW_HTML )
	{
		$return = array();
		switch( $vlevel ){
			case HC_PRESENTER::VIEW_HTML:
				$return = 'id:' . $model->id;
				$return = HC_Html_Factory::element('span')
					->add_attr('class', 'text-muted')
					->add_attr('class', 'text-smaller2')
					->add_attr('class', 'squeeze-in')
					->add_child( $return )
					;
				break;
			case HC_PRESENTER::VIEW_TEXT:
				$return = 'id:' . $model->id;
				break;
			case HC_PRESENTER::VIEW_RAW:
				$return = $model->id;
				break;
		}

		return $return;
	}

	function text( $model, $vlevel = HC_PRESENTER::VIEW_HTML, $with_change = FALSE )
	{
		$return = array();
		$return['date'] = $model->present_date($vlevel, $with_change);
		$return['time'] = $model->present_time($vlevel, $with_change);
		if( $model->type == $model->_const('TYPE_SHIFT') ){
			$return['location'] = $model->present_location($vlevel, $with_change);
		}
		$return['user'] = $model->present_user($vlevel, $with_change);
		return $return;
	}

	function status_class( $model )
	{
		$return = array();
		list( $label_text, $label_class ) = $this->_status_details($model);
		if( ! is_array($label_class) )
			$label_class = array( $label_class );
		$return = array_merge( $return, $label_class );
		return $return;
	}

	function type( $model, $vlevel = HC_PRESENTER::VIEW_HTML )
	{
		switch( $model->type ){
			case $model->_const('TYPE_TIMEOFF'):
				$type_text = HCM::__('Timeoff');
				$type_icon = 'timeoff';
				break;
			default:
				$type_text = HCM::__('Shift');
				$type_icon = 'shift';
				break;
		}

		switch( $vlevel ){
			case HC_PRESENTER::VIEW_HTML:
				$label_class = $this->status_class($model);

				$return = HC_Html_Factory::widget('titled', 'span')
					->add_attr('class', array('alert') )
					->add_attr('class', array('alert-condensed') )
					->add_child( HC_Html::icon(HC_App::icon_for($type_icon)) )
					->add_child( $type_text )
					;
				if( ! is_array($label_class) ){
					$label_class = array( $label_class );
				}
				foreach( $label_class as $lc ){
					$return->add_attr('class', 'alert-' . $lc);
				}
				break;
			case HC_PRESENTER::VIEW_HTML_ICON:
				$return = HC_Html::icon( HC_App::icon_for($type_icon) );

				list( $label_text, $label_class ) = $this->_status_details($model);
				// $return->add_attr('class', 'text-' . $label_class);
				$title = array();
				$title[] = $model->present_type(HC_PRESENTER::VIEW_RAW);
				$title[] = $label_text;
				$title = join( ': ', $title );
				$return->add_attr('title', $title);
				break;
			case HC_PRESENTER::VIEW_TEXT:
				$return = HCM::_x('Type', 'noun') . ': ' . $type_text;
				break;
			case HC_PRESENTER::VIEW_RAW:
				$return = $type_text;
				break;
		}

		return $return;
	}

	function status( $model, $vlevel = HC_PRESENTER::VIEW_HTML )
	{
		list( $label_text, $label_class ) = $this->_status_details($model);
		$label_class = $this->status_class($model, TRUE);
		$type_text = $model->present_type( HC_PRESENTER::VIEW_RAW );

		switch( $vlevel ){
			case HC_PRESENTER::VIEW_HTML:
			case HC_PRESENTER::VIEW_HTML_ICON:
				// $return = HC_Html::label( $label_class, $label_text );
				// $type_label = $model->present_label( $vlevel );
				// $label_text = $type_label . $label_text;
				$label_text_text = $model->present_status( HC_PRESENTER::VIEW_RAW );

				$return = HC_Html_Factory::widget('titled', 'span')
					->add_attr('class', array('alert') )
					->add_attr('class', array('alert-condensed') )

					->add_attr('title', $label_text_text)
					->add_child( $label_text )
					;
				if( ! is_array($label_class) ){
					$label_class = array( $label_class );
				}
				foreach( $label_class as $lc ){
					// $return->add_attr('class', 'alert-' . $lc);
					// $return->add_attr('class', 'alert-' . $lc);
				}
				// $return->add_attr('class', 'alert-default-o');

				// $color = $model->location->present_color();
				$type = $model->type; 
				switch( $type ){
					case $model->_const('TYPE_TIMEOFF'):
						$color = '#ddd';
						break;
					default:
						$color = '#dff0d8';
						break;
				}

				if( $model->status == $model->_const('STATUS_DRAFT') ){
					$color1 = HC_Lib::adjust_color_brightness( $color, 0 );
					$color2 = HC_Lib::adjust_color_brightness( $color, 20 );

					$return->add_attr('style',
						"background: repeating-linear-gradient(
							-45deg,
							$color1,
							$color1 6px,
							$color2 6px,
							$color2 12px
							);
						"
						);
				}
				else { 
					$return->add_attr('style', 'background-color: ' . $color . ';');
					// $wrap->add_attr('class', 'alert-success');
				}

				break;
			case HC_PRESENTER::VIEW_TEXT:
				$return = join(': ', array(HCM::__('Status'), $type_text, $label_text));
				break;
			case HC_PRESENTER::VIEW_RAW:
				$type_label = $model->present_label( $vlevel );
				$return = join(': ', array($type_text, $label_text));
				break;
		}

		return $return;
	}

	private function _status_details( $model, $skip_type = TRUE )
	{
		$return = NULL;
		$status = $model->status;
		$type = $model->type; 

		switch( $type ){
			case $model->_const('TYPE_TIMEOFF'):
				$details = array(
					SHIFT_HC_MODEL::STATUS_ACTIVE 	=> array( HCM::__('Active'),	'warning' ),
					SHIFT_HC_MODEL::STATUS_DRAFT	=> array( HCM::__('Pending'),	array('warning-o', 'border-dotted') ),
					);
				break;
			default:
				$details = array(
					SHIFT_HC_MODEL::STATUS_ACTIVE 	=> array( HCM::__('Active'),	'success' ),
					// SHIFT_HC_MODEL::STATUS_DRAFT	=> array( HCM::__('Draft'),		array('success-o', 'border-dotted') ),
					SHIFT_HC_MODEL::STATUS_DRAFT	=> array( HCM::__('Draft'),		array('success-o') ),
					);
				break;
		}

		if( isset($details[$status]) ){
			$return = $details[$status];
		}

		return $return;
	}

	public function conflict_class( $model )
	{
		if( $model->is_published() ){
			$return = array('danger-o');
			$return = array('danger-m');
			// $return = array('danger', 'danger-o');
		}
		else {
			// $return = array('danger-m');
			$return = array();
			$return = array('danger-m');
		}
		return $return;
	}

	function status_text( $model )
	{
		$return = '';
		$details = $this->_status_details( $model );
		if( $details ){
			$return = $details[0];
		}
		return $return;
	}

	function title( $model, $vlevel = HC_PRESENTER::VIEW_HTML )
	{
		$return = array(
			$model->present_date($vlevel),
			$model->present_time($vlevel),
			);
		$return = join( ' ', $return );
		return $return;
	}

	function title_misc( $model )
	{
		$title_misc = array();
		// $title_misc[] = $model->present_status();
		switch( $model->type ){
			case $model->_const('TYPE_SHIFT'):
				$title_misc[] = $model->present_location();
				break;
		}

		$title_misc[] = $model->present_user();


		$out = HC_Html_Factory::widget('list')
			->add_attr('class', array('list-inline', 'list-separated'))
			;
		foreach( $title_misc as $tm ){
			$out->add_item( $tm );
		}
		return $out;
	}

	function title_misc_misc( $model )
	{
		$title_misc = array();
		$title_misc[] = 'id: ' . $model->id;

		$out = HC_Html_Factory::widget('list')
			->add_attr('class', array('list-inline', 'list-separated'))
			;
		foreach( $title_misc as $tm ){
			$out->add_item( $tm );
		}
		return $out;
	}

	function details( $model, $vlevel = HC_PRESENTER::VIEW_HTML )
	{
		switch( $vlevel ){
			case HC_PRESENTER::VIEW_HTML:
				$return = HC_Html_Factory::widget('shift_view');
				$return->set_shift( $model );
				break;
			case HC_PRESENTER::VIEW_TEXT:
			case HC_PRESENTER::VIEW_RAW:
				$return = array();
				$return[] = $model->present_title($vlevel);
				$return[] = $model->present_title_misc($vlevel);
				$return = join( ' ', $return );
				break;
		}
		return $return;
	}

	function user( $model, $vlevel = HC_PRESENTER::VIEW_HTML, $with_change = FALSE )
	{
		if( ! isset($model->user) )
			$model->user->get();

		$return = array();
		if( $model->user->id && $model->user->exists() ){
			$return[] = $model->user->present_title($vlevel);
		}
		else {
			$user_view = $model->user->present_label($vlevel) . '______';
			$user_view = HCM::__('Open Shift');
			switch( $vlevel ){
				case HC_PRESENTER::VIEW_HTML:
					$user_view = HC_Html_Factory::widget('titled', 'span')
						->add_attr('class', 'text-danger')
						->add_child( $user_view )
						;
					break;
			}
			$return[] = $user_view;
		}

		if( $with_change ){
			$changes = $model->get_changes();
			if( 
				isset($changes['user_id'])
			){
				$old_obj = HC_App::model( $model->user->my_class() );
				$old_obj->get_by_id( $changes['user_id'] );
				$return[] = ' [' . HCM::__('Old Value') . ': ' . $old_obj->present_title(HC_PRESENTER::VIEW_RAW) . ']';
			}
		}
		$return = join( '', $return );
		return $return;
	}

	function location( $model, $vlevel = HC_PRESENTER::VIEW_HTML, $with_change = FALSE )
	{
		if( $model->type == $model->_const('TYPE_TIMEOFF') ){
			$return = array();
			$return[] = $model->location->set('name', HCM::__('Timeoff'))->present_title($vlevel);
		}
		else {
			if( ! (isset($model->location) && $model->location->id) ){
				$model->location->get();
			}

			$return = array();
			$return[] = $model->location->present_title($vlevel);

			if( $with_change ){
				$changes = $model->get_changes();
				if( 
					isset($changes['location_id'])
				){
					$old_obj = HC_App::model( $model->location->my_class() );
					$old_obj->get_by_id( $changes['location_id'] );
					$return[] = ' [' . HCM::__('Old Value') . ': ' . $old_obj->present_title(HC_PRESENTER::VIEW_RAW) . ']';
				}
			}
		}
		$return = join( '', $return );
		return $return;
	}

	function start( $model, $vlevel = HC_PRESENTER::VIEW_HTML )
	{
		return $this->_time( $model->start, $vlevel );
	}

	function end( $model, $vlevel = HC_PRESENTER::VIEW_HTML )
	{
		return $this->_time( $model->end, $vlevel );
	}

	function release_request( $model, $vlevel = HC_PRESENTER::VIEW_HTML )
	{
		$return = NULL;
		if( $model->release_request ){
			$return = HCM::__('Pending');
			}
		else {
			$return = HCM::__('No Request');
		}
		return $return;
	}

	private function _time( $value, $vlevel = HC_PRESENTER::VIEW_HTML )
	{
		$return = array();
		switch( $vlevel ){
			case HC_PRESENTER::VIEW_HTML:
				$return[] = HC_Html::icon(HC_App::icon_for('time'));
				break;
			case HC_PRESENTER::VIEW_TEXT:
				$return[] = HCM::__('Time');
				$return[] = ': ';
				break;
		}

		$t = HC_Lib::time();
		$t->setTimestamp( $value );
		$return[] = $t->formatTime();

		$return = join( '', $return );
		return $return;
	}

	function time( $model, $vlevel = HC_PRESENTER::VIEW_HTML, $with_change = FALSE )
	{
		$t = HC_Lib::time();

		$return = array();
		switch( $vlevel ){
			case HC_PRESENTER::VIEW_HTML:
				if( $model->type == $model->_const('TYPE_TIMEOFF') ){
					$return[] = HC_Html::icon(HC_App::icon_for('timeoff'));
				}
				else {
					// $return[] = HC_Html::icon(HC_App::icon_for('time'));
				}
				break;
			case HC_PRESENTER::VIEW_TEXT:
				$return[] = HCM::__('Time');
				$return[] = ': ';
				break;
		}

		$period_view = $t->formatPeriodOfDay($model->start, $model->end);
		$period_view = str_replace(' ', '', $period_view);
		$return[] = $period_view;

		if( $with_change ){
			$changes = $model->get_changes();
			if( 
				isset($changes['start'])
				OR
				isset($changes['end'])
			){
				$old_start = isset($changes['start']) ? $changes['start'] : $model->start;
				$old_end = isset($changes['end']) ? $changes['end'] : $model->end;
				$return[] = ' [' . HCM::__('Old Value') . ': ' . $t->formatPeriodOfDay($old_start, $old_end) . ']';
			}
		}

		$return = join( '', $return );
		return $return;
	}

	function date( $model, $vlevel = HC_PRESENTER::VIEW_HTML, $with_change = FALSE )
	{
		$t = HC_Lib::time();
		$t->setDateDb( $model->date );

		$return = array();
		switch( $vlevel ){
			case HC_PRESENTER::VIEW_HTML:
				$return[] = HC_Html::icon(HC_App::icon_for('date'));
				break;
			case HC_PRESENTER::VIEW_TEXT:
				$return[] = HCM::__('Date');
				$return[] = ': ';
				break;
		}

		$return[] = $t->formatDateFull();

		if( $with_change ){
			$changes = $model->get_changes();
			if( 
				isset($changes['date'])
			){
				$t->setDateDb( $changes['date'] );
				$return[] = ' [' . HCM::__('Old Value') . ': ' . $t->formatDateFull() . ']';
			}
		}

		$return = join( '', $return );
		return $return;
	}

	function calendar_refresh( $model )
	{
		$refresh_keys = array();
		$refresh_keys[] = 'dat-' . $model->date;
		$refresh_keys[] = $model->location_id ? 'loc-' . $model->location_id : 'loc-0';
		$refresh_keys[] = $model->user_id ? 'use-' . $model->user_id : 'use-0';

		$parent_refresh = HC_Lib::get_combinations($refresh_keys);

		$refresh_keys2 = array();
		$changes = $model->get_changes();
		
		if( ! array_key_exists('id', $changes) ){
			if( array_intersect(array('date', 'user_id', 'location_id'), array_keys($changes)) ){
				if( array_key_exists('user_id', $changes) ){
					$refresh_keys2[] = $changes['user_id'] ? 'use-' . $changes['user_id'] : 'use-0';
				}
				else {
					$refresh_keys2[] = $model->user_id ? 'use-' . $model->user_id : 'use-0';
				}

				if( array_key_exists('location_id', $changes) ){
					$refresh_keys2[] = $changes['location_id'] ? 'loc-' . $changes['location_id'] : 'loc-0';
				}
				else {
					$refresh_keys2[] = $model->location_id ? 'loc-' . $model->location_id : 'loc-0';
				}

				if( array_key_exists('date', $changes) ){
					$refresh_keys2[] = $changes['date'] ? 'dat-' . $changes['date'] : 'dat-0';
				}
				else {
					$refresh_keys2[] = 'dat-' . $model->date;
				}
			}
		}

		if( $refresh_keys2 ){
			$parent_refresh2 = HC_Lib::get_combinations($refresh_keys2);
			$parent_refresh = array_merge( $parent_refresh, $parent_refresh2 );
		}

		$final_parent_refresh = array();
		foreach( $parent_refresh as $pr ){
			$final_parent_refresh[ join('-', $pr) ] = 1;
		}

//		$return = array_keys( $final_parent_refresh );
		$return = $final_parent_refresh;
		return $return;
	}
}