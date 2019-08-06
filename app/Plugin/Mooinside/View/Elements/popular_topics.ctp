<?php
$num_item_show = 5;
$topicHelper = MooCore::getInstance()->getHelper('Topic_Topic');
$popular_topics = $this->requestAction(
        "topics/popular/num_item_show:$num_item_show"
);
?>
<?php if (!empty($popular_topics)): ?>
<div class="landing-block topheading">
    <ul class="menu_left ">
        <li><a href="#"><?php echo __('top Headline'); ?></a></li>
    </ul>
        <?php foreach ($popular_topics as $key => $topic): ?>
            <?php if ($key == 0): ?>                   
                <div class="landing_large_item" >
                    <img style="background-image:url(<?php echo $topicHelper->getImage($topic, array()) ?>);" src="<?php echo $this->request->webroot ?>theme/mooInside/img/s.png"  />
                    <div class="topic_info">
                        <div class="topic-time">
                            <?php echo $this->Moo->getTime($topic['Topic']['last_post'], Configure::read('core.date_format'), $utz) ?>
                        </div>
                        <div class="title-topic">
                            <a href="<?php
                            echo $this->Html->url(array(
                                'plugin' => 'topic',
                                'controller' => 'topics',
                                'action' => 'view',
                                $topic['Topic']['id'],
                                seoUrl($topic['Topic']['title'])
                            ));
                            ?>">
            <?php echo h($topic['Topic']['title']) ?>
                            </a>
                        </div>
                    </div>
                    <div class="gradient_bg"></div>
                </div>
        <?php endif; ?>
            <?php endforeach; ?>
        <!-- item topic list -->
        <div class="landing_mini_item">
            <?php foreach ($popular_topics as $key => $topic): ?>

        <?php if ($key > 0 && $key < 5): ?>  
                    <div class="landing_item">
                        <a href=<?php if (!empty($ajax_view)): ?>"javascript:void(0)" onclick="loadPage('topics', '<?php echo $this->request->base ?>/topics/ajax_view/<?php echo $topic['Topic']['id'] ?>')"<?php else: ?>"<?php echo $this->request->base ?>/topics/view/<?php echo $topic['Topic']['id'] ?>/<?php echo seoUrl($topic['Topic']['title']) ?>"<?php endif; ?>>
                           <img src="<?php echo $topicHelper->getImage($topic, array('prefix' => '150_square')) ?>" class="topic-thumb" />
                        </a>
                        <div class="topic-info">
                            <a class="title" href=<?php if (!empty($ajax_view)): ?>"javascript:void(0)" onclick="loadPage('topics', '<?php echo $this->request->base ?>/topics/ajax_view/<?php echo $topic['Topic']['id'] ?>')"<?php else: ?>"<?php echo $this->request->base ?>/topics/view/<?php echo $topic['Topic']['id'] ?>/<?php echo seoUrl($topic['Topic']['title']) ?>"<?php endif; ?>>
            <?php echo h($this->Text->truncate($topic['Topic']['title'], 45)) ?>
                        </a>
                    </div>
                </div>
        <?php endif; ?>
    <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>