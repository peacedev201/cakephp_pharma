import React from "react";
import ReactDOM from 'react-dom';
// import IconMenu from 'material-ui/IconMenu';
// import IconButton from 'material-ui/IconButton';
import MuiThemeProvider from 'material-ui/styles/MuiThemeProvider';
import ReactionActions from '../../data/actions/ReactionActions';
import LikeActions from "../../data/actions/LikeActions";
import _ from "lodash";
import AppDispatcher from "../../data/AppDispatcher";
import ReactionActionTypes from "../../data/actions/ReactionActionTypes";

class ReactionDefault extends React.Component{
    constructor(props) {
        super(props);
        this.state = {
            width: parseInt(_.get(this.props, 'width', 24)),
            height: parseInt(_.get(this.props, 'height', 24)),
            name: parseInt(_.get(this.props, 'name', 'Like Default'))
        };
    }
    render() {
        return (<svg
        version="1.1"
        width={this.state.width}
        height={this.state.height}
        xmlns="http://www.w3.org/2000/svg"
        xmlnsXlink="http://www.w3.org/1999/xlink"
        viewBox="0 -3 30 30"
        onClick={this.props.onClick}
        onMouseDown={this.props.onMouseDown}
        onMouseUp={this.props.onMouseUp}
        onMouseMove={this.props.onMouseMove}
        onTouchStart={this.props.onTouchStart}
        onTouchMove={this.props.onTouchMove}
        onTouchCancel={this.props.onTouchCancel}
        style={{verticalAlign: "middle"}}
    >
    <path className="like-icon-default" fill="#9a9b9b" d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.59 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-1.91l-.01-.01L23 10z"/>
            </svg>);
    }
}
class ReactionLike extends React.Component{
    constructor(props) {
        super(props);
        this.state = {
            width: parseInt(_.get(this.props, 'width', 24)),
            height: parseInt(_.get(this.props, 'height', 24)),
            name: parseInt(_.get(this.props, 'name', 'Like')),
            head_gradient: 'like_head_gradient',
            dropshadow: 'like_dropshadow',
            filter_head_gradient: 'url(#like_head_gradient)',
            filter_dropshadow: 'url(#like_dropshadow)',
        };
    }
    componentWillMount() {
        const head_gradient_id = _.uniqueId("like_head_gradient-");
        const dropshadow_id = _.uniqueId("like_dropshadow-");
        this.setState({
            head_gradient: head_gradient_id,
            dropshadow: dropshadow_id,
            filter_head_gradient: 'url(#'+head_gradient_id+')',
            filter_dropshadow: 'url(#'+dropshadow_id+')',
        });
    }
    render() {
        return (<svg
        version="1.1"
        xmlns="http://www.w3.org/2000/svg"
        xmlnsXlink="http://www.w3.org/1999/xlink"
        xmlSpace="preserve"
        viewBox="0 0 194 194"
        width={this.state.width}
        height={this.state.height}
        onClick={this.props.onClick}
        style={this.props.style}
    >
    <defs>
        <linearGradient id={this.state.head_gradient} x1="99.18622" y1="179.46996" x2="99.18622" y2="12.46996" gradientUnits="userSpaceOnUse">
            <stop offset="0" stopColor="#0068ff"/>
            <stop offset="0.26964" stopColor="#0966ff"/>
            <stop offset="0.70788" stopColor="#215fff"/>
            <stop offset="1" stopColor="#355aff"/>
            </linearGradient>
        {/*<linearGradient id="like_head_override" className="like_head_override" x1="99.18622" y1="179.46996" x2="99.18622" y2="12.46996" gradientUnits="userSpaceOnUse"><stop offset="0" stopColor="var(--color-stop-1)"/><stop offset="0.26964" stopColor="var(--color-stop-2)"/><stop offset="0.70788" stopColor="#var(--color-stop-3)"/><stop offset="1" stopColor="var(--color-stop-4)"/></linearGradient>*/}
    <filter id={this.state.dropshadow} x="-40%" y="-40%" width="180%" height="180%" filterUnits="userSpaceOnUse">
        <feGaussianBlur in="SourceAlpha" stdDeviation="3"/>
            <feOffset dx="5" dy="5" result="offsetblur"/>
            <feOffset dx="-5" dy="-5" result="offsetblur"/>
        <feMerge>
        <feMergeNode/>
        <feMergeNode in="SourceGraphic"/>
        <feMergeNode in="SourceGraphic"/>
            </feMerge>
            </filter>
            </defs>
            <g>
            <circle className="like-a-head" opacity="0.24" fill="#000" filter={this.state.filter_dropshadow} cx="99.18622" cy="95.96996" r="84"/>
            <circle className="like-a-face" fill={this.state.filter_head_gradient} cx="99.18622" cy="95.96996" r="83.5"/>
            <rect className="like-a-hand" fill="#fff" x="52.68622" y="86.46996" width="24" height="48" rx="4" ry="4"/>
            <path className="like-a-hand" fill="#fff" d="M136.71119,92.47a8.613,8.613,0,0,0,5-8c0-7-5.025-8-8.025-8h-21.975c4.975-11,5-18,5-22s-3-12-9-12-7,4-7,4,1.5,10.5,0,12-19,22-19,26c0,2.98749-.01385,7.07922-.02094,8.96033-.00012.01355-.004.02606-.004.03967v31a5,5,0,0,0,5,5h38c.07861,0,.15179-.01953.22955-.02313,9.10669-.23645,12.79541-3.14368,12.79541-6.97687,0-4-3-5-3-5s4-1,4-6-3-6-3-6,6-1,6-7S136.71119,92.47,136.71119,92.47Z"/>
            </g>
            </svg>);
    }
}
class ReactionLove extends React.Component{
    constructor(props) {
        super(props);
        this.state = {
            width: parseInt(_.get(this.props, 'width', 24)),
            height: parseInt(_.get(this.props, 'height', 24)),
            name: parseInt(_.get(this.props, 'name', 'Love')),
            head_gradient: 'love_head_gradient',
            dropshadow: 'love_dropshadow',
            filter_head_gradient: 'url(#love_head_gradient)',
            filter_dropshadow: 'url(#love_dropshadow)',
        };
    }
    componentWillMount() {
        const head_gradient_id = _.uniqueId("love_head_gradient-");
        const dropshadow_id = _.uniqueId("love_dropshadow-");
        this.setState({
            head_gradient: head_gradient_id,
            dropshadow: dropshadow_id,
            filter_head_gradient: 'url(#'+head_gradient_id+')',
            filter_dropshadow: 'url(#'+dropshadow_id+')',
        });
    }
    render() {
        return (<svg
        version="1.1"
        xmlns="http://www.w3.org/2000/svg"
        xmlnsXlink="http://www.w3.org/1999/xlink"
        viewBox="0 0 194 194"
        width={this.state.width}
        height={this.state.height}
        onClick={this.props.onClick}
        style={this.props.style}
    >
    <defs>
        <linearGradient id={this.state.head_gradient} x1="99.18622" y1="179.46996" x2="99.18622" y2="12.46996" gradientUnits="userSpaceOnUse">
            <stop offset="0" stopColor="#ff0046"/>
            <stop offset="0.26596" stopColor="#fc0948"/>
            <stop offset="0.69818" stopColor="#f5214e"/>
            <stop offset="1" stopColor="#ef3653"/>
            </linearGradient>
        {/*<linearGradient id="love_head_override" className="love_head_override" x1="99.18622" y1="179.46996" x2="99.18622" y2="12.46996" gradientUnits="userSpaceOnUse"><stop offset="0" stopColor="var(--color-stop-1)"/><stop offset="0.26596" stopColor="var(--color-stop-2)"/><stop offset="0.69818" stopColor="var(--color-stop-3)"/><stop offset="1" stopColor="var(--color-stop-4)"/></linearGradient>*/}
    <filter id={this.state.dropshadow} x="-40%" y="-40%" width="180%" height="180%" filterUnits="userSpaceOnUse">
        <feGaussianBlur in="SourceAlpha" stdDeviation="3"/>
            <feOffset dx="5" dy="5" result="offsetblur"/>
            <feOffset dx="-5" dy="-5" result="offsetblur"/>
        <feMerge>
        <feMergeNode/>
        <feMergeNode in="SourceGraphic"/>
        <feMergeNode in="SourceGraphic"/>
            </feMerge>
            </filter>
            </defs>
            <g>
            <circle className="love-a-head" opacity="0.24" fill="#000" filter={this.state.filter_dropshadow} cx="99.18622" cy="95.96996" r="84"/>
            <circle className="love-a-face" fill={this.state.filter_head_gradient} cx="99.18622" cy="95.96996" r="83.5"/>
            <path className="love-a-heart" fill="#fff" d="M120.88544,56.47s-12.23767-1.88031-22.09961,13.5351C88.92389,54.58965,76.68622,56.47,76.68622,56.47s-27,0-27,28,49,57,49,57,49.19922-29,49.19922-57S120.88544,56.47,120.88544,56.47Z"/>
            </g>
            </svg>);
    }
}
class ReactionHaha extends React.Component{
    constructor(props) {
        super(props);
        this.state = {
            width: parseInt(_.get(this.props, 'width', 24)),
            height: parseInt(_.get(this.props, 'height', 24)),
            name: parseInt(_.get(this.props, 'name', 'Haha')),
            head_gradient: 'haha_head_gradient',
            dropshadow: 'haha_dropshadow',
            filter_head_gradient: 'url(#haha_head_gradient)',
            filter_dropshadow: 'url(#haha_dropshadow)',
        };
    }
    componentWillMount() {
        const head_gradient_id = _.uniqueId("haha_head_gradient-");
        const dropshadow_id = _.uniqueId("haha_dropshadow-");
        this.setState({
            head_gradient: head_gradient_id,
            dropshadow: dropshadow_id,
            filter_head_gradient: 'url(#'+head_gradient_id+')',
            filter_dropshadow: 'url(#'+dropshadow_id+')',
        });
    }
    render() {
        return (<svg
        version="1.1"
        xmlns="http://www.w3.org/2000/svg"
        xmlnsXlink="http://www.w3.org/1999/xlink"
        viewBox="0 0 194 194"
        width={this.state.width}
        height={this.state.height}
        onClick={this.props.onClick}
        style={this.props.style}
    >
    <defs>
        <linearGradient id={this.state.head_gradient} x1="98.93622" y1="179.46996" x2="98.93622" y2="12.46996" gradientUnits="userSpaceOnUse">
            <stop offset="0" stopColor="#fecc68"/>
            <stop offset="0.65099" stopColor="#fed05f"/>
            <stop offset="1" stopColor="#fed458"/>
            </linearGradient>
        {/*<linearGradient id="haha_head_override" className="angry_head_override" x1="98.93622" y1="179.46996" x2="98.93622" y2="12.46996" gradientUnits="userSpaceOnUse"><stop offset="0" stopColor="var(--color-stop-1)"/><stop offset="0.65099" stopColor="var(--color-stop-2)"/><stop offset="1" stopColor="var(--color-stop-3)"/></linearGradient>*/}
    <filter id={this.state.dropshadow} x="-40%" y="-40%" width="180%" height="180%" filterUnits="userSpaceOnUse">
        <feGaussianBlur in="SourceAlpha" stdDeviation="3"/>
            <feOffset dx="5" dy="5" result="offsetblur"/>
            <feOffset dx="-5" dy="-5" result="offsetblur"/>
        <feMerge>
        <feMergeNode/>
        <feMergeNode in="SourceGraphic"/>
        <feMergeNode in="SourceGraphic"/>
            </feMerge>
            </filter>
            </defs>
            <g>
            <circle className="haha-a-head" opacity="0.24" fill="#000" filter={this.state.filter_dropshadow} cx="99.18622" cy="95.96996" r="84"/>
            <circle className="haha-a-face" fill={this.state.filter_head_gradient} cx="99.18622" cy="95.96996" r="83.5"/>
            <polyline className="haha-a-eye" fill="none" stroke="#262c38" strokeLinecap="round" strokeLinejoin="round" strokeWidth="9" points="50.436 56.47 70.436 68.47 50.436 79.47"/>
            <polyline className="haha-a-eye" fill="none" stroke="#262c38" strokeLinecap="round" strokeLinejoin="round" strokeWidth="9" points="149.436 56.47 129.436 68.47 149.436 79.47"/>
            <path className="haha-a-mouth" fill="#262c38" d="M140.24131,131.16814A47.66108,47.66108,0,0,0,150.157,101.97v-.00006A1.99992,1.99992,0,0,0,148.15708,99.97H51.157A1.99992,1.99992,0,0,0,49.157,101.9699V101.97a47.65,47.65,0,0,0,9.77252,29.01556,203.31474,203.31474,0,0,1,81.31177.18262Z"/>
            <path className="haha-a-tongue" fill="#f55065" d="M58.982,131.05706A47.9148,47.9148,0,0,0,97.157,149.97h5A47.90939,47.90939,0,0,0,140.18351,131.246a199.22134,199.22134,0,0,0-81.20148-.189Z"/>
            <path className="haha-a-throat" fill="#303a49" d="M144.27555,125.00286A47.77939,47.77939,0,0,0,150.157,101.97v-.00006A1.99992,1.99992,0,0,0,148.15708,99.97H51.157A1.99992,1.99992,0,0,0,49.157,101.9699V101.97a47.78429,47.78429,0,0,0,5.76587,22.826A200.263,200.263,0,0,1,99.157,119.97,199.84806,199.84806,0,0,1,144.27555,125.00286Z"/>
            <path className="haha-a-mouth" fill="#262c38" d="M146.11145,103.97a44.02526,44.02526,0,0,1-43.95441,42h-5a43.70323,43.70323,0,0,1-34.99512-17.33954A43.653,43.653,0,0,1,53.20264,103.97h92.90881m2.04565-4H51.157a1.99992,1.99992,0,0,0-1.99994,1.99994V101.97a48.01586,48.01586,0,0,0,48,48h5a47.98586,47.98586,0,0,0,48-48v-.00006A1.99992,1.99992,0,0,0,148.1571,99.97Z"/>
            </g>
            </svg>);
    }
}
class ReactionWow extends React.Component{
    constructor(props) {
        super(props);
        this.state = {
            width: parseInt(_.get(this.props, 'width', 24)),
            height: parseInt(_.get(this.props, 'height', 24)),
            name: parseInt(_.get(this.props, 'name', 'Wow')),
            head_gradient: 'wow_head_gradient',
            dropshadow: 'wow_dropshadow',
            filter_head_gradient: 'url(#wow_head_gradient)',
            filter_dropshadow: 'url(#wow_dropshadow)',
        };
    }
    componentWillMount() {
        const head_gradient_id = _.uniqueId("wow_head_gradient-");
        const dropshadow_id = _.uniqueId("wow_dropshadow-");
        this.setState({
            head_gradient: head_gradient_id,
            dropshadow: dropshadow_id,
            filter_head_gradient: 'url(#'+head_gradient_id+')',
            filter_dropshadow: 'url(#'+dropshadow_id+')',
        });
    }
    render() {
        return (<svg
        version="1.1"
        xmlns="http://www.w3.org/2000/svg"
        xmlnsXlink="http://www.w3.org/1999/xlink"
        viewBox="0 0 194 194"
        width={this.state.width}
        height={this.state.height}
        onClick={this.props.onClick}
        style={this.props.style}
    >
    <defs>
        <linearGradient id={this.state.head_gradient} x1="98.93622" y1="179.46996" x2="98.93622" y2="12.46996" gradientUnits="userSpaceOnUse">
            <stop offset="0" stopColor="#fecc68"/>
            <stop offset="0.65099" stopColor="#fed05f"/>
            <stop offset="1" stopColor="#fed458"/>
            </linearGradient>
        {/*<linearGradient id="wow_head_override" className="wow_head_override" x1="98.93622" y1="179.46996" x2="98.93622" y2="12.46996" gradientUnits="userSpaceOnUse"><stop offset="0" stopColor="var(--color-stop-1)"/><stop offset="0.65099" stopColor="var(--color-stop-2)"/><stop offset="1" stopColor="var(--color-stop-3)"/></linearGradient>*/}
    <filter id={this.state.dropshadow} x="-40%" y="-40%" width="180%" height="180%" filterUnits="userSpaceOnUse">
        <feGaussianBlur in="SourceAlpha" stdDeviation="3"/>
            <feOffset dx="5" dy="5" result="offsetblur"/>
            <feOffset dx="-5" dy="-5" result="offsetblur"/>
        <feMerge>
        <feMergeNode/>
        <feMergeNode in="SourceGraphic"/>
        <feMergeNode in="SourceGraphic"/>
            </feMerge>
            </filter>
            </defs>
            <g>
            <circle className="wow-a-head" opacity="0.24" fill="#000" filter={this.state.filter_dropshadow} cx="98.93622" cy="95.96996" r="84"/>
            <circle className="wow-a-face" fill={this.state.filter_head_gradient} cx="98.93622" cy="95.96996" r="83.5"/>
            <path className="wow-a-eyebrow" fill="none" stroke="#262c38" strokeLinecap="round" strokeLinejoin="round" strokeWidth="9" d="M50.93622,38.97a13.46,13.46,0,0,1,11.5-6.5,15.80665,15.80665,0,0,1,12,5"/>
            <path className="wow-a-eyebrow" fill="none" stroke="#262c38" strokeLinecap="round" strokeLinejoin="round" strokeWidth="9" d="M124.93622,36.97s3-4,12-4,12,7,12,7"/>
            <ellipse className="wow-a-eye" fill="#262c38" cx="60.93622" cy="76.96996" rx="17.5" ry="12.5" transform="translate(-25.44656 123.61342) rotate(-79.99913)"/>
            <ellipse className="wow-a-eye" fill="#262c38" cx="137.93622" cy="76.96996" rx="12.5" ry="17.5" transform="matrix(0.98481, -0.17366, 0.17366, 0.98481, -11.27092, 25.12398)"/>
            <ellipse className="wow-a-mouth" fill="#262c38" cx="98.93622" cy="129.96996" rx="24.5" ry="33.5"/>
            </g>
            </svg>);
    }
}
class ReactionSad extends React.Component{
    constructor(props) {
        super(props);
        this.state = {
            width: parseInt(_.get(this.props, 'width', 24)),
            height: parseInt(_.get(this.props, 'height', 24)),
            name: parseInt(_.get(this.props, 'name', 'Sad')),
            head_gradient: 'sad_head_gradient',
            dropshadow: 'sad_dropshadow',
            filter_head_gradient: 'url(#sad_head_gradient)',
            filter_dropshadow: 'url(#sad_dropshadow)',
        };
    }
    componentWillMount() {
        const head_gradient_id = _.uniqueId("sad_head_gradient-");
        const dropshadow_id = _.uniqueId("sad_dropshadow-");
        this.setState({
            head_gradient: head_gradient_id,
            dropshadow: dropshadow_id,
            filter_head_gradient: 'url(#'+head_gradient_id+')',
            filter_dropshadow: 'url(#'+dropshadow_id+')',
        });
    }
    render() {
        return (<svg
        version="1.1"
        xmlns="http://www.w3.org/2000/svg"
        xmlnsXlink="http://www.w3.org/1999/xlink"
        viewBox="0 0 194 194"
        width={this.state.width}
        height={this.state.height}
        onClick={this.props.onClick}
        style={this.props.style}
    >
    <defs>
        <linearGradient id={this.state.head_gradient} x1="98.93622" y1="179.46996" x2="98.93622" y2="12.46996" gradientUnits="userSpaceOnUse">
            <stop offset="0" stopColor="#fecc68"/>
            <stop offset="0.65099" stopColor="#fed05f"/>
            <stop offset="1" stopColor="#fed458"/>
            </linearGradient>
        {/*<linearGradient id="sad_head_override" className="sad_head_override" x1="98.93622" y1="179.46996" x2="98.93622" y2="12.46996" gradientUnits="userSpaceOnUse"><stop offset="0" stopColor="var(--color-stop-1)"/><stop offset="0.65099" stopColor="var(--color-stop-2)"/><stop offset="1" stopColor="var(--color-stop-3)"/></linearGradient>*/}
    <filter id={this.state.dropshadow} x="-40%" y="-40%" width="180%" height="180%" filterUnits="userSpaceOnUse">
        <feGaussianBlur in="SourceAlpha" stdDeviation="3"/>
            <feOffset dx="5" dy="5" result="offsetblur"/>
            <feOffset dx="-5" dy="-5" result="offsetblur"/>
        <feMerge>
        <feMergeNode/>
        <feMergeNode in="SourceGraphic"/>
        <feMergeNode in="SourceGraphic"/>
            </feMerge>
            </filter>
            </defs>
            <g>
            <circle className="sad-a-head" opacity="0.24" fill="#000" filter={this.state.filter_dropshadow} cx="98.93622" cy="95.96996" r="84"/>
            <circle className="sad-a-face" fill={this.state.filter_head_gradient} cx="98.93622" cy="95.96996" r="83.5"/>
            <path className="sad-a-eyebrow" fill="none" stroke="#262c38" strokeLinecap="round" strokeLinejoin="round" strokeWidth="9" d="M48.43622,87.47a12.94942,12.94942,0,0,1,7.086-9.81217c6.67221-3.55236,11.9355-1.7088,11.9355-1.7088"/>
            <path className="sad-a-eyebrow" fill="none" stroke="#262c38" strokeLinecap="round" strokeLinejoin="round" strokeWidth="9" d="M149.45767,87.47a12.94942,12.94942,0,0,0-7.086-9.81217c-6.67221-3.55236-11.9355-1.7088-11.9355-1.7088"/>
            <ellipse className="sad-a-eye" cx="62.43622" cy="102.46996" rx="11" ry="12"/>
            <ellipse className="sad-a-eye" fill="#262c38" cx="135.43622" cy="102.46996" rx="11" ry="12"/>
            <path className="sad-a-mouth" fill="none" stroke="#262c38" strokeLinecap="round" strokeLinejoin="round" strokeWidth="9" d="M78.43622,144.47s5-10,20-10,21,11,21,11"/>
            <path className="sad-a-water" fill="#4475ff" d="M161.817,168.785a8.35647,8.35647,0,0,0-.38074-1.31506l-14-31.9999h-.00009l-13,31.9999a9.98071,9.98071,0,0,0-.89624,3.82825,14.49672,14.49672,0,1,0,28.277-2.51318Z"/>
            </g>
            </svg>);
    }
}
class ReactionAngry extends React.Component{
    constructor(props) {
        super(props);
        this.state = {
            width: parseInt(_.get(this.props, 'width', 24)),
            height: parseInt(_.get(this.props, 'height', 24)),
            name: parseInt(_.get(this.props, 'name', 'Angry')),
            head_gradient: 'angry_head_gradient',
            dropshadow: 'angry_dropshadow',
            filter_head_gradient: 'url(#angry_head_gradient)',
            filter_dropshadow: 'url(#angry_dropshadow)',
        };
    }
    componentWillMount() {
        const head_gradient_id = _.uniqueId("angry_head_gradient-");
        const dropshadow_id = _.uniqueId("angry_dropshadow-");
        this.setState({
            head_gradient: head_gradient_id,
            dropshadow: dropshadow_id,
            filter_head_gradient: 'url(#'+head_gradient_id+')',
            filter_dropshadow: 'url(#'+dropshadow_id+')',
        });
    }
    render() {
        return (<svg
        version="1.1"
        xmlns="http://www.w3.org/2000/svg"
        xmlnsXlink="http://www.w3.org/1999/xlink"
        viewBox="0 0 194 194"
        width={this.state.width}
        height={this.state.height}
        onClick={this.props.onClick}
        style={this.props.style}
    >
    <defs>
        <linearGradient id={this.state.head_gradient} className="angry_head_gradient" x1="98.93622" y1="179.46996" x2="98.93622" y2="12.46996" gradientUnits="userSpaceOnUse">
            <stop offset="0" stopColor="#fed458"/>
            <stop offset="0.12129" stopColor="#fdcb58"/>
            <stop offset="0.31915" stopColor="#fbb357"/>
            <stop offset="0.56886" stopColor="#f78b56"/>
            <stop offset="0.85792" stopColor="#f25454"/>
            <stop offset="1" stopColor="#ef3753"/>
            </linearGradient>
        {/*<linearGradient id="angry_head_override" className="angry_head_override" x1="98.93622" y1="179.46996" x2="98.93622" y2="12.46996" gradientUnits="userSpaceOnUse"><stop offset="0" stopColor="var(--color-stop-1)"/><stop offset="0.12129" stopColor="var(--color-stop-2)"/><stop offset="0.31915" stopColor="var(--color-stop-3)"/><stop offset="0.56886" stopColor="var(--color-stop-4)"/><stop offset="0.85792" stopColor="var(--color-stop-5)"/><stop offset="1" stopColor="var(--color-stop-6)"/></linearGradient>*/}
    <filter id={this.state.dropshadow} x="-40%" y="-40%" width="180%" height="180%" filterUnits="userSpaceOnUse">
        <feGaussianBlur in="SourceAlpha" stdDeviation="3"/>
            <feOffset dx="5" dy="5" result="offsetblur"/>
            <feOffset dx="-5" dy="-5" result="offsetblur"/>
        <feMerge>
        <feMergeNode/>
        <feMergeNode in="SourceGraphic"/>
        <feMergeNode in="SourceGraphic"/>
            </feMerge>
            </filter>
            </defs>
            <g>
            <circle className="angry-a-head" opacity="0.24" fill="#000" filter={this.state.filter_dropshadow} cx="99.18622" cy="95.96996" r="84"/>
            <circle className="angry-a-face" fill={this.state.filter_head_gradient} cx="98.93622" cy="95.96996" r="83.5"/>
            <ellipse className="angry-a-eye" fill="#262c38" cx="61.93622" cy="118.46996" rx="8.5" ry="8"/>
            <ellipse className="angry-a-eye" fill="#262c38" cx="134.93622" cy="118.46996" rx="8.5" ry="8"/>
            <path className="angry-a-eyebrow" fill="none" stroke="#262c38" strokeLinecap="round" strokeLinejoin="round" strokeWidth="9" d="M34.43622,100.47s21,15,53,11"/>
            <path className="angry-a-eyebrow" fill="none" stroke="#262c38" strokeLinecap="round" strokeLinejoin="round" strokeWidth="9" d="M110.43622,112.47s23,5,50-12"/>
            <path className="angry-a-mouth" fill="#262c38" d="M71.43622,147.47s3-7,26-7,28,7,28,7,6,5-11,5h-35S69.43622,152.47,71.43622,147.47Z"/>
            </g>
            </svg>);
    }
}
class ReactionCool extends React.Component{
    constructor(props) {
        super(props);
        this.state = {
            width: parseInt(_.get(this.props, 'width', 24)),
            height: parseInt(_.get(this.props, 'height', 24)),
            name: parseInt(_.get(this.props, 'name', 'Cool')),
            head_gradient: 'cool_head_gradient',
            dropshadow: 'cool_dropshadow',
            filter_head_gradient: 'url(#cool_head_gradient)',
            filter_dropshadow: 'url(#cool_dropshadow)',
            cheek_left: 'cool-cheek-left',
            cheek_right: 'cool-cheek-right',
            filter_cheek_left: 'url(#cool-cheek-left)',
            filter_cheek_right: 'url(#cool-cheek-right)'
        };
    }
    componentWillMount() {
        const head_gradient_id = _.uniqueId("cool_head_gradient-");
        const dropshadow_id = _.uniqueId("cool_dropshadow-");
        const cheekleft_id = _.uniqueId("cool_cheek_left-");
        const cheekright_id = _.uniqueId("cool_cheek_right-");
        this.setState({
            head_gradient: head_gradient_id,
            dropshadow: dropshadow_id,
            filter_head_gradient: 'url(#'+head_gradient_id+')',
            filter_dropshadow: 'url(#'+dropshadow_id+')',
            cheek_left: cheekleft_id,
            cheek_right: cheekright_id,
            filter_cheek_left: 'url(#'+cheekleft_id+')',
            filter_cheek_right: 'url(#'+cheekright_id+')'
        });
    }
    render() {
        return (<svg
        version="1.1"
        xmlns="http://www.w3.org/2000/svg"
        xmlnsXlink="http://www.w3.org/1999/xlink"
        viewBox="0 0 193 194"
        width={this.state.width}
        height={this.state.height}
        onClick={this.props.onClick}
        style={this.props.style}
    >
    <defs>
        <linearGradient id={this.state.head_gradient} x1="98.5" y1="179.46996" x2="98.5" y2="12.46996" gradientUnits="userSpaceOnUse">
            <stop offset="0" stopColor="#fecc68"/>
            <stop offset="0.65099" stopColor="#fed05f"/>
            <stop offset="1" stopColor="#fed458"/>
            </linearGradient>
            <filter id={this.state.dropshadow} x="-40%" y="-40%" width="180%" height="180%" filterUnits="userSpaceOnUse">
        <feGaussianBlur in="SourceAlpha" stdDeviation="3"/>
            <feOffset dx="5" dy="5" result="offsetblur"/>
            <feOffset dx="-5" dy="-5" result="offsetblur"/>
        <feMerge>
        <feMergeNode/>
        <feMergeNode in="SourceGraphic"/>
        <feMergeNode in="SourceGraphic"/>
            </feMerge>
            </filter>
            <radialGradient id={this.state.cheek_left} cx="51.51836" cy="115.51837" r="22.48164" gradientUnits="userSpaceOnUse">
            <stop offset="0" stopColor="#f77669"/>
            <stop offset="0.18825" stopColor="#f88269"/>
            <stop offset="0.52943" stopColor="#fba269"/>
            <stop offset="0.98208" stopColor="#ffd66a"/>
            <stop offset="1" stopColor="#ffd86a"/>
            </radialGradient>
            <radialGradient id={this.state.cheek_right} cx="141.48165" cy="73.48173" r="22.48169" gradientUnits="userSpaceOnUse">
            <stop offset="0" stopColor="#f77669"/>
            <stop offset="0.18825" stopColor="#f88269"/>
            <stop offset="0.52943" stopColor="#fba269"/>
            <stop offset="0.98208" stopColor="#ffd66a"/>
            <stop offset="1" stopColor="#ffd86a"/>
            </radialGradient>
        {/*<linearGradient id="cool_head_override" className="cool_head_override" x1="98.5" y1="179.46996" x2="98.5" y2="12.46996" gradientUnits="userSpaceOnUse"><stop offset="0" stopColor="var(--color-stop-1)"/><stop offset="0.12129" stopColor="var(--color-stop-2)"/><stop offset="0.31915" stopColor="var(--color-stop-3)"/></linearGradient>*/}
        {/*<radialGradient id="cool-cheek-override" className="cool-cheek-override" cx="51.51836" cy="115.51837" r="22.48164" gradientUnits="userSpaceOnUse"><stop offset="0" stopColor="#f77669"/><stop offset="0.18825" stopColor="#f88269"/><stop offset="0.52943" stopColor="#fba269"/><stop offset="0.98208" stopColor="#ffd66a"/><stop offset="1" stopColor="#ffd86a"/></radialGradient>*/}
    </defs>
        <g>
        <circle className="cool-a-head" opacity="0.24" fill="#000" filter={this.state.filter_dropshadow} cx="99.18622" cy="95.96996" r="84"/>
            <circle className="cool-a-face" fill={this.state.filter_head_gradient} cx="98.5" cy="95.96996" r="83.5"/>
            <path className="cool-a-eyebrow" fill="none" stroke="#262c38" strokeLinecap="round" strokeMiterlimit="10" strokeWidth="9" d="M99.864,64.49594c-.58553-.94091-4.64976-7.7203-1.94314-15.53034,3.22165-9.29533,13.05562-11.45527,14.1184-11.66962,7.61943-1.53681,15.68954,1.8043,20.70515,8.48347"/>
            <path className="cool-a-eyebrow" fill="none" stroke="#262c38" strokeLinecap="round" strokeMiterlimit="10" strokeWidth="9" d="M69,76.3583c-.53069-.973-4.43-7.84856-12.56043-9.33834C46.7629,65.247,40.04078,72.7427,39.33091,73.562c-5.08945,5.87524-6.155,14.54421-2.81123,22.19845"/>
            <circle className="cool-a-cheek-left" fill={this.state.filter_cheek_left} cx="51.51835" cy="115.51837" r="22.48164"/>
            <circle className="cool-a-cheek-right" fill={this.state.filter_cheek_right} cx="141.48164" cy="73.48168" r="22.48169"/>
            <path className="cool-a-mouth" fill="none" stroke="#262c38" strokeLinecap="round" strokeMiterlimit="10" strokeWidth="9" d="M133,106.11151c-.74455,2.31091-5.93363,17.52593-22.292,24.62988-15.91878,6.91315-30.18734.83529-32.4717-.18346"/>
            </g>
            </svg>);
    }
}
class ReactionConfused extends React.Component{
    constructor(props) {
        super(props);
        this.state = {
            width: parseInt(_.get(this.props, 'width', 24)),
            height: parseInt(_.get(this.props, 'height', 24)),
            name: parseInt(_.get(this.props, 'name', 'Confused')),
            head_gradient: 'confused_head_gradient',
            dropshadow: 'confused_dropshadow',
            filter_head_gradient: 'url(#confused_head_gradient)',
            filter_dropshadow: 'url(#confused_dropshadow)',
        };
    }
    componentWillMount() {
        const head_gradient_id = _.uniqueId("confused_head_gradient-");
        const dropshadow_id = _.uniqueId("confused_dropshadow-");
        this.setState({
            head_gradient: head_gradient_id,
            dropshadow: dropshadow_id,
            filter_head_gradient: 'url(#'+head_gradient_id+')',
            filter_dropshadow: 'url(#'+dropshadow_id+')',
        });
    }
    render() {
        return (<svg
        version="1.1"
        xmlns="http://www.w3.org/2000/svg"
        xmlnsXlink="http://www.w3.org/1999/xlink"
        viewBox="0 0 193 193"
        width={this.state.width}
        height={this.state.height}
        onClick={this.props.onClick}
        style={this.props.style}
    >
    <defs>
        <linearGradient id={this.state.head_gradient} x1="98.5" y1="179" x2="98.5" y2="12" gradientUnits="userSpaceOnUse">
            <stop offset="0" stopColor="#fecc68"/>
            <stop offset="0.65099" stopColor="#fed05f"/>
            <stop offset="1" stopColor="#fed458"/>
            </linearGradient>
        {/*<linearGradient id="confused_head_override" className="angry_head_override" x1="98.5" y1="179" x2="98.5" y2="12" gradientUnits="userSpaceOnUse"><stop offset="0" stopColor="var(--color-stop-1)"/><stop offset="0.65099" stopColor="var(--color-stop-2)"/><stop offset="1" stopColor="var(--color-stop-3)"/></linearGradient>*/}
    <filter id={this.state.dropshadow} x="-40%" y="-40%" width="180%" height="180%" filterUnits="userSpaceOnUse">
        <feGaussianBlur in="SourceAlpha" stdDeviation="3"/>
            <feOffset dx="5" dy="5" result="offsetblur"/>
            <feOffset dx="-5" dy="-5" result="offsetblur"/>
        <feMerge>
        <feMergeNode/>
        <feMergeNode in="SourceGraphic"/>
        <feMergeNode in="SourceGraphic"/>
            </feMerge>
            </filter>
            </defs>
            <g>
            <circle className="confused-a-head" opacity="0.24" fill="#000" filter={this.state.filter_dropshadow} cx="98.5" cy="95.5" r="84"/>
            <circle className="confused-a-face" fill={this.state.filter_head_gradient} cx="98.5" cy="95.5" r="83.5"/>
            <path className="confused-a-eyebrow" fill="none" stroke="#262c38" strokeLinecap="round" strokeLinejoin="round" strokeWidth="9" d="M38,55.77461s3.66137-2.74469,12.94886-7.025A120.21579,120.21579,0,0,1,65.18926,43"/>
            <path className="confused-a-eyebrow" fill="none" stroke="#262c38" strokeLinecap="round" strokeLinejoin="round" strokeWidth="9" d="M130,57s4.5744.18566,13.71592,3.87049,13.27,6.62451,13.27,6.62451"/>
            <ellipse className="confused-a-eyeballs" fill="#fff" cx="133.41667" cy="101" rx="28.41667" ry="31"/>
            <ellipse className="confused-a-eye" fill="#262c38" cx="134.33333" cy="102.08333" rx="8.5" ry="9.91667"/>
            <ellipse className="confused-a-eyeballs" fill="#fff" cx="63.41667" cy="101" rx="28.41667" ry="31"/>
            <ellipse className="confused-a-eye" fill="#262c38" cx="64.33333" cy="102.08333" rx="8.5" ry="9.91667"/>
            <path className="confused-a-mouth" fill="#262c38" d="M97.08131,146.25s.92238-5.25,7.994-5.25,8.60887,5.25,8.60887,5.25,1.84476,3.75-3.38206,3.75H99.541S96.46639,150,97.08131,146.25Z"/>
            </g>
            </svg>);
    }
}
class ReactionAll extends React.Component{
    render() {
        var _total = parseInt(_.get(this.props, 'total', 0));
        var iconSize = parseInt(_.get(this.props, 'width', 20));

        return (<span style={{verticalAlign:"middle", display: "inline-block", minWidth: iconSize+"px", textAlign: "center"}} onClick={this.props.onClick}>{_total}</span>);
    }
}

