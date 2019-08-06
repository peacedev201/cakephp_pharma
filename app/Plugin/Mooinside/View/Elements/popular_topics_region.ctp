<?php
        $num_item_show = 10;
        $topicHelper = MooCore::getInstance()->getHelper('Topic_Topic');
        $popular_topics = Cache::read('topic.popular_topics.' . $num_item_show, 'topic');
        if (!$popular_topics) {
            $popular_topics = $this->requestAction(
                    "topics/popular/num_item_show:$num_item_show"
            );
            Cache::write('topic.popular_topics.' . $num_item_show, $popular_topics, 'topic');
        }
        ?>
        <?php
        if (!empty($popular_topics)):
            ?>
            <div class="box2">
                <h3><?php echo __('Popular Topics') ?></h3>
                <div class="box_content">
                    <ul class="topic-block">
                        <?php $i = 1; ?>
                        <?php foreach ($popular_topics as $key => $topic): ?>
                            <li>
                                <div class="topic-block-title">
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
                            </li>
                            <?php
                            unset($popular_topics[$key]);
                            if ($i == 5){
                                break;
                            }
                            $i++;
                            
                            ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <?php
        endif;
        ?>
        <div class="box2">
            <div class="ad_section">
                <img src="<?php echo $this->request->webroot ?>theme/mooInside/img/ad/ad_2.png" />
            </div>
        </div>
        <div class="box2">
            <div class="ad_section">
                <?php 
                    $this->Helpers->MooRequirejs->addPath(array(
                     "moosimpleWeather"=>$this->Helpers->MooRequirejs->assetUrlJS("js/jquery.simpleWeather.min.js"),
                    ));

                    $this->Helpers->MooRequirejs->addShim(array(
                     'moosimpleWeather'=>array("deps" =>array('jquery')),
                    ));
                  ?>
                <?php //echo $this->Html->script(array('jquery.simpleWeather.min'), array('inline' => false)); ?>
               <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery', 'moosimpleWeather'), 'object' => array('$'))); ?>

                  $.simpleWeather({
                //location: 'Austin, TX',
                woeid: '1252431',
                unit: 'c',
                success: function(weather) {
                  html = '<h2><i class="icon-'+weather.code+'"></i> '+weather.temp+'&deg;'+weather.units.temp+'</h2>';
                  html += '<ul><li>'+weather.city+', '+weather.region+'</li>';
                  html += '<li class="currently">'+weather.currently+'</li>';
                  html += '<li>'+weather.wind.direction+' '+weather.wind.speed+' '+weather.units.speed+'</li></ul>';

                  $("#weather").html(html);
                },
                error: function(error) {
                  $("#weather").html('<p>'+error+'</p>');
                }
              });

                <?php $this->Html->scriptEnd(); ?>
                <style type="text/css">
                    #weather{
                        background-image:url(<?php echo $this->request->webroot ?>theme/mooInside/img/austin-2.jpg);
                    }
                </style>
                <div id="weather"></div>
            </div>
        </div>

        <div class="box2">
            <div class="ad_section">
                <img src="<?php echo $this->request->webroot ?>theme/mooInside/img/ad/ad_4.png" />
            </div>
        </div>