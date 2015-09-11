<?php
$acl['shift::*::conflicts::view'] = create_function( '$u, $o', '
	if( $o->user_id && ($u->id == $o->user_id) ){
		return TRUE;
	}
');