class ReactionReviewListItem extends React.Component{
    render() {
        var _width = parseInt(_.get(this.props, 'width', 24));
        var _height = parseInt(_.get(this.props, 'height', 24));
        var _lineHeight = parseInt(_.get(this.props, 'lineHeight', 24));

        return (
            <span
                style={{ display: "inline-block", float: "left", width: "auto", height: _height+"px",lineHeight: _lineHeight+"px"}}
                onClick={this.props.onClick}>
            {this.props.children}
            </span>
        );
    }
}

class ReactionModalInBody extends React.Component{
    constructor(props) {
        super(props);

        this.popup = null;
    }
    componentDidMount() {
        this.popup = document.createElement("div");
        document.body.appendChild(this.popup);
        this._renderLayer();
    }
    _renderLayer() {
        ReactDOM.render(this.props.children, this.popup);
    }
    componentDidUpdate() {
        this._renderLayer();
    }
    componentWillUnmount() {
        ReactDOM.unmountComponentAtNode(this.popup);
        document.body.removeChild(this.popup);
    }
    render() {
        // Render a placeholder
        //return (React.createElement('div', this.props, ''));
        return (<div></div>);
    }
}

class ReactionModalContent extends React.Component{
    constructor(props) {
        super(props);

        this.state = {
            top: 0,
            left: 0
        }

        this.interval = null;

        this.wrapperRef = null;
        //ClickOutside ------
        this.setWrapperRef = this.setWrapperRef.bind(this);
        this.handleClickOutside = this.handleClickOutside.bind(this);
        //End ---------------
    }

