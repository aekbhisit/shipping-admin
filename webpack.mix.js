const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

 mix.js('resources/assets/js/accordiation.js', 'public/assets/js')
 mix.js('resources/assets/js/advancedform-rtl.js', 'public/assets/js')
 mix.js('resources/assets/js/advancedform.js', 'public/assets/js')
 mix.js('resources/assets/js/anchor.min.js', 'public/assets/js')
 mix.js('resources/assets/js/apexcharts-rtl.js', 'public/assets/js')
 mix.js('resources/assets/js/bootsnav.js', 'public/assets/js')
 mix.js('resources/assets/js/chart-dygraph.js', 'public/assets/js')
 mix.js('resources/assets/js/chart.js', 'public/assets/js')
 mix.js('resources/assets/js/chart2_test.js', 'public/assets/js')
 mix.js('resources/assets/js/chartjs.js', 'public/assets/js')
 mix.js('resources/assets/js/charts.js', 'public/assets/js')
 mix.js('resources/assets/js/chat.js', 'public/assets/js')
 mix.js('resources/assets/js/custom.js', 'public/assets/js')
 mix.js('resources/assets/js/echarts.js', 'public/assets/js')
 mix.js('resources/assets/js/flot.js', 'public/assets/js')
 mix.js('resources/assets/js/form-elements.js', 'public/assets/js')
 mix.js('resources/assets/js/formeditor.js', 'public/assets/js')
 mix.js('resources/assets/js/forms.js', 'public/assets/js')
 mix.js('resources/assets/js/fullcalendar.js', 'public/assets/js')
 mix.js('resources/assets/js/index1', 'public/assets/js')
 mix.js('resources/assets/js/index2-rtl.js', 'public/assets/js')
 mix.js('resources/assets/js/index2.js', 'public/assets/js')
 mix.js('resources/assets/js/index3.js', 'public/assets/js')
 mix.js('resources/assets/js/index4.js', 'public/assets/js')
 mix.js('resources/assets/js/index5.js', 'public/assets/js')
 mix.js('resources/assets/js/jvectormap.js', 'public/assets/js')
 mix.js('resources/assets/js/left-menu.js', 'public/assets/js')
 mix.js('resources/assets/js/morris.js', 'public/assets/js')
 mix.js('resources/assets/js/nvd3.js', 'public/assets/js')
 mix.js('resources/assets/js/othercharts.js', 'public/assets/js')
 mix.js('resources/assets/js/popover.js', 'public/assets/js')
 mix.js('resources/assets/js/prefixfree.min.js', 'public/assets/js')
 mix.js('resources/assets/js/rangeslider.js', 'public/assets/js')
 mix.js('resources/assets/js/respond.min.js', 'public/assets/js')
 mix.js('resources/assets/js/rickshaw.js', 'public/assets/js')
 mix.js('resources/assets/js/select2.js', 'public/assets/js')
 mix.js('resources/assets/js/sparkline.js', 'public/assets/js')
 mix.js('resources/assets/js/stiky.js', 'public/assets/js')
 mix.js('resources/assets/js/summernote.js', 'public/assets/js')
 mix.js('resources/assets/js/sweet-alert.js', 'public/assets/js')
 mix.js('resources/assets/js/wizard.js', 'public/assets/js')
 // mix.js('resources/assets/js/mwz.js', 'public/assets/js')
 mix.sass('resources/assets/scss/style.scss', 'public/assets/css')
 mix.sass('resources/assets/custom-theme/boxed.scss', 'public/assets/css')
 mix.sass('resources/assets/custom-theme/closed-darksidemenu.scss', 'public/assets/css')
 mix.sass('resources/assets/custom-theme/closed-sidemenu.scss', 'public/assets/css')
 mix.sass('resources/assets/custom-theme/combined.scss', 'public/assets/css')
 mix.sass('resources/assets/custom-theme/dark-boxed.scss', 'public/assets/css')
 mix.sass('resources/assets/custom-theme/dark-style.scss', 'public/assets/css')
 mix.sass('resources/assets/custom-theme/demo-styles.scss', 'public/assets/css')
 mix.sass('resources/assets/custom-theme/easy-responsive-tabs-dark.scss', 'public/assets/css')
 mix.sass('resources/assets/custom-theme/easy-responsive-tabs.scss', 'public/assets/css')
 mix.sass('resources/assets/custom-theme/full-sidemenu-dark.scss', 'public/assets/css')
 mix.sass('resources/assets/custom-theme/full-sidemenu.scss', 'public/assets/css')
 mix.sass('resources/assets/custom-theme/sidemenu-icon-dark.scss', 'public/assets/css')
 mix.sass('resources/assets/custom-theme/sidemenu-icon.scss', 'public/assets/css')
 mix.sass('resources/assets/custom-theme/skin-mode.scss', 'public/assets/css')
 // mix.sass('resources/assets/custom-theme/mwz.scss', 'public/assets/css')
 mix.sass('resources/assets/custom-theme/colors/color-skins/color.scss', 'public/assets/css/colors')
 mix.sass('resources/assets/custom-theme/colors/fonts/font1.scss', 'public/assets/css/fonts')
 mix.sass('resources/assets/custom-theme/colors/fonts/font2.scss', 'public/assets/css/fonts')
 mix.sass('resources/assets/custom-theme/colors/fonts/font3.scss', 'public/assets/css/fonts')
 mix.sass('resources/assets/custom-theme/colors/fonts/font4.scss', 'public/assets/css/fonts')
 mix.sass('resources/assets/custom-theme/colors/fonts/font5.scss', 'public/assets/css/fonts')
 mix.copyDirectory('resources/assets/images', 'public/assets/images')
 mix.copyDirectory('resources/assets/plugins', 'public/assets/plugins')
 mix.copyDirectory('resources/assets/fonts', 'public/assets/fonts')

 mix.options({
     processCssUrls: false
 });

 mix.browserSync('http://127.0.0.1:8000');
