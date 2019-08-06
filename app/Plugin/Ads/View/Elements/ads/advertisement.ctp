<?php if(Configure::read('Ads.ads_enabled')): ?>
<?php if($placement):?>
<?php if($adsCampaigns): ?>
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
           url:"<?php echo $this->Html->url(array('controller'=>'ads_placement','action'=>'loadAjaxCampaign')); ?>",
           data:data
       }).success(function(data){
          if(data){
              $("#ads_widgets_<?php echo $key ?> .commercial_item").first().remove();
              $("#ads_widgets_<?php echo $key ?>").append(data);
          }
           num_load_start_<?php echo $key ?> +=1;
           setTimeout(function(){ajax_call_<?php echo $key ?>();}, <?php echo $time_interval;  ?>);
       });
    }
        setTimeout(function(){ajax_call_<?php echo $key ?>();}, <?php echo $time_interval;  ?>);
        
        
//        $("body").on("click","#ads_widgets_<?php echo $key ?> a",function(){
//                var idCampaign = $(this).attr("data-value");
//            $.ajax({
//               type:'POST',
//               url:"<?php echo $this->Html->url(array('controller'=>'ads','action'=>'update_click_count')); ?>",
//               data:{"id":idCampaign}
//           });
//        });
       
   
    });
<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>
<div id="ads_widgets_<?php echo $key ?>" style="text-align:center;padding-top:5px; ">
    <?php if($background_block === "0"): ?>
<!--    class blackground block-->
    <?php endif;  ?>
<?php if($ads_type == 'html'):  // if type is html?>
        <div class="row">
            <div class="col-md-6"><p style="font-size: 15;font-weight: bold"><?php echo __d('Ads','Sponsored') ?></p></div>
            <div class="col-md-6"><span style="background-color: #E4E4E4;"><a href="<?php echo $this->request->base.'/ads/create';?>" style="color:grey"><?php echo __d('Ads','See Your Add Here') ?></a></span></div>
        </div>
 <?php foreach($adsCampaigns as $key=>$campaign): ?>
 <?php $linkClick= $this->Html->url(array('controller'=>'ads','action'=>'update_click_count/'.$campaign['AdsCampaign']['id']))?>
       <div class="row commercial_item" style="border-bottom: 1px solid #E4E4E4;margin-bottom: 10px">
        <?php if($campaign['AdsCampaign']['ads_image']): ?>
           <div class="col-md-12 ads_image" >
               <a data-value="<?php echo $campaign['AdsCampaign']['id']; ?>" href="<?php echo $linkClick ?>"><img  src="<?php echo  FULL_BASE_URL . $this->request->webroot.'uploads/commercial/'. $campaign['AdsCampaign']['ads_image'] ?>" class="img-responsive"></a>
           </div>
        <?php endif; ?>
        <div class="col-md-12 ads_title">
        <?php if($title_enable): ?>
            <div class="col-md-12">
                <a data-value="<?php echo $campaign['AdsCampaign']['id']; ?>" href="<?php echo $linkClick ?>"><?php echo $campaign['AdsCampaign']['ads_title'] ?></a>
            </div>
        <?php endif; ?>
        <?php if($campaign['AdsCampaign']['description']): ?>
            <div class="col-md-12 ads_description">
                <p><?php echo $campaign['AdsCampaign']['description']; ?></p>
            </div> 
        <?php endif; ?>
        </div>       
       </div>
  <?php endforeach; ?>  
<?php endif; ?>

<?php if($ads_type == 'image'): ?>
<?php $linkClick = $this->Html->url(array('controller'=>'ads','action'=>'update_click_count/'.$adsCampaigns[0]['AdsCampaign']['id'])) ?>
      <div>
        <div class="row">
            <div class="col-md-6"><p style="font-size: 15;font-weight: bold"><?php echo __d('Ads','Sponsored') ?></p></div>
            <div class="col-md-6"><span style="background-color: #E4E4E4;"><a href="<?php echo $this->request->base.'/ads/create';?>" style="color:grey"><?php echo __d('Ads','See Your Add Here') ?></a></span></div>
        </div>
    </div>
    <div class="row commercial_item">
        <div><a data-value="<?php echo $adsCampaigns[0]['AdsCampaign']['id'] ?>" href="<?php echo $linkClick ?>"><img class="img-responsive" src="<?php echo  FULL_BASE_URL . $this->request->webroot.'uploads/commercial/'. $adsCampaigns[0]['AdsCampaign']['ads_image'] ?>"></a></div>
    </div>
<?php endif; ?>    
</div>
<?php else: ?>
<div>
    <div class="row">
        <div class="col-md-6"><p style="font-size: 15;font-weight: bold"><?php echo __d('Ads','Sponsored') ?></p></div>
        <div class="col-md-6"><span style="background-color: #E4E4E4;"><a href="<?php echo $this->request->base.'/ads/create';?>" style="color:grey"><?php echo __d('Ads','See Your Add Here') ?></a></span></div>
    </div>
</div>

<?php endif; ?>
<?php endif; ?>
<?php endif; ?>