    componentDidMount() {
        //ClickOutside ------
        // document.addEventListener('mousedown', this.handleClickOutside);
        // document.addEventListener("pointerdown", this.handleClickOutside);
        // document.addEventListener("touchstart", this.handleClickOutside);

        document.addEventListener('mouseup', this.handleClickOutside);
        document.addEventListener("pointerup", this.handleClickOutside);
        document.addEventListener("touchend", this.handleClickOutside);

        var position = this.calculaterModalPosition();

        this.setState(prevState => ({
            top: position.top,
            left: position.left
        }));

        var self = this;
        this.interval = setInterval(function() {
            self.props.callBackOfChild({
                action: 'closeModal'
            });
        }, 3000);
    }
    componentWillUnmount() {
        //ClickOutside ------
        // document.removeEventListener('mousedown', this.handleClickOutside);
        // document.removeEventListener('pointerdown', this.handleClickOutside);
        // document.removeEventListener('touchstart', this.handleClickOutside);

        document.removeEventListener('mouseup', this.handleClickOutside);
        document.removeEventListener('pointerup', this.handleClickOutside);
        document.removeEventListener('touchend', this.handleClickOutside);

        clearInterval(this.interval);
    }
    //ClickOutside ------ Set the wrapper ref
    setWrapperRef(node) {
        this.wrapperRef = node;
    }
    //ClickOutside ------ event if click out side
    handleClickOutside(event) {
        if (this.wrapperRef && !this.wrapperRef.contains(event.target)) {
            this.props.callBackOfChild({
                action: 'closeModal'
            });
        }
    }

