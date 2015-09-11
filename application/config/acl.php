<?php
$CI =& ci_get_instance();
$modules = $CI->hc_modules;

$acl['*'] = create_function( '$u, $o', '
	if( $u->level >= $u->_const("LEVEL_MANAGER") ){
		return TRUE;
	}
');

$acl['shift::view'] = create_function( '$u, $o', '
	if( $u->level >= $u->_const("LEVEL_MANAGER") ){
		return TRUE;
	}

/* can view own pending timeoffs, otherwise no */
	if( $o->user_id && ($u->id == $o->user_id) ){
		return TRUE;
	}
	elseif( $o->status != $o->_const("STATUS_ACTIVE") ){
		return FALSE;
	}

/* if anonymous then cannot view open shifts */
	if( (! $u->id) && (! $o->user_id) ){
		return FALSE;
	}

/* if anonymous then cannot view timeoffs */
	if( (! $u->id) && (in_array($o->type, array($o->_const("TYPE_TIMEOFF")))) ){
		return FALSE;
	}

/* cannot view others timeoffs */
	if( ($u->id != $o->user_id) && (in_array($o->type, array($o->_const("TYPE_TIMEOFF")))) ){
		return FALSE;
	}

	$ri = HC_Lib::ri();
	if( $ri ){
		/* shortcode so can view it anyway */
		return TRUE;
	}
	else {
		$user_level = $u->level ? $u->level : 0;
		$app_conf = HC_App::app_conf();
		$wall_schedule_display = $app_conf->get("wall:schedule_display");

		if( $user_level >= $wall_schedule_display ){
			return TRUE;
		}
	}
');

$acl['shift::edit'] = create_function( '$u, $o', '
	return;
');

$acl['shift::add'] = create_function( '$u, $o', '
	if( $o->user_id && ($o->user_id == $u->id) ){
		return TRUE;
	}
');

$acl['shift::validate'] = create_function( '$u, $o', '
	if( $u->level >= $u->_const("LEVEL_MANAGER") ){
		return TRUE;
	}
	if( ! $u->id ){
		return FALSE;
	}
	if( $o->user_id != $u->id ){
		return FALSE;
	}
	if( ! in_array($o->type, array($o->_const("TYPE_TIMEOFF"))) ){
		return FALSE;
	}

	if( $o->status !== NULL ){
		$app_conf = HC_App::app_conf();
		$timeoff_approval_required = $app_conf->get("timeoff:approval_required");
		if( $timeoff_approval_required ){
			if( ! in_array($o->status, array($o->_const("STATUS_DRAFT"))) ){
				return FALSE;
			}
		}
		else {
			if( ! in_array($o->status, array($o->_const("STATUS_ACTIVE"))) ){
				return FALSE;
			}
		}
	}

	return TRUE;
');

if( $modules->exists('logaudit') ){
	$acl['shift::history::view'] = create_function( '$u, $o', '
		if( $o->user_id && ($u->id == $o->user_id) ){
			return TRUE;
		}
	');
}

if( $modules->exists('notes') ){
	$acl['shift::notes::view'] = $acl['shift::view'];
	$acl['shift::notes::add'] = create_function( '$u, $o', '
		if( $o->user_id && ($u->id == $o->user_id) ){
			return TRUE;
		}
		if( ! $o->id ){ // can add notes for new shifts
			return TRUE;
		}
	');
	$acl['note::edit'] = create_function( '$u, $o', '
		if( $o->author_id && ($o->author_id == $u->id) ){
			return TRUE;
		}
	');

	$acl['note::view'] = create_function( '$u, $o', '
		if( $o->author_id && ($o->author_id == $u->id) ){
			return TRUE;
		}
		if( $o->access_level == $o->_const("LEVEL_EVERYONE") ){
			return TRUE;
		}
		if( $u->id ){
			if( $u->level == $u->_const("LEVEL_STAFF") ){
				if( $o->access_level == $o->_const("LEVEL_ALL_USERS") ){
					return TRUE;
				}
				elseif( $o->access_level == $o->_const("LEVEL_OWNER") ){
					if( $o->shift_id ){
						if( $o->shift->get()->user_id == $u->id ){
							return TRUE;
						}
					}
				}
			}
		}
	');
}

if( $modules->exists('loginlog') ){
}