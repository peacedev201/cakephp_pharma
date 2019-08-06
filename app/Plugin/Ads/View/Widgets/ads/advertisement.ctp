<?php if(Configure::read('Ads.ads_enabled') && !$role_ads_hide_all_ads): ?>
<?php if($placement):?>
<?php if($adsCampaigns): ?>
<?php if($adsPlacement['AdsPlacement']['enable']): ?>
<?php if($flagAjax): ?>

<?php $this->Html->scriptStart(array('inline' => false)); ?>
    $(document).ready(function(){
      <?php echo 'var key_'.$key .'='.$key ?>;
      <?php echo 'var num_load_start_'.$key .'='.$num_load_start ?>;
      <?php echo 'var list_campaigns_'.$key .'='.$listCampaignsId; ?>;
    function ajax_call_<?php echo $key ?>() {
      
         if(num_load_start_<?php echo $key ?> > list_campaigns_<?php echo $key ?>.length - 1){
               num_load_start_<?php echo $key ?> = 0;
           }
          var id = list_campaigns_<?php echo $key?>[num_load_start_<?php echo $key; ?>];
          var data = {"id":id,"title_enable":"<?php echo $title_enable?>",'ads_type':"<?php echo $ads_type ?>"};
          //console.log("load from "+num_load_start_229 + " : " + id);
       $.ajax({
           type:'POST',
           url:"<?php echo $this->request->base.'/commercial_placement/loadAjaxCampaign';?>",
           data:data
       }).success(function(data){
          if(data){
              $("#ads_widgets_<?php echo $key ?> .commercial_item").first().remove();
              $("#ads_widgets_<?php echo $key ?> .commercial_wrapper").append(data);
          }
           num_load_start_<?php echo $key ?> +=1;
           setTimeout(function(){ajax_call_<?php echo $key ?>();}, <?php echo $time_interval;  ?>);
       });
    }
        setTimeout(function(){ajax_call_<?php echo $key ?>();}, <?php echo $time_interval;  ?>);       
   
    });
<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>
<?php echo $this->Html->css(array('/commercial/css/commercial.css' )); ?>
    <div id="ads_widgets_<?php echo $key ?>" class="ads-background <?php if(is_numeric($background_block)){echo "background_block";} ?>" >
        <div class="row ads-titles-round">
                    <div class="col-md-6 col-sm-6 col-xs-6"><p style="font-size: 15px;font-weight: bold;text-align: left"><?php echo $title_enable ? $title : '&nbsp;'; ?></p></div>
                    <?php if(!$reach_limit && $role_ads_can_add_ads == true && $show_see_your_ad_here == true):?>
                            
                    <div class="col-md-6 col-sm-6 col-xs-6 see_your_ads_here"><span style="background-color: #E4E4E4;"><a href="<?php echo $this->request->base.'/ads/create/'.$placement;?>" style="color:grey"><?php echo __d('ads','See Your Ad Here') ?></a></span></div>
                    <?php endif;?>
        </div>
        <div class="commercial_wrapper" style="width:<?php echo $adsPlacement['AdsPlacement']['dimension_width'];?>px">
            <?php if($ads_type == 'html'):  // if type is html?>
                <?php foreach($adsCampaigns as $key=>$campaign): ?>
                    <?php $linkClick= $this->request->base.'/commercial/update_click_count/'.$campaign['AdsCampaign']['id'];?>
                    <div class="row commercial_item" style="">
                        <?php if($campaign['AdsCampaign']['ads_image']): ?>
                           <div class="col-md-12 ads_image" >
                               <a data-value="<?php echo $campaign['AdsCampaign']['id']; ?>" href="<?php echo $linkClick; ?>" target="_blank"><img  src="<?php echo  FULL_BASE_URL . $this->request->webroot.'uploads/commercial/'. $campaign['AdsCampaign']['ads_image'] ?>" class="img-responsive"></a>
                           </div>
                        <?php endif; ?>
                        <div class="col-md-12 ads_title">
                        <div class="col-md-12">
                            <a data-value="<?php echo $campaign['AdsCampaign']['id']; ?>" href="<?php echo $linkClick; ?>" target="_blank"><?php echo h($campaign['AdsCampaign']['ads_title']); ?></a>
                        </div>
                        <?php if($campaign['AdsCampaign']['description']): ?>
                            <div class="col-md-12 ads_description">
                                <p><?php echo h($campaign['AdsCampaign']['description']); ?></p>
                            </div> 
                        <?php endif; ?>
                        </div>       
                   </div>
                <?php endforeach; ?>  
            <?php endif; ?>
            <?php if($ads_type == 'image'): ?>
                <?php foreach($adsCampaigns as $campaign): ?>
                    <div class="row commercial_item">
                        <?php $linkClick = $this->request->base.'/commercial/update_click_count/'.$campaign['AdsCampaign']['id']; ?>
                        <div><a data-value="<?php echo $campaign['AdsCampaign']['id'] ?>" href="<?php echo $linkClick ?>" target="_blank"><img class="img-responsive" src="<?php echo  FULL_BASE_URL . $this->request->webroot.'uploads/commercial/'. $campaign['AdsCampaign']['ads_image'] ?>"></a></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>  
        </div>
    </div>
<?php endif; ?>
<?php endif; ?>
<?php endif; ?>
<?php endif; ?>
