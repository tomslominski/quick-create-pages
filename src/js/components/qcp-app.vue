<template>
	<form :action="formAction" method="POST">
		<div class="qcp-primary">
			<page-list :pages="pages" :hierarchical="hierarchical" />
		</div>

		<div class="qcp-secondary">
			<div class="qcp-secondary-text">
				<div class="qcp-form-field">
					<label for="qcp-post_type">{{ $translate('Post type') }}</label>

					<select name="qcp[post_type]" id="qcp-post_type" @change="onPostTypeChange" ref="qcp-post_type">
						<option v-for="postType in postTypes " :value="postType.slug">{{ postType.name }}</option>
					</select>
				</div>

				<p>{{ preCreationMessage }}</p>
			</div>

			<div class="qcp-secondary-actions">
				<input type="hidden" name="qcp[nonce]" :value="nonce">

				<input type="submit" :value="$translatePlaceholders('Create {0}', this.postTypes[this.selectedPostType].pluralName.toLowerCase())" class="button button-primary">
			</div>
		</div>
	</form>
</template>

<script>
/* global qcpConfig */

export default {
	name: 'QcpApp',

	data() {
		return {
			pages: qcpConfig.pages ?? [
				{
					name: '',
					slug: '',
					children: [],
				},
			],
			postTypes: qcpConfig.postTypes,
			selectedPostType: qcpConfig.selectedPostType ?? 'page',
			formAction: qcpConfig.formAction,
			nonce: qcpConfig.nonce,
		}
	},

	computed: {
		/**
		 * Whether the currently selected post type is hierarchical.
		 *
		 * @returns {boolean}
		 */
		hierarchical() {
			return this.postTypes[this.selectedPostType].hierarchical;
		},

		/**
		 * The message displayed in the submit box.
		 *
		 * @returns {string}
		 */
		preCreationMessage() {
			let top = this.pages.length;
			let children = 0;

			const count = pages => {
				pages.forEach( page => {
					children++;

					if( page.children.length > 0 ) {
						count( page.children );
					}
				} );
			}

			count(this.pages);

			children = children - top;

			const singular = this.postTypes[this.selectedPostType].name.toLowerCase();
			const plural = this.postTypes[this.selectedPostType].pluralName.toLowerCase();

			if( this.hierarchical ) {
				return this.$translatePlaceholders('{0} {1}, including {2} top level {3} and {4} child {5} will be created.',
					top + children,
					top + children === 1 ? singular : plural,
					top,
					top === 1 ? singular : plural,
					children,
					children === 1 ? singular : plural
				);
			} else {
				return this.$translatePlaceholders('{0} {1} will be created.',
					top + children,
					top + children === 1 ? singular : plural
				);
			}
		}
	},

	/**
	 * Set default post type on the post type field on mount.
	 */
	mounted() {
		this.$refs['qcp-post_type'].value = this.selectedPostType;
	},

	methods: {
		/**
		 * Manage post type field change.
		 *
		 * @param {Object} event
		 */
		onPostTypeChange(event) {
			const oldValue = this.postTypes[this.selectedPostType];
			const newValue = this.postTypes[event.target.value];
			const hasChildren = (() => {
				for( let page of this.pages ) {
					if( page.children.length > 0 ) {
						return true;
					}
				}

				return false;
			})();

			if( newValue.hierarchical ) {
				this.selectedPostType = newValue.slug;
			} else {
				if( hasChildren ) {
					if( confirm( this.$translate( 'Changing from a hierarchical post type to a non-hierarchical post type will erase all child pages. Continue?' ) ) ) {
						this.pages.forEach( page => {
							page.children = [];
						} );

						this.selectedPostType = newValue.slug;
					} else {
						event.target.value = oldValue.slug;
					}
				} else {
					this.selectedPostType = newValue.slug;
				}
			}
		}
	}
}
</script>
