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

// mix.js('resources/js/app.js', 'public/js')
//     .postCss('resources/css/app.css', 'public/css', [
//         //
//     ]);

/*
|--------------------------------------------------------------------------
| I2C WEB
|--------------------------------------------------------------------------
|
*/


// Remove existing generated assets from public folder
// rimraf(path.resolve('public/css/web'), () => { });
// rimraf(path.resolve('public/js/web'), () => { });
// rimraf(path.resolve('public/images/web'), () => { });
rimraf(path.resolve('public/web-build'), () => { });

mix.copy('resources/web-assets/favicon.ico', 'public/favicon.ico')
    .copyDirectory('resources/web-assets/images', 'public/web-build/images')
    .copyDirectory('resources/web-assets/css/icons', 'public/web-build/css/icons');

// CSS
mix.styles('resources/web-assets/style.css', 'public/web-build/style.bundle.css')
    // .styles('resources/web-assets/css/font-icons.css', 'public/web-build/css/font-icons.bundle.css')
    .styles('resources/web-assets/css/swiper.css', 'public/web-build/css/swiper.css')
    .styles([
        'resources/css/web/global-custom.css',
        'resources/web-assets/custom/custom.css',
    ], 'public/web-build/css/custom.bundle.css');

mix.copy('resources/web-assets/css/font-icons.css', 'public/web-build/css/font-icons.css');

// JS
mix.scripts([
    'resources/web-assets/js/plugins.min.js',
    'resources/web-assets/js/functions.bundle.js',
], 'public/web-build/js/script.bundle.js')
    .scripts([
        'resources/js/web/plugins/formvalidation/dist/js/FormValidation.js',
        'resources/js/web/plugins/formvalidation/dist/js/plugins/Bootstrap5.js',
    ], 'public/web-build/js/form-validation.bundle.js');

mix.js([
    'resources/js/web/global-custom.js',
    'resources/web-assets/custom/custom.js',
], 'public/web-build/js/global-custom.bundle.js')
    .js('resources/js/web/contact-us-form.js', 'public/web-build/js/contact-us.bundle.js')
    .js('resources/js/web/business-lead-form.js', 'public/web-build/js/business-lead.bundle.js');
