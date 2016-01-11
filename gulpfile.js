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

    mix.copy('bower_components/angularjs/angular.js', 'public/js');
    mix.copy('bower_components/angular-sanitize/angular-sanitize.js', 'public/js');
    mix.copy('bower_components/angular-modal-service/dst/angular-modal-service.js', 'public/js');

    mix.copy('bower_components/jquery/dist/jquery.js', 'public/js');
    mix.copy('bower_components/jquery-ui/jquery-ui.js', 'public/js');

    mix.copy('bower_components/font-awesome/scss/', 'resources/assets/sass/font-awesome');
    mix.copy('bower_components/font-awesome/fonts/', 'public/fonts/font-awesome');

    mix.copy('bower_components/bourbon/app/assets/stylesheets/', 'resources/assets/sass/bourbon');
    mix.copy('bower_components/neat/app/assets/stylesheets/', 'resources/assets/sass/neat');
    mix.copy('resources/assets/images', 'public/images');
    mix.sass('app.scss');
});