    calculaterModalPosition(){
        var windowWidth = window.innerWidth;
        var rectBounding = ReactDOM.findDOMNode(this).getBoundingClientRect();
        var rectCenterX = (rectBounding.width/2);
        var rectCenterY = (rectBounding.height/2);

        var modalOffsetTop = 0;
        var modalOffsetLeft = 0;
        var modalOffsetRight = 0;

        var parentPosition = this.props.position;
        var parentCenterX = (parentPosition.width/2) + parentPosition.left;

        var parentCenterYT = parentPosition.top;

        modalOffsetTop = parentCenterYT - (rectCenterY + parentPosition.defaultHeight);
        modalOffsetLeft = parentCenterX - rectCenterX;
        modalOffsetRight = modalOffsetLeft + rectBounding.width;

        if(modalOffsetTop < 0){
            modalOffsetTop = parentCenterYT + (rectCenterY + parentPosition.defaultHeight);
        }

        if(modalOffsetLeft < 0 && modalOffsetRight <= windowWidth){
            modalOffsetLeft = 5;
        }else if(modalOffsetLeft >= 0 && modalOffsetRight > windowWidth){
            modalOffsetLeft = modalOffsetLeft - (modalOffsetRight - windowWidth) - 5;
        }

        return {
            top: modalOffsetTop,
            left: modalOffsetLeft
        }
    }

