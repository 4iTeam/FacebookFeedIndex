const mix = require('laravel-mix');

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
let moduleDir='core';
let publicDir='public';

(function() {
    let module='base';
    let from=[moduleDir,module,'resources'].join('/');
    let to=[publicDir].join('/');
    mix.js([from + '/js/la.js'], to + '/js/la.js')
})();

(function() {
    let module='admin';
    let from=[moduleDir,module,'resources'].join('/');
    let to=[publicDir,'vendor','admin'].join('/');
    mix.js([from + '/js/la.js'], to + '/js/la.js')
        .js(from+'/js/datatables.js',to+'/js/datatables.js');
    mix.less(from + '/less/core.less', to + '/css');
    mix.less(from + '/less/form-2columns.less', to + '/css');
    mix.less(from + '/less/role.less', to + '/css');
    mix.less(from + '/less/table.less', to + '/css');

})();
(function() {
    let to='public';
    let from='resources/assets';
    mix.less(from + '/less/app.less', to + '/css');
    mix.styles([from + '/css/style.css',from + '/css/app.css'], to + '/css/style.css');

    mix.scripts(from + '/js/global/*.js', to + '/js/global.js');
    mix.scripts(from + '/js/support/page-loader.js', to + '/js/support/page-loader.js');
    mix.scripts(from + '/js/support/list.js', to + '/js/support/list.js');
    mix.scripts(from + '/js/support/indexer.js', to + '/js/support/indexer.js');
})();

(function(){
    const { lstatSync, readdirSync,existsSync } = require('fs');
    const { join } = require('path');

    const isDirectory = source => lstatSync(source).isDirectory()
    const getDirectories = source =>
        readdirSync(source).map(name => join(source, name)).filter(isDirectory)
    const directories=getDirectories('core');
    for (let i = 0, len = directories.length; i < len; i++) {
        if( existsSync(directories[i]+'/public') ) {
            mix.copyDirectory(directories[i] + '/public', publicDir);
        }
    }
})();


