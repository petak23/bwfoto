// Node
const path = require('path');

// Webpack
const webpack = require("webpack");
const merge = require("webpack-merge");

// Other
const devMode = process.env.NODE_ENV !== "production";

// Vue
const VUE_VERSION = require("vue/package.json").version;
const VUE_LOADER_VERSION = require("vue-loader/package.json").version;

// Webpack plugins
const TerserPlugin = require("terser-webpack-plugin");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const {VueLoaderPlugin} = require("vue-loader");
const FriendlyErrorsWebpackPlugin = require('friendly-errors-webpack-plugin');

// Config
const ROOT_PATH = __dirname;
const CACHE_PATH = ROOT_PATH + "/temp/webpack";

var AssetsPlugin = require('assets-webpack-plugin');
 
module.exports = {
  mode: devMode ? "development" : "production",
  context: path.join(ROOT_PATH, "app/assets"),
  devServer: {
    publicPath: '/dist/'
  },
  entry: {
    front: [path.join(ROOT_PATH, "app/assets/front/css/main.css"), path.join(ROOT_PATH, "app/assets/front/js/main.js")],
    admin: [path.join(ROOT_PATH, "app/assets/admin/css/main.css"), path.join(ROOT_PATH, "app/assets/admin/js/main.js")]
    //texyla: [path.join(ROOT_PATH, "www/texyla/css/main.css"), path.join(ROOT_PATH, "www/texyla/texyla-init.js")]
  },
  resolve: {
    extensions: ['*', '.js', '.jsx'], //		extensions: [".js", ".vue", ".ts", ".tsx"],
    alias: {
      'vue$': 'vue/dist/vue.esm.js' ,
      '@': path.resolve(__dirname, "app/assets")
    },
    modules: ['node_modules']
  },
  output: {
    filename: '[name].[contenthash:8].js',
    path: path.join(ROOT_PATH, 'www/dist'),
    //publicPath: "/dist/"
  },
  plugins: [
    // enable vue-loader to use existing loader rules for other module types
		new VueLoaderPlugin(),
    
    // fix legacy jQuery plugins which depend on globals
		new webpack.ProvidePlugin({
			$: "jquery",
			jQuery: "jquery",
			"window.jQuery": "jquery",
			"window.$": "jquery",
			Popper: ["popper.js", "default"]
		}),
    
    new MiniCssExtractPlugin({
      filename: "[name].[contenthash:8].css"
    }),
    new AssetsPlugin({ // Pre aplikaciu filename: '[name].[contenthash:8].[ext]' a prepojenie s nette
      includeManifest: 'manifest',
      path: path.join(ROOT_PATH, 'www/dist')
    }),
    
    // human webpack errors
		new FriendlyErrorsWebpackPlugin()
  ],
  module: {
    noParse: /^(vue|vue-router|vuex|vuex-router-sync)$/,
		rules: [
			{
				test: /\.vue$/,
				use: [
					...!devMode ? [] : [
						{
							loader: 'cache-loader',
							options: {
								cacheDirectory: path.join(CACHE_PATH, "vue-loader"),
								cacheIdentifier: [
									process.env.NODE_ENV || 'development',
									webpack.version,
									VUE_VERSION,
									VUE_LOADER_VERSION
								].join('|')
							}
						}
					],
					...[{
						loader: 'vue-loader',
						options: {
							compilerOptions: {
								preserveWhitespace: false
							},
							cacheDirectory: path.join(CACHE_PATH, "vue-loader"),
							cacheIdentifier: [
								process.env.NODE_ENV || 'development',
								webpack.version,
								VUE_VERSION,
								VUE_LOADER_VERSION
							].join('|')
						}
					}]
				]
			},
      {
        test: /\.css$/,
        use: [
          MiniCssExtractPlugin.loader,
          'css-loader'
        ]
      },
      {
        test: /\.(png|svg|jpe?g|gif|webp)$/,
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

if (process.env.NODE_ENV === "development") {
	const development = {
//		output: {
//			globalObject: 'this'
//		},
//		devServer: {
//			host: WEBPACK_DEV_SERVER_HOST,
//			port: WEBPACK_DEV_SERVER_PORT,
//			disableHostCheck: true,
//			contentBase: path.join(ROOT_PATH, "www"),
//			headers: {
//				"Access-Control-Allow-Origin": "*",
//				"Access-Control-Allow-Headers": "*",
//			},
//			stats: "errors-only",
//			hot: true,
//			inline: true,
//			proxy: {
//				"/": `http://${WEBPACK_DEV_SERVER_PROXY_HOST}:${WEBPACK_DEV_SERVER_PROXY_PORT}`
//			}
//		},
	};

	module.exports = merge(module.exports, development);
}

if (process.env.NODE_ENV === "production") {
	const production = {
//		output: {
//			filename: '[name].[contenthash:8].bundle.js',
//			chunkFilename: '[name].[contenthash:8].chunk.js'
//		},
		devtool: "none",
		optimization: {
			minimizer: [
				new TerserPlugin({
					test: /\.m?js(\?.*)?$/i,
					chunkFilter: () => true,
					warningsFilter: () => true,
					extractComments: false,
					sourceMap: true,
					cache: true,
					cacheKeys: defaultCacheKeys => defaultCacheKeys,
					parallel: true,
					include: undefined,
					exclude: undefined,
					minify: undefined,
					terserOptions: {
						output: {
							comments: /^\**!|@preserve|@license|@cc_on/i
						},
						compress: {
							arrows: false,
							collapse_vars: false,
							comparisons: false,
							computed_props: false,
							hoist_funs: false,
							hoist_props: false,
							hoist_vars: false,
							inline: false,
							loops: false,
							negate_iife: false,
							properties: false,
							reduce_funcs: false,
							reduce_vars: false,
							switches: false,
							toplevel: false,
							typeofs: false,
							booleans: true,
							if_return: true,
							sequences: true,
							unused: true,
							conditionals: true,
							dead_code: true,
							evaluate: true
						},
						mangle: {
							safari10: true
						}
					}
				})
			]
		},
//		plugins: [
//			// optimize CSS files
//			new OptimizeCSSAssetsPlugin(),
//		],
	};

	module.exports = merge(module.exports, production);
}