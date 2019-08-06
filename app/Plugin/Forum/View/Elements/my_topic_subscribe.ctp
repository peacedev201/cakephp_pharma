<div class="forum-category-head">
    <span class="forum-category-name"><?php echo __d('forum','Subscribed Topics');?></span>
</div>
<div class="forum-collapse">
    <div class="forum-head clearfix hidden-xs">
        <div class="col-sm-6"><span class="forum-head-title forum-head-first"><?php echo __d('forum','Topic');?></span></div>
        <div class="col-sm-1"><span class="forum-head-title"><?php echo __d('forum','Voices');?></span></div>
        <div class="col-sm-1"><span class="forum-head-title"><?php echo __d('forum','Replies');?></span></div>
        <div class="col-sm-4"><span class="forum-head-title"><?php echo __d('forum','Last Reply');?></span></div>
    </div>
    <?php echo $this->element( 'lists/topic_list', array() ); ?>
</div>