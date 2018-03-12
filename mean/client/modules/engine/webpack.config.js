const path = require('path');
const webpack = require('webpack');
const ExtractTextPlugin = require ('extract-text-webpack-plugin');

module.exports = {
    entry: {
        'tid-engine': path.resolve(__dirname, './ng/tid-app-entry-point.js'),
        'vendors.min': path.resolve(__dirname, './vendors.js')
    },
    output: {
        path: path.resolve(__dirname, 'build'),
        publicPath: '/build/',
        filename: '[name].js'
    },
    resolve: {
        alias: {
            angular: path.resolve(__dirname, '../../vendors/angular.1.2.28.min.js')
        },
        modules: ['./ng']
    },
    externals: {jquery: 'jQuery'},
    module: {
        rules: [
            {
                test: /\.js$/,
                use: ['babel-loader'],
                exclude: /(node_modules|public|angular.js)/
            }, 
            {
                test: /\.css$/,
                use: ExtractTextPlugin.extract({
                    fallback: 'style-loader',
                    use: 'css-loader'
                })
            },
            {
                test: /\.(eot|svg|ttf|woff|woff2|otf)$/,
                use: ['file-loader?name=/fonts/[name].[ext]'],
            }
        ]
    },
    plugins: [
        new ExtractTextPlugin('styles.css'),
        new webpack.optimize.UglifyJsPlugin({
            include: /\.min\.js$/,
            parallel: true
        })
    ]
};
