<script src="<?php echo $this->request->base; ?>/js/global/jquery-1.11.1.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&key=<?php echo Configure::read('core.google_dev_key');?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $this->request->base; ?>/css/font-awesome/css/font-awesome.min.css?v=2.4.0"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->request->base; ?>/business/css/business.css"/>
<?php echo  $this->Html->css('https://fonts.googleapis.com/css?family=Roboto:400,300,500,700'); ?>
<style>
    body{
        margin:0;
        font-family: 'Roboto';
    }
</style>
<div class="wrap_map">
    <div id="map" style="width: 100%;height: 100%;"></div>
    <?php if($direction):?>
    <div class="side-box">
        <div class="get-directions-box island">
            <div class="get-directions-content">
                <h3><?php echo __d('business', 'Get directions');?></h3>
                <?php echo $this->form->hidden('travelMode', array(
                    'value' => 'DRIVING'
                ));?>
                <div class="contentbox">
                    <div class="transit-buttons bustn-group bustn-group-full clearfix" data-component-bound="true">
                        <a class="bustn travel_mode selected" href="javascript:void(0)" data-type="DRIVING">
                            <span title="<?php echo __d('business', 'Driving');?>">
                                <i class="fa fa-car" aria-hidden="true"></i>
                            </span>
                        </a>
                        <a class="bustn travel_mode" href="javascript:;" data-type="TRANSIT">
                            <span title="<?php echo __d('business', 'Public Transit');?>">
                                <i class="fa fa-train" aria-hidden="true"></i>
                            </span>
                        </a>
                        <a class="bustn travel_mode" href="javascript:;" data-type="WALKING">
                            <span title="<?php echo __d('business', 'Walking');?>">
                                <i class="fa fa-male" aria-hidden="true"></i>
                            </span>
                        </a>
                        <a class="bustn travel_mode" href="javascript:;" data-type="BICYCLING">
                            <span title="<?php echo __d('business', 'Cycling');?>">
                                <i class="fa fa-motorcycle" aria-hidden="true"></i>
                            </span>
                        </a>
                    </div>
                    <div class="starting-point">
                        <strong><?php echo __d('business', 'Start from');?></strong>
                        <div class="location">
                            <div class="user-location nested-icon-label">
                                <div class="input-with-dropper">
                                    <input type="text" name="location" id="js-dropper-text" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ending-point">
                        <strong><?php echo __d('business', 'Destination');?></strong>
                        <div class="location">
                            <div class="business-location media-block">
                                <div class="media-story">
                                    <address>
                                        <?php echo $address;?>
                                    </address>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="javascript:void(0)" onclick="calculateAndDisplayRoute()" class="button">
                        <span><?php echo __d('business', 'Get directions');?></span>
                    </a>
                    <div id="direction-warning" style="display: none"></div>
                    <div id="direction-steps" class="direction_steps" style="display: none"></div>
                </div>
            </div>
        </div>
    </div>
    <?php endif;?>
