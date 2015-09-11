<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Loginlog_HC_Controller extends _Front_HC_Controller
{
	function user_zoom_menubar( $object )
	{
		$acl = HC_App::acl();
		if( ! $acl->set_object($object)->can('loginlog::view') ){
			return;
		}
		return $this->render( 
			'loginlog/user_zoom_menubar',
			array(
				'object'	=> $object,
				)
			);
	}

	function index( $user_id = 0 )
	{
		$model = HC_App::model('loginlog');
		$model
			->include_related( 'user', array('id', 'email', 'first_name', 'last_name', 'active'), TRUE, TRUE )
			;

		if( $user_id ){
			if( is_object($user_id) ){
				$user_id = $user_id->id;
			}

			$model
				->where_related( 'user', 'id', $user_id )
				;
			$user = HC_App::model('user');
			$user
				->where('id', $user_id )
				->get()
				;
		}

		$model->get();

	/* render view */
		$this->layout->set_partial(
			'header', 
			$this->render( 
				'loginlog/_header',
				array(
					)
				)
			);

			$this->layout->set_partial(
			'content', 
			$this->render( 
				'loginlog/index',
				array(
					'user_id'	=> $user_id,
					'entries' 	=> $model,
					)
				)
			);

		$this->layout();
	}
}

/* End of file customers.php */
/* Location: ./application/controllers/admin/categories.php */