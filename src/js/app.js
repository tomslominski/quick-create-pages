/* global qcpConfig */

import { createApp } from 'vue';
import PageItemComponent from './components/page-item';
import PageListComponent from './components/page-list';

createApp({
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
			selectedPostType: qcpConfig.selectedPostType ?? 'page'
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

			if( this.hierarchical ) {
				return `${top + children} ${top + children === 1 ? 'page' : 'pages'}, including ${top} top level ${top === 1 ? 'page' : 'pages'} and ${children} child ${children === 1 ? 'page' : 'pages'} will be created.`;
			} else {
				return `${top + children} ${top + children === 1 ? 'page' : 'pages'} will be created.`;
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
					return page.children.length > 0;
				}

				return false;
			})();

			if( newValue.hierarchical ) {
				this.selectedPostType = newValue.slug;
			} else {
				if( hasChildren ) {
					if( confirm( 'Changing from a hierarchical post type to a non-hierarchical post type will erase all child pages. Continue?' ) ) {
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
})
.component('page-item', PageItemComponent)
.component('page-list', PageListComponent)
.mount("#qcp-app");
