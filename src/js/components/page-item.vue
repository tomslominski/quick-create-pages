<template>
	<div class="qcp-page">
		<div class="qcp-page-main">
			<div class="qcp-form-field">
				<label :for="page.getIdAttribute('name')">Name</label>
				<input type="text" :name="page.getNameAttribute('name')" :id="page.getIdAttribute('name')" v-model="page.name">
			</div>

			<div class="qcp-form-field">
				<label :For="page.getIdAttribute('slug')">Slug</label>
				<input type="text" :name="page.getNameAttribute('slug')" :id="page.getIdAttribute('slug')" v-model="page.slug">
			</div>

			<div class="qcp-form-field qcp-form-field-submit" v-if="(page.depth > 1 || parentPageList.length > 1) || (hierarchical && page.depth < 5)">
				<button type="button" @click="deletePage" class="qcp-delete-page" v-if="page.depth > 1 || parentPageList.length > 1">Delete page</button>

				<button type="button" @click="addChild" class="button qcp-add-child-page" v-if="hierarchical && page.depth < 5">Add child page</button>
			</div>
		</div>

		<div class="qcp-page-children" v-if="page.children.length > 0">
			<page-list :pages="page.children" :parentPage="page" :hierarchical="hierarchical" />
		</div>
	</div>
</template>

<script>
	import Page from './../classes/page';

	export default {
		props: {
			page: {
				type: Page,
				required: true,
			},
			parentPageList: {
				type: Array,
				required: true,
			},
			hierarchical: {
				type: Boolean,
			}
		},

		methods: {
			/**
			 * Add child page below the current page.
			 */
			addChild() {
				this.page.children.push( new Page('', '', [], this.page, this.page.children.length) );
			},

			/**
			 * Delete current page.
			 */
			deletePage() {
				this.parentPageList.forEach( (sibling, index) => {
					if( this.page.id === sibling.id ) {
						this.parentPageList.splice(index, 1);
					}
				});
			}
		}
	}
</script>
