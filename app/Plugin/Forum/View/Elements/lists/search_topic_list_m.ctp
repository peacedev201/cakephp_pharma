<div class="forum-collapse">
    <div class="forum-head forum-search-head clearfix hidden-xs">
        <div class="col-sm-2"><span class="forum-head-title forum-head-first"><?php echo __d('forum','Author');?></span></div>
        <div class="col-sm-10"><span class="forum-head-title"><?php echo __d('forum','Search Results');?></span></div>
    </div>
    <?php echo $this->element( 'Forum.lists/search_topic_list', array() ); ?>
</div>