<?php
echo $this->addPhraseJs(array(
    'btn_ok' => __d('gift', 'Ok'),
    'btn_cancel' => __d('gift', 'cancel'),
    'please_confirm' => __d('gift', 'Please Confirm'),
));
?>
<?php if ($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["mooGift"], function(mooGift) {
        mooGift.loadMyGifts();
        mooGift.viewGift();
        mooGift.sendGift();
        mooGift.deleteGift();
        mooGift.initNavGift({
            'type' : '<?php if(!empty($type) && $type == 'my'){echo $type;}?>'
        });
    });
</script>
<?php else:?>
    <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('mooGift', 'jqueryUi'), 'object' => array('mooGift'))); ?>
        mooGift.loadMyGifts();
        mooGift.viewGift();
        mooGift.sendGift();
        mooGift.deleteGift();
        mooGift.initNavGift({
            'type' : '<?php if(!empty($type) && $type == 'my'){echo $type;}?>'
        });

    <?php $this->Html->scriptEnd(); ?>
<?php endif;?>
<?php if($page == 1):?>
<li>
    <ul id="my-gift-type">
        <li <?php if(empty($param) || $param == GIFT_SAVED):?>class="active"<?php endif;?>>
            <a id="saved_gifts" href="javascript:void(0)">
                <?php echo __d('gift', 'Saved gifts');?>      
            </a>
        </li>
        <li <?php if($param == GIFT_RECEIVED):?>class="active"<?php endif;?>>
            <a id="received_gifts" href="javascript:void(0)">
                <?php echo __d('gift', 'Received gifts');?>
            </a>
        </li>
        <li <?php if($param == GIFT_SENT):?>class="active"<?php endif;?>>
            <a id="sent_gifts" href="javascript:void(0)">
                <?php echo __d('gift', 'Sent gifts');?>
            </a>
        </li>
    </ul>
</li>
<?php endif;?>
<?php if (count($gifts) > 0): ?>
    <ul class="blog-content-list">
        <?php
            foreach ($gifts as $key => $gift):
                $sender = !empty($gift['Sender']) ? $gift['Sender'] : null;
                $receiver = !empty($gift['Receiver']) ? $gift['Receiver'] : null;
                $giftSent = !empty($gift['GiftSent']) ? $gift['GiftSent'] : null;
                $friend = !empty($gift['GiftFriend']) ? $gift['GiftFriend'] : null;
                $gift = $gift['Gift'];
        ?>
            <?php if($param == GIFT_SAVED || ($type == 'my' && $param == null)):?>
                <li class="full_content p_m_10" id="mygift<?php echo $gift['id'];?>">
                    <?php echo $this->Moo->getItemPhoto(array('User' => $friend), array(
                        'prefix' => '100_square',
                        'class' => 'img_wrapper2 user_list thumb_mobile'
                    ))?>
                    <div class="blog-info">
                        <?php echo $gift['title']; ?>
                        <div class="extra_info">
                            <?php echo __d('gift', 'Created');?> <?php echo $this->Moo->getTime($gift['created'], Configure::read('core.date_format'), $utz) ?>
                            <br/>
                            <?php echo __d('gift', 'Sent to');?> <?php echo $this->Moo->getName($friend, false)?>
                        </div>
                        <?php if(!empty($gift['message'])):?>
                            <div class="blog-description-truncate">
                                <div>
                                    <?php echo __d('gift', 'Message');?>: <?php echo $gift['message'];?>
                                </div>
                            </div>
                        <?php endif;?>
                        <?php if($permission_can_send_gift):?>
                        <a class="button button-action send_gift" href="javascript:void(0)" data-id="<?php echo $gift['id'];?>" id="btnSendGift">
                            <?php echo __d('gift', 'Send');?>
                        </a>
                        <?php endif;?>
                        <a class="button button-action" href="<?php echo $url;?>create/<?php echo $gift['id'];?>">
                            <?php echo __d('gift', 'Edit');?>
                        </a>
                        <a href="javascript:void(0)" class="button button-action delete_gift" data-gift_sent_id="" data-gift_id="<?php echo $gift['id'];?>">
                            <?php echo __d('gift', 'Delete');?>
                        </a>
                    </div>
                </li>
            <?php elseif($param == GIFT_RECEIVED):?>
                <li class="full_content p_m_10" id="mygift<?php echo $giftSent['id'];?>">
                    <?php echo $this->Moo->getItemPhoto(array('User' => $sender), array(
                        'prefix' => '100_square',
                        'class' => 'img_wrapper2 user_list thumb_mobile'
                    ))?>
                    <div class="blog-info">
                        <a href="javascript:void(0)" class="view_gift name-main" data-id="<?php echo $giftSent['id'];?>">
                            <?php echo $gift['title']; ?>
                        </a>
                        <div class="extra_info">
                            <?php echo __d('gift', 'Received');?> <?php //echo $this->Moo->getName($receiver, false)?> <?php echo $this->Moo->getTime($giftSent['created'], Configure::read('core.date_format'), $utz) ?>
                            <br/>
                            <?php echo __d('gift', 'Sent by');?>: <?php echo $this->Moo->getName($sender, false)?>
                        </div>
                        <?php if(!empty($giftSent['message'])):?>
                            <div class="blog-description-truncate">
                                <div>
                                    <?php echo __d('gift', 'Message');?>: <?php echo $giftSent['message'];?>
                                </div>
                            </div>
                        <?php endif;?>
                        <?php
                         if($giftSent['gift_category_id'] == 4): ?>
                            <a href="javascript:void(0)" class="button button-action activate_gift" data-id="<?php echo $giftSent['id'];?>">
                                <?php echo __d('gift', 'Activate');?>
                            </a>
                        <?php endif ?>
                        <a class="button button-action" data-backdrop="true" dbtn ata-dismiss="" data-toggle="modal" data-target="#themeModal" href="<?php echo $this->request->base;?>/conversations/ajax_send/<?php echo $sender['id'];?>">
                            <?php echo __d('gift', 'Send message');?>
                        </a>
                        <a href="javascript:void(0)" class="view_gift button button-action" data-id="<?php echo $giftSent['id'];?>">
                            <?php echo __d('gift', 'View');?>
                        </a>
                        <a href="javascript:void(0)" class="delete_gift button button-action" data-gift_sent_id="<?php echo $giftSent['id'];?>" data-gift_id="">
                            <?php echo __d('gift', 'Delete');?>
                        </a>
                    </div>
                </li>
            <?php elseif($param == GIFT_SENT):?>
                <li class="full_content p_m_10" id="mygift<?php echo $giftSent['id'];?>">
                    <?php echo $this->Moo->getItemPhoto(array('User' => $receiver), array(
                        'prefix' => '100_square',
                        'class' => 'img_wrapper2 user_list thumb_mobile'
                    ))?>
                    <div class="blog-info">
                        <a href="javascript:void(0)" class="view_gift name-main" data-id="<?php echo $giftSent['id'];?>">
                            <?php echo $gift['title']; ?>
                        </a>
                        <div class="extra_info">
                            <?php echo __d('gift', 'Sent');?> <?php echo $this->Moo->getTime($giftSent['created'], Configure::read('core.date_format'), $utz) ?>
                            <br/>
                            <?php echo __d('gift', 'Sent to');?>: <?php echo $this->Moo->getName($receiver, false)?>
                            <br/>
                            <?php echo __d('gift', 'Viewed');?>: <?php echo $giftSent['viewed'] == 1 ? __d('gift', 'Yes') : __d('gift', 'No');?>
                        </div>
                        <?php if(!empty($giftSent['message'])):?>
                            <div class="blog-description-truncate">
                                <div>
                                    <?php echo __d('gift', 'Message');?>: <?php echo $giftSent['message'];?>
                                </div>
                            </div>
                        <?php endif;?>
                        <a class="button button-action" data-backdrop="true" data-dismiss="" data-toggle="modal" data-target="#themeModal" href="<?php echo $this->request->base;?>/conversations/ajax_send/<?php echo $receiver['id'];?>">
                            <?php echo __d('gift', 'Send message');?>
                        </a>
                        <a href="javascript:void(0)" class="view_gift button button-action" data-id="<?php echo $giftSent['id'];?>">
                            <?php echo __d('gift', 'View');?>
                        </a>
                        <a href="javascript:void(0)" class="delete_gift button button-action" data-gift_sent_id="<?php echo $giftSent['id'];?>" data-gift_id="">
                            <?php echo __d('gift', 'Delete');?>
                        </a>
                    </div>
                </li>
            <?php endif;?>
        <?php endforeach ?>
    </ul>
