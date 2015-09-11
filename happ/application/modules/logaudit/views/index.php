<?php
if( ! $entries ){
	echo HCM::__('Nothing');
	return;
}
$t = HC_Lib::time();

$out = HC_Html_Factory::widget('list')
	->add_attr('class', array('list-unstyled'))
	// ->add_attr('class', array('list-separated'))
	->add_attr('class', array('list-padded'))
	// ->add_attr('class', array('list-striped'))
	->add_attr('class', array('list-separated'))
	->add_attr('class', array('list-bordered'))
	;

foreach( $entries as $action_time => $changes ){
	if( ! $changes ){
		continue;
	}
	$t->setTimestamp( $action_time );
	$changed_objs = array_keys($changes);
	$changed_props = array_keys($changes[$changed_objs[0]]);
	$user = $changes[$changed_objs[0]][$changed_props[0]]->user;

	$header_view = HC_Html_Factory::widget('list')
		// ->add_attr('class', array('list-unstyled'))
		->add_attr('class', array('list-inline'))
		// ->add_attr('class', array('list-separated'))
		->add_attr('class', array('text-small'))
		->add_attr('class', array('text-muted'))
		;
	$header_view
		->add_item( $t->formatFull() )
		->add_item( $user->present_title() )
		;

	$body = HC_Html_Factory::widget('list')
		->add_attr('class', array('list-unstyled', 'list-separated'))
		;
	$my_class = $object->my_class();
	foreach( $changes as $object_full_key => $obj_changes ){
		$row = HC_Html_Factory::widget('grid');
		$this_object = $objects[$object_full_key];

		$status_as_created = NULL;
		if( isset($obj_changes['id']) && isset($obj_changes['status']) ){
			$status_as_created = $obj_changes['status']->new;
			unset($obj_changes['status']);
		}

// echo "ofk = $object_full_key<br>";
		list( $this_object_class, $this_object_id, $this_object_relname ) = explode( '.', $object_full_key );

//_print_r( array_keys($obj_changes) );
		foreach( $obj_changes as $pname => $e ){
			$this_class = $this_object->my_class();
			$pname = $this_object->prop_name( $pname );

			$pname_view = array();
			if( $this_class != $my_class ){
				$pname_view[] = $this_object->present_label( HC_PRESENTER::VIEW_TEXT );
				// $pname_view[] = HC_Html_Factory::element('br');
				$pname_view[] = '::';
				if( $this_object_relname != $my_class ){
					$pname_view[] = $this_object_relname;
					$pname_view[] = '::';
				}
			}
			if( $pname == 'id' ){
			/* translators: this is an entry in the list of changes history of an object */
				$pname_view[] = HCM::__('Object Created');
			}
			else {
				$pname_view[] = $this_object->present_property_name( $pname, HC_PRESENTER::VIEW_TEXT );
			}
			$pname_view = join('', $pname_view);

			$change_view = '';
			if( $pname == 'id' ){
				switch( $this_class ){
					case 'note':
						$pname_view = '';
						$change_view = HC_Html_Factory::element('em')
							->add_child( HC_Html::icon(HC_App::icon_for('comment')) )
							->add_child( $this_object->present_content() )
							;
						break;
					default:
						if( $status_as_created !== NULL ){
							$this_object->status = $status_as_created;
							$change_view = $this_object->present_status();
						}
						break;
				}
			}
			else {
				if( is_object($this_object->{$pname}) ){
					$pobject = HC_App::model($pname);

					$old_view = '';
					$new_view = '';

					if( $e->old ){
						$pobject->get_by_id( $e->old );
						if( $pobject->exists() )
							$old_view = $pobject->present_title();
						else
							$old_view = '';
					}

					if( $e->new ){
						$pobject->get_by_id( $e->new );
						if( $pobject->exists() )
							$new_view = $pobject->present_title();
						else
							$new_view = '';
					}

					if( ($e->old) && (! $e->new) ){
						$old_view = '<span style="text-decoration: line-through;">' . $old_view . '</span>';
						$new_view = '';
					}
					elseif( (! $e->old) && ($e->new) ){
						$old_view = '';
					}
				}
				else {
					$old_view = '';
					if( $e->old !== NULL ){
						$this_object->{$pname} = $e->old;
						$old_view = $this_object->{'present_' . $pname}();
					}

					$this_object->{$pname} = $e->new;
					$new_view = $this_object->{'present_' . $pname}();
				}

				$change_view = HC_Html_Factory::widget('list')
					->add_attr('class', array('list-inline'))
					// ->add_attr('class', array('list-separated'))
					;
				if( $old_view ){
					$change_view->add_item( $old_view );
					}
				if( $new_view && $old_view ){
					$change_view->add_item( HC_Html::icon('arrow-right') );
					}
				if( $new_view ){
					$change_view->add_item( $new_view );
					}
			}

			if( strlen($pname_view) ){
				$row->add_item( $pname_view, 4 );
				$row->add_item( $change_view, 8 );
			}
			else {
				$row->add_item( $change_view, 12 );
			}
		}
		$body->add_item( $row );
	}

/*
	$entry = HC_Html_Factory::widget('panel')
		->add_attr('class', array('panel-default', 'panel-condensed'))
		;
	$entry->set_header( $header_view );
	$entry->set_body( $body );
*/

/*
	$entry = HC_Html_Factory::widget('grid')
		;
	$entry->add_item( $header_view, 4 );
	$entry->add_item( $body, 8 );
*/
	$entry = HC_Html_Factory::widget('list')
		->add_attr('class', 'list-unstyled')
		;
	$entry->add_item( $header_view );
	$entry->add_item( $body );

	$out->add_item( $entry );
}

echo $out->render();
?>