const mix = require('laravel-mix');

mix.webpackConfig(webpack => {
	return {
		plugins: [
			new webpack.DefinePlugin({
				__VUE_OPTIONS_API__: true,
				__VUE_PROD_DEVTOOLS__: false
			})
		]
	};
})
	.js('src/js/app.js', 'assets/js')
	.vue()
	.sass('src/scss/style.scss', 'assets/css')
	.sourceMaps();
