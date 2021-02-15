/**
 * Class describing a single Page item.
 */
export default class Page {
	/**
	 * Set up object properties.
	 */
	constructor(name, slug, children, parentPage, id = 0) {
		this.name = name;
		this.slug = slug;
		this.children = children;
		this.parentPage = parentPage;
		this.id = id;
		this.depth = this.calculateDepth();
	}

	/**
	 * Returns page IDs for this page and all parents.
	 *
	 * @param {boolean} includeCurrent Whether to include the current page object ID.
	 * @returns {int[]} Array of page IDs.
	 */
	getParentPageIds(includeCurrent = true) {
		let page = this;
		let ids = [];

		while( typeof page !== 'undefined' && page !== null ) {
			ids.push(page.id);
			page = page.parentPage;
		}

		if( !includeCurrent ) {
			ids.splice(0, 1);
		}

		return ids.reverse();
	}

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
	}

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
	}

	/**
	 * Get Vue v-for component key.
	 *
	 * @returns {string}
	 */
	getKey() {
		return 'page-' + this.getParentPageIds().reverse().join( '-' );
	}

	/**
	 * Calculate the depth of the current Page.
	 *
	 * @returns {number}
	 */
	calculateDepth() {
		return this.getParentPageIds().length;
	}
}
