<?php $helper = MooCore::getInstance()->getHelper('Forum_Forum');?>
<?php if (!empty($topics)): ?>
    <?php foreach ($topics as $topic): ?>
        <div class="forum-lists forum-search-item clearfix" style="">
            <div class="search-item-info">
                <div class="date">
                    <i class="material-icons">schedule</i><?php echo $this->Moo->getTime( $topic['ForumTopic']['created'], Configure::read('core.date_format'), $utz )?>
                    <?php if($topic['ForumTopic']['parent_id']):?>
                        <a class="reply-link-detail" href="<?php echo $topic['ParentTopic']['moo_href'].'/reply_id:'.$topic['ForumTopic']['id'];?>">#<?php echo $topic['ForumTopic']['id'];?></a>
                    <?php endif;?>
                </div>
                <?php if($topic['ForumTopic']['parent_id']):?>
                    <div class="forum-search-item-head">
                        <?php echo __d('forum', 'In reply to') ;?>: <a href="<?php echo $topic['ParentTopic']['moo_href'];?>"><?php echo $topic['ParentTopic']['moo_title'];?></a>
                    </div>
                <?php else:?>
                    <div class="forum-search-item-head">
                        <?php echo __d('forum', 'Topic') ;?>: <a href="<?php echo $topic['ForumTopic']['moo_href'];?>"><?php echo $topic['ForumTopic']['moo_title'];?></a>
                        <span class="forum-topic-started-in">in: <a class="forum-in-forum" href="<?php echo $topic['Forum']['moo_href'];?>"><?php echo $topic['Forum']['moo_title'];?></a></span>
                    </div>
                <?php endif;?>
            </div>
            <div class="search-item-main clearfix">
                <div class="col-sm-2">
                    <div class="forum-reply-user">
                        <?php if(!empty($topic['User']['id'])):?>
                            <div class="forum-reply-avatar">
                                <?php echo $this->Moo->getItemPhoto(array('User' => $topic['User']), array('prefix' => '50_square'), array('class' => 'topic-author-avatar img-circle'))?>
                            </div>
                            <div class="forum-reply-info">
                                <span class="forum-reply-name">
                                    <?php echo $this->Moo->getName($topic['User'])?>
                                </span>
                                <?php $this->getEventManager()->dispatch(new CakeEvent('element.comments.afterRenderUserNameComment', $this,array('user'=>$topic['User']))); ?>
                                <?php if($helper->checkModerator($topic, array('Forum' => $topic['Forum']))):?>
                                    <span class="forum-member-role"><?php echo __d('forum','(Moderator)');?></span>
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
                <div class="col-sm-10">
                    <div class="forum-reply-content">
                        <?php echo $this->Text->truncate($this->Moo->cleanHtml($this->Text->convert_clickable_links_for_hashtags($helper->bbcodetohtml($topic['ForumTopic']['description'],true))), 300, array('exact' => false, 'html' => true));?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach;?>

<?php else: ?>
    <div class="forum-lists"><?php echo __d('forum','No more results found');?></div>
<?php endif;?>