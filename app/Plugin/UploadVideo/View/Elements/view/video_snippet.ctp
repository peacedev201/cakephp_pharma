<?php $oVideoHelper = MooCore::getInstance()->getHelper('Video_Video'); ?>

<div class="video-upload-render">
    <video controls preload="none" width="1280" height="720" poster="<?php echo $oVideoHelper->getImage($video, array()); ?>">
        <source src="<?php echo $oVideoHelper->getVideo($video); ?>" type='video/mp4'/>
        <p class="vjs-no-js">To view this video please enable JavaScript, and consider upgrading to a web browser that <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a></p>
    </video>
</div>

<div class="clear"></div>