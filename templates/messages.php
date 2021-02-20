<?php foreach( ['messages', 'errors'] as $type ) : ?>
	<?php if( QCP()->submission_handler->$type ) : ?>
		<div class="notice notice-<?php echo $type === 'errors' ? 'error' : 'success'; ?> is-dismissible qcp-messages">
			<ul>
				<?php foreach( QCP()->submission_handler->$type as $message ) : ?>
					<li><?php echo $message; ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>
<?php endforeach; ?>
