
var _onlyOne = [];
var _ = require('lodash');


module.exports =(function () {
    var execute = function(id,callback){
        if (!isRunning(id)){
            module.exports.freeze(id);
            callback();
        }
    };
    var release = function(id){
        _.pull(_onlyOne,id);

    };
    var freeze = function(id){
        _onlyOne.push(id);
    };
    var isRunning = function(id){
      return _.includes(_onlyOne,id);
    };
    return {
        execute:execute,
        release:release,
        isRunning:isRunning,
        freeze:freeze
    }
}());