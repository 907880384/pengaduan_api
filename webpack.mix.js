const mix = require('laravel-mix');

// mix.react('resources/js/app.js', 'public/js')
//     .sass('resources/sass/app.scss', 'public/css');

// mix.react('resources/js/auth.js', 'public/js');
// mix.sass('resources/sass/auth.scss', 'public/css');

//Mix Of Auth
mix.styles([
    'node_modules/bootstrap/dist/css/bootstrap.min.css',
    'node_modules/bootstrap-social/bootstrap-social.css',
    'resources/assets/css/style.css',
    'resources/assets/js/custom.js'
], 'public/css/auth.css').sourceMaps();

mix.js([
    'resources/js/app.js',
    'node_modules/bootstrap/dist/js/bootstrap.min.js',
    'node_modules/jquery.nicescroll/dist/jquery.nicescroll.min.js',
    'node_modules/moment/moment.js',
    'resources/assets/js/stisla.js',
    'resources/assets/js/scripts.js',
    'resources/assets/js/custom.js'
], 'public/js/auth.js');


//Mix Of Globes
mix.styles([
    'node_modules/bootstrap/dist/css/bootstrap.min.css',
    'node_modules/bootstrap-social/bootstrap-social.css',
    'node_modules/select2/dist/css/select2.min.css',
    'node_modules/jqvmap/dist/jqvmap.min.css',
    'node_modules/summernote/dist/summernote-bs4.css',
    'node_modules/owl.carousel/dist/assets/owl.carousel.min.css',
    'node_modules/owl.carousel/dist/assets/owl.theme.default.min.css',
    'node_modules/datatables/media/css/jquery.dataTables.min.css',
    'node_modules/datatables.net-bs4/css/dataTables.bootstrap4.min.css',
    'node_modules/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css',
    'node_modules/weathericons/css/weather-icons.min.css',
    'resources/assets/css/style.css',
    'resources/assets/css/components.css',
    'resources/assets/js/custom.js'
], 'public/css/app.css');

mix.js([
    'resources/js/app.js',
    'node_modules/bootstrap/dist/js/bootstrap.min.js',
    'node_modules/select2/dist/js/select2.full.min.js',
    'node_modules/jquery.nicescroll/dist/jquery.nicescroll.min.js',
    'node_modules/jqvmap/dist/jqvmap.min.css',
    'node_modules/datatables/media/js/jquery.dataTables.min.js',
    'node_modules/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
    'node_modules/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js',
    'node_modules/jquery-sparkline/jquery.sparkline.min.js',
    'node_modules/chart.js/dist/Chart.min.js',
    'node_modules/owl.carousel/dist/owl.carousel.min.js',
    'node_modules/summernote/dist/summernote-bs4.js',
    'node_modules/chocolat/dist/js/jquery.chocolat.min.js',

    'resources/assets/js/stisla.js',
    'resources/assets/js/scripts.js',
    'resources/assets/js/custom.js'
], 'public/js/app.js').sourceMaps();

mix.webpackConfig({ node: { fs: 'empty' }});