</div>
<script type="text/javascript">
    var directionsService = '';
    var directionsDisplay = '';
    var map = '';
    var step_instructions = [];
    var step_markers = [];
    var from_marker = '';
    function initialize() {
        var myOptions = {
            zoom: 16,
            center: new google.maps.LatLng('<?php echo $lat; ?>', '<?php echo $lng; ?>'),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        <?php if ($scrollwheel == 0): ?>
            myOptions['scrollwheel'] = false;
        <?php endif; ?>
        map = new google.maps.Map(document.getElementById('map'), myOptions);
        this.map = map;

        <?php if ($position == 'center'): ?>
            map.panBy(-300, 0)
        <?php endif; ?>

        <?php if ($no_marker == 0): ?>
            var market_options = {
                map: map,
                position: new google.maps.LatLng('<?php echo $lat; ?>', '<?php echo $lng; ?>'),
            };
            marker = new google.maps.Marker(market_options);
            infowindow = new google.maps.InfoWindow({
                content: "<?php echo urldecode($address); ?>"
            });
            google.maps.event.addListener(marker, 'click', function () {
                infowindow.open(map, marker);
            });
            <?php if ($hide_info == 0): ?>
                infowindow.open(map, marker);
            <?php endif; ?>
        <?php endif; ?>

        <?php if($direction):?>
        //init direction
        directionsService = new google.maps.DirectionsService;
        directionsDisplay = new google.maps.DirectionsRenderer;
        directionsDisplay.setMap(map);
        directionsDisplay.setOptions( { suppressMarkers: true } );
        <?php endif; ?>

    }

    function calculateAndDisplayRoute() {
        deleteMarkers();
        jQuery('#direction-warning').hide();
        var selectedMode = jQuery('#travelMode').val();
        var settings = {
            origin: document.getElementById('js-dropper-text').value,
            destination: '<?php echo $address; ?>',
            travelMode: google.maps.TravelMode[selectedMode]
        };
        directionsService.route(settings, function (response, status) {
            if (status === google.maps.DirectionsStatus.OK) {
                step_instructions = [];
                directionsDisplay.setDirections(response);

                //set start from marker
                from_marker = new google.maps.Marker({
                    position: response.routes[0].legs[0].start_location,
                    map: this.map,
                    icon: 'https://chart.googleapis.com/chart?chst=d_map_pin_icon&chld=flag|ADDE63'
                });
                infowindow = new google.maps.InfoWindow({
                    content: response.routes[0].legs[0].start_address
                });
                infowindow.open(map, from_marker);
                
                //show step info
                showStepInfo(response);
            } else {
                jQuery('#direction-warning').html("<?php echo __d('business', "We didn't recognize one of your addresses. Please enter at least a city or a postal code.");?>").show();
            }
        });
    }
    
    function showStepInfo(response) {
        // Grab the result from the routes
        var routeResult = response.routes[0];

        // Iterate over each leg of the route
        routeResult.legs.forEach(function(leg) {
            // Build HTML for each step
            var stepHTML = 
                '<div class="directionStep">\n\
                    Distance: ' + routeResult.legs[0].distance.text + '<br/> \n\
                    Duration: ' + routeResult.legs[0].duration.text + ' \n\
                </div>';
            var count = 1;
            leg.steps.forEach(function(step) {
                step_instructions.push(step.instructions);
                stepHTML += 
                    '<div class="directionStep" data-location="' + step.start_location + '" data-instructions="' + (count - 1) + '" onclick="showStep(this)">'+
                        '<div>' + count + '. ' + step.instructions + '</div>'+
                        '<div class="grey">' + step.distance.text + ' (' + step.duration.text + ')' + '</div>'+
                    '</div>';
                count++;
            });

            // Put the step HTML somewhere
            jQuery('#direction-steps').html(stepHTML).show();
        });
    }
    
    function showStep(item) {
        var location = jQuery(item).data('location');
        location = location.replace('(', '');
        location = location.replace(')', '');
        location = location.split(',');
        var lat = parseFloat(location[0]);
        var lng = parseFloat(location[1]);
        var instructions = step_instructions[jQuery(item).data('instructions')]; 
        var map = this.map;
        var stepDisplay = new google.maps.InfoWindow;
        deleteMarkers();
        var market_options = {
            map: map,
            position: new google.maps.LatLng(lat, lng)
        };
        var marker = new google.maps.Marker(market_options);
        step_markers.push(marker);
        infowindow = new google.maps.InfoWindow({
            content: instructions
        });
        infowindow.open(map, marker);
        attachInstructionText(stepDisplay, marker, instructions, map);
    }
    
    function deleteMarkers() {
        setMapOnAll(null);
        step_markers = [];
        from_marker = '';
    }
    
    function setMapOnAll(map) {
        for (var i = 0; i < step_markers.length; i++) {
            step_markers[i].setMap(map);
        }
        if(from_marker != '')
        {
            from_marker.setMap(map);
        }
    }


    function attachInstructionText(stepDisplay, marker, text, map) {
        google.maps.event.addListener(marker, 'click', function() {
            // Open an info window when the marker is clicked on, containing the text
            // of the step.
            stepDisplay.setContent(text);
            stepDisplay.open(map, marker);
        });
    }
    
    jQuery('.travel_mode').click(function(){
        jQuery('.travel_mode').removeClass('selected');
        jQuery(this).addClass('selected');
        jQuery('#travelMode').val(jQuery(this).data('type'))
    })

    google.maps.event.addDomListener(window, 'load', initialize);
</script>
