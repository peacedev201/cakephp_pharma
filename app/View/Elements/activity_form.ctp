<?php
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

if ($user['FEELING'] == FEELING_EXTEND_FOREVER || ($user['FEELING'] && !empty($user['FEELING_valid'])  && date('Ymd') <= date('Ymd',strtotime($user['FEELING_valid']))))
    $fShowHide = true;
?>
<?php if($this->request->is('ajax')):?>
<script type="text/javascript">
    require(["jquery","mooActivities"], function($,mooActivities) {
        mooActivities.initActivityForm();
    });

    require(["jquery","mooToggleEmoji"], function($,mooToggleEmoji) {
        mooToggleEmoji.init('message');
    });
</script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery','mooActivities'), 'object' => array('$', 'mooActivities'))); ?>
mooActivities.initActivityForm();
<?php $this->Html->scriptEnd(); ?>

<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery', 'mooToggleEmoji'),  'object' => array('$', 'mooToggleEmoji'))); ?>
mooToggleEmoji.init('message');
<?php $this->Html->scriptEnd();  ?>

<?php endif; ?>

<form id="wallForm" class="<?= ($fShowHide) ? 'show-feeling' : '' ?>">
	<?php
	echo $this->Form->hidden('type', array('value' => $type));
	echo $this->Form->hidden('target_id', array('value' => $target_id));
	echo $this->Form->hidden('action', array('value' => 'wall_post'));
	echo $this->Form->hidden('wall_photo');
	
	?>
	<div class="form-feed-holder">
		<div class="post-status">
		<?php
		   echo $this->Form->textarea('message', array('name' => 'messageText', 'placeholder' => $text),true);
		?>
            <div id="message-emoji" class="emoji-toggle"></div>
		</div>
	
	</div>
	<div>
		<div id="wall_photo_preview" style="display:none">
			 <span id="addMoreImage" style="display:none;" class="addMoreImage"><i class="material-icons">add</i></span>            
        </div>
            
                <!-- Hook for plugin -->
                <?php $this->getEventManager()->dispatch(new CakeEvent('View.Elements.activityForm.renderReviewItems', $this, array('type' => $type, 'target_id' => $target_id))); ?>

                <?php echo $this->Form->userTagging('','userTagging',true); ?>  
	</div>
        <div class="userTagging-userShareLink">
            <input type="hidden" name="data[userShareLink]" id="userShareLink" value="" autocomplete="off" placeholder="Share link" type="text">
        </div>
        <div class="userTagging-userShareVideo">
            <input type="hidden" name="data[userShareVideo]" id="userShareVideo" value="" autocomplete="off" placeholder="Share link" type="text">
        </div>
        <input type="hidden" name="data[shareImage]" id="shareImage" value="1">
        <div class="stt-action">
            <div style="width: 40px;" data-toggle="tooltip" title="<?php echo __('Add photos to your post');?>" id="select-2"></div>
            <div class="user-tagging-container">
            	<i class="" data-toggle="tooltip" title="<?php echo __('Tag people in your post');?>" onclick="$('.userTagging-userTagging').toggleClass('hidden')"><em class="material-icons">person_add</em></i>
            </div>
            
            <!-- Hook for plugin -->
            <?php $this->getEventManager()->dispatch(new CakeEvent('View.Elements.activityForm.afterRenderItems', $this, array('type' => $type, 'target_id' => $target_id))); ?>
            <div id="commentButton_0" class="post-stt-btn">
			<div class="wall-post-action">
                <?php
                $privacy = array(
                    PRIVACY_EVERYONE => __('Everyone'),
                    PRIVACY_FRIENDS => __('Friends Only'),
                    MY_REGION => __('My Region Only'),
                    MY_JOB_AREA => __('My Job Area Only'),
                );
                ?>
				<?php if (strtolower($type) == 'user' && !$target_id):?>
					<?php echo $this->Form->select('privacy', $privacy, array('empty' => false)); ?>
				<?php else:?>
					<?php echo $this->Form->hidden('privacy', array('value' => PRIVACY_EVERYONE));?>
				<?php endif;?>
				<a href="javascript:void(0)" class="btn btn-action" style="margin-bottom:3px" id="status_btn"> <?php echo __('Share')?></a>
			</div>
            </div>
        </div>
	
	
        
</form>