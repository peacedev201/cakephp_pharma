<?php
	$helper = MooCore::getInstance()->getHelper('Forum_Forum');
$this->addPhraseJs(array(
    'url_has_been_copied_to_clipboard' => __d('forum','Url has been copied to clipboard')
));
$page = 1;
if(isset($this->request->params['paging']['ForumTopic']['page']))
    $page = $this->request->params['paging']['ForumTopic']['page'];
?>

<?php if (!empty($replies)): ?>
<?php
    $ssl_mode = Configure::read('core.ssl_mode');
    $http = (!empty($ssl_mode)) ? 'https' :  'http';
    $forumFileModel = Moocore::getInstance()->getModel('Forum.ForumFile');
?>
<?php if($this->request->is('ajax')): ?>
    <script type="text/javascript">
        require(["jquery","mooForum"], function($,mooForum) {
            mooForum.initListReply();
        });
    </script>
<?php else: ?>
    <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery', 'mooForum'), 'object' => array('$', 'mooForum'))); ?>
    mooForum.initListReply();
    <?php $this->Html->scriptEnd(); ?>
<?php endif; ?>

<?php foreach ($replies as $reply):?>
    <?php $reply_files = $forumFileModel->getFiles($reply['ForumTopic']['id']);?>
    <div class="forum-reply-item">

        <div class="forum-reply-head">
            <div class="forum-reply-user">
                <?php if(!empty($reply['User']['id'])):?>
                    <div class="forum-reply-avatar">
                        <?php echo $this->Moo->getItemPhoto(array('User' => $reply['User']), array('prefix' => '50_square'), array('class' => 'reply-author-avatar'))?>
                    </div>
                    <div class="forum-reply-info">
                        <span class="forum-reply-name">
                        <?php echo $this->Moo->getName($reply['User'], true); ?>
                        </span>
                        <?php $this->getEventManager()->dispatch(new CakeEvent('element.comments.afterRenderUserNameComment', $this,array('user'=>$reply['User']))); ?>
                        <?php if($helper->checkModerator($reply, array('Forum' => $reply['Forum']))):?>
                        <div class="forum-reply-role"><?php echo __d('forum','Moderator');?></div>
                        <?php endif;?>
                    </div>
                <?php else:?>
                    <div class="forum-reply-avatar">
                        <a class="user-img-deleted">
                            <img src="<?php echo $this->request->base;?>/user/img/noimage/Unknown-user-sm.png" class="reply-author-avatar" alt="<?php echo __d('forum','Deleted Account');?>" title="<?php echo __d('forum','Deleted Account');?>">
                        </a>
                    </div>
                    <div class="forum-reply-info">
                        <span class="forum-reply-name">
                           <a class="username-deleted"><b><?php echo __d('forum','Deleted Account');?></b></a>
                        </span>
                    </div>
                <?php endif;?>
            </div>
        </div>

        <div class="forum-reply-body">
            <div class="forum-reply-content">
                <?php echo $this->Moo->cleanHtml($helper->bbcodetohtml($reply['ForumTopic']['description'],true));?>
            </div>
            <?php if(!empty($reply['User']['signature']) && $reply['User']['show_signature']):?>
            <div class="forum-user-signature">
                <?php echo $reply['User']['signature'];?>
            </div>
            <?php endif;?>
            <?php echo $this->element( 'lists/file_list', array('files' => $reply_files) ); ?>
        </div>
        <div class="forum-reply-footer">
            <div class="forum-reply-date">
                <a href="<?php echo $topic['ForumTopic']['moo_href'].'/reply_id:'.$reply['ForumTopic']['id'] ;?>">
                    <i class="material-icons">schedule</i><?php echo $this->Moo->getTime( $reply['ForumTopic']['created'], Configure::read('core.date_format'), $utz )?>
                </a>
            </div>
            <div class="forum-reply-action">
                <?php if(!empty($cuser)):
                    $is_active = isset($reply_thanks[$reply['ForumTopic']['id']]) ? 'active' : '';
                ?>
                    <a class="forum-btn-link thank-topic btn-thank-<?php echo $reply['ForumTopic']['id'];?> <?php echo $is_active;?>" data-id="<?php echo $reply['ForumTopic']['id'];?>" data-parent="<?php echo $reply['ForumTopic']['parent_id'];?>">
                        <i class="material-icons">mood</i><?php echo __d('forum','Thanks');?>
                    </a>
                    <?php
                     $this->MooPopup->tag(array(
                        'href'=>$this->Html->url(array("controller" => "forumTopics",
                        "action" => "ajax_show",
                        "plugin" => 'forum',
                        $reply['ForumTopic']['id'],
                        )),
                        'title' => __d('forum','People Who Thank This'),
                        'innerHtml'=> '(<span id="topic_thank_'. $reply['ForumTopic']['id']. '">' . $reply['ForumTopic']['count_thank'] . '</span>)',
                        'data-dismiss' => 'modal',
                         'class' => 'forum-btn-link thank-topic-count btn-thank-'. $reply['ForumTopic']['id']. ' ' . $is_active
                    ));
                    ?>
                    <a class="forum-btn-link quote-reply" data-id="<?php echo $reply['ForumTopic']['id'];?>">
                        <i class="material-icons">format_quote</i><?php echo __d('forum','Quote');?>
                    </a>

                    <a class="forum-btn-link" href="<?php echo $this->request->base.'/forum/forum_reports/ajax_create/'. $reply['ForumTopic']['id'];?>">
                        <i class="material-icons">flag</i><?php echo __d('forum', 'Report');?>
                    </a>
                <?php endif;?>

                <?php if ( ($reply['ForumTopic']['user_id'] == $uid ) || ( !empty( $reply['ForumTopic']['id'] ) && !empty($cuser) && $cuser['Role']['is_admin'] ) || $is_moderator ): ?>

                    <a id="" href="<?php echo $this->request->base.'/forums/topic/reply/'.$reply['ForumTopic']['id'];?>?page=<?php echo $page;?>&app_no_tab=1" class="forum-btn-link">
                        <i class="material-icons">edit</i><?php echo __d('forum','Edit');?>
                    </a>
                    <a class="forum-btn-link delete-topic" data-id="<?php echo $reply['ForumTopic']['id'];?>" data-topic="<?php echo $reply['ForumTopic']['parent_id'];?>">
                        <i class="material-icons">delete</i><?php echo __d('forum','Delete');?>
                    </a>
                <?php endif;?>
                <a class="forum-btn-link topic-get-link" data-href="<?php echo $http.'://'.$_SERVER['SERVER_NAME'].$this->request->base.'/forums/topic/view/'.$topic['ForumTopic']['id'].'/reply_id:'.$reply['ForumTopic']['id'];?>">
                    <i class="material-icons">link</i><?php echo __d('forum','Get link');?>
                </a>
                <?php
                $this->MooPopup->tag(array(
                    'href'=>$this->Html->url(array("controller" => "forumTopics",
                    "action" => "ajax_show_history",
                    "plugin" => 'forum',
                    $reply['ForumTopic']['id']
                    )),
                    'title' => __d('forum','Show edit history'),
                    'innerHtml'=>  '<i class="material-icons">change_history</i>'.__d('forum','Edit history'),
                    'style' => !$reply['ForumTopic']['user_edited'] ? 'display:none;' : '',
                    'id' => 'history_reply_'. $reply['ForumTopic']['id'],
                    'class' => 'forum-btn-link edit-history-btn',
                    'data-dismiss'=>'modal'
                ));
                ?>
            </div>
        </div>
    </div>
<?php endforeach;?>

<?php else: ?>
<div class="topic-body text-center"><?php echo __d('forum','No more results found');?></div>
<?php endif;?>