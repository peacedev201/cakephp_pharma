<?php
$num_item_show = 5;
$videoHelper = MooCore::getInstance()->getHelper('Video_Video');
$popular_videos = $this->requestAction(
        "videos/popular/num_item_show:$num_item_show"
);

?>
<?php if(!empty($popular_videos)): ?>
<div class="landing-block cat_topic">
    <ul class="menu_left ">
        <li><a href="<?php echo $this->request->base ?>/videos"><?php echo __('Top Videos') ?></a></li>               
    </ul>
    <div class="landing_video">

        <div class="landing_large_video">
            <img src="<?php echo $this->request->webroot ?>theme/mooInside/img/s.png"  style="background-image:url(<?php echo $videoHelper->getImage($popular_videos[0], array()) ?>);" />

            <div class="video_info">
                <a href="<?php echo $this->Html->url(array(
                        'plugin' => 'video',
                        'controller' => 'videos',
                        'action' => 'view',
                        $popular_videos[0]['Video']['id'],
                        seoUrl($popular_videos[0]['Video']['title'])
                    )); ?>">
                    <?php echo h($popular_videos[0]['Video']['title']); ?>
                </a>
                <div class="extra_info">
                    <?php echo h($this->Text->truncate($popular_videos[0]['Video']['description'], 300)); ?>
                </div>
            </div>
        </div>
        
        <?php
        // remove first item
        unset($popular_videos[0]);
        ?>
        
        <div class="landing_small_video">
            <?php foreach ($popular_videos as $item): ?>
                <div class="video_item">
                    <a class="video_cover" href="<?php echo $this->Html->url(array(
                        'plugin' => 'video',
                        'controller' => 'videos',
                        'action' => 'view',
                        $item['Video']['id'],
                        seoUrl($item['Video']['title'])
                    )); ?>">
                        <img src='<?php echo $videoHelper->getImage($item, array()) ?>' />                           
                    </a>
                    <div class="video_title">
                        <a href="<?php echo $this->Html->url(array(
                        'plugin' => 'video',
                        'controller' => 'videos',
                        'action' => 'view',
                        $item['Video']['id'],
                        seoUrl($item['Video']['title'])
                    )); ?>"><?php echo h($item['Video']['title']); ?></a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>