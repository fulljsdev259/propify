require('mix-env-file')
require('laravel-mix-alias')
require('laravel-mix-auto-extract')

const mix = require('laravel-mix');
// const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;
const CompressionPlugin = require('compression-webpack-plugin')
const CleanWebpackPlugin = require('clean-webpack-plugin')

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

mix.options({
        // extractVueStyles: true,
        clearConsole: true,
        processCssUrls: true
    })
    .combine([
        'public/css/app.css',
        'public/css/fontello.css',
        'public/css/arial.css',
        'public/css/times_new_roman.css',
    ], 'public/css/app.css')
    // .combine([
    //     'node_modules/animate.css/animate.min.css'
    // ], 'public/css/vendor.css')
    .sass('resources/sass/app.scss', 'public/css/app.css')
    .postCss('resources/css/fontello.css', 'public/css/fontello.css')
    .js('resources/js/index.js', 'public/js/app.js')
    .alias({
        '@': '/resources/js',
        'img': '/resources/img',
        'css': '/resources/css',
        'sass': '/resources/sass',
        'svg': '/resources/svg',
        'fonts': '/resources/fonts',
        'boot': '/resources/js/boot',
        'plugins': '/resources/js/plugins',
        'components': '/resources/js/components',
        'helpers': '/resources/js/helpers',
        'lang': '/resources/js/lang',
        'routes': '/resources/js/routes',
        'layouts': '/resources/js/layouts',
        'middlewares': '/resources/js/middlewares',
        'guards': '/resources/js/guards',
        'mixins': '/resources/js/mixins',
        'store': '/resources/js/store',
        'views': '/resources/js/views',
        'utils': '/resources/js/utils',
        'config': '/resources/js/config'
    })
    .webpackConfig({
        devServer: {
            open: false,
            // hot: true,
            inline: true,
            overlay: true,
            progress: true,
            compress: true,
            stats: {
                colors: true,
                hash: false,
                version: false,
                timings: false,
                assets: false,
                chunks: false,
                modules: false,
                reasons: false,
                children: false,
                source: false,
                errors: false,
                errorDetails: false,
                warnings: false,
                publicPath: false
            }
        }
    })
    .version()
    .disableNotifications();
    // .env(process.env.ENV_FILE);

if (mix.inProduction()) {
    mix.options({
            uglify: {
                // exclude: /\/vendor.js(\?.*)?$/,
                uglifyOptions: {
                    compress: {
                        ecma: 6,
                        drop_console: true
                    },
                    output: {
                        ecma: 6,
                        comments: false
                    }
                }
            }
        })
        .webpackConfig({
            output: {
                filename: '[name].js?id=[hash]',
                chunkFilename: 'js/[chunkhash].js?id=[hash]'
            },
            plugins: [
                new CleanWebpackPlugin(['public/js/**/*']),
                new CompressionPlugin({
                    asset: "[path].gz[query]",
                    algorithm: "gzip",
                    test: /\.js(\?.*)?$|\.css(\?.*)?$|\.html(\?.*)?$|\.woff2(\?.*)?$|\.ttf(\?.*)?$|\.svg(\?.*)?$|\.woff(\?.*)?$|\.eot(\?.*)?$/,
                })
            ]
        })
} else {
    mix.webpackConfig({
        devtool: 'source-map',
        output: {
            filename: '[name].js',
            chunkFilename: 'js/[name].js',
        },
        /*plugins: [
            new BundleAnalyzerPlugin({
                openAnalyzer: false
            })
        ]*/
    }).sourceMaps();
}

mix.autoExtract()