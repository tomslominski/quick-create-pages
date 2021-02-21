<template>
	<div class="qcp-page">
		<div class="qcp-page-main">
			<div class="qcp-form-field">
				<label :for="this.getIdAttribute('name')">Name</label>
				<input type="text" :name="this.getNameAttribute('name')" :id="this.getIdAttribute('name')" :value="this.name" @input="$emit('update:name', $event.target.value)">
			</div>

			<div class="qcp-form-field">
				<label :For="this.getIdAttribute('slug')">Slug</label>
				<input type="text" :name="this.getNameAttribute('slug')" :id="this.getIdAttribute('slug')" :value="this.slug"  @input="$emit('update:slug', $event.target.value)">
			</div>

			<div class="qcp-form-field qcp-form-field-submit" v-if="(this.depth > 1 || this.siblings.length > 1) || (this.hierarchical && this.depth < 5)">
				<button type="button" @click="deletePage" class="qcp-delete-page" v-if="this.depth > 1 || this.siblings.length > 1">Delete page</button>

				<button type="button" @click="addChild" class="button qcp-add-child-page" v-if="this.hierarchical && this.depth < 5">Add child page</button>
			</div>
		</div>

		<div class="qcp-page-children" v-if="this.children.length > 0">
			<page-list :pages="this.children" :hierarchical="hierarchical" />
		</div>
	</div>
</template>

<script>
	export default {
		props: {
			id: {
				type: Number,
				required: true,
			},
			name: {
				type: String,
				required: true,
			},
			slug: {
				type: String,
				required: true,
			},
			children: {
				type: Array,
				required: true,
			},
			hierarchical: {
				type: Boolean,
			}
		},

		computed: {
			/**
			 * Calculate the depth of the current Page.
			 *
			 * @returns {number}
			 */
			depth: function() {
				return this.getParentPageIds().length;
			},

			/**
			 * Get other pages at the same depth from the parent Page List.
			 */
			siblings: function() {
				return this.$parent.pages;
			}
		},

		emits: ['update:name', 'update:slug'],

		methods: {
			/**
			 * Add child page below the current page.
			 */
			addChild() {
				this.children.push( {
					name: '',
					page: '',
					children: []
				} );
			},

			/**
			 * Delete current page.
			 */
			deletePage() {
				this.siblings.forEach( (sibling, index) => {
					if( this.id === sibling.id ) {
						this.siblings.splice(index, 1);
					}
				});
			},

			/**
			 * Returns page IDs for this page and all parents.
			 *
			 * @param {boolean} includeCurrent Whether to include the current page object ID.
			 * @returns {int[]} Array of page IDs.
			 */
			getParentPageIds(includeCurrent = true) {
				let currentComponent = this;
				let ids = [];

				while( currentComponent && currentComponent?.$parent ) {
					ids.push(currentComponent.id);
					currentComponent = currentComponent?.$parent?.$parent;
				}

				if( !includeCurrent ) {
					ids.splice(0, 1);
				}

				return ids.reverse();
			},

			/**
			 * Get the name HTML attribute for this page, for the specified field.
			 *
			 * @param {string} name Name of field to get attribute for.
			 * @returns {string}
			 */
			getNameAttribute(name) {
				let attribute = 'qcp[pages]';
				const parents = this.getParentPageIds(false);

				// Children fields for parent pages
				parents.forEach(id => {
					attribute += `[${id}][children]`;
				});

				// ID for current page
				attribute += `[${this.id}][${name}]`;

				return attribute;
			},

			/**
			 * Get the id HTML attribute for this page, for the specified field.
			 *
			 * @param {string} name Name of field to get attribute for.
			 * @returns {string}
			 */
			getIdAttribute(name) {
				return this.getNameAttribute(name)
					.replace(/\[|\]/g, '-')
					.replaceAll('--', '-')
					.replace(/-$/, '');
			},
		}
	}
</script>
