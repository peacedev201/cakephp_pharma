import React from 'react';
import Modal from 'react-modal';
import PoupStore from '../stores/PoupStore';
import CHAT_CONSTANTS from '../constants/ChatConstants';
import ChatWebAPIUtils from '../utils/ChatWebAPIUtils';
var __ = require('../utils/ChatMooI18n').i18next;
import _ from 'lodash';
import DetectRTC  from 'detectrtc';
import ChatMooUtils from '../utils/ChatMooUtils';
class ReportWindow extends React.Component {
    constructor(props) {
        super(props);
        this.handleModalCloseRequest = this.handleModalCloseRequest.bind(this);
        this.handleSaveClicked = this.handleSaveClicked.bind(this);
        this._onChange = this._onChange.bind(this);
        this.state = {data: PoupStore.get('report')};
    }
    handleModalCloseRequest(){
        ChatWebAPIUtils.closeReportModal();
    }
    handleSaveClicked(e) {

        // Fixing wanring : You tried to return focus to null but it is not in the DOM anymore
        var rId = this.state.data.rId;
        var reason = this.refs.reason.value;

        setTimeout(function () {
            ChatWebAPIUtils.sendRequestReportMesasgeSpam({rId: rId, reason: reason});
        }, 400);
        this.handleModalCloseRequest();
    }
    _onChange() {
        this.setState({data: PoupStore.get('report')});
    }
    componentDidMount(){
        PoupStore.addChangeListener(this._onChange);
    }
    componentWillUnmount() {
        PoupStore.removeChangeListener(this._onChange);
    }
    render(){
        return <Modal
            className="Modal__Bootstrap modal-dialog"
            closeTimeoutMS={150}
            isOpen={this.state.data.isOpen}
            onRequestClose={this.handleModalCloseRequest}
            contentLabel="Modal"
        >
            <div className="modal-content">
                <div className="modal-header">
                    <button type="button" className="close" onClick={this.handleModalCloseRequest}>
                        <span aria-hidden="true">&times;</span>
                        <span className="sr-only">{__.t("popup_title_close")}</span>
                    </button>
                    <h4 className="modal-title">{__.t("popup_title_report")}</h4>
                </div>
                <div className="modal-body">
                    <ul className="list6 list6sm2">
                        <li>
                            <div className="col-md-2">
                                <label>{__.t("popup_reason")}</label>
                            </div>
                            <div className="col-md-10">
                                <textarea ref="reason"></textarea>
                            </div>
                            <div className="clear"></div>
                        </li>
                        <li>
                            <div className="col-md-2">
                                <label>&nbsp;</label>
                            </div>
                            <div className="col-md-10">
                                
                                    <a href="#" onClick={this.handleSaveClicked} className="button" > {__.t("popup_button_report")}</a>

                              
                            </div>
                            <div className="clear"></div>
                        </li>
                    </ul>
                </div>
            </div>
        </Modal>;
    }
};
class AlertWindow extends React.Component {
    constructor(props) {
        super(props);
        this.handleModalCloseRequest = this.handleModalCloseRequest.bind(this);
        this.state = {data: PoupStore.get('alert')};
        this._onChange = this._onChange.bind(this);
    }
    handleModalCloseRequest() {
        ChatWebAPIUtils.closeAlertModal();
    }
    _onChange() {
        this.setState({data: PoupStore.get('alert')});
    }
    componentDidMount(){
        PoupStore.addChangeListener(this._onChange);
    }
    componentWillUnmount() {
        PoupStore.removeChangeListener(this._onChange);
    }
    render(){
        var close_button_header = "";
        var close_button_footer = "";
        if(this.state.data.close_button !== false){
            close_button_header = <button type="button" className="close" onClick={this.handleModalCloseRequest}>
                <span aria-hidden="true">&times;</span>
                <span className="sr-only">{__.t("popup_title_close")}</span>
            </button>
            
            close_button_footer = <div className="modal-footer">
                <button type="button" className="btn btn-default pull-right"
                        onClick={this.handleModalCloseRequest}>
                    {__.t("popup_button_close")}
                </button>

            </div>
        }
        return <Modal
            className="Modal__Bootstrap modal-dialog"
            closeTimeoutMS={150}
            isOpen={this.state.data.isOpen}
            onRequestClose={this.handleModalCloseRequest}
            contentLabel="Modal"
        >
            <div className="modal-content">
                <div className="modal-header">
                    {close_button_header}
                    <h4 className="modal-title">{this.state.data.title}</h4>
                </div>
                <div className="modal-body">
                    <ul>
                        <li>
                            <div className="col-md-12" dangerouslySetInnerHTML={{__html:this.state.data.body}}>

                            </div>
                            <div className="clear"></div>
                        </li>
                        <li>
                            <div className="clear"></div>
                        </li>
                    </ul>
                </div>
                {close_button_footer}
            </div>
        </Modal>;
    }
};
class AlertYesNoWindow extends React.Component {
    constructor(props) {
        super(props);

        this.state = {data: PoupStore.get('alertYN')};
        this.handleModalCloseRequest = this.handleModalCloseRequest.bind(this);
        this.handleSaveCloseRequest = this.handleSaveCloseRequest.bind(this);
        this._onChange = this._onChange.bind(this);
    }

