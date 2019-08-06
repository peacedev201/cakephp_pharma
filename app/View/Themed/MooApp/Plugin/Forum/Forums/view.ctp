<?php
$forumHelper = MooCore::getInstance()->getHelper('Forum_Forum');
$is_moderator = $forumHelper->checkModerator(array('User' => $cuser),array('Forum'=>$forum['Forum']));
?>
<div class="forum-bar-content">
    <form method="get" action="<?php echo $forum['Forum']['moo_href'];?>" id="form_forum_search_topic">
        <div class="input-group">
            <?php echo $this->Form->text('keyword', array('class' => 'form-control', 'placeholder' => __d('forum','Search Forum'), 'value' => !empty($keyword) ? $keyword : ''));?>
            <span class="input-group-btn">
                <input type="submit" class="topButton mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1" id="btn_forum_search_topic" value="<?php echo __d('forum', 'Search'); ?>"/>
            </span>
        </div>
    </form>
</div>

<div class="bar-content">
    <div class="content_center forum-content-center">
        <div class="mo_breadcrumb">
            <h1><?php echo h($forum['Forum']['moo_title'])?></h1>

            <?php if ($uid):?>
                <a href="<?php echo $this->request->base?>/forums/topic/create/<?php echo $forum['Forum']['id'];?>" class="topButtonX mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1" id="<?php echo (!$forum['Forum']['status'] && !$is_moderator && !$cuser['Role']['is_admin']) ? 'btn_forum_locked' : '';?>"><?php echo __d('forum','Create New Topic');?></a>
            <?php endif;?>
        </div>

        <div class="post_content">
            <?php echo $this->Moo->cleanHtml($this->Text->convert_clickable_links_for_hashtags( $forum['Forum']['description'] , 1 ))?>
        </div>

        <div class="content-pagination forum-pagination clearfix">
            <div class="pagination-count">
                <?php echo __d('forum','Viewing %s topics', $this->Paginator->counter('{:current}')).' - '.$this->Paginator->counter('{:start}').__d('forum',' through %s (of %s total)', $this->Paginator->counter('{:end}'), $this->Paginator->counter('{:count}')) ;?>
            </div>
            <ul class="pagination">
                <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->first(__d('forum', 'First'), array('class' => 'paginate_button previous', 'tag' => 'li'), '<a href="javascript:void(0)">'.__d('forum', 'First').'</a>', array('class' => 'paginate_button disabled', 'tag' => 'li', 'escape' => false)) : '';?>
                <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->prev(__d('forum', 'Prev'), array('class' => 'paginate_button previous', 'tag' => 'li'), '<a href="javascript:void(0)">'.__d('forum', 'Previous').'</a>', array('class' => 'paginate_button disabled', 'tag' => 'li', 'escape' => false)) : '';?>
                <?php echo $this->Paginator->numbers(array('class' => 'paginate_button', 'separator' => '', 'tag' => 'li', 'currentLink' => true, 'currentClass' => 'active', 'currentTag' => 'span')); ?>
                <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->next(__d('forum', 'Next'), array('class' => 'paginate_button next', 'tag' => 'li'), '<a href="javascript:void(0)">'.__d('game', 'Next').'</a>', array('class' => 'paginate_button disabled', 'tag' => 'li', 'escape' => false)) : '';?>
                <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->last(__d('forum', 'Last'), array('class' => 'paginate_button next', 'tag' => 'li'), '<a href="javascript:void(0)">'.__d('game', 'Last').'</a>', array('class' => 'paginate_button disabled', 'tag' => 'li', 'escape' => false)) : '';?>
            </ul>
        </div>

        <div class="forum-list-topics">
            <div id="list-content">
                <?php echo $this->element( 'lists/topic_list_m', array() ); ?>
            </div>
        </div>

        <div class="content-pagination forum-pagination clearfix">
            <div class="pagination-count">
                <?php echo __dn('forum','Viewing %s topic','Viewing %s topics', $this->Paginator->counter('{:current}'),$this->Paginator->counter('{:current}')).' - '.$this->Paginator->counter('{:start}').__d('forum',' through %s (of %s total)', $this->Paginator->counter('{:end}'), $this->Paginator->counter('{:count}')) ;?>
            </div>
            <ul class="pagination">
                <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->first(__d('forum', 'First'), array('class' => 'paginate_button previous', 'tag' => 'li'), '<a href="javascript:void(0)">'.__d('forum', 'First').'</a>', array('class' => 'paginate_button disabled', 'tag' => 'li', 'escape' => false)) : '';?>
                <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->prev(__d('forum', 'Prev'), array('class' => 'paginate_button previous', 'tag' => 'li'), '<a href="javascript:void(0)">'.__d('forum', 'Previous').'</a>', array('class' => 'paginate_button disabled', 'tag' => 'li', 'escape' => false)) : '';?>
                <?php echo $this->Paginator->numbers(array('class' => 'paginate_button', 'separator' => '', 'tag' => 'li', 'currentLink' => true, 'currentClass' => 'active', 'currentTag' => 'span')); ?>
                <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->next(__d('forum', 'Next'), array('class' => 'paginate_button next', 'tag' => 'li'), '<a href="javascript:void(0)">'.__d('game', 'Next').'</a>', array('class' => 'paginate_button disabled', 'tag' => 'li', 'escape' => false)) : '';?>
                <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->last(__d('forum', 'Last'), array('class' => 'paginate_button next', 'tag' => 'li'), '<a href="javascript:void(0)">'.__d('game', 'Last').'</a>', array('class' => 'paginate_button disabled', 'tag' => 'li', 'escape' => false)) : '';?>
            </ul>
        </div>
    </div>

    <?php if(!empty($sub_forums)):?>
    <div class="content_center forum-content-center">
        <div class="forum-category-head">
            <span class="forum-category-name"><?php echo __d('forum','Sub-forums');?></span>
        </div>

        <div class="forum-collapse">
            <div class="forum-head clearfix hidden-xs">
                <div class="col-sm-6"><span class="forum-head-title forum-head-first"><?php echo __d('forum','Forum');?></span></div>
                <div class="col-sm-1"><span class="forum-head-title"><?php echo __d('forum','Topics');?></span></div>
                <div class="col-sm-1"><span class="forum-head-title"><?php echo __d('forum','Replies');?></span></div>
                <div class="col-sm-4"><span class="forum-head-title"><?php echo __d('forum','Last Post');?></span></div>
            </div>
            <?php echo $this->element('lists/forum_list', array('forums' => $sub_forums));?>
        </div>
    </div>
    <?php endif;?>
