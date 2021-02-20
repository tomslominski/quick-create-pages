<div class="wrap wp-core-ui" id="qcp-app">
	<h1><?php _e( 'Quick Create Pages', 'quick-create-pages' ); ?></h1>

	<?php \QuickCreatePages\get_template( 'messages.php' ); ?>

	<form action="<?php echo admin_url( 'tools.php?page=quick-create-pages' ); ?>" method="POST" class="qcp-container">
		<div class="qcp-main">
			<page-list :pages="pages" :parent-page="null" :hierarchical="hierarchical" />
		</div>

		<div class="qcp-secondary">
			<div class="qcp-secondary-text">
				<div class="qcp-form-field">
					<label for="qcp-post_type">Post type</label>

					<select name="qcp[post_type]" id="qcp-post_type" @change="onPostTypeChange" ref="qcp-post_type">
						<option v-for="postType in postTypes " :value="postType.slug">{{ postType.name }}</option>
					</select>
				</div>

				<p>{{ preCreationMessage }}</p>
			</div>

			<div class="qcp-secondary-actions">
				<input type="hidden" name="qcp[nonce]" value="<?php echo wp_create_nonce( 'qcp_create_pages' ); ?>">

				<input type="submit" value="<?php _e( 'Create pages', 'quick-create-pages' ); ?>" class="button button-primary">
			</div>
		</div>
	</form>
</div>
