const dotenvExpand = require('dotenv-expand')
dotenvExpand(require('dotenv').config({ path: '../../.env' /*, debug: true*/ }))

const mix = require('laravel-mix')
require('laravel-mix-merge-manifest')

mix.setPublicPath('../../public').mergeManifest()

mix
  .sass(__dirname + '/Resources/assets/sass/app.scss', 'css/job.css')
  .js(__dirname + '/Resources/assets/js/app.js', 'js/job.js')
  .js(__dirname + '/Resources/assets/js/manual_credit.js', 'js/manual_credit.js')
  .js(__dirname + '/Resources/assets/js/deposit.js', 'js/deposit.js')
  .js(__dirname + '/Resources/assets/js/withdraw.js', 'js/withdraw.js')
  .js(__dirname + '/Resources/assets/js/pusher.js', 'js/pusher.js')

if (mix.inProduction()) {
  mix.version()
}
