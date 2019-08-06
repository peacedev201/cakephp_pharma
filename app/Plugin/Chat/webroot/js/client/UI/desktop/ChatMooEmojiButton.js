import React from 'react';
import ChatMooUtils from '../../utils/ChatMooUtils';

export default class ChatMooEmojiButton extends React.Component {
    constructor(props) {
        super(props);
    }
    emojIsClicked = (text) => {
        this.props.emojIsClicked(text);
    }
    render(){
        var emoj = [];
        var i = 0;
        ChatMooUtils.getEmojiJson().forEach(function (e) {
            i++;
            emoj.push(<span onClick={this.emojIsClicked.bind(this,e.emoji)} key={i} title={e.text}
                            className={e.class}></span>);
        }.bind(this));
        var display = (this.props.isShow) ? "block" : "none";
        var displayEmotion = (ChatMooUtils.isAllowedEmotion())?"block":"none";
        return (<div  style={{display:displayEmotion}} >
                <div className="moochat_buttonicon moochat_buttonemoticon chatwindow-emoji"
                     onClick={this.props.handleButtonEmojIsClicked}>
                    <i className="material-icons">insert_emoticon</i>
                </div>
                <div style={{display:display}} className="moochat_iconlist">{emoj}</div>
            </div>
        );
    }
}