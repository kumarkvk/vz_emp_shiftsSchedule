<?php
include_once( NTS_SYSTEM_APPPATH . 'third_party/MX/Controller.php' );

//class MY_Controller extends CI_Controller 
class MY_HC_Base_Base_Controller extends MX_Controller 
{
	public $is_module = FALSE;
	public $default_params = array();
	public $layout = NULL;
	protected $is_setup = FALSE;

	function __construct()
	{
		parent::__construct();

		if( defined('NTS_DEVELOPMENT') ){
			if( ! ($this->input->is_ajax_request() OR $this->is_module()) ){
				$this->output->enable_profiler(TRUE);
			}
		}

		$this->load->database();

		$ri = HC_Lib::ri();

		if( ! $this->is_setup() ){
			$setup_redirect = 'setup';
			if( $ri )
				$setup_redirect = $ri . '/setup';

			$this->redirect( $setup_redirect );
			exit;
			}

		$this->load->library( array('session', 'hc_modules') );

	/* add module models paths for autoloading */

		$extensions = HC_App::extensions();
		$acl = HC_App::acl();

		$look_in_dirs = $this->config->look_in_dirs();
		foreach( $look_in_dirs as $ldir ){
			if( class_exists('Datamapper') ){
				Datamapper::add_model_path( $ldir );
			}
			$this->load->add_package_path( $ldir );
			$extensions->add_dir( $ldir );
			$acl->add_dir( $ldir );
		}

		$extensions->init();
		$acl->init();

	/* reload config paths */
		$app_conf = HC_App::app_conf();
		$this->load->library( 'hc_modules' );

	/* events and notifiers */
		$this->load->library( array('hc_events', 'hc_email') );
		$this->hc_email->from = $app_conf->get('email_from');
		$this->hc_email->fromName = $app_conf->get('email_from_name');

	// conf
		$this->load->library( 'hc_auth', NULL, 'auth' );

		$user = $this->auth->user();
		$acl->set_user( $user );

		$CI =& ci_get_instance();
		$current_url = $CI->config->site_url($CI->uri->uri_string());
		$this->session->set_flashdata('referrer', $current_url);

		$this->layout = new HC_View_Layout;
	}

	function my_parent()
	{
		$return = '';
		if( $this->calling_parent() ){
			$return = $this->calling_parent();
		}
		else {
			$return = $this->get_current_slug();
		}
		return $return;
	}

	function render( $file, $params = array() )
	{
		/* add some useful params */
		if( $this->calling_parent() ){
			$_calling_parent = $this->calling_parent() . '/_pass';
		}
		else {
			$_calling_parent = $this->uri->segment(1);
		}

		if( ! isset($params['_calling_parent']) ){
			$params['_calling_parent'] = $_calling_parent;
		}

		return $this->load->view(
			$file,
			$params,
			TRUE
			);
	}

/* access level to notes and other */
	function access_levels_to( $what )
	{
		$return = array();
		return $return;
	}

	function check_level( $require_level )
	{
		if( 
			! (
			$this->auth && 
			$this->auth->user() &&
			($this->auth->user()->level >= $require_level)
			)
		)
		{
			$this->session->set_flashdata('error', 'You are not allowed to access this page');
			$this->redirect('');
			exit;
		}
	}

	public function calling_parent()
	{
		$return = '';
		if( $this->is_module && is_string($this->is_module) ){
			$return = $this->is_module;
		}
		return $return;
	}

