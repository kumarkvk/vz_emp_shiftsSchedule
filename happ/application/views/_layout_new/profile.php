<?php
$out = HC_Html_Factory::element('div');
$nav = HC_Html_Factory::widget('list')
	->add_attr('class', array('nav', 'nav-pills'))
	;

if( $user && $user->id ){
	$user_title = $user->present_title();
	$user_title .= ' [';
	if( $login_with == 'username' )
		$user_title .= $user->username;
	else
		$user_title .= $user->email;
	$user_title .= ']';

	$nav->add_item(
		HC_Html_Factory::widget('titled', 'a')
			->add_attr('href', HC_Lib::link('auth/profile'))
			->add_child( $user_title )
		);
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