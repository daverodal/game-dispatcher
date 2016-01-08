var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {

    mix.copy('node_modules/node-bourbon/node_modules/bourbon/app/assets/stylesheets/', 'resources/assets/sass/');
    mix.copy('node_modules/node-bourbon/node_modules/bourbon/app/assets/stylesheets/', 'resources/assets/sass/');
    mix.copy('resources/assets/images', 'public/images');
    mix.sass('app.scss');
});
