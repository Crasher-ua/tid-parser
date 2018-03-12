const path = require('path');

module.exports = {
    entry: {
        'tid-engine': './ng/tid-app-entry-point.js'
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
    module: {
        loaders: [{
            test: /\.js$/,
            loader: 'babel-loader',
            exclude: /(node_modules|public|angular.js)/
        }]
    }
};
