let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/assets/js/app.js', 'public/js/app.min.js')
    .js('resources/assets/js/components/dashboard.js', 'public/js/dashboard.min.js')
    .copy('resources/assets/js/components/chart.js', 'public/js/chart.min.js')
    .sass('resources/assets/sass/app.scss', 'public/css/app.min.css')
    .options({
        processCssUrls: false
    })
    .version();
