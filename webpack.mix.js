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
const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
    .vue(3)
    .extract()
    .disableNotifications()
    .version()
    .postCss('resources/css/app.css', 'public/css', [
        //
    ]);

mix.webpackConfig(webpack =>{
    return {
        resolve:{
            alias:{
                videojs: 'video.js',
                WaveSurfer: 'wavesurfer.js',
                RecordRTC: 'recordtrc'
            }
        },
        plugins:[
            new webpack.ProvidePlugin({
                videojs: 'video.js/dist/video.cjs.js',
                RecordRTC: 'recordtrc'
            })
        ]
    }
});
