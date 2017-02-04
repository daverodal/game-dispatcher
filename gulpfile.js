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

    /*mix.scripts(['../../../bower_components/angularjs/angular.js',
        '../../../bower_components/angular-sanitize/angular-sanitize.js',
        '../../../bower_components/angular-modal-service/dst/angular-modal-service.js',
        '../../../bower_components/jquery/dist/jquery.js',
        '../../../bower_components/jquery-ui/jquery-ui.js']);*/

    mix.copy('bower_components/jquery-ui/jquery-ui.js', 'public/js');


    mix.copy('bower_components/font-awesome/scss/', 'resources/assets/sass/font-awesome');
    mix.copy('bower_components/font-awesome/fonts/', 'public/fonts/font-awesome');

    mix.copy('bower_components/bourbon/app/assets/stylesheets/', 'resources/assets/sass/bourbon');
    mix.copy('bower_components/neat/app/assets/stylesheets/', 'resources/assets/sass/neat');

    mix.copy('bower_components/angular-right-click/src/ng-right-click.js', 'public/js');

    mix.copy('resources/assets/images', 'public/images');
    if(!elixir.config.production){
        mix.sass('app.scss');
        mix.sass('vendor/wargame/rebellion/rebellion/rebellionAll.scss');
    }else{
        console.log("SKIPPING SASS!");
    }

    mix.browserify('main.js', 'public/javascripts/main.js');
    mix.browserify('gameMain.js', 'public/javascripts/gameMain.js');
    mix.browserify('ngGameMain.js', 'public/javascripts/ngGameMain.js');

    mix.copy('resources/assets/js/sync.js', 'public/js');
    mix.version(['css/app.css', 'javascripts/main.js', 'javascripts/gameMain.js','javascripts/ngGameMain.js','vendor/wargame/mollwitz/css',
        'vendor/wargame/medieval/css','vendor/wargame/tmcw/css','vendor/wargame/spi/css']);


});