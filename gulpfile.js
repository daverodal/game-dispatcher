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

    mix.copy('bower_components/jquery-ui/jquery-ui.js', 'public/js');

    mix.copy(['vendor/daverodal/wargaming/Wargame/common-sync.js',
        'vendor/daverodal/wargaming/Wargame/global-header.js',
        'vendor/daverodal/wargaming/Wargame/fix-header.js',
        'vendor/daverodal/wargaming/Wargame/global-funcs.js',
        'vendor/daverodal/wargaming/Wargame/initialize.js',
        'vendor/daverodal/wargaming/Wargame/Sync.js',
    ], 'resources/assets/js/imported');

    mix.copy(['vendor/daverodal/rebellion/Sass/'],
    'resources/assets/sass/vendor/wargame/rebellion/');

    mix.copy(['vendor/daverodal/Medieval/Medieval/game-controller.js',
        'vendor/daverodal/Medieval/Medieval/ng-global-header.js',
    ], 'resources/assets/js/imported');

    mix.copy('node_modules/font-awesome/fonts/', 'public/fonts/font-awesome');

    mix.copy('bower_components/angular-right-click/src/ng-right-click.js', 'public/js');

    mix.copy('resources/assets/images', 'public/images');
    mix.copy('resources/assets/js/images', 'public/js');

    if(!elixir.config.production){
        mix.sass('app.scss');
        mix.sass('vendor/wargame/rebellion/rebellionAll.scss');
    }else{
        console.log("SKIPPING SASS!");
    }

    mix.browserify('main.js', 'public/javascripts/main.js');
    mix.browserify('gameMain.js', 'public/javascripts/gameMain.js');
    mix.browserify('ngGameMain.js', 'public/javascripts/ngGameMain.js');

    mix.copy('resources/assets/js/sync.js', 'public/js');
    mix.version(['css/app.css', 'javascripts/main.js', 'javascripts/gameMain.js','javascripts/ngGameMain.js','vendor/wargame/mollwitz/css',
        'vendor/wargame/medieval/css','vendor/wargame/tmcw/css','vendor/wargame/spi/css', 'vendor/wargame/nta/css', 'vendor/wargame/tactical/css']);


});