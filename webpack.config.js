let Encore = require('@symfony/webpack-encore');

Encore
// directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Add 1 entry for each "page" of your app
     * (including one that's included on every page - e.g. "app")
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if you JavaScript imports CSS.
     */.addEntry('www', './assets/www.js')
//.addEntry('page1', './assets/js/page1.js')
//.addEntry('page2', './assets/js/page2.js')

    .addStyleEntry('www_style', './assets/less/main.less')

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk().splitEntryChunks()

/*
 * FEATURE CONFIG
 *
 * Enable & configure other features below. For a full
 * list of features, see:
 * https://symfony.com/doc/current/frontend.html#adding-more-features
 */.cleanupOutputBeforeBuild()
// .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // enables Sass/SCSS support
    //.enableSassLoader()

    // enables Less support
    .enableLessLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()
;

let config = Encore.getWebpackConfig();

module.exports = config;
