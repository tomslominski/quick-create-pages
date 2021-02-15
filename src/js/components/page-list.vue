<template>
	<div class="qcp-page-list">
		<page-item v-for="page in pages" :page="page" :parent-page-list="pages" :key="page.getKey()" :hierarchical="hierarchical" />
	</div>

	<div class="qcp-page-list-tools" v-if="pages.length > 0">
		<button type="button" @click="addPage" class="button qcp-add-page">Add page</button>
	</div>
</template>

<script>
	import Page from "../classes/page";

	export default {
		props: {
			pages: {
				type: Array,
				required: true,
			},
			parentPage: {
				type: Page,
				required: false,
			},
			hierarchical: {
				type: Boolean,
			}
		},

		methods: {
			/**
			 * Add page to the end of current page list.
			 */
			addPage() {
				this.pages.push( new Page('', '', [], this.parentPage, this.pages.length) );
			},
		}
	}
</script>
