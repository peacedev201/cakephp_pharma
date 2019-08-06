import React from 'react';
import ChatWebAPIUtils from '../../utils/ChatWebAPIUtils';
import ChatMooUtils from '../../utils/ChatMooUtils';
var __ = require('../../utils/ChatMooI18n').i18next;

export default class ChatSendPictureButton extends React.Component {
    constructor(props) {
        super(props);
    }
    handleSendPicture = (e) => {
        if(!ChatMooUtils.isAllowedSendPicture()){
            return;
        }
        var files = e.target.files;
        var data = new FormData();
        var error = 0;
        var mesasges = "";
        for (var i = 0; i < files.length; i++) {
            var file = files[i];

            if (!file.type.match('image.*')) {
                mesasges = __.t("Images only");
                error = 1;
            } else if (file.size > ChatMooUtils.getUploadFileLimitOnSite()) {
                mesasges = __.t("file_is_too_large") + ChatMooUtils.getUploadFileLimitOnSite();
                error = 1;
            } else {
                data.append('image', file, file.name);
                data.append('roomId', this.props.room.id);
            }
        }
        if (!error) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', ChatWebAPIUtils.getSiteUrl() + '/chats/send-picture', true);
            xhr.send(data);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    var json = JSON.parse(xhr.responseText);
                    if (json.hasOwnProperty('error_code')) {
                        if (json.error_code == 0) {
                            ChatWebAPIUtils.sendRequestTextMessage(json.result.filename, this.props.room.id, "image");
                        } else {
                            ChatWebAPIUtils.openAlertModal(__.t("warning"),xhr.responseText);
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
        var displaySendPicutre = (ChatMooUtils.isAllowedSendPicture())?"block":"none";
        return (<div className="moochat_buttonicon moochat_buttoncamera moochat_floatL" style={{display:displaySendPicutre }}>

                <div className="_3jk">
                    <i className="material-icons">photo_camera</i>
                    <form action="/send" className="_vzk"
                          method="post" encType="multipart/form-data">
                        <input onChange={this.handleSendPicture} type="file" className="_n _2__f _5f0v" title={__.t("add_photo")}
                               name="attachment[]" multiple="" accept="image/*" />
                    </form>
                </div>
            </div>
        );
    }
}