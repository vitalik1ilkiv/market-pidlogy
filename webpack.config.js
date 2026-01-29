const path = require("path");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const BrowserSyncPlugin = require("browser-sync-webpack-plugin");

const mode = process.env.NODE_ENV || "development";
const devMode = mode === "development";
const target = devMode ? "web" : "browserslist";
const devtool = devMode ? "source-map" : undefined;

module.exports = {
  mode,
  target,
  devtool,

  // entry: path.resolve(__dirname, 'src', 'index.js'),

  entry: {
    scripts: "./assets/index.js",
    styles: "./assets/scss/styles.scss",
  },

  output: {
    path: path.resolve("./dist"),
    clean: true,
    // filename: 'index.[contenthash].js',
    filename: "[name].js",
  },

  plugins: [
    new MiniCssExtractPlugin({
      filename: "[name].css",
    }),
    new BrowserSyncPlugin(
      {
        // Адреса локального WordPress сайту
        proxy: "http://localhost/market-pidlogy",
        // Файли для відстеження змін
        files: ["**/*.php", "dist/*.js", "dist/*.css"],
        // Не відкривати браузер автоматично (можна змінити на true)
        open: false,
        // Порт для BrowserSync
        port: 3000,
        // Затримка перед перезавантаженням
        reloadDelay: 0,
        // Показувати повідомлення в браузері
        notify: true,
      },
      {
        // Webpack плагін не перезавантажує сторінку сам
        reload: false,
      },
    ),
  ],

  module: {
    rules: [
      {
        test: /\.(s|sc)ss$/i,
        exclude: "/node_modules/",
        use: [MiniCssExtractPlugin.loader, "css-loader", "sass-loader"],
      },
      {
        test: /\.js$/,
        loader: "babel-loader",
        exclude: "/node_modules/",
      },
    ],
  },
};
