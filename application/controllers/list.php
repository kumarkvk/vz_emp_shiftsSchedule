<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class List_HC_Controller extends _Front_HC_Controller
{
	protected $form = NULL;
	protected $views_path = 'list';
	protected $rootlink = 'list';
	protected $fix = array(
		'location'	=> array(),
		'staff'		=> array(),
		);

	function __construct()
	{
		parent::__construct();

		if( ($test_user = $this->auth->user()) && $test_user->id ){
		}
		else {
			$this->fix['filter'] = NULL;
		}

		$acl = HC_App::acl();
		if( $this->hc_modules->exists('shift_groups') ){
			$this->form = HC_Lib::form()
				->set_input( 'action', 'hidden' )
				->set_input( 'id', 'checkbox_set' )
				;
		}
	}

	function quickstats()
	{
		$args = func_get_args();
		/* supplied from command line */
		if( (count($args) >= 1) && is_array($args[0]) ){
			list( $shifts, $state ) = $args[0];
		}
		else {
			$state = $this->_grab_state( func_get_args(), 'browse' );
			$shifts = $this->_init_shifts( $state );
		}
		// return;
		$this->_display( $shifts, $state, 'browse', 'quickstats' );
		return;
	}

/* LIST SHIFTS */
	function index()
	{
		$tab = isset($this->fix['tab']) ? $this->fix['tab'] : 'calendar';
		return $this->_index( func_get_args(), $tab );
	}

	function browse()
	{
		return $this->_index( func_get_args(), 'browse' );
	}

	function calendar()
	{
		return $this->_index( func_get_args(), 'calendar' );
	}

	function _index( $args, $tab )
	{
		$state = $this->_grab_state( $args, $tab );

	/* do some cleanup */
		if( ! in_array($state['range'], array('week', 'month')) ){
			if( $tab == 'calendar' ){
				$tab = 'browse';
			}
		}
		// if( ($tab == 'calendar') && $state['by'] && ($state['range'] == 'month')){
			// $state['by'] = NULL;
		// }

		$shifts = $this->_init_shifts( $state );

		switch( $tab ){
			case 'calendar':
				$view_file_prefix = 'calendar' . '_' . $state['range'];
				break;
			case 'browse':
				$view_file_prefix = 'table';
				break;
		}

		switch( $state['by'] ){
			case 'staff':
				$view_file = $view_file_prefix . '_by_staff';
				break;
			case 'location':
				$view_file = $view_file_prefix . '_by_location';
				break;
			default:
				$view_file = $view_file_prefix . '';
				break;
		}
		$this->_display( $shifts, $state, $tab, $view_file );
	}

/* DAY SHIFTS */
	function day()
	{
		$args = func_get_args();

		/* supplied from command line */
		if( (count($args) >= 1) && is_array($args[0]) ){
			list( $shifts, $state ) = $args[0];
			$state['range'] = 'day';
		}
		else {
			$state = $this->_grab_state( func_get_args(), 'browse' );
			$state['range'] = 'day';
			$shifts = $this->_init_shifts( $state );
		}
		$this->_display( $shifts, $state, 'browse', 'list' );
	}

	function listing()
	{
		$args = func_get_args();

		/* supplied from command line */
		if( (count($args) >= 1) && is_array($args[0]) ){
			list( $shifts, $state ) = $args[0];
		}
		else {
			$state = $this->_grab_state( func_get_args(), 'browse' );
			$shifts = $this->_init_shifts( $state );
		}
		$this->_display( $shifts, $state, 'browse', 'list' );
	}

/* DOWNLOAD SHIFTS */
	function download()
	{
		$state = $this->_grab_state( func_get_args(), 'index' );
		$shifts = $this->_init_shifts( $state );
		return $this->_download( $shifts );
	}

/* SHIFTS OF A GROUP */
	function group( $model )
	{
		if( ! is_object($model) ){
			$id = $model;
			$model = HC_App::model('shift');
			$model->where('id', $id)->get();
		}
		$group_id = $model->group_id;

		$shifts = array();
		if( $group_id > 0 ){
			$shifts = HC_App::model('shift');
			$shifts->where('group_id', $group_id); 
			$shifts->get();

			$acl = HC_App::acl();
			$shifts = $acl->filter( $shifts, 'view' );
		}

		$state = array();
		$this->_display( $shifts, $state, 'browse', 'table' );
	}

	/* renders output */
	private function _prepare_display( $state )
	{
		static $params = array();

	/* load locations */
		if( ! isset($params['all_locations']) ){
			$params['all_locations'] = array();

			if( $this->fix['location'] ){
				if( 
					is_array($this->fix['location']) &&
					(count($this->fix['location']) == 1) &&
					($this->fix['location'][0] == 0)
					){
						/* don't show locations */
						$params['all_locations'] = array();
					}
				else {
					$model = HC_App::model('location');
					$model->where_in('id', $this->fix['location'] );
					$model->get();
					foreach( $model as $obj ){
						$params['all_locations'][ $obj->id ] = $obj;
					}
				}
			}
			else {
				$model = HC_App::model('location');
				$model->get();
				foreach( $model as $obj ){
					$params['all_locations'][ $obj->id ] = $obj;
				}
			}
		}

	/* load users */
		if( ! isset($params['all_staffs']) ){
			$params['all_staffs'] = array();

			if( $this->fix['staff'] ){
				$model = HC_App::model('user');
				$model->where_in('id', $this->fix['staff'] );
				$model->get();
				foreach( $model as $obj ){
					$params['all_staffs'][ $obj->id ] = $obj;
				}
			}
			else {
				$nastaff = HC_App::model('user');
				$nastaff->id = 0;
				$params['all_staffs'][0] = $nastaff;

				$model = HC_App::model('user');
				$model->get_staff();
				foreach( $model as $obj ){
					$params['all_staffs'][ $obj->id ] = $obj;
				}
			}
		}

	/* filtered staffs */
		if( array_key_exists('staff', $state) && $state['staff'] ){
			$params['staffs'] = array();
			foreach( $state['staff'] as $oid ){
				$params['staffs'][$oid] = $params['all_staffs'][$oid];
			}
		}
		else {
			$params['staffs'] = $params['all_staffs'];
		}

	/* filtered locations */
		if( array_key_exists('location', $state) && $state['location'] ){
			$params['locations'] = array();
			foreach( $state['location'] as $oid ){
				$params['locations'][$oid] = $params['all_locations'][$oid];
			}
		}
		else {
			$params['locations'] = $params['all_locations'];
		}

		return $params;
	}

	private function _display( $shifts, $state, $tab = 'list', $display = 'table' )
	{
		$extensions = HC_App::extensions();

		$is_module = ( $this->input->is_ajax_request() OR $this->is_module() ) ? 1 : 0;
		$is_print = (isset($state['print']) && $state['print']) ? 1 : 0;
		if( $is_module ){
			$is_print = 0;
		}

		$display_params = $this->_prepare_display( $state );
		if( $is_print ){
			$this->form = NULL;
		}

	/* render */
		$rootlink = $this->rootlink;
		$layout = clone $this->layout;

	/* performance sensitive */
		if( isset($this->fix['tab']) && $this->fix['tab'] ){
			$enabled_tabs = is_array($this->fix['tab']) ? $this->fix['tab'] : array($this->fix['tab']);
		}
		else {
			$enabled_tabs = array('calendar', 'browse');
		}

		if( ! $is_module ){
			if( ! in_array($display, array('list')) ){
				$layout->set_partial(
					'control', 
					$this->render( 
						$this->views_path . '/_control',
						array(
							'fix'			=> $this->fix,
							'rootlink'		=> $rootlink,
							'tab'			=> $tab,
							'enabled_tabs'	=> $enabled_tabs,
							'state'			=> $state,
							'all_staffs'	=> $display_params['all_staffs'],
							'all_locations'	=> $display_params['all_locations'],
							'staffs'		=> $display_params['staffs'],
							'locations'		=> $display_params['locations'],
							)
						)
					);
			}
		}

		if( ! $is_module ){
			$layout->set_partial(
				'filter', 
				$this->render( 
					$this->views_path . '/_filter',
					array(
						'rootlink'		=> $rootlink,
						'tab'			=> $tab,
						'state'			=> $state,
						'all_staffs'	=> $display_params['all_staffs'],
						'all_locations'	=> $display_params['all_locations'],
						'staffs'		=> $display_params['staffs'],
						'locations'		=> $display_params['locations'],
						'fix'			=> $this->fix,
						)
					)
				);
		}

		$display_file = '_' . $display;

		$form = $this->form;
		$acl = HC_App::acl();
		$can_edit_shifts = $acl->filter( $shifts, 'edit' );
		if( ! $can_edit_shifts ){
			$form = NULL;
		}

		$list_view = $this->render(
			$this->views_path . '/' . $display_file,
			array(
				'rootlink'		=> $rootlink,
				'state'		=> $state,
				'shifts'	=> $shifts,
				'form'		=> $form,

				'all_staffs'	=> $display_params['all_staffs'],
				'all_locations'	=> $display_params['all_locations'],
				'staffs'		=> $display_params['staffs'],
				'locations'		=> $display_params['locations'],
				)
			);

	/* extensions */
		$more_content = array();
		if( ($display == 'list') && (isset($state['range']) && ($state['range'] == 'day')) ){
			$more_content = $extensions->run(
				'list/day',
				'state', $state
				);
		}

		if( $more_content ){
			$list_view = HC_Html_Factory::widget('list')
				->add_attr('class', 'list-unstyled')
				->add_attr('class', 'list-separated')
				->add_item( $list_view )
				;
			foreach( $more_content as $subkey => $subvalue ){
				if( $subvalue ){
					$list_view->add_item( $subvalue );
				}
			}
		}

		$layout->set_partial(
			'list',
			$list_view
			);

		$this->layout->set_partial(
			'content', 
			$this->render(
				$this->views_path . '/index',
				array(
					'layout'	=> $layout,
					'is_module'	=> $is_module,
					'is_print'	=> $is_print,
					)
				)
			);
		if( $is_print ){
			$this->layout('print');
		}
		else {
			$this->layout();
		}
	}

/* init state from supplied params */
	private function _grab_state( $args, $tab = 'browse' )
	{
		$t = HC_Lib::time();

		$state = array(
			'range'		=> 'week',
			'date'		=> $t->formatDate_Db(),
			'by'		=> NULL,
			'location'	=> array(),
			'staff'		=> array(),
			'type'		=> NULL,
			'wide'		=> NULL,
			'filter'	=> NULL,
			);

		$default_params = $this->default_params;
		$supplied = hc_parse_args( $args );
		$supplied = array_merge( $default_params, $supplied );

		foreach( $supplied as $k => $v ){
			if( in_array($k, array('staff', 'location', 'type')) ){
				if( strpos($v, '.') !== FALSE ){
					$v = explode('.', $v);
				}
				elseif( strpos($v, ',') !== FALSE ){
					$v = explode(',', $v);
				}
				else {
					$v = array($v);
				}
			}
			$state[$k] = $v;
		}

		/* check _current_user_id_ */
		if( isset($state['staff']) ){
			$check_current_user = array('_current_user_id_', '_current_user_id', 'current_user_id', 'current_user', '_current_user_');
			$current_user_key = '';
			foreach( $check_current_user as $cuk ){
				if( in_array($cuk, $state['staff']) ){
					$current_user_key = $cuk;
					break;
				}
			}

			if( $current_user_key ){
				$current_user_id = 0;
				if( $test_user = $this->auth->user() ){
					$current_user_id = $test_user->id;
				}
				$state['staff'] = HC_Lib::replace_in_array($state['staff'], $current_user_key, $current_user_id);
			}
		}

	/* fixed ? */
		$force_fixed = array( 'location', 'staff' );

		foreach( $default_params as $k => $v ){
			if( in_array($k, $force_fixed) ){
				$this->fix[$k] = $state[$k];
			}
		}

		switch( $state['range'] ){
			case 'all':
				$t = HC_Lib::time();
				$shifts = HC_App::model('shift');
				$min_date = $shifts->select_min('date')->get()->date;
				$max_date = $shifts->select_max('date_end')->get()->date_end;

				if( $min_date && $max_date ){
					$state['date'] = $min_date . '_' . $max_date;
				}
				break;

			case 'upcoming':
				$t = HC_Lib::time();
				$shifts = HC_App::model('shift');
				$min_date = $t->setNow()->formatDate_Db();
				$max_date = $shifts->select_max('date_end')->get()->date_end;

				if( $min_date && $max_date ){
					$state['date'] = $min_date . '_' . $max_date;
				}
				break;

			case 'day':
			case 'week':
			case 'month':
				if( ! $state['date'] ){
					$t->setNow();
					$state['date'] = $t->formatDate_Db();
				}
				break;

			case 'custom':
				if( strpos($state['date'], '_') !== FALSE ){
					list( $start_date, $end_date ) = explode('_', $state['date']);
				}
				else {
					$start_date = $end_date = $state['date'];
				}

				$t->setNow();
				if( ! $start_date ){
					$start_date = $t->formatDate_Db();
					}
				if( ! $end_date ){
					$end_date = $t->formatDate_Db();
					}
				$state['date'] = join('_', array($start_date, $end_date));
				break;
		}

	/* if custom dates supplied */
		if( isset($supplied['customdates']) && $supplied['customdates'] ){
			$post = $this->input->post();
			if( isset($post['start_date']) && isset($post['end_date']) ){
				if( $post['end_date'] <= $post['start_date'] ){
					$post['end_date'] = $post['start_date'];
				}
				$state['date'] = $post['start_date'] . '_' . $post['end_date'];
				$state['range'] = 'custom';
				unset( $state['customdates'] );

				$link = HC_Lib::link( $this->views_path . '/' . $tab, $state );
				$redirect_to = $link->url();
				$this->redirect( $redirect_to );
				return;
			}
		}

	/* something fixed ? */
		foreach( $this->fix as $fk => $fv ){
			if( $fv ){
				unset( $state[$fk] );
			}
		}

		return $state;
	}

/* find appropriate shifts */
	private function _init_shifts( $state )
	{
		$t = HC_Lib::time();
		$shifts = HC_App::model('shift');
		switch( $state['range'] ){
			case 'custom':
				if( strpos($state['date'], '_') !== FALSE ){
					list( $start_date, $end_date ) = explode('_', $state['date']);
				}
				else {
					$start_date = $end_date = $state['date'];
				}
				break;

			case 'day':
				$start_date = $state['date'];
				$end_date = 0;
				break;

			case 'all':
				$start_date = $end_date = 0;
				break;

			case 'upcoming':
				$t->setNow();
				$start_date = $t->formatDate_Db();
				$end_date = NULL;
				break;

			default:
				$t->setDateDb( $state['date'] );
				list( $start_date, $end_date ) = $t->getDates( $state['range'], TRUE );
				break;
		}

		if( $start_date && $end_date ){
			// $shifts->where('date_end >=', $start_date);
			// $shifts->where('date <=', $end_date);
			$shifts->where('date_end >=', $start_date);
			$shifts->where('date <=', $end_date);
			$shifts->where('date >=', $start_date);
		}
		elseif( $start_date && $end_date === 0 ){
			$shifts->where('date =', $start_date);
		}
		elseif( $start_date && ($end_date === NULL) ){
			$shifts->where('date_end >=', $start_date);
		}

	/* location */
		$where_location = array();
		if( $this->fix['location'] ){
			if( 
				is_array($this->fix['location']) &&
				(count($this->fix['location']) == 1) &&
				($this->fix['location'][0] == 0)
				){
					/* all locations */
				}
			else {
				$where_location = $this->fix['location'];
			}
		}
		if( isset($state['location']) ){
			if( ! is_array($state['location']) ){
				$state['location'] = array($state['location']);
			}
			$where_location = $state['location'];
		}

		if( $where_location ){
			$shifts->group_start();
			$shifts->or_where('type', $shifts->_const('TYPE_TIMEOFF'));
			$shifts->or_where_in_related('location', 'id', $where_location);
			$shifts->group_end();
		}

	/* staff */
		$where_staff = array();
		if( $this->fix['staff'] ){
			$where_staff = $this->fix['staff'];
		}
		if( isset($state['staff']) ){
			if( ! is_array($state['staff']) ){
				$state['staff'] = array($state['staff']);
			}
			$where_staff = $state['staff'];
		}

		if( count($where_staff) ){
			if( in_array(0, $where_staff) ){
				$shifts->group_start();
				$shifts->or_where_related('user', 'id', NULL, TRUE);
				$shifts->or_where_related('user', 'id', 0);
				$shifts->or_where_in_related('user', 'id', $where_staff);
				$shifts->group_end();
			}
			else {
				$shifts->where_related('user', 'id', $where_staff);
			}
		}

	/* type */
		if( isset($this->fix['type']) ){
			$state['type'] = $this->fix['type'];
		}

		if( isset($state['by']) && ($state['by'] == 'location') ){
			$shifts->where('type', $shifts->_const('TYPE_SHIFT'));
		}

		if( isset($state['type']) && ($state['type'] !== NULL) ){
			$this_types = array();
			$this_statuses = array();
			foreach( $state['type'] as $stype ){
				if( strpos($stype, '_') === FALSE ){
					$this_type = $stype;
					$this_types[$this_type] = 1;
				}
				else {
					list( $this_type, $this_status ) = explode('_', $stype);
					$this_types[$this_type] = 1;
					$this_statuses[$this_status] = 1;
				}
			}
			if( $this_types ){
				$shifts->where_in('type', array_keys($this_types));
			}
			if( $this_statuses ){
				$shifts->where_in('status', array_keys($this_statuses));
			}
		}

	/* status */
		if( isset($state['status']) ){
			$shifts->where('status', $state['status']);
		}
	
	/* extensions */
		$extensions = HC_App::extensions();
		$current_filter = '';
		if( isset($this->fix['filter']) ){
			$current_filter = $this->fix['filter'];
		}
		elseif( isset($state['filter']) ){
			$current_filter = $state['filter'];
		}

	/* preprocess */
		if( $current_filter ){
			if( $extensions->has(array('list/filter', $current_filter)) ){
				$shifts = $extensions->run(
					array('list/filter', $current_filter),
					'pre',
					$shifts
					);
			}
		}

	/* NOW GET */
		$shifts->get();
		// $shifts->get_iterated();
		// $shifts->check_last_query();

		$acl = HC_App::acl();
		$return = $acl->filter( $shifts, 'view' );

	/* extensions with postprocess */
		if( $current_filter ){
			if( $extensions->has(array('list/filter', $current_filter)) ){
				$return = $extensions->run(
					array('list/filter', $current_filter),
					'post',
					$return
					);
			}
		}

		return $return;
		// return $shifts;
	}

/* pushes download */
	private function _download( $shifts )
	{
		$app_conf = HC_App::app_conf();
		$separator = $app_conf->get( 'csv_separator' );

	// header
		$headers = array(
			HCM::__('Type'),
			HCM::__('Date'),
			HCM::__('Time'),
			HCM::__('Duration'),
			HCM::__('Staff'),
			HCM::__('Location'),
			HCM::__('Status')
			);

		$data = array();
		$data[] = join( $separator, $headers );

		$t = HC_Lib::time();

	// shifts
		foreach( $shifts as $sh )
		{
			$values = array();

		// type
			$values[] = $sh->present_type(HC_PRESENTER::VIEW_RAW);

		// date
			$values[] = $sh->present_date(HC_PRESENTER::VIEW_RAW);

		// time
			$values[] = $sh->present_time(HC_PRESENTER::VIEW_RAW);

		// duration
			$values[] = $t->formatPeriodExtraShort($sh->get_duration(), 'hour');

		// staff
			$values[] = $sh->present_user(HC_PRESENTER::VIEW_RAW);

		// location
			$values[] = $sh->present_location(HC_PRESENTER::VIEW_RAW);

		// status
			$values[] = $sh->present_status(HC_PRESENTER::VIEW_RAW);

		/* add csv line */
			$data[] = HC_Lib::build_csv( array_values($values), $separator );
		}

	// output
		$out = join( "\n", $data );

		$file_name = isset( $this->conf['export'] ) ? $this->conf['export'] : 'export';
		$file_name .= '-' . date('Y-m-d_H-i') . '.csv';

		$this->load->helper('download');
		force_download($file_name, $out);
		return;
	}
}