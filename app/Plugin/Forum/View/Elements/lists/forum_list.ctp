<?php if (!empty($forums)): ?>
    <?php
    if(!isset($helper)){
       $helper = MooCore::getInstance()->getHelper('Forum_Forum');
    }

    foreach ($forums as $forum):
        $forum_icon = $helper->getIconForum($forum);
        if(empty($forum['last_topic'])){
            $forumTopicModel = MooCore::getInstance()->getModel('Forum.ForumTopic');
            $forum['last_topic'] = $forumTopicModel->findById($forum['Forum']['last_topic_id']);
        }
        if(!empty($forum['last_topic']['User']['id'])) {
            $avt = $this->Moo->getItemPhoto(array('User' => $forum['last_topic']['User']), array('prefix' => '50_square'), array('class' => 'img-circle', 'width' => '60%', 'height' => '60%'));
        }
        $style = !$forum['Forum']['status'] ? 'background-color:'. Configure::read('Forum.forum_locked_bg_color') : '';
    ?>
    <div class="forum-lists clearfix" style="<?php echo $style;?>">
        <div class="col-sm-6">
            <div class="forum-lists-index">
                <a class="forum-img-large" href="<?php echo $forum['Forum']['moo_href'];?>"><img src="<?php echo $forum_icon;?>" alt="<?php echo $forum['Forum']['name'];?>"></a>
                <div class="forum-lists-info">
                    <div class="forum-title">
                        <a class="forum-title-link" href="<?php echo $forum['Forum']['moo_href'];?>"><?php echo $forum['Forum']['name'];?></a>
                        <span class="forum-icon"><?php echo $helper->getLockIcon($forum);?></span>
                    </div>
                    <div class="forum-description">
                        <?php echo nl2br(strip_tags($this->Text->truncate($forum['Forum']['description'], 150, array('exact' => false, 'html' => true))));?>
                    </div>
                    <?php if(!empty($forum['subs'])):?>
                        <div class="forum-sub-list clearfix">
                            <?php foreach ($forum['subs'] as $sub): ?>
                            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4">
                                <a class="forum-sub-item" href="<?php echo $sub['Forum']['moo_href'];?>">
                                    <img src="<?php echo $helper->getIconForum($sub);?>" alt="<?php echo $sub['Forum']['name'];?>" width="20px" height="20px" title="">
                                    <?php echo $sub['Forum']['name'];?>
                                </a>
                            </div>
                            <?php endforeach;?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-sm-1 hidden-xs"><span class="forum-text-count"><?php echo $helper->round($forum['Forum']['count_topic']);?></span></div>
        <div class="col-sm-1 hidden-xs"><span class="forum-text-count"><?php echo $helper->round($forum['Forum']['count_reply']);?></span></div>
        <div class="col-sm-4">
            <div class="forum-user-lastest">
                <?php if(!empty($forum['last_topic'])):?>
                    <?php if(!isset($avt)):?>
                        <a class="user-img-deleted">
                            <img src="<?php echo $this->request->base;?>/user/img/noimage/Unknown-user-sm.png" class="img-circle" width="60%" height="60%" alt="<?php echo __d('forum','Deleted Account');?>" title="<?php echo __d('forum','Deleted Account');?>">
                        </a>
                <?php else:
                        echo $avt;
                 endif; ?>
                    <div class="forum-lastest-main">
                        <a class="forum-lastest-title" href="<?php echo $forum['last_topic']['ForumTopic']['moo_href'];?>"><?php echo $this->Text->truncate($forum['last_topic']['ForumTopic']['title'], 40, array('exact' => false)); ?></a>
                        <span class="forum-lastest-date"><?php echo $this->Moo->getTime($forum['last_topic']['ForumTopic']['created'], Configure::read('core.date_format'), $utz) ?></span>
                        <span class="forum-latest-info">
                            <?php if(!empty($forum['last_topic']['User']['id'])){
                                echo __d('forum', 'By: ').$this->Moo->getName($forum['last_topic']['User'], true);
                                }else{
                                    echo __d('forum', 'By: ').'<a><b>'.__d('forum','Deleted Account').'</b></a>';
                                }
                            ?>
                        </span>
                    </div>
                <?php else:?>
                    <span class="forum-text-count"><?php echo __d('forum', 'No Topic');?></span>
                <?php endif;?>
            </div>
        </div>
    </div>
    <?php endforeach;?>
<?php else: ?>
    <div class="forum-lists"><?php echo __d('forum','No more results found');?></div>
<?php endif;?>