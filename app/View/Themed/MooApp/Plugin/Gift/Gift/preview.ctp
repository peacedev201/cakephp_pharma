<div class="modal-content">    
    <div class="title-modal">
        <?php echo __d('gift', 'Gift Preview');?>
    </div>
    <div class="modal-body">
        <?php if(!empty($error)):?>
            <?php echo $error;?>
        <?php else:?>
            <?php if(!empty($gift['GiftSent'])):
                $giftSent = $gift['GiftSent'];
                $sender = $gift['User'];
                $receiver = $gift['Receiver'];
                $gift = $gift['Gift'];
            ?>
                <?php if($giftSent['receiver_id'] == MooCore::getInstance()->getViewer(true)):?>
                    <a href="<?php echo $gift['moo_href']; ?>">
                        <?php echo $gift['title']; ?>
                    </a>
                    <div class="parent_feed_time">
                        <span class="date">
                            <?php echo __d('gift', 'Received');?> <?php echo $this->Moo->getTime($giftSent['created'], Configure::read('core.date_format'), $utz) ?>
                        </span>
                    </div>
                    <p>
                        <?php echo __d('gift', 'Sent by');?>: <?php echo $sender['name'];?>
                    </p>
                    <?php if(!empty($giftSent['message'])):?>
                        <p>
                            <?php echo __d('gift', 'Message');?>: <?php echo $giftSent['message'];?>
                        </p>
                    <?php endif;?>
                <?php elseif($giftSent['sender_id'] == MooCore::getInstance()->getViewer(true)):?>
                    <a href="<?php echo $gift['moo_href']; ?>">
                        <?php echo $gift['title']; ?>
                    </a>
                    <div class="parent_feed_time">
                        <span class="date">
                            <?php echo __d('gift', 'Sent');?> <?php echo $this->Moo->getTime($giftSent['created'], Configure::read('core.date_format'), $utz) ?>
                        </span>
                    </div>
                    <p>
                        <?php echo __d('gift', 'Viewed');?>: <?php echo $giftSent['viewed'] == 1 ? __d('gift', 'Yes') : __d('gift', 'No');?>
                    </p>
                    <p>
                        <?php echo __d('gift', 'Sent to');?>: <?php echo $receiver['name'];?>
                    </p>
                    <?php if(!empty($giftSent['message'])):?>
                        <p>
                            <?php echo __d('gift', 'Message');?>: <?php echo $giftSent['message'];?>
                        </p>
                    <?php endif;?>
                <?php endif;?>
            <?php elseif(!empty($gift['Gift'])):
                $gift = $gift['Gift'];
            ?>
                <div class="pull-left">
                    <a href="<?php echo $gift['moo_href']; ?>">
                        <?php echo $gift['title']; ?>
                    </a>
                    <p>
                        <?php echo sprintf(__d('gift', 'Cost %s credits'), $gift['price']);?>
                    </p>
                </div>
                <div class="pull-right">
                    <a href="<?php echo $url;?>ajax_send_gift_dialog/<?php echo $gift['id'];?>" data-backdrop="static" data-target="#themeModal" data-toggle="modal">
                        <?php echo __d('gift', 'Sent to friend');?>
                    </a>
                </div>
            <?php endif;?>
            <?php if(!empty($filename)):?>
                <?php if(!empty($title)):?>
                <h2 class="gift-review-name-m"><?php echo $title;?></h2>
                <?php endif;?>
                <?php if(!empty($credits)):?>
                    <p class="gift-review-credit">
                        <?php if($credits > 0):?>
                            <?php echo $credits;?> <?php echo __d('gift', 'credits'); ?>
                        <?php else:?>
                            <?php echo __d('gift', 'Free'); ?>
                        <?php endif;?>
                    </p>
                <?php endif;?>
                <?php if(!empty($message)):?>
                <p class="gift-review-description">
                    <?php echo $message;?>
                </p>
                <?php endif;?>
                <?php if($type == GIFT_TYPE_PHOTO):?>
                    <img style="max-width: 100%;" src="<?php echo $file_url;?>" />
                <?php elseif($type == GIFT_TYPE_VIDEO):?>
                    <video  class="video-js vjs-default-skin" controls preload="auto" width="100%" height="350"
                        poster=""
                        data-setup="{}">
                        <source src="<?php echo $file_url;?>" type='video/<?php echo $extension;?>' />
                    </video>
                <?php elseif($type == GIFT_TYPE_AUDIO):?>
                    <!--<audio id="music" preload="true">
                        <source src="<?php echo $file_url;?>" type="audio/<?php echo $extension;?>" />
                    </audio>
                    <div id="audioplayer">
                        <button id="pButton" class="play play_audio" ></button>
                        <div id="timeline">
                          <div id="playhead"></div>
                        </div>
                    </div>-->
                    <audio id="music" controls autoplay>
                        <source src="<?php echo $file_url;?>" type="audio/<?php echo $extension;?>" />
                    </audio>
                <?php endif;?>
            <?php else:?>
                <?php echo __d('gift', 'Please upload a file');?>
            <?php endif;?>
        <?php endif;?>
    </div>
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