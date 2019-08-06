<?php
    echo $this->Html->css(array('SocialPublisher.sp.css'));
?>
<div class="sp_center">
    <h3><?php echo __('Your Accounts')?></h3>
    <p><?php echo __('Connect to social accounts to share your post')?></p>

    <div class="sp_provider"> 
            <?php if(!$fbook['connect']): ?>
                <img src="<?php echo $this->request->webroot?>SocialPublisher/img/facebook.png" class="sp_quick">
                <span>
                        <a href="javascript:;" onclick="javascript: toggleSocialSharing('facebook')">
                                <?php echo __('Connect to Facebook')?>		
                        </a>
                </span>
            <?php else: ?>
                <img src="<?php echo $fbook['user']['photoURL'] ?>" class="sp_quick">
                <span>
                       <div><strong><?php echo $fbook['user']['displayName'] ?></strong></div>
                       <div><a class="smoothbox" href="<?php echo $this->request->base ?>/social_publishers/logoutsocial/facebook"><?php echo __('Disconnect')?></a></div>
                </span>           
                 <div style="margin-top: 10px">
                        <?php echo $this->Form->checkbox('facebook_sharing', array('hiddenField'=>false,'class'=> 'social_share','value' => 'facebook','checked'=>($fbook['sharing']?true:false))); ?><img src="<?php echo $this->request->webroot?>SocialPublisher/img/loading.gif" style="display: none" id="facebook_sharing_loading"> <label><?php echo __('Enable Sharing')?></label>                       
                        <img src="<?php echo $this->request->webroot?>SocialPublisher/img/facebook.png" class="sp_logo">
                </div>
            <?php endif; ?>
    </div>
    <div class="sp_provider"> 
        <?php if(!$twitter['connect']): ?>
            <img src="<?php echo $this->request->webroot?>SocialPublisher/img/twitter.png" class="sp_quick">
            <span>
                    <a href="javascript:void(0)" onclick="javascript: toggleSocialSharing('twitter')">
                            <?php echo __('Connect to Twitter')?>		
                    </a>
            </span>
        <?php else: ?>
                <img src="<?php echo $twitter['user']['photoURL'] ?>" class="sp_quick">
                <span>
                       <div><strong><?php echo $twitter['user']['displayName'] ?></strong></div>
                       <div><a class="smoothbox" href="<?php echo $this->request->base ?>/social_publishers/logoutsocial/twitter"><?php echo __('Disconnect')?></a></div>
                </span>
                 <div style="margin-top: 10px">
                        <?php echo $this->Form->checkbox('twitter_sharing', array('hiddenField'=>false,'class'=> 'social_share','value' => 'twitter','checked'=>($twitter['sharing']?true:false))); ?><img src="<?php echo $this->request->webroot?>SocialPublisher/img/loading.gif" style="display: none" id="twitter_sharing_loading"> <label><?php echo __('Enable Sharing')?></label>                
                        <img src="<?php echo $this->request->webroot?>SocialPublisher/img/twitter.png" class="sp_logo">
                </div>
        <?php endif; ?>
    </div>
</div>
<script type="text/javascript">
    
    var toggleSocialSharing = function(provider) {
         window.location.href = baseUrl + '/social_publishers/loginsocial/' + provider;
    };
    
    $(document).ready(function(){
        $(".social_share").change(function(){   
            var is_checked = $(this).is(":checked");
            var url = baseUrl + '/social_publishers/loginsocial/' + $(this).val() ;
            var getData = {
                'flag': is_checked
            };           
            var el_id = $(this).attr('id');   
            $('#' + el_id).hide();
            $('#' + el_id + '_loading').show();
            $.ajax({
                type: "GET",
                cache: false,
                url: url,
                dataType: "html",
                data: getData,
                success: function(res)
                {
                    $('#' + el_id).show();
                    $('#' + el_id + '_loading').hide();
                },
                error: function(res){
                    $('#' + el_id).show();
                    $('#' + el_id + '_loading').show();
                }
            });
        });	
    });
</script>

