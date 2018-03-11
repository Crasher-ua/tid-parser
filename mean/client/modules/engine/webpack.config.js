const path = require('path');

module.exports = {
    plugins: [],
    entry: {
        'tid-engine': './ng/tid-app-entry-point.js'
    },
    output: {
        path: path.resolve(__dirname, 'build'),
        publicPath: '/build/',
        filename: '[name].js'
    },
    resolve: {
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
