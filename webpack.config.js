const Encore = require("@symfony/webpack-encore");

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
  Encore.configureRuntimeEnvironment(process.env.NODE_ENV || "dev");
}

Encore.copyFiles({
  from: "./assets/fonts",
  to: "fonts/[path][name].[ext]",
})
  // directory where compiled assets will be stored
  .setOutputPath("public/build/")
  // public path used by the web server to access the output path
  .setPublicPath("/build")
  // only needed for CDN's or subdirectory deploy
  //.setManifestKeyPrefix('build/')

  /*
   * ENTRY CONFIG
   *
   * Each entry will result in one JavaScript file (e.g. app.js)
   * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
   */
  .addEntry("app", "./assets/app.js")
  // ... outras configs ...
  .addStyleEntry("home", "./assets/styles/home.css")
  .addStyleEntry("login", "./assets/styles/login.css")
  .addStyleEntry("register", "./assets/styles/registration.css")
  .addStyleEntry("user", "./assets/styles/user.css")
  .addStyleEntry("categories", "./assets/styles/categories.css")
  .addStyleEntry("courses", "./assets/styles/courses.css")
  .addStyleEntry("lesson", "./assets/styles/lesson.css")
  .addStyleEntry("themes", "./assets/styles/themes.css")
  .addStyleEntry("course", "./assets/styles/course.css")
  .addStyleEntry("cart", "./assets/styles/cart.css")
  .addStyleEntry("order-success", "./assets/styles/order-success.css")
  .addStyleEntry("study", "./assets/styles/study.css")
  .addStyleEntry("certifications", "./assets/styles/certifications.css")
  .addStyleEntry("certification", "./assets/styles/certification.css")
  .addStyleEntry("404", "./assets/styles/404.css")
  .addStyleEntry("admin", "./assets/styles/admin.css")
  // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
  .splitEntryChunks()

  // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
  .enableStimulusBridge("./assets/controllers.json")

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

  // configure Babel
  // .configureBabel((config) => {
  //     config.plugins.push('@babel/a-babel-plugin');
  // })

  // enables and configure @babel/preset-env polyfills
  .configureBabelPresetEnv((config) => {
    config.useBuiltIns = "usage";
    config.corejs = "3.38";
  });

// enables Sass/SCSS support
//.enableSassLoader()

// uncomment if you use TypeScript
//.enableTypeScriptLoader()

// uncomment if you use React
//.enableReactPreset()

// uncomment to get integrity="..." attributes on your script & link tags
// requires WebpackEncoreBundle 1.4 or higher
//.enableIntegrityHashes(Encore.isProduction())

// uncomment if you're having problems with a jQuery plugin
//.autoProvidejQuery()

module.exports = Encore.getWebpackConfig();
