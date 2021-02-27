<div class="wrap wp-core-ui qcp">
	<header class="qcp-header">
		<h1><?php _e( 'Quick Create Pages', 'quick-create-pages' ); ?></h1>
	</header>

	<?php \QuickCreatePages\get_template( 'messages' ); ?>

	<main class="qcp-main" id="qcp-app">
		<qcp-app />
	</main>

	<footer class="qcp-footer">
		<p><?php printf( __( 'Ask questions and report bugs on <a href="%s">GitHub</a>.', 'quick-create-pages' ), 'https://github.com/tomslominski/quick-create-pages' ); ?></p>
	</footer>
</div>
