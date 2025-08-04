const dotenvExpand = require('dotenv-expand')
dotenvExpand(require('dotenv').config({ path: '../../.env' /*, debug: true*/ }))

const mix = require('laravel-mix')
require('laravel-mix-merge-manifest')

mix.setPublicPath('../../public').mergeManifest()

mix
  .sass(__dirname + '/Resources/assets/sass/app.scss', 'css/setting.css')
  .js(__dirname + '/Resources/assets/js/app.js', 'js/setting.js')
  .js(__dirname + '/Resources/assets/js/app.slug.js', 'js/setting.slug.js')
  .js(__dirname + '/Resources/assets/js/app.tag.js', 'js/setting.tag.js')

if (mix.inProduction()) {
  mix.version()
}
