const dotenvExpand = require('dotenv-expand')
dotenvExpand(require('dotenv').config({ path: '../../.env' /*, debug: true*/ }))

const mix = require('laravel-mix')
require('laravel-mix-merge-manifest')

mix.setPublicPath('../../public').mergeManifest()

mix
  .sass(__dirname + '/Resources/assets/sass/app.scss', 'css/user.css')
  .js(__dirname + '/Resources/assets/js/app.js', 'js/user.js')
  .js(__dirname + '/Resources/assets/js/user.js', 'js/user_user.js')

mix.js(__dirname + '/Resources/assets/js/permission.js', 'js/user_permission.js')
  .sass(__dirname + '/Resources/assets/sass/permission.scss', 'css/user_permission.css')

mix.js(__dirname + '/Resources/assets/js/role.js', 'js/user_role.js')
  .sass(__dirname + '/Resources/assets/sass/role.scss', 'css/user_role.css')

  .copyDirectory(
    __dirname + '/Resources/assets/images',
    '../../public/assets/images',
  )

if (mix.inProduction()) {
  mix.version()
}
