<template>
	<div class="qcp-page-list">
		<page-item v-for="(page, index) in pages"
				   :key="index"
				   :id="index"
				   v-model:name="page.name"
				   v-model:slug="page.slug"
				   v-model:children="page.children"
				   :hierarchical="hierarchical" />
	</div>

	<div class="qcp-page-list-tools" v-if="pages.length > 0">
		<button type="button" @click="addPage" class="button qcp-add-page">{{ $translate('Add page') }}</button>
	</div>
</template>

<script>
	export default {
		name: 'PageList',

		props: {
			pages: {
				type: Array,
				required: true,
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
				this.pages.push( {
					name: '',
					slug: '',
					children: [],
				} );
			},
		}
	}
</script>
