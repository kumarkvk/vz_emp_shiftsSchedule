<?php
$user_level = $user ? $user->level : 0;

$menu = HC_Html_Factory::widget('main_menu');
$menu->set_menu( $menu_conf );
$menu->set_disabled( $disabled_panels );
$menu->set_current( $this_uri );

echo $menu->render( $user_level . '/' );
?>