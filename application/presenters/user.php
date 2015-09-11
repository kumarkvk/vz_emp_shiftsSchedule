<?php
class User_HC_Presenter extends HC_Presenter
{
	function level( $model, $vlevel = HC_PRESENTER::VIEW_HTML )
	{
		$text = array(
			USER_HC_MODEL::LEVEL_STAFF 		=> HCM::__('Staff'),
			USER_HC_MODEL::LEVEL_MANAGER	=> HCM::__('Manager'),
			USER_HC_MODEL::LEVEL_ADMIN		=> HCM::__('Admin'),
			);
		$return = isset($text[$model->level]) ? $text[$model->level] : 'N/A';
		return $return;
	}

	function status( $model, $vlevel = HC_PRESENTER::VIEW_HTML )
	{
		list( $label_text, $label_class ) = $this->_status_details($model);
		$label_class = $this->status_class($model, TRUE);

		switch( $vlevel ){
			case HC_PRESENTER::VIEW_HTML:
			case HC_PRESENTER::VIEW_HTML_ICON:
				$return = HC_Html::label( $label_class, $label_text );
				break;
			case HC_PRESENTER::VIEW_TEXT:
				$return = HCM::__('Status') . ': ' . $label_text;
				break;
			case HC_PRESENTER::VIEW_RAW:
				$return = $label_text;
				break;
		}
		return $return;
	}

	function status_class( $model, $skip_type = FALSE )
	{
		$return = array();
		list( $label_text, $label_class ) = $this->_status_details($model, $skip_type);
		if( ! is_array($label_class) )
			$label_class = array( $label_class );
		$return = array_merge( $return, $label_class );
		return $return;
	}

	private function _status_details( $model )
	{
		$return = NULL;
		$status = $model->active;

		$details = array(
/* translators: status */
			USER_HC_MODEL::STATUS_ACTIVE 	=> array( HCM::__('Active'),	'success' ),
/* translators: status */
			USER_HC_MODEL::STATUS_ARCHIVE	=> array( HCM::__('Archived'),	'archive' ),
			);

		if( isset($details[$status]) ){
			$return = $details[$status];
		}

		return $return;
	}

	function label( $model, $vlevel = HC_PRESENTER::VIEW_HTML )
	{
		$return = '';
		switch( $vlevel ){
			case HC_PRESENTER::VIEW_HTML:
			case HC_PRESENTER::VIEW_HTML_ICON:
				$ri = HC_Lib::ri();
				if( $ri == 'wordpress' ){
					$avatar = get_avatar( $model->email, 16 );
					$return = HC_Html::icon( '', TRUE, $avatar );
				}
				else {
					$return = HC_Html::icon(HC_App::icon_for('user'));
					if( ! $model->exists() ){
						$return->add_attr('class', 'text-danger');
					}
					else {
						if( ($model->id) && ($model->active != $model->_const('STATUS_ACTIVE')) ){
							$return = HC_Html::icon_stack(
								array( HC_App::icon_for('user'), array('ban', 'text-muted') )
								);
						}
					}
				}
				break;
			case HC_PRESENTER::VIEW_TEXT:
				$return = HCM::__('Staff');
				break;
		}
		return $return;
	}

	function title( $model, $vlevel = HC_PRESENTER::VIEW_HTML )
	{
		$return = array();
		$return[] = $this->label( $model, $vlevel );

		switch( $vlevel ){
			case HC_PRESENTER::VIEW_TEXT:
				$return[] = ': ';
				break;
		}

		if( $model->exists() ){
			$return[] = $model->first_name . ' ' . $model->last_name;
		}
		else{
			$return[] = HCM::__('Open Shift');
		}

		$return = join( '', $return );
		return $return;
	}

	function title_misc( $model )
	{
		$title_misc = array();
		$title_misc[] = $model->present_status();
		$title_misc[] = 'id: ' . $model->id;
		$title_misc[] = $model->present_level();

		$out = HC_Html_Factory::widget('list')
			->add_attr('class', array('list-inline', 'list-separated'))
			;
		foreach( $title_misc as $tm ){
			$out->add_item( $tm );
		}
		return $out;
	}
}