    render() {
        return (
            <div style={Object.assign({top: this.state.top+"px", left: this.state.left+"px"}, this.props.style)} ref={this.setWrapperRef}>
                {this.props.children}
            </div>
        );
    }
}

export class ReactionReview extends React.Component{
    constructor(props) {
        super(props);

        var iconSize = parseInt(_.get(this.props, 'iconSize', 20));

        this.icon = {
            props: {width: iconSize, height: iconSize, lineHeight: iconSize},
            svgProps: {width: iconSize, height: iconSize},
            style: Object.assign({display: "inline-block", height: iconSize+"px", verticalAlign: "middle", color: "rgb(144, 148, 156)"}, this.props.style)
        };

        this.state = {
            objectId: this.props.id,
            objectType: this.props.objectType,
            reactionType: {
                all:      { type: -1, sysActive: 1,  total: parseInt(this.props.countAll) },
                like:     { type: this.props.typeList.like.type, sysActive: this.props.typeList.like.sysActive, total: parseInt(this.props.typeList.like.count) },
                love:     { type: this.props.typeList.love.type, sysActive: this.props.typeList.love.sysActive, total: parseInt(this.props.typeList.love.count) },
                haha:     { type: this.props.typeList.haha.type, sysActive: this.props.typeList.haha.sysActive, total: parseInt(this.props.typeList.haha.count) },
                wow:      { type: this.props.typeList.wow.type, sysActive: this.props.typeList.wow.sysActive, total: parseInt(this.props.typeList.wow.count) },
                sad:      { type: this.props.typeList.sad.type, sysActive: this.props.typeList.sad.sysActive, total: parseInt(this.props.typeList.sad.count) },
                angry:    { type: this.props.typeList.angry.type, sysActive: this.props.typeList.angry.sysActive, total: parseInt(this.props.typeList.angry.count) },
                cool:     { type: this.props.typeList.cool.type, sysActive: this.props.typeList.cool.sysActive, total: parseInt(this.props.typeList.cool.count) },
                confused: { type: this.props.typeList.confused.type, sysActive: this.props.typeList.confused.sysActive, total: parseInt(this.props.typeList.confused.count) }
            }
        }

        this.handleViewWhoReacted = this.handleViewWhoReacted.bind(this);
    }

    componentWillReceiveProps(nextProps) {
        var reactionType = this.state.reactionType;

        reactionType.all.total = parseInt(nextProps.countAll);

        reactionType.like.total = parseInt(nextProps.typeList.like.count);
        reactionType.love.total = parseInt(nextProps.typeList.love.count);
        reactionType.haha.total = parseInt(nextProps.typeList.haha.count);
        reactionType.wow.total = parseInt(nextProps.typeList.wow.count);
        reactionType.sad.total = parseInt(nextProps.typeList.sad.count);
        reactionType.angry.total = parseInt(nextProps.typeList.angry.count);
        reactionType.cool.total = parseInt(nextProps.typeList.cool.count);
        reactionType.confused.total = parseInt(nextProps.typeList.confused.count);

        this.setState({
            reactionType: reactionType
        });

// if(this.props.id == 147){
//     console.log('nextProps trong reaction component', nextProps);
//     console.log('state trong reactionVIEW component', this.state);
// }

    }

    handleViewWhoReacted(actionType){
        var objectId, objectType, reactionType;
        objectId = this.state.objectId;
        objectType = this.state.objectType;

        switch (actionType) {
            case "like":
                reactionType = this.state.reactionType.like.type;
                break;
            case "love" :
                reactionType = this.state.reactionType.love.type;
                break;
            case "haha" :
                reactionType = this.state.reactionType.haha.type;
                break;
            case "wow" :
                reactionType = this.state.reactionType.wow.type;
                break;
            case "sad" :
                reactionType = this.state.reactionType.sad.type;
                break;
            case "angry" :
                reactionType = this.state.reactionType.angry.type;
                break;
            case "cool" :
                reactionType = this.state.reactionType.cool.type;
                break;
            case "confused" :
                reactionType = this.state.reactionType.confused.type;
                break;
            case "all":
                reactionType = this.state.reactionType.all.type;
                break;
        }

        var url = mooConfig.url.full + mooConfig.url.base+'/api/'+ objectType +'/reaction/view/'+ objectId +'/'+reactionType + '?access_token='+mooConfig.access_token;
        //var url = mooConfig.url.full + mooConfig.url.base+'/reactions/ajax_show/'+ objectType +'/'+ objectId +'/'+reactionType + '?access_token='+mooConfig.access_token;

        window.location.href = url;
    }

