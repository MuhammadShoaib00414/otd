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

mix.webpackConfig({ resolve: { fallback: { "crypto": require.resolve("crypto-browserify"), "stream": require.resolve("stream-browserify") } } })

mix.sass('resources/sass/admin/main.scss', 'public/css/admin');

mix.js('resources/js/main.js', 'public/js')
    .vue()
    .browserSync({
        open: 'external',
        host: 'otd-diversity.test',
        proxy: 'otd-diversity.test'
    })
    .version();