</div>

<div class="bar-content">
<div class="forum-content-center box2 filter_block tp-info">
    <div class="mo_breadcrumb">
        <h1><?php echo __d('forum','Forum Info');?></h1>
    </div>

    <div class="clearfix">
        <ul class="forum-list-info">
            <li>
                <i class="material-icons">brightness_high</i> <?php echo $forum['Forum']['count_topic'].' '.__d('forum','topics');?>
            </li>
            <li>
                <i class="material-icons">forum</i> <?php echo $forum['Forum']['count_reply'].' '.__d('forum','replies');?>
            </li>
            <?php if(!empty($last_topic)):?>
            <li>
                <i class="material-icons">account_circle</i>
                <?php $last_reply_user = MooCore::getInstance()->getItemByType('User', $last_topic['ForumTopic']['user_id']);?>
                <?php echo __d('forum','Last post by');?>:  <?php echo !empty($last_reply_user) ? $this->Moo->getName($last_reply_user['User'], true) : ''; ?>
            </li>
            <li>
                <i class="material-icons">access_time</i>
                <?php echo __d('forum','Last activity');?>: <?php echo $this->Moo->getTime($last_topic['ForumTopic']['modified'], Configure::read('core.date_format'), $utz)?>
            </li>
            <?php endif;?>
            <?php if(!empty($moderators)):?>
            <li>
                <i class="material-icons">person_pin</i>
                <?php echo __d('forum','Moderators');?>:
                <?php foreach( $moderators as $mod):?>
                <a class="mod-user" href="<?php echo $mod['User']['moo_href'];?>"><?php echo $mod['User']['moo_title'];?></a>
                <?php endforeach;?>
            </li>
            <?php endif;?>
            <?php if(!empty($uid)):?>
            <li>
                <a class="btn_subscribe <?php echo $is_subscribe ? 'active' : '';?>" data-id="<?php echo $forum['Forum']['id'];?>">
                    <?php if($is_subscribe):?>
                        <i class="material-icons">done</i>
                    <?php else:?>
                        <i class="material-icons">rss_feed</i>
                    <?php endif;?>
                    <?php if($is_subscribe):?>
                    <i class="material-icons">done</i><span><?php echo __d('forum','Subscribed');?></span>
                    <?php else:?>
                    <i class="material-icons" style="display: none">done</i><span><?php echo __d('forum','Subscribe');?></span>
                    <?php endif;?>
                </a>
            </li>
            <?php endif;?>
        </ul>
    </div>
</div>
</div>
<script>
    function doRefesh()
    {
        location.reload();
    }
</script>

<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery","mooForum"], function($,mooForum) {
        mooForum.initOnViewForum();
    });
</script>
<?php else: ?>
    <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery','mooForum'), 'object' => array('$', 'mooForum'))); ?>
        mooForum.initOnViewForum();
    <?php $this->Html->scriptEnd(); ?>
<?php endif; ?>