    handleModalCloseRequest() {
        _.get(this.state,'data.callbackNo',_.noop)();
        ChatWebAPIUtils.closeAlertYesNoModal();
    }
    handleSaveCloseRequest() {
        _.get(this.state,'data.callback',_.noop)();
        ChatWebAPIUtils.closeAlertYesNoModal();
    }
    _onChange(){
        this.setState({data: PoupStore.get('alertYN')});
    }
    componentDidMount(){
        PoupStore.addChangeListener(this._onChange);
    }
    componentWillUnmount(){
        PoupStore.removeChangeListener(this._onChange);
    }
    render(){
        return <Modal
            className="Modal__Bootstrap modal-dialog"
            closeTimeoutMS={150}
            isOpen={this.state.data.isOpen}
            onRequestClose={this.handleModalCloseRequest}
            contentLabel="Modal"
        >
            <div className="modal-content">
                <div className="modal-header">
                    <button type="button" className="close" onClick={this.handleModalCloseRequest}>
                        <span aria-hidden="true">&times;</span>
                        <span className="sr-only">{__.t("popup_title_close")}</span>
                    </button>
                    <h4 className="modal-title">{this.state.data.title}</h4>
                </div>
                <div className="modal-body">
                    <ul>
                        <li>
                            <div className="col-md-12" dangerouslySetInnerHTML={{__html:this.state.data.body}}>

                            </div>
                            <div className="clear"></div>
                        </li>
                        <li>
                            <div className="col-md-10">

                            </div>

                            <div className="clear"></div>
                        </li>
                    </ul>
                </div>
                <div className="modal-footer">
                    <button type="button" className="btn btn-primary"
                            onClick={this.handleSaveCloseRequest}>
                        {this.state.data.yesButton}
                    </button> &nbsp;&nbsp;&nbsp;
                    <button type="button" className="btn btn-default" id={this.state.data.noButtonId}
                            onClick={this.handleModalCloseRequest}>
                        {this.state.data.noButton}
                    </button>



                </div>
            </div>
        </Modal>;
    }
};
class AlertRTCSupportedWindow extends React.Component {
    constructor(props) {
        super(props);
        this.handleModalCloseRequest = this.handleModalCloseRequest.bind(this);
        this.state = {data: PoupStore.get('alertRTCSupported')};
        this._onChange = this._onChange.bind(this);
    }
    locale = {
        rtc_supported_alert_switch_browsers_content:__.t("rtc_supported_alert_switch_browsers_content"),
    }
    handleModalCloseRequest() {
        _.get(this.state,'data.callbackNo',_.noop)();
        ChatWebAPIUtils.closeRTCSupportedAlertModal();
    }
    _onChange() {
        this.setState({data: PoupStore.get('alertRTCSupported')});
    }
    componentDidMount(){
        PoupStore.addChangeListener(this._onChange);
    }
    componentWillUnmount() {
        PoupStore.removeChangeListener(this._onChange);
    }
    render(){
        return <Modal
            className="Modal__Bootstrap modal-dialog"
            closeTimeoutMS={150}
            isOpen={this.state.data.isOpen}
            onRequestClose={this.handleModalCloseRequest}
            contentLabel="Modal"
        >
            <div className="modal-content">
                <div className="modal-header">
                    <button type="button" className="close" onClick={this.handleModalCloseRequest}>
                        <span aria-hidden="true">&times;</span>
                        <span className="sr-only">{__.t("popup_title_close")}</span>
                    </button>
                    <h4 className="modal-title">{this.state.data.title}</h4>
                </div>
                <div className="modal-body">
                    <ul>
                        <li>
                            <div className="col-md-12">
                                <div dangerouslySetInnerHTML={{__html:this.state.data.body}}></div>
                                <p>
                                    {this.locale.rtc_supported_alert_switch_browsers_content.replace('%s', DetectRTC.browser.name)}
                                </p>
                                <div>
                                    <div>
                                        <a href="https://www.google.com/chrome/index.html" target="_blank">
                                            <img src="/chat/img/chrome.png" alt="Chrome" width="64" />
                                            <div> Google Chrome </div>
                                        </a>
                                    </div>
                                    <div>
                                        <a href="https://www.mozilla.org/firefox" target="_blank">
                                            <img src="/chat/img/firefox.png" alt="Firefox" width="64" />
                                            <div> Mozilla Firefox </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div className="clear"></div>
                        </li>
                        <li>
                            <div className="clear"></div>
                        </li>
                    </ul>
                </div>
                <div className="modal-footer">
                    <button type="button" className="btn btn-default pull-right"
                            onClick={this.handleModalCloseRequest}>
                        {__.t("popup_button_close")}
                    </button>

                </div>
            </div>
        </Modal>;
    }
};

