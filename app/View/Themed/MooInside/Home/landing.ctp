<?php if (Configure::read('Mooinside.mooinside_enabled')): ?>
<div id="north">
<div class="headline topic-color">
    <ul id="browse">
        <li class="current home">
            <span href="<?php echo $this->request->base?>/topics/"><i class="material-icons">home</i></span>
        </li>
    </ul>

    <div class="clear"></div>
</div>
</div>
<div class="main_content landing_page">
   
    <div id="right"  class="sl-rsp-modal col-md-4 pull-right">
        <?php if (Configure::read('Mooinside.show_popular_topics_widget')): ?>
        <?php echo $this->element('Mooinside.popular_topics_region') ?>
        <?php endif; ?>
        
    </div>
   
    <div id="center" class="col-md-8">
        <?php if (Configure::read('Mooinside.show_popular_topics_widget')): ?>
        <?php echo $this->element('Mooinside.popular_topics') ?>
        <?php endif; ?>
        
        <!-- Category topic -->
        <?php if (Configure::read('Mooinside.show_latest_topic_of_the_first_category')): ?>
        <?php echo $this->element('Mooinside.latest_topic_of_the_first_category') ?>
        <?php endif; ?>
        <!--end cat -->
        <div class="ad_center_section">
            <img src='<?php echo $this->request->webroot ?>theme/mooInside/img/ad/ad_5.png' /> 
        </div>
        <!-- Popular Video -->
        <?php if (Configure::read('Mooinside.show_top_videos_widget')): ?>
        <?php echo $this->element('Mooinside.top_videos') ?>
        <?php endif; ?>
        
        <!-- end video -->
        <!--Album popular-->
        <?php if (Configure::read('Mooinside.show_photos_of_popular_album_widget')): ?>
        <?php echo $this->element('Mooinside.photos_of_the_popular_album') ?>
        <?php endif; ?>
        
        <!--end album-->
        
    </div>
    <div class="clear"></div>
</div>
<?php endif; ?>