	function layout( $template_file = NULL )
	{
		$template_dir = '_layout_new';
		$ri = HC_Lib::ri();

		$is_module = $this->is_module();
		$is_ajax = $this->input->is_ajax_request();

		if( $is_module OR $is_ajax ){
			$template_file = 'index_module';
		}
		else {
			if( ! $template_file ){
				$template_file = 'index';
			}

		/* theme */
			$theme_dir = $GLOBALS['NTS_APPPATH'] . '/../theme';
			if( file_exists($theme_dir) ){
				$theme_head = $theme_dir . '/head.php';
				if( file_exists($theme_head) ){
					$this->layout->set_partial(
						'theme_head', 
						$this->render( 
							'../../theme/head',
							array(
								)
							)
						);
				}

				$theme_header = $theme_dir . '/header.php';
				if( file_exists($theme_header) ){
					$this->layout->set_partial(
						'theme_header', 
						$this->render( 
							'../../theme/header',
							array(
								)
							)
						);
				}

				$theme_footer = $theme_dir . '/footer.php';
				if( file_exists($theme_footer) ){
					$this->layout->set_partial(
						'theme_footer', 
						$this->render( 
							'../../theme/footer',
							array(
								)
							)
						);
				}
			}

			$this->layout->set_param('ri', $ri);

		/* head */
			// if( ! $ri ){
				$page_title = $this->config->item('nts_app_title');
				$this->layout->set_partial(
					'head',
					$this->render( 
						$template_dir . '/head',
						array(
							'layout'		=> $this->layout,
							'page_title'	=> $page_title,
							)
						)
					);
			// }

		/* menu & profile */
			$user = NULL;
			if( 
				$this->auth && 
				$this->auth->check() &&
				$this->auth->user() &&
				$this->auth->user()->active
				){
				$user = $this->auth->user();
			}
			$user = $this->auth->user();

		/* menu */
			if( (1 OR $user) && (! $this->is_setup) ){
				$menu_conf = $this->config->item('menu');
				$disabled_panels = $this->config->item('disabled_panels');
				$this_uri = $this->uri->uri_string();
				$user_level = $user ? $user->level : 0;

				$acl = HC_App::acl();
				$auth_user = $acl->user();

				$this->layout->set_partial(
					'menu', 
					$this->render( 
						$template_dir . '/menu',
						array(
							'menu_conf'			=> $menu_conf,
							'disabled_panels'	=> $disabled_panels,
							'this_uri'			=> $this_uri,
							'user'				=> $auth_user,
							)
						)
					);
			}

		/* profile */
			$app_conf = HC_App::app_conf();
			if( (1 OR (! $ri)) && (! $this->is_setup) ){
				$this_method = $this->router->fetch_method();
				$login_with = $app_conf->get('login_with');
				$this->layout->set_partial(
					'profile',
					$this->render( 
						$template_dir . '/profile',
						array(
							'this_method'	=> $this_method,
							'login_with'	=> $login_with,
							'user'			=> $user,
							)
						)
					);
			}

		/* brand */
			$brand_title = $this->config->item('nts_app_title');
			$brand_url = $this->config->item('nts_app_url');
			$hc_app_version = $this->config->item('hc_app_version');
			if( (! $ri) && strlen($brand_title) ){
				$this->layout->set_partial(
					'brand', 
					$this->render( 
						$template_dir . '/brand',
						array(
							'brand_title'	=> $brand_title,
							'brand_url'		=> $brand_url,
							'ri'			=> $ri,
							'app_version'	=> $hc_app_version,
							)
						)
					);
			}
		}

	/* flashdata */
		if( ! $is_module ){
			$this->layout->set_partial(
				'flashdata', 
				$this->render( 
					$template_dir . '/flashdata',
					array(
						'message'		=> $this->session->flashdata('message_ajax') ? $this->session->flashdata('message_ajax') : $this->session->flashdata('message'),
						'debug_message'	=> $this->session->flashdata('debug_message'),
						'error'			=> $this->session->flashdata('error_ajax') ? $this->session->flashdata('error_ajax') : $this->session->flashdata('error'),
						)
					)
				);

			$this->layout->set_partial(
				'flashdata_ajax',
				$this->render( 
					$template_dir . '/flashdata',
					array(
						'message'		=> $this->session->flashdata('message_ajax'),
						'debug_message'	=> NULL,
						'error'			=> $this->session->flashdata('error_ajax'),
						)
					)
				);
		}

	/* final output */
		$this->layout->set_template( $template_dir . '/' . $template_file );

	/* return */
		$this->load->view( 
			$this->layout->template(),
			array(
				'layout'	=> $this->layout
				)
			);

/*
		return $this->render(
			$this->layout->template(),
			array(
				'layout'	=> $this->layout
				)
			);
*/
	}

	function is_module()
	{
		return $this->is_module;
	}

	function get_current_slug()
	{
		$parts = array();
		$parts[] = $this->uri->segment(1);
		$segment2 = $this->uri->segment(2);
		if( $segment2 != 'index' ){
			$segment2 = 'index';
		}
		$parts[] = $segment2;
		$return = join( '/', $parts );
		return $return;
	}

	function check_setup( $db = NULL )
	{
		$return = FALSE;
		if( $db === NULL ){
			$db = $this->db;
		}
		else {
			// echo "DB IS SET!";
		}

		if( $db->table_exists('conf') ){
			$return = TRUE;
		}
		return $return;
	}

	function is_setup( $db = NULL )
	{
		$return = TRUE;

		if( $this->check_setup($db) ){
			$return = TRUE;
		}
		else {
			$return = FALSE;
			if( $this->is_setup )
				$return = TRUE;
		}
		return $return;
	}

	function fix_path( $path )
	{
		$return = str_replace( '-', '_', $path );
		return $return;
	}

	// function redirect( $to, $parent = 0 )
	function redirect( $to = '', $parent_refresh = array() )
	{
		$parent = 0;
		if( $this->input->is_ajax_request() ){
//			if( $this->input->post() )
//			{
				// clear flash
				$this->session->set_flashdata( 'message', NULL );
				$this->session->set_flashdata( 'error', NULL );
//			}

			if( (! is_array($to)) && ($to == '-referrer-') ){
			}
			else {
			// already starts with http:// ?
				if( ! HC_Lib::is_full_url($to) ){
					$to = HC_Lib::link($to);
					$to = $to->url();
				}
			}

			$out = array(
				'redirect'	=> $to,
				'parent'	=> $parent,
				'parent_refresh'	=> $parent_refresh,
//				'message'		=> $this->session->flashdata('message'),
//				'debug_message'	=> $this->session->flashdata('debug_message'),
//				'error'			=> $this->session->flashdata('error'),
				);

			$this->output->set_content_type('application/json');
			$this->output->enable_profiler(FALSE);
			echo json_encode($out);
			hc_ci_before_exit();
			exit;
//			return;
		}
		else {
			if( (! is_array($to)) && ($to == '-referrer-') ){
				$to = ( ! isset($_SERVER['HTTP_REFERER']) OR $_SERVER['HTTP_REFERER'] == '') ? '' : trim($_SERVER['HTTP_REFERER']);
			}
			HC_Lib::redirect($to);
			return;
		}
		return;
	}

	protected function _check_model( $model, $redirect_to = '' )
	{
		if( ! $model->exists() ){
			$this->session->set_flashdata( 
				'message',
				join( ': ', array( HCM::__('Object not found'), get_class($model), $model->id) )
				);
			$this->redirect( $redirect_to );
			return FALSE;
		}
		return TRUE;
	}
}

class MY_HC_Base_Controller extends MY_HC_Base_Base_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->config->load('menu', TRUE, TRUE );
	}
}