<?php else: ?>
    <?php echo '<div align="center">' . __d('gift', 'No more results found') . '</div>' ?>
<?php endif ?>

<?php if (count($gifts) >= Configure::read('Gift.gift_items_per_page')): ?>
    <!--<div class="view-more">
        <a href="javascript:void(0)" onclick="moreResults('<?php echo $more_url ?>', 'list-content', this)"><?php echo __d('gift', 'Load More') ?></a>
    </div>-->
    <?php $this->Html->viewMore($more_url, 'list-content') ?>
<?php endif; ?>

<?php if($this->request->is('ajax')): ?>
    <script type="text/javascript">
        require(["jquery","mooBehavior"], function($, mooBehavior) {
            mooBehavior.initMoreResults();
        });
    </script>
<?php else: ?>
    <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery', 'mooBehavior'), 'object' => array('$', 'mooBehavior'))); ?>
    mooBehavior.initMoreResults();
    <?php $this->Html->scriptEnd(); ?>
<?php endif; ?>
<?php if($this->request->is('ajax')): ?>
<script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
<?php endif; ?>
    $(document).ready(function(){
        $('.activate_gift').click(function(){
            element = $(this);
            // disableButton(element);
            id = $(this).attr('data-id');
            $.post("<?php echo $this->request->base?>/gifts/ajax_activate_gift", {id,id}, function(data){
                // enableButton(element);
                var json = $.parseJSON(data);
                
                $('#portlet-config').find('.modal-title').text(json.title);
                $('#portlet-config').find('.modal-body').text(json.message);
                $('#portlet-config').modal('show');
            });
            
            return false;
        });
        $('#portlet-config .ok').on('click',function(e){
            $('#portlet-config').modal('hide');
        });
    });
    var tmp_class;
    function disableButton(element)
    {
        tmp_class = $(element).attr("class");
        $(element).attr("class", "icon-refresh icon-spin");
        $(element).addClass('disabled');
    }
    function enableButton(element)
    {
        $(element).attr("class", tmp_class);
        $(element).removeClass('disabled');
    }
<?php if($this->request->is('ajax')): ?>
</script>
<?php else: ?>
<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>

