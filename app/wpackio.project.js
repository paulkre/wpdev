const { resolveEntry, resolveThemeVersion } = require("..");

module.exports = {
  // Project Identity
  appName: "wpdev", // Unique name of your project
  type: "theme", // Plugin or theme
  slug: "wpdev", // Plugin or Theme slug, basically the directory name under `wp-content/<themes|plugins>`
  // Used to generate banners on top of compiled stuff
  bannerConfig: {
    name: "WPDev",
    author: "Paul Kretschel",
    link: "https://github.com/paulkre/wpdev",
    version: resolveThemeVersion(),
    license: "MIT",
  },
  // Files we need to compile, and where to put
  files: [
    // If this has length === 1, then single compiler
    {
      name: "app",
      entry: resolveEntry("./src/UI"),
      // Extra webpack config to be passed directly
      webpackConfig: (config, merge) => {
        // merge the new module.rules with webpack-merge api
        return merge(config, {});
      },
    },
    // If has more length, then multi-compiler
  ],
  // Output path relative to the context directory
  // We need relative path here, else, we can not map to publicPath
  outputPath: "dist",
  // Project specific config
  hasReact: false,
  hasSass: false,
  hasLess: false,
  hasFlow: false,
  // Externals
  // <https://webpack.js.org/configuration/externals/>
  externals: {
    // jquery: "jQuery"
  },
  // Webpack Aliases
  // <https://webpack.js.org/configuration/resolve/#resolve-alias>
  alias: undefined,
  // Show overlay on development
  errorOverlay: true,
  // Auto optimization by webpack
  // Split all common chunks with default config
  // <https://webpack.js.org/plugins/split-chunks-plugin/#optimization-splitchunks>
  // Won't hurt because we use PHP to automate loading
  optimizeSplitChunks: true,
  // Usually PHP and other files to watch and reload when changed
  watch: ["./*.php", "./src/**/*.php", "./page-templates/**/*.php"],
  // Files that you want to copy to your ultimate theme/plugin package
  // Supports glob matching from minimatch
  // @link <https://github.com/isaacs/minimatch#usage>
  packageFiles: [
    "src/**/*.{php,mustache}",
    "page-templates/**/*.php",
    "vendor/**",
    "dist/**",
    "*.php",
    "README.md",
    "languages/**",
    "LICENSE",
    "*.css",
    "static/**",
    "screenshot.png",
  ],
  // Path to package directory, relative to the root
  packageDirPath: "package",
};
