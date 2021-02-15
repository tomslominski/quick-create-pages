const mix = require('laravel-mix');

mix.js('src/js/app.js', 'assets/js')
	.vue()
	.sass('src/scss/style.scss', 'assets/css')
	.sourceMaps();
