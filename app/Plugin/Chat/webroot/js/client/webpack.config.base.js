var path = require('path');

var BUILD_DIR = path.resolve(__dirname, "../../../../../webroot/chat/js/client");
var APP_DIR = path.resolve(__dirname, './');


const webpack = require('webpack');

module.exports = function(configName) {
    var config ;
    switch(configName) {
        case 'webpack':
            config =  {
                entry:{
                    //'vendor':['react','react-dom','whatwg-fetch'],
                    'mooChat': APP_DIR +'/main.js',
                    'mooChat-mobile': APP_DIR +'/main-mobile.js',
                    'mooChat-admin': APP_DIR +'/main-admin.js',
                    'mooChat-videoCalling': APP_DIR +'/videoCalling.js',
                },
                output: {
                    path: BUILD_DIR,
                    filename: '[name].js',
                    //filename: '[chunkhash].[name].bundle.js'
                    libraryTarget: "umd"
                },
                module : {
                    rules : [
                        {
                            test : /\.js?/,
                            include : APP_DIR,
                            exclude: /(node_modules|bower_components)/,
                            use:{
                                loader : 'babel-loader'
                            }
                        }
                    ]
                },
                node: {
                    console: true,
                    fs: 'empty',
                    net: 'empty',
                    tls: 'empty'
                },
                /*
                plugins: [
                    new webpack.optimize.CommonsChunkPlugin({
                        names: ['vendor','manifest'], // Specify the common bundle's name.
                        //minChunks: function (module) {
                            // this assumes your vendor imports exist in the node_modules directory
                        //    return module.context && module.context.indexOf('node_modules') !== -1;
                        //}
                    }),

                ]*/
            };
            break;
        case 'BUILD_DIR':
            config = BUILD_DIR;
            break;
        case 'APP_DIR':
            config = APP_DIR;
            break;
        default:
            config = {};
    }
    return config;
}