<?php $topicHelper = MooCore::getInstance()->getHelper('Topic_Topic'); ?>

<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery','mooTopic'), 'object' => array('$', 'mooTopic'))); ?>
mooTopic.initOnView();
<?php $this->Html->scriptEnd(); ?> 

<?php $this->setNotEmpty('east');?>
<?php $this->start('east'); ?>
	<div class="box2">
		<h3><?php echo __( 'Topic Creator')?></h3>
		<div class="box_content">
		    <?php echo $this->element('misc/user_mini', array('user' => $topic['User'])); ?>
		</div>
	</div>
	
	<?php if ( !empty( $files ) ): ?>
	<div class="box2">
            <div class="box_content">
		<h3><?php echo __( 'Attachments')?></h3>
		<div class="box_content">
                    <ul class="list6 list6sm">
                    <?php foreach ($files as $attachment): ?>     
                        <li><i class="material-icons icon-small">attach_file</i> <a href="<?php echo $this->request->base?>/attachments/download/<?php echo $attachment['Attachment']['id']?>"><?php echo $attachment['Attachment']['original_filename']?></a> <span class="date">(<?php echo __n('%s download', '%s downloads', $attachment['Attachment']['downloads'], $attachment['Attachment']['downloads'] )?>)</span></li>
                    <?php endforeach; ?>
                    </ul>
                </div>
            </div>
	</div>
    <?php endif; ?>

    <?php if(!empty($tags)): ?>
        <div class="box2">
            <h3><?php echo __( 'Tags')?></h3>
            <div class="box_content">
                <?php echo $this->element( 'blocks/tags_item_block' ); ?>
            </div>
        </div>
    <?php endif; ?>

<?php $this->end();?>

