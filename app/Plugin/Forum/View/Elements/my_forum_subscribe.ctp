<?php
$helper = MooCore::getInstance()->getHelper('Forum_Forum');
?>
<div class="forum-category-head">
    <span class="forum-category-name"><?php echo __d('forum','Subscribed Forums');?></span>
</div>

<?php if (!empty($forums)): ?>
<div class="forum-collapse forum-mb15">
    <div class="forum-head clearfix hidden-xs">
        <div class="col-sm-6"><span class="forum-head-title forum-head-first"><?php echo __d('forum','Forums');?></span></div>
        <div class="col-sm-1"><span class="forum-head-title"><?php echo __d('forum','Topics');?></span></div>
        <div class="col-sm-1"><span class="forum-head-title"><?php echo __d('forum','Replies');?></span></div>
        <div class="col-sm-4"><span class="forum-head-title"><?php echo __d('forum','Last Post');?></span></div>
    </div>
    <?php echo $this->element('lists/forum_list', array('forums' => $forums, 'helper' => $helper));?>
</div>
<?php else: ?>
    <div class="topic-body text-center"><?php echo __d('forum','No more results found');?></div>
<?php endif;?>