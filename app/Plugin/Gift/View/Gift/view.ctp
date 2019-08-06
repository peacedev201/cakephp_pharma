<?php

echo $this->Html->css(array('Gift.gift'), array('inline' => false));
echo $this->Html->script(array('Gift.gift'), array('inline' => false));
?>
<?php
    $gift = $gift['Gift'];
     $giftHelper = MooCore::getInstance()->getHelper('Gift_Gift');
?>
<div class="bar-content">
    <div class="content_center">
        <div class="mo_breadcrumb">
            <h1><?php echo $gift['title']; ?></h1>
        </div>
        <div class="count-credits">
            <?php if($gift['price'] > 0):?>
                <?php echo round($gift['price'],2);?> <?php echo __d('gift', 'Credits'); ?>
            <?php else:?>
                <?php echo __d('gift', 'Free'); ?>
            <?php endif;?>
        </div>
        <div class="gift-body">
            <?php echo $gift['message']; ?>
            <?php if($gift['type'] == GIFT_TYPE_PHOTO):?>
            <img style="max-width: 100%;" src="<?php echo $giftHelper->getImage($gift)?>" />
            <?php elseif($gift['type'] == GIFT_TYPE_VIDEO):?>
            <video  class="video-js vjs-default-skin" controls preload="auto" width="100%" height="350">
                <source src="<?php echo $giftHelper->getFile($gift)?>" type='video/<?php echo $gift['extension'];?>' />
            </video>
            <?php elseif($gift['type'] == GIFT_TYPE_AUDIO):?>
			<audio id="music" preload="true">
				<source src="<?php echo $giftHelper->getFile($gift)?>" type="audio/<?php echo $gift['extension'];?>" />
			</audio>
			<div id="audioplayer">
				<button id="pButton" class="play play_audio" ></button>
				<div id="timeline">    
				  <div id="playhead"></div>
				</div>
			</div>
                          
            <?php endif;?>
            <div class="clear"></div>
            <?php if($permission_can_send_gift && 
                    (($gift['type'] == GIFT_TYPE_PHOTO && $permission_allow_photo_gift) || 
                    ($gift['type'] == GIFT_TYPE_AUDIO && $permission_allow_audio_gift) || 
                    ($gift['type'] == GIFT_TYPE_VIDEO && $permission_allow_video_gift))):?>
            <a class="btn btn-action customize-btn" href="<?php echo $this->request->base.'/gifts/create/'.$gift['id'];?>">
                <?php echo __d('gift', 'Customize and Send this gift'); ?>
            </a>
            <?php endif;?>
        </div>
    </div>
</div>

<div class="bar-content full_content p_m_10">
    <div class="content_center">
        <?php echo $this->renderLike(); ?>
    </div>
</div>
<div class="bar-content">
    <?php echo $this->renderComment(); ?>
</div>

<?php if ($this->request->is('ajax')): ?>
    <script type="text/javascript">
        require(["mooGift"], function(mooGift) {
            mooGift.playAudio();
            mooGift.initAudio({ 'dialog' : 1 });
        });
    </script>
<?php else:?>
    <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('mooGift', 'jqueryUi'), 'object' => array('mooGift'))); ?>
    mooGift.playAudio();
    mooGift.initAudio({ 'dialog' : 1 });
    <?php $this->Html->scriptEnd(); ?>
<?php endif;?>