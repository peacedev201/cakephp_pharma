import React from 'react';
import ChatWebAPIUtils from '../../utils/ChatWebAPIUtils';
import ChatMooUtils from '../../utils/ChatMooUtils';
var __ = require('../../utils/ChatMooI18n').i18next;

export default class ChatSendFileButton extends React.Component {
    constructor(props) {
        super(props);
    }
    handleSendFile = (e) => {
        if(!ChatMooUtils.isAllowedSendFiles()){
            return;
        }
        var files = e.target.files;
        var data = new FormData();
        var error = 0;
        var mesasges = "";
        for (var i = 0; i < files.length; i++) {
            var file = files[i];

            if (file.size > ChatMooUtils.getUploadFileLimitOnSite()) {
                mesasges = __.t("file_is_too_large") + ChatMooUtils.getUploadFileLimitOnSite();
                error = 1;
            } else {
                data.append('file', file, file.name);
                data.append('roomId', this.props.room.id);
            }


        }
        if (!error) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', ChatWebAPIUtils.getSiteUrl() + '/chats/send-files', true);
            xhr.send(data);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    var json = JSON.parse(xhr.responseText);
                    if (json.hasOwnProperty('error_code')) {
                        if (json.error_code == 0) {
                            ChatWebAPIUtils.sendRequestTextMessage(json.result.filename, this.props.room.id, "file");
                        } else {
                            try {
                                var error = JSON.parse(xhr.responseText);
                                if(error.hasOwnProperty("error_code") && error.hasOwnProperty('result')){
                                    if(error.error_code == 1){
                                        ChatWebAPIUtils.openAlertModal(__.t("warning"),error.result.error);
                                    }
                                }
                            } catch (e) {
                                ChatWebAPIUtils.openAlertModal(__.t("warning"),xhr.responseText);
                            }

                        }
                    }

                    console.log(json);
                } else {
                    console.log(" Error in upload, try again.");
                }
            }.bind(this);
        }else{
            ChatWebAPIUtils.openAlertModal(__.t("warning"),mesasges);
        }
    }
    render(){
        var displaySendFiles = (ChatMooUtils.isAllowedSendFiles())?"block":"none";
        return (<div className="moochat_buttonicon moochat_buttonfiles moochat_floatL" style={{display:displaySendFiles}}>

                <div className="_3jk">
                    <i className="material-icons">attachment</i>
                    <form action="/sendfile" className="_vzk"  method="post"
                          encType="multipart/form-data">
                        <input id={"moochat-add-files-button-"+this.props.room.id}
                               onChange={this.handleSendFile} type="file" className="_n _2__f _5f0v"  title={__.t("add_files")}
                               name="attachment[]" multiple="" accept="*" />
                    </form>
                </div>
            </div>
        );
    }
}