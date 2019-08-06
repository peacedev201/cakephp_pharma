<?php
if(Configure::read('Event.event_enabled') == 1):
    if(empty($title)) $title = "Popular Events";
    if(empty($num_item_show)) $num_item_show = 10;
    if(isset($title_enable)&&($title_enable)=== "") $title_enable = false; else $title_enable = true;
    $eventHelper = MooCore::getInstance()->getHelper('Event_Event');
    $popular_events = Cache::read('popular_events.'.$num_item_show, 'event');
    if(!$popular_events){
        $popular_events = $this->requestAction(
            "events/popular/num_item_show:$num_item_show"
        );
        Cache::write('popular_events.'.$num_item_show,$popular_events, 'event');
    }
    ?>
    <?php if (!empty($popular_events)): ?>
    <div class="box2">
        <div>

            <?php
            if (!empty($popular_events)):
                ?>
            <div id="carousel-event" class="carousel slide" data-ride="carousel">
		<div class="carousel-inner" role="listbox">
                <?php foreach ($popular_events as $key=>$event): ?>
                                   
                    <div class="item <?php if($key==0): ?>active<?php endif; ?>" >
			<img style="background-image:url(<?php echo $eventHelper->getImage($event, array());?>);" src="<?php echo $this->request->webroot ?>theme/mooInside/img/s.png"  />
                        <div class="carousel-caption">
                            <a class="title" href="<?php echo $this->request->base?>/events/view/<?php echo $event['Event']['id']?>/<?php echo seoUrl($event['Event']['title'])?>">
                                <b><?php echo h($event['Event']['title'])?></b>
                            </a>
                            <div class="event-info">
                            
                            <div class="m_b_5">
                            <?php if ($event['Event']['type'] == PRIVACY_PUBLIC): ?>
                            <?php echo __('Public')?>
                            <?php elseif ($event['Event']['type'] == PRIVACY_PRIVATE): ?>
                            <?php echo __('Private')?>
                            <?php endif; ?>
                            &middot; <?php echo __( '%s attending', $event['Event']['event_rsvp_count'])?>
                            </div>
                            <div class="m_b_5">
                                <span><b><?php echo __('Time') ?></b></span>
                                <div>
                                <?php echo $this->Time->format(' F j, Y', $event['Event']['from'])?> <?php echo $event['Event']['from_time']?> <br/>
                                <?php echo $this->Time->format(' F j, Y', $event['Event']['to'])?> <?php echo $event['Event']['to_time']?>
                                </div>
                            </div>
                            <div class="m_b_5">
                                <span><b><?php echo __('Location') ?></b></span>
                                <div>
                                    <?php echo h($event['Event']['location'])?>
                                </div>
                            </div>
                            <div class="m_b_5">
                                <span><b><?php echo __('Address') ?></b></span>
                                <div>
                                    <?php echo h($event['Event']['address'])?> (<a href="<?php echo $this->request->base; ?>/events/show_g_map/<?php echo $event['Event']['id']; ?>" data-toggle="modal" data-target="#mapmodals" rel="google_map" title="<?php echo __( 'View Map')?>"><?php echo __( 'View Map')?></a>)
                                </div>
                            </div>
                            <div>
                               
                            </div>
                        </div>
                        </div>
					
                    </div>
                <?php endforeach; ?>
		</div>
		<!-- Controls -->
                <a class="left carousel-control" href="#carousel-event" role="button" data-slide="prev">
                      <span class="material-icons" aria-hidden="true">chevron_left</span>
                      <span class="sr-only">Previous</span>
                </a>
                <a class="right carousel-control" href="#carousel-event" role="button" data-slide="next">
                      <span class="material-icons" aria-hidden="true">chevron_right</span>
                      <span class="sr-only">Next</span>
                </a>
            </div>
                
            <?php
            else:
                echo __( 'Nothing found');
            endif;
            ?>
        </div>
    </div>
    <?php endif; ?>
<?php endif; ?>