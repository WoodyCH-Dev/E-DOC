const mix = require('laravel-mix');

mix.js(['resources/js/app.js', 'node_modules/flatpickr/dist/flatpickr.js'], 'public/js').vue();
mix.sass('resources/sass/app.scss', 'public/css');
mix.stylus('node_modules/flatpickr/src/style/flatpickr.styl', 'public/css');
mix.copy('resources/images', 'public/images', false);
