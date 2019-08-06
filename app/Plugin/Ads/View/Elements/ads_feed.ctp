<?php if($this->request->is('ajax')): ?>
<script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true,
    'requires' => array('jquery'),
    'object' => array('$'))); ?>
<?php endif; ?>
(function(){
   var ads_data = jQuery.parseJSON('<?php echo json_encode($aAds); ?>');
   var ads_key = -1;
   var ads_uid = '<?php echo $ads_uid; ?>';
   function show_ads_<?php echo  $ads_uid; ?>(){
       ads_key++;
       if(ads_key >= Object.keys(ads_data).length){
           ads_key = 0;
       }
       var current_data = ads_data[ads_key];
       var ads_feed_id = '#activity_'+ads_uid;
       jQuery(ads_feed_id +' .div_single a').attr('href',mooConfig.url.base+'/ads/update_click_count/'+current_data.id);
       jQuery(ads_feed_id +' .ads_feed_title a').attr('href',mooConfig.url.base+'/ads/update_click_count/'+current_data.id);
       jQuery(ads_feed_id + ' .div_single a img').attr('src','<?php echo  FULL_BASE_URL . $this->request->webroot.'uploads/commercial/'?>'+current_data.ads_image);
       jQuery(ads_feed_id +' .ads_feed_title a').empty().html(current_data.ads_title);
       jQuery(ads_feed_id +' .ads_feed_description').empty().html(current_data.description);
          jQuery.get(mooConfig.url.base+'/ads/update_view_count/'+current_data.id,function(data){
          setTimeout(show_ads_<?php echo  $ads_uid; ?>,<?php echo $time_interval; ?>);
   });
       
   }

   setTimeout(show_ads_<?php echo  $ads_uid; ?>,1000);
})();
<?php if($this->request->is('ajax')): ?>
</script>
<?php else: ?>
<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>


<li id="activity_<?php echo $ads_uid; ?>">
    <div class="feed_main_info">
        <div class="activity_feed_content_text">
            <div class="">
                <div class="activity_content p_photos photo_addlist">
                    <div class="div_single">

                        <a  target="_blank">
                            <img class="single_img horizionImage" src="" alt="">
                        </a>	   

                    </div>					
                </div>
            </div>
            <div class="comment_message">
                <div class="ads_feed_title" style="font-size: 14px;font-weight: bold;text-decoration: none;"><a target="_blank"></a></div>
                <div class="ads_feed_description"></div>
            </div>
        </div>
    </div>
</li>