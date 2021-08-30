const webpack = require("webpack");
const Dotenv = require('dotenv-webpack');
const path = require("path");
const MiniCssExtractPlugin = require('mini-css-extract-plugin')
const TerserPlugin = require('terser-webpack-plugin');

module.exports = [
{
    optimization: {
        minimizer: [new TerserPlugin({
            extractComments: false,
        })],
    },
    entry: {
        main: './src/js/login.js',
    },
    output: {
        filename: "js/login.js",
        path: path.resolve(__dirname, "../dist")
    },
    devtool: "source-map",
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /(node_modules)/,
                use: {
                    loader: "babel-loader",
                    options: {
                        presets: [
                            "@babel/preset-env"
                        ]
                    }
                }
            },
            {
                test: /\.(scss|css)$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    {
                        loader: "css-loader",
                        options: {
                            importLoaders: 2,
                            sourceMap: true,
                            url: false,
                        }
                    },
                    {
                        loader: 'postcss-loader',
                        options: {
                            postcssOptions: {
                                plugins: [
                                    'autoprefixer',
                                ]
                            }
                        }
                    },
                    'sass-loader'
                ],
            },
        ],
    },
    plugins: [
        new Dotenv({
            path: "./.env"
        }),
        new MiniCssExtractPlugin({
            filename: "css/login.css",
            chunkFilename: "login.css"
        }),
    ],
},
{   
    optimization: {
        minimizer: [new TerserPlugin({
            extractComments: false,
        })],
    },
    // https://webpack.js.org/configuration/experiments/
    experiments: {
        topLevelAwait: true
    },
    entry: {
        main: './src/js/payment.js',
    },
    output: {
        filename: "js/payment.js",
        path: path.resolve(__dirname, "../dist")
    },
    devtool: "source-map",
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /(node_modules)/,
                use: {
                    loader: "babel-loader",
                    options: {
                        presets: [
                            "@babel/preset-env"
                        ]
                    }
                }
            },
            {
                test: /\.(scss|css)$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    {
                        loader: "css-loader",
                        options: {
                            importLoaders: 2,
                            sourceMap: true,
                            url: false,
                        }
                    },
                    {
                        loader: 'postcss-loader',
                        options: {
                            postcssOptions: {
                                plugins: [
                                    'autoprefixer',
                                ]
                            }
                        }
                    },
                    'sass-loader'
                ],
            },
        ],
    },
    plugins: [
        new Dotenv({
            path: "./.env"
        }),
        new MiniCssExtractPlugin({
            filename: "css/payment.css",
            chunkFilename: "payment.css"
        }),
    ],
}
];