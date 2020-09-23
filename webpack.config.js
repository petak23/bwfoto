const path = require('path');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

module.exports = {
  devServer: {
    publicPath: '/assets/'
  },
  entry: {
    front: ['./www/js/main.js', './www/css/front/main.css' ],
    admin: ['./www/css/admin/main.css']
  },
  mode: (process.env.NODE_ENV === 'production') ? 'production' : 'development',
  resolve: {
    extensions: ['*', '.js', '.jsx'],
    alias: {
      'vue$': 'vue/dist/vue.esm.js' // 'vue/dist/vue.common.js' for webpack 1
    },
    modules: ['node_modules']
  },
  output: {
    filename: '[name].js', //.[contenthash:8]
    path: path.join(__dirname, 'www', 'assets')
  },
  plugins: [
    new MiniCssExtractPlugin({
      filename: "[name].css" //[contenthash:8].
    })
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
              outputPath: 'images/'
            }
          }  
        ]
      },
      {
        test: /\.(woff|woff2|eot|ttf|otf)$/,
        use: [
          {
            loader: 'file-loader',
            options: {
              name: '[name].[hash:8].[ext]',
              outputPath: 'fonts/'
            }
          }
        ]
      }
    ]
  }
};
