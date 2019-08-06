import React from 'react';
import ReactDOM from 'react-dom';
import ChatWebAPIUtils from '../../utils/ChatWebAPIUtils';
import ChatMooUtils from '../../utils/ChatMooUtils';
import RoomStore from '../../stores/RoomStore';
import FriendStore from '../../stores/FriendStore';
var __ = require('../../utils/ChatMooI18n').i18next;
import Avatar from './../Avatar';
export default class ChatAddFriends extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            usersIsChoosen: [],
            usersSuggestion: [],
            userSuggestionText: '',
            isFocusUserSuggestionText: false,
            runSearchEngine: false
        };
    }
    engineBloodHoundCallback = (users) => {
        this.setState({usersSuggestion: users});
    }
    handleInputSuggestion = (e) =>{
        var name = e.target.value;
        if(FriendStore.isCachedKeyword(name)){
            this.setState({userSuggestionText: name, runSearchEngine: true});
        }else{
            ChatWebAPIUtils.sendRequestSearchName(name,(function(){
                this.setState({userSuggestionText: name, runSearchEngine: true});
            }).bind(this));
        }

        // FriendStore.getBloodhoundEngine().search(e.target.value, this.engineBloodHoundCallback, this.engineBloodHoundCallback);
    }
    handleDoneButtonIsClicked = () =>{
        var tmp = this.state.usersIsChoosen.slice(0);
        if(ChatMooUtils.isAllowedChatGroup()){
            this.props.handleAddFriendSubmit(tmp);
        }
        this.setState({usersIsChoosen:[],userSuggestionText: ''});
    }
    handleRemoveUserIsChoosen = (i) =>{
        var tmp = this.state.usersIsChoosen.splice(0);
        tmp.splice(i, 1);
        this.setState({"usersIsChoosen": tmp, isFocusUserSuggestionText: true});
    }
    handleAddUserIsChoosen = (i) =>{
        var tmp = this.state.usersIsChoosen.splice(0);
        tmp.push(this.state.usersSuggestion[i].id);
        this.setState({
            usersIsChoosen: tmp,
            userSuggestionText: '',
            isFocusUserSuggestionText: true,
            usersSuggestion: []
        });
    }
    componentDidUpdate(){
        var state = {};
        if (this.state.isFocusUserSuggestionText) {
            ReactDOM.findDOMNode(this.refs.userSuggestionText).focus();
            state.isFocusUserSuggestionText = false;
        }
        if (this.state.runSearchEngine) {
            FriendStore.getBloodhoundEngine().search(this.state.userSuggestionText, this.engineBloodHoundCallback, this.engineBloodHoundCallback);
            state.runSearchEngine = false;
        }
        if (state.hasOwnProperty('isFocusUserSuggestionText') || state.hasOwnProperty('runSearchEngine')) {
            this.setState(state);
        }
    }
    render() {
        var friends = FriendStore.getAll();
        var display = (this.props.isShow) ? "block" : "none";

        var suggestDisplay = "none";
        var widthInputSuggestion = 'auto';
        var placeholderInputSuggestion = __.t("add_friends_to_this_chat");
        var suggestItems = [];

        var itemsIsChoosen = [];
        var members = RoomStore.get(this.props.roomId).members;

        if (this.state.usersSuggestion.length > 0) {
            suggestDisplay = "block";

            for (var i = 0; i < this.state.usersSuggestion.length; i++) {
                if (members.indexOf(this.state.usersSuggestion[i].id)  == -1 && this.state.usersIsChoosen.indexOf(this.state.usersSuggestion[i].id) == -1 && suggestItems.length < 6)

                    suggestItems.push(<div key={i} className="suggestion-item tt-suggestion tt-selectable"
                                           onClick={this.handleAddUserIsChoosen.bind(this,i)}>
                        <Avatar  src={this.state.usersSuggestion[i].avatar} />
                        <span className="text">{this.state.usersSuggestion[i].name}</span>
                    </div>);
            }
        }
        if (this.state.usersIsChoosen.length > 0) {
            widthInputSuggestion = '20px';
            placeholderInputSuggestion = '';
            for (var i = 0; i < this.state.usersIsChoosen.length; i++) {
                itemsIsChoosen.push(<span key={i}
                                          className="tag label label-info">{friends[this.state.usersIsChoosen[i]].name}
                    <span data-role="remove" onClick={this.handleRemoveUserIsChoosen.bind(this,i)}></span></span>);
            }
        }
        return (
            <div className="_54_-" style={{display:display}}>

                <table className="uiGrid _51mz" cellSpacing="0" cellPadding="0">
                    <tbody>
                    <tr className="_51mx">
                        <td className="_51m- vTop _54__">
                            <div className="clearfix uiTokenizer uiInlineTokenizer">
                                <div className="tokenarea hidden_elem"></div>

                                <div className="uiTypeahead" id="js_6x">
                                    <div className="wrap"><input type="hidden" autoComplete="off"
                                                                 className="hiddenInput"/>
                                        <div className="innerWrap">
                                            <div>
                                                <div className="bootstrap-tagsinput">
                                                    {itemsIsChoosen}
                                                    <span className="twitter-typeahead" style={{position: 'relative', display: 'inline-block'}}>
            <input type="text"
                   className="tt-hint"
                   readOnly=""
                   autoComplete="off"
                   spellCheck="false"
                   tabIndex="-1"
                   dir="ltr"
                   style={{position: 'absolute', top: '0px', left: '0px', borderColor: 'transparent', boxShadow: 'none', opacity: 1, background: 'none 0% 0% / auto repeat scroll padding-box border-box rgba(0, 0, 0, 0)'}}/>
            <input
                type="text" placeholder={placeholderInputSuggestion} className="tt-input" autoComplete="off"
                spellCheck="false" dir="auto"
                style={{position: 'relative', verticalAlign: 'top', width: widthInputSuggestion, backgroundColor: 'transparent'}}
                size="24"
                onChange={this.handleInputSuggestion}
                value={this.state.userSuggestionText}
                ref="userSuggestionText"
            />
            <pre
                aria-hidden="true"
                style={{position: 'absolute', visibility: 'hidden', whiteSpace: 'pre', fontSize: '12px', fontStyle: 'normal', fontVariant: 'normal', fontWeight: 400, wordSpacing: '0px', letterSpacing: '0px', textIndent: '0px', textRendering: 'auto', textTransform: 'none'}}>t</pre>
            <div
                className="tt-menu"
                style={{position: 'absolute', top: '100%', left: '0px', zIndex: '100', display: suggestDisplay}}>
            <div className="tt-dataset tt-dataset-friends_userTagging">
                {suggestItems}
            </div>
        </div>
        </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td className="_51m- vTop _51mw">
                            <label className="doneButton uiButton uiButtonConfirm">
                                <input value={__.t("button_done")} type="submit" onClick={this.handleDoneButtonIsClicked}/>
                            </label>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        );
    }
};
