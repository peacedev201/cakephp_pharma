<?php 
    $is_app = $this->request->is('androidApp') || $this->request->is('iosApp');
$this->addPhraseJs(array(
    'information'   =>  __('Information'),
    'please_contact_any_sales_reps_visiting_you'  =>  __('Please contact any Sales reps visiting you'),
    'gif_msg_part2'  =>  __('Item(GIF plugin, and, etc..) is freely available from any Sales reps'),
    'gif_msg_part3' =>  __('no charge to member or Sales reps, One item from One Sales rep'),
    'gif_msg_part4' =>  __('You can get 10 items if 10 Sales reps send items'),
    'gif_msg_part5' =>  __('Please click here to see How-to'),
    'gif_msg_part6' =>  __('click here to see the Sales reps in your region'),
    'or'            =>  __('or')
));
$fShowHide = false;
$userModel = MooCore::getInstance()->getModel('User');
$userModel->cacheQueries = false;
$user = $userModel->find('first', array('conditions' => array('User.id' => $_SESSION['Auth']['User']['id'])))['User'];

if ($user['STICKER'] == STICKER_EXTEND_FOREVER || ($user['STICKER'] && !empty($user['STICKER_valid'])  && date('Ymd') <= date('Ymd',strtotime($user['STICKER_valid']))))
    $fShowHide = true;
?>
<?php if(!empty($item_type)): 
    $style = "";
    if($item_type == "item_comment_edit" || $item_type == "activity_comment_edit")
    {
        $style = "sticker_button_edit_comment";
    }
    else if($item_type == "activity_edit")
    {
        $style = "sticker_button_edit_activity";
    }
?>
    <div class="sticker_button <?= ($fShowHide) ? 'show-sticker' : '' ?> sticker_button_comment <?php if(!Configure::read('GifComment.gif_comment_enabled')):?>sticker_button_comment_nogif<?php endif;?> <?php echo $style;?>">
        <i class="material-icons" title="<?php echo __d('sticker', 'Sticker');?>" data-toggle="tooltip" data-original-title="<?php echo __d('sticker', 'Sticker');?>" data-item-type="<?php echo $item_type;?>" data-item-id="<?php echo $item_id;?>" data-photo-theater="<?php echo $photo_theater;?>">
            color_lens
        </i>
    </div>
    <?php if($sticker_image != null):?>
        <div class="sticker_select" id="sticker_<?php echo $item_type;?>_<?php echo $item_id;?>">
            <div class="sticker_select_animation">
                <?php 
                    echo $this->Element('Sticker.misc/sticker_animation', array(
                        'sticker_image' => $sticker_image
                    ));
                ?>
                <div class="sticker_remove_select" data-item-type="<?php echo $item_type;?>" data-item-id="<?php echo $item_id;?>">x</div>
            </div>
        </div>
        <?php if($this->request->is('ajax')): ?>
            <script>
                require(["jquery","mooSticker"], function($, mooSticker) {
                    mooSticker.initEditComment('<?php echo $item_type;?>', '<?php echo $item_id;?>', '<?php echo !empty($sticker_image_id) ? $sticker_image_id : "";?>');
                });
            </script>
        <?php else: ?>
            <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery', 'mooSticker'),  'object' => array('$', 'mooSticker'))); ?>
                mooSticker.initEditComment('<?php echo $item_type;?>', '<?php echo $item_id;?>', '<?php echo !empty($sticker_image_id) ? $sticker_image_id : "";?>');
            <?php $this->Html->scriptEnd();  ?>
        <?php endif; ?>
    <?php endif;?>
<?php else:?>
	<?php echo $this->Form->hidden('sticker_image_id', array(
        'id' => 'sticker_image_id',
        'value' => '',
        'disabled' => 'disabled'
    ));?>
    <div class="sticker_button <?= ($fShowHide) ? 'show-sticker' : '' ?> <?php echo $is_app ? "sticker_button_app" : "";?>">
        <i class="material-icons" title="<?php echo __d('sticker', 'Sticker');?>" data-toggle="tooltip" data-original-title="<?php echo __d('sticker', 'Sticker');?>">
            color_lens
        </i>
    </div>
<?php endif;?>

<?php if($this->request->is('ajax')): ?>
    <script>
        require(['jquery','mooSticker', 'mooStickerSlick', 'mooStickerScrollbar'], function($,mooSticker) {
            mooSticker.initSticker();
        });
    </script>
<?php endif;?>