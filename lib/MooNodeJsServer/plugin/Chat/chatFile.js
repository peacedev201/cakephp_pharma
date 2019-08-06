//var chatDB;

var formidable = require('formidable');
var fs = require('fs');
var path = require('path');
var uploadDir = path.join(__dirname, '/..','/..', '/..', '/..', '/app/webroot/uploads/chat/')
var chatFile = (function () {

    var init = function (app) {
        return;
        app.post('/chat/send-file', function (req, res) {
            var form = new formidable.IncomingForm();
            form.parse(req, function (err, fields, files) {
                // `file` is the name of the <input> field of type `file`
                console.log(files.file);
                var old_path = files.file.path,
                    file_size = files.file.size,
                    file_ext = files.file.name.split('.').pop(),
                    index = old_path.lastIndexOf('/') + 1,
                    file_name = old_path.substr(index),
                    new_path = path.join(uploadDir, file_name + '.' + file_ext);
                console.log("/chat/send-file",old_path,new_path);
                fs.readFile(old_path, function (err, data) {
                    fs.writeFile(new_path, data, function (err) {
                        console.log(err)
                        fs.unlink(old_path, function (err) {
                            if (err) {
                                res.status(500);
                                res.json({'success': false});
                            } else {
                                res.status(200);
                                res.json({'success': true});
                            }
                        });
                    });
                });
            });
        });
    };

    return {
        init: init,
    };
}());


module.exports = chatFile;