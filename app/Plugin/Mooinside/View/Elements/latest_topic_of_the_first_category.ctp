<?php
$num_item_show = 11;
$topicHelper = MooCore::getInstance()->getHelper('Topic_Topic');
$categories = $this->requestAction(
        "mooinsides/getTopicCategories"
);
$topics = $this->requestAction(
        "mooinsides/getTopicsOfFirstCategories"
);

?>
<?php if(!empty($topics)): ?>
<div class="landing-block cat_topic">
    <ul class="menu_left ">
        <?php foreach ($categories as  $key => $item): //echo '<pre>'; print_r($item);?>
        <?php if ($key < 4): ?>  
        <li><a href="<?php echo $this->request->base ?>/topics/index/<?php echo $item['Category']['id'] ?>/<?php echo seoUrl($item['Category']['name']) ?>"><?php echo $item['Category']['name'] ?></a></li>
        <?php elseif ($key == 4) :  ?>
            <li><a href="<?php echo $this->request->base?>/topics/"><?php echo __('More option'); ?></a></li>
        <?php endif; ?>
        <?php endforeach; ?>
    </ul>
    
    <div class="cat_topic_list">
        <div class="cat_large">
            <img src="<?php echo $topicHelper->getImage($topics[0], array()) ?>" />
            <div class="topic_info">
                <div class="topic_title">
                    <a href="<?php echo $this->Html->url(array(
                        'plugin' => 'topic',
                        'controller' => 'topics',
                        'action' => 'view',
                        $topics[0]['Topic']['id'],
                        seoUrl($topics[0]['Topic']['title'])
                    )); ?>"><?php echo $topics[0]['Topic']['title']; ?></a>
                </div>
                <div class="topic_short_description">
                    <?php echo strip_tags($this->Text->truncate($topics[0]['Topic']['body'], 300)); ?>
                </div>
            </div>
        </div>
        <?php unset($topics[0]); ?>
        <div class="cat_small">
            <?php foreach ($topics as $key => $item): ?>
            <?php if($key < 6): ?>
            <div class="topic_item_hasImage">
                <?php if($key == 1): ?>
                <img src="<?php echo $topicHelper->getImage($item, array('prefix' => '150_square')) ?>" />
                <?php endif; ?>
                <div class="topic_title">
                    <a href="<?php echo $this->Html->url(array(
                        'plugin' => 'topic',
                        'controller' => 'topics',
                        'action' => 'view',
                        $item['Topic']['id'],
                        seoUrl($item['Topic']['title'])
                    )); ?>"><?php echo h($item['Topic']['title']); ?></a>
                </div>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php if(count($topics) > 5): ?>
        <div class="cat_small">
            <?php foreach ($topics as $key => $item): ?>
             <?php if($key >= 6): ?>
            <div class="topic_item_hasImage">
                <?php if($key == 6): ?>
                <img src="<?php echo $topicHelper->getImage($item, array('prefix' => '150_square')) ?>" />
                <?php endif; ?>
                <div class="topic_title">
                    <a href="<?php echo $this->Html->url(array(
                        'plugin' => 'topic',
                        'controller' => 'topics',
                        'action' => 'view',
                        $item['Topic']['id'],
                        seoUrl($item['Topic']['title'])
                    )); ?>"><?php echo h($item['Topic']['title']); ?></a>
                </div>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    
</div>
<?php endif; ?>