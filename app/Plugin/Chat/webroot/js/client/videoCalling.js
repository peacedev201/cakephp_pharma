import React from 'react';
import ReactDOM from 'react-dom';
import ChatWebAPIUtils from'./utils/ChatWebAPIUtils';
import adapter from 'webrtc-adapter';
import VideoCallingContainer from './UI/containers/VideoCallingContainer';

window.adapter = adapter ;
var root = document.getElementById('root');
ReactDOM.render(
        <div><VideoCallingContainer/></div>,
        document.getElementById('root')
);

window.ChatWebAPIUtils = ChatWebAPIUtils;
