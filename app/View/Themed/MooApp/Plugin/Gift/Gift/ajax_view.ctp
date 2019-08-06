<?php 
    if(empty($error))
    {
        $sender = $gift['Sender'];
        $receiver = $gift['Receiver'];
        $giftSent = $gift['GiftSent'];
        $gift = $gift['Gift'];
    }
?>
<div class="title-modal">
    <?php if(!empty($error)):?>
        <?php echo __d('gift', 'View gift');?>
    <?php else:?>
        <?php echo $gift['title'];?>
    <?php endif?>
</div>
<div class="modal-body">
    <?php if(!empty($error)):?>
        <?php echo $error;?>
    <?php else:?>
        <?php if($giftSent['sender_id'] == MooCore::getInstance()->getViewer(true)):?>
            <p class="gift-review-description">
                <?php echo __d('gift', 'Sent');?> <?php echo $this->Moo->getTime($giftSent['created'], Configure::read('core.date_format'), $utz) ?>
            </p>
            <p class="gift-review-description">
                <?php echo __d('gift', 'Sent to');?>: <?php echo $this->Moo->getName($receiver, false)?>
            </p>
            <p class="gift-review-description">
                <?php echo __d('gift', 'Viewed');?>: <?php echo $giftSent['viewed'] == 1 ? __d('gift', 'Yes') : __d('gift', 'No');?>
            </p>
            <p class="gift-review-description">
                <?php echo __d('gift', 'Message');?>: <?php echo $gift['message'];?>
            </p>
        <?php elseif($giftSent['receiver_id'] == MooCore::getInstance()->getViewer(true)):?> 
            <p class="gift-review-description">
                <?php echo __d('gift', 'Received');?> <?php //echo $this->Moo->getName($receiver, false)?> <?php echo $this->Moo->getTime($giftSent['created'], Configure::read('core.date_format'), $utz) ?>
            </p>
            <p class="gift-review-description">
                <?php echo __d('gift', 'Sent by');?>: <?php echo $this->Moo->getName($sender, false)?>
            </p>
            <p class="gift-review-description">
                <?php echo __d('gift', 'Message');?>: <?php echo $gift['message'];?>
            </p>
            <br/>
        <?php endif;?>
        <?php if($gift['type'] == GIFT_TYPE_PHOTO):?>
            <img style="max-width: 100%;" src="<?php echo $this->request->base.'/'.GIFT_FILE_URL.$gift['filename'];?>" />
        <?php elseif($gift['type'] == GIFT_TYPE_VIDEO):?>
            <video  class="video-js vjs-default-skin" controls preload="auto" width="100%" height="350"
                poster=""
                data-setup="{}">
                <source src="<?php echo $this->request->base.'/'.GIFT_FILE_URL.$gift['filename'];?>" type='video/<?php echo $gift['extension'];?>' />
            </video>
        <?php elseif($gift['type'] == GIFT_TYPE_AUDIO):?>
            <!--<audio id="music" preload="true">
                <source src="<?php echo $this->request->base.'/'.GIFT_FILE_URL.$gift['filename'];?>" type="audio/<?php echo $gift['extension'];?>" />
            </audio>
            <div id="audioplayer">
                <button id="pButton" class="play play_audio" ></button>
                <div id="timeline">    
                  <div id="playhead"></div>
                </div>
            </div>-->
            <audio id="music" controls autoplay>
                <source src="<?php echo $this->request->base.'/'.GIFT_FILE_URL.$gift['filename'];?>" type="audio/<?php echo $gift['extension'];?>" />
            </audio>
        <?php endif;?>
    <?php endif;?>
</div>
    <div class="stt-action share-action">
        <button type="button" class="close_share mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored" data-dismiss="modal"><?php echo __('Cancel') ?></button>
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