const mix = require('laravel-mix');
const path = require('path');
const rimraf = require('rimraf');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css', [
        //
    ]);

/*
|--------------------------------------------------------------------------
| I2C WEB
|--------------------------------------------------------------------------
|
*/

// Remove existing generated assets from public folder
rimraf(path.resolve('public/css/web'), () => { });
rimraf(path.resolve('public/js/web'), () => { });
rimraf(path.resolve('public/images/web'), () => { });

mix.copyDirectory('resources/web-assets/images', 'public/images/web');
mix.copyDirectory('resources/web-assets/css/icons', 'public/css/web/icons');

// CSS
mix.styles('resources/web-assets/style.css', 'public/css/web/style.bundle.css');
mix.styles('resources/web-assets/css/font-icons.css', 'public/css/web/font-icons.bundle.css');

mix.styles([
    'resources/css/web/global-custom.css',
    'resources/web-assets/css/custom.css',
], 'public/css/web/custom.bundle.css');

// mix.styles('resources/web-assets/css/swiper.css', 'public/css/web/swiper.css');

// JS
mix.scripts([
    'resources/web-assets/js/plugins.min.js',
    'resources/web-assets/js/functions.bundle.js',
], 'public/js/web/script.bundle.js')
    .scripts([
        'resources/js/web/plugins/formvalidation/dist/js/FormValidation.js',
        'resources/js/web/plugins/formvalidation/dist/js/plugins/Bootstrap5.js',
    ], 'public/js/web/form-validation.bundle.js');

mix.js('resources/js/web/global-custom.js', 'public/js/web/global-custom.bundle.js')
    .js('resources/js/web/contact-us-form.js', 'public/js/web/contact-us.bundle.js');
