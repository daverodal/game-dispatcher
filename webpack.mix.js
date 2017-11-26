let mix = require('laravel-mix');
let webpack = require('webpack');

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
mix.webpackConfig({
    plugins: [
        new webpack.ProvidePlugin({
            $: 'jquery',
            jQuery: 'jquery',
            'window.jQuery': 'jquery',
            'window.$': 'jquery',
            Popper: ['popper.js', 'default']
        })
    ],
    module: {
        loaders: [
            { test: /\.(jpe?g|gif|png|svg|woff|ttf|wav|mp3|m4a)$/, loader: "file" }
        ]
    }});
mix.copy('resources/assets/images','public/images');
mix.js('resources/assets/js/app.js', 'public/js').version();
mix.js('resources/assets/js/main.js', 'public/javascripts/main.js')
    .js('resources/assets/js/gameMain.js', 'public/javascripts/gameMain.js')
    .js('resources/assets/js/ngGameMain.js', 'public/javascripts/ngGameMain.js')
   .sass('resources/assets/sass/app.scss', 'public/css').version();
mix.version(['public/vendor/css/', 'public/vendor/javascripts']);

