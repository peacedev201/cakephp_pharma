<?php $forumHelper = MooCore::getInstance()->getHelper('Forum_Forum');
    $shareUrl = $this->Html->url(array(
        'plugin' => false,
        'controller' => 'share',
        'action' => 'ajax_share',
        'Forum_Forum_Topic',
        'id' => $topic['ForumTopic']['id'],
        'type' => 'forum_topic_item_detail'
        ), true);

    $is_moderator = $forumHelper->checkModerator(array('User' => $cuser),array('Forum'=>$topic['Forum']));
    $pin_info = $forumHelper->getPinInfo($topic['ForumTopic']['id']);
?>

<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery', 'mooForum', 'hideshare'),'object'=>array('$', 'mooForum'))); ?>
mooForum.initOnViewTopic();
<?php $this->Html->scriptEnd(); ?>

<div class="forum-bar-content">
    <form method="get" action="<?php echo $this->request->base.'/forums/topic/view/'.$topic['ForumTopic']['id']?>?app_no_tab=1" id="form_search_reply">
        <div class="input-group">
            <?php echo $this->Form->text('keyword', array('class' => 'form-control', 'placeholder' => __d('forum','Search')));?>
            <span class="input-group-btn">
                <input type="submit" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1" id="btn_search_reply" value="<?php echo __d('forum', 'Search'); ?>"/>
            </span>
        </div>
    </form>
</div>

