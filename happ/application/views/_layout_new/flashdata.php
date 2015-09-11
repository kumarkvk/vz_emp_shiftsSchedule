<?php if( $message ) : ?>
	<div class="hc-auto-dismiss">
	<?php if( is_array($message) ) : ?>
		<ul class="hc-list-unstyled">
			<?php foreach( $message as $m ) : ?>
				<li>
					<div class="hc-alert hc-alert-success">
						<button type="button" class="hc-close" data-dismiss="alert">&times;</button>
						<?php echo $m; ?>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php else : ?>
		<div class="hc-alert hc-alert-success">
			<button type="button" class="hc-close" data-dismiss="alert">&times;</button>
			<?php echo $message;?>
		</div>
	<?php endif; ?>
	</div>
<?php endif; ?>

<?php if( $error ) : ?>
	<div>
	<?php
	$danger = HC_Html_Factory::widget('alert')
		->add_attr('class', 'alert-danger')
		->set_items( $error )
		;
	echo $danger->render();
	?>
	</div>
<?php endif; ?>

<?php
if( isset($debug_message) && $debug_message ){
	if( ! is_array($debug_message) ){
		$debug_message = array( $debug_message );
	}

	$debug_message = HC_Html_Factory::widget('list')
		->add_attr('class', 'list-unstyled')
		->add_attr('class', 'list-separated')
		->add_attr('class', 'list-bordered')
		->set_items( $debug_message )
		;

	$debug = HC_Html_Factory::widget('alert')
		->add_attr('class', 'alert-warning-o')
		->set_items( $debug_message )
		;
	echo $debug->render();
}
?>