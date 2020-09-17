const path = require('path');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

module.exports = {
  devServer: {
    publicPath: '/assets/'
  },
  entry: [ './www/js/main.js', './www/css/front/main.css' ],
  mode: (process.env.NODE_ENV === 'production') ? 'production' : 'development',
  resolve: {
    extensions: ['*', '.js', '.jsx'],
    alias: {
      'vue$': 'vue/dist/vue.esm.js' // 'vue/dist/vue.common.js' for webpack 1
    },
    modules: ['node_modules']
  },
  output: {
    filename: 'bundle.js',
    path: path.join(__dirname, 'www', 'assets')
  },
  plugins: [
    new MiniCssExtractPlugin({
      filename: "style.css"
    }),
  ],
  module: {
    rules: [
      {
        test: /\.css$/,
        use: [
          MiniCssExtractPlugin.loader,
          'css-loader'
        ]
      },
      {
        test: /\.(png|svg|jpg|gif)$/,
        use: [
          {
            loader: 'file-loader',
            options: {
              name: '[name].[hash:8].[ext]',
              outputPath: 'assets/'
            }
          }  
        ]
      }
    ]
  }
};
