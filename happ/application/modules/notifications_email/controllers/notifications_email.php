<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Notifications_Email_HC_Controller extends _Front_HC_Controller
{
	function add_form_inputs( $parent_object = NULL )
	{
		$acl = HC_App::acl();
		if( $parent_object ){
			if( ! $acl->set_object($parent_object)->can('notification_email::skip') ){
				return;
			}
		}

		$form = HC_Lib::form();
		$form->set_inputs(
			array(
				'notifications_email_skip'	=> 'checkbox',
				)
			);

		return $this->render(
			'notifications_email/add_form_inputs',
			array(
				'form'			=> $form,
				)
			);
	}

	function api_insert( $post )
	{
		$notifications_email_skip = isset($post['notifications_email_skip']) ? $post['notifications_email_skip'] : FALSE;
		if( $notifications_email_skip ){
			$messages = HC_App::model('messages');
			$messages->remove_engine('email');
		}

		$return = TRUE;
		return $return;
	}
}