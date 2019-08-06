<?php if(!empty($topics)):?>
<?php $helper = MooCore::getInstance()->getHelper('Forum_Forum');?>
<div class="box2">
    <?php if(empty($title)) $title = __d('forum', 'Recent topics'); ?>
    <h3><?php echo __( $title)?></h3>
    <div class="box_content">
        <ul class="list-recent-topics list-forum-widget">
            <?php foreach ($topics as $topic): ?>
                <li>
                    <a class="forum-img-large" href="<?php echo $topic['ForumTopic']['moo_href'];?>">
                        <img class="img_wrapper2 user_list" src="<?php echo $helper->getTopicImage($topic, array('prefix' => '150_square'))?>" alt="<?php echo $topic['ForumTopic']['moo_title'];?>">
                    </a>
                    <div class="recent_topic_info">
                        <a class="topic-permalink" href="<?php echo $topic['ForumTopic']['moo_href'];?>"><?php echo $topic['ForumTopic']['moo_title'];?></a>
                        <div class="topic-meta">
                            <span class="topic-started-by"><?php echo __d('forum', 'Started by');?>: <?php echo $this->Moo->getItemPhoto(array('User' => $topic['User']), array('prefix' => '50_square'), array('class' => 'topic-author-avatar'))?>
                                <?php echo $this->Moo->getName($topic['User'])?>
                            </span>
                            <span class="topic-started-in">in: <a href="<?php echo $topic['Forum']['moo_href'];?>"><?php echo $topic['Forum']['moo_title'];?></a></span>
                        </div>
                        <span class="widget-time"><?php echo $this->Moo->getTime( $topic['ForumTopic']['created'], Configure::read('core.date_format'), $utz )?></span>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php endif;?>