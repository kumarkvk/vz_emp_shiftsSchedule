<?php
class Shift_HC_Model extends MY_model
{
	var $table = 'shifts';
	var $default_order_by = array(
		'date' => 'ASC',
		'start' => 'ASC',
		'date_end' => 'ASC',
		'end' => 'ASC',
		'id' => 'ASC'
		);
	var $has_one = array(
		'user' => array(
			'class'			=> 'user',
			'other_field'	=> 'shift',
			),
		'location' => array(
			'class'			=> 'location',
			'other_field'	=> 'shift',
			),
		);

	const STATUS_ACTIVE = 1;
	const STATUS_DRAFT = 2;

	const TYPE_SHIFT = 1;
	const TYPE_TIMEOFF = 2;

	var $validation = array(
		'date'		=> array('required', 'trim', 'check_date_end'),
		// 'location'	=> array('required'),
		'end'		=> array('required', 'check_time', 'check_date_end'),
		'status'	=> array(
			'enum' => array(
				self::STATUS_ACTIVE,
				self::STATUS_DRAFT,
				)
			),
		'type'	=> array(
			'enum' => array(
				self::TYPE_SHIFT,
				self::TYPE_TIMEOFF,
				)
			),
		);

	protected function _prepare_get()
	{
		parent::_prepare_get();
		$this
			->include_related( 'location', array('id', 'name', 'show_order', 'description'), TRUE, TRUE )
			->include_related( 'user', array('id', 'first_name', 'last_name', 'active'), TRUE, TRUE )
			->order_by( 'date', 'ASC' )
			->order_by( 'start', 'ASC' )
			->order_by( 'user_last_name IS NULL', 'ASC' )
			->order_by( 'user_last_name', 'ASC' )
			->order_by( 'user_first_name', 'ASC' )
			->order_by( 'date_end', 'ASC' )
			->order_by( 'end', 'ASC' )
			->order_by( 'status', 'ASC' )
			->order_by( 'location_show_order', 'ASC' )
			;
	}

/*
	public function count($exclude_ids = NULL, $column = NULL, $related_id = NULL)
	{
		$this->_prepare_get();
		return parent::count($exclude_ids, $column, $related_id);
	}
*/

	public function get( $limit = NULL, $offset = NULL )
	{
		$this->_prepare_get();
		return parent::get( $limit, $offset );
	}

	public function get_iterated( $limit = NULL, $offset = NULL )
	{
		$this->_prepare_get();
		return parent::get_iterated( $limit, $offset );
	}

	public function from_array( $data )
	{
		$return = parent::from_array( $data );
		$this->skip_validation( FALSE );
		$this->validate();
		$this->skip_validation( FALSE );
		return $return;
	}

	public function is_published()
	{
		$return = ( $this->status == self::STATUS_ACTIVE ) ? TRUE : FALSE;
		return $return;
	}

	public function get_duration()
	{
		if( $this->date_end > $this->date ){
			$return = $this->end + (24*60*60 - $this->start);
		}
		else {
			$return = $this->end - $this->start;
		}
		return $return;
	}

	function publish()
	{
		$this->status = self::STATUS_ACTIVE;
		return $this->save();
	}

	function unpublish()
	{
		$this->status = self::STATUS_DRAFT;
		return $this->save();
	}

	public function overlaps( $two )
	{
		$return = TRUE;

		$one_start	= $this->date . sprintf('%05d', $this->start);
		$one_end	= $this->date_end . sprintf('%05d', $this->end);
		$two_start	= $two->date . sprintf('%05d', $two->start);
		$two_end	= $two->date_end . sprintf('%05d', $two->end);

		if(
			( $one_end <= $two_start )
			OR
			( $one_start >= $two_end )
		){
			$return = FALSE;
		}

		if( $return ){
			/* if me */
			if(
				( $this->id ) &&
				( $this->id == $two->id )
			){
				$return = FALSE;
			}
		}

		return $return;
	}
	
	public function covers( $two )
	{
		$return = FALSE;

		$one_start	= $this->date . sprintf('%05d', $this->start);
		$one_end	= $this->date_end . sprintf('%05d', $this->end);
		$two_start	= $two->date . sprintf('%05d', $two->start);
		$two_end	= $two->date_end . sprintf('%05d', $two->end);

		if(
			( $one_end >= $two_end )
			&&
			( $one_start <= $two_start )
		){
			$return = TRUE;
		}

		if( $return ){
			/* if me */
			if(
				( $this->id ) &&
				( $this->id == $two->id )
			){
				$return = FALSE;
			}
		}

		return $return;
	}

/* validation */
	public function _check_date_end( $field )
	{
		if( ! $this->date ){
			return TRUE;
		}

		if( $this->end <= $this->start ){
			$t = HC_Lib::time();
			$t->setDateDb( $this->date );
			$t->modify( '+1 day' );
			$this->date_end = $t->formatDate_Db();
		}
		else {
			$this->date_end = $this->date;
		}
		return TRUE;
	}

	public function _check_time( $field )
	{
		$return = ( $this->end != $this->start ) ? TRUE : FALSE;
		if( ! $return ){
			$return = HCM::__('The end time should differ from the start time');
		}
		return $return;
	}

	protected function _before_delete()
	{
		$CI =& ci_get_instance();

	/* delete notes */
		if( $CI->hc_modules->exists('notes') ){
			$this->note->get()->delete_all();
		}

	/* delete logaudit */
		if( $CI->hc_modules->exists('logaudit') ){
			$logaudit = HC_App::model("logaudit");
			$logaudit
				->where('object_class',	$this->my_class())
				->where('object_id',	$this->id)
				->delete()
				;
		}

	/* delete trade */
		if( $CI->hc_modules->exists('trades') ){
			$this->trade_request->get()->delete_all();
			$this->offer_request->get()->delete_all();
			$this->offer_offer->get()->delete_all();
		}
	}

	public function __get($name)
	{
		switch( $name ){
			case 'time_id':
				return $this->start . '-' . $this->end;
				break;
		}
		return parent::__get($name);
	}
}
