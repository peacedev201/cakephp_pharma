<?php $this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery', 'mooBusiness', 'business_markerclusterer'), 
    'object' => array('$', 'mooBusiness')
));?>
    mooBusiness.initSearchPage();
<?php $this->Html->scriptEnd(); ?>

<?php echo $this->Element('mobile_menu');?>
<?php if($is_app):?>
    <div id="map_canvas" style="width: 100%;height: 300px;display: none"></div>
<?php endif;?>
<div class="bar-content">
    <div class="content_center">
        <?php 
            switch ($task)
            {
                case 'my':
                    echo $this->element('Business.my_businesses', array(
                        'businesses' => $businesses,
                        'more_url' => $more_url
                    ));
                    break;
                case 'my_reviews':
                    echo $this->element('Business.my_review', array(
                        'reviews' => $reviews
                    ));
                    break;
                case 'my_following':
                    echo $this->element('Business.my_following', array(
                        'businesses' => $businesses,
                        'more_url' => $more_url
                    ));
                    break;
                case 'my_favourites':
                    echo $this->element('Business.my_favourites', array(
                        'businesses' => $businesses,
                        'more_url' => $more_url
                    ));
                    break;
                default:
                    echo $this->element('Business.all_businesses', array(
                        'businesses' => $businesses,
                        'locations' => $locations
                    ));
            }
        ?>
    </div>
</div>

<?php if($is_app):?>
    <script>
    function doRefesh()
    {
        location.reload();
    }
    </script>
    <?php echo $this->MooGzip->script(array('zip'=>'mobile.action.bundle.js.gz','unzip'=>'MooApp.mobile.action.bundle'));?>
<?php endif;?>