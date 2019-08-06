<?php
     if($is232)
        $this->Html->css( array('FriendInviter.fi'),array('block' => 'css'));
     else
        echo $this->Html->css(array('jquery.mp','FriendInviter.fi.css'), null, array('inline' => false));
?>
<div class="bar-content full_content p_m_10">
<div class="content_center">
<div class="mo_breadcrumb">
<h1><?php echo  __d('friend_inviter','Invitation sent'); ?></h1>
</div>
<div class="sl-content-nnn">
<?php echo  __d('friend_inviter','Please continue to  '); ?>
<a href="<?php echo $this->request->base?>/friend_inviters">
	<?php echo  __d('friend_inviter','Import Connection'); ?></a>	<br/><br/>
	<div>
            <a class="button button-action" href="<?php echo $this->request->base?>/friend_inviters"><?php echo  __d('friend_inviter','Ok, thanks!'); ?></a>
	</div>
</div> 
</div>
</div>