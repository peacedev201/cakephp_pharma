<?php
if(Configure::read('Topic.topic_enabled') == 1):
if(empty($title)) $title = "Popular Topics";
if(empty($num_item_show)) $num_item_show = 10;
if(isset($title_enable)&&($title_enable)=== "") $title_enable = false; else $title_enable = true;
$topicHelper = MooCore::getInstance()->getHelper('Topic_Topic');

?>
<?php if (!empty($popular_topics)): ?>
<div class="box2">
    <?php if($title_enable): ?>
    <h3><?php echo $title; ?></h3>
    <?php endif; ?>
    <div class="box_content">

        <?php
        if (!empty($popular_topics)):
            ?>
            <ul class="topic-block">
                <?php foreach ($popular_topics as $topic): ?>
                    <li>
                        
                       
						<div class="topic-block-title">
							<a href="<?php echo $this->request->base?>/topics/view/<?php echo $topic['Topic']['id']?>/<?php echo seoUrl($topic['Topic']['title'])?>">
								<?php echo h($topic['Topic']['title'])?>
							</a>
						</div>
                            
                        
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php
        else:
            echo __('Nothing found');
        endif;
        ?>
    </div>
</div>
<?php endif;endif; ?>