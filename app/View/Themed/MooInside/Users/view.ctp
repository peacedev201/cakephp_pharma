
<style>
    #themeModal .modal-body{
        padding:15px;
    }
</style>

<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery","mooUser"], function($,mooUser) {
        mooUser.initOnUserView();
    });
</script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery','mooUser'), 'object' => array('$', 'mooUser'))); ?>
mooUser.initOnUserView();
<?php $this->Html->scriptEnd(); ?> 
<?php endif; ?>


<?php $this->setNotEmpty('east');?>
<?php $this->start('east'); ?>
	<?php if ( $canView ): ?>
	

	<?php if ($user['User']['friend_count']): ?>
	<div class="box2 box-friend" >
		<h3><?php echo __('Friends')?> (<?php echo $user['User']['friend_count']?>)</h3>
		<div class="box_content">
		    <?php echo $this->element( 'blocks/users_block', array( 'users' => $friends ) ); ?>
		</div>
	</div>
	<?php endif; ?>
	
	<?php if ( !empty( $mutual_friends ) ): ?>
	<div class="box2 mutual-friend">
		<h3>
		
		
<?php
      $this->MooPopup->tag(array(
             'href'=>$this->Html->url(array("controller" => "friends",
                                            "action" => "ajax_show_mutual",
                                            "plugin" => false,
                                            $user['User']['id']
                                            
                                        )),
             'title' => __('Mutual Friends'),
             'innerHtml'=> __('Mutual Friends'),
     ));
 ?>
</h3>
		<div class="box_content">
		    <?php echo $this->element( 'blocks/users_block', array( 'users' => $mutual_friends ) ); ?>
		</div>
	</div>
	<?php endif; ?>
    <?php endif; ?>

	<?php if ( $canView ): ?>
	    
		 <?php echo $this->element('Video.blocks/videos_block'); ?>
	
		<?php echo $this->element('Blog.blocks/blogs_block'); ?>
	
		<?php echo $this->element('Group.blocks/group_block'); ?>
		
	<?php endif; ?>
		
	
<?php $this->end(); ?>

<div class="profilePage ">
	<div id="profile-content">
		<?php 
		if ( !empty( $activity ) )
		{
			echo '<ul class="list6 comment_wrapper" id="list-content">';
                        ?>
                        <?php if (isset($groupTypeItem['type'])): ?>
                            <script>
                                
                            </script>

                            <?php if($groupTypeItem['type'] == PRIVACY_RESTRICTED && !$groupTypeItem['is_member']): ?>
                            <div class="privacy_mess">
                                <div class="m_b_5"><?php echo __('This content is private'); ?></div>
                                <a href="javascript:void(0);" onclick="return requestJoinGroup(<?php echo $groupTypeItem['id']; ?>);" class="btn btn-action"><?php echo __('Join Group to access'); ?></a>
                            </div>
                            <?php elseif($groupTypeItem['type'] == PRIVACY_PRIVATE && !$groupTypeItem['is_member']): ?>
                                <div class="privacy_mess"><?php echo __('This is a private group. You must be invited by a group admin in order to join'); ?></div>

                            <?php else: ?>
                                <?php if (Configure::read('core.comment_sort_style') == COMMENT_RECENT): ?>
                                    <?php echo $this->element( 'activities', array( 'activities' => array( $activity ) ) ); ?>
                                <?php elseif(Configure::read('core.comment_sort_style') == COMMENT_CHRONOLOGICAL): ?>
                                    <?php echo $this->element( 'activities_chrono', array( 'activities' => array( $activity ) ) ); ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php elseif(isset($eventTypeItem) && empty($eventTypeItem)): ?>
                            <div class="privacy_mess"><?php echo __('This is a private event.'); ?></div>
                        <?php else: ?>
                            <?php if (Configure::read('core.comment_sort_style') == COMMENT_RECENT): ?>
                                <?php echo $this->element( 'activities', array( 'activities' => array( $activity ) ) ); ?>
                            <?php elseif(Configure::read('core.comment_sort_style') == COMMENT_CHRONOLOGICAL): ?>
                                <?php echo $this->element( 'activities_chrono', array( 'activities' => array( $activity ) ) ); ?>
                            <?php endif; ?>
                        <?php endif; ?>
			<?php echo '</ul>';
		}
		else
		{		
			if ( $canView )
				echo $this->element('ajax/profile_detail');
			else
				printf( __('<div class="privacy_profile full_content p_m_10">%s only shares some information with everyone</div>'), $user['User']['name'] );
		}		
		?>
	</div>
</div> 