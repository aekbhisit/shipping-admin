const dotenvExpand = require('dotenv-expand')
dotenvExpand(require('dotenv').config({ path: '../../.env' /*, debug: true*/ }))

const mix = require('laravel-mix')
require('laravel-mix-merge-manifest')

mix.setPublicPath('../../public').mergeManifest()

mix
  .sass(__dirname + '/Resources/assets/sass/app.scss', 'css/statement.css')
  .js(__dirname + '/Resources/assets/js/transfer.js', 'js/statement.transfer.js')
  .js(__dirname + '/Resources/assets/js/statement.js', 'js/statement.js')
  .js(__dirname + '/Resources/assets/js/temp.js', 'js/statement.temp.js')
  .js(__dirname + '/Resources/assets/js/sms.js', 'js/statement.sms.js')

if (mix.inProduction()) {
  mix.version()
}
