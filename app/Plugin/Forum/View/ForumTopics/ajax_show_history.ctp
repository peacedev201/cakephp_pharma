<script type="text/javascript">
    require(["jquery","mooBehavior"], function($, mooBehavior) {
        mooBehavior.initMoreResults();
    });
</script>
<?php $helper = MooCore::getInstance()->getHelper('Forum_Forum');?>
<?php if ($page == 1):?>
<div class="title-modal">
    <?php echo __d('forum','Edit History') ?>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
</div>
<?php endif;?>

<?php if ($page == 1):?>
<div class="modal-body">
<ul id="list-content-history" class="edit-history">
<?php endif;?>
<?php
$forumHelper = MooCore::getInstance()->getHelper('Forum_Forum');
	foreach ($histories as $history){
		?>
		<li>
            <?php if(!empty($history['User']['id'])):?>
			    <?php echo $this->Moo->getItemPhoto(array('User' => $history['User']), array( 'prefix' => '50_square'))?>
            <?php else:?>
                <a class="">
                    <img src="<?php echo $this->request->base;?>/user/img/noimage/Unknown-user-sm.png" class="" alt="<?php echo __d('forum','Deleted Account');?>" title="<?php echo __d('forum','Deleted Account');?>">
                </a>
            <?php endif;?>
                    <div>
                        <div><?php echo !empty($history['User']['id']) ? $this->Moo->getName($history['User']) : '<a><b>'. __d('forum','Deleted Account'). '</b></a>' ;?></div>
						<?php echo $this->Moo->getTime( $history['ForumTopicHistory']['created'], Configure::read('core.date_format'), $utz )?>
                        <div class="forum-reply-content">
						<p><?php echo $forumHelper->viewMore($this->Moo->cleanHtml($helper->bbcodetohtml($history['ForumTopicHistory']['content'],true)),500);?></p>
                        </div>
                    </div>
		</li>
		<?php 	
	} 
	if ($historiesCount > $page * RESULTS_LIMIT)
	{
		?>
		<li>
			<?php $this->Html->viewMore($more_url, 'list-content-history'); ?>
		</li>
		<?php 
	}
?>
<?php if ($page == 1):?>
</ul>
</div>
<?php endif;?>