<div class="forum-bar-content">
    <div class="post_body forum_post_body">
        <div class="mo_breadcrumb forum-topic-breadcrumb">
            <h1><?php echo h($topic['ForumTopic']['title'])?></h1>
            <?php if(!empty($uid)): ?>
            <div class="list_option">
                <div class="dropdown">
                    <button id="dropdown-edit" data-target="#" data-toggle="dropdown">
                        <i class="material-icons">more_vert</i>
                    </button>

                    <ul role="menu" class="dropdown-menu" aria-labelledby="dropdown-edit">
                        <?php if ( (($topic['ForumTopic']['user_id'] == $uid ) || $is_moderator) && Configure::read('Forum.forum_enable_user_pin_topic')):?>
                            <?php if ($topic['ForumTopic']['ping']):?>
                                <li><a href="javascript:void(0)" class="unpin-forum-topic" data-id="<?php echo $topic['ForumTopic']['id'];?>" title="<?php echo __d('forum','UnPin')?>"><?php echo __d('forum', 'UnPin')?></a></li>
                            <?php else:?>
                                <li><a href="<?php echo $this->request->base. '/forums/topic/pin/'.$topic['ForumTopic']['id'] ;?>" class="pin-topic" title="<?php echo __d('forum','Pin')?>"><?php echo __d('forum', 'Pin')?></a></li>
                            <?php endif;?>
                        <?php endif;?>
                        <?php if ($is_moderator):?>
                            <li><a href="javascript:void(0)" class="lock-topic" data-id="<?php echo $topic['ForumTopic']['id'];?>"> <?php echo $topic['ForumTopic']['status'] ?  __d('forum','Lock') : __d('forum','Open');?></a></li>
                        <?php endif;?>
                        <?php if ( ($topic['ForumTopic']['user_id'] == $uid ) || ( !empty( $topic['ForumTopic']['id'] ) && !empty($cuser) && $cuser['Role']['is_admin'] ) || $is_moderator): ?>
                            <li><a href="<?php echo $this->request->base.'/forums/topic/create/'.$topic['ForumTopic']['forum_id'].'/'.$topic['ForumTopic']['id'];?>?app_no_tab=1" class="edit-topic"> <?php echo __d('forum','Edit')?></a></li>
                            <li><a href="javascript:void(0)" class="delete-topic" data-id="<?php echo $topic['ForumTopic']['id'];?>"> <?php echo __d('forum','Delete')?></a></li>
                        <?php endif; ?>
                        <li>
                            <a href="<?php echo $this->request->base.'/forum/forum_reports/ajax_create/'. $topic['ForumTopic']['id'];?>">
                                <?php echo __d('forum', 'Report');?>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo $this->request->base.'/forums/topic/ajax_invite/'. $topic['ForumTopic']['id'];?>"><?php echo  __d('forum', 'Invite Friends');?></a>
                        </li>
                        <?php echo $this->element('share/menu',array('param' => 'Forum_ForumTopic','action' => 'forum_topic_item_detail' ,'id'=>$topic['ForumTopic']['id'])); ?>
                        <li class="seperate"></li>
                    </ul>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="forum-topic-content-head">
            <div class="forum-owner-topic">
                <div class="forum-owner-avatar">
                    <?php if(!empty($topic['User']['id'])):?>
                        <?php echo $this->Moo->getImage(array('User' => $topic['User']), array("prefix" => "50_square", "alt"=>h($topic['User']['name']))); ?>
                    <?php else:?>
                        <img src="<?php echo $this->request->base;?>/user/img/noimage/Unknown-user-sm.png" title="<?php echo __d('forum','Deleted Account');?>">
                    <?php endif;?>
                </div>
                <div class="forum-owner-info">
                    <span class="forum-owner-name">
                        <?php echo $this->Moo->getName($topic['User'], true); ?>
                    </span>
                    <div class="forum-owner-date">
                        <?php echo $this->Moo->getTime($topic['ForumTopic']['created'], Configure::read('core.date_format'), $utz)?>
                    </div>
                    <?php if(!empty($pin_info)):?>
                        <div class="forum-topic-pin-info">
                            <?php echo __dn('forum', 'Pinned %s day by', 'Pinned %s days by', $pin_info['ForumPin']['time'], $pin_info['ForumPin']['time']).' '.$this->Moo->getName($pin_info['User']). ' '. $this->Moo->getTime( $pin_info['ForumPin']['created'], Configure::read('core.date_format'), $utz );?>
                        </div>
                    <?php endif;?>
                </div>
            </div>
        </div>

        <div class="forum-post_content">
            <div class="post_content">
                <?php
                //$text = htmlspecialchars($text, ENT_QUOTES, 'utf-8');
                echo $forumHelper->bbcodetohtml($this->Moo->cleanHtml($this->Text->convert_clickable_links_for_hashtags( $topic['ForumTopic']['description']  , Configure::read('Forum.forum_enable_hashtag') )),true)?>
            </div>

            <?php if(!empty($cuser['signature'])):?>
                <div class="forum-user-signature">
                    <?php echo $cuser['signature'];?>
                </div>
            <?php endif;?>

            <div class="tp-files-list">
                <?php echo $this->element( 'lists/file_list', array() ); ?>
            </div>

            <div class="forum-topic-section">
                <?php if(!empty($cuser)):?>
                    <a class="forum-btn-link btn_subscribe" data-id="<?php echo $topic['ForumTopic']['id'];?>">
                        <?php if($is_subscribe):?>
                            <i class="material-icons">done</i>
                        <?php else:?>
                            <i class="material-icons">rss_feed</i>
                        <?php endif;?>
                        <?php echo $is_subscribe ? __d('forum','Unsubscribe') : __d('forum','Subscribe');?>
                    </a>
                    <a class="forum-btn-link btn_favorite" data-id="<?php echo $topic['ForumTopic']['id'];?>">
                        <?php if($is_favorite):?>
                            <i class="material-icons">star</i>
                        <?php else:?>
                            <i class="material-icons">star_border</i>
                        <?php endif;?>
                        <?php echo __d('forum','Favorite');?>
                    </a>
                    <?php
                    $this->MooPopup->tag(array(
                        'href'=>$this->Html->url(array("controller" => "forumTopics",
                            "action" => "ajax_invite",
                            "plugin" => 'forum',
                            $topic['ForumTopic']['id'],
                        )),
                        'title' => __d('forum', 'Invite Friends'),
                        'innerHtml'=> '<i class="material-icons">person_add</i>'.__d('forum', 'Invite Friends'),
                        'class' => 'forum-btn-link inviteFriendsBtn'
                    ));
                    ?>

                    <a class="forum-btn-link quote-reply" data-id="<?php echo $topic['ForumTopic']['id'];?>">
                        <i class="material-icons">format_quote</i><?php echo __d('forum','Quote');?>
                    </a>
                    <a class="forum-btn-link forum-post-reply" href="#wrap_form_reply">
                        <i class="material-icons">reply</i><?php echo __d('forum','Post reply');?>
                    </a>
                    <?php
                    $this->MooPopup->tag(array(
                        'href'=>$this->Html->url(array("controller" => "forumTopics",
                            "action" => "ajax_show_history",
                            "plugin" => 'forum',
                            $topic['ForumTopic']['id']
                        )),
                        'title' => __d('forum','Show edit history'),
                        'innerHtml'=> '<i class="material-icons">change_history</i>'.__d('forum','Edit history'),
                        'style' => empty($reply['ForumTopic']['edited']) ? 'display:none;' : '',
                        'id' => 'history_reply_'. $topic['ForumTopic']['id'],
                        'class' => 'forum-btn-link edit-history-btn',
                        'data-dismiss'=>'modal'
                    ));
                    ?>
                <?php endif;?>
            </div>
        </div>
    </div>
</div>

<?php if(!empty($keyword)):?>
    <div class="bar-content full_content p_m_10">
        <div class="content_center">
            <h5><?php echo __d('forum','Search results for "%s" keyword', h($keyword));?></h5>
        </div>
    </div>
<?php endif;?>

