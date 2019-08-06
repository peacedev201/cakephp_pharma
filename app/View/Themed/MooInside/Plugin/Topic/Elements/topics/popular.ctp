<?php
if(Configure::read('Topic.topic_enabled') == 1):
if(empty($title)) $title = "Popular Topics";
if(empty($num_item_show)) $num_item_show = 10;
if(isset($title_enable)&&($title_enable)=== "") $title_enable = false; else $title_enable = true;
$topicHelper = MooCore::getInstance()->getHelper('Topic_Topic');
$popular_topics = Cache::read('topic.popular_topics.'.$num_item_show,'topic');
if(!$popular_topics){
    $popular_topics = $this->requestAction(
        "topics/popular/num_item_show:$num_item_show"
    );
    Cache::write('topic.popular_topics.'.$num_item_show, $popular_topics,'topic');
}
?>
<?php if (!empty($popular_topics)): ?>
<div class="box2">
    
    <div >

        <?php
        if (!empty($popular_topics)):
            ?>
            <div id="carousel-topic" class="carousel slide" data-ride="carousel">
		<div class="carousel-inner" role="listbox">
                <?php foreach ($popular_topics as $key => $topic): ?>
                                   
                    <div class="item <?php if($key==0): ?>active<?php endif; ?>" >
			<img style="background-image:url(<?php echo $topicHelper->getImage($topic, array())?>);" src="<?php echo $this->request->webroot ?>theme/mooInside/img/s.png"  />
                        <div class="carousel-caption">
                            <div class="topic-time">
                                    <?php echo $this->Moo->getTime( $topic['Topic']['last_post'], Configure::read('core.date_format'), $utz )?>
                            </div>
                            <div class="title-topic">
                                <a href="<?php echo $this->request->base?>/topics/view/<?php echo $topic['Topic']['id']?>/<?php echo seoUrl($topic['Topic']['title'])?>">
                                    <?php echo h($topic['Topic']['title'])?>
                                </a>
                            </div>
                            
                        </div>
						<div class="gradient_bg"></div>
                    </div>
                <?php endforeach; ?>
		</div>
		<!-- Controls -->
                <a class="left carousel-control" href="#carousel-topic" role="button" data-slide="prev">
                      <span class="material-icons" aria-hidden="true">chevron_left</span>
                      <span class="sr-only">Previous</span>
                </a>
                <a class="right carousel-control" href="#carousel-topic" role="button" data-slide="next">
                      <span class="material-icons" aria-hidden="true">chevron_right</span>
                      <span class="sr-only">Next</span>
                </a>
            </div>
        <?php
        else:
            echo __('Nothing found');
        endif;
        ?>
    </div>
</div>
<?php endif;endif; ?>