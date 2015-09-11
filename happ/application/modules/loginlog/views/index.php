<?php
$t = HC_Lib::time();

$table = HC_Html_Factory::widget('table')
	->add_attr('class', 'table')
	->add_attr('class', 'table-striped')
	->add_attr('class', 'table-condensed')
	;
$header = array();
$header[] = HCM::__('Date and Time');
if( ! $user_id ){
	$header[] = HCM::__('User');
}
$header[] = 'IP';

$table->set_header( $header );

foreach( $entries as $e ){
	$row = array();

	$t->setTimestamp( $e->action_time );
	$this_view = '';
	$this_view .= $t->formatWeekdayShort();
	$this_view .= ', ';
	$this_view .= $t->formatDate();
	$this_view .= ' ';
	$this_view .= $t->formatTime();
	$row[] = $this_view;

	if( ! $user_id ){
		$row[] = $e->user->present_title();
	}
	$row[] = $e->remote_ip;
	$table->add_row( $row );
}

echo $table->render();
?>