    render() {
        var flag, all, dislike, like, love, haha, wow, sad, angry, cool, confused;
        flag = false;

        if(this.state.reactionType.like.sysActive && this.state.reactionType.like.total > 0){
            like = <ReactionReviewListItem {...this.icon.props} onClick={() => this.handleViewWhoReacted("like")}>
                <ReactionLike {...this.icon.svgProps} name="Like"></ReactionLike>
            </ReactionReviewListItem>;
            flag = true;
        }else{
            like = '';
        }
        if(this.state.reactionType.love.sysActive && this.state.reactionType.love.total > 0){
            love = <ReactionReviewListItem {...this.icon.props} onClick={() => this.handleViewWhoReacted("love")}>
                <ReactionLove {...this.icon.svgProps} name="Love"></ReactionLove>
            </ReactionReviewListItem>;
            flag = true;
        }else{
            love = '';
        }
        if(this.state.reactionType.haha.sysActive && this.state.reactionType.haha.total > 0){
            haha = <ReactionReviewListItem {...this.icon.props} onClick={() => this.handleViewWhoReacted("haha")}>
                <ReactionHaha {...this.icon.svgProps} name="Haha"></ReactionHaha>
            </ReactionReviewListItem>;
            flag = true;
        }else{
            haha = '';
        }
        if(this.state.reactionType.wow.sysActive && this.state.reactionType.wow.total > 0){
            wow = <ReactionReviewListItem {...this.icon.props} onClick={() => this.handleViewWhoReacted("wow")}>
                <ReactionWow {...this.icon.svgProps} name="Wow"></ReactionWow>
            </ReactionReviewListItem>;
            flag = true;
        }else{
            wow = '';
        }
        if(this.state.reactionType.sad.sysActive && this.state.reactionType.sad.total > 0){
            sad = <ReactionReviewListItem {...this.icon.props} onClick={() => this.handleViewWhoReacted("sad")}>
                <ReactionSad {...this.icon.svgProps} name="Sad"></ReactionSad>
            </ReactionReviewListItem>;
            flag = true;
        }else{
            sad = '';
        }
        if(this.state.reactionType.angry.sysActive && this.state.reactionType.angry.total > 0){
            angry = <ReactionReviewListItem {...this.icon.props} onClick={() => this.handleViewWhoReacted("angry")}>
                <ReactionAngry {...this.icon.svgProps} name="Angry"></ReactionAngry>
            </ReactionReviewListItem>;
            flag = true;
        }else{
            angry = '';
        }
        if(this.state.reactionType.cool.sysActive && this.state.reactionType.cool.total > 0){
            cool = <ReactionReviewListItem {...this.icon.props} onClick={() => this.handleViewWhoReacted("cool")}>
                <ReactionCool {...this.icon.svgProps} name="Cool"></ReactionCool>
            </ReactionReviewListItem>;
            flag = true;
        }else{
            cool = '';
        }
        if(this.state.reactionType.confused.sysActive && this.state.reactionType.confused.total > 0){
            confused = <ReactionReviewListItem {...this.icon.props} onClick={() => this.handleViewWhoReacted("confused")}>
                <ReactionConfused {...this.icon.svgProps} name="Confused"></ReactionConfused>
            </ReactionReviewListItem>;
            flag = true;
        }else{
            confused = '';
        }
        if(this.state.reactionType.all.sysActive && this.state.reactionType.all.total > 0){
            all = <ReactionReviewListItem {...this.icon.props} onClick={() => this.handleViewWhoReacted("all")}>
                <ReactionAll {...this.icon.svgProps} total={this.state.reactionType.all.total}></ReactionAll>
            </ReactionReviewListItem>;
            flag = true;
        }else{
            all = '';
        }

        if(flag = true){
            return (
                <span style={this.icon.style}>
                    {like}{love}{haha}{wow}{sad}{angry}{cool}{confused}{all}
                </span>
            );
        }
        else{
            return '';
        }
    }
}
export class ReactionButton extends React.Component{
    constructor(props) {
        super(props);

        var iconSize = parseInt(_.get(this.props, 'iconSize', 35));
        var iconSizeActive = iconSize + 13;
        var boxSize = parseInt(_.get(this.props, 'boxSize', 35));
        var likeSize = parseInt(_.get(this.props, 'likeSize', 24));

        this.iconDefaultProps = {width: likeSize, height: likeSize, style: {verticalAlign: "middle"}};

        this.icon = {
            width: iconSize, height: iconSize,
            pros: {width: iconSize, height: iconSize},
            styleActive: {transform: "scale(1.3) translate(0, -6px)"},
            parentStyle:{width: iconSize+"px", height: iconSize+"px", display: "flex", alignItems: "flex-start", justifyContent: "center"},
            parentStyle2:{width: iconSizeActive+"px", height: iconSize+"px", display: "flex", alignItems: "flex-start", justifyContent: "center"}
        };
        this.modal = {
            overlayStyle : {backgroundColor: "rgba(255,255,255,0)", width: "100%", height: "100%", display: "block", position: "fixed", top: "0", left: "0", bottom: "0", right: "0", zIndex: "1000"},
            boxStyle: { display: "flex", position: "fixed", backgroundColor: "#ffffff", borderRadius: "90px", padding: "1px 3px", boxShadow: "0px 0px 60px rgba(0, 0, 0, 0.2)", zIndex: "1001", width: "auto",
                        height: this.icon.height+"px"
                      }
        };

        this.state = {
            reactionEnable: this.props.isPluginActive,
            reactionIsLike: this.props.isViewerLiked, // true | fasle
            reactionActive: this.props.isViewerReactionLabel, // '' | like | haha | love ...
            reactionActiveName: this.props.typeList.like.name,
            reactionType: {
                //dislike:  { type: 0,  sysActive: false, reacted: false },
                like:     { type: this.props.typeList.like.type, sysActive: this.props.typeList.like.sysActive, reacted: this.props.typeList.like.reacted, name: this.props.typeList.like.name, width: this.icon.width, height: this.icon.height, style: {}, parent: {} },
                love:     { type: this.props.typeList.love.type, sysActive: this.props.typeList.love.sysActive, reacted: this.props.typeList.love.reacted, name: this.props.typeList.love.name, width: this.icon.width, height: this.icon.height, style: {}, parent: {} },
                haha:     { type: this.props.typeList.haha.type, sysActive: this.props.typeList.haha.sysActive, reacted: this.props.typeList.haha.reacted, name: this.props.typeList.haha.name, width: this.icon.width, height: this.icon.height, style: {}, parent: {} },
                wow:      { type: this.props.typeList.wow.type, sysActive: this.props.typeList.wow.sysActive, reacted: this.props.typeList.wow.reacted, name: this.props.typeList.wow.name, width: this.icon.width, height: this.icon.height, style: {}, parent: {} },
                sad:      { type: this.props.typeList.sad.type, sysActive: this.props.typeList.sad.sysActive, reacted: this.props.typeList.sad.reacted, name: this.props.typeList.sad.name, width: this.icon.width, height: this.icon.height, style: {}, parent: {} },
                angry:    { type: this.props.typeList.angry.type, sysActive: this.props.typeList.angry.sysActive, reacted: this.props.typeList.angry.reacted, name: this.props.typeList.angry.name, width: this.icon.width, height: this.icon.height, style: {}, parent: {} },
                cool:     { type: this.props.typeList.cool.type, sysActive: this.props.typeList.cool.sysActive, reacted: this.props.typeList.cool.reacted, name: this.props.typeList.cool.name, width: this.icon.width, height: this.icon.height, style: {}, parent: {} },
                confused: { type: this.props.typeList.confused.type, sysActive: this.props.typeList.confused.sysActive, reacted: this.props.typeList.confused.reacted, name: this.props.typeList.confused.name, width: this.icon.width, height: this.icon.height, style: {}, parent: {} },
            },
            objectType: this.props.objectType,
            objectId: this.props.id,
            modalboxStyle: this.modal.boxStyle,
            position: { top: 0, left: 0, width: 0, height: 0, defaultHeight: 0},
            isOpen: false
        }

        this.Button = {
            //warpperStyle: {height: "35px", padding: "0 0", textAlign: "center", flex: "1", color: "#5e5e5e", fontSize: "12px"},
            warpperStyle: {position: "relative", height: boxSize+"px", lineHeight: boxSize+"px", textAlign: "center", color: "#5e5e5e", whiteSpace: "nowrap"},
            //iconButtonStyle: {height: boxSize+"px", padding: "0", fontSize: "12px"}
            labelStyle: {verticalAlign: "middle", lineHeight: likeSize+"px", marginLeft: "3px"}
        }

        // This binding is necessary to make `this` work in the callback
        this.handleOpenModal = this.handleOpenModal.bind(this);
        this.handleCloseModal = this.handleCloseModal.bind(this);
        this.handleReactionChange = this.handleReactionChange.bind(this);

        this.callBackCloseModal = this.callBackCloseModal.bind(this);

        this.callBackModalContent = this.callBackModalContent.bind(this);
    }

