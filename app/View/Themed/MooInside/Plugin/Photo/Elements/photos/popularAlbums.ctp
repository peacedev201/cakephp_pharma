<?php if (Configure::read('Photo.photo_enabled') == 1): ?>
<?php
$photoHelper = MooCore::getInstance()->getHelper('Photo_Photo');
if(empty($title)) $title = "Popular Albums";
if(empty($num_item_show)) $num_item_show = 10;
if(isset($title_enable)&&($title_enable)=== "") $title_enable = false; else $title_enable = true;

$popular_albums = Cache::read('photo.popular_albums.'.$num_item_show, 'photo');
if(!$popular_albums){
    $popular_albums = $this->requestAction(
        "albums/popular/num_item_show:$num_item_show"
    );
    Cache::write('photo.popular_albums.'.$num_item_show, $popular_albums, 'photo');
}
?>
<?php if (!empty($popular_albums)):?>
<div class="box2">
    
    <div class="popular-album">
        
        <div id="carousel-album" class="carousel slide" data-ride="carousel">
                
                <div class="carousel-inner" role="listbox">
                    <?php foreach ($popular_albums as $key => $album): ?>
                    <div class="item <?php if($key==0): ?>active<?php endif; ?>" >
                        <img style="background-image:url(<?php echo $photoHelper->getAlbumCover($album['Album']['cover'], array()) ?>);" src="<?php echo $this->request->webroot ?>theme/mooInside/img/s.png"  />    
                        
                            <!-- <div class="carousel-caption">
                                    <div class="album-title">
                                            <a class="popular_album_cover" href="<?php echo $this->request->base?>/albums/view/<?php echo $album['Album']['id']?>/<?php echo seoUrl($album['Album']['title'])?>">
                                                    <?php echo h($album['Album']['title'])?>


                                            </a>

                                    </div>
                            </div> -->

                    </div>
                    <?php endforeach; ?>
                </div>
                <!-- Controls -->
                  <a class="left carousel-control" href="#carousel-album" role="button" data-slide="prev">
                        <span class="material-icons" aria-hidden="true">chevron_left</span>
                        <span class="sr-only">Previous</span>
                  </a>
                  <a class="right carousel-control" href="#carousel-album" role="button" data-slide="next">
                        <span class="material-icons" aria-hidden="true">chevron_right</span>
                        <span class="sr-only">Next</span>
                  </a>
                  <ol class="carousel-indicators">
                    <?php foreach ($popular_albums as $key => $album1): ?>
                    <li data-target="#carousel-album" data-slide-to="<?php echo $key; ?>" <?php if( $key ==1): ?>class="active"<?php endif; ?>>
                        <img style="background-image:url(<?php echo $photoHelper->getAlbumCover($album1['Album']['cover'], array()) ?>);" src="<?php echo $this->request->webroot ?>theme/mooInside/img/s.png"  />    

                    </li>
                    <?php endforeach; ?>
                </ol>
        </div>
        
        <div class="clear"></div>
    </div>
</div>
<?php endif; ?>
<?php endif; ?>