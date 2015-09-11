<?php
$out = HC_Html_Factory::element('div');
$nav = HC_Html_Factory::widget('list')
	->add_attr('class', array('nav', 'nav-pills'))
	->add_attr('class', array('text-smaller'))
	->add_attr('class', array('nav-condensed'))
	->add_attr('style', 'margin-bottom: 0.5em;')
	;

if( $user && $user->id ){
	$user_title = $user->present_title();
	$user_title .= ' [';
	if( $login_with == 'username' )
		$user_title .= $user->username;
	else
		$user_title .= $user->email;
	$user_title .= ']';

	$link_profile = 'auth/profile';
	$nav->add_item(
		HC_Html_Factory::widget('titled', 'a')
			->add_attr('href', HC_Lib::link($link_profile))
			->add_child( $user_title )
		);

	$auth_user = $this->auth->user();
	$acl = HC_App::acl();
	$acl_user = $acl->user();
	$ri = HC_Lib::ri();

	if( $auth_user->id != $acl_user->id ){
		if( $ri == 'wordpress' ){
			if( $auth_user->level >= $auth_user->_const('LEVEL_MANAGER') ){
				$nav->add_divider();

				$app = HC_App::app();
				$admin_url = get_admin_url() . 'admin.php?page=' . $app;

				$nav->add_item(
					HC_Html_Factory::widget('titled', 'a')
						->add_attr('href', $admin_url)
						->add_child( HC_Html::icon('cogs') )
						->add_child(
							HC_Html_Factory::element('span')
								->add_attr('class', 'hidden-xs')
								->add_child( HCM::__('Admin Area') )
							)
					);
			}
		}
	}

	$nav->add_divider();
	$nav->add_item(
		HC_Html_Factory::widget('titled', 'a')
			->add_attr('href', HC_Lib::link('auth/logout'))
			->add_child(
				HC_Html_Factory::element('span')
					->add_attr('class', 'hidden-xs')
					->add_child( HCM::__('Log Out') )
				)
			->add_child( HC_Html::icon('sign-out') )
		);
}
else {
	if( $this_method != 'login' ){
		$nav->add_item(
			HC_Html_Factory::widget('titled', 'a')
				->add_attr('href', HC_Lib::link('auth/login'))
				->add_child( HC_Html::icon('sign-in') )
				->add_child(
					HC_Html_Factory::element('span')
						->add_attr('class', 'hidden-xs')
						->add_child( HCM::__('Log In') )
					)
			);
	}
	else {
		$nav->add_item(
			HC_Html_Factory::widget('titled', 'a')
				->add_attr('href', HC_Lib::link())
				->add_child( HC_Html::icon('arrow-left') )
				->add_child( HCM::__('Back To Start Page') )
			);
	}
}

$out->add_child( $nav );
echo $out->render();
?>