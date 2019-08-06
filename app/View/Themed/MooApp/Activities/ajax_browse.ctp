<?php

$this->MooApp->loading();

$this->Html->css(array('MooApp.feed/activites','MooApp.feed/emoji','MooApp.main','MooApp.feed/plugin'), array('block' => 'mooAppOptimizedCss','minify'=>false));

$this->MooGzip->script(array(
        'zip'=>'activities.everyone.bundle.js.gz',
        'unzip'=>'MooApp.activities.everyone.bundle'
));

$this->addPhraseJs(array(
        'like' => __("%s Like"),
        'likes' => __("%s Likes"),
        'dislike' => __("%s Dislike"),
        'dislikes' => __("%s Dislikes"),
        'comment' => __("%s Comment"),
        'comments' => __("%s Comments"),
        'time' => __("Time"),
        'location' => __("Location"),
        'address' => __("Address"),
        'members' => __("members"),
        'newFeedPlaceholder' => __("What's on your mind?"),
        'report' => __("Report Activity"),
        'deleteFeed' => __("Delete Activity"),
        'deleteComment' => __("Delete Comment"),
        )
);

/*CUSTOM FOR SPOTLIGHT PROFILE*/
if (Configure::read('Spotlight.spotlight_enabled')) :
    
    $this->Html->css(array('https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick.min.css','https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick-theme.min.css'), array('block' => 'mooAppOptimizedCss','minify'=>false));
    
    $spotlight ='';
    $spotlightUserArray = array();
    $beforeRenderSpotlight = new CakeEvent("View.Mooapp.activities.ajax_browse.renderSpotlightForApp", $this, array());
    $this->getEventManager()->dispatch($beforeRenderSpotlight);
    $result = $beforeRenderSpotlight->result['result'];
    $canJoin = $result['canJoin'];
    $spotUsers = $result['topSpotlight'];
    if ( !empty( $spotUsers ) ): 
        foreach ($spotUsers as $i => $user):
            $spotlightUserArray[$i]['id'] =  $user['User']['id'];
            $spotlightUserArray[$i]['url'] =  FULL_BASE_URL . str_replace('?','',mb_convert_encoding($user['User']['moo_href'], 'UTF-8', 'UTF-8')) ;
            $spotlightUserArray[$i]['avatar'] =  $this->Moo->getImageUrl(array('User'=>$user['User']), array('prefix' => '200_square'));
        endforeach;
    endif;
    $spotlightUserArray =  json_encode($spotlightUserArray);
endif;
/*END CUSTOM FOR SPOTLIGHT PROFILE*/
?>

<?php $this->start('mooAppOptimizedContent'); ?>
<script type="text/javascript">
    <?php if(Configure::read('Spotlight.spotlight_enabled') && isset($canJoin) && $canJoin == 0 ): ?>
    window.show_spotlight_profile_btn = true;
    <?php endif; ?>
    <?php if(isset($spotlightUserArray) && !empty($spotlightUserArray) ): ?>
    window.spotlight_user = <?php echo $spotlightUserArray ?>;
    <?php endif; ?>
    <?php if(Configure::read('MooApp.show_more_button_what_new_box')==1): ?>
    window.show_more_button = true;
   <?php else: ?>
    window.show_more_button = false;
   <?php endif; ?>
  <?php if (!empty($cuser) && !$cuser['confirmed'] && Configure::read('core.email_validation')) : ?>
    window.email_validation = false;
//     document.getElementById("resend_validation_link").addEventListener("click", resendValidation);
//    
//    function resendValidation() {
//        
//        var xhr = new XMLHttpRequest();
//        xhr.open('POST', mooConfig.url.base + '/users/resend_validation', true);
//        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
//        xhr.onload = function () {
//            var div = '<div class="confirm_send_email_validate" style="font-size: 15px;color: #f00;font-weight: 600;margin-top: 19px;"> <?php echo  __("Validation link has been resent.") ?> </div>';
//            document.getElementById('confirm_remindMessage').innerHTML += div;
//        };
//        xhr.send();
//    }
   <?php else : ?>
    window.email_validation = true;
  <?php endif; ?>
  <?php 
  	$renderAppOptimizedContent= new CakeEvent("View.Mooapp.activities.ajax_browse.renderAppOptimizedContent", $this, array());
  	$this->getEventManager()->dispatch($renderAppOptimizedContent);
  ?>
</script>
<?php $this->end(); ?>

