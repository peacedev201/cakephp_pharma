<?php $helper = MooCore::getInstance()->getHelper('Forum_Forum');?>
<?php if (!empty($topics)): ?>
    <?php foreach ($topics as $topic):
            if($topic['ForumTopic']['ping']){
                $style = 'background-color:'. Configure::read('Forum.forum_pined_bg_color');
            }else{
                $style = !$topic['ForumTopic']['status'] ? 'background-color:'. Configure::read('Forum.forum_topic_locked_text_color') : '';
            }
    ?>
        <?php if(!empty($topic['LastPost']['id'])) {
            $last_post_user = MooCore::getInstance()->getItemByType('User', $topic['LastPost']['user_id']);
        } ?>
        <div class="forum-lists clearfix" style="<?php echo $style;?>">
            <div class="<?php echo $type != 'my' ? 'col-sm-6' : 'col-sm-12';?>">
                <div class="forum-lists-index">
                    <a class="forum-img-large" href="<?php echo $topic['ForumTopic']['moo_href'];?>">
                        <img src="<?php echo $helper->getTopicImage($topic, array('prefix' => '150_square'))?>" alt="<?php echo $topic['ForumTopic']['moo_title'];?>">
                    </a>

                    <div class="forum-lists-info">
                        <div class="forum-title">
                            <a class="forum-title-link" href="<?php echo $topic['ForumTopic']['moo_href'];?>"><?php echo $topic['ForumTopic']['moo_title'];?></a>
                            <?php if(!$topic['ForumTopic']['status']):?>
                                <span class="forum-icon"><i class="material-icons" title="<?php echo __d('forum','Locked topic');?>">lock_outline</i></span>
                            <?php endif;?>
                        </div>

                        <div class="forum-topic-post-time">
                            <?php echo $this->Moo->getTime( $topic['ForumTopic']['created'], Configure::read('core.date_format'), $utz )?>
                        </div>

                        <div class="forum-topic-meta">
                            <span class="forum-topic-started-by">
                                <?php echo __d('forum', 'Started by');?>:
                                <?php if(!empty($topic['User']['id'])):?>
                                    <?php echo $this->Moo->getItemPhoto(array('User' => $topic['User']), array('prefix' => '50_square'), array('class' => 'forum-topic-author-avatar'))?>
                                    <?php echo $this->Moo->getName($topic['User'])?>
                                <?php else:?>
                                    <a class="user-img-deleted">
                                        <img src="<?php echo $this->request->base;?>/user/img/noimage/Unknown-user-sm.png" class="forum-topic-author-avatar" width="60%" height="60%" alt="<?php echo __d('forum','Deleted Account');?>" title="<?php echo __d('forum','Deleted Account');?>">
                                    </a>
                                    <a class="username-deleted"><?php echo __d('forum','Deleted Account');?></a>
                                <?php endif;?>
                            </span>
                            <?php $this->getEventManager()->dispatch(new CakeEvent('element.comments.afterRenderUserNameComment', $this,array('user'=>$topic['User']))); ?>
                            <?php if($helper->checkModerator($topic, array('Forum' => $topic['Forum']))):?>
                                <span class="forum-member-role"><?php echo __d('forum','(Moderator)');?></span>
                            <?php endif;?>
                            <?php if(empty($is_view_forum)):?>
                                <span class="forum-topic-started-in">in: <a class="forum-in-forum" href="<?php echo $topic['Forum']['moo_href'];?>"><?php echo $topic['Forum']['moo_title'];?></a></span>
                            <?php endif;?>
                        </div>
                    </div>
                </div>
            </div>
            <?php if($type != 'my'):?>
                <div class="col-sm-1 hidden-xs"><span class="forum-text-count"><?php echo $helper->round($topic['ForumTopic']['count_user']);?></span></div>
                <div class="col-sm-1 hidden-xs"><span class="forum-text-count"><?php echo $helper->round($topic['ForumTopic']['count_reply']);?></span></div>
                <div class="col-sm-4">
                    <?php if(!empty($topic['LastPost']['id'])): ?>
                    <div class="forum-user-lastest">
                        <?php if(!empty($last_post_user['User']['id'])):?>
                            <?php echo $this->Moo->getItemPhoto(array('User' => $last_post_user['User']), array('prefix' => '50_square'), array('class' => 'topic-author-avatar img-circle'))?>
                            <div class="forum-lastest-main">
                                <span class="forum-lastest-date"><?php echo $this->Moo->getTime( $topic['LastPost']['created'], Configure::read('core.date_format'), $utz )?></span>
                                <a class="forum-lastest-title" href="<?php echo $topic['ForumTopic']['moo_href'];?>">
                                    <?php echo $topic['LastPost']['moo_title'];?>
                                </a>
                                <span class="forum-lastest-title"><?php echo $this->Moo->getName($last_post_user['User'])?></span>
                            </div>
                        <?php else: ?>
                            <a class="user-img-deleted">
                                <img src="<?php echo $this->request->base;?>/user/img/noimage/Unknown-user-sm.png" class="topic-author-avatar img-circle" alt="<?php echo __d('forum','Deleted Account');?>" title="<?php echo __d('forum','Deleted Account');?>">
                            </a>
                            <div class="forum-lastest-main">
                                <span class="forum-lastest-date"><?php echo $this->Moo->getTime( $topic['LastPost']['created'], Configure::read('core.date_format'), $utz )?></span>
                                <a class="forum-lastest-title" href="<?php echo $topic['ForumTopic']['moo_href'];?>">
                                    <?php echo $topic['LastPost']['moo_title'];?>
                                </a>
                                <span class="forum-lastest-title"><a><b><?php echo __d('forum','Deleted Account');?></b></a></span>
                            </div>
                        <?php endif;?>
                    </div>
                    <?php else: ?>
                        <span class="forum-text-count"><?php echo __d('forum','No Replies');?></span>
                    <?php endif;?>
                </div>
            <?php endif;?>
        </div>
    <?php endforeach;?>

<?php else: ?>
    <div class="forum-lists"><?php echo __d('forum','No more results found');?></div>
<?php endif;?>

<?php if (isset($more_url)&& !empty($more_result)): ?>
    <?php $this->Html->viewMore($more_url) ?>
<?php endif; ?>

<?php if($this->request->is('ajax')): ?>
    <script type="text/javascript">
        require(["jquery","mooBehavior"], function($,mooBehavior) {
            mooBehavior.initMoreResults();
        });
    </script>
<?php endif; ?>