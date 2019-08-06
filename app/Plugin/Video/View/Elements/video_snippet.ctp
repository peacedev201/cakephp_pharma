<?php $videoHelper = MooCore::getInstance()->getHelper('Video_Video'); ?>
<?php if (!empty($video['Video']['pc_upload'])): ?>
    <div class="video-upload-render">
        <video controls preload="none" width="1280" height="720" poster="<?php echo $videoHelper->getImage($video, array()); ?>">
            <source src="<?php echo $videoHelper->getVideo($video); ?>" type='video/mp4'/>
            <p class="vjs-no-js">To view this video please enable JavaScript, and consider upgrading to a web browser that <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a></p>
        </video>
    </div>

    <div class="clear"></div>
    
    <style>
        .video-upload-render video {
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
            width: 100%;
            height: 100%;
            background-color: #000;
        }
    </style>
<?php else: ?>    
    <?php
    $ssl_mode = Configure::read('core.ssl_mode');
    $http = (!empty($ssl_mode)) ? 'https' : 'http';
    switch ($video['Video']['source']) {
        case VIDEO_TYPE_YOUTUBE:
            echo '<iframe width="' . VIDEO_WIDTH . '" height="' . VIDEO_HEIGHT . '" src="' . $http . '://www.youtube.com/embed/' . $video['Video']['source_id'] . '?wmode=opaque" frameborder="0" allowfullscreen></iframe>';
            break;

        case VIDEO_TYPE_VIMEO:
            echo '<iframe src="' . $http . '://player.vimeo.com/video/' . $video['Video']['source_id'] . '" width="' . VIDEO_WIDTH . '" height="' . VIDEO_HEIGHT . '" frameborder="0"></iframe>';
            break;
    }
    ?>
<?php endif; ?>