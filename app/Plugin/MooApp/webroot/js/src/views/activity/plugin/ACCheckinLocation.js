import React from 'react';
import {Card, CardActions, CardHeader, CardMedia, CardTitle, CardText} from 'material-ui/Card';
import _ from 'lodash';
import GoogleMap from 'google-map-react';
import {ACGridPhoto} from '../ACPost';


export  class ACCheckinLocation extends React.Component {
    
   
    constructor(props) {
        super(props);
    }
    
    renderMarkers(map, maps) {
        var myLatLng = {lat: parseFloat(_.get(this.props, 'objects.mapObject.lat','')) , lng: parseFloat(_.get(this.props, 'objects.mapObject.lng',''))};
        
        var contentString = '<div id="content">'+
            '<div id="siteNotice">'+
            '</div>'+
            '<p id="firstHeading" class="firstHeading"><b>'+ _.get(this.props, 'objects.mapObject.name','') +'</b></p>'+
            '<div id="bodyContent">'+
            _.get(this.props, 'objects.mapObject.address','')
            '</div>'+
            '</div>';

        var infowindow = new maps.InfoWindow({
          content: contentString
        });
        
        let marker = new maps.Marker({
          position: myLatLng,
          map,
          title: _.get(this.props, 'objects.mapObject.name','')
        });
        
        marker.addListener('click', function() {
          infowindow.open(map, marker);
        });
        
      }
    
    render() {
        var content,images , media,map;
        map = content = media = <div></div> ;
        images = [];
        if(_.has(this.props,'objects.contentHtml')){
            if(!_.isEmpty(this.props.objects.contentHtml)){
                content = <CardText style={{wordBreak:"break-word",padding:"8px"}} dangerouslySetInnerHTML={{__html: _.get(this.props, 'objects.contentHtml','')}}></CardText>;
            }
        } 
        media = <ACGridPhoto {...this.props}/>;
        
        var lat = _.get(this.props, 'objects.mapObject.lat','');
        var lng = _.get(this.props, 'objects.mapObject.lng','');
        map = <div className="checkin_map" style={{marginTop:"5px",width:"100%",height:"350px"}}>
        <GoogleMap
                bootstrapURLKeys={{ key:mooConfig.google_dev_key}}
                defaultZoom={16}
                defaultCenter={[parseFloat(lat),parseFloat(lng)]}
                onGoogleApiLoaded={({map, maps}) => this.renderMarkers(map, maps)}
                yesIWantToUseGoogleMapApiInternals
            >
            </GoogleMap>
            
        </div>;
        
        return <div>{content}{media}{map}</div>;

    }
}
