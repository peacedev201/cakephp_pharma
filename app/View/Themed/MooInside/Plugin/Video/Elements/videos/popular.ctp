<?php
if(Configure::read('Video.video_enabled') == 1):
    if(empty($title)) $title = "Popular Videos";
    if(empty($num_item_show)) $num_item_show = 10;
    if(isset($title_enable)&&($title_enable)=== "") $title_enable = false; else $title_enable = true;
    
    $popular_videos = Cache::read('video.popular_video.'.$num_item_show,'video');
    if(empty($popular_videos)){
        $popular_videos = $this->requestAction(
            "videos/popular/num_item_show:$num_item_show"
        );
        Cache::write('video.popular_video.'.$num_item_show,$popular_videos,'video');
    }
    $videoHelper = MooCore::getInstance()->getHelper('Video_Video');
    ?>
    <?php if (!empty($popular_videos)): ?>
    <div class="box2">
        <div>
            <?php
            if (!empty($popular_videos)):
                ?>
            <div id="carousel-video" class="carousel slide" data-ride="carousel">
		<div class="carousel-inner" role="listbox">
                 <?php foreach ($popular_videos as $key=>$video): ?>
                                   
                    <div class="item <?php if($key==0): ?>active<?php endif; ?>" >
			<img src="<?php echo $this->request->webroot ?>theme/mooInside/img/s.png"  style="background-image:url(<?php echo $videoHelper->getImage($video, array())?>);" />
                        <div class="carousel-caption">
                            <div class="video_info">
                                    <a href=<?php if ( !empty( $ajax_view ) ): ?>"javascript:void(0)" onclick="loadPage('videos', '<?php echo $this->request->base?>/videos/ajax_view/<?php echo $video['Video']['id']?>', true)"<?php else: ?>"<?php echo $this->request->base?>/videos/view/<?php echo $video['Video']['id']?>/<?php echo seoUrl($video['Video']['title'])?>"<?php endif; ?>><?php echo h($this->Text->truncate( $video['Video']['title'], 100 ))?></a>
                                    <div class="extra_info">
                                        <div><?php echo __( 'Posted by')?> <?php echo $this->Moo->getName($video['User'], false)?></div>
                                        <?php echo __n('%s like','%s likes',$video['Video']['like_count'],$video['Video']['like_count']); ?>
                                    </div>
                            </div>
                            
                        </div>
			
                    </div>
                <?php endforeach; ?>
		</div>
		<!-- Controls -->
                <a class="left carousel-control" href="#carousel-video" role="button" data-slide="prev">
                      <span class="material-icons" aria-hidden="true">chevron_left</span>
                      <span class="sr-only">Previous</span>
                </a>
                <a class="right carousel-control" href="#carousel-video" role="button" data-slide="next">
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