<!--Begin Center-->
<div class="bar-content full_content p_m_10">
    <div class="content_center">
	<div class="post_body topic_view_body post_content">
		<div class="topic-time">
			 <?php echo $this->Moo->getTime($topic['Topic']['created'], Configure::read('core.date_format'), $utz)?>
		</div>
        <?php if(!empty($uid)): ?>
            <div class="list_option">
                <div class="dropdown">
                    <button class="btn btn-default" id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false">
                        <span><?php echo __('Edit') ?></span> <i class="material-icons">expand_more</i>
                    </button>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                        <?php if ( ( !empty($cuser) && $cuser['Role']['is_admin'] ) ): ?>
                            <?php if ( !$topic['Topic']['pinned'] ): ?>
                            <li><a href='<?php echo $this->request->base?>/topics/do_pin/<?php echo $topic['Topic']['id']?>'><?php echo __( 'Pin Topic')?></a></li>
                            <?php else: ?>
                            <li><a href='<?php echo $this->request->base?>/topics/do_unpin/<?php echo $topic['Topic']['id']?>'><?php echo __( 'Unpin Topic')?></a></li>
                            <?php endif; ?>

                            <?php if ( !$topic['Topic']['locked'] ): ?>
                            <li><a href='<?php echo $this->request->base?>/topics/do_lock/<?php echo $topic['Topic']['id']?>'><?php echo __( 'Lock Topic')?></a></li>
                            <?php else: ?>
                            <li><a href='<?php echo $this->request->base?>/topics/do_unlock/<?php echo $topic['Topic']['id']?>'><?php echo __( 'Unlock Topic')?></a></li>
                            <?php endif; ?>
                        <?php endif; ?>
                            
                        <?php if ( ($topic['Topic']['user_id'] == $uid ) || ( !empty($cuser['Role']['is_admin']) ) ): ?>
                      <li><?php echo $this->Html->link(__( 'Edit Topic'), array(
                          'plugin' => 'Topic',
                          'controller' => 'topics',
                          'action' => 'create',
                          $topic['Topic']['id']
                      )); ?></li>
                      <li><a href="javascript:void(0);" class="deleteTopic" data-id="<?php echo $topic['Topic']['id']?>"><?php echo __( 'Delete')?></a></li>
                        <li class="seperate"></li>
                        <?php endif; ?>
                        
                        <li>
                            <?php
      $this->MooPopup->tag(array(
             'href'=>$this->Html->url(array("controller" => "reports",
                                            "action" => "ajax_create",
                                            "plugin" => false,
                                            'Topic_Topic',
                                            $topic['Topic']['id'],
                                        )),
             'title' => __( 'Report Topic'),
             'innerHtml'=> __( 'Report Topic'),
     ));
 ?>
                           </li>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
	    <h1><?php echo h($topic['Topic']['title'])?></h1>
            	    <!--<div class="extra_info"><?php echo __( 'Posted in')?> <a href="<?php echo $this->request->base?>/topics/index/<?php echo $topic['Topic']['category_id']?>/<?php echo seoUrl($topic['Category']['name'])?>"><strong><?php echo $topic['Category']['name']?></strong></a> </div>-->
            <div class="post_content topic_detail">
                		<div class="extra_info">
			<?php echo __( 'Posted in')?> <a href="<?php echo $this->request->base?>/topics/index/<?php echo $topic['Topic']['category_id']?>/<?php echo seoUrl($topic['Category']['name'])?>">
				<strong><?php echo $topic['Category']['name']?></strong>
			</a>
		</div>
		<div class="item_thumb">
                    <img style="background-image:url(<?php echo $topicHelper->getImage($topic, array())?>);" src="<?php echo $this->request->webroot ?>theme/mooInside/img/s.png"  />
                    
                </div>
		<div class="news_content">
			<div class="top_news_view">
				<!--- popular topics -->
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
					<?php if($title_enable): ?>
					<h3><?php echo $title; ?></h3>
					<?php endif; ?>
					<div class="box_content">

						<?php
						if (!empty($popular_topics)):
							?>
							<ul class="topic-block">
								<?php foreach ($popular_topics as $topic1): ?>
									<li>
										
									   
										<div class="topic-block-title">
											<a href="<?php echo $this->request->base?>/topics/view/<?php echo $topic1['Topic']['id']?>/<?php echo seoUrl($topic1['Topic']['title'])?>">
												<?php echo h($topic1['Topic']['title'])?>
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
				
				<!--end popular topics -->
			</div>
			<?php echo $this->Moo->cleanHtml($this->Text->convert_clickable_links_for_hashtags( $topic['Topic']['body'] , Configure::read('Topic.topic_hashtag_enabled')))?>
		</div>		

            </div>
	    <?php if ( !empty( $pictures ) ): ?>
            <div class='topic_attached_file'>
                <div class="date"><?php echo __( 'Attached Images')?></div>
                <ul class="list4 p_photos ">
                <?php foreach ($pictures as $p): ?>     
                    <li class='col-xs-6 col-ms-4 col-md-3' >
                        <div class="p_2">
                        <a style="background-image:url(<?php echo $this->request->webroot?>uploads/attachments/t_<?php echo $p['Attachment']['filename']?>)" href="<?php echo $this->request->webroot?>uploads/attachments/<?php echo $p['Attachment']['filename']?>" class="attached-image layer_square"></a>
                        </div>
                    </li>
                <?php endforeach; ?>
                </ul>
                <div class='clear'></div>
            </div>
        <?php endif; ?>

        <?php $this->Html->rating($topic['Topic']['id'],'topics', 'Topic'); ?>

        <div class="clear"></div>
        </div>

	
    </div>
</div>

<?php if (!$topic['Topic']['locked'] || (!empty($cuser) && $cuser['Role']['is_admin']) ): ?>
<div class="bar-content full_content p_m_10">
    <div class="content_center">
        <?php echo $this->element('likes', array('shareUrl' => $this->Html->url(array(
                                    'plugin' => false,
                                    'controller' => 'share',
                                    'action' => 'ajax_share',
                                    'Topic_Topic',
                                    'id' => $topic['Topic']['id'],
                                    'type' => 'topic_item_detail'
                                ), true), 'item' => $topic['Topic'], 'type' => 'Topic_Topic')); ?>
    </div>
</div>
<?php endif; ?>

<div class="bar-content full_content p_m_10">
    <?php echo $this->renderComment();?>
</div>

