// browserify -t [ babelify --presets [ react ] ]   main-mobile.js -s mooChat >  mooChat-mobile.js
//  webpack main-mobile.js mooChat-mobile.js
// NODE_ENV=production browserify -t [ babelify --presets [ react ] ]   main-mobile.js -s mooChat | uglifyjs -c -m >  mooChat-mobile.js && mv mooChat-mobile.js mooChat.test.js && java -jar /Users/duy/Documents/tool/moo-release/compiler-latest/compiler.jar --js mooChat.test.js --js_output_file mooChat-mobile.js

import React from 'react';
import ReactDOM from 'react-dom';
import ChatApp  from'./UI/ChatApp-mobile.js';
import 'whatwg-fetch';
import _ from 'lodash';

var elemDiv = document.createElement('div');
elemDiv.id = "appChat";
document.body.appendChild(elemDiv);
var chatComponent = ReactDOM.render(<ChatApp />, elemDiv);
//ReactDOM.render(<ChatApp />, document.getElementById('app'));
module.exports = {
    openChatWithOneUser:function(uId){
        chatComponent.openChatWithOneUser(uId);
    },
    openChatRoom:function(rId){
        chatComponent.openChatRoom(rId);
    },
    markMessagesInARoomIsSeen:function(rId){
        chatComponent.markMessagesInARoomIsSeen(rId);
    },
    markReadOnMessagesPage:function(e){
        chatComponent.markReadOnMessagesPage(e);
    }
}
// hacking for chatApp
if (_.has(window,"window.activityAction.fetch")){
    var origin_fetch = window.activityAction.fetch;
    window.activityAction.fetch = function(i){
        origin_fetch(i);
        fetch(mooConfig.url.base+'/api/chat/config?access_token='+mooConfig.access_token)
            .then(function(response) {
                return response.json()
            }).then(function(json) {
            /*if ((json.mooChat && mooConfig.mooChat.hide_offline != json.mooChat.hide_offline) || (json.setting && json.setting.site_offline)){
                location.reload();
            }*/
        }).catch(function(ex) {
            console.log('parsing failed', ex)
        })
    }
}
window.showSubscriptionMessageHook = function(){
    location.reload();
}
window.markAllMessagesAsReadHook = function(uId){
    chatComponent.markReadAllMessages(uId);
}