<div class="forum-bar-content">
    <div class="content-pagination forum-pagination clearfix">
        <div class="pagination-count">
            <?php echo __dn('forum','Viewing %s reply','Viewing %s replies', $this->Paginator->counter('{:current}'), $this->Paginator->counter('{:current}')).' - '.$this->Paginator->counter('{:start}').__d('forum',' through %s (of %s total)', $this->Paginator->counter('{:end}'), $this->Paginator->counter('{:count}')) ;?>
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

<div class="forum-bar-content">
    <div class="forum-list-reply">
        <?php if(!empty($reply_id) || !empty($keyword)):?>
            <a class="view-all-reply" href="<?php echo $topic['ForumTopic']['moo_href'];?>"><?php echo __d('forum','View all replies');?></a>
        <?php endif;?>
        <?php echo $this->element( 'lists/list_replies', array('is_moderator' => $is_moderator) ); ?>
    </div>
</div>

<div class="forum-bar-content">
    <div class="content-pagination forum-pagination clearfix">
        <div class="pagination-count">
            <?php echo __dn('forum','Viewing %s reply','Viewing %s replies', $this->Paginator->counter('{:current}'), $this->Paginator->counter('{:current}')).' - '.$this->Paginator->counter('{:start}').__d('forum',' through %s (of %s total)', $this->Paginator->counter('{:end}'), $this->Paginator->counter('{:count}')) ;?>
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

<div class="forum-bar-content">
    <div class="content_center">
        <div class="forum_post_body" id="wrap_form_reply">
            <?php if($topic['ForumTopic']['status']|| $is_moderator):?>
                <?php if(!empty($cuser)):?>
                    <?php echo $this->element( 'reply_form', array()); ?>
                <?php endif;?>
            <?php else:?>
                <p><?php echo __d('forum','This topic is marked as locked to new reply');?></p>
            <?php endif;?>
        </div>
    </div>
</div>

<div class="bar-content">
    <div class="forum-content-center box2 filter_block tp-info">
        <div class="mo_breadcrumb">
            <h1><?php echo __d('forum','Topic Info');?></h1>
        </div>
        <?php /*if($topic['ForumTopic']['thumb']):;*/?><!--
            <img width="50" src="<?php /*echo $forumHelper->getTopicImage($topic, array('prefix' => '100'))*/?>" class="img_wrapper2 user_list thumb_mobile">
        --><?php /*endif;*/?>
        <div class="clearfix">
            <ul class="forum-list-info">
                <li>
                    <i class="material-icons">brightness_high</i> <?php echo __d('forum','In');?>: <a href="<?php echo $topic['Forum']['moo_href'];?>"><?php echo $topic['Forum']['moo_title'];?></a>
                </li>
                <li>
                    <i class="material-icons">forum</i>
                    <?php echo $topic['ForumTopic']['count_reply'].' '.__d('forum','replies');?>
                </li>
                <li>
                    <i class="material-icons">remove_red_eye</i>
                    <?php echo $topic['ForumTopic']['count_view'].' '.__d('forum','views');?>
                </li>
                <li>
                    <i class="material-icons">account_circle</i>
                    <?php echo $topic['ForumTopic']['count_user'].' '.__d('forum','participants');?>
                </li>
                <?php if(!empty($last_reply)):?>
                    <li>
                        <i class="material-icons">access_time</i>
                        <?php $last_reply_user = MooCore::getInstance()->getItemByType('User', $last_reply['ForumTopic']['user_id']);?>
                        <?php echo __d('forum','Last reply from');?>:  <?php echo !empty($last_reply_user) ? $this->Moo->getName($last_reply_user['User'], true) : ''; ?>
                    </li>
                    <li>
                        <i class="material-icons">av_timer</i>
                        <?php echo __d('forum','Last activity');?>: <?php echo $this->Moo->getTime($last_reply['ForumTopic']['modified'], Configure::read('core.date_format'), $utz)?>
                    </li>
                <?php endif;?>

                <?php if(!empty($uid)):?>
                    <li class="tp-info-item">
                        <a class="btn_subscribe  <?php echo $is_subscribe ? 'active' : '';?>" data-id="<?php echo $topic['ForumTopic']['id'];?>">
                            <?php if($is_subscribe):?>
                                <i class="material-icons">done</i><span><?php echo __d('forum','Unsubscribe');?></span>
                            <?php else:?>
                                <i class="material-icons">rss_feed</i><span><?php echo __d('forum','Subscribe');?></span>
                            <?php endif;?>
                        </a>
                    </li>
                    <li class="tp-info-item">
                        <a class="btn_favorite <?php echo $is_favorite ? 'active' : '';?>" data-id="<?php echo $topic['ForumTopic']['id'];?>">
                            <?php if($is_favorite):?>
                                <i class="material-icons">star</i>
                            <?php else:?>
                                <i class="material-icons">star_border</i>
                            <?php endif;?>
                            <?php echo __d('forum','Favorite');?>
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
