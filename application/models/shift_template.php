<?php
class Shift_Template_HC_Model extends MY_model
{
/*
properties: 
	name
	start
	end
*/

	var $table = 'shift_templates';
	var $default_order_by = array('start' => 'ASC');
	var $validation = array(
		'name'	=> array('required', 'trim', 'max_length' => 50, 'unique'),
		'start'	=> array('required', 'trim'),
		'end'	=> array('required', 'trim', 'differs' => 'start'),
		);

	public function get_duration()
	{
		if( $this->end > $this->start )
			$return = $this->end - $this->start;
		else
			$return = $this->end + (24*60*60 - $this->start);
		return $return;
	}
}