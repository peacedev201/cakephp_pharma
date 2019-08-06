<?php echo $this->Html->css(array('SocialPublisher.sp.css')); ?> 
<?php 
    if(!empty($site_rtl)){
        echo $this->Html->css(array('SocialPublisher.sp_ltr.css')); 
    }
?> 
<style type="text/css">
    <?php if(!empty($isMobile)): ?>
    .social-share-on .open>.dropdown-menu {
        right: 0 !important;
        left: -96px !important;
        border-radius: 0 !important;
        width: 300px !important;
    }
    <?php else: ?>
    .social-share-on .open>.dropdown-menu {
        white-space: nowrap
    }
    <?php endif; ?>
</style>
<?php if(count($fbook) || count($twitter)): ?>
<?php $upload_video = Configure::read('UploadVideo.uploadvideo_enabled'); ?>
 <div class="social-share-container">
     <div class="social-share-on" <?php if($upload_video): ?>style="top: 0px;left: 130px;"<?php endif;?>>
						<div class="dropdown" data-toggle="tooltip" title="<?php echo __d('social_publisher','Share on FB/Twitter');?>">
						  <button type="button" data-toggle="dropdown">
							<i class="material-icons md-icon dp48">share</i>
						  </button>
						  <div class="dropdown-menu">
							   <?php if(count($fbook)): ?>
									<div>
										<?php echo $this->Form->checkbox('fbook', array('hiddenField'=>false,'class'=> 'social_share','value' => 'facebook','checked'=>($fbook['sharing'] ?true:false))); ?>
										
										<span id='facebook_status'>
											<?php if($fbook['connect']): ?>
													 <div><i class="pb-iconfb"></i> <?php echo __d('social_publisher','Connected as '). '<b>' . $fbook['user']['displayName']. '</b>';?> </div> 
                                                                                                         <div><a class="logout_social" href="<?php if(!$is232): ?> javascript:logoutSocial('facebook') <?php else: ?> javascript:void(0) <?php endif; ?>" rel="facebook"><?php echo __d('social_publisher','Click here'); ?></a> <?php echo __d('social_publisher','to disconnect.') ?></div>
											<?php else: ?>                                                                                      
												 <i class="pb-iconfb"></i><?php echo __d('social_publisher','facebook');?>                                                                                                
											<?php endif; ?>
										</span>
									</div>
								 <?php endif; ?>
								
								<?php if(count($twitter)): ?>
										<div>
											<?php echo $this->Form->checkbox('twitter', array('hiddenField'=>false,'class'=> 'social_share','value' => 'twitter','checked'=>($twitter['sharing'] && $twitter['connect']?true:false))); ?>
											
											<span id='twitter_status'>
												<?php if($twitter['connect']): ?>
														 <div><i class="pb-icontwitter"></i> <?php echo __d('social_publisher','Connected as ') . '<b>' . $twitter['user']['displayName'] . '</b>';?> </div> 
														 <div><a class="logout_social" href="<?php if(!$is232): ?> javascript:logoutSocial('twitter') <?php else: ?> javascript:void(0) <?php endif; ?>" rel="twitter"><?php echo __d('social_publisher','Click here'); ?></a> <?php echo __d('social_publisher','to disconnect.') ?></div>
												<?php else: ?>
													<i class="pb-icontwitter"></i> <?php echo __d('social_publisher','twitter');?>
												<?php endif; ?>
											</span>
										</div>
								<?php endif; ?>
							</div>						  
						</div>
                                                <input type="hidden" id="fb_connect" value="<?php if(count($fbook) && $fbook['connect']) echo 1; else echo 0; ?>">
                                                <input type="hidden" id="twitter_connect" value="<?php if(count($twitter) && $twitter['connect']) echo 1; else echo 0; ?>">             
                    </div>                 
</div>
<?php endif; ?>
<?php if(!$is232): ?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
    var fb_connect = false;
    var twitter_connect = false;
    $(document).ready(function(){
         <?php if(count($fbook) && $fbook['connect']): ?>
                fb_connect = true;
         <?php endif; ?>

         <?php if(count($twitter) && $twitter['connect']): ?>
                twitter_connect = true;
         <?php endif; ?>

        $(".social_share").change(function(){   
            var is_checked = $(this).is(":checked");
            toggleSocialSharing($(this).val(), is_checked);	
        });	
    });

var toggleSocialSharing = function(provider, flag) {
        var is_connect;
        if(provider == 'facebook'){
            is_connect = fb_connect;
            $('.simple-modal-body #fbook_pop').prop( "checked", flag);
            $('#fbook_pop').prop( "checked", flag);
        }else{
            is_connect = twitter_connect;
            $('.simple-modal-body #twitter_pop').prop( "checked", flag);
            $('#twitter_pop').prop( "checked", flag);
        }
        if(flag && !is_connect){
           // var child_window = window.open(baseUrl + '/social/auths/loginsocial/' + provider, 'mywindow', 'width=500,height=500');
           window.location.href = baseUrl + '/social_publishers/loginsocial/' + provider;
        }else{
            var url = baseUrl + '/social_publishers/loginsocial/' + provider ;
            var getData = {
                'flag': flag
            };
                $.ajax({
                        type: "GET",
                        cache: false,
                        url: url,
                        dataType: "html",
                        data: getData,
                        success: function(res)
                        {
                        }
                    });
        }
   
};

var logoutSocial = function(provider){
    var url = baseUrl + '/social_publishers/logoutsocial/' + provider ;
     $.ajax({
            type: "GET",
            cache: false,
            url: url,
            dataType: "html",
            data: {},
            success: function(res)
            {
                if(provider == 'facebook'){
                    fb_connect = false;
                    $("#fbook").prop( "checked", false);
                    $("#facebook_status").html('<i class="pb-iconfb"></i> <?php echo __d('social_publisher','facebook');?>');
                }else{
                    twitter_connect = false;
                    $("#twitter").prop( "checked", false);
                    $("#twitter_status").html('<i class="pb-icontwitter"></i> <?php echo __d('social_publisher','twitter');?>');
                }
            }
      });
}
<?php $this->Html->scriptEnd(); ?>
<?php else: ?>
    <?php
        $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery', 'mooSocialPublisher'), 'object' => array('$', 'mooSocialPublisher')));
    ?>
        mooSocialPublisher.initOnIndex();
    <?php $this->Html->scriptEnd(); ?>
<?php endif; ?>
