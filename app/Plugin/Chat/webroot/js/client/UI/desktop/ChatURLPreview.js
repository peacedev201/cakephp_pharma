import React from 'react';


export default class ChatURLPreview extends React.Component {
    constructor(props) {
        super(props);
    }
    handleRemoveLink = (e) =>{
        this.props.hanldeCloseLinkPreview(e);
    }
    render(){
        if(!this.props.data.show){
            return (null);
        }else{

            var response = this.props.data.data;
            if(response.hasOwnProperty('result')){
                var data = response.result;

                var title = (data.hasOwnProperty("title"))?data.title:"";
                var description = (data.hasOwnProperty("description"))?data.description:"";
                var type = (data.hasOwnProperty("type"))?data.type:"";
                var img = (data.hasOwnProperty("image"))? <span style={{"backgroundImage":"url(" + data.image + ")"}} className="img_link"></span> :"";
                var code = (data.hasOwnProperty("code"))?data.code:"";
                if (title != "" || description !="" || type!="" || img != "" || code!=""){
                    return <div className="moochat_link_attach"><span onClick={this.handleRemoveLink} className="remove_review_link"><i className="material-icons">clear</i></span> {img}<div className="linkcontent"><div style={{"fontWeight":"bold"}}>{title}</div><div className="link_description">{description}</div></div></div>;
                }else{
                    return (null);
                }
            }
            return <div className="moochat_iconlist"><div className="chat-spinner">
                <div className="bounce1"></div>
                <div className="bounce2"></div>
                <div className="bounce3"></div>
            </div></div>;
        }

    }
};