    componentWillReceiveProps(nextProps) {
        var reactionType = this.state.reactionType;
        var reactionActiveName = nextProps.typeList.like.name;

        reactionType.like.reacted = nextProps.typeList.like.reacted;
        reactionType.like.sysActive = nextProps.typeList.like.sysActive;
        if(reactionType.like.reacted){
            reactionType.like.style = this.icon.styleActive;
            reactionType.like.parent = this.icon.parentStyle2;
            reactionActiveName = nextProps.typeList.like.name;
        }else{
            reactionType.like.style = {};
            reactionType.like.parent = this.icon.parentStyle;
        }

        reactionType.love.reacted = nextProps.typeList.love.reacted;
        reactionType.love.sysActive = nextProps.typeList.love.sysActive;
        if(reactionType.love.reacted){
            reactionType.love.style = this.icon.styleActive;
            reactionType.love.parent = this.icon.parentStyle2;
            reactionActiveName = nextProps.typeList.love.name;
        }else{
            reactionType.love.style = {};
            reactionType.love.parent = this.icon.parentStyle;
        }

        reactionType.haha.reacted = nextProps.typeList.haha.reacted;
        reactionType.haha.sysActive = nextProps.typeList.haha.sysActive;
        if(reactionType.haha.reacted){
            reactionType.haha.style = this.icon.styleActive;
            reactionType.haha.parent = this.icon.parentStyle2;
            reactionActiveName = nextProps.typeList.haha.name;
        }else{
            reactionType.haha.style = {};
            reactionType.haha.parent = this.icon.parentStyle;
        }

        reactionType.wow.reacted = nextProps.typeList.wow.reacted;
        reactionType.wow.sysActive = nextProps.typeList.wow.sysActive;
        if(reactionType.wow.reacted){
            reactionType.wow.style = this.icon.styleActive;
            reactionType.wow.parent = this.icon.parentStyle2;
            reactionActiveName = nextProps.typeList.wow.name;
        }else{
            reactionType.wow.style = {};
            reactionType.wow.parent = this.icon.parentStyle;
        }

        reactionType.sad.reacted = nextProps.typeList.sad.reacted;
        reactionType.sad.sysActive = nextProps.typeList.sad.sysActive;
        if(reactionType.sad.reacted){
            reactionType.sad.style = this.icon.styleActive;
            reactionType.sad.parent = this.icon.parentStyle2;
            reactionActiveName = nextProps.typeList.sad.name;
        }else{
            reactionType.sad.style = {};
            reactionType.sad.parent = this.icon.parentStyle;
        }

        reactionType.angry.reacted = nextProps.typeList.angry.reacted;
        reactionType.angry.sysActive = nextProps.typeList.angry.sysActive;
        if(reactionType.angry.reacted){
            reactionType.angry.style = this.icon.styleActive;
            reactionType.angry.parent = this.icon.parentStyle2;
            reactionActiveName = nextProps.typeList.angry.name;
        }else{
            reactionType.angry.style = {};
            reactionType.angry.parent = this.icon.parentStyle;
        }

        reactionType.cool.reacted = nextProps.typeList.cool.reacted;
        reactionType.cool.sysActive = nextProps.typeList.cool.sysActive;
        if(reactionType.cool.reacted){
            reactionType.cool.style = this.icon.styleActive;
            reactionType.cool.parent = this.icon.parentStyle2;
            reactionActiveName = nextProps.typeList.cool.name;
        }else{
            reactionType.cool.style = {};
            reactionType.cool.parent = this.icon.parentStyle;
        }

        reactionType.confused.reacted = nextProps.typeList.confused.reacted;
        reactionType.confused.sysActive = nextProps.typeList.confused.sysActive;
        if(reactionType.confused.reacted){
            reactionType.confused.style = this.icon.styleActive;
            reactionType.confused.parent = this.icon.parentStyle2;
            reactionActiveName = nextProps.typeList.confused.name;
        }else{
            reactionType.confused.style = {};
            reactionType.confused.parent = this.icon.parentStyle;
        }

        document.body.style.overflow = null;

        this.setState({
            reactionEnable: nextProps.isPluginActive,
            reactionIsLike: nextProps.isViewerLiked,
            reactionActive: nextProps.isViewerReactionLabel,
            reactionActiveName: reactionActiveName,
            isOpen: false,
            reactionType: reactionType
        });

// if(this.props.id == 147){
//     console.log('nextProps trong reaction component', nextProps);
//     console.log('state trong reaction component', this.state);
// }

    }

    callBackCloseModal(){
        this.handleCloseModal();
    }

    handleOpenModal(){
        var rectBounding = ReactDOM.findDOMNode(this).getBoundingClientRect();
        document.body.style.overflow = "hidden";
        this.setState(prevState => ({
            isOpen: true,
            position: { top: rectBounding.top, left: rectBounding.left, width: rectBounding.width, height: rectBounding.height, defaultHeight: this.iconDefaultProps.height}
            //modalboxStyle: Object.assign(position, this.modal.boxStyle)
        }));
    }
    handleCloseModal(){
        document.body.style.overflow = null;
        this.setState(prevState => ({
            isOpen: false
        }));
    }

    handleReactionChange(actionType) {
        var action, id, objectType, reactionId, reactionType, reactionTypeLabel;
        id = _.get(this.props, 'id', 0);
        objectType = _.get(this.props, 'objectType', '');
        reactionId = id+'Reaction'+objectType;
        action = 'like';

        switch (actionType) {
            case "like":
                reactionType = this.state.reactionType.like.type;
                reactionTypeLabel = 'like';
                if(this.state.reactionType.like.reacted == 1) {
                    action = 'unlike';
                }
                break;
            case "love" :
                reactionType = this.state.reactionType.love.type;
                reactionTypeLabel = 'love';
                if(this.state.reactionType.love.reacted == 1) {
                    action = 'unlike';
                }
                break;
            case "haha" :
                reactionType = this.state.reactionType.haha.type;
                reactionTypeLabel = 'haha';
                if(this.state.reactionType.haha.reacted == 1) {
                    action = 'unlike';
                }
                break;
            case "wow" :
                reactionType = this.state.reactionType.wow.type;
                reactionTypeLabel = 'wow';
                if(this.state.reactionType.wow.reacted == 1) {
                    action = 'unlike';
                }
                break;
            case "sad" :
                reactionType = this.state.reactionType.sad.type;
                reactionTypeLabel = 'sad';
                if(this.state.reactionType.sad.reacted == 1) {
                    action = 'unlike';
                }
                break;
            case "angry" :
                reactionType = this.state.reactionType.angry.type;
                reactionTypeLabel = 'angry';
                if(this.state.reactionType.angry.reacted == 1) {
                    action = 'unlike';
                }
                break;
            case "cool" :
                reactionType = this.state.reactionType.cool.type;
                reactionTypeLabel = 'cool';
                if(this.state.reactionType.cool.reacted == 1) {
                    action = 'unlike';
                }
                break;
            case "confused" :
                reactionType = this.state.reactionType.confused.type;
                reactionTypeLabel = 'confused';
                if(this.state.reactionType.confused.reacted == 1) {
                    action = 'unlike';
                }
                break;
        }

        if(action === 'like'){
            ReactionActions.doReaction(reactionId, objectType , reactionType, reactionTypeLabel);
        }else if(action === 'unlike'){
            ReactionActions.doUnReaction(reactionId, objectType , reactionType, reactionTypeLabel);
        }
    }

    callBackModalContent(data){
        if(data.action == 'closeModal'){
            this.handleCloseModal();
        }
    }

