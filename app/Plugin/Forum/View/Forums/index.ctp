<?php
$helper = MooCore::getInstance()->getHelper('Forum_Forum');
$this->setNotEmpty('west');?>
<?php $this->start('west'); ?>

<?php echo $this->element('menu');?>

<?php $this->end(); ?>

<style>
    /* Rotating glyphicon when expanding/collapsing */
    .mo_breadcrumb .glyphicon {
        transition: .3s transform ease-in-out;
    }
    .mo_breadcrumb .collapsed .glyphicon {
        transform: rotate(-90deg);
    }
</style>
<div class="bar-content">
    <?php
        foreach ($cats as $cat):
    ?>
    <div class="content_center forum-content-center">
        <div class="forum-category-head">
            <img class="forum-category-img" src="<?php echo $helper->getIconForumCategory($cat);?>" alt="<?php echo $cat['ForumCategory']['name'];?>" title="<?php echo $cat['ForumCategory']['name'];?>">
            <span class="forum-category-name"><?php echo $cat['ForumCategory']['name'];?></span>

            <?php if(Configure::read('Forum.forum_show_expand') == true): ?>
            <a class="forum-toggle-btn" href="#" data-toggle="collapse" data-target="#forum_<?php echo $cat['ForumCategory']['id']?>">
                <span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span>
            </a>
            <?php endif;?>
        </div>
        <?php
        if(count($cat['Forums']) == 0):
            echo '<div class="forum-collapse collapse in" id="forum_' .$cat['ForumCategory']['id'] .'">'. __d('forum','No Forum') .'</div>';
        else:
        ?>
        <div class="forum-collapse collapse in" id="forum_<?php echo $cat['ForumCategory']['id']?>">
            <div class="forum-head clearfix hidden-xs">
                <div class="col-sm-6"><span class="forum-head-title forum-head-first"><?php echo __d('forum','Forum');?></span></div>
                <div class="col-sm-1"><span class="forum-head-title"><?php echo __d('forum','Topics');?></span></div>
                <div class="col-sm-1"><span class="forum-head-title"><?php echo __d('forum','Replies');?></span></div>
                <div class="col-sm-4"><span class="forum-head-title"><?php echo __d('forum','Last Post');?></span></div>
            </div>
           <?php echo $this->element('lists/forum_list', array('forums' => $cat['Forums'], 'helper' => $helper));?>
        </div>
        <?php endif;?>
    </div>
    <?php endforeach;?>

</div>