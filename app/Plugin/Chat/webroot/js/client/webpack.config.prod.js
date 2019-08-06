const webpackMerge = require('webpack-merge');
const commonConfig = require('./webpack.config.base.js');
const webpack = require('webpack');
var CompressionPlugin = require("compression-webpack-plugin");
module.exports = function(env) {
    return webpackMerge(commonConfig('webpack'), {
        output: {
            path: commonConfig('BUILD_DIR'),
            //filename: '[name].bundle.min.js'
        },
        plugins: [
            new webpack.DefinePlugin({
                'process.env': {
                    'NODE_ENV': JSON.stringify('production')
                }
            }),
            new CompressionPlugin({
                asset: "[path].gz[query]",
                algorithm: "gzip",
                test: /\.js$|\.html$/,
                threshold: 10240,
                minRatio: 0.8
            }),

        ]
    })
};