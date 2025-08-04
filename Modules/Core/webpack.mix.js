const dotenvExpand = require('dotenv-expand');
dotenvExpand(require('dotenv').config({ path: '../../.env'/*, debug: true*/}));

const mix = require('laravel-mix');
require('laravel-mix-merge-manifest');

mix.setPublicPath('../../public').mergeManifest();

mix.js(__dirname + '/Resources/assets/js/app.js', 'js/admin.js')
    .sass( __dirname + '/Resources/assets/sass/app.scss', 'css/admin.css');

mix.js(__dirname + '/Resources/assets/js/front.js', 'js/front.js')
    .sass( __dirname + '/Resources/assets/sass/front.scss', 'css/front.css');

mix.js(__dirname + '/Resources/assets/js/lang.th.js', 'js/lang.th.js')
mix.js(__dirname + '/Resources/assets/js/admin_menu.js', 'js/admin_menu.js')
    .sass(__dirname + '/Resources/assets/sass/admin_menu.scss', 'css/admin_menu.css');
if (mix.inProduction()) {
    mix.version();
}
