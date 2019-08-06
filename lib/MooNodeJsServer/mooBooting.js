var mooDB = require("./mooDB");
var log = require("./mooLog");
var fs = require("fs");

function load(path){
    if (fs.existsSync(path)){
        require(path).init();
    }
}
var mooBooting = (function () {

    var initDataSource = function(mooDB){
        mooDB = mooDB ;
    };
    var run = function(){
        /*
        var files = fs.readdirSync("../../app/Plugin");
        for (var i in files) {
            load("../../app/Plugin/"+files[i]+"/webroot/js/server/moo"+files[i]+".js");
        }*/
        var files = fs.readdirSync("./plugin");
        for (var i in files) {
            load("./plugin/"+files[i]+"/moo"+files[i]+".js");
        }
    };
    return {
        initDataSource : initDataSource,
        run:run
    };
}());


module.exports =  mooBooting ;