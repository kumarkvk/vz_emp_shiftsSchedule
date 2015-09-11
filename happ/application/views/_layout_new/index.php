<?php
$ri = $layout->param('ri');
?>
<?php
if( (! $ri) && $layout->has_partial('head') ){
	echo $layout->partial('head');
}
?>
<?php
if( $layout->has_partial('theme_header') ){
	echo $layout->partial('theme_header');
}
?>
<div id="nts">
<div class="<?php echo $ri ? 'hc-container-fluid' : 'hc-container'; ?>">
<?php if( $ri ) : ?>
<p>&nbsp;</p>
<?php endif; ?>
<div class="hc-no-print">
<?php
if( $layout->has_partial('brand') ){
	echo $layout->partial('brand');
}
if( $layout->has_partial('profile') ){
	echo $layout->partial('profile');
}
if( $layout->has_partial('menu') ){
	echo $layout->partial('menu');
}
if( $layout->has_partial('header') ){
	echo $layout->partial('header');
}
elseif( $layout->has_partial('header_ajax') ){
	echo $layout->partial('header_ajax');
}
?>
</div>
<?php
$flashdata = '';
if( $layout->has_partial('flashdata') ){
	$flashdata = $layout->partial('flashdata');
}

/* CONTENT */
if( $layout->has_partial('sidebar') ){
	$content = HC_Html_Factory::widget( 'grid' )
		->add_item( array($flashdata, $layout->partial('content')), 9 )
		->add_item( $layout->partial('sidebar'), 3 )
		;
	echo $content->render();
}
else {
	echo $flashdata;
	echo $layout->partial('content');
}
?>

</div><!-- /container -->
</div><!-- /nts -->

<?php
if( $layout->has_partial('theme_footer') ){
	echo $layout->partial('theme_footer');
}
?>

<?php if( ! $ri ) : ?>
</body>
</html>
<?php endif; ?>