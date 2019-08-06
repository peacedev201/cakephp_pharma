<?php if (Configure::read('Photo.photo_enabled') == 1): ?>
    <?php
    $photoHelper = MooCore::getInstance()->getHelper('Photo_Photo');
    if (empty($title))
        $title = "Popular Albums";
    if (empty($num_item_show))
        $num_item_show = 1;
    if (isset($title_enable) && ($title_enable) === "")
        $title_enable = false;
    else
        $title_enable = true;

    $popular_albums = $this->requestAction(
            "mooinsides/getPhotosOfPopularAlbum/num_item_show:$num_item_show"
    );
    ?>
    <?php if (!empty($popular_albums)): ?>
        <div class="landing-block topalbum">
            <ul class="menu_left ">
                <li><a href="<?php echo $this->request->base ?>/photos"><?php echo __('Gallery') ?></a></li>               
            </ul>
            <div class="popular-album-landing">
                <div class="title"><?php echo $popular_albums['Album']['title']; ?></div>
<!--                <div class="description">
                    <?php echo $this->Moo->formatText( $popular_albums['Album']['description'], false, true, array('no_replace_ssl' => 1) )?>
                </div>-->
                <div id="carousel-album" class="carousel slide" data-ride="carousel">

                    <div class="carousel-inner" role="listbox">
                        <?php if(isset($popular_albums['Photos'])): ?>
                        <?php foreach ($popular_albums['Photos'] as $key => $photo): ?>
                            <div class="item <?php if ($key == 0): ?>active<?php endif; ?>" >
                                <img style="background-image:url(<?php echo $photoHelper->getImage($photo, array()) ?>);" src="<?php echo $this->request->webroot ?>theme/mooInside/img/s.png"  />    
                                <!--div class="carousel-caption">
                                    <div class="album-title">
                                        <a class="popular_album_cover" href="<?php echo $this->request->base ?>/albums/view/<?php echo $photo['Photo']['id'] ?>/<?php echo seoUrl($photo['Photo']['caption']) ?>">
                                            <?php echo h($photo['Photo']['caption']) ?>
                                        </a>

                                    </div>
                                </div-->

                            </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <!-- Controls -->
                    <a class="left carousel-control" href="#carousel-album" role="button" data-slide="prev">
                        <span class="icon-left-open-mini" aria-hidden="true"></span>
                        <span class="sr-only"><?php echo __('Previous'); ?></span>
                    </a>
                    <a class="right carousel-control" href="#carousel-album" role="button" data-slide="next">
                        <span class="icon-right-open-mini" aria-hidden="true"></span>
                        <span class="sr-only"><?php echo __('Next'); ?></span>
                    </a>
                    <ol class="carousel-indicators">
                        <?php if(isset($popular_albums['Photos'])): ?>
                        <?php foreach ($popular_albums['Photos'] as $key => $photo): ?>
                            <li data-target="#carousel-album" data-slide-to="<?php echo $key ?>" class="<?php if ($key == 0): ?>active<?php endif; ?>">
                                <img style="background-image:url(<?php echo $photoHelper->getImage($photo, array('prefix' => '75_square')) ?>);" src="<?php echo $this->request->webroot ?>theme/mooInside/img/s.png"  />    
                            </li>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </ol>
                </div>

                <div class="clear"></div>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>