var selectVideoSource = "";
var selectAudioInputSource = "";
var selectAudioOutputSource = "";

class AlertVideoCallSettingWindow extends React.Component {
    constructor(props) {
        super(props);
        DetectRTC.load(function() {});
        this.state = {data: PoupStore.get('alertVideoCallSetting'), video:true};
        this.handleModalCloseRequest = this.handleModalCloseRequest.bind(this);
        this.handleSaveCloseRequest = this.handleSaveCloseRequest.bind(this);
        this._onChange = this._onChange.bind(this);
        this.changeCamera = this.changeCamera.bind(this);
        this.gotVideoStream = this.gotVideoStream.bind(this);
        this.handleVideoStreamError = this.handleVideoStreamError.bind(this);
    }
    locale = {
        cancel:__.t("cancel"),
        save:__.t("save"),
        settings:__.t("settings"),
        camera:__.t("camera"),
        microphone:__.t("microphone"),
        audio_output:__.t("audio_output"),
        default:__.t("default"),
        popup_title_close:__.t("popup_title_close"),
        videocall_setting_tip:__.t("videocall_setting_tip")
    }
    styles = {
        settingLine: {
            marginTop: 20
        },
        video:{
            width: "100%",
            height: 144
        }
    }
    handleModalCloseRequest() {
        ChatWebAPIUtils.closeVideoCallSettingModal();
    }
    handleSaveCloseRequest() {
        //this.stopStreaming();
        var videoSource = document.querySelector('select#videoSource');
        var audioInputSource = document.querySelector('select#audioInputSource');
        var audioOutputSource = document.querySelector('select#audioOutputSource');
        if(selectVideoSource != videoSource.value || 
          selectAudioInputSource != audioInputSource.value || 
          selectAudioOutputSource != audioOutputSource.value)
        {
            selectVideoSource = videoSource.value;
            selectAudioInputSource = audioInputSource.value;
            selectAudioOutputSource = audioOutputSource.value;
            var streamConfig = {
                videoSource: selectVideoSource,
                audioInputSource: selectAudioInputSource,
                audioOutputSource: selectAudioOutputSource
            }
            ChatWebAPIUtils.saveVideoCallSetting(streamConfig);
        }
        ChatWebAPIUtils.closeVideoCallSettingModal();
    }
    _onChange(){
        this.setState({data: PoupStore.get('alertVideoCallSetting')});
    }
    componentDidMount(){
        PoupStore.addChangeListener(this._onChange);
    }
    componentWillMount() {
        Modal.setAppElement('body');
    }
    componentWillUnmount(){
        PoupStore.removeChangeListener(this._onChange);
        //this.stopStreaming();
    }
    changeCamera(){
        this.stopStreaming();
        var videoSource = document.querySelector('select#videoSource');
        var audioInputSource = document.querySelector('select#audioInputSource');
        if(videoSource != null)
        {
            var audioSource = audioInputSource.value;
            var videoSource = videoSource.value;
            var constraints = {
                audio: {deviceId: audioSource ? {exact: audioSource} : undefined},
                video: {deviceId: videoSource ? {exact: videoSource} : undefined}
            };
        }
        else if(selectVideoSource != "")
        {
            var constraints = {
                audio: {deviceId: selectAudioInputSource ? {exact: selectAudioInputSource} : undefined},
                video: {deviceId: selectVideoSource ? {exact: selectVideoSource} : undefined}
            };
        }
        else
        {
            var constraints = {
                audio: true,
                video: true
            };
        }
        // Fix for WKWebview
        /*if ( typeof navigator.mediaDevices !== 'undefined' && ChatMooUtils.isAllowedVideoCalling()){
            navigator.mediaDevices.getUserMedia(constraints).
            then(this.gotVideoStream).catch(this.handleVideoStreamError);
        }*/

    }
    gotVideoStream(stream){
        this.video.srcObject = stream;
    }
    handleVideoStreamError(err){
        console.log("The following error occurred: " + err.name);
    }
    stopStreaming(){
        if (this.video) {
            let stream = this.video.srcObject;
            if(stream)
            {
                let tracks = stream.getTracks();
                tracks.forEach(function(track) {
                  track.stop();
                });
                this.video.srcObject = null;
            }
        }
    }
    render(){
        //webcam
        var videoDevices = [];
        DetectRTC.videoInputDevices.forEach(function(device, idx) {
            videoDevices.push(<option key={device.id} value={device.id}>{device.label}</option>);
        })
        if(videoDevices.length == 0){
            videoDevices.push(<option key="0" value="0">{this.locale.default}</option>);
        }
        
        //audio input
        var audioInputDevices = [];
        DetectRTC.audioInputDevices.forEach(function(device, idx) {
            audioInputDevices.push(<option key={device.id} value={device.id}>{device.label}</option>);
        })
        
        if(audioInputDevices.length == 0){
            audioInputDevices.push(<option key="0" value="0">{this.locale.default}</option>);
        }
        
        //audio output
        var audioOutputDevices = [];
        DetectRTC.audioOutputDevices.forEach(function(device, idx) {
            audioOutputDevices.push(<option key={device.id} value={device.id}>{device.label}</option>);
        })
        if(audioOutputDevices.length == 0){
            audioOutputDevices.push(<option key="0" value="0">{this.locale.default}</option>);
        }
        
        //laod default camera
        this.changeCamera();
        
        return <Modal
            className="Modal__Bootstrap modal-dialog"
            closeTimeoutMS={150}
            isOpen={this.state.data.isOpen}
            onRequestClose={this.handleModalCloseRequest}
            contentLabel="Modal"
        >
            <div className="modal-content">
                <div className="modal-header">
                    <button type="button" className="close" onClick={this.handleModalCloseRequest}>
                        <span aria-hidden="true">&times;</span>
                        <span className="sr-only">{this.locale.popup_title_close}</span>
                    </button>
                    <h4 className="modal-title">{this.locale.settings}</h4>
                </div>
                <div className="modal-body">
                    <ul>
                        <li>
                            <div className="col-md-8">
                                <div className="col-md-12">
                                    {this.locale.camera}
                                </div>
                                <div className="col-md-12">
                                    <select id="videoSource" onChange={this.changeCamera} defaultValue={selectVideoSource}>
                                        {videoDevices}
                                    </select>
                                </div>
                            </div>
                            <div className="col-md-4">
                                <video ref={(input) => { this.video = input; }} autoPlay={true} style={this.styles.video}></video>
                            </div>
                            <div className="clear"></div>
                        </li>
                        <li style={this.styles.settingLine}>
                            <div className="col-md-12">
                                {this.locale.microphone}
                            </div>
                            <div className="col-md-12">
                                <select id="audioInputSource" defaultValue={selectAudioInputSource}>
                                    {audioInputDevices}
                                </select>
                            </div>
                            
                            <div className="clear"></div>
                        </li>
                        <li style={this.styles.settingLine}>
                            <div className="col-md-12">
                                {this.locale.audio_output}
                            </div>
                            <div className="col-md-12">
                                <select id="audioOutputSource" defaultValue={selectAudioOutputSource}>
                                    {audioOutputDevices}
                                </select>
                            </div>
                            
                            <div className="clear"></div>
                        </li>
                        <li style={this.styles.settingLine}>
                            <div className="col-md-12">
                                {this.locale.videocall_setting_tip}
                            </div>

                            <div className="clear"></div>
                        </li>
                    </ul>
                </div>
                <div className="modal-footer">
                    <button type="button" className="btn btn-primary"
                            onClick={this.handleModalCloseRequest}>
                        {this.locale.cancel}
                    </button> &nbsp;&nbsp;&nbsp;
                    <button type="button" className="btn btn-default" id={this.state.data.noButtonId}
                            onClick={this.handleSaveCloseRequest}>
                        {this.locale.save}
                    </button>
                </div>
            </div>
        </Modal>;
    }
};
export default class PoupWindow extends React.Component {
    render(){
        return <div>
            <ReportWindow />
            <AlertWindow />
            <AlertYesNoWindow />
            <AlertRTCSupportedWindow />
            <AlertVideoCallSettingWindow />
        </div>;
    }
};
