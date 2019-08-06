
// browserify -t [ babelify --presets [ react ] ]   main-admin.js -s mooChat  >  ../mooChat-admin.js
// NODE_ENV=production browserify -t [ babelify --presets [ react ] ]   main-admin.js -s mooChat | uglifyjs -c -m >  ../mooChat-admin.js
import React from 'react';
import ReactDOM from 'react-dom';
import ChatGeneral  from'./UI/admin/ChatGeneral';
import ChatMonitor  from'./UI/admin/ChatMonitor';

module.exports = function mooChat(){
    return {
        renderGeneral:function(){
            ReactDOM.render(<ChatGeneral />, document.getElementById('chatGeneral'));
        },
        renderMonitor:function(){
            ReactDOM.render(<ChatMonitor />, document.getElementById('chatGeneral'));
        },
        
    };
}

window.mooChat = module.exports;