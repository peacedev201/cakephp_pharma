const webpackMerge = require('webpack-merge');
const commonConfig = require('./webpack.config.base.js');
module.exports = function(env) {
    return webpackMerge(commonConfig('webpack'), {

    })
};