    render() {
        if(!this.state.reactionEnable){
            return '';
        }else{

            var defaultButton, popupModal, modalIconLike, modalIconLove, modalIconHaha, modalIconWow, modalIconSad, modalIconAngry, modalIconCool, modalIconConfused;

            if(!this.state.isOpen){
                popupModal = '';
            }else{
                if(this.state.reactionType.like.sysActive){
                    modalIconLike = <span style={this.state.reactionType.like.parent}>
                            <ReactionLike name="Like" {...this.icon.pros} style={this.state.reactionType.like.style} onClick={() => this.handleReactionChange("like")}></ReactionLike>
                        </span>;
                }else{
                    modalIconLike = '';
                }
                if(this.state.reactionType.love.sysActive){
                    modalIconLove = <span style={this.state.reactionType.love.parent}>
                            <ReactionLove name="Love" {...this.icon.pros} style={this.state.reactionType.love.style} onClick={() => this.handleReactionChange("love")}></ReactionLove>
                        </span>;
                }else{
                    modalIconLove = '';
                }
                if(this.state.reactionType.haha.sysActive){
                    modalIconHaha = <span style={this.state.reactionType.haha.parent}>
                            <ReactionHaha name="Haha" {...this.icon.pros} style={this.state.reactionType.haha.style} onClick={() => this.handleReactionChange("haha")}></ReactionHaha>
                        </span>;
                }else{
                    modalIconHaha = '';
                }
                if(this.state.reactionType.wow.sysActive){
                    modalIconWow = <span style={this.state.reactionType.wow.parent}>
                            <ReactionWow name="Wow" {...this.icon.pros} style={this.state.reactionType.wow.style} onClick={() => this.handleReactionChange("wow")}></ReactionWow>
                        </span>;
                }else{
                    modalIconWow = '';
                }
                if(this.state.reactionType.cool.sysActive){
                    modalIconCool = <span style={this.state.reactionType.cool.parent}>
                <ReactionCool name="Cool" {...this.icon.pros} style={this.state.reactionType.cool.style} onClick={() => this.handleReactionChange("cool")}></ReactionCool>
                    </span>;
                }else{
                    modalIconCool = '';
                }
                if(this.state.reactionType.confused.sysActive){
                    modalIconConfused = <span style={this.state.reactionType.confused.parent}>
                <ReactionConfused name="Confused" {...this.icon.pros} style={this.state.reactionType.confused.style} onClick={() => this.handleReactionChange("confused")}></ReactionConfused>
                    </span>;
                }else{
                    modalIconConfused = '';
                }
                if(this.state.reactionType.sad.sysActive){
                    modalIconSad = <span style={this.state.reactionType.sad.parent}>
                            <ReactionSad name="Sad" {...this.icon.pros} style={this.state.reactionType.sad.style} onClick={() => this.handleReactionChange("sad")}></ReactionSad>
                        </span>;
                }else{
                    modalIconSad = '';
                }
                if(this.state.reactionType.angry.sysActive){
                    modalIconAngry = <span style={this.state.reactionType.angry.parent}>
                            <ReactionAngry name="Angry" {...this.icon.pros} style={this.state.reactionType.angry.style} onClick={() => this.handleReactionChange("angry")}></ReactionAngry>
                        </span>;
                }else{
                    modalIconAngry = '';
                }

                popupModal = <ReactionModalInBody open={this.state.isOpen}>
                    <div style={this.modal.overlayStyle}></div>
                    <ReactionModalContent style={this.state.modalboxStyle} position={this.state.position}  callBackOfChild={(object) => this.callBackModalContent(object)}>
                        {modalIconLike}{modalIconLove}{modalIconHaha}{modalIconWow}{modalIconCool}{modalIconConfused}{modalIconSad}{modalIconAngry}
                    </ReactionModalContent>
                </ReactionModalInBody>;
            }

            //onTouchStart onTouchMove onTouchCancel onMouseDown
            if(this.state.reactionIsLike == 0){
                // defaultButton = <ReactionDefault {...this.iconDefaultProps} name="Like" onClick={() => this.handleOpenModal()} onMouseDown={() => this.handlePress()} onTouchStart={() => this.handlePress("onTouchStart")} onTouchMove={() => this.handlePress("onTouchMove")} onTouchCancel={() => this.handlePress("onTouchCancel")}></ReactionDefault>;
                defaultButton = <ReactionDefault {...this.iconDefaultProps} name="Like"></ReactionDefault>;
            }else{
                if(this.state.reactionActive === 'like'){
                    defaultButton = <ReactionLike {...this.iconDefaultProps} name="Like"></ReactionLike>;
                }else if(this.state.reactionActive === 'love'){
                    defaultButton = <ReactionLove {...this.iconDefaultProps} name="Love"></ReactionLove>;
                }else if(this.state.reactionActive === 'haha'){
                    defaultButton = <ReactionHaha {...this.iconDefaultProps} name="Haha"></ReactionHaha>;
                }else if(this.state.reactionActive === 'wow'){
                    defaultButton = <ReactionWow {...this.iconDefaultProps} name="Wow"></ReactionWow>;
                }else if(this.state.reactionActive === 'cool'){
                    defaultButton = <ReactionCool {...this.iconDefaultProps} name="Cool"></ReactionCool>;
                }else if(this.state.reactionActive === 'confused'){
                    defaultButton = <ReactionConfused {...this.iconDefaultProps} name="Confused"></ReactionConfused>;
                }else if(this.state.reactionActive === 'sad'){
                    defaultButton = <ReactionSad {...this.iconDefaultProps} name="Sad"></ReactionSad>;
                }else if(this.state.reactionActive === 'angry'){
                    defaultButton = <ReactionAngry {...this.iconDefaultProps} name="Angry"></ReactionAngry>;
                }
            }

            return (
                    <div style={this.Button.warpperStyle} onClick={() => this.handleOpenModal()}>
                    {popupModal}
                    {defaultButton}<span style={this.Button.labelStyle}>{this.state.reactionActiveName}</span>
                    </div>
            );
        }

    }
}

export class ReactionLibrary {

    static getData(record){
        if(record != false){
            return {
                id: record.get('id'),
                objectType: record.get('objectType'),
                isPluginActive: record.get('isPluginActive'),
                countAll: record.get('countAll'),
                isViewerReactionType: record.get('isViewerReactionType'),
                isViewerReactionLabel: record.get('isViewerReactionLabel'),
                isViewerLiked: record.get('isViewerLiked'),
                typeList: record.get('typeList')
            };
        }
        return false;
    }

    static getDataRequest(object){
        if(object != false){
            return {
                id: _.get(object, 'objectId', 0),
                objectType: _.get(object, 'objectType', ''),
                isPluginActive: _.get(object, 'isPluginActive', 0),
                countAll: _.get(object, 'countAll', 0),
                isViewerReactionType: _.get(object, 'currentType', -1),
                isViewerReactionLabel: _.get(object, 'currentTypeLabel', ''),
                isViewerLiked: _.get(object, 'isLike', 0),
                typeList: _.get(object, 'typeList', {})
            };
        }
        return false;
    }

    static ActivityReactionButton(reaction){
        if(reaction != false){
            if(reaction.isPluginActive == 1){
                return <MuiThemeProvider>
                        <span  style={{height: "35px", padding: "0 0", textAlign: "center", flex: "1", color: "#5e5e5e", fontSize: "12px"}}>
                            <button tabIndex="0" type="button" style={{border: "10px", boxSizing: "border-box", display: "inline-block", fontFamily: "Roboto, sans-serif", 'WebkitTapHighlightColor': "rgba(0, 0, 0, 0)", cursor: "pointer", textDecoration: "none", margin: "0px", padding: "0px", outline: "none", fontSize: "12px", fontWeight: "inherit", position: "relative", overflow: "visible", transition: "all 450ms cubic-bezier(0.23, 1, 0.32, 1) 0ms", width: "48px", height: "35px", background: "none"}}>
                            {/*<IconButton style={{height: "35px", padding: "0"}}  >*/}
                                <ReactionButton {...reaction} boxSize="35" likeSize="24" iconSize="35"></ReactionButton>
                                {/*</IconButton>*/}
                                {/*<span style={{position: "absolute", top: "-10px", left: "-30px", display: "inline-block", verticalAlign: "middle"}}>{reaction.objectType}</span>*/}
                            </button>
                        </span>
                </MuiThemeProvider>;
            }
        }
        return '';
    }

    static ActivityReactionReview(reaction){
        if(reaction != false){
            if(reaction.isPluginActive == 1){
                return <ReactionReview {...reaction} iconSize="20" style={{marginTop: "-2px", marginRight: "5px", fontSize: "12px"}}></ReactionReview>;
            }
        }
        return '';
    }

    static ItemReactionButton(reaction){
        if(reaction != false){
            if(reaction.isPluginActive == 1){
                return <MuiThemeProvider>
                        <span  style={{height: "35px", padding: "0 0", textAlign: "center", flex: "1", color: "#5e5e5e", fontSize: "12px"}}>
                            <button tabIndex="0" type="button" style={{border: "10px", boxSizing: "border-box", display: "inline-block", fontFamily: "Roboto, sans-serif", 'WebkitTapHighlightColor': "rgba(0, 0, 0, 0)", cursor: "pointer", textDecoration: "none", margin: "0px", padding: "0px", outline: "none", fontSize: "12px", fontWeight: "inherit", position: "relative", overflow: "visible", transition: "all 450ms cubic-bezier(0.23, 1, 0.32, 1) 0ms", width: "48px", height: "35px", background: "none"}}>
                            {/*<IconButton style={{height: "35px", padding: "0"}}  >*/}
                                <ReactionButton {...reaction} boxSize="35" likeSize="24" iconSize="35"></ReactionButton>
                                {/*</IconButton>*/}
                                {/*<span style={{position: "absolute", top: "-10px", left: "-30px", display: "inline-block", verticalAlign: "middle"}}>{reaction.objectType}</span>*/}
                            </button>
                        </span>
                </MuiThemeProvider>;
            }
        }
        return '';
    }

    static ItemReactionReview(reaction){
        if(reaction != false){
            if(reaction.isPluginActive == 1){
                return <ReactionReview {...reaction} iconSize="20" style={{marginTop: "-2px", marginRight: "5px", fontSize: "12px"}}></ReactionReview>;
            }
        }
        return '';
    }

    static CommentReactionButton(reaction){
        if(reaction != false){
            if(reaction.isPluginActive == 1){
                return <span style={{display: "inline-block", marginLeft: "5px", marginRight: "5px"}}>
                    <ReactionButton {...reaction} boxSize="20" likeSize="20" iconSize="35"></ReactionButton>
                        </span>;
            }
        }
        return '';
    }

    static CommentReactionReview(reaction){
        if(reaction != false){
            if(reaction.isPluginActive == 1){
                return <ReactionReview {...reaction} iconSize="20" style={{fontSize: "12px"}}></ReactionReview>;
            }
        }
        return '';
    }

    static BrowserReactionReview(reaction){
        if(reaction != false){
            if(reaction.isPluginActive == 1){
                return <ReactionReview {...reaction} iconSize="21" style={{fontSize: "13px", marginTop: "-4px"}}></ReactionReview>;
            }
        }
        return '';
    }
}
