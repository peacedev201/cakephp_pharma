<?php if(!isset($type)){
   $type = '';
}
if(isset($forum_topics)){
    $topics = $forum_topics;
}
?>
<div class="forum-collapse">
<div class="forum-head clearfix hidden-xs">
    <div class="<?php echo $type != 'my' ? 'col-sm-6' : 'col-sm-12';?>"><span class="forum-head-title forum-head-first"><?php echo __d('forum','Topic');?></span></div>
    <?php if($type != 'my'):?>
    <div class="col-sm-1"><span class="forum-head-title"><?php echo __d('forum','Participants');?></span></div>
    <div class="col-sm-1"><span class="forum-head-title"><?php echo __d('forum','Replies');?></span></div>
    <div class="col-sm-4"><span class="forum-head-title"><?php echo __d('forum','Last Reply');?></span></div>
    <?php endif;?>
</div>
<?php echo $this->element( 'Forum.lists/topic_list', array('type' => $type, 'topics' => $topics) ); ?>
</div>
