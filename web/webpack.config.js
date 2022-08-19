const Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    .copyFiles([
        {from: './node_modules/ckeditor/', to: 'ckeditor/[path][name].[ext]', pattern: /\.(js|css)$/, includeSubdirectories: false},
        {from: './node_modules/ckeditor/adapters', to: 'ckeditor/adapters/[path][name].[ext]'},
        {from: './node_modules/ckeditor/lang', to: 'ckeditor/lang/[path][name].[ext]'},
        {from: './node_modules/ckeditor/plugins', to: 'ckeditor/plugins/[path][name].[ext]'},
        {from: './node_modules/ckeditor/skins', to: 'ckeditor/skins/[path][name].[ext]'}
    ])

    .copyFiles({
        from: './assets/images',

        // optional target path, relative to the output dir
        to: 'images/[path][name].[ext]',

        // if versioning is enabled, add the file hash too
        to: 'images/[path][name].[hash:8].[ext]',

        // only copy files matching this pattern
        pattern: /\.(png|jpg|jpeg)$/
    })

    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('app', './assets/app.js')
    .addEntry('homepage', './assets/entries/homepage.js')
    .addEntry('login', './assets/entries/login.js')
    .addEntry('register', './assets/entries/register.js')
    .addEntry('register_waiting', './assets/entries/register_waiting.js')
    .addEntry('register_confirmation', './assets/entries/register_confirmation.js')
    .addEntry('reset_password', './assets/entries/reset_password.js')
    .addEntry('request_reset_password', './assets/entries/request_reset_password.js')
    .addEntry('check_email_reset_password', './assets/entries/check_email_reset_password.js')
    .addEntry('reset_password_successful', './assets/entries/reset_password_successful.js')
    .addEntry('space_member', './assets/entries/space_member.js')
    .addEntry('edit_member', './assets/entries/edit_member.js')
    .addEntry('edit_password', './assets/entries/edit_password.js')
    .addEntry('change_email', './assets/entries/change_email.js')
    .addEntry('notif_change_email', './assets/entries/notif_change_email.js')
    .addEntry('confirm_change_email', './assets/entries/confirm_change_email.js')
    .addEntry('contact_page', './assets/entries/contact_page.js')
    .addEntry('notif_contact_page', './assets/entries/notif_contact_page.js')
    .addEntry('about_page', './assets/entries/about_page.js')
    .addEntry('legal_page', './assets/entries/legal_page.js')
    .addEntry('add_post', './assets/entries/add_post.js')
    .addEntry('forum', './assets/entries/forum.js')
    .addEntry('post_forum', './assets/entries/post_forum.js')
    .addEntry('admin-field-message_collection','./assets/entries/admin-field-message_collection.js')
    .addEntry('admin-msg-signal-types','./assets/entries/admin-msg-signal-types.js')
    .addEntry('follow_subject','./assets/entries/follow_subject.js')
    .addEntry('my_contribution','./assets/entries/my_contribution.js')
    .addEntry('error','./assets/entries/error.js')

    // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
    .enableStimulusBridge('./assets/controllers.json')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    .configureBabel((config) => {
        config.plugins.push('@babel/plugin-proposal-class-properties');
    })

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })

    // enables Sass/SCSS support
    .enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment if you use React
    //.enableReactPreset()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    //.autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
